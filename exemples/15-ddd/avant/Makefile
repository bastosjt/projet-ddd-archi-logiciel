.PHONY: start stop restart install db-init db-reset fixtures consume bash logs build test

# ─── Docker ───────────────────────────────────────────────────────────────────

start:
	docker compose up -d
	@echo ""
	@echo "  App      → http://localhost:8089"
	@echo "  Mailpit  → http://localhost:8025"
	@echo ""

stop:
	docker compose down

restart: stop start

build:
	docker compose build

logs:
	docker compose logs -f app worker

# ─── Application ──────────────────────────────────────────────────────────────

install:
	docker compose exec app composer install

bash:
	docker compose exec app bash

# ─── Base de données ──────────────────────────────────────────────────────────

db-init:
	docker compose exec app php bin/console doctrine:schema:create
	docker compose exec app php bin/console doctrine:migrations:version --no-interaction 2>/dev/null || true
	$(MAKE) fixtures

db-reset:
	docker compose exec app php bin/console doctrine:schema:drop --force
	$(MAKE) db-init

fixtures:
	docker compose exec app php bin/console doctrine:fixtures:load --no-interaction

# ─── Messenger ────────────────────────────────────────────────────────────────

consume:
	docker compose exec app php bin/console messenger:consume async -vv

# ─── Tests ────────────────────────────────────────────────────────────────────

test:
	docker compose exec app php bin/phpunit --colors=always

# ─── Setup complet (premier lancement) ───────────────────────────────────────

setup: build start
	@echo "En attente que MySQL soit prêt..."
	@sleep 10
	$(MAKE) install
	$(MAKE) db-init
	@echo ""
	@echo "  Projet prêt ! Ouvrez http://localhost:8089"
	@echo ""
