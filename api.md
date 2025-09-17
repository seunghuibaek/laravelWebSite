### 개요
- 목적: Talend API Tester로 게시글 등록 API를 호출하는 전체 시나리오 제공(로그인 토큰 획득 포함)
- 엔드포인트: POST http://127.0.0.1:8000/api/board/{board_code}/posts
- 인증: Bearer Token (Laravel Sanctum)
- 전제: 프로젝트는 User 모델에 HasApiTokens 적용, 라우트는 auth:sanctum 보호, 컨트롤러에서 로그인 사용자만 허용

---

### 사전 체크리스트
- 서버 실행: http://127.0.0.1:8000 가 동작 중인지
- 대상 게시판: is_active = true, allow_user_write = true
- 파일 업로드 정책: use_file_upload, max_file_count, max_file_size(MB)
- 사용자 계정 존재 여부(토큰 발급에 필요)

---

### Sanctum 토큰을 얻는 3가지 방법
아래 중 사용 가능한 방법을 선택하세요.

#### 방법 A) 개발용으로 가장 빠름: Artisan Tinker에서 개인 액세스 토큰 발급
1) 프로젝트 루트로 이동
    - PowerShell: cd C:\works\993.laravel_ai\project
2) Tinker 실행
    - php artisan tinker
3) 토큰 발급
    - $user = \App\Models\User::first();
    - $token = $user->createToken('talend')->plainTextToken;
    - $token  // 이 값이 Bearer 토큰
4) Talend에서 Authorization: Bearer {위에서 받은 토큰}으로 사용

토큰 무효화(선택):
- $user->tokens()->delete();  // 해당 사용자 토큰 전체 삭제

장점: 구현 추가 없이 즉시 사용. 단점: 서버 콘솔 접근 필요.

#### 방법 B) 기존 로그인 API가 있을 때
- 예시: POST http://127.0.0.1:8000/api/login
- Body(JSON): { "email": "user@example.com", "password": "secret" }
- 성공 응답 예: { "token": "..." }
- Talend에서 Extractor로 token 값을 환경 변수에 저장 후 사용
  주의: 현재 제공된 코드에는 별도 로그인 API가 보이지 않습니다. 없다면 A 또는 C를 사용하세요.

#### 방법 C) 임시 토큰 발급 API 추가(개발용, 운영 금지)
- routes/api.php에 임시 엔드포인트를 추가(코드 변경 가능할 때만):
    - Route::post('/auth/token', function (\Illuminate\Http\Request $request) {
      $request->validate(['email'=>'required|email','password'=>'required']);
      $user = \App\Models\User::where('email',$request->email)->first();
      if (!$user || !\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
      return response()->json(['message'=>'Invalid credentials'], 401);
      }
      return response()->json(['token'=>$user->createToken('talend')->plainTextToken]);
      });
- Talend에서 해당 엔드포인트로 로그인 → token 추출 → 이후 요청에 Bearer 적용

---

### Talend API Tester 시나리오(End-to-End)
이 시나리오는 “토큰 획득 → 글 등록(JSON) → 글 등록(multipart)”을 하나의 컬렉션/테스트로 구성하는 방법입니다.

#### 1) Environment(환경 변수) 설정
- Environments → New Environment
- Variables 추가:
    - base_url = http://127.0.0.1:8000
    - board_code = free  (필요에 맞게 변경)
    - token =  (비워둠; Extractor로 채움 또는 수동 입력)

#### 2) Step 1 - 토큰 얻기 요청(Get Token)
- 방법 A(Tinker)를 쓴다면 이 Step은 건너뛰고, Tinker에서 얻은 토큰을 token 변수에 직접 붙여넣어도 됩니다.
- 방법 B 또는 C를 사용할 경우 Talend에 요청 생성:
    - Name: Get Token
    - Method: POST
    - URL: {{base_url}}/api/login  (또는 {{base_url}}/api/auth/token)
    - Headers:
        - Accept: application/json
        - Content-Type: application/json
    - Body (raw JSON):
        - { "email": "user@example.com", "password": "secret" }
    - Send 클릭 후 200/201 응답에서 token 값 확인

