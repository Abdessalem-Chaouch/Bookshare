<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Nouveau Blog</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 24px;
            margin: 0;
        }

        .meta {
            color: #888;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .content img {
            max-width: 100%;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        /* Bouton rempli */
        .btn-filled {
            display: inline-block;
            background-color: #007bff;
            /* couleur de fond */
            color: #fff;
            /* texte */
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
        }

        /* Bouton avec bordure seulement */
        .btn-outline {
            display: inline-block;
            background-color: transparent;
            /* transparent */
            color: #007bff;
            /* texte */
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            border: 2px solid #007bff;
            /* bordure */
        }
    </style>

</head>

<body>
    <div class="container">
        <div class="header">
            <h1>{{ $blog->title }}</h1>
            <div class="meta">
                Publié le {{ $blog->created_at->format('d M, Y') }} |
                Catégorie : {{ $blog->category->name ?? 'N/A' }}
            </div>
        </div>

        <div class="content">


            <p>{{ $blog->content }}</p>

            <p style="text-align:center; margin-top:20px;">
                <a href="{{ route('articleDetail', $blog->id) }}" class="btn">Voir le blog complet</a>
            </p>
        </div>
    </div>
</body>

</html>