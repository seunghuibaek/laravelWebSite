<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('sort_order')->orderBy('id', 'desc')->paginate(20);
        return view('manager.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('manager.banners.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|max:5120',
            'link_url' => 'nullable|url|max:2048',
            'is_active' => 'sometimes|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        $path = $request->file('image')->store('banners', 'public');

        Banner::create([
            'title' => $data['title'],
            'image_path' => $path,
            'link_url' => $data['link_url'] ?? null,
            'is_active' => (bool)($data['is_active'] ?? true),
            'sort_order' => $data['sort_order'] ?? 0,
            'starts_at' => $data['starts_at'] ?? null,
            'ends_at' => $data['ends_at'] ?? null,
        ]);

        return redirect()->route('manager.banners.index')->with('success', '배너가 등록되었습니다.');
    }

    public function edit(Banner $banner)
    {
        return view('manager.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|max:5120',
            'link_url' => 'nullable|url|max:2048',
            'is_active' => 'sometimes|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        if ($request->hasFile('image')) {
            if ($banner->image_path && Storage::disk('public')->exists($banner->image_path)) {
                Storage::disk('public')->delete($banner->image_path);
            }
            $banner->image_path = $request->file('image')->store('banners', 'public');
        }

        $banner->fill([
            'title' => $data['title'],
            'link_url' => $data['link_url'] ?? null,
            'is_active' => (bool)($data['is_active'] ?? false),
            'sort_order' => $data['sort_order'] ?? 0,
            'starts_at' => $data['starts_at'] ?? null,
            'ends_at' => $data['ends_at'] ?? null,
        ])->save();

        return redirect()->route('manager.banners.index')->with('success', '배너가 수정되었습니다.');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->image_path && Storage::disk('public')->exists($banner->image_path)) {
            Storage::disk('public')->delete($banner->image_path);
        }
        $banner->delete();
        return redirect()->route('manager.banners.index')->with('success', '배너가 삭제되었습니다.');
    }

    public function toggle(Banner $banner)
    {
        $banner->is_active = !$banner->is_active;
        $banner->save();
        return back()->with('success', '배너 상태가 변경되었습니다.');
    }
}
