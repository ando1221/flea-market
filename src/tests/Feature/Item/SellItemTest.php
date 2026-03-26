<?php

namespace Tests\Feature\Item;

use App\Models\Category;
use App\Models\Condition;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SellItemTest extends TestCase
{
    use RefreshDatabase;

    // ログイン済みユーザーが商品出品画面から必要な情報を入力して保存したとき、
    // 商品情報が items テーブルと category_item テーブルに正しく保存されることを確認するテスト
    public function test_user_can_store_item_with_required_fields(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'name' => '出品ユーザー',
            'zip' => '123-4567',
            'address' => '東京都渋谷区1-1-1',
            'building' => 'テストビル',
            'profile_image_path' => 'profiles/test.jpg',
        ]);

        $condition = Condition::factory()->create([
            'label' => '良好',
        ]);

        $category1 = Category::factory()->create([
            'name' => 'ファッション',
        ]);

        $category2 = Category::factory()->create([
            'name' => 'メンズ',
        ]);

        $file = UploadedFile::fake()->create('item.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($user)->post('/sell', [
            'name' => 'テスト商品',
            'brand_name' => 'テストブランド',
            'description' => '商品の説明です',
            'price' => 5000,
            'condition_id' => $condition->id,
            'category_ids' => [$category1->id, $category2->id],
            'image' => $file,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('items', [
            'user_id' => $user->id,
            'name' => 'テスト商品',
            'brand_name' => 'テストブランド',
            'description' => '商品の説明です',
            'price' => 5000,
            'condition_id' => $condition->id,
        ]);

        $item = Item::where('name', 'テスト商品')->firstOrFail();

        $this->assertDatabaseHas('category_item', [
            'item_id' => $item->id,
            'category_id' => $category1->id,
        ]);

        $this->assertDatabaseHas('category_item', [
            'item_id' => $item->id,
            'category_id' => $category2->id,
        ]);

        $this->assertNotNull($item->image_path);
    }
}