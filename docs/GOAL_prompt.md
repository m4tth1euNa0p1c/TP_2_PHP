# Master Prompt — TP API F1 (Symfony 6.4 + MySQL + JWT)

## 0) Objectif & périmètre

Tu construis une **API publique JSON** sous **Symfony 6.4 / PHP 8.1+** avec **Doctrine ORM** et **LexikJWT**.
Base **MySQL**. Respecte les **codes HTTP** (y compris en erreur), **validation stricte**, **logs** (pas d’infos sensibles en réponse), **types d’arguments d’URL** et **méthodes REST**. Utilise **git** avec commits fréquents. 

Fonctionnel F1 exigé :

* Écuries ↔ pilotes (titulaire/réserviste, points licence = 12 par défaut, date début F1).
* 1 écurie **possède 1 moteur** (marque).
* **Registre d’infractions** (pénalités en points OU amendes en €) visant **soit** un pilote **soit** une écurie, avec nom de course, description, date.
* **Route pour modifier les pilotes d’une écurie.**
* **Route pour infliger une amende/pénalité à une écurie OU un pilote.**
* **Route listant toutes les infractions** + filtres par **écurie**, **pilote**, **date**.
* **Service** déclenché à l’ajout d’une infraction : si un pilote a **< 12** points, statut **suspendu**.
* **Sécurité JWT** sur l’ensemble des routes ; seul **ROLE_ADMIN** peut créer des infractions. 

S’aligne sur ta M_1 : **entités claires**, **services (ex: ProductManager→InfractionManager)**, **repositories avec requêtes custom**, **hiérarchie de rôles**, **endpoints documentés**.

---

## 1) Livrables attendus

* Code Symfony 6.4 prêt à lancer (Docker **optionnel**).
* **README** exhaustif (setup, env, commandes, rôles, jeux de données, scénarios de test).
* **Fixtures** : ≥ 3 écuries, ≥ 3 pilotes/écurie, moteurs, utilisateurs (admin & user), infractions exemples. 
* **Postman/Insomnia collection** + **.env.example**.
* **Jeu de tests** (PHPUnit + tests fonctionnels HTTP) + rapports (OK/KO).
* **Export SQL** (dump) et **migrations Doctrine** synchronisées.

---

## 2) Architecture & dépendances (calquée sur M_1)

* Bundles : `doctrine`, `security`, `maker`, `twig` (facultatif), `lexik/jwt-authentication-bundle`, `nelmio/cors-bundle` (si besoin), `symfony/validator`, `doctrine/doctrine-fixtures-bundle`.
* Dossiers clés :

  * `src/Entity` : `Team`, `Engine`, `Driver`, `Infraction`, `User`
  * `src/Repository` : requêtes custom (filtres)
  * `src/Service` : `InfractionManager` (logique métier + événement)
  * `src/Controller` : `AuthController`, `TeamController`, `DriverController`, `InfractionController`
  * `src/DataFixtures` : `UserFixtures`, `TeamFixtures`, `DriverFixtures`, `EngineFixtures`, `InfractionFixtures`
  * `src/Security` : config JWT
  * `tests/` : unitaires + fonctionnels
* **Hiérarchie rôles** : `ROLE_ADMIN > ROLE_MANAGER > ROLE_USER` (même style que M_1).

---

## 3) Modèle de données (Doctrine ↔ MySQL) — conforme à ta DB

### Entity `Team`

* `id` (int, PK), `name` (string, unique, <=120), timestamps.
* Relations : `oneToOne` `Engine` (owning side `Engine.team`, unique), `oneToMany` `Driver`.

### Entity `Engine`

* `id`, `brand` (string, <=120), `team` (`oneToOne`, unique, on delete cascade).

### Entity `Driver`

* `id`, `firstName` (<=100), `lastName` (<=100), `isStarter` (bool),
  `licensePoints` (int, default 12, **Assert: 0..12** + **DB CHECK**),
  `status` (enum: `active|suspendu`), `f1StartDate` (date), timestamps,
  `team` (`manyToOne`, index).
* **Règle** : suspension si `<12` après pénalité.

### Entity `Infraction`

* `id`, `occurredAt` (datetime), `raceName` (<=160), `description` (text),
  `type` (enum: `PENALTY_POINTS|FINE_EUR`), `amount` (decimal 12,2),
  **cible exclusive** : `driver` (`manyToOne`, nullable) XOR `team` (`manyToOne`, nullable).
* **CHECK** SQL (au moins logique applicative) :

  * exclusivité : exactement l’un des deux non-null,
  * cohérence type/montant (points ≥1 pour penalty, montant ≥0 pour amende).

