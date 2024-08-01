<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Term;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TermTest extends TestCase
{
    use RefreshDatabase;


    // 利用規約ページ
    // 未ログインのユーザーは会員側の利用規約ページにアクセスできる
    public function test_guest_can_access_terms_index()
    {
        // 一般ユーザーアカウントの未承認
        $guest = User::factory()->create();
        $this->assertGuest();

        // 利用規約の作成
        Term::factory()->create();

        // 会員側の店舗一覧ページにアクセス
        $response = $this->get(route('terms.index'));

        // アクセス成功したか確認
        $response->assertStatus(200);
    }

    // ログイン済みの一般ユーザーは会員側の利用規約ページにアクセスできる
    public function test_user_can_access_terms_index()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 利用規約の作成
        Term::factory()->create();

        // 会員側の店舗一覧ページにアクセス
        $response = $this->get(route('terms.index'));

        // アクセス成功したか確認
        $response->assertStatus(200);
    }

    // ログイン済みの管理者は会員側の利用規約ページにアクセスできない
    public function test_admin_cannot_access_terms_index()
    {
        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // 利用規約の作成
        Term::factory()->create();

        // 会員側の店舗一覧ページにアクセス
        $response = $this->get(route('terms.index'));

        // リダイレクト
        $response->assertRedirect(route('admin.home'));
    }
}
