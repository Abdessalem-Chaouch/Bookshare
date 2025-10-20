''''import pandas as pd
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
'''''
import pandas as pd
import mysql.connector
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity
import pickle
import os
from dotenv import load_dotenv

# ✅ Charger les variables depuis le .env Laravel
load_dotenv(dotenv_path=os.path.join(os.path.dirname(__file__), "../.env"))

# ✅ Récupérer les infos de connexion MySQL depuis le .env
DB_HOST = os.getenv("DB_HOST", "127.0.0.1")
DB_PORT = os.getenv("DB_PORT", "3306")
DB_DATABASE = os.getenv("DB_DATABASE", "bookshare")
DB_USERNAME = os.getenv("DB_USERNAME", "root")
DB_PASSWORD = os.getenv("DB_PASSWORD", "")

# ✅ Connexion à la base MySQL Laravel
conn = mysql.connector.connect(
    host=DB_HOST,
    user=DB_USERNAME,
    password=DB_PASSWORD,
    database=DB_DATABASE,
    port=DB_PORT
)

# ✅ Charger les données depuis la table `livres` (et `categories`)
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

# ✅ Nettoyage des champs
books_df['description'] = books_df['description'].fillna('')
books_df['categories'] = books_df['categories'].fillna('')

# ✅ Combiner les features
books_df['combined_features'] = books_df['categories'] + ' ' + books_df['description']

# ✅ TF-IDF
tfidf = TfidfVectorizer(stop_words='english')
tfidf_matrix = tfidf.fit_transform(books_df['combined_features'])

# ✅ Calcul de similarité cosinus
cosine_sim = cosine_similarity(tfidf_matrix, tfidf_matrix)

# ✅ Sauvegarde du modèle
os.makedirs("Ai/models", exist_ok=True)
with open("Ai/models/books_model.pkl", "wb") as f:
    pickle.dump({
        "books_df": books_df,
        "cosine_sim": cosine_sim
    }, f)

print("✅ Modèle entraîné à partir de la base MySQL Laravel et sauvegardé avec succès !")
