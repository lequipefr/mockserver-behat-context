# Mockserver context L'Équipe

Extension du MockServerContext générique pour ajouter la logique L'Équipe.

## Install

``` bash
composer require --dev lequipe/mockserver-behat-context
```

Then add a context in your `behat.yml`, with the url to your local MockServer instance:

``` yml
default:
    suites:
        default:
            contexts:

                # Add this:
                - Lequipe\MockServer\Lequipe\LequipeMockServerContext:
                    mockServer: 'http://mockserver:1080'
```

Pour mocker les différentes url vers les autres MS, modifier par exemple :

`.env.test`:

```
MS_AUTH_BASE_URI=http://mockserver:1080/ms-auth/
MS_SHELL_BASE_URI=http://mockserver:1080/ms-shell/
MS_SEARCH_BASE_URI=http://mockserver:1080/ms-search/
```

:warning: Trailing slash: Il faut normalement un slash de fin après le préfix si on suit la RFC 3986 (exemple sur [la doc de Symfony](https://symfony.com/doc/current/reference/configuration/framework.html#base-uri)), mais dans certains MS, il faudra changer la config afin de retirer le slash de début ajouté en dur, et aussi dans les dépendances (bundles...).

Ensuite, mocker les appels MS avec les phrases Gherkins :

```
Given the MS "ms-search" "GET" "/api/chrono-item-documents" will return the json from file "ms-search.json"
```

(*le chemin du fichier est relatif au fichier `.feature`)

ou alors :

```
Given the MS "ms-user" "GET" "/api/fegtch/1" will return the json:
"""
{
    "__type": "user_profile",
    "...": "..."
}
"""
```
