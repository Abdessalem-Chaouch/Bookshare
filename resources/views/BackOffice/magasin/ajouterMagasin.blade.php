@extends('baseB')
@section('content')

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Store /</span>
            {{ isset($store) ? 'Edit Store' : 'Add Store' }}
        </h4>

        <div class="row">
            <div class="col-xxl">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">{{ isset($store) ? 'Edit Store' : 'Add a New Store' }}</h5>
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

                        {{-- <form action="{{ isset($store) ? route('stores.update', $store->id) : route('AjouterMagasin') }}" method="POST" enctype="multipart/form-data"> --}}
                        <form action="{{ isset($store) ? route('stores.update', $store->id) : route('stores.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @if(isset($store))
                                @method('PUT')
                            @endif

                            {{-- Store Info --}}
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="store_name">Store Name</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="store_name" name="store_name"
                                           placeholder="Enter store name"
                                           value="{{ old('store_name', $store->store_name ?? '') }}" />
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="owner_name">Owner Name</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="owner_name" name="owner_name"
                                           placeholder="Enter owner name"
                                           value="{{ old('owner_name', $store->owner_name ?? '') }}" />
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="location">Location</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="location" name="location"
                                           placeholder="Enter store location"
                                           value="{{ old('location', $store->location ?? '') }}" />
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="contact">Contact</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="contact" name="contact"
                                           placeholder="Enter phone or email"
                                           value="{{ old('contact', $store->contact ?? '') }}" />
                                </div>
                            </div>

                            {{-- Store Image --}}
                            <div class="mb-3">
                                <label for="store_image" class="form-label">Store Image</label>
                                <input type="file" name="store_image" class="form-control">
                                @if(isset($store) && $store->store_image)
                                    <img src="{{ asset('storage/'.$store->store_image) }}" alt="Store Image" style="height:100px; margin-top:10px;">
                                @endif
                            </div>

                            {{--AI Keywords --}}
                            {{-- <div class="form-group mt-3">
                                <label for="ai_keywords">Store Keywords (for AI)</label>
                                <input type="text" name="ai_keywords" id="ai_keywords" class="form-control" placeholder="e.g. We sell rare manga books">
                            </div>

                            <div class="form-group mt-3">
                                <button type="button" id="generateDescription" class="btn btn-primary">✨ Generate Description</button>
                            </div>

                            <div class="form-group mt-3">
                                <label for="description">AI Description</label>
                                <textarea name="description" id="description" rows="4" class="form-control" placeholder="AI will generate here..."></textarea>
                            </div> --}}


                            {{-- Books Section --}}
                            {{-- <h5 class="mt-4">Books in Store</h5>
                            <div id="books-wrapper">
                                @php
                                    $booksInStore = isset($store) ? $store->livres : collect([]);
                                @endphp

                                @if($booksInStore->count() > 0)
                                    @foreach($booksInStore as $index => $book)
                                        <div class="row mb-2 book-row">
                                            <div class="col-md-7">
                                                <select name="books[{{ $index }}][id]" class="form-control">
                                                    <option value="">-- Select Book --</option>
                                                    @foreach($allBooks as $b)
                                                        <option value="{{ $b->id }}" {{ $book->id == $b->id ? 'selected' : '' }}>
                                                            {{ $b->title }} — {{ $b->author }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="number" name="books[{{ $index }}][quantity]" class="form-control" min="0"
                                                    value="{{ $book->pivot->quantity ?? 0 }}">
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-danger btn-sm remove-book">Remove</button>
                                            </div>
                                        </div>
                                    @endforeach
                                @else --}}
                                    {{-- Default empty row for Add --}}
                                    {{-- <div class="row mb-2 book-row">
                                        <div class="col-md-7">
                                            <select name="books[0][id]" class="form-control">
                                                <option value="">-- Select Book --</option>
                                                @foreach($allBooks as $b)
                                                    <option value="{{ $b->id }}">{{ $b->title }} — {{ $b->author }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="number" name="books[0][quantity]" class="form-control" placeholder="Quantity" min="0">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger btn-sm remove-book">Remove</button>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <button type="button" id="add-book" class="btn btn-secondary btn-sm mt-2">Add Another Book</button> --}}

                            {{-- Submit --}}
                            <div class="row justify-content-end mt-4">
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
    <div class="content-backdrop fade"></div>
</div>

{{-- JS for dynamic books --}}
<script>
let bookIndex = {{ isset($store) ? $store->livres->count() : 1 }};
const allBooksOptions = `@foreach($allBooks as $book)
<option value="{{ $book->id }}">{{ $book->title }} — {{ $book->author }}</option>
@endforeach`;

document.getElementById('add-book').addEventListener('click', function() {
    const wrapper = document.getElementById('books-wrapper');
    const newRow = document.createElement('div');
    newRow.classList.add('row', 'mb-2', 'book-row');
    newRow.innerHTML = `
        <div class="col-md-7">
            <select name="books[${bookIndex}][id]" class="form-control">
                <option value="">-- Select Book --</option>
                ${allBooksOptions}
            </select>
        </div>
        <div class="col-md-3">
            <input type="number" name="books[${bookIndex}][quantity]" class="form-control" placeholder="Quantity" min="0">
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger btn-sm remove-book">Remove</button>
        </div>
    `;
    wrapper.appendChild(newRow);
    bookIndex++;
});

document.addEventListener('click', function(e) {
    if(e.target && e.target.classList.contains('remove-book')) {
        e.target.closest('.book-row').remove();
    }
});


// AI KEYWORDS GENERATOR 

document.getElementById('generateDescription').addEventListener('click', async () => {
    const keywords = document.getElementById('ai_keywords').value;

    if (!keywords) {
        alert("Please enter some keywords first!");
        return;
    }

    const response = await fetch('/generate-description', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ keywords })
    });

    const data = await response.json();
    document.getElementById('description').value = data.description || "AI failed to generate text 😅";
});

</script>

@endsection
