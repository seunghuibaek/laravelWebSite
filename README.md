# Laravel 웹사이트 프로젝트

라라벨을 이용한 완전한 웹사이트 시스템입니다. 관리자 페이지와 프론트 페이지를 포함하고 있습니다.

## 주요 기능

### 관리자 페이지 (/manager)
- **관리자 인증**: 로그인/로그아웃 시스템
- **관리자 관리**: 관리자 계정 CRUD
- **게시판 관리**: 게시판 생성 및 설정 (Froala Editor 지원)
- **댓글 관리**: 댓글 및 대댓글 관리
- **문의하기 관리**: 문의사항 처리 및 답변
- **사이트 통계**: 차트를 이용한 통계 대시보드
- **시스템 설정**: 사이트 전반 설정 관리

### 프론트 페이지
- **메인 페이지**: 공지사항 및 최근 게시글 표시
- **게시판**: 일반 게시판 및 갤러리 게시판 지원
- **문의하기**: 온라인 문의 양식 (관리자 메일 알림)
- **검색**: 전체 게시글 검색 기능

## 기술 스택

- **Backend**: Laravel 12.x
- **Frontend**: Bootstrap 5.3, jQuery
- **Editor**: Froala Editor
- **Database**: SQLite (기본), MySQL/PostgreSQL 지원
- **Icons**: Font Awesome 6.0

## 설치 방법

1. **의존성 설치**
```bash
composer install
npm install
```

2. **환경 설정**
```bash
cp .env.example .env
php artisan key:generate
```

3. **데이터베이스 설정**
```bash
php artisan migrate
php artisan db:seed
```

4. **스토리지 링크 생성**
```bash
php artisan storage:link
```

5. **서버 실행**
```bash
php artisan serve
```

## 기본 계정

### 관리자 계정
- **아이디**: admin
- **비밀번호**: password
- **접속 URL**: http://localhost:8000/manager/login

## 주요 디렉토리 구조

```
app/
├── Http/Controllers/
│   ├── Manager/          # 관리자 컨트롤러
│   ├── BoardController.php
│   ├── CommentController.php
│   ├── HomeController.php
│   └── InquiryController.php
├── Models/               # 모델 클래스
│   ├── Manager.php
│   ├── Board.php
│   ├── BoardPost.php
│   ├── BoardFile.php
│   ├── Comment.php
│   ├── Inquiry.php
│   └── SystemSetting.php
└── Http/Middleware/
    └── ManagerAuth.php

resources/views/
├── manager/              # 관리자 페이지 뷰
│   ├── layouts/
│   ├── auth/
│   ├── managers/
│   ├── boards/
│   ├── comments/
│   ├── inquiries/
│   ├── settings/
│   └── statistics/
├── front/                # 프론트 페이지 뷰
│   ├── layouts/
│   ├── board/
│   └── inquiry/
└── emails/               # 이메일 템플릿

database/
├── migrations/           # 데이터베이스 마이그레이션
└── seeders/             # 시드 데이터

public/
├── css/
│   ├── manager.css      # 관리자 페이지 스타일
│   └── front.css        # 프론트 페이지 스타일
└── js/
    ├── manager.js       # 관리자 페이지 스크립트
    └── front.js         # 프론트 페이지 스크립트
```

## 게시판 기능

### 게시판 타입
- **일반 게시판**: 텍스트 기반 게시판
- **갤러리 게시판**: 이미지 중심 게시판

### 게시판 설정
- 게시판 코드 및 이름
- 파일 업로드 설정 (개수, 크기 제한)
- 공지사항 사용 여부
- 에디터 사용 여부 (Froala Editor)
- 댓글 사용 여부

## 파일 업로드

- 업로드된 파일은 `storage/app/public/uploads/` 디렉토리에 저장
- 게시판별로 별도 폴더 생성
- 파일 크기 및 개수 제한 설정 가능
- 이미지 파일 미리보기 지원

## 메일 기능

- 문의하기 접수 시 관리자에게 알림 메일 발송
- 문의 답변 시 문의자에게 답변 메일 발송
- HTML 이메일 템플릿 사용

## 보안 기능

- 관리자 인증 미들웨어
- CSRF 토큰 보호
- 비밀번호 해싱
- XSS 방지
- SQL 인젝션 방지 (Eloquent ORM)

## 커스터마이징

### 시스템 설정
관리자 페이지에서 다음 설정들을 변경할 수 있습니다:
- 사이트 이름 및 설명
- 관리자 이메일
- 페이지당 게시글 수
- 기타 사이트 설정

### 스타일 커스터마이징
- `public/css/manager.css`: 관리자 페이지 스타일
- `public/css/front.css`: 프론트 페이지 스타일

### 기능 확장
- 새로운 게시판 타입 추가
- 추가 필드 및 기능 구현
- 플러그인 시스템 구축

## 라이센스

MIT License

## 지원

문의사항이 있으시면 관리자에게 연락해 주세요.