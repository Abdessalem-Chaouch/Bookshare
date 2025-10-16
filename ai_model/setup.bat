@echo off
echo Installation du modele AI...

echo 1. Installation des dependances Python...
pip install -r requirements.txt

echo 2. Entrainement du modele AI...
python train_model.py

echo 3. Test du modele...
python predict.py 5 25

echo Modele AI pret !
pause