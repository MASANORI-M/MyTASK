@extends('layouts.app')

@section('javascript')
<script src="/js/confirm.js"></script>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between text-white bg-primary">
        TASK編集
        <form id="delete-form" action="{{ route('destroy') }}" method="POST">
            @csrf
            <input type="hidden" name="job_id" value="{{ $edit_job[0]['id'] }}" />
            <i class="fas fa-trash-alt mr-3" onclick="deleteHandle(event)"></i>
        </form>
    </div>
    <form class="card-body my-card-body" action="{{ route('update') }}" method="POST">
        @csrf
        <input type="hidden" name="job_id" value="{{ $edit_job[0]['id'] }}">
        <div class="form-group">
            <textarea name="title" class="form-control mb-4" rows="1">{{ $edit_job[0]['title'] }}</textarea>
            <textarea name="content" class="form-control" rows="8">{{ $edit_job[0]['content'] }}</textarea>
        </div>
    @error('title')
        <div class="alert alert-danger">タイトルを挿入してください</div>
    @enderror
    @error('content')
        <div class="alert alert-danger">タスク内容を挿入してください</div>
    @enderror
    @foreach($tags as $t)
        <div class="form-check form-check-inline mb-3">
            <input class="form-check-input" type="checkbox" name="tags[]" id="{{ $t['id'] }}" value="{{ $t['id'] }}"
             {{ in_array($t['id'], $include_tags) ? 'checked' : '' }}>
            <label class="form-check-label" for="{{ $t['id'] }}">{{ $t['name'] }}</label>
        </div>
    @endforeach
        <input type="text" class="form-control w-50 mb-3" name="new_tag" placeholder="NEWタグ入力" />
        <button type="submit" class="btn btn-primary">タスク更新</button>
    </form>
</div>
@endsection
