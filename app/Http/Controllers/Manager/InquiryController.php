<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\front\Controller;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class InquiryController extends Controller
{
    public function index(Request $request)
    {
        $query = Inquiry::with('repliedBy');

        // 검색 필터
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('replied_by')) {
            $query->where('replied_by', $request->replied_by);
        }

        $inquiries = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('manager.inquiries.index', compact('inquiries'));
    }

    public function show(Inquiry $inquiry)
    {
        return view('manager.inquiries.show', compact('inquiry'));
    }

    public function update(Request $request, Inquiry $inquiry)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed',
        ]);

        $inquiry->update(['status' => $request->status]);

        return redirect()->route('manager.inquiries.show', $inquiry)
            ->with('success', '문의 상태가 업데이트되었습니다.');
    }

    public function reply(Request $request, Inquiry $inquiry)
    {
        $request->validate([
            'admin_reply' => 'required|string',
        ]);

        $inquiry->markAsCompleted($request->admin_reply, auth('manager')->id());

        // 답변 이메일 발송 (선택사항)
        try {
            Mail::send('emails.inquiry-reply', [
                'inquiry' => $inquiry,
                'reply' => $request->admin_reply
            ], function ($message) use ($inquiry) {
                $message->to($inquiry->email, $inquiry->name)
                        ->subject('[답변] ' . $inquiry->subject);
            });
        } catch (\Exception $e) {
            // 메일 발송 실패 시 로그 기록 (실제 환경에서는 로그 처리)
        }

        return redirect()->route('manager.inquiries.show', $inquiry)
            ->with('success', '답변이 등록되었습니다.');
    }

    public function destroy(Inquiry $inquiry)
    {
        $inquiry->delete();

        return redirect()->route('manager.inquiries.index')
            ->with('success', '문의가 성공적으로 삭제되었습니다.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|string',
        ]);

        $ids = explode(',', $request->ids);
        $deletedCount = Inquiry::whereIn('id', $ids)->delete();

        return redirect()->route('manager.inquiries.index')
            ->with('success', "{$deletedCount}개의 문의가 삭제되었습니다.");
    }

    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|string',
            'status' => 'required|in:pending,processing,completed',
        ]);

        $ids = explode(',', $request->ids);
        $updatedCount = Inquiry::whereIn('id', $ids)->update(['status' => $request->status]);

        $statusText = [
            'pending' => '대기',
            'processing' => '처리중',
            'completed' => '완료'
        ];

        return redirect()->route('manager.inquiries.index')
            ->with('success', "{$updatedCount}개의 문의 상태가 '{$statusText[$request->status]}'로 변경되었습니다.");
    }
}
