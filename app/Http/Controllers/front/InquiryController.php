<?php

namespace App\Http\Controllers\front;

use App\Models\Inquiry;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class InquiryController extends Controller
{
    public function create()
    {
        return view('front.inquiry.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $inquiry = Inquiry::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'subject' => $request->subject,
            'message' => $request->message,
            'status' => 'pending',
            'ip_address' => $request->ip(),
        ]);

        // 관리자에게 알림 메일 발송
        try {
            $adminEmail = SystemSetting::get('admin_email', 'admin@example.com');

            Mail::send('emails.inquiry-notification', [
                'inquiry' => $inquiry
            ], function ($message) use ($adminEmail, $inquiry) {
                $message->to($adminEmail)
                        ->subject('[문의] ' . $inquiry->subject);
            });
        } catch (\Exception $e) {
            // 메일 발송 실패 시 로그 기록 (실제 환경에서는 로그 처리)
        }

        return redirect()->route('inquiry.create')
            ->with('success', '문의가 성공적으로 접수되었습니다. 빠른 시일 내에 답변드리겠습니다.');
    }
}
