@extends('baseB')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Accounts Managements /</span> Users List
    </h4>
 @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    {{-- Search & Filter --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('listeUtilisateur') }}" class="search-form" id="searchForm">
                <div class="row align-items-end">
                    <div class="col-md-6">
                        <div class="search-input-wrapper position-relative">
                            <i class="bx bx-search position-absolute" style="left:15px; top:50%; transform:translateY(-50%); color:#999;"></i>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Search by name or email..." 
                                   class="form-control ps-5" id="searchInput">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="role" class="form-select" id="roleFilter">
                            <option value="all" {{ request('role') == 'all' ? 'selected' : '' }}>All Roles</option>
                            <option value="auteur" {{ request('role') == 'auteur' ? 'selected' : '' }}>Auteur</option>
                            <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="sort" class="form-select" id="sortFilter">
                            <option value="asc" {{ request('sort','asc')=='asc'?'selected':'' }}>A-Z</option>
                            <option value="desc" {{ request('sort')=='desc'?'selected':'' }}>Z-A</option>
                        </select>
                    </div>
                </div>

                @if(request()->hasAny(['search', 'role', 'sort']))
                    <div class="mt-3 text-center">
                        <span class="badge bg-primary">{{ $users->count() }} result(s)</span>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <h5 class="card-header">Table Basic</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Profile Photo</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach($users as $user)
                        <tr>
                            <td>
                                @if($user->photo_profil)
                                    <img src="{{ asset('storage/'.$user->photo_profil) }}" alt="Profile Photo" class="rounded-circle" width="50" height="50">
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @php
                                    $roleClass = match($user->role) {
                                        'auteur' => 'bg-label-success',
                                        'user' => 'bg-label-primary',
                                        default => 'bg-label-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $roleClass }}">{{ ucfirst($user->role) }}</span>
                            </td>
                            <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('users.edit', $user->id) }}">
                                            <i class="bx bx-edit-alt me-2"></i> Edit
                                        </a>
                                        <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $user->id }}">
                                            <i class="bx bx-trash me-2"></i> Delete
                                        </a>
                                    </div>
                                </div>

                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Confirm Deletion</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete user <strong>{{ $user->name }}</strong>?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('users.delete', $user) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Yes, Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3 d-flex justify-content-center">
    {{ $users->links('pagination::bootstrap-5') }}
</div>

    </div>
</div>

@endsection

@section('scripts')
<script>
let searchTimeout;
const searchInput = document.getElementById('searchInput');
const roleFilter = document.getElementById('roleFilter');
const sortFilter = document.getElementById('sortFilter');

// Real-time search, role & sort
[searchInput, roleFilter, sortFilter].forEach(el => {
    el.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            document.getElementById('searchForm').submit();
        }, 300); // 300ms delay for smoother typing
    });
});
</script>
@endsection
