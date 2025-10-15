from flask import Flask, request, send_file, jsonify
from flask_cors import CORS
from gtts import gTTS
import io, re
from transformers import T5Tokenizer, T5ForConditionalGeneration, pipeline
from sentence_transformers import SentenceTransformer, util
import faiss
import numpy as np
import fitz  # PyMuPDF
import os
from PyPDF2 import PdfReader
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
embed_model = SentenceTransformer('all-MiniLM-L6-v2')  # ou un autre modèle de votre choix



def clean_text(text):
    import re
    text = re.sub(r'\s+', ' ', text)
    return text.strip()

# Stockage des embeddings de chaque PDF
pdf_index = {}

# ----------- Fonctions utilitaires -----------
def extract_pdf_text(pdf_path):
    """Extraire tout le texte du PDF"""
    reader = PdfReader(pdf_path)
    text = ""
    for page in reader.pages:
        text += page.extract_text() + "\n"
    return text

def chunk_text(text, chunk_size=1000):
    """Découper le texte en morceaux de chunk_size caractères"""
    chunks = []
    start = 0
    while start < len(text):
        chunks.append(text[start:start+chunk_size])
        start += chunk_size
    return chunks

def prepare_pdf(pdf_path):
    """Préparer les embeddings du PDF et les stocker dans pdf_index"""
    pdf_name = os.path.basename(pdf_path)
    if pdf_name in pdf_index:
        return  # déjà préparé
    text = extract_pdf_text(pdf_path)
    chunks = chunk_text(text, chunk_size=1000)
    embeddings = embed_model.encode(chunks, convert_to_tensor=True)
    pdf_index[pdf_name] = {"chunks": chunks, "embeddings": embeddings}
    print(f"[INFO] PDF '{pdf_name}' préparé avec {len(chunks)} chunks.")

# ----------- Endpoint /ask -----------
@app.route('/ask', methods=['POST'])
def ask_book():
    try:
        data = request.get_json()
        pdf_url = data.get("pdf")
        question = data.get("question")

        if not pdf_url or not question:
            return jsonify({"error": "PDF path or question missing"}), 400

        # Nettoyer l'URL
        pdf_url = re.sub(r'^https?://[^/]+', '', pdf_url).lstrip('/')
        if pdf_url.startswith("storage/"):
            pdf_url = pdf_url[len("storage/"):]

        # Chemin absolu
        BASE_DIR = os.path.dirname(os.path.abspath(__file__))
        pdf_path = os.path.normpath(os.path.join(BASE_DIR, '..', 'storage', 'app', 'public', pdf_url))

        if not os.path.exists(pdf_path):
            return jsonify({"error": f"PDF introuvable: {pdf_path}"}), 404

        # Préparer les embeddings
        prepare_pdf(pdf_path)
        pdf_name = os.path.basename(pdf_path)
        chunks = pdf_index[pdf_name]["chunks"]
        chunk_embeddings = pdf_index[pdf_name]["embeddings"]

        # Si question sur l'auteur, on regarde d'abord les premiers chunks
        if "auteur" in question.lower() or "qui a écrit" in question.lower():
            relevant_chunks = chunks[:5]  # premières pages
        else:
            # Recherche sémantique sur tous les chunks
            question_emb = embed_model.encode(question, convert_to_tensor=True)
            hits = util.semantic_search(question_emb, chunk_embeddings, top_k=10)
            relevant_chunks = [chunks[h['corpus_id']] for h in hits[0]]

        # Génération de réponse
        input_text = " ".join(relevant_chunks) + " Question: " + question
        inputs = tokenizer(input_text, return_tensors="pt", max_length=512, truncation=True)
        outputs = model.generate(**inputs, max_length=150)
        answer = tokenizer.decode(outputs[0], skip_special_tokens=True)

        return jsonify({"answer": answer})

    except Exception as e:
        print("[ERROR]", e)
        return jsonify({"error": str(e)}), 500

if __name__ == "__main__":
    app.run(debug=True)

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


summarizer = pipeline(
    "summarization",
    model=model,
    tokenizer=tokenizer,
    device=-1  # CPU, ou 0 pour GPU
)


@app.route('/summarize', methods=['POST'])
def summarize():
    data = request.get_json()
    text = data.get('text', '')
    if not text:
        return jsonify({"error": "No text provided"}), 400

    try:
        # Nettoyer le texte avant tout
        text = clean_text(text)

        # Découper en blocs
        chunks = chunk_text(text, max_chars=3000)
        partial_summaries = []

        for chunk in chunks:
            summary = summarizer(
                chunk,
                max_length=180,  # plus court pour chaque bloc
                min_length=60,
                do_sample=False,  # désactiver sampling pour éviter répétitions
                early_stopping=True
            )
            partial_summaries.append(summary[0]['summary_text'])

        final_summary = " ".join(partial_summaries)
        return jsonify({"summary": final_summary})

    except Exception as e:
        print("[ERROR]", e)
        return jsonify({"error": str(e)}), 500

# -------------------------
if __name__ == '__main__':
    app.run(port=5000, threaded=True)
