<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\User;
use App\Models\Term;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TermTest extends TestCase
{
    use RefreshDatabase;

    // indexアクション（利用規約ページ）
    // 未ログインのユーザーは管理者側の利用規約ページにアクセスできない
    public function test_guest_cannot_access_admin_term_index()
    {
        // 一般ユーザーアカウントの未承認
        $this->assertGuest();

        // 利用規約を作成
        $term = Term::factory()->create();

        // 利用規約ページページにアクセス
        $response = $this->get(route('admin.terms.index'));

        // リダイレクト
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側の利用規約ページにアクセスできない
    public function test_user_cannot_access_admin_term_index()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 利用規約を作成
        $term = Term::factory()->create();

        // 利用規約ページページにアクセス
        $response = $this->get(route('admin.terms.index'));

        // リダイレクト
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は管理者側の利用規約ページにアクセスできる
    public function test_admin_can_access_admin_term_index()
    {
        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // 利用規約を作成
        $term = Term::factory()->create();

        // 利用規約ページページにアクセス
        $response = $this->get(route('admin.terms.index'));

        // アクセス成功したか確認
        $response->assertStatus(200);
    }

    // editアクション（利用規約編集ページ）
    // 未ログインのユーザーは管理者側の利用規約編集ページにアクセスできない
    public function test_guest_cannot_access_admin_term_edit()
    {
        // 一般ユーザーアカウントの未承認
        $this->assertGuest();

        // 利用規約を作成
        $term = Term::factory()->create();

        // 利用規約ページページにアクセス
        $response = $this->get(route('admin.terms.edit', $term));

        // リダイレクト
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側の利用規約編集ページにアクセスできない
    public function test_user_cannot_access_admin_term_edit()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 利用規約を作成
        $term = Term::factory()->create();

        // 利用規約ページページにアクセス
        $response = $this->get(route('admin.terms.edit', $term));

        // リダイレクト
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は管理者側の利用規約編集ページにアクセスできる
    public function test_admin_can_access_admin_term_edit()
    {
        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // 利用規約を作成
        $term = Term::factory()->create();

        // 利用規約ページページにアクセス
        $response = $this->get(route('admin.terms.edit', $term));

        // アクセス成功したか確認
        $response->assertStatus(200);
    }

    // updateアクション（利用規約更新機能）
    // 未ログインのユーザーは利用規約を更新できない
    public function test_guest_cannot_access_admin_term_update()
    {
        // 一般ユーザーアカウントの未承認
        $this->assertGuest();

        // 更新「前」の利用規約を作成
        $term_data_old = Term::factory()->create();

        // 更新「後」の利用規約を作成
        $term_data_new = [
            'content' => 'テストtest',
        ];

        // 利用規約の更新リクエストの送信
        $response = $this->patch(route('admin.terms.update', $term_data_old), $term_data_new);

        // 	terms テーブルに店舗が更新されていないことを確認
        $this->assertDatabaseMissing('terms', $term_data_new);

        // リダイレクト
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは利用規約を更新できない
    public function test_user_cannot_access_admin_term_update()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 更新「前」の利用規約を作成
        $term_data_old = Term::factory()->create();

        // 更新「後」の利用規約を作成
        $term_data_new = [
            'content' => 'テストtest',
        ];

        // 利用規約の更新リクエストの送信
        $response = $this->patch(route('admin.terms.update', $term_data_old), $term_data_new);

        // 	terms テーブルに店舗が更新されていないことを確認
        $this->assertDatabaseMissing('terms', $term_data_new);

        // リダイレクト
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は利用規約を更新できる
    public function test_admin_can_access_admin_term_update()
    {
        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // 更新「前」の利用規約を作成
        $term_data_old = Term::factory()->create();

        // 更新「後」の利用規約を作成
        $term_data_new = [
            'content' => 'テストtest',
        ];

        // 利用規約の更新リクエストの送信
        $response = $this->patch(route('admin.terms.update', $term_data_old), $term_data_new);

        // 	terms テーブルに店舗が更新されていることを確認
        $this->assertDatabaseHas('terms', $term_data_new);

        // リダイレクト
        $response->assertRedirect(route('admin.company.index'));
    }
}
