# SOUTARAH Satisfaction

Application de gestion de la satisfaction client pour SOUTARAH GROUP.

## Prérequis
- XAMPP (Apache + MySQL)
- PHP 8.0 ou supérieur
- Base de données MySQL (`soutarah_satisfaction`)

## Installation

1. Placez le dossier `soutarah` dans le répertoire `C:\xampp\htdocs\`.
2. Lancez Apache et MySQL via XAMPP Control Panel.
3. Importez la structure de votre base de données dans PhpMyAdmin (si ce n'est pas déjà fait).
4. Le mot de passe root de MySQL est vide par défaut sur XAMPP. Si vous avez un mot de passe, modifiez `config/database.php`.

## Base de données requise
Le projet s'attend à trouver les tables suivantes dans `soutarah_satisfaction` :
- `vehicles`
- `admins`
- `satisfaction_responses`

*(Voir le prompt de spécifications pour la liste complète des colonnes)*

## Création du premier administrateur
Pour vous connecter, vous devez d'abord créer un administrateur directement dans la base de données. 
Le mot de passe doit être haché avec `password_hash()`.

Exemple pour insérer un admin `admin@soutarah.com` avec le mot de passe `password123` :
```sql
INSERT INTO admins (name, email, password, created_at, updated_at) 
VALUES ('Administrateur', 'admin@soutarah.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW());
```
*(Le hash ci-dessus correspond au mot de passe : `password`)*

## Utilisation

**Accès Public (Client)**
- L'URL du formulaire d'un véhicule spécifique est : 
  `http://localhost/soutarah/public/satisfaction.php?vehicle=CODE_DU_VEHICULE`
- Ce lien est celui qui doit être encodé dans le QR code.

**Accès Administration**
- Rendez-vous sur : `http://localhost/soutarah/public/index.php` et cliquez sur "Accès Administration".
- Ou directement via : `http://localhost/soutarah/admin/login.php`

## Génération des QR Codes
- Pour ce projet natif, les liens QR sont affichés dans la liste des véhicules (bouton "Lien QR"). 
- Vous pouvez utiliser un générateur externe ou intégrer une bibliothèque JavaScript/PHP future pour télécharger physiquement l'image QR dans le dossier `qrcodes/`.

## Sécurité
- Protection contre les injections SQL (Requêtes préparées PDO).
- Protection contre les attaques XSS (Échappement avec `htmlspecialchars` via la fonction `e()`).
- Protection contre les failles CSRF (Tokens ajoutés dans tous les formulaires).
- Mots de passe chiffrés (Bcrypt).
