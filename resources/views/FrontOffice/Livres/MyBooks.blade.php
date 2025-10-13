@extends('baseF')

@section('content')
<section id="featured-books" class="py-5 my-5">
  <div class="container">
    <div class="section-header align-center mb-4">
      <div class="title"><span>Your reading history</span></div>
      <h2 class="section-title">My Books</h2>
    </div>

    @php
      $hasAny = count($recentBooks) || count($lastWeekBooks) || count($lastMonthBooks) || count($notReadYet);
    @endphp

    @if(!$hasAny)
      <p class="text-center">You have no books in your library.</p>
    @else
      <div class="text-end mb-3">
        <button id="switchViewBtn" class="btn btn-outline-accent2">Switch to Card View</button>
      </div>

      {{-- TABLE VIEW --}}
      <div id="tableView" style="display:block;">
        @foreach ([
            'Recently Read' => $recentBooks,
            'Last Week' => $lastWeekBooks,
            'Last Month' => $lastMonthBooks,
            'Not read yet' => $notReadYet
          ] as $sectionTitle => $sectionBooks)

          @if(count($sectionBooks))
            <h4 class="mt-4 mb-2 text-accent">{{ $sectionTitle }}</h4>
            <div class="table-responsive mb-4">
              <table class="table custom-table align-middle">
                <thead>
                  <tr>
                    <th>Cover</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Last Read</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($sectionBooks as $payment)
                    <tr>
                      <td style="width:160px;">
                        <img src="{{ asset('storage/' . $payment->livre->photo_couverture) }}" class="cover-img">
                      </td>
                      <td>{{ $payment->livre->titre }}</td>
                      <td>{{ $payment->livre->user->name ?? 'Unknown' }}</td>
                      <td>
                        @if($payment->livre->last_read)
                          {{ \Carbon\Carbon::parse($payment->livre->last_read)->diffForHumans() }}
                        @else
                          <span class="text-muted">You haven't read it yet</span>
                        @endif
                      </td>
                      <td>
                        <a href="{{ route('livres.reader', $payment->livre->id) }}" class="btn btn-outline-accent2 mb-1">View</a>
                        <a href="{{ route('livres.download', $payment->livre->id) }}" class="btn btn-outline-accent2">Download</a>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif

        @endforeach
      </div>

      {{-- CARD VIEW --}}
      <div id="cardView" class="d-none">
        @foreach ([
            'Recently Read' => $recentBooks,
            'Last Week' => $lastWeekBooks,
            'Last Month' => $lastMonthBooks,
            'Not read yet' => $notReadYet
          ] as $sectionTitle => $sectionBooks)

          @if(count($sectionBooks))
            <h4 class="w-100 mt-4 text-accent">{{ $sectionTitle }}</h4>
            <div class="d-flex flex-wrap gap-3 mb-3">
              @foreach($sectionBooks as $payment)
                <div class="book-card p-3" style="background: rgba(255,255,255,0.05); border-radius:10px; width: 200px;">
                  <img src="{{ asset('storage/' . $payment->livre->photo_couverture) }}" class="book-cover w-100 mb-2">
                  <h5 class="book-title">{{ $payment->livre->titre }}</h5>
                  <p class="book-author">{{ $payment->livre->user->name ?? 'Unknown' }}</p>
                  <p class="book-date">
                    @if($payment->livre->last_read)
                      {{ \Carbon\Carbon::parse($payment->livre->last_read)->diffForHumans() }}
                    @else
                      <span class="text-muted">You haven't read it yet</span>
                    @endif
                  </p>
                  <a href="{{ route('livres.reader', $payment->livre->id) }}" class="btn btn-outline-accent2 w-100 mb-1">View</a>
                  <a href="{{ route('livres.download', $payment->livre->id) }}" class="btn btn-outline-accent2 w-100">Download</a>
                </div>
              @endforeach
            </div>
          @endif

        @endforeach
      </div>

    @endif
  </div>
</section>

<style>
/* keep your styling but ensure only one view visible */
.custom-table { background: transparent !important; border-collapse: separate; border-spacing: 0 10px; }
.custom-table thead { color: #fff; }
.custom-table th, .custom-table td { vertical-align: middle; padding: 12px; }
.custom-table tbody tr { background: rgba(255,255,255,0.05); border-radius: 10px; transition: transform 0.2s; }
.custom-table tbody tr:hover { transform: scale(1.01); }
.cover-img, .book-cover { width: 100%; height: 180px; object-fit: cover; border-radius: 5px; }
.btn.btn-outline-accent2 { font-size: 0.9rem; border-color: #57553fd6; color: var(--dark-color); background-color: #9a988584; border-radius: 8px; transition: 0.3s; }
.btn.btn-outline-accent2:hover { background-color: #57553f8a; color: var(--dark-color); }
.text-accent { color: #d1a085; font-weight: 600; }
</style>

<script>
document.getElementById('switchViewBtn').addEventListener('click', function() {
  const tableView = document.getElementById('tableView');
  const cardView = document.getElementById('cardView');
  if (tableView.classList.contains('d-none')) {
    tableView.classList.remove('d-none');
    tableView.style.display = 'block';
    cardView.classList.add('d-none');
    cardView.style.display = 'none';
    this.textContent = 'Switch to Card View';
  } else {
    tableView.classList.add('d-none');
    tableView.style.display = 'none';
    cardView.classList.remove('d-none');
    cardView.style.display = 'block';
    this.textContent = 'Switch to Table View';
  }
});
</script>
@endsection
