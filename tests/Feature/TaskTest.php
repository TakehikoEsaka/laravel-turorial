<?php

namespace Tests\Feature;

use App\Http\Requests\CreateTask;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskTest extends TestCase
{
    // テストケースごとにデータベースをリフレッシュしてマイグレーションを再実行する
    use RefreshDatabase;

    /**
     * 各テストメソッドの実行前に呼ばれる
     */
    public function setUp(): void
    {
        parent::setUp();

        // テストケース実行前にフォルダデータを作成する
        $this->seed('FoldersTableSeeder');
    }

    /**
     * フォルダーが正しく作成される
     * @test
     */
    public function create_folder()
    {
        $response = $this->post('/folders/create', [
            'title' => 'samplefolder',
        ]);

        // リダイレクト先を確認（タスク一覧ページのURLが正しいかは要確認）
        // $response->assertRedirect('/folders/4/tasks');  // 例えばフォルダIDが1と仮定

        // データベースにフォルダが保存されていることを確認
        $this->assertDatabaseHas('folders', [
            'title' => 'samplefolder'
        ]);
    }

    /**
     * taskが正しく作成される
     * @test
     */
    public function create_task()
    {
        $response = $this->post(
            '/folders/1/tasks/create',
            [
                'title' => 'sampletask',
                'due_date' => '2024/06/01'
            ]
        );

        // データベースにTaskが保存されていることを確認
        $this->assertDatabaseHas('tasks', [
            'title' => 'sampletask'
        ]);
    }

    // testメソッドとして認識されるには以下コメント必要なので注意
    /**
     * 期限日が日付ではない場合はバリデーションエラー
     * @test
     */
    public function due_date_should_be_date()
    {
        $response = $this->post('/folders/1/tasks/create', [
            'title' => 'Sample task',
            'due_date' => 123, // 不正なデータ（数値）
        ]);

        // ここのエラーメッセージの文章はvalidation.phpで定義したメッセージ文言と違うとTestコード通らないので注意（スペースや句読点の違いも影響する）
        $response->assertSessionHasErrors([
            'due_date' => '期限日 には日付を入力してください',
        ]);
    }

    /**
     * 期限日が過去日付の場合はバリデーションエラー
     * @test
     */
    public function due_date_should_not_be_past()
    {
        $response = $this->post('/folders/1/tasks/create', [
            'title' => 'Sample task',
            'due_date' => Carbon::yesterday()->format('Y/m/d'), // 不正なデータ（昨日の日付）
        ]);

        $response->assertSessionHasErrors([
            'due_date' => '期限日 には今日以降の日付を入力してください。',
        ]);
    }
}
