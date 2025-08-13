# Ina Zaoui

Pour se connecter avec le compte de Ina, il faut utiliser les identifiants suivants:

- identifiant : `ina`
- mot de passe : `password`

Vous trouverez dans le fichier `backup.zip` un dump SQL anonymisé de la base de données et toutes les images qui se trouvaient dans le dossier `public/uploads`.
Faudrait peut être trouver une meilleure solution car le fichier est très gros, il fait plus de 1Go.

Lancer docker desktop puis taper cette commande pour démarrer l'environnement docker

```bash
docker-compose up
```

Le projet se lance à l'adresse: 127.0.0.1:8080
La base de données se lance l'adresse: 127.0.0.1:8899

Executer cette commande pour pouvoir utiliser les commandes de symfony et composer dans l'environnement docker

```bash
docker-compose exec php /bin/bash
```
