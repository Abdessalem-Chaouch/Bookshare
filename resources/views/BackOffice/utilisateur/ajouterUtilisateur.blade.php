@extends('baseB')
@section('content')

@php $isEdit = isset($user); @endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Accounts Managements /</span>
        {{ $isEdit ? 'Edit User' : 'Add User' }}
    </h4>

    <div class="row">
        <div class="col-xxl">
            <div class="card mb-4">
                <div class="card-body">
                    <form action="{{ $isEdit ? route('users.update', $user) : route('AjouterUtilisateur.add') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if($isEdit)
                            @method('PUT')
                        @endif

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Name</label>
                            <div class="col-sm-10">
                                <input type="text" name="name" class="form-control" placeholder="John Doe" value="{{ $user->name ?? old('name') }}" />
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <input type="email" name="email" class="form-control" placeholder="john@example.com" value="{{ $user->email ?? old('email') }}" />
                            </div>
                        </div>

                        @if(!$isEdit)
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Password</label>
                            <div class="col-sm-10">
                                <input type="password" name="password" class="form-control" placeholder="Password" />
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Confirm Password</label>
                            <div class="col-sm-10">
                                <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password" />
                            </div>
                        </div>
                        @endif

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Role</label>
                            <div class="col-sm-10">
                                <select name="role" class="form-control">
                                    <option value="user" {{ ($user->role ?? '') == 'user' ? 'selected' : '' }}>User</option>
                                    <option value="admin" {{ ($user->role ?? '') == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="auteur" {{ ($user->role ?? '') == 'auteur' ? 'selected' : '' }}>Author</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Profile Photo</label>
                            <div class="col-sm-10">
                                <input type="file" name="photo_profil" class="form-control" />
                                @if($isEdit && $user->photo_profil)
                                    <img src="{{ asset('storage/'.$user->photo_profil) }}" alt="Profile Photo" class="rounded-circle mt-2" width="50" height="50">
                                @endif
                            </div>
                        </div>

                        <div class="row justify-content-end">
                            <div class="col-sm-10">
                                <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update User' : 'Add User' }}</button>
                            </div>
                        </div>
                    </form>

                    @if($errors->any())
                        <div class="alert alert-danger mt-3">
                            <ul>
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success mt-3">
                            {{ session('success') }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

@endsection
