<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;

class ProfileController extends Controller
{
    // 認証済み・メール認証済みユーザーのみ許可
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function edit()
    {
        return view('mypage.profile');
    }

    public function update(ProfileRequest $request)
    {
        $validated = $request->validated();

        $user = $request->user();

        // プロフィール画像がアップロードされた場合
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profiles', 'public');
            $validated['profile_image_path'] = $path;
        }

        unset($validated['profile_image']);

        $user->update($validated);

        return redirect()
            ->route('mypage.index')
            ->with('success', 'プロフィールを更新しました');
    }
}