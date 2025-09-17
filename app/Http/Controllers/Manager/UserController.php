<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\front\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($search = $request->get('q')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderByDesc('id')->paginate(20)->withQueryString();

        return view('manager.users.index', compact('users'));
    }

    public function create()
    {
        return view('manager.users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);
        $user->save();

        return redirect()->route('manager.users.index')->with('success', '회원이 생성되었습니다.');
    }

    public function edit(User $user)
    {
        return view('manager.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();

        return redirect()->route('manager.users.index')->with('success', '회원 정보가 업데이트되었습니다.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('manager.users.index')->with('success', '회원이 삭제되었습니다.');
    }
}
