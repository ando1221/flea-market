<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    // メールアドレス未入力でログインしたとき、
    // バリデーションエラーになることを確認するテスト
    public function test_email_is_required(): void
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    // パスワード未入力でログインしたとき、
    // バリデーションエラーになることを確認するテスト
    public function test_password_is_required(): void
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors('password');
    }

    // 登録されていないメールアドレス・パスワードでログインしたとき、
    // バリデーションエラーになることを確認するテスト
    public function test_login_fails_with_invalid_credentials(): void
    {
        $response = $this->post('/login', [
            'email' => 'no-user@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    // 正しいログイン情報を入力したとき、
    // ログイン成功してトップページへリダイレクトされることを確認するテスト
    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/');
    }
}