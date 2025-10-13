@extends('baseB')
@section('content')

<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Store/</span> Add Store</h4>

        <div class="row">
            <div class="col-xxl">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Add a New Store</h5>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ isset($store) ? route('stores.update', $store->id) : route('AjouterMagasin') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @if(isset($store))
                                @method('PUT')
                            @endif

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="store_name">Store Name</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="bx bx-store"></i></span>
                                        <input type="text" class="form-control" id="store_name" name="store_name"
                                               placeholder="Enter store name"
                                               value="{{ old('store_name', $store->store_name ?? '') }}" />
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="owner_name">Owner Name</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="bx bx-user"></i></span>
                                        <input type="text" class="form-control" id="owner_name" name="owner_name"
                                               placeholder="Enter owner name"
                                               value="{{ old('owner_name', $store->owner_name ?? '') }}" />
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="location">Location</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="bx bx-map"></i></span>
                                        <input type="text" class="form-control" id="location" name="location"
                                               placeholder="Enter store location"
                                               value="{{ old('location', $store->location ?? '') }}" />
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="contact">Contact</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="bx bx-phone"></i></span>
                                        <input type="text" class="form-control" id="contact" name="contact"
                                               placeholder="Enter phone or email"
                                               value="{{ old('contact', $store->contact ?? '') }}" />
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="store_image" class="form-label">Store Image</label>
                                <input type="file" name="store_image" class="form-control">
                                @if(isset($store) && $store->store_image)
                                    <img src="{{ asset('storage/'.$store->store_image) }}" alt="Store Image" style="height:100px; margin-top:10px;">
                                @endif
                            </div>                            

                            <div class="row justify-content-end">
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-primary">
                                        {{ isset($store) ? 'Update Store' : 'Add Store' }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->
    <div class="content-backdrop fade"></div>
</div>

@endsection