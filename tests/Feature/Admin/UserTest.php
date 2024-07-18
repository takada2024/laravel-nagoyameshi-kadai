<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    // 未ログインのユーザーは管理者側の会員一覧ページにアクセスできない
    public function test_guest_cannot_access_admin_users_index()
    {
        // 一般ユーザーアカウントの未承認
        $user = User::factory()->create();
        $this->assertGuest();

        // 管理側の会員一覧ページにアクセス
        $response = $this->get(route('admin.users.index'));

        // 失敗したのでログイン画面へリダイレクト
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側の会員一覧ページにアクセスできない
    public function test_user_cannot_access_admin_users_index()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 管理者側の会員一覧ページにアクセス
        $response = $this->get(route('admin.users.index'));

        // 失敗してリダイレクトされることを確認
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は管理者側の会員一覧ページにアクセスできる
    public function test_admin_can_access_admin_users_index()
    {
        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // 管理者用の会員一覧ページにアクセス
        $response = $this->get(route('admin.users.index'));

        // 成功したか確認
        $response->assertStatus(200);
    }

    // 未ログインのユーザーは管理者側の会員詳細ページにアクセスできない
    public function test_guest_cannot_access_admin_users_show()
    {
        // 一般ユーザーアカウントの未承認
        $user = User::factory()->create();
        $this->assertGuest();

        // 管理側の会員詳細ページにアクセス
        $response = $this->get(route('admin.users.show', $user));

        // 失敗してリダイレクトされることを確認
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側の会員詳細ページにアクセスできない
    public function test_user_cannot_access_admin_users_show()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 管理側の会員詳細ページにアクセス
        $response = $this->get(route('admin.users.show', $user));

        // 失敗してリダイレクトされることを確認
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は管理者側の会員詳細ページにアクセスできる
    public function test_admin_can_access_admin_users_show()
    {
        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 管理者用の会員一覧ページにアクセス
        $response = $this->get(route('admin.users.show', $user));

        // 成功したか確認
        $response->assertStatus(200);
    }
}
