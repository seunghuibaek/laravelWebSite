<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $settings = [
            [
                'key' => 'site_name',
                'value' => '웹사이트 이름',
                'type' => 'text',
                'group' => 'general',
                'label' => '사이트 이름',
                'description' => '웹사이트의 기본 이름입니다.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'site_description',
                'value' => '웹사이트 설명',
                'type' => 'text',
                'group' => 'general',
                'label' => '사이트 설명',
                'description' => '웹사이트에 대한 간단한 설명입니다.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'admin_email',
                'value' => 'admin@example.com',
                'type' => 'text',
                'group' => 'general',
                'label' => '관리자 이메일',
                'description' => '문의사항이 전송될 관리자 이메일 주소입니다.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'posts_per_page',
                'value' => '10',
                'type' => 'number',
                'group' => 'board',
                'label' => '페이지당 게시글 수',
                'description' => '한 페이지에 표시할 게시글 수입니다.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'use_captcha',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'security',
                'label' => '캡차 사용',
                'description' => '게시글 작성 시 캡차를 사용할지 여부입니다.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // SNS 로그인 설정
            [
                'key' => 'sns_enabled',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'general',
                'label' => 'SNS 로그인 사용',
                'description' => '네이버, 카카오, 구글 SNS 로그인을 사용할지 여부입니다.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Google
            [
                'key' => 'sns_google_client_id',
                'value' => '',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Google Client ID',
                'description' => 'Google OAuth 2.0 클라이언트 ID',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'sns_google_client_secret',
                'value' => '',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Google Client Secret',
                'description' => 'Google OAuth 2.0 클라이언트 Secret',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Kakao
            [
                'key' => 'sns_kakao_client_id',
                'value' => '',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Kakao REST API Key',
                'description' => '카카오 OAuth 2.0 REST API 키 (Client ID)',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'sns_kakao_client_secret',
                'value' => '',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Kakao Client Secret',
                'description' => '카카오 OAuth 2.0 Client Secret (선택)',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Naver
            [
                'key' => 'sns_naver_client_id',
                'value' => '',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Naver Client ID',
                'description' => '네이버 OAuth 2.0 클라이언트 ID',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'sns_naver_client_secret',
                'value' => '',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Naver Client Secret',
                'description' => '네이버 OAuth 2.0 클라이언트 Secret',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('system_settings')->insert($settings);
    }
}
