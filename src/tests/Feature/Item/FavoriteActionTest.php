<?php

namespace Tests\Feature\Item;

use App\Models\Condition;
use App\Models\Favorite;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteActionTest extends TestCase
{
    use RefreshDatabase;

    // ログイン済みユーザーがいいねアイコンを押したとき、
    // いいねした商品として登録されることを確認するテスト
    public function test_user_can_favorite_item(): void
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

        $response = $this->actingAs($user)->post("/item/{$item->id}/favorite");

        $response->assertStatus(302);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    // すでにいいね済みの商品で詳細ページを開いたとき、
    // いいね済み判定が取れることを確認するテスト
    public function test_favorited_item_is_recognized_as_favorited(): void
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

        Favorite::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->actingAs($user)->get("/item/{$item->id}");

        $response->assertStatus(200);
        $response->assertSee('ハートロゴ_ピンク.png');
    }

    // いいね済みの商品でもう一度いいね解除したとき、
    // いいね情報が削除されることを確認するテスト
    public function test_user_can_unfavorite_item(): void
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

        Favorite::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->actingAs($user)->delete("/item/{$item->id}/favorite");

        $response->assertStatus(302);

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }
}