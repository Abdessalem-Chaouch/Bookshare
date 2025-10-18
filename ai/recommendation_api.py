from flask import Flask, request, jsonify
from flask_cors import CORS
import pymysql
from pymysql.err import MySQLError
from dotenv import load_dotenv
import os

# Charger les variables d'environnement depuis .env
load_dotenv()

app = Flask(__name__)
CORS(app)

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

# --- Lancer le serveur Flask ---
if __name__ == '__main__':
    app.run(debug=True, threaded=False)
