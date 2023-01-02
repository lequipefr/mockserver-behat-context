Feature: I can send expectations on a mockserver instance.

    Scenario: Expectation that a request on a given path will return given json response.

        Given the request "GET" "/users/1" will return the json:
            """
            [
                {
                    "id": 1,
                    "name": "Zidane edited"
                }
            ]
            """
        Then mockserver should receive the following expectation only:
            """
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
            """


    Scenario: Expectation that a request on a given path will return given json response from file.
        The file path is relative to your current ".feature" file.

        Given the request "GET" "/users/1" will return the json from file "stubs/payload.json"

        Then mockserver should receive the following expectation only:
            """
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
            """


    Scenario: Expectation that a request on a given path will return given raw body from file.
        The file path is relative to your current ".feature" file.

        Given the request "GET" "/Football/article.html" will return body from file "stubs/article.html"

        Then mockserver should receive the following expectation only:
            """
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
            """


    Scenario: Expect a given header in the request.

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
        Then mockserver should receive the following expectation only:
            """
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
            """


    Scenario: Expect to receive a given json in the request.
        Not to mismatch with "I will receive this raw body"
        which takes a plain text body instead, and won't interpret the json.

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
        Then mockserver should receive the following expectation only:
            """
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
            """


    Scenario: Expect to receive a given raw body in the request.

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
        Then mockserver should receive the following expectation only:
            """
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
            """


    Scenario: Raw expectation.
        Sendind a expectation in the format directly defined by Mockserver
        and described here: https://app.swaggerhub.com/apis/jamesdbloom/mock-server-openapi/5.12.x#/expectation/put_expectation
        Useful to send a fully custom expectation that no behat phrases defined in this context can do.

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
        Then mockserver should receive the following expectation only:
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

    Scenario: I can pass query parameter in the request path
        Given the request "GET" "/users/1?param=val&scopes[]=basics&scopes[]=optins&array[key]=val" will return the json:
            """
            [
                {
                    "id": 1,
                    "name": "Zidane edited"
                }
            ]
            """
        Then mockserver should receive the following expectation only:
            """
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
            """

    Scenario: I can pass query parameter with deeper array
        Given the request "GET" "/users/search?level0=a&level1[x]=b&level2[x][y]=c&level3[x][y][z]=d" will return the json:
            """
            [
                {
                    "id": 1,
                    "name": "Zidane"
                }
            ]
            """
        Then mockserver should receive the following expectation only:
            """
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
            """

    Scenario: I can pass query parameters with a dot
        Given the request "GET" "/users/1?sport.id=1" will return the json:
            """
            [
                {
                    "id": 1,
                    "name": "Zidane"
                }
            ]
            """
        Then mockserver should receive the following expectation only:
            """
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
            """

    Scenario: I can return a given status code
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
        Then mockserver should receive the following expectation only:
            """
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
            """
