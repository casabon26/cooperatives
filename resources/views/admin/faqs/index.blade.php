@extends('layouts.app')

@section('content')
<div class="py-4">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4>Manage FAQs</h4>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-3">
      <div class="card-body">
        <form method="post" action="/admin/manage-faqs">
          @csrf
          <div class="mb-2">
            <label class="form-label">Question</label>
            <input name="question" class="form-control" required>
          </div>
          <div class="mb-2">
            <label class="form-label">Answer</label>
            <textarea name="answer" class="form-control" rows="4" required></textarea>
          </div>
          <button class="btn btn-primary">Add FAQ</button>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="table-responsive">
        <table class="table mb-0">
          <thead>
            <tr><th>ID</th><th>Question</th><th>Answer</th><th></th></tr>
          </thead>
          <tbody>
            @forelse($items as $it)
              <tr>
                <td>{{ $it['id'] }}</td>
                <td style="max-width:40%;">{{ Str::limit($it['question'], 120) }}</td>
                <td style="max-width:50%;">{{ Str::limit($it['answer'] ?? '', 200) }}</td>
                <td class="text-end">
                  <a href="/admin/manage-faqs/{{ $it['id'] }}/edit" class="btn btn-sm btn-outline-primary">Edit</a>
                  <form method="post" action="/admin/manage-faqs/{{ $it['id'] }}/delete" style="display:inline">
                    @csrf
                    <button class="btn btn-sm btn-outline-danger" type="submit" data-confirm="Delete FAQ?">Delete</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr><td colspan="4"><div class="small text-muted p-3">No FAQs yet.</div></td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