### Entity `User` (LexikJWT)

* `id`, `email` (unique), `password` (hash), `roles` (json), `isActive` (bool), timestamps.

> Remarque : garde la **compatibilité MySQL** et les **migrations Doctrine** synchronisées.

---

## 4) Sécurité (JWT) & accès

* `POST /api/login` → obtention token (comme M_1).
* Accès : **toutes les routes /api/** requièrent JWT ;
  **création d’infraction** : **ROLE_ADMIN uniquement**. 

---

## 5) Endpoints & contrats (style M_1)

### Auth

* `POST /api/login`
  Body: `{ "username": "admin@example.com", "password": "pass" }`
  200: `{ "token": "…" }` | 401 bad creds

### Users (optionnel mini)

* `POST /api/user/create` (public) — créer user basique (ROLE_USER).

### Teams & Drivers

* `PATCH /api/team/{id}/drivers` — **modifier les pilotes d’une écurie** (ajout/retrait par IDs).

  * Auth: JWT (ROLE_MANAGER ou ADMIN).
  * Body: `{ "add": [driverIds], "remove": [driverIds] }`
  * 200: état courant des pilotes; 400 validations; 404 si id invalide. 

* `GET /api/team/{id}/drivers` — lister pilotes d’une écurie (filtre `isStarter?`).

* CRUD minimal `Team`, `Driver` (sécurisé JWT).

### Infractions

* `POST /api/infractions` — **infliger amende/pénalité à une écurie OU un pilote**.

  * Auth: **ROLE_ADMIN**.
  * Body (exemples) :

    * Pénalité points pilote :

      ```json
      { "type":"PENALTY_POINTS", "amount": 3, "driverId": 12, "raceName":"GP Bahrain", "occurredAt":"2025-03-08T14:00:00Z", "description":"Track limits" }
      ```
    * Amende écurie :

      ```json
      { "type":"FINE_EUR", "amount": 50000.00, "teamId": 3, "raceName":"GP Monaco", "occurredAt":"2025-05-25T16:10:00Z", "description":"Unsafe release" }
      ```
  * 201: ressource + effet métier (points mis à jour + éventuelle suspension).
  * 400: règles d’exclusivité/validations; 403: rôle insuffisant; 404: cible inconnue. 

* `GET /api/infractions` — **listing + filtres** `?teamId=&driverId=&from=&to=`

  * Filtre par **écurie**, **pilote**, **date** (intervalle).
  * 200: liste paginée. 

---

## 6) Repositories (requêtes custom, esprit M_1)

* `InfractionRepository::search(teamId?, driverId?, from?, to?)`

  * jointures conditionnelles, tri `occurredAt DESC`.
* `DriverRepository::byTeam(teamId, isStarter?)`
* `DriverRepository::activeOrSuspended()`
* `TeamRepository::withEngine()`
* `StatsRepository` (optionnel) : amendes totales par écurie, points retirés par pilote.

---

## 7) Services & logique métier

* **`InfractionManager`**

  * `createPenaltyForDriver(dto)` : décrémente `licensePoints`, met `status='suspendu'` si `<12`, persiste l’infraction (transaction).
  * `createFineForTeam(dto)` : enregistre l’amende (transaction).
  * **Émet des logs** (info/warning) sans fuite de données sensibles.
* **Écouteur/Subscriber** (optionnel) sur `Infraction` pour tracer.

---

## 8) Validation & erreurs

* **Symfony Validator** : not blank, ranges (0..12), enums, dates ISO8601, XOR driver/team.
* **Schéma d’erreur JSON** : `{ "status": 400, "code": "VALIDATION_ERROR", "errors": { "field": ["msg"] } }`
* **HTTP codes** : 200/201, 204 delete, 400, 401, 403, 404, 409 (conflit), 422 (validation), 500.

---

## 9) Fixtures (exigé par le sujet)

* **3 écuries mini** (Ferrari, Mercedes, Red Bull), 1 moteur par écurie.
* **≥ 3 pilotes par écurie**, mélange titulaires/réserves, dates de début cohérentes, points=12.
* **Utilisateurs** :

  * admin: `admin@example.com` (ROLE_ADMIN)
  * user: `user@example.com` (ROLE_USER)
* Quelques infractions (pénalités & amendes) pour tester les filtres. 

---

## 10) Sécurité & config (style M_1)

* `security.yaml` : firewall `main` JWT, hiérarchie rôles, access_control :

  * `/api/login` & `/api/user/create` publics ;
  * `/api/infractions` **POST** → `ROLE_ADMIN`;
  * reste des routes `/api/**` → JWT requis (ROLE_USER+).

---

## 11) Qualité, Git & CI (exigés implicitement)

* **Git** : au moins 2–3 commits par fonctionnalité (entités, fixtures, sécurité, endpoints, filtres). 
* **Linters** (`php-cs-fixer`), **PHPStan** niveau 6+.
* **Tests** :

  * unitaires (service `InfractionManager`, validations),
  * fonctionnels HTTP (login, 401/403, création infraction, filtres).
* **Coverage** sommaire accepté.

---

## 12) Script de démarrage & commandes

```bash
# Création projet
composer create-project symfony/skeleton f1_api && cd f1_api
composer require symfony/orm-pack symfony/validator symfony/security-bundle symfony/maker-bundle
composer require --dev doctrine/doctrine-fixtures-bundle phpunit/phpunit
composer require lexik/jwt-authentication-bundle
# (option) CORS
composer require nelmio/cors-bundle

# JWT keys
php bin/console lexik:jwt:generate-keypair

# Entités & migrations
php bin/console make:entity   # Team, Engine, Driver, Infraction, User
php bin/console make:migration
php bin/console doctrine:migrations:migrate

# Fixtures
php bin/console doctrine:fixtures:load -n

# Tests
php bin/phpunit
```

---

## 13) Plan “To-Do” exécutable (checklist agent)

1. **Bootstrap projet & dépendances** (OK si `symfony server:start` renvoie 200 sur `/`).
2. **Configurer MySQL & Doctrine** (`.env`, migrations OK).
3. **Créer entités** (schéma exact + contraintes), **repositories**.
4. **Implémenter sécurité JWT** (login public, rôles).
5. **Fixtures** : users/teams/engines/drivers/infractions.
6. **Controllers** :

   * `PATCH /api/team/{id}/drivers` (ajout/retrait) — tests 200/400/404.
   * `POST /api/infractions` (ROLE_ADMIN) — tests 201/400/403 + suspension auto.
   * `GET /api/infractions` (filtres) — tests filtres combinés, tri, pagination.
   * CRUD minimal `Team`/`Driver` (JWT).
7. **Service `InfractionManager`** (transactions, règles, logs).
8. **Validation** + **gestion d’erreurs** unifiée (JSON).
9. **Postman collection** : login → appel protégé → création infraction → filtres.
10. **Tests PHPUnit** (≥ 8 cas) & **rapport**.
11. **README** (setup complet, comptes demo, scénarios).
12. **Git** : push final avec historique clair.

---

## 14) Scénarios de tests (exemples concrets)

* **Auth** : KO wrong creds (401), OK admin → Bearer token.
* **Modifier pilotes d’une écurie** :

  * Ajout driver inexistant → 404.
  * Retrait driver d’une autre team → 409.
  * Ajout + retrait atomiques → 200, liste finale correcte.
* **Créer infraction (admin only)** :

  * `PENALTY_POINTS` avec `teamId` rempli → 400 (mauvaise cible).
  * `PENALTY_POINTS` points=3 → driver passe de 12→9, `status='suspendu'`.
  * `FINE_EUR` négative → 422.
* **Listing infractions** :

  * `?driverId=…` renvoie seulement celles du pilote.
  * `?teamId=…&from=2025-03-01&to=2025-03-31`.
  * Pagination cohérente & tri `occurredAt DESC`.
* **Sécurité** :

  * `POST /api/infractions` avec ROLE_USER → 403.
  * Toute route `/api/**` sans token → 401.

---

## 15) Définition de Fini (DoD)

* Toutes les routes répondent en **JSON**, CODES HTTP corrects, **logs** pour debug.
* **JWT** opérationnel ; **rôle ADMIN** requis pour créer des infractions.
* **Règles métier** (XOR cible, suspension <12) garanties par validations + service.
* **Filtres** par écurie/pilote/date fonctionnent & testés.
* **Fixtures conformes au sujet** (≥3 écuries, ≥3 pilotes/écurie). 
* **README**, **tests** et **collection** fournis.
* **Schéma MySQL** = migrations Doctrine à jour (zéro diff).

---

### Notes d’alignement M_1 → TP F1

* **Structure & sécurité** : identique à M_1 (JWT, hiérarchie rôles, endpoints publics pour login/register).
* **Services/Repos** : transposition `ProductManager` → `InfractionManager`; requêtes custom comme les `ProductRepository::*` pour les filtres.
* **Docs & exemples** : mêmes conventions de réponses/erreurs/cURL que M_1, adaptés au domaine F1.