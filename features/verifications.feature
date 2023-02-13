Feature: Verifications

    Verifications phrases are sent after test has been done,
    to assert that a request has actually been sent by the tested application.

    Scenario: Verify that a simple request has been sent

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

    Scenario: Verify that a request with query parameters has been sent.

        Given the request "GET" "/api/users?active=yes&gender=F&sport.id=1" should have been called exactly 1 times

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
            """
