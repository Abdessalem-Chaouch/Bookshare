@extends('baseB')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Books /</span> My Books</h4>

    <div class="card">
        <h5 class="card-header">My Books</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Cover</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Availability</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($livres as $livre)
                    <tr>
                        <!-- Cover -->
                        <td>
                            @if($livre->photo_couverture)
                                <img src="{{ asset('storage/'.$livre->photo_couverture) }}"
                                     alt="{{ $livre->titre }}"
                                     class="rounded"
                                     width="60">
                            @else
                                <span class="text-muted">None</span>
                            @endif
                        </td>

                        <!-- Info -->
                        <td>{{ $livre->titre }}</td>
                        <td>{{ $livre->categorie?->name ?? '—' }}</td>

                        <!-- Price -->
                        <td>
                            {{ $livre->prix ? number_format($livre->prix, 2, ',', ' ') . ' DT' : '—' }}
                        </td>

                        <!-- Availability -->
                        <td>
                            <span class="badge
                                {{ $livre->disponibilite == 'disponible' ? 'bg-label-success' : '' }}
                                {{ $livre->disponibilite == 'emprunte' ? 'bg-label-warning' : '' }}
                                {{ $livre->disponibilite == 'reserve' ? 'bg-label-danger' : '' }}">
                                {{ ucfirst($livre->disponibilite) }}
                            </span>
                        </td>

                        <td>{{ $livre->stock }}</td>
   <td>
            @php $avg = $livre->averageRating(); @endphp
            @if($avg)
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= round($avg))
                        <span style="color: gold;">★</span>
                    @else
                        <span style="color: #ccc;">★</span>
                    @endif
                @endfor
                <small>({{ number_format($avg,1) }})</small>
            @else
                <span class="text-muted">No rating</span>
            @endif
        </td>
                        <!-- Actions -->
                        <td>
                            <div class="d-flex">
                                <!-- Details -->
                                <a href="{{ route('livres.show', $livre->id) }}" class="btn btn-sm btn-icon me-1" title="Details">
                                    <i class="bx bx-show"></i>
                                </a>

                                <!-- Edit -->
                                <a href="{{ route('livres.edit', $livre->id) }}" class="btn btn-sm btn-icon me-1" title="Edit">
                                    <i class="bx bx-edit-alt"></i>
                                </a>

                                <!-- Delete -->
                                <form action="{{ route('livres.destroy', $livre->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-icon me-1" title="Delete">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </form>

                                <!-- Download PDF -->
                                @if($livre->pdf_path)
                                    <a href="{{ route('livres.download', $livre->id) }}" class="btn btn-sm btn-icon" title="Download PDF">
                                        <i class="bx bx-download"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">You haven't added any books yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
