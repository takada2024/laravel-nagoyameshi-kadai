<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    // indexアクション（レビュー一覧ページ）
    // 未ログインのユーザーは会員側のレビュー一覧ページにアクセスできない
    public function test_guest_cannot_access_review_index()
    {
        // 一般ユーザーアカウントの非承認
        $guset = User::factory()->create();
        $this->assertGuest();

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // 会員側のレビュー一覧ページにアクセス
        $response = $this->get(route('restaurants.reviews.index', $restaurant->id));

        // リダイレクト
        $response->assertRedirect(route('login'));
    }

    // ログイン済みの無料会員は会員側のレビュー一覧ページにアクセスできる
    public function test_user_free_can_access_review_index()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // 会員側のレビュー一覧ページにアクセス
        $response = $this->get(route('restaurants.reviews.index', $restaurant->id));

        // アクセス成功したか確認
        $response->assertStatus(200);
    }

    // ログイン済みの有料会員は会員側のレビュー一覧ページにアクセスできる
    public function test_user_charge_can_access_review_index()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // 有料会員の作成
        $user->newSubscription('premium_plan', 'price_1PgMVLGeo7j2tfrTS0pOZqj8')->create('pm_card_visa');

        // 会員側のレビュー一覧ページにアクセス
        $response = $this->get(route('restaurants.reviews.index', $restaurant->id));

        // アクセス成功したか確認
        $response->assertStatus(200);
    }

    // ログイン済みの管理者は会員側のレビュー一覧ページにアクセスできない
    public function test_admin_cannot_access_review_index()
    {
        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // 会員側のレビュー一覧ページにアクセス
        $response = $this->get(route('restaurants.reviews.index', $restaurant->id));

        // リダイレクト
        $response->assertRedirect(route('admin.home'));
    }

    // createアクション（レビュー投稿ページ）
    // 未ログインのユーザーは会員側のレビュー投稿ページにアクセスできない
    public function test_guest_cannot_access_review_create()
    {
        // 一般ユーザーアカウントの非承認
        $guset = User::factory()->create();
        $this->assertGuest();

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // 会員側のレビュー投稿ページにアクセス
        $response = $this->get(route('restaurants.reviews.create', $restaurant->id));

        // リダイレクト
        $response->assertRedirect(route('login'));
    }

    // ログイン済みの無料会員は会員側のレビュー投稿ページにアクセスできない
    public function test_user_free_cannot_access_review_create()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // 会員側のレビュー投稿ページにアクセス
        $response = $this->get(route('restaurants.reviews.create', $restaurant->id));

        // リダイレクト
        $response->assertRedirect(route('subscription.create'));
    }

    // ログイン済みの有料会員は会員側のレビュー投稿ページにアクセスできる
    public function test_user_charge_can_access_review_create()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // 有料会員の作成
        $user->newSubscription('premium_plan', 'price_1PgMVLGeo7j2tfrTS0pOZqj8')->create('pm_card_visa');

        // 会員側のレビュー投稿ページにアクセス
        $response = $this->get(route('restaurants.reviews.create', $restaurant->id));

        // アクセス成功したか確認
        $response->assertStatus(200);
    }

    // ログイン済みの管理者は会員側のレビュー投稿ページにアクセスできない
    public function test_admin_cannot_access_review_create()
    {
        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // 会員側のレビュー投稿ページにアクセス
        $response = $this->get(route('restaurants.reviews.create', $restaurant->id));

        // リダイレクト
        $response->assertRedirect(route('admin.home'));
    }

    // storeアクション（レビュー投稿機能）
    // 未ログインのユーザーはレビューを投稿できない
    public function test_guest_cannot_post_review_store()
    {
        // 一般ユーザーアカウントの非承認
        $guset = User::factory()->create();
        $this->assertGuest();

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // レビューの作成
        $review_data = [
            'score' => 3,
            'content' => 'テストTest',
            'restaurant_id' => $restaurant->id,
            'user_id' => $guset->id
        ];

        // レビュー登録リクエストの送信
        $response = $this->post(route('restaurants.reviews.store', $restaurant->id), $review_data);

        // reviews テーブルにレビューが登録されていないことを確認
        $this->assertDatabaseMissing('reviews', $review_data);

        // リダイレクト
        $response->assertRedirect(route('login'));
    }

    // ログイン済みの無料会員はレビューを投稿できない
    public function test_user_free_cannot_post_review_store()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // レビューの作成
        $review_data = [
            'score' => 3,
            'content' => 'テストTest',
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ];

        // レビュー登録リクエストの送信
        $response = $this->post(route('restaurants.reviews.store', $restaurant->id), $review_data);

        // reviews テーブルにレビューが登録されていないことを確認
        $this->assertDatabaseMissing('reviews', $review_data);

        // リダイレクト
        $response->assertRedirect(route('subscription.create'));
    }

    // ログイン済みの有料会員はレビューを投稿できる
    public function test_user_charge_can_post_review_store()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 有料会員の作成
        $user->newSubscription('premium_plan', 'price_1PgMVLGeo7j2tfrTS0pOZqj8')->create('pm_card_visa');

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // レビューの作成
        $review_data = [
            'score' => 3,
            'content' => 'テストTest',
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ];

        // レビュー登録リクエストの送信
        $response = $this->post(route('restaurants.reviews.store', $restaurant->id), $review_data);

        // reviews テーブルにレビューが登録されていることを確認
        $this->assertDatabaseHas('reviews', $review_data);

        // リダイレクト
        $response->assertRedirect(route('restaurants.reviews.index', $restaurant->id));
    }

    // ログイン済みの管理者はレビューを投稿できない
    public function test_admin_cannot_post_review_store()
    {
        // 一般ユーザーアカウントの非承認
        $guset = User::factory()->create();
        $this->assertGuest();

        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // レビューの作成
        $review_data = [
            'score' => 3,
            'content' => 'テストTest',
            'restaurant_id' => $restaurant->id,
            'user_id' => $guset->id
        ];

        // レビュー登録リクエストの送信
        $response = $this->post(route('restaurants.reviews.store', $restaurant->id), $review_data);

        // reviews テーブルにレビューが登録されていないことを確認
        $this->assertDatabaseMissing('reviews', $review_data);

        // リダイレクト
        $response->assertRedirect(route('admin.home'));
    }

    // editアクション（レビュー編集ページ）
    // 未ログインのユーザーは会員側のレビュー編集ページにアクセスできない
    public function test_guest_cannot_access_review_edit()
    {
        // 一般ユーザーアカウントの非承認
        $guset = User::factory()->create();
        $this->assertGuest();

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // レビューの作成
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $guset->id
        ]);

        // 会員側のレビュー編集ページにアクセス
        $response = $this->get(route('restaurants.reviews.edit', [$restaurant->id, $review->id]));

        // リダイレクト
        $response->assertRedirect(route('login'));
    }

    // ログイン済みの無料会員はレビュー編集ページにアクセスできない
    public function test_user_free_cannot_access_review_edit()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // レビューの作成
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        // 会員側のレビュー編集ページにアクセス
        $response = $this->get(route('restaurants.reviews.edit', [$restaurant->id, $review->id]));

        // リダイレクト
        $response->assertRedirect(route('subscription.create'));
    }

    // ログイン済みの有料会員は会員側の自身のレビュー編集ページにアクセスできる
    public function test_user_charge_can_access_my_review_edit()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // レビューの作成
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        // 有料会員の作成
        $user->newSubscription('premium_plan', 'price_1PgMVLGeo7j2tfrTS0pOZqj8')->create('pm_card_visa');

        // 会員側のレビュー編集ページにアクセス
        $response = $this->get(route('restaurants.reviews.edit', [$restaurant->id, $review->id]));

        // アクセス成功したか確認
        $response->assertStatus(200);
    }

    // ログイン済みの有料会員は会員側の他人のレビュー編集ページにアクセスできない
    public function test_user_charge_cannot_access_other_review_edit()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 有料会員の作成
        $user->newSubscription('premium_plan', 'price_1PgMVLGeo7j2tfrTS0pOZqj8')->create('pm_card_visa');

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // 他人のユーザーアカウントの作成
        $other = User::factory()->create();

        // 他人のレビューの作成
        $other_review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $other->id
        ]);

        // 会員側のレビュー編集ページにアクセス
        $response = $this->get(route('restaurants.reviews.edit', [$restaurant->id, $other_review->id]));

        // リダイレクト
        $response->assertRedirect(route('restaurants.reviews.index', $restaurant->id));
    }

    // ログイン済みの管理者は会員側のレビュー編集ページにアクセスできない
    public function test_admin_cannot_access_review_edit()
    {
        // 一般ユーザーアカウントの非承認
        $guset = User::factory()->create();
        $this->assertGuest();

        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // レビューの作成
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $guset->id
        ]);

        // 会員側のレビュー編集ページにアクセス
        $response = $this->get(route('restaurants.reviews.edit', [$restaurant->id, $review->id]));

        // リダイレクト
        $response->assertRedirect(route('admin.home'));
    }

    // updateアクション（レビュー更新機能）
    // 未ログインのユーザーはレビューを更新できない
    public function test_guest_cannot_update_review_update()
    {
        // 一般ユーザーアカウントの非承認
        $guest = User::factory()->create();
        $this->assertGuest();

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // 更新「前」レビューの作成
        $review_data_old = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $guest->id
        ]);

        // 更新「後」レビューの作成
        $review_data_new = [
            'score' => 5,
            'content' => '更新Kousin',
        ];

        // レビュー更新リクエストの送信
        $response = $this->patch(route('restaurants.reviews.update', [$restaurant->id, $review_data_old->id]), $review_data_new);

        // reviews テーブルにレビューが登録されていないことを確認
        $this->assertDatabaseMissing('reviews', $review_data_new);

        // リダイレクト
        $response->assertRedirect(route('login'));
    }

    // ログイン済みの無料会員はレビューを更新できない
    public function test_user_free_cannot_update_review_update()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // 更新「前」レビューの作成
        $review_data_old = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        // 更新「後」レビューの作成
        $review_data_new = [
            'score' => 5,
            'content' => '更新Kousin',
        ];

        // レビュー更新リクエストの送信
        $response = $this->patch(route('restaurants.reviews.update', [$restaurant->id, $review_data_old->id]), $review_data_new);

        // reviews テーブルにレビューが登録されていないことを確認
        $this->assertDatabaseMissing('reviews', $review_data_new);

        // リダイレクト
        $response->assertRedirect(route('subscription.create'));
    }

    // ログイン済みの有料会員は他人のレビューを更新できない
    public function test_user_charge_cannot_update_other_review_update()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 他人のアカウントの承認
        $user_other = User::factory()->create();
        $this->actingAs($user_other);

        // 有料会員の作成
        $user->newSubscription('premium_plan', 'price_1PgMVLGeo7j2tfrTS0pOZqj8')->create('pm_card_visa');

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // 他人のレビューの作成
        $review_data_other = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user_other->id
        ]);

        // 更新「後」レビューの作成
        $review_data_new = [
            'score' => 5,
            'content' => '更新Kousin',
        ];

        // レビュー更新リクエストの送信
        $response = $this->patch(route('restaurants.reviews.update', [$restaurant->id, $review_data_other->id]), $review_data_new);

        // reviews テーブルにレビューが登録されていないことを確認
        $this->assertDatabaseMissing('reviews', $review_data_new);

        // リダイレクト
        $response->assertRedirect(route('subscription.create'));
    }

    // ログイン済みの有料会員は自身のレビューを更新できる
    public function test_user_charge_can_update_review_update()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 有料会員の作成
        $user->newSubscription('premium_plan', 'price_1PgMVLGeo7j2tfrTS0pOZqj8')->create('pm_card_visa');

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // 更新「前」レビューの作成
        $review_data_old = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        // 更新「後」レビューの作成
        $review_data_new = [
            'score' => 5,
            'content' => '更新Kousin',
        ];

        // レビュー更新リクエストの送信
        $response = $this->patch(route('restaurants.reviews.update', [$restaurant->id, $review_data_old->id]), $review_data_new);

        // reviews テーブルにレビューが登録されていることを確認
        $this->assertDatabaseHas('reviews', $review_data_new);

        // リダイレクト
        $response->assertRedirect(route('restaurants.reviews.index', $restaurant->id));
    }

    // ログイン済みの管理者はレビューを更新できない
    public function test_admin_cannot_update_review_update()
    {
        // 一般ユーザーアカウントの非承認
        $user = User::factory()->create();
        $this->assertGuest();

        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // 更新「前」レビューの作成
        $review_data_old = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        // 更新「後」レビューの作成
        $review_data_new = [
            'score' => 5,
            'content' => '更新Kousin',
        ];

        // レビュー更新リクエストの送信
        $response = $this->patch(route('restaurants.reviews.update', [$restaurant->id, $review_data_old->id]), $review_data_new);

        // reviews テーブルにレビューが登録されていないことを確認
        $this->assertDatabaseMissing('reviews', $review_data_new);

        // リダイレクト
        $response->assertRedirect(route('admin.home'));
    }

    // destroyアクション（レビュー削除機能）
    // 未ログインのユーザーはレビューを削除できない
    public function test_guest_cannot_delete_review_delete()
    {
        // 一般ユーザーアカウントの非承認
        $guest = User::factory()->create();
        $this->assertGuest();

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // レビューの作成
        $review_data = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $guest->id
        ]);

        // レビュー削除リクエストの送信
        $response = $this->delete(route('restaurants.reviews.destroy', [$restaurant->id, $review_data->id]));

        // reviews テーブルにレビューが削除されていないことを確認
        $this->assertDatabaseHas('reviews', [
            'id' => $review_data->id,
            'restaurant_id' => $restaurant->id,
            'user_id' => $guest->id
        ]);

        // リダイレクト
        $response->assertRedirect(route('login'));
    }

    // ログイン済みの無料会員はレビューを削除できない
    public function test_user_free_cannot_delete_review_delete()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // レビューの作成
        $review_data = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        // レビュー削除リクエストの送信
        $response = $this->delete(route('restaurants.reviews.destroy', [$restaurant->id, $review_data->id]));

        // reviews テーブルにレビューが削除されていないことを確認
        $this->assertDatabaseHas('reviews', [
            'id' => $review_data->id,
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        // リダイレクト
        $response->assertRedirect(route('subscription.create'));
    }

    // ログイン済みの有料会員は他人のレビューを削除できない
    public function test_user_charge_cannot_delete_other_review_delete()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 他人のアカウントの承認
        $user_other = User::factory()->create();
        $this->actingAs($user_other);

        // 有料会員の作成
        $user->newSubscription('premium_plan', 'price_1PgMVLGeo7j2tfrTS0pOZqj8')->create('pm_card_visa');

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // 他人のレビューの作成
        $review_data_other = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user_other->id
        ]);

        // レビュー削除リクエストの送信
        $response = $this->delete(route('restaurants.reviews.destroy', [$restaurant->id, $review_data_other->id]));

        // reviews テーブルにレビューが削除されていないことを確認
        $this->assertDatabaseHas('reviews', [
            'id' => $review_data_other->id,
            'restaurant_id' => $restaurant->id,
            'user_id' => $user_other->id
        ]);

        // リダイレクト
        $response->assertRedirect(route('subscription.create'));
    }

    // ログイン済みの有料会員は自身のレビューを削除できる
    public function test_user_charge_can_delete_review_delete()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 有料会員の作成
        $user->newSubscription('premium_plan', 'price_1PgMVLGeo7j2tfrTS0pOZqj8')->create('pm_card_visa');

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // レビューの作成
        $review_data = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        // レビュー削除リクエストの送信
        $response = $this->delete(route('restaurants.reviews.destroy', [$restaurant->id, $review_data->id]));

        // reviews テーブルにレビューが削除されていることを確認
        $this->assertDatabaseMissing('reviews', [
            'id' => $review_data->id,
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        // リダイレクト
        $response->assertRedirect(route('restaurants.reviews.index', $restaurant->id));
    }

    // ログイン済みの管理者はレビューを削除できない
    public function test_admin_cannot_delete_review_delete()
    {
        // 一般ユーザーアカウントの非承認
        $guest = User::factory()->create();
        $this->assertGuest();

        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // 店舗アカウントの作成
        $restaurant = Restaurant::factory()->create();

        // レビューの作成
        $review_data = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $guest->id
        ]);

        // レビュー削除リクエストの送信
        $response = $this->delete(route('restaurants.reviews.destroy', [$restaurant->id, $review_data->id]));

        // reviews テーブルにレビューが削除されていないことを確認
        $this->assertDatabaseHas('reviews', [
            'id' => $review_data->id,
            'restaurant_id' => $restaurant->id,
            'user_id' => $guest->id
        ]);

        // リダイレクト
        $response->assertRedirect(route('admin.home'));
    }
}
