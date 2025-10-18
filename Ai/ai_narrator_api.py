from flask import Flask, request, send_file, jsonify
from flask_cors import CORS
from gtts import gTTS
import io

app = Flask(__name__)

# ✅ Autoriser localhost:8000 et le header Content-Type
CORS(app, resources={r"/speak": {"origins": "*"}}, supports_credentials=True)

@app.route('/speak', methods=['POST', 'OPTIONS'])
def speak():
    if request.method == 'OPTIONS':
        # ✅ Réponse manuelle pour la pré-requête CORS
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
        response.headers.add("Access-Control-Allow-Origin", "*")  # ✅ encore ici
        return response

    except Exception as e:
        print("[ERROR]", e)
        return jsonify({'error': str(e)}), 500


if __name__ == '__main__':
    app.run(port=5000)
