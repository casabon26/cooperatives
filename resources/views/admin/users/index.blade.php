@extends('layouts.app')

@section('content')
<div class="py-4">
    <h1 class="h4">User Management</h1>
    <p><a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-success">Create User</a></p>
    <table class="table table-sm">
        <thead><tr><th>Name</th><th>Email</th><th>Role</th><th></th></tr></thead>
        <tbody>
        @foreach($users as $u)
            <tr>
                <td>{{ $u->name }}</td>
                <td>{{ $u->email }}</td>
                <td>
                    <form method="post" class="d-flex gap-2">
                        @csrf
                        <select name="role" class="form-select form-select-sm" style="width:200px">
                            <option value="public" {{ $u->role==='public' ? 'selected' : '' }}>Public User</option>
                            <option value="gov_admin" {{ $u->role==='gov_admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        <button formaction="{{ route('admin.users.updateRole',$u) }}" class="btn btn-sm btn-primary">Save</button>
                    </form>
                </td>
                <td>
                    <a href="{{ route('admin.users.certificates', $u) }}" class="btn btn-sm btn-outline-primary">View certificates</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div>{{ $users->links() }}</div>
</div>
@endsection
