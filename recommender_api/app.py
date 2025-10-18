from flask import Flask, jsonify
import pickle
import pandas as pd

app = Flask(__name__)

# Charger modèle
with open("books_model.pkl", "rb") as f:
    data = pickle.load(f)

books_df = data['books_df']
cosine_sim = data['cosine_sim']

@app.route('/recommend/<title>', methods=['GET'])
def recommend(title):
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

if __name__ == '__main__':
    app.run(debug=True)
