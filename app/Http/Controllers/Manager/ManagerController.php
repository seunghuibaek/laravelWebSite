<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\front\Controller;
use App\Models\Manager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ManagerController extends Controller
{
    public function index()
    {
        $managers = Manager::orderBy('created_at', 'desc')->paginate(15);

        return view('manager.managers.index', compact('managers'));
    }

    public function create()
    {
        return view('manager.managers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:managers',
            'email' => 'required|string|email|max:255|unique:managers',
            'password' => 'required|string|min:8|confirmed',
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:super_admin,admin,manager',
            'status' => 'required|in:active,inactive',
        ]);

        Manager::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'name' => $request->name,
            'phone' => $request->phone,
            'role' => $request->role,
            'status' => $request->status,
        ]);

        return redirect()->route('manager.managers.index')
            ->with('success', '관리자가 성공적으로 등록되었습니다.');
    }

    public function show(Manager $manager)
    {
        return view('manager.managers.show', compact('manager'));
    }

    public function edit(Manager $manager)
    {
        return view('manager.managers.edit', compact('manager'));
    }

    public function update(Request $request, Manager $manager)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:255', Rule::unique('managers')->ignore($manager->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('managers')->ignore($manager->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:super_admin,admin,manager',
            'status' => 'required|in:active,inactive',
        ]);

        $data = [
            'username' => $request->username,
            'email' => $request->email,
            'name' => $request->name,
            'phone' => $request->phone,
            'role' => $request->role,
            'status' => $request->status,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $manager->update($data);

        return redirect()->route('manager.managers.index')
            ->with('success', '관리자 정보가 성공적으로 수정되었습니다.');
    }

    public function destroy(Manager $manager)
    {
        // 자기 자신은 삭제할 수 없음
        if ($manager->id === auth('manager')->id()) {
            return redirect()->route('manager.managers.index')
                ->with('error', '자기 자신은 삭제할 수 없습니다.');
        }

        $manager->delete();

        return redirect()->route('manager.managers.index')
            ->with('success', '관리자가 성공적으로 삭제되었습니다.');
    }
}
