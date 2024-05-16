<?php
/**
 * User: joao
 * Date: 07/05/24
 */

namespace marqu3s\octadeskApi;

/**
 * Class LoginApi
 *
 * This is only needed when querying the API v0.0.1 !
 * DO NOT USE THIS IF QUERYING API v1.0.0.
 *
 * @see https://api-docs.octadesk.services/docs/
 */
class LoginApi extends OctadeskApi
{
    const BASE_URL = 'https://api.octadesk.services';
    const TOKEN_EXPIRATION_MINUTES = 3;

    private $subdomain;
    private $username;

    /**
     * The function is a PHP constructor that initializes properties with provided values.
     *
     * @param string $subdomain The `baseUrl` of the API.
     * @param string $username The `apiKey` for authentication.
     */
    public function __construct($subdomain, $username)
    {
        parent::__construct(self::BASE_URL, null, $username);

        $this->subdomain = $subdomain;
        $this->username = $username;
    }

    /**
     * Get an access token to be used to query the api.
     * Put this token in the Authorization header like this:
     * `Authorization: Bearer <token>`.
     *
     * @param string $apiToken
     * @param string $userEmail
     * @param bool $returnTokenOnly if true, returns only the token, otherwise returns the whole response.
     *
     * @return array
     */
    public function getAccessToken($apiToken, $returnTokenOnly = true)
    {
        $this->setEndpoint('/login/apiToken')->setPost();

        $this->headers['apiToken'] = $apiToken;
        $this->headers['subdomain'] = $this->subdomain;
        $this->headers['username'] = $this->username;

        $response = $this->queryApi();

        if ($returnTokenOnly) {
            $body = json_decode($response->getBody()->getContents());
            return $body->token;
        } else {
            return $response;
        }
    }

    /**
     * Validates a subdomain.
     *
     * @param string $accessToken The token obtained from `getAccessToken()`.
     *
     * @return bool true if the subdomain is valid, false otherwise.
     */
    public function validateSubDomain($accessToken)
    {
        $this->setEndpoint('/validate')->setGet();

        $this->headers['Authorization'] = 'Bearer ' . $accessToken;

        $this->filters[] = [
            'property' => 'subdomain',
            'operator' => OctadeskApi::FILTER_OPERATOR_EQ,
            'value' => $this->subdomain,
        ];

        $response = $this->queryApi();

        return $response->getStatusCode() === 200;
    }
}
