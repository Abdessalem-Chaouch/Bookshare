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
                            <div class="product-item">
                                <figure class="product-style">
                                    <img src="{{ asset('storage/' . $livre->photo_couverture) }}" alt="{{ $livre->titre }}" class="livre-image">

                                    <button type="button"
                                        class="add-to-cart btn btn-sm btn-primary"
                                        data-product-id="{{ $livre->id }}">
                                        Add to Cart
                                    </button>

                                    </figure>
                                    <figcaption>
                                         <h3>
                            <a href="#" class="book-link" data-title="{{ $livre->titre }}">
                                {{ $livre->titre }}
                            </a>
                        </h3>
                         <span>{{ $livre->user ? $livre->user->name : 'Auteur inconnu' }}</span>
                                        <p><strong>Prix :</strong> {{ $livre->prix ? $livre->prix . ' DT' : 'Non spécifié' }}</p>
                                        <a href="{{ route('livres.showf', ['livre' => $livre->id]) }}" class="btn btn-outline-primary">
                                📖 Voir détails
                            </a>
                                     @php
                                        $texte = "Je recommande ce livre !\n\n";
                                        $texte .= "Titre : {$livre->titre}\n";

                                        $texte .= "Description : {$livre->description}\n";
                                        $texte .= "Image : " . asset('storage/' . $livre->photo_couverture) . "\n";
                                        $texte .= "Voir le livre ici : " . route('livres.showf', $livre->id);
                                        $texteEncode = urlencode($texte);
                                    @endphp
                                    <a href="https://wa.me/?text={{ $texteEncode }}" target="_blank" class="btn btn-success mt-2">
                Partager sur WhatsApp
            </a>
            
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
                                            body: JSON.stringify({
                                                livre_id: livreId
                                            })
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
                <div class="row mt-4">
                    <div class="col-md-12 text-center">
                    {{ $livres->links('vendor.pagination.bootstrap-5') }}

                    </div>
                </div>



                </div>

            </div>
        </div>
    </div>
</section>
<section id="recommended-books" class="py-5 my-5">
    <div class="container">
        <h2 class="section-title text-center">Books Recommendations</h2>
        <div class="row" id="recommendation-list">
            <p class="text-center">Cliquez sur un livre pour voir les recommandations.</p>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){

    // 🧩 1️⃣ Charger les recommandations sauvegardées dans localStorage
    const savedRecommendations = localStorage.getItem('recommendations_html');
    const savedTitle = localStorage.getItem('recommendations_title');

    if (savedRecommendations && savedRecommendations.trim() !== "") {
        $('#recommendation-list').html(savedRecommendations);
        $('#recommended-books .section-title').text('Books Recommendations for "' + savedTitle + '"');
    }

    // 🧩 2️⃣ Quand un utilisateur clique sur un livre
  // 👉 Ne déclenche les recommandations QUE quand on clique sur le titre du livre
$('.book-link').click(function(e){
    e.preventDefault();

    let titre = $(this).data('title');

    // ✅ Stocker le titre dans localStorage
    localStorage.setItem('recommendations_title', titre);

    $.ajax({
        url: '/livres/recommendations/' + encodeURIComponent(titre),
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            let html = '';

            if (!data || data.length === 0) {
                html = '<p class="text-center">Aucune recommandation trouvée.</p>';
            } else {
                data.forEach(function(book){
                    let cover = book.thumbnail && book.thumbnail !== "nan" ? book.thumbnail : '/storage/default.jpg';
                    let author = book.authors || 'Auteur inconnu';
                    let title = book.title || 'Titre inconnu';
                    let price = book.prix ? book.prix + ' DT' : 'Non spécifié';

                    html += `
                        <div class="col-md-3 mb-4">
                            <div class="product-item">
                                <figure class="product-style">
                                    <img src="${cover}" alt="${title}" class="livre-image">
                                </figure>
                                <figcaption>
                                    <h3>${title}</h3>
                                    <span>${author}</span>
                                    <p><strong>Prix :</strong> ${price}</p>
                                </figcaption>
                            </div>
                        </div>`;
                });
            }

            $('#recommendation-list').html(html);
            $('#recommended-books .section-title').text('Books Recommendations for "' + titre + '"');
            localStorage.setItem('recommendations_html', html);

            $('html, body').animate({
                scrollTop: $("#recommended-books").offset().top
            }, 500);
        },
        error: function(err) {
            console.error(err);
            $('#recommendation-list').html('<p class="text-center text-danger">Erreur lors du chargement des recommandations.</p>');
        }
    });
});

});
</script>