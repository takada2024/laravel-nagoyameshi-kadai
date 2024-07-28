<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    // createアクション（有料プラン登録ページ）
    // 未ログインのユーザーは有料プラン登録ページにアクセスできない
    public function test_guest_cannot_access_subscription_create()
    {
        // 一般ユーザーアカウントの非承認
        $guset = User::factory()->create();
        $this->assertGuest();

        // 有料プラン登録ページにアクセス
        $response = $this->get(route('subscription.create'));

        // リダイレクト
        $response->assertRedirect(route('login'));
    }

    // ログイン済みの無料会員は有料プラン登録ページにアクセスできる
    public function test_free_user_can_access_subscription_create()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 有料プラン登録ページにアクセス
        $response = $this->get(route('subscription.create'));

        // アクセス成功したか確認
        $response->assertStatus(200);
    }

    // ログイン済みの有料会員は有料プラン登録ページにアクセスできない
    public function test_paid_user_can_access_subscription_create()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 有料会員の承認
        $user->newSubscription(
            'premium_plan',
            'price_1PgMVLGeo7j2tfrTS0pOZqj8'
        )->create('pm_card_visa');

        // 有料プラン登録ページにアクセス
        $response = $this->get(route('subscription.create'));

        // リダイレクト
        $response->assertRedirect(route('subscription.edit'));
    }

    // ログイン済みの管理者は有料プラン登録ページにアクセスできない
    public function test_admin_cannot_access_subscription_create()
    {
        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // 有料プラン登録ページにアクセス
        $response = $this->get(route('subscription.create'));

        // リダイレクト
        $response->assertRedirect(route('admin.home'));
    }

    // storeアクション（有料プラン登録機能）
    // 未ログインのユーザーは有料プランに登録できない
    public function test_guest_cannot_store_subscription_store()
    {
        // 一般ユーザーアカウントの非承認
        $guset = User::factory()->create();
        $this->assertGuest();

        // paymentMethodIdパラメータ（支払い方法のID
        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];

        // 有料プラン登録ページにアクセス
        $response = $this->get(route('subscription.store'));

        // 有料プランリクエストの送信
        $response = $this->post(route('subscription.store'), $request_parameter);

        // リダイレクト
        $response->assertRedirect(route('login'));
    }

    // ログイン済みの無料会員は有料プランに登録できる
    public function test_free_user_can_store_subscription_store()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // paymentMethodIdパラメータ（支払い方法のID
        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];

        // 有料プラン登録ページにアクセス
        $response = $this->get(route('subscription.store'));

        // 有料プランリクエストの送信
        $response = $this->post(route('subscription.store'), $request_parameter);

        // 登録後にUserインスタンスをリフレッシュ
        $user->refresh();

        // リダイレクト
        $response->assertRedirect(route('home'));
    }

    // ログイン済みの有料会員は有料プランに登録できない
    public function test_paid_user_cannot_store_subscription_store()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 有料会員の承認
        $user->newSubscription(
            'premium_plan',
            'price_1PgMVLGeo7j2tfrTS0pOZqj8'
        )->create('pm_card_visa');

        // paymentMethodIdパラメータ（支払い方法のID
        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];

        // 有料プラン登録ページにアクセス
        $response = $this->get(route('subscription.store'));

        // 有料プランリクエストの送信
        $response = $this->post(route('subscription.store'), $request_parameter);

        // リダイレクト
        $response->assertRedirect(route('subscription.edit'));
    }

    // ログイン済みの管理者は有料プランに登録できない
    public function test_admin_cannot_store_subscription_store()
    {
        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // paymentMethodIdパラメータ（支払い方法のID
        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];

        // 有料プラン登録ページにアクセス
        $response = $this->get(route('subscription.store'));

        // 有料プランリクエストの送信
        $response = $this->post(route('subscription.store'), $request_parameter);

        // リダイレクト
        $response->assertRedirect(route('admin.home'));
    }

    // editアクション（お支払い方法編集ページ）
    // 未ログインのユーザーはお支払い方法編集ページにアクセスできない
    public function test_guest_cannot_access_subscription_edit()
    {
        // 一般ユーザーアカウントの非承認
        $guset = User::factory()->create();
        $this->assertGuest();

        // お支払い方法編集ページにアクセス
        $response = $this->get(route('subscription.edit'));

        // リダイレクト
        $response->assertRedirect(route('login'));
    }

    // ログイン済みの無料会員はお支払い方法編集ページにアクセスできない
    public function test_free_user_cannot_access_subscription_edit()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // お支払い方法編集ページにアクセス
        $response = $this->get(route('subscription.edit'));

        // リダイレクト
        $response->assertRedirect(route('subscription.create'));
    }

    // ログイン済みの有料会員はお支払い方法編集ページにアクセスできる
    public function test_paid_user_can_access_subscription_edit()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 有料会員の承認
        $user->newSubscription(
            'premium_plan',
            'price_1PgMVLGeo7j2tfrTS0pOZqj8'
        )->create('pm_card_visa');

        // お支払い方法編集ページにアクセス
        $response = $this->get(route('subscription.edit'));

        // アクセス成功したか確認
        $response->assertStatus(200);
    }

    // ログイン済みの管理者はお支払い方法編集ページにアクセスできない
    public function test_admin_cannot_access_subscription_edit()
    {
        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // お支払い方法編集ページにアクセス
        $response = $this->get(route('subscription.edit'));

        // リダイレクト
        $response->assertRedirect(route('admin.home'));
    }

    // updateアクション（お支払い方法更新機能）
    // 未ログインのユーザーはお支払い方法を更新できない
    public function test_guest_cannot_update_subscription_update()
    {
        // 一般ユーザーアカウントの非承認
        $guset = User::factory()->create();
        $this->assertGuest();

        // visaからmastercardへ変更
        $request_parameter = [
            'paymentMethodId' => 'pm_card_mastercard'
        ];

        // お支払い方法更新ページにアクセス
        $response = $this->get(route('subscription.update'));

        // 有料プランリクエストの送信
        $response = $this->patch(route('subscription.update'), $request_parameter);

        // リダイレクト
        $response->assertRedirect(route('login'));
    }

    // ログイン済みの無料会員はお支払い方法を更新できない
    public function test_free_user_cannot_update_subscription_update()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // visaからmastercardへ変更
        $request_parameter = [
            'paymentMethodId' => 'pm_card_mastercard'
        ];

        // お支払い方法更新ページにアクセス
        $response = $this->get(route('subscription.update'));

        // 有料プランリクエストの送信
        $response = $this->patch(route('subscription.update'), $request_parameter);

        // リダイレクト
        $response->assertRedirect(route('subscription.create'));
    }

    // ログイン済みの有料会員はお支払い方法を更新できる
    public function test_paid_user_can_update_subscription_update()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 有料会員の承認
        $user->newSubscription(
            'premium_plan',
            'price_1PgMVLGeo7j2tfrTS0pOZqj8'
        )->create('pm_card_visa');

        // 元のデフォルトの支払い方法のID
        $default_payment_method_id  = $user->defaultPaymentMethod()->id;

        // visaからmastercardへ変更
        $request_parameter = [
            'paymentMethodId' => 'pm_card_mastercard'
        ];

        // お支払い方法更新ページにアクセス
        $response = $this->get(route('subscription.update'));

        // 有料プランリクエストの送信
        $response = $this->patch(route('subscription.update'), $request_parameter);

        // リダイレクト
        $response->assertRedirect(route('home'));

        // 変更後にUserインスタンスをリフレッシュ
        $user->refresh();

        // 更新できたことを検証
        $this->assertNotEquals($default_payment_method_id, $user->defaultPaymentMethod()->id);
    }

    // ログイン済みの管理者はお支払い方法を更新できない
    public function test_admin_cannot_update_subscription_update()
    {
        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // paymentMethodIdパラメータ（支払い方法のID
        $request_parameter = [
            'paymentMethodId' => 'pm_card_mastercard'
        ];

        // お支払い方法更新ページにアクセス
        $response = $this->get(route('subscription.update'));

        // 有料プランリクエストの送信
        $response = $this->post(route('subscription.update'), $request_parameter);

        // リダイレクト
        $response->assertRedirect(route('admin.home'));
    }

    // cancelアクション（有料プラン解約ページ）
    // 未ログインのユーザーは有料プラン解約ページにアクセスできない
    public function test_guest_cannot_access_subscription_cancel()
    {
        // 一般ユーザーアカウントの非承認
        $guset = User::factory()->create();
        $this->assertGuest();

        // 有料プラン解約ページにアクセス
        $response = $this->get(route('subscription.cancel'));

        // リダイレクト
        $response->assertRedirect(route('login'));
    }

    // ログイン済みの無料会員は有料プラン解約ページにアクセスできない
    public function test_free_user_cannot_access_subscription_cancel()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 有料プラン解約ページにアクセス
        $response = $this->get(route('subscription.cancel'));

        // リダイレクト
        $response->assertRedirect(route('subscription.create'));
    }

    // ログイン済みの有料会員は有料プラン解約ページにアクセスできる
    public function test_paid_user_can_access_subscription_cancel()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 有料会員の承認
        $user->newSubscription(
            'premium_plan',
            'price_1PgMVLGeo7j2tfrTS0pOZqj8'
        )->create('pm_card_visa');

        // 有料プラン解約ページにアクセス
        $response = $this->get(route('subscription.cancel'));

        // アクセス成功したか確認
        $response->assertStatus(200);
    }

    // ログイン済みの管理者は有料プラン解約ページにアクセスできない
    public function test_admin_cannot_access_subscription_cancel()
    {
        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // 有料プラン解約ページにアクセス
        $response = $this->get(route('subscription.cancel'));

        // リダイレクト
        $response->assertRedirect(route('admin.home'));
    }

    // destroyアクション（有料プラン解約機能）
    // 未ログインのユーザーは有料プランを解約できない
    public function test_guest_cannot_destroy_subscription_destroy()
    {
        // 一般ユーザーアカウントの非承認
        $guset = User::factory()->create();
        $this->assertGuest();

        // 解約実行
        $response = $this->delete(route('subscription.destroy'));

        // リダイレクト
        $response->assertRedirect(route('login'));
    }

    // ログイン済みの無料会員は有料プランを解約できない
    public function test_free_user_cannot_destroy_subscription_destroy()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 解約実行
        $response = $this->delete(route('subscription.destroy'));

        // リダイレクト
        $response->assertRedirect(route('subscription.create'));
    }

    // ログイン済みの有料会員は有料プランを解約できる
    public function test_paid_user_can_destroy_subscription_destroy()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 有料会員の承認
        $user->newSubscription(
            'premium_plan',
            'price_1PgMVLGeo7j2tfrTS0pOZqj8'
        )->create('pm_card_visa');

        // 解約実行
        $response = $this->delete(route('subscription.destroy'));

        // リダイレクト
        $response->assertRedirect(route('home'));

        // 解約後にUserインスタンスをリフレッシュ
        $user->refresh();

        // 解約できたことを検証
        $this->assertFalse($user->subscribed('premium_plan'));
    }

    // ログイン済みの管理者は有料プランを解約できない
    public function test_admin_cannot_destroy_subscription_destroy()
    {
        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // 解約実行
        $response = $this->delete(route('subscription.destroy'));

        // リダイレクト
        $response->assertRedirect(route('admin.home'));
    }
}
