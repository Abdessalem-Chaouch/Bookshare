@extends('baseB')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Accounts Managements /</span>Users List</h4>

    <!-- Basic Bootstrap Table -->
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
                'admin' => 'bg-label-danger',
                'author' => 'bg-label-success',
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

        <!-- Modal pour cet utilisateur -->
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
    </div>
</div>


@endsection