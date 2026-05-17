# Exercice — Domain Driven Design

## Contexte

Vous avez devant vous une petite boutique en ligne Symfony **volontairement mal conçue**.
L'application fonctionne, mais elle ne respecte aucun principe DDD.

Votre mission : **la rearchitecturer** en appliquant le Domain Driven Design.

---

## L'application (dossier `avant/`)

Une boutique e-commerce avec **3 use cases** :

| # | Use case | Route |
|---|----------|-------|
| 1 | **Passer une commande** | `POST /commandes/passer` |
| 2 | **Annuler une commande** | `POST /commandes/{id}/annuler` |
| 3 | **Voir ses commandes** | `GET /commandes?email=...` |

**Règles métier à respecter :**
- On ne peut commander qu'un produit avec un stock suffisant.
- Annuler une commande restitue le stock des produits.
- On ne peut annuler qu'une commande au statut `EN_ATTENTE`.

---

## Lancer l'application `avant/`

### Avec Docker (recommandé)

```bash
cd avant/

# Premier lancement : build + démarrage + BDD + fixtures
make setup

# Accès :
#   App     → http://localhost:8080
#   Mailpit → http://localhost:8025  (emails interceptés en dev)
```

Commandes utiles :

| Commande | Description |
|----------|-------------|
| `make start` | Démarrer les conteneurs |
| `make stop` | Arrêter les conteneurs |
| `make logs` | Suivre les logs app + worker |
| `make bash` | Ouvrir un shell dans le conteneur |
| `make db-reset` | Remettre la BDD à zéro |
| `make consume` | Lancer le consumer Messenger manuellement |

### Sans Docker (SQLite)

```bash
cd avant/
composer install

# Dans .env, activer la ligne SQLite et commenter MySQL
# DATABASE_URL="sqlite:///%kernel.project_dir%/var/boutique.db"

php bin/console doctrine:schema:create
php bin/console doctrine:fixtures:load

# Lancer le serveur
symfony server:start   # ou : php -S localhost:8000 -t public/

# Dans un second terminal : consumer pour l'asynchrone
php bin/console messenger:consume async -vv
```

---

## Architecture de l'app `avant/`

```
Controller ──dispatch──► MessageBus ──(async)──► messenger_messages (BDD)
                                                        │
                                                   Worker PHP
                                                        │
                                              CommandePasseeMessageHandler
                                                        │
                                               MailerInterface ──► Mailpit
```

Quand une commande est passée :
1. Le controller crée la commande en BDD et `dispatch()` un `CommandePasseeMessage`
2. Le message est sérialisé et stocké dans la table `messenger_messages` (transport Doctrine)
3. Le **worker** (`php bin/console messenger:consume async`) le dépile et envoie l'email
4. L'email est intercepté par **Mailpit** (visible sur http://localhost:8025)

---

## Ce qui ne va pas dans `avant/` (à identifier en cours)

- Toute la logique métier est dans `CommandeController`
- Les règles métier (stock, annulation) sont éparpillées entre les méthodes
- Le contrôleur accède directement à l'`EntityManager` → couplage fort à Doctrine
- Les entités sont de simples sacs de données sans comportement
- Pas de protection contre des états incohérents (ex : stock négatif possible en concurrence)
- Le code est impossible à tester unitairement sans base de données
- `CommandePasseeMessage` transporte un ID et un email brut — pas un vrai Domain Event
- Le `MessageHandler` va lui-même chercher la commande en BDD (pas de projection, pas de Read Model)

---

## Votre mission : recoder en DDD (dossier `apres/`)

Structurez le projet avec les trois couches DDD :

```
src/
├── Domain/                  # Cœur métier — 0 dépendance externe
│   ├── Model/
│   │   ├── Commande.php         # Aggregate Root avec comportement
│   │   ├── LigneCommande.php
│   │   ├── Produit.php
│   │   ├── StatutCommande.php   # enum PHP (Value Object)
│   │   └── Email.php            # Value Object immutable et auto-validé
│   ├── Event/
│   │   ├── CommandePassee.php   # Domain Event riche
│   │   └── CommandeAnnulee.php
│   ├── Repository/              # Interfaces définies par le Domain
│   │   ├── CommandeRepositoryInterface.php
│   │   └── ProduitRepositoryInterface.php
│   └── Exception/
│       ├── StockInsuffisantException.php
│       └── AnnulationImpossibleException.php
│
├── Application/             # Orchestration : use cases + entrée HTTP
│   ├── Controller/          # Mince, délègue aux Handlers
│   │   └── CommandeController.php
│   ├── Command/
│   │   ├── PasserCommande/
│   │   │   ├── PasserCommandeCommand.php   # DTO
│   │   │   └── PasserCommandeHandler.php
│   │   └── AnnulerCommande/
│   │       ├── AnnulerCommandeCommand.php
│   │       └── AnnulerCommandeHandler.php
│   └── Query/
│       └── MesCommandes/
│           ├── MesCommandesQuery.php
│           └── MesCommandesHandler.php
│
└── Infrastructure/          # Adaptateurs techniques (framework, BDD, email…)
    ├── Persistence/         # Implémentations Doctrine des interfaces Domain
    │   ├── DoctrineCommandeRepository.php
    │   └── DoctrineProduitRepository.php
    └── Messaging/           # Réaction aux Domain Events
        └── SendConfirmationEmailOnCommandePassee.php
```

> **Débat Controllers** : en hexagonale stricte (Cockburn) les controllers sont des *adaptateurs primaires* → Infrastructure. En pratique DDD, les placer dans Application est courant car ils orchestrent les use cases sans manipuler de techno externe directement. Les deux conventions sont valides.

### Fichiers de config à modifier pour le chargement des classes

**`config/services.yaml`** — remplacer le bloc `App\:` monolithique par 3 blocs séparés et binder les interfaces :
```yaml
App\Application\:
    resource: '../src/Application/'

App\Infrastructure\:
    resource: '../src/Infrastructure/'

# Binding interface Domain → implémentation Infra
App\Domain\Repository\CommandeRepositoryInterface: '@App\Infrastructure\Persistence\DoctrineCommandeRepository'
App\Domain\Repository\ProduitRepositoryInterface:  '@App\Infrastructure\Persistence\DoctrineProduitRepository'
```
> Le Domain n'est **pas** déclaré comme service Symfony : ses classes sont de purs objets PHP instanciés par les Handlers ou les Repositories.

**`config/routes.yaml`** — pointer vers `Application/Controller/` :
```yaml
controllers:
    resource:
        path: ../src/Application/Controller/
        namespace: App\Application\Controller
    type: attribute
```

---

### Points clés attendus

1. **Les entités du domaine ont du comportement** :
   - `Commande::annuler()` lève une exception si le statut ne le permet pas
   - `Produit::decrementerStock()` lève une exception si stock insuffisant

2. **Les Value Objects** sont immutables et se valident eux-mêmes :
   - `Email` rejette un email invalide à la construction
   - `StatutCommande` est un `enum` PHP

3. **Les Repository Interfaces** sont dans `Domain/`, les implémentations dans `Infrastructure/`

4. **Les Handlers** (`Application/`) ne connaissent que le domaine, jamais Doctrine directement

5. **Le contrôleur** ne contient que : récupération de la requête HTTP → appel du Handler → réponse
