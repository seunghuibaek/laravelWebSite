<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>문의 답변</title>
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
        .reply-box {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #2196f3;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>문의 답변</h1>
        <p>{{ config('app.name') }}에서 답변드립니다.</p>
    </div>
    
    <div class="content">
        <p>안녕하세요, {{ $inquiry->name }}님!</p>
        
        <p>문의해 주신 내용에 대해 답변드립니다.</p>
        
        <div class="inquiry-box">
            <h3>원본 문의</h3>
            <p><strong>제목:</strong> {{ $inquiry->subject }}</p>
            <p><strong>문의일:</strong> {{ $inquiry->created_at->format('Y년 m월 d일 H:i') }}</p>
            <p><strong>내용:</strong></p>
            <p>{!! nl2br(e($inquiry->message)) !!}</p>
        </div>
        
        <div class="reply-box">
            <h3>답변</h3>
            <p>{!! nl2br(e($reply)) !!}</p>
        </div>
        
        <p>추가 문의사항이 있으시면 언제든지 연락 주시기 바랍니다.</p>
        
        <p>감사합니다.</p>
    </div>
    
    <div class="footer">
        <p>{{ config('app.name') }}</p>
        <p>이 메일은 자동으로 발송된 메일입니다.</p>
    </div>
</body>
</html>