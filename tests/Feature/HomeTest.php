<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    // ~~~indexアクション（店舗一覧ページ）~~~
    // 未ログインのユーザーは会員側のトップページにアクセスできる
    public function test_guest_can_access_home()
    {
        // 一般ユーザーアカウントの未承認
        $this->assertGuest();

        // 店舗一覧ページにアクセス
        $response = $this->get(route('home'));

        // アクセス成功したか確認
        $response->assertStatus(200);
    }

    // ログイン済みの一般ユーザーは会員側のトップページにアクセスできる
    public function test_user_can_access_home()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 店舗一覧ページにアクセス
        $response = $this->get(route('home'));

        // アクセス成功したか確認
        $response->assertStatus(200);
    }

    // ログイン済みの管理者は会員側のトップページにアクセスできない
    public function test_admin_cannot_access_home()
    {
        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // 店舗一覧ページにアクセス
        $response = $this->get(route('home'));

        // リダイレクト
        $response->assertRedirect(route('admin.home'));
    }
}
