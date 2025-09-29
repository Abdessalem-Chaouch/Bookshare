<section id="featured-books" class="py-5 my-5">
    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <div class="section-header align-center">
                    <div class="title">
                        <span>Some quality items</span>
                    </div>
                    <h2 class="section-title"> Books</h2>
                </div>

                <div class="product-list" data-aos="fade-up">
                    <div class="row">


                          @foreach($livres as $livre)
<div class="col-md-3">
    <div class="product-item" >
        <figure class="product-style">
            <img src="{{ asset('storage/' . $livre->photo_couverture) }}" alt="{{ $livre->titre }}" class="livre-image">

           <button type="button" 
               class="add-to-cart btn btn-sm btn-primary" 
            data-product-id="{{ $livre->id }}">
            Add to Cart
        </button>

        </figure>
        <figcaption>
            <h3><a href="{{ route('livres.showf', $livre->id) }}"> {{ $livre->titre }}</a></h3>
            <span>{{ $livre->user ? $livre->user->name : 'Auteur inconnu' }}</span>
            <p><strong>Prix :</strong> {{ $livre->prix ? $livre->prix . ' DT' : 'Non spécifié' }}</p>
        </figcaption>
    </div>
</div>
@endforeach




<script>
document.querySelectorAll('.add-to-cart').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const livreId = this.dataset.productId;

        fetch('{{ route("cart.add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ livre_id: livreId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                alert(data.error); // ❌ Affiche seulement si stock limité
            } else {
                // ✅ Mettre à jour le badge sans reload
                document.getElementById('cart-count').textContent = data.count;
            }
        })
        .catch(err => console.error(err));
    });
});
</script>




                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<style>
    .livre-image {
        width: 100%;
        height: 300px;
        object-fit: cover;
        display: block;
    }
</style>
