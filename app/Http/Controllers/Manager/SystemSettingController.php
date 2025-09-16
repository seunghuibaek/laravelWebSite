<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\front\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SystemSettingController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::orderBy('group')->orderBy('key')->get()->groupBy('group');

        return view('manager.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
        ]);

        foreach ($request->settings as $key => $value) {
            $setting = SystemSetting::where('key', $key)->first();

            if ($setting) {
                // boolean 타입 처리
                if ($setting->type === 'boolean') {
                    $value = $request->has("settings.{$key}") ? 'true' : 'false';
                }

                $setting->update(['value' => $value]);
            }
        }

        return redirect()->route('manager.settings.index')
            ->with('success', '시스템 설정이 성공적으로 저장되었습니다.');
    }

    public function create()
    {
        return view('manager.settings.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|max:255|unique:system_settings',
            'value' => 'nullable|string',
            'type' => 'required|in:text,number,boolean,json',
            'group' => 'required|string|max:255',
            'label' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        SystemSetting::create($request->all());

        return redirect()->route('manager.settings.index')
            ->with('success', '새 설정이 성공적으로 추가되었습니다.');
    }

    public function destroy(SystemSetting $setting)
    {
        $setting->delete();

        return redirect()->route('manager.settings.index')
            ->with('success', '설정이 성공적으로 삭제되었습니다.');
    }
}
