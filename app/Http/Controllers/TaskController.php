<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTask;


use Illuminate\Http\Request;
use App\Models\Folder;
use App\Models\Task;
use Illuminate\Support\Facades\Log;



class TaskController extends Controller
{
    public function index(int $id)
    {
        // 全てのフォルダを取得する
        $folders = Folder::all();

        // 選ばれたフォルダから紐づくタスクを取得
        $current_folder = Folder::find($id);
        $tasks = Task::where("folder_id", "=", $current_folder->id)->get();
        // tinkerでApp\Models\Task::where("folder_id", "=", 1)->toSql()とするとSQL生成可能

        // hasManyをつかうとよりシンプルに描ける
        $tasks = $current_folder->tasks()->get();

        ##########################################
        // Debug方法を下にまとめる

        // dump表示
        // dump($current_folder);

        // dump die（dumpして停止する）
        // dd($current_folder);

        // Illuminate\Support\Facades\Logの利用
        $message = "current_folder id is : " . $current_folder->id;
        // Log::emergency($message);
        // Log::alert($message);
        // Log::critical($message);
        // Log::error($message);
        // Log::warning($message);
        // Log::notice($message);
        // Log::info($message);
        // Log::debug($message, ['id' => $id]);    // 関連情報を第二引数として渡せる

        // ログ用ヘルパーの利用
        logger($message); // debugレベル
        // info($message); // infoレベル
        ##########################################

        return view("tasks/index", [
            "folders" => $folders,
            "current_folder_id" => $id,
            "tasks" => $tasks
        ]);
    }

    /**
     * GET /folders/{id}/tasks/create
     */
    public function showCreateForm(int $id)
    {
        return view('tasks/create', [
            'folder_id' => $id
        ]);
    }


    public function create(int $id, CreateTask $request)
    {
        logger("create task began !! "); // debugレベル
        $current_folder = Folder::find($id);

        $task = new Task();
        $task->title = $request->title;
        $task->due_date = $request->due_date;

        $current_folder->tasks()->save($task);

        return redirect()->route('tasks.index', [
            'id' => $current_folder->id,
        ]);
    }
}
