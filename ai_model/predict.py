import joblib
import sys
import json

# Charger le modèle AI
model = joblib.load('subscription_model.pkl')

# Récupérer les paramètres
emprunts = float(sys.argv[1])
budget = float(sys.argv[2])

# Prédiction AI
prediction = model.predict([[emprunts, budget]])[0]
probability = model.predict_proba([[emprunts, budget]]).max()

# Retourner le résultat
result = {
    'prediction': prediction,
    'confidence': float(probability)
}

print(json.dumps(result))