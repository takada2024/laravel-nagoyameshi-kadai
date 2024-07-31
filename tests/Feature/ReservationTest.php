<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    // indexアクション（予約一覧ページ）
    // 未ログインのユーザーは会員側の予約一覧ページにアクセスできない
    public function test_guest_cannot_access_reservation_index()
    {
        // 一般ユーザーアカウントの非承認
        $guset = User::factory()->create();
        $this->assertGuest();

        // 会員側の予約一覧ページにアクセス
        $response = $this->get(route('reservations.index'));

        // リダイレクト
        $response->assertRedirect(route('login'));
    }

    // ログイン済みの無料会員は会員側の予約一覧ページにアクセスできない
    public function test_user_free_cannot_access_reservation_index()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 会員側の予約一覧ページにアクセス
        $response = $this->get(route('reservations.index'));

        // リダイレクト
        $response->assertRedirect(route('subscription.create'));
    }

    // ログイン済みの有料会員は会員側の予約一覧ページにアクセスできる
    public function test_user_charge_can_access_reservation_index()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 有料会員の作成
        $user->newSubscription('premium_plan', 'price_1PgMVLGeo7j2tfrTS0pOZqj8')->create('pm_card_visa');

        // 会員側の予約一覧ページにアクセス
        $response = $this->get(route('reservations.index'));

        // アクセス成功したか確認
        $response->assertStatus(200);
    }

    // ログイン済みの管理者は会員側の予約一覧ページにアクセスできない
    public function test_admin_cannot_access_review_index()
    {
        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // 会員側の予約一覧ページにアクセス
        $response = $this->get(route('reservations.index'));

        // リダイレクト
        $response->assertRedirect(route('admin.home'));
    }

    // createアクション（予約ページ）
    // 未ログインのユーザーは会員側の予約ページにアクセスできない
    public function test_guest_cannot_access_reservation_create()
    {
        // 一般ユーザーアカウントの非承認
        $guset = User::factory()->create();
        $this->assertGuest();

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // 会員側の予約一覧ページにアクセス
        $response = $this->get(route('restaurants.reservations.create', $restaurant->id));

        // リダイレクト
        $response->assertRedirect(route('login'));
    }

    // ログイン済みの無料会員は会員側の予約ページにアクセスできない
    public function test_user_free_cannot_access_reservation_create()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // 会員側の予約一覧ページにアクセス
        $response = $this->get(route('restaurants.reservations.create', $restaurant->id));

        // リダイレクト
        $response->assertRedirect(route('subscription.create'));
    }

    // ログイン済みの有料会員は会員側の予約ページにアクセスできる
    public function test_user_charge_can_access_reservation_create()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // 有料会員の作成
        $user->newSubscription('premium_plan', 'price_1PgMVLGeo7j2tfrTS0pOZqj8')->create('pm_card_visa');

        // 会員側の予約一覧ページにアクセス
        $response = $this->get(route('restaurants.reservations.create', $restaurant->id));

        // アクセス成功したか確認
        $response->assertStatus(200);
    }

    // ログイン済みの管理者は会員側の予約ページにアクセスできない
    public function test_admin_cannot_access_review_create()
    {
        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // 会員側の予約一覧ページにアクセス
        $response = $this->get(route('restaurants.reservations.create', $restaurant->id));

        // リダイレクト
        $response->assertRedirect(route('admin.home'));
    }

    // storeアクション（予約機能）
    // 未ログインのユーザーは予約できない
    public function test_guest_cannot_store_reservation_store()
    {
        // 一般ユーザーアカウントの非承認
        $guset = User::factory()->create();
        $this->assertGuest();

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // 予約データの作成
        $reservation_data = [
            'reserved_datetime' => now(),
            'number_of_people' => fake()->numberBetween(1, 50),
            'restaurant_id' => $restaurant->id,
            'user_id' => $guset->id
        ];

        // 店舗予約の登録リクエストの送信
        $response = $this->post(route('restaurants.reservations.store', $restaurant->id), $reservation_data);

        // reservations テーブルにレビューが登録されていないことを確認
        $this->assertDatabaseMissing('reservations', $reservation_data);

        // リダイレクト
        $response->assertRedirect(route('login'));
    }

    // ログイン済みの無料会員は予約できない
    public function test_user_free_cannot_store_reservation_store()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // 予約データの作成
        $reservation_data = [
            'reserved_datetime' => now(),
            'number_of_people' => fake()->numberBetween(1, 50),
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ];

        // 店舗予約の登録リクエストの送信
        $response = $this->post(route('restaurants.reservations.store', $restaurant->id), $reservation_data);

        // reservations テーブルにレビューが登録されていないことを確認
        $this->assertDatabaseMissing('reservations', $reservation_data);

        // リダイレクト
        $response->assertRedirect(route('subscription.create'));
    }

    // ログイン済みの有料会員は予約できる
    public function test_user_charge_can_store_reservation_store()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // 有料会員の作成
        $user->newSubscription('premium_plan', 'price_1PgMVLGeo7j2tfrTS0pOZqj8')->create('pm_card_visa');

        // 予約データの作成
        $reservation_data = [
            'reserved_datetime' => now(),
            'number_of_people' => fake()->numberBetween(1, 50),
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ];

        // 店舗予約の登録リクエストの送信
        $response = $this->post(route('restaurants.reservations.store', $restaurant->id), $reservation_data);

        // reservations テーブルにレビューが登録されていることを確認
        $this->assertDatabaseMissing('reservations', $reservation_data);

        // リダイレクト
        $response->assertRedirect(route('admin.home'));
    }

    // ログイン済みの管理者は予約できない
    public function test_admin_cannot_store_review_store()
    {
        // 一般ユーザーアカウントの非承認
        $guset = User::factory()->create();
        $this->assertGuest();

        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // 予約データの作成
        $reservation_data = [
            'reserved_datetime' => now(),
            'number_of_people' => fake()->numberBetween(1, 50),
            'restaurant_id' => $restaurant->id,
            'user_id' => $guset->id
        ];

        // 店舗予約の登録リクエストの送信
        $response = $this->post(route('restaurants.reservations.store', $restaurant->id), $reservation_data);

        // reservations テーブルにレビューが登録されていないことを確認
        $this->assertDatabaseMissing('reservations', $reservation_data);

        // リダイレクト
        $response->assertRedirect(route('admin.home'));
    }

    // destroyアクション（予約キャンセル機能）
    // 未ログインのユーザーは予約をキャンセルできない
    public function test_guest_cannot_destroy_reservations_destroy()
    {
        // 一般ユーザーアカウントの非承認
        $guest = User::factory()->create();
        $this->assertGuest();

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // 予約データの作成
        $reservation_data = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $guest->id
        ]);

        // 店舗予約の削除リクエストの送信
        $response = $this->delete(route('reservations.destroy', $reservation_data));

        // reservations テーブルにレビューが削除されていないことを確認
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation_data->id,
            'restaurant_id' => $restaurant->id,
            'user_id' => $guest->id
        ]);

        // リダイレクト
        $response->assertRedirect(route('login'));
    }

    // ログイン済みの無料会員は予約をキャンセルできない
    public function test_user_cannot_destroy_reservations_destroy()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // 予約データの作成
        $reservation_data = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        // 店舗予約の削除リクエストの送信
        $response = $this->delete(route('reservations.destroy', $reservation_data));

        // reservations テーブルにレビューが削除されていないことを確認
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation_data->id,
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        // リダイレクト
        $response->assertRedirect(route('subscription.create'));
    }

    // ログイン済みの有料会員は他人の予約をキャンセルできない
    public function test_user_charge_cannot_destroy_other_reservations_destroy()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 有料会員の作成
        $user->newSubscription('premium_plan', 'price_1PgMVLGeo7j2tfrTS0pOZqj8')->create('pm_card_visa');

        // 他人のユーザーアカウントの承認
        $other = User::factory()->create();

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // 他人の予約データの作成
        $reservation_data_other = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $other->id
        ]);

        // 店舗予約の削除リクエストの送信
        $response = $this->delete(route('reservations.destroy', $reservation_data_other));

        // reservations テーブルにレビューが削除されていないことを確認
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation_data_other->id,
            'restaurant_id' => $restaurant->id,
            'user_id' => $other->id
        ]);

        // リダイレクト
        $response->assertRedirect(route('reservations.index'));
    }

    // ログイン済みの有料会員は自身の予約をキャンセルできる
    public function test_user_charge_can_destroy_reservations_destroy()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 有料会員の作成
        $user->newSubscription('premium_plan', 'price_1PgMVLGeo7j2tfrTS0pOZqj8')->create('pm_card_visa');

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // 予約データの作成
        $reservation_data = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        // 店舗予約の削除リクエストの送信
        $response = $this->delete(route('reservations.destroy', $reservation_data));

        // reservations テーブルにレビューが削除されていることを確認
        $this->assertDatabaseMissing('reservations', [
            'id' => $reservation_data->id,
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        // リダイレクト
        $response->assertRedirect(route('reservations.index'));
    }

    // ログイン済みの管理者は予約をキャンセルできない
    public function test_admin_cannot_destroy_reservations_destroy()
    {
        // ユーザーアカウントの非承認
        $guest = User::factory()->create();
        $this->assertGuest();

        // 一般ユーザーアカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // 予約データの作成
        $reservation_data = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $guest->id
        ]);

        // 店舗予約の削除リクエストの送信
        $response = $this->delete(route('reservations.destroy', $reservation_data));

        // reservations テーブルにレビューが削除されていないことを確認
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation_data->id,
            'restaurant_id' => $restaurant->id,
            'user_id' => $guest->id
        ]);

        // リダイレクト
        $response->assertRedirect(route('admin.home'));
    }
}
