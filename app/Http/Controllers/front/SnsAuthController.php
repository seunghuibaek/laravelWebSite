<?php

namespace App\Http\Controllers\front;

use App\Models\SystemSetting;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SnsAuthController extends Controller
{
    public function redirect(Request $request, string $provider)
    {
        $this->ensureEnabled();
        $provider = strtolower($provider);
        if (!in_array($provider, ['google', 'kakao', 'naver'])) {
            abort(404);
        }

        [$clientId,, $authUrl, $scope, $extra] = $this->providerConfig($provider);
        $state = Str::random(40);
        $request->session()->put('oauth_state_'.$provider, $state);
        $redirectUri = route('auth.sns.callback', ['provider' => $provider]);

        $params = array_merge([
            'response_type' => 'code',
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'scope' => $scope,
            'state' => $state,
        ], $extra['auth_params'] ?? []);

        $query = http_build_query($params);
        return redirect($authUrl.'?'.$query);
    }

    public function callback(Request $request, string $provider)
    {
        $this->ensureEnabled();
        $provider = strtolower($provider);
        if (!in_array($provider, ['google', 'kakao', 'naver'])) {
            abort(404);
        }

        $state = $request->session()->pull('oauth_state_'.$provider);
        if (!$state || $state !== $request->get('state')) {
            return redirect()->route('login')->withErrors(['state' => '잘못된 요청입니다. 다시 시도해주세요.']);
        }
        if (!$request->has('code')) {
            return redirect()->route('login')->withErrors(['code' => '인증 코드가 없습니다.']);
        }

        [$clientId, $clientSecret, , , $extra] = $this->providerConfig($provider);
        $redirectUri = route('auth.sns.callback', ['provider' => $provider]);
        $code = $request->get('code');

        $client = new Client(['http_errors' => false]);

        // 1) Exchange code for token
        try {
            $tokenResp = $client->post($extra['token_url'], [
                'headers' => ['Accept' => 'application/json'],
                'form_params' => array_merge([
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                    'redirect_uri' => $redirectUri,
                    'client_id' => $clientId,
                ], $extra['token_params'] ?? [], $clientSecret ? ['client_secret' => $clientSecret] : []),
            ]);
        } catch (GuzzleException $e) {

        }

        $tokenJson = json_decode((string) $tokenResp->getBody(), true) ?: [];
        $accessToken = $tokenJson['access_token'] ?? null;
        if (!$accessToken) {
            return redirect()->route('login')->withErrors(['oauth' => '토큰 발급에 실패했습니다.']);
        }

        // 2) Fetch user info
        $userInfo = $this->fetchUserInfo($client, $provider, $accessToken);
        if (!$userInfo || !isset($userInfo['id'])) {
            return redirect()->route('login')->withErrors(['oauth' => '사용자 정보를 가져오지 못했습니다.']);
        }

        $normalized = $this->normalizeUser($provider, $userInfo);

        // 3) Find or create user
        $user = User::where('provider', $provider)->where('provider_id', $normalized['id'])->first();
        if (!$user && !empty($normalized['email'])) {
            $user = User::where('email', $normalized['email'])->first();
        }
        if ($user) {
            // attach provider if missing
            if (!$user->provider || !$user->provider_id) {
                $user->provider = $provider;
                $user->provider_id = $normalized['id'];
                if (!empty($normalized['avatar'])) $user->avatar = $normalized['avatar'];
                $user->save();
            }
        } else {
            // create user with random password
            $name = $normalized['name'] ?: ($normalized['email'] ?? ($provider.'_'.$normalized['id']));
            $email = $normalized['email'] ?: ($provider.'_'.$normalized['id'].'@example.local');
            $user = new User();
            $user->name = $name;
            $user->email = $email;
            $user->password = Hash::make(Str::random(24));
            $user->provider = $provider;
            $user->provider_id = $normalized['id'];
            if (!empty($normalized['avatar'])) $user->avatar = $normalized['avatar'];
            $user->save();
        }

        Auth::login($user, true);
        return redirect()->intended(route('home'))->with('success', 'SNS 로그인에 성공했습니다.');
    }

    private function providerConfig(string $provider): array
    {
        $enabled = SystemSetting::get('sns_enabled', false);
        if (!$enabled) {
            abort(403, 'SNS 로그인이 비활성화되어 있습니다.');
        }
        $redirectBase = [
            'google' => [
                'auth_url' => 'https://accounts.google.com/o/oauth2/v2/auth',
                'token_url' => 'https://oauth2.googleapis.com/token',
                'scope' => 'openid email profile',
            ],
            'kakao' => [
                'auth_url' => 'https://kauth.kakao.com/oauth/authorize',
                'token_url' => 'https://kauth.kakao.com/oauth/token',
                'scope' => 'profile_nickname profile_image account_email',
            ],
            'naver' => [
                'auth_url' => 'https://nid.naver.com/oauth2.0/authorize',
                'token_url' => 'https://nid.naver.com/oauth2.0/token',
                'scope' => 'name email',
            ],
        ];
        $cfg = $redirectBase[$provider];

        $clientId = match ($provider) {
            'google' => SystemSetting::get('sns_google_client_id'),
            'kakao' => SystemSetting::get('sns_kakao_client_id'),
            'naver' => SystemSetting::get('sns_naver_client_id'),
        };
        $clientSecret = match ($provider) {
            'google' => SystemSetting::get('sns_google_client_secret'),
            'kakao' => SystemSetting::get('sns_kakao_client_secret'),
            'naver' => SystemSetting::get('sns_naver_client_secret'),
        };
        if (!$clientId) {
            abort(500, strtoupper($provider).' 클라이언트 설정이 필요합니다.');
        }

        $extra = [
            'auth_params' => $provider === 'kakao' ? ['prompt' => 'login'] : [],
            'token_url' => $cfg['token_url'],
            'token_params' => $provider === 'naver' ? ['client_secret' => $clientSecret] : [],
        ];

        return [$clientId, $clientSecret, $cfg['auth_url'], $cfg['scope'], $extra];
    }

    private function fetchUserInfo(Client $client, string $provider, string $accessToken): ?array
    {
        if ($provider === 'google') {
            try {
                $resp = $client->get('https://www.googleapis.com/oauth2/v3/userinfo', [
                    'headers' => ['Authorization' => 'Bearer ' . $accessToken],
                ]);
            } catch (GuzzleException $e) {

            }
            return json_decode((string)$resp->getBody(), true) ?: [];
        }
        if ($provider === 'kakao') {
            try {
                $resp = $client->get('https://kapi.kakao.com/v2/user/me', [
                    'headers' => ['Authorization' => 'Bearer ' . $accessToken],
                ]);
            } catch (GuzzleException $e) {

            }
            return json_decode((string)$resp->getBody(), true) ?: [];
        }
        if ($provider === 'naver') {
            try {
                $resp = $client->get('https://openapi.naver.com/v1/nid/me', [
                    'headers' => ['Authorization' => 'Bearer ' . $accessToken],
                ]);
            } catch (GuzzleException $e) {

            }
            $data = json_decode((string)$resp->getBody(), true) ?: [];
            return $data['response'] ?? null;
        }
        return null;
    }

    private function normalizeUser(string $provider, array $raw): array
    {
        if ($provider === 'google') {
            return [
                'id' => $raw['sub'] ?? ($raw['id'] ?? ''),
                'email' => $raw['email'] ?? null,
                'name' => $raw['name'] ?? ($raw['given_name'] ?? null),
                'avatar' => $raw['picture'] ?? null,
            ];
        }
        if ($provider === 'kakao') {
            $kprofile = $raw['kakao_account']['profile'] ?? [];
            return [
                'id' => (string)($raw['id'] ?? ''),
                'email' => $raw['kakao_account']['email'] ?? null,
                'name' => $kprofile['nickname'] ?? null,
                'avatar' => $kprofile['profile_image_url'] ?? null,
            ];
        }
        if ($provider === 'naver') {
            return [
                'id' => (string)($raw['id'] ?? ''),
                'email' => $raw['email'] ?? null,
                'name' => $raw['name'] ?? ($raw['nickname'] ?? null),
                'avatar' => $raw['profile_image'] ?? null,
            ];
        }
        return ['id' => '', 'email' => null, 'name' => null, 'avatar' => null];
    }

    private function ensureEnabled(): void
    {
        if (!SystemSetting::get('sns_enabled', false)) {
            abort(403, 'SNS 로그인이 비활성화되어 있습니다.');
        }
    }
}