- Extractor 설정(응답 → token 변수 저장):
    - Response 탭 → Extractors → Add → JSONPath
    - JSONPath: $.token
    - Save as: token (Scope: Environment)
    - Save 후 재전송하면 token 변수가 자동 채워짐

Tip: 상단 Variables 패널에서 token 값이 보이는지 확인하세요.

#### 3) Step 2 - 게시글 등록(JSON)
- Name: Create Post (JSON)
- Method: POST
- URL: {{base_url}}/api/board/{{board_code}}/posts
- Headers:
    - Accept: application/json
    - Content-Type: application/json
    - Authorization: Bearer {{token}}
- Body (raw JSON):
    - {
      "title": "Talend로 등록한 첫 글",
      "content": "본문 내용입니다.",
      "is_secret": false
      }
- Send → 기대: 201 Created, message 및 post 오브젝트 수신

#### 4) Step 3 - 게시글 등록(파일 업로드, multipart/form-data)
- Name: Create Post (multipart)
- Method: POST
- URL: {{base_url}}/api/board/{{board_code}}/posts
- Headers:
    - Accept: application/json
    - Authorization: Bearer {{token}}
    - Content-Type는 비워두세요(폼 구성 시 Talend가 자동 지정)
- Body: Multipart form
    - title = 파일 포함 글
    - content = 이미지와 함께 업로드합니다.
    - is_secret = false
    - files[] = [Choose File] (여러 파일이면 files[] 키를 여러 줄 추가)
- Send → 기대: 201 Created

주의사항:
- 파일 업로드는 해당 게시판의 use_file_upload가 true일 때만 동작
- 용량 제한: files.* 검증은 max_file_size(MB) × 1024(KB) 기준
- 개수 제한: max_file_count 이내

---

### 성공/오류 응답 예시
- 성공(201):
    - {
      "message": "게시글이 등록되었습니다.",
      "post": {
      "id": 123,
      "board_code": "free",
      "title": "Talend로 등록한 첫 글",
      "content": "본문 내용입니다.",
      "author_name": "홍길동",
      "created_at": "2025-09-17T08:21:35.000000Z",
      "url": "http://127.0.0.1:8000/board/free/123"
      }
      }

- 401 Unauthorized:
    - 원인: Authorization 누락/오류, 토큰 만료
    - 조치: Bearer {{token}} 확인, 토큰 재발급

- 403 Forbidden:
    - 원인: 해당 게시판에서 글쓰기 비활성화(allow_user_write=false)
    - 조치: 관리자 기능설정 변경

- 404 Not Found:
    - 원인: board_code 오타, 비활성 게시판(is_active=false)
    - 조치: 코드/상태 확인

- 422 Unprocessable Entity:
    - 원인: title/content 누락, 파일 용량/개수 초과
    - 조치: 필드/정책 준수

---

### Talend 활용 팁
- Environments에 base_url, board_code, token 변수화 → 여러 테스트 간 재사용
- 컬렉션 순서: 1) Get Token → 2) Create Post(JSON) → 3) Create Post(multipart)
- Extractor로 token 자동 저장하여 재시도 시 편리
- 필요하면 Pre-request Script로 token 미설정 시 경고 로그 출력(선택)

---

### 요약 및 권장 경로
- 가장 간단: 방법 A(Tinker)로 토큰을 발급받고 Talend의 token 변수에 붙여넣기
- 자동화 필요: 방법 B(로그인 API) 또는 C(임시 발급 API)로 Get Token → Extractor로 token 저장
- 이후 Authorization: Bearer {{token}}으로 /api/board/{{board_code}}/posts 호출

원하시면 실제 이메일/보드코드/파일 예시를 알려주세요. Talend 스크린샷 기준으로 Extractor 설정 경로와 값까지 맞춤 안내해 드리겠습니다.
