""" from transformers import T5ForConditionalGeneration, Trainer, TrainingArguments, AutoTokenizer
from datasets import load_from_disk
import os 
# Charger le dataset
# dataset = load_from_disk("C:/Bookshare/Ai/load_booksum")
dataset_path = os.path.join(os.path.dirname(__file__), "load_booksum")
dataset = load_from_disk(dataset_path)

print(dataset)  # vérifier colonnes : doit inclure "text" et "summary"

# Charger le tokenizer
tokenizer = AutoTokenizer.from_pretrained("t5-small")

# Fonction de tokenisation
def preprocess_function(examples):
    inputs = examples["text"]
    targets = examples["summary"]
    model_inputs = tokenizer(inputs, max_length=512, truncation=True)
    labels = tokenizer(targets, max_length=150, truncation=True)
    model_inputs["labels"] = labels["input_ids"]
    return model_inputs

# Tokeniser le dataset
tokenized_dataset = dataset.map(preprocess_function, batched=True)

# Charger le modèle
model = T5ForConditionalGeneration.from_pretrained("t5-small")

# Arguments d'entraînement
training_args = TrainingArguments(
    output_dir="Ai/models/booksum_t5",
    per_device_train_batch_size=1,
    num_train_epochs=1,
    learning_rate=2e-4,
    weight_decay=0.01,
    save_total_limit=2,
    logging_dir="Ai/logs",
    logging_steps=10,
    remove_unused_columns=False  # ⚡ important
)

# Créer le Trainer
trainer = Trainer(
    model=model,
    args=training_args,
    train_dataset=tokenized_dataset,
    eval_dataset=None
)

# Lancer l’entraînement
trainer.train()

model.save_pretrained("Ai/models/booksum_t5_final")
tokenizer.save_pretrained("Ai/models/booksum_t5_final") 
print("✅ Entraînement terminé et modèle + tokenizer sauvegardés !")
"""


from transformers import T5ForConditionalGeneration, Trainer, TrainingArguments, AutoTokenizer
from datasets import load_from_disk
import os

# 🔹 Définir le chemin du dataset
dataset_path = os.path.join(os.path.dirname(__file__), "load_booksum")

if not os.path.exists(dataset_path):
    raise FileNotFoundError(f"Le dataset n'existe pas dans {dataset_path}. Génère-le avec load_booksum.py")

dataset = load_from_disk(dataset_path)

# Colonnes disponibles
print("Colonnes disponibles dans le dataset :")
for split, ds in dataset.items():
    print(f"{split}: {ds.column_names}")

# Tokenisation si nécessaire
if 'text' in dataset[list(dataset.keys())[0]].column_names and 'summary' in dataset[list(dataset.keys())[0]].column_names:
    tokenizer = AutoTokenizer.from_pretrained("t5-small")

    def preprocess_function(examples):
        inputs = examples["text"]
        targets = examples["summary"]
        model_inputs = tokenizer(inputs, max_length=128, truncation=True)  # <- réduit la longueur
        labels = tokenizer(targets, max_length=64, truncation=True)       # <- réduit la longueur
        model_inputs["labels"] = labels["input_ids"]
        return model_inputs

    tokenized_dataset = dataset.map(preprocess_function, batched=True)
else:
    print("⚡ Dataset déjà tokenisé")
    tokenized_dataset = dataset

# 🔹 Utiliser un petit sous-ensemble pour test rapide
train_dataset = tokenized_dataset["train"].select(range(100))  # <- juste 100 exemples
eval_dataset = tokenized_dataset.get("validation", tokenized_dataset["train"]).select(range(20))  # <- 20 exemples

# Charger le modèle
model = T5ForConditionalGeneration.from_pretrained("t5-small")

# Arguments d'entraînement
training_args = TrainingArguments(
    output_dir="Ai/models/booksum_t5_test",
    per_device_train_batch_size=2,  # petit batch
    num_train_epochs=1,
    logging_steps=5,
    save_total_limit=1,
    remove_unused_columns=False
)

trainer = Trainer(
    model=model,
    args=training_args,
    train_dataset=train_dataset,
    eval_dataset=eval_dataset
)

# Lancer l’entraînement rapide
trainer.train()

# Sauvegarder le modèle et tokenizer
model.save_pretrained("Ai/models/booksum_t5_test")
if 'tokenizer' in locals():
    tokenizer.save_pretrained("Ai/models/booksum_t5_test")

print("✅ Test d'entraînement terminé !")
