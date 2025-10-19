from flask import Flask, request, send_file, jsonify
from flask_cors import CORS
import pandas as pd
from gtts import gTTS
import io, re
from transformers import T5Tokenizer, T5ForConditionalGeneration, pipeline
from sentence_transformers import SentenceTransformer, util
import faiss
import numpy as np
import fitz  # PyMuPDF
import os
from PyPDF2 import PdfReader
import pymysql
from pymysql.err import MySQLError
from dotenv import load_dotenv
from sklearn.metrics.pairwise import cosine_similarity
import pickle
from transformers import AutoTokenizer
# Charger les variables d'environnement depuis .env
load_dotenv()

# ------
# -------------------
# Flask App
# -------------------------

# -------------------------
# Flask App
# -------------------------
app = Flask(__name__)
CORS(app, resources={r"/*": {"origins": "*"}}, supports_credentials=True)

# -------------------------
# Modèles et pipelines
# -------------------------
model_path = "Ai/models/booksum_t5_final"
tokenizer = T5Tokenizer.from_pretrained(model_path)
model = T5ForConditionalGeneration.from_pretrained(model_path)
summarizer = pipeline("summarization", model=model, tokenizer=tokenizer, device=-1)
# Ajouter quelque part après les imports et avant vos routes
#embed_model = SentenceTransformer('all-MiniLM-L6-v2')  # ou un autre modèle de votre choix
embed_model = SentenceTransformer('all-mpnet-base-v2')

qa_pipeline = pipeline("question-answering", model="deepset/roberta-base-squad2")



qa_tokenizer = AutoTokenizer.from_pretrained("deepset/roberta-base-squad2")

def split_for_qa(text, max_tokens=450):
    """Découpe le texte en chunks ≤ max_tokens pour Roberta."""
    words = text.split()
    chunks = []
    current = []
    for w in words:
        current.append(w)
        # Compter les tokens via le tokenizer
        token_count = len(qa_tokenizer.encode(" ".join(current), add_special_tokens=True))
        if token_count > max_tokens:
            current.pop()  # retirer le mot qui dépasse
            chunks.append(" ".join(current))
            current = [w]
    if current:
        chunks.append(" ".join(current))
    return chunks

def summarize_large_text(text, chunk_size=1500, max_tokens_per_chunk=250):
    """Résumé hiérarchique pour gros textes."""
    chunks = [text[i:i+chunk_size] for i in range(0, len(text), chunk_size)]
    partial_summaries = []

    for chunk in chunks:
        summary = summarizer(
            chunk,
            max_new_tokens=max_tokens_per_chunk,
            do_sample=False
        )[0]['summary_text'].strip()
        partial_summaries.append(summary)

    while len(partial_summaries) > 1:
        new_summaries = []
        for i in range(0, len(partial_summaries), 3):
            block = " ".join(partial_summaries[i:i+3])
            summary = summarizer(
                block,
                max_new_tokens=max_tokens_per_chunk,
                do_sample=False
            )[0]['summary_text'].strip()
            new_summaries.append(summary)
        partial_summaries = new_summaries

    return partial_summaries[0] if partial_summaries else "Je ne sais pas"

# ------------------------- Routes -------------------------
@app.route('/embed_book', methods=['POST'])
def embed_book():
    data = request.get_json()
    text = data.get('text', '')
    if not text:
        return jsonify({"error": "No text provided"}), 400

    # Découper en gros chunks pour embeddings
    chunks = [text[i:i+3000] for i in range(0, len(text), 3000)]
    embeddings = [embed_model.encode(c).tolist() for c in chunks]
    return jsonify({"chunks": chunks, "embeddings": embeddings})

@app.route('/ask', methods=['POST'])
def ask_question():
    data = request.get_json()
    question = data.get('question', '').lower()
    chunks = data.get('chunks', [])
    embeddings = data.get('embeddings', [])

    if not question or not chunks or not embeddings:
        return jsonify({"error": "Missing data"}), 400

    # Détecter si la question est précise
    precise_keywords = ["page", "quel", "combien", "qui", "où", "quand", "?"]
    is_precise = any(k in question for k in precise_keywords)

    if is_precise:
        # QA sur les 3 chunks les plus pertinents
        question_embedding = embed_model.encode(question).reshape(1, -1)
        similarities = cosine_similarity(question_embedding, np.array(embeddings))
        top_indices = similarities.argsort()[0][-3:]

        answers = []
        for idx in top_indices:
            context = chunks[idx]
        small_chunks = split_for_qa(context)
        for sc in small_chunks:
            ans = qa_pipeline({'question': question, 'context': sc})['answer']
            if ans.strip():
                answers.append(ans.strip())

        answer = answers[0] if answers else "Je ne sais pas"

    else:
        # Résumé global
        full_text = " ".join(chunks)
        answer = summarize_large_text(full_text)

    return jsonify({"answer": answer})

