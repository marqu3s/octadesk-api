<?php
/**
 * User: joao
 * Date: 07/05/24
 */

namespace marqu3s\octadeskApi;

/**
 * Class CustomFieldsApi
 *
 * @see https://api-docs.octadesk.services/docs/
 */
class CustomFieldsApi extends OctadeskApi
{
    const BASE_URL = 'https://api.octadesk.services';

    private $subdomain;
    private $username;

    /**
     * The function is a PHP constructor that initializes properties with provided values.
     *
     * @param string $subdomain The `baseUrl` of the API.
     * @param string $username The `apiKey` for authentication.
     */
    public function __construct($accessToken, $subdomain, $username)
    {
        parent::__construct(self::BASE_URL, null, $username);

        $this->apiKey = $accessToken;
        $this->subdomain = $subdomain;
        $this->username = $username;
    }

    public function getFieldDetails($uuid)
    {
        $this->setEndpoint("/custom-fields/$uuid");
        $this->headers['AppSubDomain'] = $this->subdomain;

        $response = $this->queryApi();

        return $response;
    }
}
