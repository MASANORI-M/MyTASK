<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;
use App\Models\Tag;
use App\Models\JobTag;
use DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        $tags = Tag::where('user_id', "=", \Auth::id())
            ->whereNull('deleted_at')
            ->orderBy('id', 'DESC')
            ->get();

        return view('create', compact('tags'));
    }

    public function store(Request $request)
    {
        $posts = $request->all();
        $request->validate(['title' => 'required', 'content' => 'required']);

        // トランザクション開始
        DB::transaction(function() use($posts) {
        // ジョブIDをインサートし取得
            $job_id = Job::insertGetId(['title' => $posts['title'], 'content' => $posts['content'], 'user_id' => \Auth::id()]);
            $tag_exists = Tag::where('user_id', '=', \Auth::id())->where('name', '=', $posts['new_tag'])->exists();
            // 新規タグが入力されているか　＆　新規タグが既にtagsテーブルに存在するかチェック
            if(!empty($posts['new_tag']) && !$tag_exists) {
                // 新規タグが無ければ、tagsテーブルにインサートしIDを取得
                $tag_id = Tag::insertGetId(['user_id' => \Auth::id(), 'name' => $posts['new_tag']]);
                // job_tagsテーブルにインサートし、ジョブとタグのIDを紐づけ
                JobTag::insert(['job_id' => $job_id, 'tag_id' => $tag_id]);
            }

            if(!empty($posts['tags'][0])) {
                foreach ($posts['tags'] as $tag) {
                        JobTag::insert(['job_id' => $job_id, 'tag_id' => $tag]);
                }
            }
        });

        
        return redirect(route('home'));
    }

    public function edit($id)
    {

        $edit_job = Job::select('jobs.*', 'tags.id AS tag_id')
            ->leftJoin('job_tags', 'job_tags.job_id', '=', 'jobs.id')
            ->leftJoin('tags', 'job_tags.tag_id', '=', 'tags.id')
            ->where('jobs.user_id', '=', \Auth::id())
            ->where('jobs.id', '=', $id)
            ->whereNull('jobs.deleted_at')
            ->get();
        
        $include_tags = [];
        foreach ($edit_job as $job) {
            array_push($include_tags, $job['tag_id']);
        }

        return view('edit', compact('edit_job', 'include_tags'));
    }

    public function update(Request $request)
    {
        $posts = $request->all();
        $request->validate(['title' => 'required', 'content' => 'required']);

        DB::transaction(function() use($posts) {
            Job::where('id', $posts['job_id'])->update(['title' => $posts['title'], 'content' => $posts['content']]);
            // 一旦ジョブとタグの紐づけを解除
            JobTag::where('job_id', '=', $posts['job_id'])->delete();
            // 再度ジョブとタグの紐づけ
            foreach($posts['tags'] as $tag) {
                JobTag::insert(['job_id' => $posts['job_id'], 'tag_id' => $tag]);
            }

            $tag_exists = Tag::where('user_id', '=', \Auth::id())->where('name', '=', $posts['new_tag'])->exists();
            // 新規タグが入力されているか　＆　新規タグが既にtagsテーブルに存在するかチェック
            if(!empty($posts['new_tag']) && !$tag_exists) {
                // 新規タグが無ければ、tagsテーブルにインサートしIDを取得
                $tag_id = Tag::insertGetId(['user_id' => \Auth::id(), 'name' => $posts['new_tag']]);
                // job_tagsテーブルにインサートし、ジョブとタグのIDを紐づけ
                JobTag::insert(['job_id' => $posts['job_id'], 'tag_id' => $tag_id]);
            }
        });


        return redirect(route('home'));
    }

    public function destroy(Request $request)
    {
        $posts = $request->all();

        Job::where('id', $posts['job_id'])
            ->update(['deleted_at' => date("Y-m-d H:i:s", time())]);
        
        return redirect(route('home'));
    }
}
