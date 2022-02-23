# Projet 8 - Améliorez une application existante de ToDo & Co

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/22463e313b6e42dcaf4462978e99abe1)](https://www.codacy.com/gh/baeteromain/ToDoList/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=baeteromain/ToDoList&amp;utm_campaign=Badge_Grade)
[![Maintainability](https://api.codeclimate.com/v1/badges/0f248a38aedc1d581e3f/maintainability)](https://codeclimate.com/github/baeteromain/ToDoList/maintainability)
## Installation :
Telechargez directement le projet ou effectuez un git clone via la commande suite :

https://github.com/baeteromain/ToDoList.git

En suivant, effectuez un ``composer install`` à la racine du projet permettant d'installer les dépendances utilisées dans ce projet.

## Base de données :
### Configuration
Modifiez le fichier ```.env``` situé à la racine du projet avec vos informations spécifiques à votre base de données, voir l'exemple ci-dessous :

```
DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7
```

Si vous avez le CLI symfony vous pouvez effectuer les commandes suivantes :

Création de la base de donnée via la commande suivante :

```symfony console doctrine:database:create```

Lancement de la migration via la commande suivante :

```symfony console doctrine:migrations:migrate```

Ajout des fixtures en base de données permettant d'avoir un échantillon de données :

```symfony console doctrine:fixtures:load```

*Note : Si vous n'avez pas le client symfony, remplacez ```symfony console``` par ```php bin/console``` ( ex : ```php bin/console doctrine:database:create```)*

Lancez la commande ```symfony serve``` *(ou ```php bin/console server:run```, si vous n'avez pas le CLI symfony)

Il en vous reste plus qu'à vous rendre à l'adresse de votre serveur web ( ex : localhost:8000 )

## Tests

Pour lancer les tests, rendez-vous à la racine de votre projet et entrez la commande : 
```php ./vendor/bin/phpunit```

Vous pouvez trouver la documentation à l'adresse suivante :
https://symfony.com/doc/current/testing.html

Pour l'ajout de nouveaux tests, merci de les placer dans le dossier ``./tests``

## Contribution

### Guide

1. [Forkez](https://help.github.com/articles/fork-a-repo/) le repo.
1. Fait un [Checkout](https://git-scm.com/docs/git-checkout) sur la branche `master`.
1. Suivez le fichier [README.md](https://github.com/coco2053/To-Do-And-Co/blob/master/README.md) pour installer le projet.
1. Créez une nouvelle branche pour la fonctionnalité et positionnez vous sur cette branche.
1. Codez la nouvelle fonctionnalité ou bugfix.
1. Ajoutez vos tests unitaires et fonctionnels.
1. Commitez et pushez votre code.

### Ouvrir une Pull Request

1. Soumettez votre Pull Request
1. Verifiez le resultat de l'analyse Codacy
1. Attendez que le develeppeur en chef merge la Pull Request

### Standards à respecter

- [PSR-1: Basic Coding Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md)
- [PSR-2: Coding Style Guide](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)
- [PSR-4: Autoloading Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md)
- [Symfony Coding Standards](https://symfony.com/doc/current/contributing/code/standards.html)

Merci pour votre contribution ;-)

## Contexte :

Vous venez d’intégrer une startup dont le cœur de métier est une application permettant de gérer ses tâches quotidiennes. L’entreprise vient tout juste d’être montée, et l’application a dû être développée à toute vitesse pour permettre de montrer à de potentiels investisseurs que le concept est viable (on parle de Minimum Viable Product ou MVP).

Le choix du développeur précédent a été d’utiliser le framework PHP Symfony, un framework que vous commencez à bien connaître !

Bonne nouvelle ! ToDo & Co a enfin réussi à lever des fonds pour permettre le développement de l’entreprise et surtout de l’application.

Votre rôle ici est donc d’améliorer la qualité de l’application. La qualité est un concept qui englobe bon nombre de sujets : on parle souvent de qualité de code, mais il y a également la qualité perçue par l’utilisateur de l’application ou encore la qualité perçue par les collaborateurs de l’entreprise, et enfin la qualité que vous percevez lorsqu’il vous faut travailler sur le projet.

Ainsi, pour ce dernier projet de spécialisation, vous êtes dans la peau d’un développeur expérimenté en charge des tâches suivantes :

- l’implémentation de nouvelles fonctionnalités ;
- la correction de quelques anomalies ;
- et l’implémentation de tests automatisés.

Il vous est également demandé d’analyser le projet grâce à des outils vous permettant d’avoir une vision d’ensemble de la qualité du code et des différents axes de performance de l’application.

Il ne vous est pas demandé de corriger les points remontés par l’audit de qualité de code et de performance. Cela dit, si le temps vous le permet, ToDo & Co sera ravi que vous réduisiez la dette technique de cette application.
## Besoin client

### Corrections d'anomalies
#### Une tâche doit être attachée à un utilisateur
Actuellement, lorsqu’une tâche est créée, elle n’est pas rattachée à un utilisateur. Il vous est demandé d’apporter les corrections nécessaires afin qu’automatiquement, à la sauvegarde de la tâche, l’utilisateur authentifié soit rattaché à la tâche nouvellement créée.

Lors de la modification de la tâche, l’auteur ne peut pas être modifié.

Pour les tâches déjà créées, il faut qu’elles soient rattachées à un utilisateur “anonyme”.

#### Choisir un rôle pour un utilisateur
Lors de la création d’un utilisateur, il doit être possible de choisir un rôle pour celui-ci. Les rôles listés sont les suivants :

rôle utilisateur (ROLE_USER) ;
rôle administrateur (ROLE_ADMIN).
Lors de la modification d’un utilisateur, il est également possible de changer le rôle d’un utilisateur.

### Implémentation de nouvelles fonctionnalités
#### Autorisation
Seuls les utilisateurs ayant le rôle administrateur (ROLE_ADMIN) doivent pouvoir accéder aux pages de gestion des utilisateurs.

Les tâches ne peuvent être supprimées que par les utilisateurs ayant créé les tâches en question.

Les tâches rattachées à l’utilisateur “anonyme” peuvent être supprimées uniquement par les utilisateurs ayant le rôle administrateur (ROLE_ADMIN).

### Implémentation de tests automatisés
Il vous est demandé d’implémenter les tests automatisés (tests unitaires et fonctionnels) nécessaires pour assurer que le fonctionnement de l’application est bien en adéquation avec les demandes.

Ces tests doivent être implémentés avec PHPUnit ; vous pouvez aussi utiliser Behat pour la partie fonctionnelle.

Vous prévoirez des données de tests afin de pouvoir prouver le fonctionnement dans les cas explicités dans ce document.

Il vous est demandé de fournir un rapport de couverture de code au terme du projet. Il faut que le taux de couverture soit supérieur à 70 %.

### Documentation technique
Il vous est demandé de produire une documentation expliquant comment l’implémentation de l'authentification a été faite. Cette documentation se destine aux prochains développeurs juniors qui rejoindront l’équipe dans quelques semaines. Dans cette documentation, il doit être possible pour un débutant avec le framework Symfony de :

- comprendre quel(s) fichier(s) il faut modifier et pourquoi ;
- comment s’opère l’authentification ;
- et où sont stockés les utilisateurs.

S’il vous semble important de mentionner d’autres informations , n’hésitez pas à le faire.

Par ailleurs, vous ouvrez la marche en matière de collaboration à plusieurs sur ce projet. Il vous est également demandé de produire un document expliquant comment devront procéder tous les développeurs souhaitant apporter des modifications au projet.

Ce document devra aussi détailler le processus de qualité à utiliser ainsi que les règles à respecter.

### Audit de qualité du code & performance de l'application
Les fondateurs souhaitent pérenniser le développement de l’application. Cela dit, ils souhaitent dans un premier temps faire un état des lieux de la dette technique de l’application.

Au terme de votre travail effectué sur l’application, il vous est demandé de produire un audit de code sur les deux axes suivants : la qualité de code et la performance.

Bien évidemment, il vous est fortement conseillé d’utiliser des outils vous permettant d’avoir des métriques pour appuyer vos propos.

Concernant l’audit de performance, l’usage de Blackfire est obligatoire. Ce dernier vous permettra de produire des analyses précises et adaptées aux évolutions futures du projet.