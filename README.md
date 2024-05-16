# octadesk-api

PHP client for Octadesk API.

This client supports Octadesk API v0.0.1 and is prepared to support the v1.0.0.

_NOTE_: The 1.0.0 version is under development by the time this was written.

Package GuzzleHttp is used to communicate with the API.

## Usage

To query the API v0.0.1 use `OctadeskApi::API_V0` as the parameter to the client's constructor `$version` parameter.

Use `OctadeskApi::API_V1` to query the API v1.0.0.

For v0.0.1 you must get an access token by calling LoginApi and then use this access token in all requests. Pass it to the `$apiToken` parameter in the client's constructor. See the [octadesk documentation](https://api-docs.octadesk.services/docs). The api

For v1.0.0 you must use a token generated at Octadesk API page under admin settings.

### Login

To get an access token for a user, query the LoginApi. The `$apiToken` must be generated at Octadesk UI in the tickets settings, integration code.

```php
$subdomain = "mysubdomainatoctadesk";
$userEmail = "user@domain.com";
$apiToken = "octa.yyyyyyyyyyyy.zzzzzzzzzzzz";

$api = new LoginApi($subdomain, $userEmail);
$accessToken = $api->getAccessToken($apiToken);
```

### Tickets search

```php
$apiUrl = "https://api.octadesk.services";
$userEmail = "user@domain.com";

# The token obtained using LoginApi (to use with v0.0.1). See above.
# Or token generated at Octadesk API page under admin settings (to use with v1.0.0)
$accessToken = "OCTADESK.xxxxxx.xxxxxxx.xxxxxxx";

$api = new TicketsApi($apiUrl, $accessToken, $userEmail, "application/json", OctadeskApi::API_V0);

# Get tickets requested by someone.
$filters = [
  [
    "property" => "requester.id",
    "operator" => OctadeskApi::FILTER_OPERATOR_EQ,
    "value" => $personOctadeskUuid,
  ],
];

$sort = ["property" => "number", "direction" => "desc"];
$page = 1;
$ticketsPerPage = 10;

$response = $api->search($filters, $sort, $page, $ticketsPerPage);
```
