<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Admin;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RestaurantTest extends TestCase
{
    use RefreshDatabase;

    // indexアクション（店舗一覧ページ）
    // 未ログインのユーザーは会員側の店舗一覧ページにアクセスできる
    public function test_guest_can_access_restaurants_index()
    {
        // 一般ユーザーアカウントの未承認
        $guest = User::factory()->create();
        $this->assertGuest();

        // 会員側の店舗一覧ページにアクセス
        $response = $this->get(route('restaurants.index'));

        // アクセス成功したか確認
        $response->assertStatus(200);
    }

    // ログイン済みの一般ユーザーは会員側の店舗一覧ページにアクセスできる
    public function test_user_can_access_restaurants_index()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 会員側の店舗一覧ページにアクセス
        $response = $this->get(route('restaurants.index'));

        // アクセス成功したか確認
        $response->assertStatus(200);
    }

    // ログイン済みの管理者は会員側の店舗一覧ページにアクセスできない
    public function test_admin_cannot_access_restaurants_index()
    {
        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // 会員側の店舗一覧ページにアクセス
        $response = $this->get(route('restaurants.index'));

        // リダイレクト
        $response->assertRedirect(route('admin.home'));
    }

    // showアクション（店舗一覧ページ）
    // 未ログインのユーザーは会員側の店舗詳細ページにアクセスできる
    public function test_guest_can_access_restaurants_show()
    {
        // 一般ユーザーアカウントの未承認
        $guest = User::factory()->create();
        $this->assertGuest();

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // 会員側の店舗一覧ページにアクセス
        $response = $this->get(route('restaurants.show', $restaurant));

        // アクセス成功したか確認
        $response->assertStatus(200);
    }

    // ログイン済みの一般ユーザーは会員側の店舗詳細ページにアクセスできる
    public function test_user_can_access_restaurants_show()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // 会員側の店舗一覧ページにアクセス
        $response = $this->get(route('restaurants.show', $restaurant));

        // アクセス成功したか確認
        $response->assertStatus(200);
    }

    // ログイン済みの管理者は会員側の店舗詳細ページにアクセスできない
    public function test_admin_cannot_access_restaurants_show()
    {
        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // 会員側の店舗一覧ページにアクセス
        $response = $this->get(route('restaurants.show', $restaurant));

        // リダイレクト
        $response->assertRedirect(route('admin.home'));
    }
}
