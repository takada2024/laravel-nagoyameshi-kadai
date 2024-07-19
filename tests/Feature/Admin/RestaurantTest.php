<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RestaurantTest extends TestCase
{
    use RefreshDatabase;

    // ~~~indexアクション（店舗一覧ページ）~~~
    // 未ログインのユーザーは管理者側の店舗一覧ページにアクセスできない
    public function test_guest_cannot_access_admin_restaurants_index()
    {
        // 一般ユーザーアカウントの未承認
        $this->assertGuest();

        // 店舗一覧ページページにアクセス
        $response = $this->get(route('admin.restaurants.index'));

        // リダイレクト
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側の店舗一覧ページにアクセスできない
    public function test_user_cannot_access_admin_restaurants_index()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 店舗一覧ページページにアクセス
        $response = $this->get(route('admin.restaurants.index'));

        // リダイレクト
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は管理者側の店舗一覧ページにアクセスできる
    public function test_admin_can_access_admin_restaurants_index()
    {
        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // 店舗一覧ページページにアクセス
        $response = $this->get(route('admin.restaurants.index'));

        // アクセス成功したか確認
        $response->assertStatus(200);
    }

    // ~~showアクション（店舗詳細ページ）~~
    // 未ログインのユーザーは管理者側の店舗詳細ページにアクセスできない
    public function test_guest_cannot_access_admin_restaurants_show()
    {
        // 一般ユーザーアカウントの未承認
        $this->assertGuest();

        // 店舗アカウント作成
        $restaurant = Restaurant::factory()->create();

        // 店舗詳細ページページにアクセス
        $response = $this->get(route('admin.restaurants.show', $restaurant));

        // リダイレクト
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側の店舗詳細ページにアクセスできない
    public function test_user_cannot_access_admin_restaurants_show()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 店舗アカウント作成
        $restaurant = Restaurant::factory()->create();

        // 店舗詳細ページページにアクセス
        $response = $this->get(route('admin.restaurants.show', $restaurant));

        // リダイレクト
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は管理者側の店舗詳細ページにアクセスできる
    public function test_admin_can_access_admin_restaurants_show()
    {
        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // 店舗アカウント作成
        $restaurant = Restaurant::factory()->create();

        // 店舗詳細ページページにアクセス
        $response = $this->get(route('admin.restaurants.show', $restaurant));

        // アクセス成功したか確認
        $response->assertStatus(200);
    }

    // ~~createアクション（店舗登録ページ）~~
    // 未ログインのユーザーは管理者側の店舗登録ページにアクセスできない
    public function test_guest_cannot_access_admin_restaurants_create()
    {
        // 一般ユーザーアカウントの未承認
        $this->assertGuest();

        // 店舗登録ページページにアクセス
        $response = $this->get(route('admin.restaurants.create'));

        // リダイレクト
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側の店舗登録ページにアクセスできない
    public function test_user_cannot_access_admin_restaurants_create()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 店舗登録ページページにアクセス
        $response = $this->get(route('admin.restaurants.create'));

        // リダイレクト
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は管理者側の店舗登録ページにアクセスできる
    public function test_admin_can_access_admin_restaurants_create()
    {
        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // 店舗登録ページページにアクセス
        $response = $this->get(route('admin.restaurants.create'));

        // アクセス成功したか確認
        $response->assertStatus(200);
    }

    // ~~storeアクション（店舗登録機能）~~
    // 未ログインのユーザーは店舗を登録できない
    public function test_guest_cannot_register_admin_restaurants_store()
    {
        // ユーザーがログインされていないことの確認
        $this->assertGuest();

        // 店舗のデータ
        $restaurant_data = [
            'name' => 'テストテスト',
            'description' => 'テストテスト',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '0000000',
            'address' => 'テストテスト',
            'opening_time' => '10:00:00',
            'closing_time' => '20:00:00',
            'seating_capacity' => 50,
        ];

        // 店舗登録リクエストの送信
        $response = $this->post(route('admin.restaurants.store'), $restaurant_data);

        // restaurants テーブルに店舗が登録されていないことを確認
        $this->assertDatabaseMissing('restaurants', $restaurant_data);

        // リダイレクト
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは店舗を登録できない
    public function test_user_cannot_register_admin_restaurants_store()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 店舗のデータ
        $restaurant_data = [
            'name' => 'テストテスト',
            'description' => 'テストテスト',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '0000000',
            'address' => 'テストテスト',
            'opening_time' => '10:00:00',
            'closing_time' => '20:00:00',
            'seating_capacity' => 50,
        ];

        // 店舗登録リクエストの送信
        $response = $this->post(route('admin.restaurants.store'), $restaurant_data);

        // restaurants テーブルに店舗が登録されていないことを確認
        $this->assertDatabaseMissing('restaurants', $restaurant_data);

        // リダイレクト
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は店舗を登録できる
    public function test_admin_can_register_admin_restaurants_store()
    {
        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // 店舗のデータ
        $restaurant_data = [
            'name' => 'テストテスト',
            'description' => 'テストテスト',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '0000000',
            'address' => 'テストテスト',
            'opening_time' => '10:00:00',
            'closing_time' => '20:00:00',
            'seating_capacity' => 50,
        ];

        // 店舗登録リクエストの送信
        $response = $this->post(route('admin.restaurants.store'), $restaurant_data);

        // restaurants テーブルに店舗が登録されていることを確認
        $this->assertDatabaseHas('restaurants', $restaurant_data);

        // リダイレクト
        $response->assertRedirect(route('admin.restaurants.index'));
    }

    // ~~editアクション（店舗編集ページ）~~
    // 未ログインのユーザーは管理者側の店舗編集ページにアクセスできない
    public function test_guest_cannot_access_admin_restaurants_edit()
    {
        // 一般ユーザーアカウントの未承認
        $this->assertGuest();

        // 店舗アカウント作成
        $restaurant = Restaurant::factory()->create();

        // 店舗編集ページページにアクセス
        $response = $this->get(route('admin.restaurants.edit', $restaurant));

        // リダイレクト
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側の店舗編集ページにアクセスできない
    public function test_user_cannot_access_admin_restaurants_edit()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 店舗アカウント作成
        $restaurant = Restaurant::factory()->create();

        // 店舗編集ページページにアクセス
        $response = $this->get(route('admin.restaurants.edit', $restaurant));

        // リダイレクト
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は管理者側の店舗編集ページにアクセスできる
    public function test_admin_can_access_admin_restaurants_edit()
    {
        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // 店舗アカウント作成
        $restaurant = Restaurant::factory()->create();

        // 店舗編集ページページにアクセス
        $response = $this->get(route('admin.restaurants.edit', $restaurant));

        // アクセス成功したか確認
        $response->assertStatus(200);
    }

    // updateアクション（店舗更新機能）
    // 未ログインのユーザーは店舗を更新できない
    public function test_guest_cannot_register_admin_restaurants_update()
    {
        // 一般ユーザーアカウントの未承認
        $this->assertGuest();

        // 更新「前」の店舗アカウント作成
        $old_restaurant = Restaurant::factory()->create();

        // 更新「後」の店舗アカウント作成
        $new_restaurant_data = [
            'name' => '更新',
            'description' => '更新',
            'lowest_price' => 8000,
            'highest_price' => 9000,
            'postal_code' => '7654321',
            'address' => '更新',
            'opening_time' => '14:00:00',
            'closing_time' => '24:00:00',
            'seating_capacity' => 80
        ];
        // 店舗登録リクエストの送信
        $response = $this->patch(route('admin.restaurants.update', $old_restaurant), $new_restaurant_data);

        // restaurants テーブルに店舗が登録されていないことを確認
        $this->assertDatabaseMissing('restaurants', $new_restaurant_data);

        // リダイレクト
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは店舗を更新できない
    public function test_user_cannot_register_admin_restaurants_update()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 更新「前」の店舗アカウント作成
        $old_restaurant = Restaurant::factory()->create();

        // 更新「後」の店舗アカウント作成
        $new_restaurant_data = [
            'name' => '更新',
            'description' => '更新',
            'lowest_price' => 8000,
            'highest_price' => 9000,
            'postal_code' => '7654321',
            'address' => '更新',
            'opening_time' => '14:00:00',
            'closing_time' => '24:00:00',
            'seating_capacity' => 80
        ];
        // 店舗登録リクエストの送信
        $response = $this->patch(route('admin.restaurants.update', $old_restaurant), $new_restaurant_data);

        // restaurants テーブルに店舗が登録されていないことを確認
        $this->assertDatabaseMissing('restaurants', $new_restaurant_data);

        // リダイレクト
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は店舗を更新できる
    public function test_admin_can_register_admin_restaurants_update()
    {
        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // 更新「前」の店舗アカウント作成
        $old_restaurant = Restaurant::factory()->create();

        // 更新「後」の店舗アカウント作成
        $new_restaurant_data = [
            'name' => '更新',
            'description' => '更新',
            'lowest_price' => 8000,
            'highest_price' => 9000,
            'postal_code' => '7654321',
            'address' => '更新',
            'opening_time' => '14:00:00',
            'closing_time' => '24:00:00',
            'seating_capacity' => 80
        ];

        // 店舗登録リクエストの送信
        $response = $this->patch(route('admin.restaurants.update', $old_restaurant), $new_restaurant_data);

        // restaurants テーブルに店舗が登録されていることを確認
        $this->assertDatabaseHas('restaurants', $new_restaurant_data);

        // リダイレクト
        $response->assertRedirect(route('admin.restaurants.show', $old_restaurant));
    }

    // destroyアクション（店舗削除機能）
    // 未ログインのユーザーは店舗を削除できない
    public function test_guest_cannot_access_admin_restaurants_destroy()
    {
        // 一般ユーザーアカウントの未承認
        $this->assertGuest();

        // 店舗アカウント作成
        $restaurant = Restaurant::factory()->create();

        // 店舗の削除
        $response = $this->delete(route('admin.restaurants.destroy', $restaurant));

        // restaurants テーブルに店舗が存在することを確認
        $this->assertDatabaseHas('restaurants', ['id' => $restaurant->id]);

        // リダイレクト
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは店舗を削除できない
    public function test_user_cannot_access_admin_restaurants_destroy()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 店舗アカウント作成
        $restaurant = Restaurant::factory()->create();

        // 店舗の削除
        $response = $this->delete(route('admin.restaurants.destroy', $restaurant));

        // restaurants テーブルに店舗が存在することを確認
        $this->assertDatabaseHas('restaurants', ['id' => $restaurant->id]);

        // リダイレクト
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は店舗を削除できる
    public function test_admin_can_access_admin_restaurants_destroy()
    {
        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // 店舗アカウント作成
        $restaurant = Restaurant::factory()->create();

        // 店舗の削除
        $response = $this->delete(route('admin.restaurants.destroy', $restaurant));

        // restaurants テーブルに店舗が存在しないことを確認
        $this->assertDatabaseMissing('restaurants', ['id' => $restaurant->id]);

        // リダイレクト
        $response->assertRedirect(route('admin.restaurants.index'));
    }
}