# -------------------------
# Route pour charger un livre
# -------------------------


@app.route('/speak', methods=['POST', 'OPTIONS'])
def speak():
    if request.method == 'OPTIONS':
        response = app.make_default_options_response()
        response.headers.add("Access-Control-Allow-Origin", "*")
        response.headers.add("Access-Control-Allow-Headers", "Content-Type")
        response.headers.add("Access-Control-Allow-Methods", "POST, OPTIONS")
        return response

    try:
        data = request.get_json()
        text = data.get('text', '')
        lang = data.get('lang', 'fr')

        if not text:
            return jsonify({'error': 'No text provided'}), 400

        tts = gTTS(text, lang=lang)
        mp3_data = io.BytesIO()
        tts.write_to_fp(mp3_data)
        mp3_data.seek(0)

        response = send_file(mp3_data, mimetype="audio/mpeg")
        response.headers.add("Access-Control-Allow-Origin", "*")
        return response

    except Exception as e:
        print("[ERROR]", e)
        return jsonify({'error': str(e)}), 500



def clean_text(text):
    text = re.sub(r'http\S+', '', text)
    text = re.sub(r'Vous pouvez utiliser le code à l\'adresse suivante.*', '', text)
    text = text.replace("’", "'").replace("‘", "'").replace("«", "").replace("»", "")
    text = re.sub(r'[^A-Za-zÀ-ÖØ-öø-ÿ0-9\s.,;:!?\'"-]', '', text)
    text = re.sub(r'\s+', ' ', text)
    return text.strip()

# === Nettoyage du résumé final ===
def clean_summary(text):
    text = text.replace("’", "'").replace("‘", "'").replace("«", "").replace("»", "")
    text = re.sub(r'\b[a-zA-Z]\b', '', text)
    text = re.sub(r'[:&@#*]', '', text)
    text = re.sub(r'\s+([.,;!?])', r'\1', text)
    text = re.sub(r'\.{2,}', '.', text)
    text = re.sub(r'\?{2,}', '?', text)
    text = re.sub(r'\!{2,}', '!', text)
    text = re.sub(r'\s+', ' ', text)
    return text.strip()

# === Découpage du texte en chunks par tokens ===
def chunk_text_by_tokens(text, max_tokens=500):
    words = text.split()
    chunks = []
    current_chunk = []
    current_tokens = 0

    for word in words:
        word_tokens = len(tokenizer.encode(word, add_special_tokens=False))
        if current_tokens + word_tokens > max_tokens:
            chunks.append(" ".join(current_chunk))
            current_chunk = [word]
            current_tokens = word_tokens
        else:
            current_chunk.append(word)
            current_tokens += word_tokens

    if current_chunk:
        chunks.append(" ".join(current_chunk))
    return chunks

# === Résumé hiérarchique ===
def hierarchical_summarize(text, chunk_size=500):
    # 1️⃣ Découper en chunks
    chunks = chunk_text_by_tokens(text, max_tokens=chunk_size)
    partial_summaries = []

    # 2️⃣ Résumer chaque chunk
    for chunk in chunks:
        n_tokens = min(400, max(50, int(len(tokenizer(chunk)['input_ids']) * 0.6)))

        summary = summarizer(
            chunk,
            max_new_tokens=n_tokens,
            do_sample=False,
            early_stopping=True
        )[0]['summary_text'].strip()
        partial_summaries.append(summary)

    # 3️⃣ Résumer les résumés partiels si >1
    while len(partial_summaries) > 1:
        new_summaries = []
        for i in range(0, len(partial_summaries), 3):  # regrouper par 3
            block = " ".join(partial_summaries[i:i+3])
            n_tokens = min(400, max(50, int(len(tokenizer(block)['input_ids']) * 0.6)))
            summary = summarizer(
                block,
                max_new_tokens=n_tokens,
                do_sample=False,
                early_stopping=True
            )[0]['summary_text'].strip()
            new_summaries.append(summary)
        partial_summaries = new_summaries

    final_summary = clean_summary(partial_summaries[0])
    return final_summary

# === Route Flask pour résumer ===
@app.route('/summarize', methods=['POST'])
def summarize():
    data = request.get_json()
    text = data.get('text', '')

    if not text:
        return jsonify({"error": "No text provided"}), 400

    try:
        text = clean_text(text)
        final_summary = hierarchical_summarize(text, chunk_size=1500)

        return jsonify({"summary": final_summary})
    except Exception as e:
        print("[ERROR]", e)
        return jsonify({"error": str(e)}), 500

