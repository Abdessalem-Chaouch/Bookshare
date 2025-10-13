from datasets import load_dataset
from transformers import AutoTokenizer

# Charger le dataset chapters
dataset = load_dataset("ubaada/booksum-complete-cleaned", "chapters")

# Charger le tokenizer du modèle
tokenizer = AutoTokenizer.from_pretrained("t5-small")  # ou "facebook/bart-large-cnn"

# Prétraitement
def preprocess_function(examples):
    inputs = [str(x) for x in examples["text"]]
    targets = [str(x) for x in examples["summary"]]
    model_inputs = tokenizer(inputs, max_length=512, truncation=True)
    labels = tokenizer(targets, max_length=150, truncation=True)
    model_inputs["labels"] = labels["input_ids"]
    return model_inputs

# Tokenisation
tokenized_datasets = dataset.map(preprocess_function, batched=True, remove_columns=dataset["train"].column_names)

# Sauvegarder
tokenized_datasets.save_to_disk("Ai/load_booksum")
print("✅ Dataset tokenisé et sauvegardé")
