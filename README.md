# MockServer Behat context

Behat context for [MockServer](https://www.mock-server.com/).

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
                - Lequipe\MockServer\MockServerContext:
                    mockServer: 'http://127.0.0.1:1080'
```

Now, in your behat tests, you should be able to mock the webservices your project depends to, with:

```
Given the request "GET" "/user/1" will return the json:
    """
    {
        "id": 1,
        "name": "Zidane"
    }
    """
```

## Configuration example

You may want to configure your project so that in test environment, external API are replaced by MockServer.

For example, you may have this kind of configuration:

`.env`: points to real api

```
USERS_API=https://users-api.local
```

`.env.test`: points to MockServer instance

```
USERS_API=http://127.0.0.1:1080
```

## License

This library is under [MIT license](LICENSE).
