from transformers import T5ForConditionalGeneration, Trainer, TrainingArguments, AutoTokenizer
from datasets import load_from_disk

# Charger le dataset
dataset = load_from_disk("C:/Bookshare/Ai/load_booksum")
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

