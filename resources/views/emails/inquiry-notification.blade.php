<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>새로운 문의가 접수되었습니다</title>
    <style>
        body {
            font-family: 'Malgun Gothic', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .inquiry-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .info-table th,
        .info-table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        .info-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            width: 30%;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>새로운 문의 접수</h1>
        <p>{{ config('app.name') }}에 새로운 문의가 접수되었습니다.</p>
    </div>
    
    <div class="content">
        <p>안녕하세요, 관리자님!</p>
        
        <p>새로운 문의가 접수되어 알려드립니다. 아래 내용을 확인하시고 빠른 답변 부탁드립니다.</p>
        
        <div class="inquiry-box">
            <h3>문의 정보</h3>
            <table class="info-table">
                <tr>
                    <th>문의 번호</th>
                    <td>#{{ $inquiry->id }}</td>
                </tr>
                <tr>
                    <th>문의자</th>
                    <td>{{ $inquiry->name }}</td>
                </tr>
                <tr>
                    <th>이메일</th>
                    <td>{{ $inquiry->email }}</td>
                </tr>
                <tr>
                    <th>전화번호</th>
                    <td>{{ $inquiry->phone ?: '미입력' }}</td>
                </tr>
                <tr>
                    <th>문의 제목</th>
                    <td>{{ $inquiry->subject }}</td>
                </tr>
                <tr>
                    <th>접수일시</th>
                    <td>{{ $inquiry->created_at->format('Y년 m월 d일 H:i') }}</td>
                </tr>
                <tr>
                    <th>IP 주소</th>
                    <td>{{ $inquiry->ip_address }}</td>
                </tr>
            </table>
            
            <h4>문의 내용</h4>
            <div style="background: #fff; padding: 15px; border-radius: 6px; border: 1px solid #ddd;">
                {!! nl2br(e($inquiry->message)) !!}
            </div>
        </div>
        
        <div style="text-align: center;">
            <a href="{{ route('manager.inquiries.show', $inquiry) }}" class="btn">
                관리자 페이지에서 답변하기
            </a>
        </div>
        
        <div style="background: #e3f2fd; padding: 15px; border-radius: 6px; margin: 20px 0;">
            <h4 style="color: #1976d2; margin-top: 0;">답변 가이드</h4>
            <ul style="margin-bottom: 0;">
                <li>문의 접수 후 영업일 기준 1-2일 내 답변을 목표로 합니다.</li>
                <li>복잡한 문의의 경우 상태를 '처리중'으로 변경해 주세요.</li>
                <li>답변 완료 후 상태를 '완료'로 변경해 주세요.</li>
                <li>필요시 문의자에게 직접 이메일 또는 전화로 연락하세요.</li>
            </ul>
        </div>
    </div>
    
    <div class="footer">
        <p>{{ config('app.name') }} 관리 시스템</p>
        <p>이 메일은 자동으로 발송된 메일입니다.</p>
    </div>
</body>
</html>