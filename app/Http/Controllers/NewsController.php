<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::orderByDesc('published_at')->paginate(20);
        return view('admin.news.index', compact('news'));
    }

    public function store(Request $request)
    {
        $this->authorize('access-admin');
        $data = $request->validate([
            'title'=>'required|string|max:191',
            'content'=>'required|string',
            'published_at'=>'nullable|date',
        ]);
        $data['created_by'] = $request->user()->id;
        $news = News::create($data);
        try { AuditLog::create(['user_id'=>$request->user()->id,'action'=>'create_news','ip_address'=>$request->ip(),'meta'=>['news_id'=>$news->id]]); } catch (\Throwable $e) {}
        return back()->with('success','News created');
    }

    public function update(Request $request, News $news)
    {
        $this->authorize('access-admin');
        $data = $request->validate([
            'title'=>'required|string|max:191',
            'content'=>'required|string',
            'published_at'=>'nullable|date',
        ]);
        $news->update($data);
        try { AuditLog::create(['user_id'=>$request->user()->id,'action'=>'update_news','ip_address'=>$request->ip(),'meta'=>['news_id'=>$news->id]]); } catch (\Throwable $e) {}
        return back()->with('success','News updated');
    }

    public function destroy(Request $request, News $news)
    {
        $this->authorize('access-admin');
        $news->delete();
        try { AuditLog::create(['user_id'=>$request->user()->id,'action'=>'delete_news','ip_address'=>$request->ip(),'meta'=>['news_id'=>$news->id]]); } catch (\Throwable $e) {}
        return back()->with('success','News deleted');
    }
}
