<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    // 未ログインのユーザーは管理者側のトップページにアクセスできない
    public function test_guest_cannot_access_admin_home(): void
    {
        // 一般ユーザーアカウントの非承認
        $guset = User::factory()->create();
        $this->assertGuest();

        // 管理者側のトップページにアクセス
        $response = $this->get(route('admin.home'));

        // リダイレクト
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側のトップページにアクセスでない
    public function test_user_cannot_access_admin_home(): void
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 管理者側のトップページにアクセス
        $response = $this->get(route('admin.home'));

        // リダイレクト
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は管理者側のトップページにアクセスできる
    public function test_admin_can_access_admin_home(): void
    {
        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // 管理者側のトップページにアクセス
        $response = $this->get(route('admin.home'));

        // アクセス成功したか確認
        $response->assertStatus(200);
    }
}
