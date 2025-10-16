import pandas as pd
import numpy as np
from sklearn.ensemble import RandomForestClassifier
from sklearn.model_selection import train_test_split
import joblib

# Données d'entraînement simulées
data = {
    'emprunts_mois': [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12, 15, 18, 20, 1, 2, 3, 5, 8, 12],
    'budget': [8, 12, 15, 18, 22, 25, 28, 32, 35, 38, 45, 50, 55, 60, 10, 14, 16, 20, 30, 40],
    'abonnement': ['Basic', 'Basic', 'Basic', 'Premium', 'Premium', 'Premium', 'Premium', 'VIP', 'VIP', 'VIP', 'VIP', 'VIP', 'VIP', 'VIP', 'Basic', 'Basic', 'Basic', 'Premium', 'Premium', 'VIP']
}

df = pd.DataFrame(data)

# Préparation des données
X = df[['emprunts_mois', 'budget']]
y = df['abonnement']

# Division train/test
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

# Entraînement du modèle AI
model = RandomForestClassifier(n_estimators=100, random_state=42)
model.fit(X_train, y_train)

# Évaluation
accuracy = model.score(X_test, y_test)
print(f"Précision du modèle: {accuracy:.2f}")

# Sauvegarde du modèle
joblib.dump(model, 'subscription_model.pkl')
print("Modèle AI sauvegardé!")

# Test
test_prediction = model.predict([[5, 25]])
print(f"Test: 5 emprunts, 25€ budget → {test_prediction[0]}")