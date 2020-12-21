<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Task;    // 追加

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
        // getでtasks/にアクセスされた場合の「一覧表示処理」
    public function index()
    {
        if (\Auth::check()) { // 認証済みの場合
            // MEMO: 認証済みユーザーのオブジェクトを$userに格納している
            $user = \Auth::user();
            // タスク一覧を取得
            // MEMO: $tasksに格納はされているのは、認証済みユーザーのリレーションを元に取得したTaskのレコード
            $tasks = $user->tasks()->orderBy('created_at', 'desc')->get();

            // タスク一覧ビューでそれを表示
            return view('tasks.index', [
                'tasks' => $tasks,
            ]);
        } else {
            // Welcomeビューでそれらを表示
            return view('welcome');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (\Auth::check()) { // 認証済みの場合
            $task = new Task;

            // タスク作成ビューを表示
            return view('tasks.create', [
                'task' => $task,
            ]);
        } else {
            // トップページへリダイレクトさせる
            return back();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    // バリデーション
        $request->validate([
            'status' => 'required|max:10',
            'content' => 'required|max:255',
        ]);
    

        if (\Auth::check()) { // 認証済みの場合
            // 認証済みユーザを取得
            $user = \Auth::user();
            // ユーザの投稿の一覧を作成日時の降順で取得
            $task = $user->tasks()->orderBy('created_at', 'desc');

            // タスクを作成
            $task = new task;
            $task->status = $request->status;
            $task->content = $request->content;
            $task->user_id = \Auth::id();
            $task->save();
        }

        // トップページへリダイレクトさせる
        return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $task = Task::findOrFail($id);
        
        if (\Auth::id() === $task->user_id) { // 認証済みの場合

            // タスク詳細ビューでそれを表示
            return view('tasks.show', [
                'task' => $task,
            ]);
        } else {
            // トップページへリダイレクトさせる
            return redirect('/');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $task = Task::findOrFail($id);
        
        if (\Auth::id() === $task->user_id) { // 認証済みの場合

            // タスク編集ビューでそれを表示
            return view('tasks.edit', [
                'task' => $task,
            ]);
        } else {
            // トップページへリダイレクトさせる
            return back();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // バリデーション
        $request->validate([
            'status' => 'required|max:10',
            'content' => 'required|max:255',
        ]);
        
        $task = Task::findOrFail($id);
        
        if (\Auth::id() === $task->user_id) { // 認証済みの場合
            // タスクを更新
            $task->status = $request->status;
            $task->content = $request->content;
            $task->save();
        }
            // トップページへリダイレクトさせる
        return redirect('/');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        $task = Task::findOrFail($id);
        
        if (\Auth::id() === $task->user_id) { // 認証済みの場合
            // タスクを削除
            $task->delete();
        }
        // トップページへリダイレクトさせる
        return redirect('/');
    }
}
