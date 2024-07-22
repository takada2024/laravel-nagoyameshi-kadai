<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    // indexアクション（会社概要ページ）
    // 未ログインのユーザーは管理者側の会社概要ページにアクセスできない
    public function test_guest_cannot_access_admin_company_index()
    {
        // 一般ユーザーアカウントの未承認
        $this->assertGuest();

        // 会社概要を作成
        $company = Company::factory()->create();

        // 会社概要ページページにアクセス
        $response = $this->get(route('admin.company.index'));

        // リダイレクト
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側の会社概要ページにアクセスできない
    public function test_user_cannot_access_admin_company_index()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 会社概要を作成
        $company = Company::factory()->create();

        // 会社概要ページページにアクセス
        $response = $this->get(route('admin.company.index'));

        // リダイレクト
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は管理者側の会社概要ページにアクセスできる
    public function test_admin_can_access_admin_company_index()
    {
        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // 会社概要を作成
        $company = Company::factory()->create();

        // 会社概要ページページにアクセス
        $response = $this->get(route('admin.company.index', $company));

        // アクセス成功したか確認
        $response->assertStatus(200);
    }

    // editアクション（会社概要編集ページ）
    // 未ログインのユーザーは管理者側の会社概要編集ページにアクセスできない
    public function test_guest_cannot_access_admin_company_edit()
    {
        // 一般ユーザーアカウントの未承認
        $this->assertGuest();

        // 会社概要を作成
        $company = Company::factory()->create();

        // 会社概要編集ページページにアクセス
        $response = $this->get(route('admin.company.edit', $company));

        // リダイレクト
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側の会社概要編集ページにアクセスできない
    public function test_user_cannot_access_admin_company_edit()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 会社概要を作成
        $company = Company::factory()->create();

        // 会社概要編集ページページにアクセス
        $response = $this->get(route('admin.company.edit', $company));

        // リダイレクト
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は管理者側の会社概要編集ページにアクセスできる
    public function test_admin_can_access_admin_company_edit()
    {
        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // 会社概要を作成
        $company = Company::factory()->create();

        // 会社概要編集ページページにアクセス
        $response = $this->get(route('admin.company.edit', $company));

        // アクセス成功したか確認
        $response->assertStatus(200);
    }

    // updateアクション（会社概要更新機能）
    // 未ログインのユーザーは会社概要を更新できない
    public function test_guest_cannot_access_admin_company_update()
    {
        // 一般ユーザーアカウントの未承認
        $this->assertGuest();

        // 更新「前」の会社概要を作成
        $company_data_old = Company::factory()->create();

        // 更新「後」の会社概要を作成
        $company_data_new = [
            'name' => 'テストTest',
            'postal_code' => '7654321',
            'address' => 'テストTest',
            'representative' => 'テストTest',
            'establishment_date' => 'テストTest',
            'capital' => 'テストTest',
            'business' => 'テストTest',
            'number_of_employees' => 'テストTest',
        ];

        // 会社概要の更新リクエストの送信
        $response = $this->patch(route('admin.company.update', $company_data_old), $company_data_new);

        // 	companies テーブルに店舗が更新されていないことを確認
        $this->assertDatabaseMissing('companies', $company_data_new);

        // リダイレクト
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは会社概要を更新できない
    public function test_user_cannot_access_admin_company_update()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 更新「前」の会社概要を作成
        $company_data_old = Company::factory()->create();

        // 更新「後」の会社概要を作成
        $company_data_new = [
            'name' => 'テストTest',
            'postal_code' => '7654321',
            'address' => 'テストTest',
            'representative' => 'テストTest',
            'establishment_date' => 'テストTest',
            'capital' => 'テストTest',
            'business' => 'テストTest',
            'number_of_employees' => 'テストTest',
        ];

        // 会社概要の更新リクエストの送信
        $response = $this->patch(route('admin.company.update', $company_data_old), $company_data_new);

        // 	companies テーブルに店舗が更新されていないことを確認
        $this->assertDatabaseMissing('companies', $company_data_new);

        // リダイレクト
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は会社概要を更新できる
    public function test_admin_can_access_admin_company_update()
    {
        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // 更新「前」の会社概要を作成
        $company_data_old = Company::factory()->create();

        // 更新「後」の会社概要を作成
        $company_data_new = [
            'name' => 'テストTest',
            'postal_code' => '7654321',
            'address' => 'テストTest',
            'representative' => 'テストTest',
            'establishment_date' => 'テストTest',
            'capital' => 'テストTest',
            'business' => 'テストTest',
            'number_of_employees' => 'テストTest',
        ];

        // 会社概要の更新リクエストの送信
        $response = $this->patch(route('admin.company.update', $company_data_old), $company_data_new);

        // 	companies テーブルに店舗が更新されていることを確認
        $this->assertDatabaseHas('companies', $company_data_new);

        // リダイレクト
        $response->assertRedirect(route('admin.company.index'));
    }
}
