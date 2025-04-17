<?php
/**
 * User: joao
 * Date: 07/05/24
 */

namespace marqu3s\octadeskApi;

use GuzzleHttp\Client;
use Psr\Http\Message\RequestInterface;

abstract class OctadeskApi
{
    const FILTER_OPERATOR_EQ = 'eq'; // equal
    const FILTER_OPERATOR_NE = 'ne'; // not equal
    const FILTER_OPERATOR_GT = 'gt'; // greater than
    const FILTER_OPERATOR_GE = 'ge'; // greater than or equals
    const FILTER_OPERATOR_LT = 'lt'; // less than
    const FILTER_OPERATOR_LE = 'le'; // less than or equals
    const FILTER_OPERATOR_IN = 'in'; // in
    const FILTER_OPERATOR_NIN = 'nin'; // not in

    const SORT_ASC = 'asc';
    const SORT_DESC = 'desc';

    const API_V0 = 'v0.0.1';
    const API_V1 = 'v1.0.0'; // unstable

    /** @var Client */
    protected $client;

    /** @var Psr\Http\Message\ResponseInterface */
    protected $response;

    /** @var string */
    public $apiVersion = self::API_V0;

    /** @var string */
    public $apiKey;

    /** @var string */
    public $baseUrl;

    /** @var string */
    public $agentEmail;

    /** @var string */
    public $endpoint;

    /** @var string */
    public $verb = 'GET';

    /** @var string */
    public $responseType = 'application/json';

    /** @var array */
    public $headers = [];

    /** @var array */
    public $filters = [];

    /** @var array */
    public $bodyFields = [];

    /** @var array */
    public $postFields = [];

    /** @var array with keys 'property' and 'direction' */
    public $sort = [];

    /** @var integer */
    public $page;

    /** @var integer */
    public $limit;

    /**
     * The function is a PHP constructor that initializes properties with provided values,
     * including an optional response type.
     *
     * @param string $baseUrl The `baseUrl` of the API.
     * @param string $apiKey The `apiKey` for authentication.
     * @param string $agentEmail The `agentEmail` for authentication.
     * @param string $responseType The `responseType` expected.
     * @param string $version The `version` of the API. Either self::API_V0 ou self::API_V1.
     */
    public function __construct(
        $baseUrl,
        $apiKey,
        $agentEmail,
        $responseType = 'application/json',
        $version = self::API_V0
    ) {
        $this->apiVersion = $version;
        $this->apiKey = $apiKey;
        $this->baseUrl = $baseUrl;
        $this->agentEmail = $agentEmail;
        $this->responseType = $responseType;
    }

    /**
     * Sets the endpoint for the API request.
     * It will be concatenated the `baseUrl` to form the full URL.
     * It must start with a `/`.
     *
     * @param string $endpoint The endpoint for the API request.
     *
     * @return $this
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    /**
     * Sets the verb to be used in the API request to GET.
     *
     * @return $this
     */
    public function setGet()
    {
        $this->verb = 'GET';

        return $this;
    }

    /**
     * Sets the verb to be used in the API request to POST.
     *
     * @return $this
     */
    public function setPost()
    {
        $this->verb = 'POST';

        return $this;
    }

    /**
     * Sets the verb to be used in the API request to PUT.
     *
     * @return $this
     */
    public function setPut()
    {
        $this->verb = 'PUT';

        return $this;
    }

    /**
     * Sets the verb to be used in the API request to PATCH.
     *
     * @return $this
     */
    public function setPatch()
    {
        $this->verb = 'PATCH';

        return $this;
    }

    public function getTotalItems()
    {
        if ($this->response === null) {
            return null;
        }

        if ($this->apiVersion === self::API_V1) {
            return $this->response->getHeaders()['X-Total-Items'][0];
        }

        return $this->response->getHeaders()['total-count'][0];
    }

    public function getTotalPages()
    {
        if ($this->response === null) {
            return null;
        }

        if ($this->apiVersion === self::API_V1) {
            return $this->response->getHeaders()['X-Total-Pages'][0];
        }

        return $this->response->getHeaders()['total-pages'][0];
    }

    public function getSearchId()
    {
        if ($this->response === null || $this->apiVersion === self::API_V1) {
            return null;
        }

        return $this->response->getHeaders()['search-id'][0];
    }

    /**
     * The queryApi function sends a request to an API with specified headers, post fields,
     * and endpoint using the Guzzle HTTP client in PHP.
     *
     * @return Psr\Http\Message\ResponseInterface
     */
    public function queryApi()
    {
        $this->validateFilters();
        $this->validateSort();

        if (!empty($this->page)) {
            $this->page = (int) $this->page;
        }

        if (!empty($this->limit)) {
            $this->limit = (int) $this->limit;
        }

        if ($this->apiVersion === self::API_V0) {
            return $this->queryApiV0();
        }

        return $this->queryApiV1();
    }

