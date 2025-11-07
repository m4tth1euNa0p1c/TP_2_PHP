.PHONY: tdb tfix tunit tfun tint tall test-setup help

help: ## Afficher l'aide
	@echo "Commandes disponibles:"
	@echo "  make test-setup  - Préparer la base de données de test"
	@echo "  make tdb         - Recréer la base de données de test"
	@echo "  make tfix        - Charger les fixtures de test"
	@echo "  make tunit       - Lancer les tests unitaires"
	@echo "  make tfun        - Lancer les tests fonctionnels"
	@echo "  make tint        - Lancer les tests d'intégration"
	@echo "  make tall        - Lancer tous les tests"

tdb: ## Recréer la base de données de test
	php bin/console doctrine:database:drop --if-exists --force --env=test
	php bin/console doctrine:database:create --env=test
	php bin/console doctrine:migrations:migrate -n --env=test

tfix: ## Charger les fixtures de test
	php bin/console doctrine:fixtures:load -n --env=test

test-setup: tdb tfix ## Préparer l'environnement de test complet

tunit: ## Lancer les tests unitaires
	php bin/phpunit --testsuite=unit --testdox

tfun: ## Lancer les tests fonctionnels
	php bin/phpunit --testsuite=functional --testdox

tint: ## Lancer les tests d'intégration
	php bin/phpunit --testsuite=integration --testdox

tall: test-setup ## Lancer tous les tests avec préparation
	php bin/phpunit --testdox

test: tall ## Alias pour tall
