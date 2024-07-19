<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    // ~~indexアクション（カテゴリ一覧ページ）~~
    // 未ログインのユーザーは管理者側のカテゴリ一覧ページにアクセスできない
    public function test_guest_cannot_access_admin_categories_index()
    {
        // 一般ユーザーアカウントの未承認
        $this->assertGuest();

        // カテゴリ一覧ページにアクセス
        $response = $this->get(route('admin.categories.index'));

        // リダイレクト
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側のカテゴリ一覧ページにアクセスできない
    public function test_user_cannot_access_admin_categories_index()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // カテゴリ一覧ページにアクセス
        $response = $this->get(route('admin.categories.index'));

        // リダイレクト
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は管理者側のカテゴリ一覧ページにアクセスできる
    public function test_admin_can_access_admin_categories_index()
    {
        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // カテゴリ一覧ページにアクセス
        $response = $this->get(route('admin.categories.index'));

        // アクセス成功したか確認
        $response->assertStatus(200);
    }

    // ~~storeアクション（カテゴリ登録機能）~~
    // 未ログインのユーザーはカテゴリを登録できない
    public function test_guest_cannot_register_admin_categories_store()
    {
        // 一般ユーザーアカウントの未承認
        $this->assertGuest();

        // カテゴリデータを作成
        $category_data = Category::factory()->make()->toArray();

        // カテゴリ登録リクエストの送信
        $response = $this->post(route('admin.categories.store'), $category_data);

        // categories テーブルにカテゴリが登録されていないことを確認
        $this->assertDatabaseMissing('categories', $category_data);

        // リダイレクト
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーはカテゴリを登録できない
    public function test_user_cannot_register_admin_categories_store()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // カテゴリデータを作成
        $category_data = Category::factory()->make()->toArray();

        // カテゴリ登録リクエストの送信
        $response = $this->post(route('admin.categories.store'), $category_data);

        // categories テーブルにカテゴリが登録されていないことを確認
        $this->assertDatabaseMissing('categories', $category_data);

        // リダイレクト
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者はカテゴリを登録できる
    public function test_admin_can_register_admin_categories_store()
    {
        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // カテゴリデータを作成
        $category_data = Category::factory()->make()->toArray();

        // カテゴリ登録リクエストの送信
        $response = $this->post(route('admin.categories.store'), $category_data);

        // categories テーブルにカテゴリが登録されてることを確認
        $this->assertDatabaseHas('categories', $category_data);

        // リダイレクト
        $response->assertRedirect(route('admin.categories.index'));
    }

    // ~~updateアクション（カテゴリ更新機能）~~
    // 未ログインのユーザーはカテゴリを更新できない
    public function test_guest_cannot_update_admin_categories_update()
    {
        // 一般ユーザーアカウントの未承認
        $this->assertGuest();

        // 更新「前」のカテゴリデータ作成
        $category_data_old = Category::factory()->create();

        // 更新「後」のカテゴリデータ作成
        $category_data_new = [
            'name' => 'テストTestてすと',
        ];

        // カテゴリ更新リクエストの送信
        $response = $this->patch(route('admin.categories.update', $category_data_old), $category_data_new);

        // categories テーブルにカテゴリが更新されていないことを確認
        $this->assertDatabaseMissing('categories', $category_data_new);

        // リダイレクト
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーはカテゴリを更新できない
    public function test_user_cannot_update_admin_categories_update()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // 更新「前」のカテゴリデータ作成
        $category_data_old = Category::factory()->create();

        // 更新「後」のカテゴリデータ作成
        $category_data_new = [
            'name' => 'テストTestてすと',
        ];

        // カテゴリ更新リクエストの送信
        $response = $this->patch(route('admin.categories.update', $category_data_old), $category_data_new);

        // categories テーブルにカテゴリが更新されていないことを確認
        $this->assertDatabaseMissing('categories', $category_data_new);

        // リダイレクト
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者はカテゴリを更新できる
    public function test_admin_can_update_admin_categories_update()
    {
        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // 更新「前」のカテゴリデータ作成
        $category_data_old = Category::factory()->create();

        // 更新「後」のカテゴリデータ作成
        $category_data_new = [
            'name' => 'テストTestてすと',
        ];

        // カテゴリ更新リクエストの送信
        $response = $this->patch(route('admin.categories.update', $category_data_old), $category_data_new);

        // categories テーブルにカテゴリが更新されていることを確認
        $this->assertDatabaseHas('categories', $category_data_new);

        // リダイレクト
        $response->assertRedirect(route('admin.categories.index'));
    }

    // destroyアクション（カテゴリ削除機能）
    // 未ログインのユーザーはカテゴリを削除できない
    public function test_guest_cannot_delite_admin_categories_destroy()
    {
        // 一般ユーザーアカウントの未承認
        $this->assertGuest();

        // カテゴリデータを作成
        $category_data = Category::factory()->create();

        // カテゴリの削除
        $response = $this->delete(route('admin.categories.destroy', $category_data));

        // categories テーブルにカテゴリが削除されていないことを確認
        $this->assertDatabaseHas('categories', ['id' => $category_data->id]);

        // リダイレクト
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーはカテゴリを削除できない
    public function test_user_cannot_access_admin_categories_destroy()
    {
        // 一般ユーザーアカウントの承認
        $user = User::factory()->create();
        $this->actingAs($user);

        // カテゴリデータを作成
        $category_data = Category::factory()->create();

        // カテゴリの削除
        $response = $this->delete(route('admin.categories.destroy', $category_data));

        // categories テーブルにカテゴリが削除されていないことを確認
        $this->assertDatabaseHas('categories', ['id' => $category_data->id]);

        // リダイレクト
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者はカテゴリを削除できる
    public function test_admin_can_access_admin_categories_destroy()
    {
        // 管理アカウントの承認
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // カテゴリデータを作成
        $category_data = Category::factory()->create();

        // カテゴリの削除
        $response = $this->delete(route('admin.categories.destroy', $category_data));

        // categories テーブルにカテゴリが削除されていることを確認
        $this->assertDatabaseMissing('categories', ['id' => $category_data->id]);

        // リダイレクト
        $response->assertRedirect(route('admin.categories.index'));
    }
}
