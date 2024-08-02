<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    // indexアクション（お気に入り一覧ページ）
    // 未ログインのユーザーは会員側のお気に入り一覧ページにアクセスできない
    public function test_guest_cannot_access_favorite_index()
    {
        // 一般ユーザーアカウントの非承認
        $guset = User::factory()->create();
        $this->assertGuest();

        // 会員側のお気に入り一覧ページにアクセス
        $response = $this->get(route('favorites.index'));

        // リダイレクト
        $response->assertRedirect(route('login'));
    }

    // ログイン済みの無料会員は会員側のお気に入り一覧ページにアクセスできない
    public function test_user_free_cannot_access_favorite_index()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 会員側のお気に入り一覧ページにアクセス
        $response = $this->get(route('favorites.index'));

        // リダイレクト
        $response->assertRedirect(route('subscription.create'));
    }

    // ログイン済みの有料会員は会員側のお気に入り一覧ページにアクセスできる
    public function test_user_charge_can_access_favorite_index()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 有料会員の作成
        $user->newSubscription('premium_plan', 'price_1PgMVLGeo7j2tfrTS0pOZqj8')->create('pm_card_visa');

        // 会員側のお気に入り一覧ページにアクセス
        $response = $this->get(route('favorites.index'));

        // アクセス成功したか確認
        $response->assertStatus(200);
    }

    // ログイン済みの管理者は会員側のお気に入り一覧ページにアクセスできない
    public function test_admin_cannot_access_favorite_index()
    {
        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // 会員側のお気に入り一覧ページにアクセス
        $response = $this->get(route('favorites.index'));

        // リダイレクト
        $response->assertRedirect(route('admin.home'));
    }

    // storeアクション（お気に入り追加機能）
    // 未ログインのユーザーはお気に入りに追加できない
    public function test_guest_cannot_add_favorite_store()
    {
        // 一般ユーザーアカウントの非承認
        $guset = User::factory()->create();
        $this->assertGuest();

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // お気に入り登録リクエストの送信
        $response = $this->post(route('favorites.store', $restaurant->id));

        // restaurant_user テーブルにお気に入りが登録されていないことを確認
        $this->assertDatabaseMissing('restaurant_user', ['restaurant_id' => $restaurant->id]);

        // リダイレクト
        $response->assertRedirect(route('login'));
    }

    // ログイン済みの無料会員はお気に入りに追加できない
    public function test_user_free_cannot_add_favorite_store()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // お気に入り登録リクエストの送信
        $response = $this->post(route('favorites.store', $restaurant->id));

        // restaurant_user テーブルにお気に入りが登録されていないことを確認
        $this->assertDatabaseMissing('restaurant_user', ['restaurant_id' => $restaurant->id]);

        // リダイレクト
        $response->assertRedirect(route('subscription.create'));
    }

    // ログイン済みの有料会員はお気に入りに追加できる
    public function test_user_charge_can_add_favorite_store()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 有料会員の作成
        $user->newSubscription('premium_plan', 'price_1PgMVLGeo7j2tfrTS0pOZqj8')->create('pm_card_visa');

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // お気に入り登録リクエストの送信
        $response = $this->post(route('favorites.store', $restaurant->id));

        // restaurant_user テーブルにお気に入りが登録されていることを確認
        $this->assertDatabaseHas('restaurant_user', ['restaurant_id' => $restaurant->id]);

        // アクセス成功したか確認
        $response->assertStatus(302);
    }

    // ログイン済みの管理者はお気に入りに追加できない
    public function test_admin_cannot_add_favorite_store()
    {
        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // お気に入り登録リクエストの送信
        $response = $this->post(route('favorites.store', $restaurant->id));

        // restaurant_user テーブルにお気に入りが登録されていないことを確認
        $this->assertDatabaseMissing('restaurant_user', ['restaurant_id' => $restaurant->id]);

        // リダイレクト
        $response->assertRedirect(route('admin.home'));
    }

    // destroyアクション（お気に入り解除機能）
    // 未ログインのユーザーはお気に入りを解除できない
    public function test_guest_cannot_add_favorite_delete()
    {
        // 一般ユーザーアカウントの非承認
        $guset = User::factory()->create();
        $this->assertGuest();

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // お気に入りの作成
        $guset->favorite_restaurants()->attach($restaurant->id);

        // お気に入り削除リクエストの送信
        $response = $this->delete(route('favorites.destroy', $restaurant->id));

        // リダイレクト
        $response->assertRedirect(route('login'));
    }

    // ログイン済みの無料会員はお気に入りを解除できない
    public function test_user_free_cannot_add_favorite_delete()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // お気に入りの作成
        $user->favorite_restaurants()->attach($restaurant->id);

        // お気に入り削除リクエストの送信
        $response = $this->delete(route('favorites.destroy', $restaurant->id));

        // リダイレクト
        $response->assertRedirect(route('subscription.create'));
    }

    // ログイン済みの有料会員はお気に入りを解除できる
    public function test_user_charge_can_add_favorite_delete()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 有料会員の作成
        $user->newSubscription('premium_plan', 'price_1PgMVLGeo7j2tfrTS0pOZqj8')->create('pm_card_visa');

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // お気に入りの作成
        $user->favorite_restaurants()->attach($restaurant->id);

        // お気に入り削除リクエストの送信
        $response = $this->delete(route('favorites.destroy', $restaurant->id));

        // アクセス成功したか確認
        $response->assertStatus(302);
    }

    // ログイン済みの管理者はお気に入りを解除できない
    public function test_admin_cannot_add_favorite_delete()
    {
        // 一般ユーザーアカウントの非承認
        $guset = User::factory()->create();
        $this->assertGuest();

        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // お気に入りの作成
        $guset->favorite_restaurants()->attach($restaurant->id);

        // お気に入り削除リクエストの送信
        $response = $this->delete(route('favorites.destroy', $restaurant->id));

        // リダイレクト
        $response->assertRedirect(route('admin.home'));
    }
}
