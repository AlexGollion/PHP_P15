# Ina Zaoui

## Pré-requis

Installer docker desktop: https://www.docker.com/

Récupérer le projet

```bash
git clone https://github.com/AlexGollion/PHP_P15.git
cd PHP_P15
```

Créer un fichier newrelic.ini (en copiant newrelic.ini.example) et modifiant la clé par la votre que vous pouvez trouver ici https://newrelic.com/fr

## Installation

Lancer docker desktop puis taper cette commande pour créer et démarrer l'environnement docker

```bash
docker-compose up --build -d
```

Le projet se lance à l'adresse: 127.0.0.1:8080

Exécuter cette commande pour pouvoir exécuter des commandes dans le container php

```bash
docker-compose exec php /bin/bash
```

Ensuite exécuter ces commandes dans le container php afin de créer la base de donnée et générer les fixtures

```bash
bin/console doctrine:database:drop -f --if-exists
bin/console doctrine:database:create
bin/console doctrine:migrations:migrate
bin/console doctrine:fixtures:load --group=dev
```

## Utilisation

Pour se connecter avec le compte de Ina, il faut utiliser les identifiants suivants:

- identifiant : `Ina Zaoui`
- mot de passe : `password`

### Docker

Pour lancer l'environnement docker

```bash
docker-compose up -d
```

Pour arrêter l'environnement docker

```bash
docker-compose down
```

Exécuter cette commande pour pouvoir exécuter des commandes dans le container php

```bash
docker-compose exec php /bin/bash
```

## Tests

Avant de commencer les tests, dans le container php, charger les fixtures de test

```bash
bin/console doctrine:fixtures:load --env=test --group=test
```

Commande pour exécuter les tests

```bash
vendor/bin/phpunit
```

Commande pour exécuter un test spécifique

```bash
vendor/bin/phpunit --filter=nomDuTest
```

Commande pour avoir le test-coverage

```bash
vendor/bin/phpunit --coverage-html public/test-coverage
```
