# Behat phrases

## Expectations

### Simply return a given status code

``` cucumber
Given the request "PUT" "/users/1/flush" will return status code 204
```

Payload sent to mockserver endpoint `PUT /expectation`:

``` json
{
    "httpRequest": {
        "method": "PUT",
        "path": "/users/1/flush"
    },
    "httpResponse": {
        "statusCode": 204
    }
}
```

### Return a json response

``` cucumber
Given the request "GET" "/users/1" will return the json:
"""
[
    {
        "id": 1,
        "name": "Zidane edited"
    }
]
"""
```

Payload sent to mockserver endpoint `PUT /expectation`:

``` json
{
    "httpRequest": {
        "method": "GET",
        "path": "/users/1"
    },
    "httpResponse": {
        "body": [
            {
                "id": 1,
                "name": "Zidane edited"
            }
        ]
    }
}
```

### Return json response from file

The file path is relative to your current ".feature" file.

``` cucumber
Given the request "GET" "/users/1" will return the json from file "stubs/payload.json"
```

Payload sent to mockserver endpoint `PUT /expectation`:

``` json
{
    "httpRequest": {
        "method": "GET",
        "path": "/users/1"
    },
    "httpResponse": {
        "body": [
            {
                "id": 1,
                "name": "Zidane edited"
            }
        ]
    }
}
```

### Return raw body

``` cucumber
Given the request "GET" "/index" will return:
"""
<html><body><p>Hello</p></body></html>
"""
```

Payload sent to mockserver endpoint `PUT /expectation`:

``` json
{
    "httpRequest": {
        "method": "GET",
        "path": "/index"
    },
    "httpResponse": {
        "body": "<html><body><p>Hello</p></body></html>"
    }
}
```

### Return raw body with parameters and a cookie


The first phrase `i will ...` does not send the expectation.
It just prefill the expectation, and will be sent later
in the second phrase `the request ... will return`

``` cucumber
Given I will receive the cookie "session" "4930456C-C718-476F-971F-CB8E047AB349"
And the request "GET" "/view/cart?cartId=055CA455-1DF7-45BB-8535-4F83E7266092" will return:
"""
some_response_body
"""
```

Payload sent to mockserver endpoint `PUT /expectation`:

``` json
{
    "httpRequest": {
        "method": "GET",
        "path": "/view/cart",
        "queryStringParameters": [
            {
                "name": "cartId",
                "values": ["055CA455-1DF7-45BB-8535-4F83E7266092"]
            }
        ],
        "cookies": [
            {
                "name": "session",
                "value": "4930456C-C718-476F-971F-CB8E047AB349"
            }
        ]
    },
    "httpResponse": {
        "body": "some_response_body"
    }
}
```

### Return raw body from file

The file path is relative to your current ".feature" file.

``` cucumber
Given the request "GET" "/Football/article.html" will return body from file "stubs/article.html"
```

Payload sent to mockserver endpoint `PUT /expectation`:

``` json
{
    "httpRequest": {
        "method": "GET",
        "path": "/Football/article.html"
    },
    "httpResponse": {
        "body": {
            "string": "<html>\n    <head>\n        <meta charset=\"utf-8\" \/>\n        <title>« Je serai un jour sélectionneur des Bleus »<\/title>\n    <\/head>\n    <body>\n        <p>Body<\/p>\n    <\/body>\n<\/html>"
        }
    }
}
```

### Expect a given header in the request

``` cucumber
Given I will receive the header "Content-Type" "application/json"
And the request "GET" "/users/1" will return the json:
"""
[
    {
        "id": 1,
        "name": "Zidane edited"
    }
]
"""
```

Payload sent to mockserver endpoint `PUT /expectation`:

``` json
{
    "httpRequest": {
        "method": "GET",
        "path": "/users/1",
        "headers": [
            {
                "name": "Content-Type",
                "values": [
                    "application/json"
                ]
            }
        ]
    },
    "httpResponse": {
        "body": [
            {
                "id": 1,
                "name": "Zidane edited"
            }
        ]
    }
}
```

### Expect to receive a given json in the request

Not to mismatch with "I will receive this raw body"
which takes a plain text body instead, and won't interpret the json.

``` cucumber
Given I will receive this json payload:
"""
{
    "name": "Zidane"
}
"""
And the request "POST" "/users" will return the json:
"""
{
    "success": true,
    "message": "User created successfully"
}
"""
```

Payload sent to mockserver endpoint `PUT /expectation`:

``` json
{
    "httpRequest": {
        "method": "POST",
        "path": "/users",
        "body": {
            "name": "Zidane"
        }
    },
    "httpResponse": {
        "body": {
            "success": true,
            "message": "User created successfully"
        }
    }
}
```

### Expect to receive a given raw body in the request

``` cucumber
Given I will receive this raw body:
"""
<node>whatever</node>
"""
And the request "POST" "/nodes" will return the json:
"""
{
    "success": true,
    "message": "Node added"
}
"""
```

Payload sent to mockserver endpoint `PUT /expectation`:

``` json
{
    "httpRequest": {
        "method": "POST",
        "path": "/nodes",
        "body": {
            "string": "<node>whatever<\/node>"
        }
    },
    "httpResponse": {
        "body": {
            "success": true,
            "message": "Node added"
        }
    }
}
```

### Raw expectation

Sendind a expectation in the format directly defined by Mockserver
and described here: https://app.swaggerhub.com/apis/jamesdbloom/mock-server-openapi/5.12.x#/expectation/put_expectation

