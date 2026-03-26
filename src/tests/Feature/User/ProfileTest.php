<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    // プロフィール編集画面を開いたとき、
    // 各項目の初期値が正しく表示されることを確認するテスト
    public function test_profile_edit_form_shows_current_user_values(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'name' => 'テストユーザー',
            'zip' => '123-4567',
            'address' => '東京都渋谷区1-1-1',
            'building' => 'テストビル',
            'profile_image_path' => 'profiles/test.jpg',
        ]);

        $response = $this->actingAs($user)->get('/mypage/profile');

        $response->assertStatus(200);
        $response->assertSee('テストユーザー');
        $response->assertSee('123-4567');
        $response->assertSee('東京都渋谷区1-1-1');
        $response->assertSee('テストビル');
    }

    // プロフィール編集画面から情報を更新したとき、
    // ユーザー情報が正しく更新されることを確認するテスト
    public function test_user_can_update_profile(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'name' => '更新前ユーザー',
            'zip' => '111-1111',
            'address' => '更新前住所',
            'building' => '更新前ビル',
            'profile_image_path' => null,
        ]);

        $file = UploadedFile::fake()->create('profile.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($user)->post('/mypage/profile', [
            'name' => '更新後ユーザー',
            'zip' => '222-2222',
            'address' => '更新後住所',
            'building' => '更新後ビル',
            'profile_image' => $file,
        ]);

        $response->assertRedirect('/mypage');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => '更新後ユーザー',
            'zip' => '222-2222',
            'address' => '更新後住所',
            'building' => '更新後ビル',
        ]);

        $this->assertNotNull($user->fresh()->profile_image_path);
    }
}