import pandas as pd
import mysql.connector
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity
import pickle

# ✅ Connexion à ta base MySQL Laravel
conn = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",  # ton mot de passe MySQL
    database="bookshare",  # remplace par le nom réel
    port="3308"
)

# ✅ Charger les données depuis la table `livres` (et éventuellement `categories`)
query = """
SELECT
    l.id,
    l.titre AS title,
    l.description,
    l.isbn AS isbn13,
    l.photo_couverture AS thumbnail,
    l.prix,
    c.name AS categories
FROM livres l
LEFT JOIN categories c ON l.categorie_id = c.id
"""
books_df = pd.read_sql(query, conn)

conn.close()

# ✅ Nettoyage
books_df['description'] = books_df['description'].fillna('')
books_df['categories'] = books_df['categories'].fillna('')

# ✅ Feature combinée : catégorie + description
books_df['combined_features'] = books_df['categories'] + ' ' + books_df['description']

# ✅ TF-IDF
tfidf = TfidfVectorizer(stop_words='english')
tfidf_matrix = tfidf.fit_transform(books_df['combined_features'])

# ✅ Similarité cosinus
cosine_sim = cosine_similarity(tfidf_matrix, tfidf_matrix)

# ✅ Sauvegarde du modèle
with open("books_model.pkl", "wb") as f:
    pickle.dump({
        "books_df": books_df,
        "cosine_sim": cosine_sim
    }, f)

print("✅ Modèle entraîné à partir de ta base MySQL et sauvegardé avec succès !")