Useful to send a fully custom expectation that no behat phrases defined in this context can do.

``` cucumber
Given I expect this request:
"""
{
    "httpRequest": {
        "method": "get",
        "path": "/my/custom/path",
        "queryStringParameters": [
            {
                "name": "myParam",
                "values": [
                    "possibleValue",
                    "otherPossibleValue"
                ]
            }
        ],
        "body": {
            "expected_body": "ok"
        }
    },
    "httpResponse": {
        "statusCode": 200,
        "body": {
            "my_custom_body": "ok"
        }
    }
}
"""
```

Payload sent to mockserver endpoint `PUT /expectation`:

``` json
{
    "httpRequest": {
        "method": "get",
        "path": "/my/custom/path",
        "queryStringParameters": [
            {
                "name": "myParam",
                "values": [
                    "possibleValue",
                    "otherPossibleValue"
                ]
            }
        ],
        "body": {
            "expected_body": "ok"
        }
    },
    "httpResponse": {
        "statusCode": 200,
        "body": {
            "my_custom_body": "ok"
        }
    }
}
```

### Query parameters in the request path

``` cucumber
Given the request "GET" "/users/1?param=val&scopes[]=basics&scopes[]=optins&array[key]=val" will return the json:
"""
[
    {
        "id": 1,
        "name": "Zidane edited"
    }
]
"""
```

Payload sent to mockserver endpoint `PUT /expectation`:

``` json
{
    "httpRequest": {
        "method": "GET",
        "path": "/users/1",
        "queryStringParameters": [
            {
                "name": "param",
                "values": ["val"]
            },
            {
                "name": "scopes[0]",
                "values": ["basics"]
            },
            {
                "name": "scopes[1]",
                "values": ["optins"]
            },
            {
                "name": "array[key]",
                "values": ["val"]
            }
        ]
    },
    "httpResponse": {
        "body": [
            {
                "id": 1,
                "name": "Zidane edited"
            }
        ]
    }
}
```

### Query parameters with deeper array

``` cucumber
Given the request "GET" "/users/search?level0=a&level1[x]=b&level2[x][y]=c&level3[x][y][z]=d" will return the json:
"""
[
    {
        "id": 1,
        "name": "Zidane"
    }
]
"""
```

Payload sent to mockserver endpoint `PUT /expectation`:

``` json
{
    "httpRequest": {
        "method": "GET",
        "path": "/users/search",
        "queryStringParameters": [
            {
                "name": "level0",
                "values": ["a"]
            },
            {
                "name": "level1[x]",
                "values": ["b"]
            },
            {
                "name": "level2[x][y]",
                "values": ["c"]
            },
            {
                "name": "level3[x][y][z]",
                "values": ["d"]
            }
        ]
    },
    "httpResponse": {
        "body": [
            {
                "id": 1,
                "name": "Zidane"
            }
        ]
    }
}
```

### Query parameters with a dot

PHP convert dots to underscores if using parse_str(), which is not wanted with mockserver.

``` cucumber
Given the request "GET" "/users/1?sport.id=1" will return the json:
"""
[
    {
        "id": 1,
        "name": "Zidane"
    }
]
"""
```

Payload sent to mockserver endpoint `PUT /expectation`:

``` json
{
    "httpRequest": {
        "method": "GET",
        "path": "/users/1",
        "queryStringParameters": [
            {
                "name": "sport.id",
                "values": ["1"]
            }
        ]
    },
    "httpResponse": {
        "body": [
            {
                "id": 1,
                "name": "Zidane"
            }
        ]
    }
}
```

### Return a given status code

``` cucumber
Given the response status code will be 206
Given the request "GET" "/users" will return the json:
"""
[
    {
        "id": 1,
        "name": "Zidane"
    }
]
"""
```

Payload sent to mockserver endpoint `PUT /expectation`:

``` json
{
    "httpRequest": {
        "method": "GET",
        "path": "/users"
    },
    "httpResponse": {
        "statusCode": 206,
        "body": [
            {
                "id": 1,
                "name": "Zidane"
            }
        ]
    }
}
```

## Reset

### Reset all expectations.

Mockerver is already reset before all scenarios though.

``` cucumber
Given I reset mocks
```

Mockserver is reset.

## Verifications

Verifications phrases are sent after test has been done,
to assert that a request has actually been sent by the tested application.

### Verify that a simple request has been sent

``` cucumber
Given the request "GET" "/api/users" should have been called exactly 1 times
```

Payload sent to mockserver endpoint `PUT /verify`:

``` json
{
    "httpRequest": {
        "method": "GET",
        "path": "/api/users"
    },
    "times": {
        "atLeast": 1,
        "atMost": 1
    }
}
```

### Verify that a request with query parameters has been sent.

``` cucumber
Given the request "GET" "/api/users?active=yes&gender=F&sport.id=1" should have been called exactly 1 times
```

Payload sent to mockserver endpoint `PUT /verify`:

``` json
{
    "httpRequest": {
        "method": "GET",
        "path": "/api/users",
        "queryStringParameters": [
            {
                "name": "active",
                "values": ["yes"]
            },
            {
                "name": "gender",
                "values": ["F"]
            },
            {
                "name": "sport.id",
                "values": ["1"]
            }
        ]
    },
    "times": {
        "atLeast": 1,
        "atMost": 1
    }
}
```

