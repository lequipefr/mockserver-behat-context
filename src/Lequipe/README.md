# Mockserver context L'Équipe

Context Behat pour [MockServer](https://www.mock-server.com/).

Cette page est spécifique à l'installation et l'usage
de ce contexte sur les projets de L'Équipe.

## Install

Ajouter dans `composer.json` le repo privé :

``` json
    "repositories": [

        {
            "type": "vcs",
            "url": "git@gitlab.lequipe.net:lequipe/back/modules/packages/mockserver-behat-context.git"
        },

        "...",
    ]
```

Puis lancer :

``` bash
composer require --dev lequipe/mockserver-behat-context
```

Ensuite ajouter dans `behat.yml.dist` :

``` yml
default:
    suites:
        default:
            contexts:

                # Add this:
                - Lequipe\MockServer\Lequipe\LequipeMockServerContext:
                    mockServer: 'http://mockserver:1080'
```

Le server `http://mockserver:1080` correspond au container installé dans `docker-dev-stack`.

Modifier le `.env.test` du projet pour mocker les différentes url vers les autres MS :

`.env.test`:

```
MS_AUTH_BASE_URI=http://mockserver:1080/ms-auth/
MS_SHELL_BASE_URI=http://mockserver:1080/ms-shell/
MS_SEARCH_BASE_URI=http://mockserver:1080/ms-search/
```

:warning: **Trailing slash** : Il faut normalement un slash de fin après le préfix si on suit la RFC 3986 (exemple sur [la doc de Symfony](https://symfony.com/doc/current/reference/configuration/framework.html#base-uri)), mais dans certains MS, il faudra changer la config afin de retirer le slash de début ajouté en dur, et aussi dans les dépendances (bundles...).

## Utilisation

### Mocker un appel

Si votre MS appel par exemple le MS user pendant sont endpoint :

```
Given the MS "ms-user" "GET" "/api/fetch/1" will return the json:
"""
{
    "__type": "user_profile",
    "...": "..."
}
"""
```

### Mocker un appel avec un flux venant d'un fichier

```
Given the MS "ms-search" "GET" "/api/chrono-item-documents" will return the json from file "ms-search.json"
```

**Notes** :
- le nom `ms-search` correspond au préfix ajouté dans le `.env.test`
- le chemin du fichier est relatif au fichier `.feature`

### Attendre un json précis dans la requête

```
Given I will receive this json payload:
"""
{"name": "Zidane edited"}
"""
And the request "PATCH" "/users/1" will return the json:
"""
[
  {
     "id": 1,
     "name": "Zidane edited"
  }
]
"""
```

### Attendre un header précis dans la requête

```
Given I will receive the header "Content-Type" "application/form-data"
And the MS "ms-user" "POST" "/api/account/19095/avatar" will return the json:
"""
{"avatar": "/api/avatars/19095"}
"""
```

### Ré-initialiser les mocks manuellement

**Notes** :
Les mocks sont ré-initialisés automatiquement avant le premier mock.
Cette phrase sera donc utile pour débugger, ou pour ré-initialiser les mocks au milieu du scénario si jamais.

```
Given I reset mocks
```
