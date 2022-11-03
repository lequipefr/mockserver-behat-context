# MockServer Behat context

Behat context for [MockServer](https://www.mock-server.com/).

## Roadmap

- Handle all cases in the "Client Code Examples" section in <https://mock-server.com/mock_server/mockserver_clients.html>
- Remove the Lequipe custom context `src/Lequipe/`
- Move and publish on github as an open source library
- Reference this PHP client in Mockserver library, and add examples in <https://mock-server.com/mock_server/mockserver_clients.html>
- Add "Composer" in "Where" menu <https://mock-server.com/where/maven_central.html>

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
                - Lequipe\MockServer\Behat\MockServerContext:
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

## Usage

### PHP client

You can use [MockServerClient](./src/MockServerClient.php) as a simple client,
and send your expectations as raw arrays, as defined in
[mockserver swagger api](https://app.swaggerhub.com/apis/jamesdbloom/mock-server-openapi/5.12.x#/expectation/put_expectation):

``` php
<?php

use Lequipe\MockServer\Client\MockServerClient;

$client = new MockServerClient('http://127.0.0.1:1080');

$client->expectation([
    'httpRequest' => [
        'method' => 'GET',
        'path' => '/users/1',
    ],
    'httpResponse' => [
        'body' => [
            [
                'id' => 1,
                'name' => 'Zidane',
            ],
        ],
    ],
]);
```

### Builder

Instead of raw arrays, you can use the [expectation builder](./src/Expectation/ExpectationBuilder.php):

``` php
<?php

use Lequipe\MockServer\MockServerClient;
use Lequipe\MockServer\Expectation\ExpectationBuilder;

$client = new MockServerClient('http://127.0.0.1:1080');

$builder = new ExpectationBuilder();

$builder->expectedRequest()
    ->method('GET')
    ->path('/users/1')
;

$builder->mockedRespone()
    ->bodyJson([
        [
            'id' => 1,
            'name' => 'Zidane',
        ],
    ])
;

$client->expectation($builder->toArray());
```

See other examples in [unit tests](./tests/ExpectationBuilderTest.php).

### Behat context

This library provide a behat context, for example here is some phrases you can use:

``` cucumber
Feature: My feature

    Scenario: My scenario

        Given I will receive this json payload:
            """
            {
                "name": "Zidane"
            }
            """
        Given I will receive the header "Content-Type" "application/json"
        Given the request "GET" "/users/1" will return the json:
            """
            [
                {
                    "id": 1,
                    "name": "Zidane edited"
                }
            ]
            """

    Scenario: Other scenario

        Given the request "GET" "/users/1" will return the json from file "stubs/payload.json"

```

Check **all available phrases and examples** in [features/expectations.feature](./features/expectations.feature).

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

### Multiple services

If you have multiple apis or microservices
and want to make sure a request is sent to the expected service,
you can use one of these following trick:

#### Custom path prefix

`.env`:
```
USERS_API=https://users-api.local
OTHER_API=https://other-api.com
```

`.env.test`:
```
USERS_API=http://127.0.0.1:1080/users-api/
OTHER_API=http://127.0.0.1:1080/other-api/
```

This way, when running behat tests,
your application will hit mockserver in any case,
but with a different path prefix.

Then, your tests will looks like:

``` cucumber
Given the request "GET" "/users-api/users/1" will return the json: ...
Given the request "GET" "/other-api/v1/something" will return the json: ...
```

#### Custom domain name

One drawback of the solution above is that
it can be difficult to concatenate a base host with a relative path
if you didn't handled this case since the beginning,
and always assumed that you hit your api at the root.

So in this case you can use different hosts name:

`.env`:
```
USERS_API=https://users-api.local
OTHER_API=https://other-api.com
```

`.env.test`:
```
USERS_API=https://users-api.mockserver:1080
OTHER_API=https://other-api.mockserver:1080
```

If you do the necessary in your hosts name resolution to make
any `*.mockserver` requests will hit your localhost,
your mockserver instance will receive all requests with a different hostname
that you can match with the `httpRequest.headers[name = 'Host']` parameter in expectation.

## Develop

Running tests:

``` bash
vendor/bin/phpunit
vendor/bin/behat
```

## License

This library is under [MIT license](LICENSE).
