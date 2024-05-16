<?php
/**
 * User: joao
 * Date: 07/05/24
 */

namespace marqu3s\octadeskApi;

/**
 * Class AuthenticationApi
 *
 * @see https://developers.octadesk.com/reference/authentication
 */
class AuthenticationApi extends OctadeskApi
{
    /**
     * Check if the apiKey is valid.
     *
     * @return Psr\Http\Message\ResponseInterface
     * @see https://developers.octadesk.com/reference/checkapitoken
     */
    public function checkApiKey()
    {
        $this->setEndpoint('/auth/check')->setGet();

        $response = $this->queryApi();

        return $response;
    }
}
