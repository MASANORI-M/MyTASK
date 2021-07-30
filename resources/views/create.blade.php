@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header text-white bg-primary">新規TASK作成</div>
    <form class="card-body my-card-body" action="{{ route('store') }}" method="POST">
        @csrf
        <div class="form-group">
            <textarea name="title" class="form-control mb-4" rows="1" placeholder="タイトル"></textarea>
            <textarea name="content" class="form-control" rows="8" placeholder="TASK入力"></textarea>
        </div>
    @error('title')
        <div class="alert alert-danger">タイトルを挿入してください</div>
    @enderror
    @error('content')
        <div class="alert alert-danger">タスク内容を挿入してください</div>
    @enderror
    @foreach($tags as $t)
        <div class="form-check form-check-inline mb-3">
            <input class="form-check-input" type="checkbox" name="tags[]" id="{{ $t['id'] }}" value="{{ $t['id'] }}">
            <label class="form-check-label" for="{{ $t['id'] }}">{{ $t['name'] }}</label>
        </div>
    @endforeach
        <input type="text" class="form-control w-50 mb-3" name="new_tag" placeholder="NEWタグ入力" />
        <button type="submit" class="btn btn-primary">タスクUP</button>
    </form>
</div>
@endsection
