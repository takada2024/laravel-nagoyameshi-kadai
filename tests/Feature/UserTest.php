<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    // indexアクション（会員情報ページ）
    // 未ログインのユーザーは会員側の会員情報ページにアクセスできない
    public function test_guest_cannot_access_user_index()
    {
        // 一般ユーザーアカウントの未承認
        $guest = User::factory()->create();
        $this->assertGuest();

        // 会員側の会員情報ページにアクセス
        $response = $this->get(route('user.index'));

        // リダイレクト
        $response->assertRedirect(route('login'));
    }

    // ログイン済みの一般ユーザーは会員側の会員情報ページにアクセスできる
    public function test_user_can_access_user_index()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 会員側の会員情報ページにアクセス
        $response = $this->get(route('user.index', $user));

        // アクセス成功したか確認
        $response->assertStatus(200);
    }

    // ログイン済みの管理者は会員側の会員情報ページにアクセスできない
    public function test_admin_cannot_access_user_index()
    {
        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // 会員側の会員情報ページにアクセス
        $response = $this->get(route('user.index'));

        // リダイレクト
        $response->assertRedirect(route('admin.home'));
    }

    // editアクション（会員情報編集ページ）
    // 未ログインのユーザーは会員側の会員情報編集ページにアクセスできない
    public function test_guest_cannot_access_user_edit()
    {
        // 一般ユーザーアカウントの未承認
        $guest = User::factory()->create();
        $this->assertGuest();

        // 会員側の会員情報編集ページにアクセス
        $response = $this->get(route('user.edit', $guest->id));

        // リダイレクト
        $response->assertRedirect(route('login'));
    }

    // ログイン済みの一般ユーザーは会員側の他人の会員情報編集ページにアクセスできない
    public function test_user_cannot_access_other_user_edit()
    {
        // 一般ユーザーアカウントの承認（正規用）
        $user_my = User::factory()->create();
        $this->actingAs($user_my);

        // 一般ユーザーアカウントの承認（不正用）
        $user_other = User::factory()->create();

        // 会員側の会員情報編集ページにアクセス
        $response = $this->get(route('user.edit', $user_other));

        // リダイレクト
        $response->assertRedirect(route('user.index'));
    }

    // ログイン済みの一般ユーザーは会員側の自身の会員情報編集ページにアクセスできる
    public function test_user_can_access_user_edit()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 会員側の会員情報編集ページにアクセス
        $response = $this->get(route('user.edit', $user));

        // アクセス成功したか確認
        $response->assertStatus(200);
    }

    // ログイン済みの管理者は会員側の会員情報編集ページにアクセスできない
    public function test_admin_cannot_access_user_edit()
    {
        // 一般ユーザーアカウントの未承認
        $guest = User::factory()->create();
        $this->assertGuest();

        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // 会員側の会員情報編集ページにアクセス
        $response = $this->get(route('user.edit', $guest->id));

        // リダイレクト
        $response->assertRedirect(route('admin.home'));
    }

    // updateアクション（会員情報更新機能）
    // 未ログインのユーザーは会員情報を更新できない
    public function test_guest_cannot_access_user_update()
    {
        // 更新「前」の一般ユーザーアカウント（未承認）
        $guest_data_old = User::factory()->create();
        $this->assertGuest();

        // 更新「後」の般ユーザーアカウント
        $user_data_new = [
            'name' => '更新update',
            'kana' => 'コウシン',
            'postal_code' => '4568520',
            'address' => '東京都大阪市愛知1-1',
            'phone_number' => '0120112222',
            'birthday' => '20000102',
            'occupation' => '魔法使い'
        ];

        // 店舗更新リクエストの送信
        $response = $this->patch(route('user.update', $guest_data_old), $user_data_new);

        // リダイレクト
        $response->assertRedirect(route('login'));
    }

    // ログイン済みの一般ユーザーは他人の会員情報を更新できない
    public function test_user_cannot_access_other_user_update()
    {
        // 一般ユーザーアカウントの承認（正規用）
        $user_data_my = User::factory()->create();
        $this->actingAs($user_data_my);

        // 一般ユーザーアカウントの承認（不正用）
        $user_data_other_old = User::factory()->create();

        // 更新「後」の般ユーザーアカウント
        $user_data_other_new = [
            'name' => '更新update',
            'kana' => 'コウシン',
            'postal_code' => '4568520',
            'address' => '東京都大阪市愛知1-1',
            'phone_number' => '0120112222',
            'birthday' => '20000102',
            'occupation' => '魔法使い'
        ];

        // 店舗更新リクエストの送信
        $response = $this->patch(route('user.update', $user_data_other_old), $user_data_other_new);

        // リダイレクト
        $response->assertRedirect(route('user.index'));
    }

    // ログイン済みの一般ユーザーは自身の会員情報を更新できる
    public function test_user_can_access_user_update()
    {
        // 一般ユーザーアカウントの承認（
        $user_data_old = User::factory()->create();
        $this->actingAs($user_data_old);

        // 更新「後」の般ユーザーアカウント
        $user_data_new = [
            'name' => '更新update',
            'kana' => 'コウシン',
            'email' => 'kousin@kousin.com',
            'postal_code' => '4568520',
            'address' => '東京都大阪市愛知1-1',
            'phone_number' => '0120112222',
            'birthday' => '20000102',
            'occupation' => '魔法使い'
        ];

        // 店舗更新リクエストの送信
        $response = $this->patch(route('user.update', $user_data_old), $user_data_new);

        // リダイレクト
        $response->assertRedirect(route('user.index'));
    }

    // ログイン済みの管理者は会員情報を更新できない
    public function test_admin_cannot_access_user_update()
    {
        // 一般ユーザーアカウントの非承認
        $guset_data = User::factory()->create();
        $this->assertGuest();

        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // 更新「後」の般ユーザーアカウント
        $user_data = [
            'name' => '更新update',
            'kana' => 'コウシン',
            'postal_code' => '4568520',
            'address' => '東京都大阪市愛知1-1',
            'phone_number' => '0120112222',
            'birthday' => '20000102',
            'occupation' => '魔法使い'
        ];

        // 店舗更新リクエストの送信
        $response = $this->patch(route('user.update', $guset_data), $user_data);

        // リダイレクト
        $response->assertRedirect(route('admin.home'));
    }
}
