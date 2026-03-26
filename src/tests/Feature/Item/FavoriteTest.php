<?php

namespace Tests\Feature\Item;

use App\Models\Condition;
use App\Models\Favorite;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    // ログイン済みユーザーがマイリストページを開いたとき、
    // いいねした商品だけが表示されることを確認するテスト
    public function test_only_favorited_items_are_displayed_in_mylist(): void
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

        $likedItem = Item::factory()->create([
            'condition_id' => $condition->id,
            'name' => 'いいね商品',
        ]);

        $notLikedItem = Item::factory()->create([
            'condition_id' => $condition->id,
            'name' => '未いいね商品',
        ]);

        Favorite::create([
            'user_id' => $user->id,
            'item_id' => $likedItem->id,
        ]);

        $response = $this->actingAs($user)->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertSee('いいね商品');
        $response->assertDontSee('未いいね商品');
    }

    // マイリスト内に購入済み商品があるとき、
    // Sold ラベルが表示されることを確認するテスト
    public function test_sold_label_is_displayed_in_mylist(): void
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

        $item = Item::factory()->sold()->create([
            'condition_id' => $condition->id,
            'name' => '売却済み商品',
        ]);

        Favorite::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->actingAs($user)->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertSee('売却済み商品');
        $response->assertSee('Sold');
    }

    // 未ログインユーザーがマイリストページを開いたとき、
    // loginページを表示するテスト（未ログインではマイリストを表示しない）
    public function test_guest_sees_mylist_link_to_login(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('href="' . route('login') . '"', false);
    }
}