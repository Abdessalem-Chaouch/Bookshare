from flask import Flask, request, send_file, jsonify
from flask_cors import CORS
from gtts import gTTS
import io
from transformers import T5Tokenizer, T5ForConditionalGeneration, pipeline

app = Flask(__name__)
CORS(app, resources={r"/*": {"origins": "*"}}, supports_credentials=True)

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

# Charger le tokenizer et le modèle (vérifie que spiece.model est présent)
tokenizer = T5Tokenizer.from_pretrained(model_path)
model = T5ForConditionalGeneration.from_pretrained(model_path)
summarizer = pipeline("summarization", model=model, tokenizer=tokenizer)

@app.route('/summarize', methods=['POST'])
def summarize():
    data = request.get_json()
    text = data.get('text', '')
    if not text:
        return jsonify({"error": "No text provided"}), 400

    try:
        summary = summarizer(text, max_length=150, min_length=30, do_sample=False)
        return jsonify({"summary": summary[0]['summary_text']})
    except Exception as e:
        print("[ERROR]", e)
        return jsonify({"error": str(e)}), 500

# -------------------------
# ✅ Lancer l’application
# -------------------------
if __name__ == '__main__':
    app.run(port=5000)
