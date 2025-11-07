# API F1 Infractions - Symfony

API REST sécurisée par JWT pour gérer les infractions (pénalités et amendes) en Formule 1 TP.

## Membres du groupe

- Matthieu Barraque
- Yanis Ait-Bihi

## 📋 Table des matières

- [Technologies](#technologies)
- [Prérequis](#prérequis)
- [Installation](#installation)
- [Configuration](#configuration)
- [Démarrage](#démarrage)
- [Endpoints API](#endpoints-api)
- [Authentification](#authentification)
- [Tests](#tests)
- [Schéma de la base de données](#schéma-de-la-base-de-données)

## 🛠️ Technologies

- **PHP 8.1+**
- **Symfony 6.4**
- **MySQL 8.0**
- **Doctrine ORM**
- **LexikJWTAuthenticationBundle** pour l'authentification JWT
- **PHPUnit** pour les tests

## 📦 Prérequis

- PHP 8.1 ou supérieur
- Composer 2.x
- MySQL 8.0
- Extensions PHP : `pdo_mysql`, `mbstring`, `xml`, `ctype`, `intl`

## 🚀 Installation

### 1. Cloner le projet

```bash
git clone <url-du-repo>
cd TP_php_API_2
```

### 2. Installer les dépendances

```bash
composer install
```

### 3. Configurer la base de données

Créez un fichier `.env.local` à la racine du projet :

```env
DATABASE_URL="mysql://root:root_pwd@127.0.0.1:3306/f1_api?serverVersion=8.0&charset=utf8mb4"
```

Adaptez les credentials (`root:root_pwd`) selon votre configuration MySQL.

### 4. Créer la base de données

```bash
php bin/console doctrine:database:create
```

### 5. Exécuter les migrations

```bash
php bin/console doctrine:migrations:migrate -n
```

### 6. Générer les clés JWT

```bash
php bin/console lexik:jwt:generate-keypair
```

Les clés seront générées dans `config/jwt/`.

### 7. Charger les fixtures (données de test)

```bash
php bin/console doctrine:fixtures:load -n
```

## ⚙️ Configuration

### Utilisateurs de test

Les fixtures créent 3 utilisateurs :

| Email | Mot de passe | Rôle | Permissions |
|-------|-------------|------|-------------|
| `admin@example.com` | `admin123` | ROLE_ADMIN | Toutes les opérations + création d'infractions |
| `manager@example.com` | `manager123` | ROLE_MANAGER | Lecture + modification des pilotes d'écuries |
| `user@example.com` | `user123` | ROLE_USER | Lecture seule |

### Données de test

Les fixtures chargent :
- **4 écuries** : Ferrari, Mercedes, Red Bull, McLaren
- **12 pilotes** (3 par écurie, dont 1 réserviste)
- **1 moteur** par écurie
- **7 infractions** exemples

## 🎯 Démarrage

### Démarrer le serveur de développement

```bash
symfony server:start
```

Ou avec PHP natif :

```bash
php -S localhost:8000 -t public/
```

L'API sera accessible sur `http://localhost:8000`

## 📡 Endpoints API

### Authentication

#### Login
```http
POST /api/login
Content-Type: application/json

{
  "email": "admin@example.com",
  "password": "admin123"
}
```

**Réponse (200)** :
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
}
```

#### Créer un utilisateur
```http
POST /api/user/create
Content-Type: application/json

{
  "email": "newuser@example.com",
  "password": "password123"
}
```

### Teams

#### Lister toutes les écuries
```http
GET /api/team
Authorization: Bearer {token}
```

#### Lister les pilotes d'une écurie
```http
GET /api/team/{id}/drivers
GET /api/team/{id}/drivers?isStarter=true
Authorization: Bearer {token}
```

#### Modifier les pilotes d'une écurie (ROLE_MANAGER)
```http
PATCH /api/team/{id}/drivers
Authorization: Bearer {token}
Content-Type: application/json

{
  "add": [5, 6],
  "remove": [3]
}
```

### Drivers

#### Lister tous les pilotes
```http
GET /api/driver
Authorization: Bearer {token}
```

#### Détails d'un pilote
```http
GET /api/driver/{id}
Authorization: Bearer {token}
```

### Infractions

#### Lister les infractions (avec filtres optionnels)
```http
GET /api/infractions
GET /api/infractions?teamId=1
GET /api/infractions?driverId=1
GET /api/infractions?from=2025-03-01&to=2025-03-31
Authorization: Bearer {token}
```

#### Créer une pénalité pour un pilote (ROLE_ADMIN uniquement)
```http
POST /api/infractions
Authorization: Bearer {token}
Content-Type: application/json

{
  "type": "PENALTY_POINTS",
  "amount": 3,
  "driverId": 1,
  "raceName": "GP Bahrain",
  "occurredAt": "2025-03-08T14:00:00Z",
  "description": "Dépassement des limites de piste"
}
```

**Comportement automatique** : Si le pilote descend en dessous de 12 points, son statut passe à "suspendu".

#### Créer une amende pour une écurie (ROLE_ADMIN uniquement)
```http
POST /api/infractions
Authorization: Bearer {token}
Content-Type: application/json

{
  "type": "FINE_EUR",
  "amount": 50000.00,
  "teamId": 1,
  "raceName": "GP Monaco",
  "occurredAt": "2025-05-25T16:10:00Z",
  "description": "Sortie dangereuse des stands"
}
```

## 🔐 Authentification

L'API utilise JWT (JSON Web Tokens) pour l'authentification.

### Utilisation

1. **Obtenir un token** : `POST /api/login`
2. **Utiliser le token** : Ajouter le header `Authorization: Bearer {token}` à chaque requête

### Hiérarchie des rôles

```
ROLE_ADMIN
  ↳ ROLE_MANAGER
      ↳ ROLE_USER
```

- **ROLE_ADMIN** : Peut créer des infractions
- **ROLE_MANAGER** : Peut modifier les pilotes des écuries
- **ROLE_USER** : Lecture seule

## 🧪 Tests

### Collection Postman

Importez le fichier `F1_API.postman_collection.json` dans Postman.

La collection contient :
- Authentification (login admin, user, création d'utilisateur)
- Tests des routes Teams
- Tests des routes Drivers
- Tests des routes Infractions avec tous les filtres
- Gestion automatique du token JWT

### Tests PHPUnit

```bash
php bin/phpunit
```

## 🗄️ Schéma de la base de données

### Entity `User`
- `id` (PK)
- `email` (unique)
- `password` (hashed)
- `roles` (JSON)
- `isActive` (boolean)
- Timestamps

### Entity `Team`
- `id` (PK)
- `name` (unique, max 120 chars)
- Relation `OneToOne` avec `Engine`
- Relation `OneToMany` avec `Driver`
- Timestamps

### Entity `Engine`
- `id` (PK)
- `brand` (max 120 chars)
- Relation `OneToOne` avec `Team` (cascade delete)

### Entity `Driver`
- `id` (PK)
- `firstName`, `lastName` (max 100 chars)
- `isStarter` (boolean)
- `licensePoints` (int, default 12, range 0-12)
- `status` (string: 'active' | 'suspendu')
- `f1StartDate` (date)
- Relation `ManyToOne` avec `Team`
- Timestamps

### Entity `Infraction`
- `id` (PK)
- `occurredAt` (datetime)
- `raceName` (max 160 chars)
- `description` (text)
- `type` (string: 'PENALTY_POINTS' | 'FINE_EUR')
- `amount` (decimal 12,2)
- **Cible exclusive** : `driver` XOR `team` (exactement l'un des deux)
- Validation : cohérence type/montant

## 📝 Règles métier

### Pénalités en points

- Lorsqu'un pilote reçoit une pénalité en points :
  - Ses `licensePoints` sont décrémentés
  - Si `licensePoints < 12`, son `status` passe à "suspendu"
  - Transaction atomique (infraction + mise à jour pilote)

### Amendes

- Les amendes sont toujours en euros
- Ciblent uniquement les écuries
- Montant minimum : 0

### Contraintes

- Une infraction ne peut cibler **qu'un seul objet** : soit un pilote, soit une écurie
- Un pilote ne peut appartenir qu'à une seule écurie
- Une écurie possède exactement un moteur

## 🔧 Commandes utiles

```bash
# Vider le cache
php bin/console cache:clear

# Voir les routes
php bin/console debug:router

# Recharger les fixtures
php bin/console doctrine:fixtures:load -n

# Créer une migration
php bin/console make:migration

# Voir les logs
tail -f var/log/dev.log
```

## 🐛 Troubleshooting

### Erreur "Access denied for user"

Vérifiez votre `.env.local` et assurez-vous que les credentials MySQL sont corrects.

### Erreur JWT "Unable to find key"

Régénérez les clés JWT :
```bash
php bin/console lexik:jwt:generate-keypair --overwrite
```

### Erreur de migration

Supprimez et recréez la base :
```bash
php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate -n
php bin/console doctrine:fixtures:load -n
```

## 📄 Licence

Ce projet est réalisé dans le cadre d'un TP académique.

## 🚀 Améliorations possibles

- Pagination des résultats
- Rate limiting
- Refresh tokens
- Documentation OpenAPI/Swagger
- Cache Redis
