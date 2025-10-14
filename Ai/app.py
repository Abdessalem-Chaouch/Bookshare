from flask import Flask, request, send_file, jsonify
from flask_cors import CORS
from gtts import gTTS
import io
from transformers import T5Tokenizer, T5ForConditionalGeneration, pipeline
import re

app = Flask(__name__)
CORS(app, resources={r"/*": {"origins": "*"}}, supports_credentials=True)

# -------------------------
# ✅ Nettoyage du texte
# -------------------------
def clean_text(text):
    # Supprimer motifs parasites fréquents dans les PDF/ebooks
    text = re.sub(r'(--t 0 N|\.{2,}|a{2,}|c Ol ;: >- 0\. 0 u|: p\. \d+ \(b\)|F\. Schmidt/Fotolia)', ' ', text)
    # Supprimer caractères bizarres
    text = re.sub(r'[^a-zA-Z0-9À-ÿ\s\.,;:\-\'\"]+', ' ', text)
    # Espaces multiples
    text = re.sub(r'\s+', ' ', text)
    return text.strip()

# -------------------------
# ✅ Text-to-Speech Route
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

# -------------------------
# ✅ Summarization Route
# -------------------------
model_path = "Ai/models/booksum_t5_final"

tokenizer = T5Tokenizer.from_pretrained(model_path)
model = T5ForConditionalGeneration.from_pretrained(model_path)

summarizer = pipeline(
    "summarization",
    model=model,
    tokenizer=tokenizer,
    device=-1  # CPU, ou 0 pour GPU
)

def chunk_text(text, max_chars=3000):
    """Découpe le texte en blocs de max_chars caractères"""
    chunks = []
    start = 0
    while start < len(text):
        end = start + max_chars
        if end < len(text):
            end = text.rfind('.', start, end) + 1
            if end <= start:
                end = start + max_chars
        chunk = text[start:end].strip()
        if chunk:
            chunks.append(chunk)
        start = end
    return chunks

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
# ✅ Lancer l’application
# -------------------------
if __name__ == '__main__':
    app.run(port=5000)
