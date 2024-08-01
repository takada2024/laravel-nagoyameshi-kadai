<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    // 会社概要ページ
    // 未ログインのユーザーは会員側の会社概要ページにアクセスできる
    public function test_guest_can_access_company_index()
    {
        // 一般ユーザーアカウントの未承認
        $guest = User::factory()->create();
        $this->assertGuest();

        // 会社概要の作成
        Company::factory()->create();

        // 会員側の店舗一覧ページにアクセス
        $response = $this->get(route('company.index'));

        // アクセス成功したか確認
        $response->assertStatus(200);
    }

    // ログイン済みの一般ユーザーは会員側の会社概要ページにアクセスできる
    public function test_user_can_access_company_index()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 会社概要の作成
        Company::factory()->create();

        // 会員側の店舗一覧ページにアクセス
        $response = $this->get(route('company.index'));

        // アクセス成功したか確認
        $response->assertStatus(200);
    }

    // ログイン済みの管理者は会員側の会社概要ページにアクセスできない
    public function test_admin_cannot_access_company_index()
    {
        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // 会社概要の作成
        Company::factory()->create();

        // 会員側の店舗一覧ページにアクセス
        $response = $this->get(route('company.index'));

        // リダイレクト
        $response->assertRedirect(route('admin.home'));
    }
}


// php artisan test tests/Feature/TermTest.php
