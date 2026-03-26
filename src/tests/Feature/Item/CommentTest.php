<?php

namespace Tests\Feature\Item;

use App\Models\Comment;
use App\Models\Condition;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    // ログイン済みユーザーがコメントを送信したとき、
    // コメントが保存されることを確認するテスト
    public function test_logged_in_user_can_post_comment(): void
    {
        $condition = Condition::factory()->create();

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'name' => 'テストユーザー',
            'zip' => '123-4567',
            'address' => '東京都渋谷区1-1-1',
            'building' => 'テストビル',
            'profile_image_path' => 'profiles/test.jpg',
        ]);

        $item = Item::factory()->create([
            'condition_id' => $condition->id,
        ]);

        $response = $this->actingAs($user)->post("/item/{$item->id}/comments", [
            'body' => 'テストコメント',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'body' => 'テストコメント',
        ]);
    }

    // 未ログインユーザーがコメント送信したとき、
    // ログイン画面へリダイレクトされてコメント保存されないことを確認するテスト
    public function test_guest_cannot_post_comment(): void
    {
        $condition = Condition::factory()->create();

        $item = Item::factory()->create([
            'condition_id' => $condition->id,
        ]);

        $response = $this->post("/item/{$item->id}/comments", [
            'body' => 'ゲストコメント',
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseCount('comments', 0);
    }

    // コメント未入力で送信したとき、
    // バリデーションエラーになることを確認するテスト
    public function test_comment_is_required(): void
    {
        $condition = Condition::factory()->create();

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'name' => 'テストユーザー',
            'zip' => '123-4567',
            'address' => '東京都渋谷区1-1-1',
            'building' => 'テストビル',
            'profile_image_path' => 'profiles/test.jpg',
        ]);

        $item = Item::factory()->create([
            'condition_id' => $condition->id,
        ]);

        $response = $this->actingAs($user)->post("/item/{$item->id}/comments", [
            'body' => '',
        ]);

        $response->assertSessionHasErrors('body');
    }

    // コメントが255文字を超えるとき、
    // バリデーションエラーになることを確認するテスト
    public function test_comment_cannot_exceed_255_characters(): void
    {
        $condition = Condition::factory()->create();

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'name' => 'テストユーザー',
            'zip' => '123-4567',
            'address' => '東京都渋谷区1-1-1',
            'building' => 'テストビル',
            'profile_image_path' => 'profiles/test.jpg',
        ]);

        $item = Item::factory()->create([
            'condition_id' => $condition->id,
        ]);

        $response = $this->actingAs($user)->post("/item/{$item->id}/comments", [
            'body' => str_repeat('a', 256),
        ]);

        $response->assertSessionHasErrors('body');
    }
}