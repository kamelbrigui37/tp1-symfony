# Réponses au TP Symfony

## Partie 5 — CRUD des catégories

### 1. Pourquoi utiliser CategorieType ?
CategorieType centralise la définition du formulaire de création
et de modification des catégories.

### 2. Pourquoi valider les données ?
La validation empêche l'enregistrement de données incorrectes,
comme un nom vide ou une description trop courte.

### 3. Pourquoi utiliser un token CSRF ?
Le token CSRF permet de vérifier que la demande de suppression
provient d'un formulaire légitime de l'application.

### 4. Pourquoi empêcher la suppression d'une catégorie
qui contient des articles ?
Cela préserve la cohérence des données et évite de supprimer
une catégorie encore utilisée.

### 5. Pourquoi utiliser Git et une Pull Request ?
Une branche permet de développer une fonctionnalité séparément.
La Pull Request permet de vérifier les changements avant
de les fusionner dans main.