# === Route pour embeddings (optionnel) ===
@app.route('/embed', methods=['POST'])
def embed_text():
    data = request.get_json()
    text = data.get('text', '')

    if not text:
        return jsonify({"error": "No text provided"}), 400

    try:
        text = clean_text(text)
        chunks = chunk_text_by_tokens(text, max_tokens=500)
        embeddings = [embed_model.encode(c).tolist() for c in chunks]
        return jsonify({"embeddings": embeddings})
    except Exception as e:
        print("[ERROR]", e)
        return jsonify({"error": str(e)}), 500



# --- Fonction pour obtenir une connexion MySQL via PyMySQL ---
def get_connection():
    try:
        conn = pymysql.connect(
            host=os.getenv("DB_HOST", "127.0.0.1"),
            port=int(os.getenv("DB_PORT", 3308)),
            user=os.getenv("DB_USERNAME", "root"),
            password=os.getenv("DB_PASSWORD", ""),
            database=os.getenv("DB_DATABASE", "bookshare"),
            cursorclass=pymysql.cursors.DictCursor,  # Retourne dict au lieu de tuple
            connect_timeout=5
        )
        return conn
    except MySQLError as e:
        print("❌ Erreur connexion MySQL:", e)
        return None
    except Exception as ex:
        print("❌ Autre erreur :", ex)
        return None

# --- Route de recommandation ---
@app.route('/recommend', methods=['POST'])
def recommend():
    data = request.get_json()
    if not data or "user_id" not in data:
        return jsonify({"error": "user_id manquant"}), 400

    user_id = data["user_id"]
    conn = get_connection()
    if not conn:
        return jsonify({"error": "Impossible de se connecter à la base de données"}), 500

    try:
        cursor = conn.cursor()

        # 1️⃣ Compter les likes par catégorie
        cursor.execute("""
            SELECT b.category_id, COUNT(*) as likes_count
            FROM likes l
            JOIN blogs b ON l.blog_id = b.id
            WHERE l.user_id = %s
            GROUP BY b.category_id
        """, (user_id,))
        likes_by_category = cursor.fetchall()

        if not likes_by_category:
            return jsonify({"message": "Aucun like trouvé pour cet utilisateur", "recommendations": []})

        # 2️⃣ Catégories préférées
        max_likes = max(row['likes_count'] for row in likes_by_category)
        favorite_categories = [row['category_id'] for row in likes_by_category if row['likes_count'] == max_likes]

        # 3️⃣ Articles déjà aimés
        cursor.execute("SELECT blog_id FROM likes WHERE user_id = %s", (user_id,))
        liked_articles = [row['blog_id'] for row in cursor.fetchall()]

        # 4️⃣ Articles recommandés
        format_categories = ','.join(['%s'] * len(favorite_categories))
        query = f"SELECT * FROM blogs WHERE category_id IN ({format_categories})"
        params = favorite_categories

        if liked_articles:
            format_liked = ','.join(['%s'] * len(liked_articles))
            query += f" AND id NOT IN ({format_liked})"
            params += liked_articles

        cursor.execute(query, params)
        recommendations = cursor.fetchall()

        return jsonify({"recommendations": recommendations})

    except MySQLError as e:
        return jsonify({"error": "Erreur base de données", "details": str(e)}), 500
    except Exception as ex:
        return jsonify({"error": "Erreur serveur", "details": str(ex)}), 500
    finally:
        try:
            cursor.close()
            conn.close()
        except:
            pass

# --- Route GET pour tester le serveur ---
@app.route('/', methods=['GET'])
def home():
    return jsonify({"message": "Serveur Flask actif. Utiliser POST /recommend avec JSON."})


with open("C:/Bookshare/Ai/books_model.pkl", "rb") as f:
    data = pickle.load(f)

books_df = data['books_df']
cosine_sim = data['cosine_sim']

@app.route('/recommendBook/<title>', methods=['GET'])
def recommendBook(title):
    title = title.replace('+', ' ').strip()
    # 🔍 Trouver le livre
    indices = books_df[books_df['title'].str.lower() == title.lower()].index
    if len(indices) == 0:
        return jsonify({"error": "Livre non trouvé"}), 404

    idx = indices[0]
    sim_scores = list(enumerate(cosine_sim[idx]))
    sim_scores = sorted(sim_scores, key=lambda x: x[1], reverse=True)
    top_indices = [i for i, score in sim_scores[1:6]]

    results = []
    for i in top_indices:
        book = books_df.iloc[i]
        results.append({
            "id": int(book['id']),
            "title": str(book['title']),
            "isbn13": str(book['isbn13']),
            "categories": str(book['categories']),
            "thumbnail": str(book['thumbnail']),
            "description": str(book['description']),
            "prix": float(book['prix']) if pd.notna(book['prix']) else None
        })

    return jsonify(results)



# -------------------------
if __name__ == '__main__':
    app.run(port=5000, threaded=True)
