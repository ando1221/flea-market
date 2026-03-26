<?php

namespace Tests\Feature\Item;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Condition;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemShowTest extends TestCase
{
    use RefreshDatabase;

    // 商品詳細ページを開いたとき、
    // 必要な情報がすべて表示されることを確認するテスト
    public function test_item_detail_shows_required_information(): void
    {
        $condition = Condition::factory()->create([
            'label' => '良好',
        ]);

        $commentUser = User::factory()->create([
            'name' => 'コメントユーザー',
            'profile_image_path' => null,
        ]);

        $item = Item::factory()->create([
            'condition_id' => $condition->id,
            'name' => '腕時計',
            'brand_name' => 'Rolax',
            'price' => 15000,
            'description' => 'スタイリッシュな腕時計です',
            'image_path' => 'products/test.jpg',
        ]);

        $category1 = Category::factory()->create([
            'name' => 'ファッション',
        ]);

        $category2 = Category::factory()->create([
            'name' => 'メンズ',
        ]);

        $item->categories()->sync([$category1->id, $category2->id]);

        Comment::create([
            'user_id' => $commentUser->id,
            'item_id' => $item->id,
            'body' => 'とても気になります',
        ]);

        $response = $this->get("/item/{$item->id}");

        $response->assertStatus(200);
        $response->assertSee('腕時計');
        $response->assertSee('Rolax');
        $response->assertSee('15,000');
        $response->assertSee('スタイリッシュな腕時計です');
        $response->assertSee('ファッション');
        $response->assertSee('メンズ');
        $response->assertSee('良好');
        $response->assertSee('コメントユーザー');
        $response->assertSee('とても気になります');
    }

    // 商品詳細ページを開いたとき、
    // 複数選択されたカテゴリが表示されることを確認するテスト
    public function test_multiple_categories_are_displayed(): void
    {
        $condition = Condition::factory()->create();

        $item = Item::factory()->create([
            'condition_id' => $condition->id,
            'name' => 'バッグ',
        ]);

        $category1 = Category::factory()->create([
            'name' => 'レディース',
        ]);

        $category2 = Category::factory()->create([
            'name' => 'ハンドメイド',
        ]);

        $category3 = Category::factory()->create([
            'name' => 'アクセサリー',
        ]);

        $item->categories()->sync([
            $category1->id,
            $category2->id,
            $category3->id,
        ]);

        $response = $this->get("/item/{$item->id}");

        $response->assertStatus(200);
        $response->assertSee('レディース');
        $response->assertSee('ハンドメイド');
        $response->assertSee('アクセサリー');
    }
}