    private function queryApiV0()
    {
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Accept' => $this->responseType,
                'Content-Type' => $this->responseType,
            ],
        ]);

        $query = [];

        if (!empty($this->page)) {
            $query['page'] = $this->page;
        }

        if (!empty($this->limit)) {
            $query['take'] = $this->limit;
        }

        foreach ($this->filters as $i => $filter) {
            $value = $filter['value'];
            switch ($filter['operator']) {
                case self::FILTER_OPERATOR_GE:
                case self::FILTER_OPERATOR_GT:
                    $value = '>' . $value;
                    break;
                case self::FILTER_OPERATOR_LE:
                case self::FILTER_OPERATOR_LT:
                    $value = '<' . $value;
                    break;
                case self::FILTER_OPERATOR_NE:
                    $value = '!' . $value;
                    break;
            }
            $query[$filter['property']] = $value;
        }

        if (isset($this->sort['property']) && isset($this->sort['direction'])) {
            $query['sortBy'] = $this->sort['property'];
            $query['sortDirection'] = $this->sort['direction'];
        }

        $options['query'] = $query;

        # Body fields (values to be sent in the body of the request as json).
        if (count($this->bodyFields)) {
            $options['json'] = $this->bodyFields;
        }

        # Post fields (values to be sent as if they are comming from a form).
        if (count($this->postFields)) {
            $options['form_params'] = $this->postFields;
        }

        # Headers.
        if (!empty($this->apiKey)) {
            $this->headers['Authorization'] = 'Bearer ' . $this->apiKey;
        }
        if (count($this->headers)) {
            $options['headers'] = $this->headers;
        }

        $this->response = $this->client->request($this->verb, $this->endpoint, $options);

        return $this->response;
    }

    private function queryApiV1()
    {
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'X-API-KEY' => $this->apiKey,
                'octa-agent-email' => $this->agentEmail,
                'accept' => $this->responseType,
            ],
        ]);

        # Create the query string.
        $queryString = [];

        foreach ($this->filters as $i => $filter) {
            $queryString["filters[$i][property]"] = $filter['property'];
            $queryString["filters[$i][operator]"] = $filter['operator'];
            $queryString["filters[$i][value]"] = $filter['value'];
        }

        if (isset($this->sort['property']) && isset($this->sort['direction'])) {
            $queryString['sort[property]'] = $this->sort['property'];
            $queryString['sort[direction]'] = $this->sort['direction'];
        }

        if (!empty($this->page)) {
            $queryString['page'] = $this->page;
        }

        if (!empty($this->limit)) {
            $queryString['limit'] = $this->limit;
        }

        # Create a string with the query string.
        foreach ($queryString as $key => $value) {
            $queryString[$key] = "$key=$value";
        }
        $options['query'] = implode('&', $queryString);

        # Body fields (values to be sent in the body of the request as json).
        if (count($this->bodyFields)) {
            $options['json'] = $this->bodyFields;
        }

        # Post fields (values to be sent as if they are comming from a form).
        if (count($this->postFields)) {
            $options['form_params'] = $this->postFields;
        }

        if (count($this->headers)) {
            $options['headers'] = $this->headers;
        }

        $this->response = $this->client->request($this->verb, $this->endpoint, $options);

        return $this->response;
    }

    /**
     * Validates the filters.
     *
     * @throws \Exception
     */
    private function validateFilters()
    {
        foreach ($this->filters as $i => $filter) {
            if (
                !isset($filter['property']) ||
                !isset($filter['operator']) ||
                !isset($filter['value'])
            ) {
                throw new \Exception(
                    'Invalid filter configuration. Check https://developers.octadesk.com/reference/filters.'
                );
            }

            if (
                !in_array($filter['operator'], [
                    self::FILTER_OPERATOR_EQ,
                    self::FILTER_OPERATOR_NE,
                    self::FILTER_OPERATOR_GT,
                    self::FILTER_OPERATOR_GE,
                    self::FILTER_OPERATOR_LT,
                    self::FILTER_OPERATOR_LE,
                    self::FILTER_OPERATOR_IN,
                    self::FILTER_OPERATOR_NIN,
                ])
            ) {
                throw new \Exception(
                    'Invalid filter operator. Check https://developers.octadesk.com/reference/filters.'
                );
            }

            # Na API v0.0.1 o filtro pelo id do requisitante do ticket (pessoa que abriu)
            # deve se chamar `idRequester` e na API v1.0.0 deve se chamar `requester.id`.
            if ($this->apiVersion === self::API_V0) {
                if ($filter['property'] === 'requester.id') {
                    $this->filters[$i]['property'] = 'idRequester';
                }
            } else {
                if ($filter['property'] === 'idRequester') {
                    $this->filters[$i]['property'] = 'requester.id';
                }
            }
        }
    }

    /**
     * Validates the sort.
     *
     * @throws \Exception
     */
    private function validateSort()
    {
        if (count($this->sort) === 0) {
            return;
        }

        if (!isset($this->sort['property']) || !isset($this->sort['direction'])) {
            throw new \Exception(
                'Invalid sort configuration. Check https://developers.octadesk.com/reference/sort.'
            );
        }

        if (!in_array($this->sort['direction'], [self::SORT_ASC, self::SORT_DESC])) {
            throw new \Exception(
                'Invalid sort direction. Check https://developers.octadesk.com/reference/sort.'
            );
        }
    }
}
