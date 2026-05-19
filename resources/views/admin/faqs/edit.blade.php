@extends('layouts.app')

@section('content')
<div class="py-4">
  <div class="container">
    <h4>Edit FAQ</h4>
    <div class="card">
      <div class="card-body">
        <form method="post" action="/admin/manage-faqs/{{ $item['id'] }}">
          @csrf
          <div class="mb-2">
            <label class="form-label">Question</label>
            <input name="question" class="form-control" value="{{ $item['question'] }}" required>
          </div>
          <div class="mb-2">
            <label class="form-label">Answer</label>
            <textarea name="answer" class="form-control" rows="6" required>{{ $item['answer'] }}</textarea>
          </div>
          <button class="btn btn-primary">Save</button>
          <a href="/admin/manage-faqs" class="btn btn-outline-secondary ms-2">Cancel</a>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
