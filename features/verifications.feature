Feature: I can verify request have been received by mockserver.

    Scenario: I can verify that a simple request has been sent.

        Given the request "GET" "/api/users" should have been called exactly 1 times

        Then mockserver should receive the following verification only:
            """
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
            """

    Scenario: I can verify that a request with query parameters has been sent.

        Given the request "GET" "/api/users?active=yes&gender=F" should have been called exactly 1 times

        Then mockserver should receive the following verification only:
            """
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
                        }
                    ]
                },
                "times": {
                    "atLeast": 1,
                    "atMost": 1
                }
            }
            """
