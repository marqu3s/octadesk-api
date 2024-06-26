<?php
/**
 * User: joao
 * Date: 07/05/24
 */

namespace marqu3s\octadeskApi;

/**
 * Class ContactsApi
 *
 * @see https://developers.octadesk.com/reference/contacts
 */
class ContactsApi extends OctadeskApi
{
    const PERSON_TYPE_NONE = 0;
    const PERSON_TYPE_EMPLOYEE = 1;
    const PERSON_TYPE_CUSTOMER = 2;
    const PERSON_TYPE_HANDLER = 3;
    const PERSON_TYPE_SYSTEM = 4;
    const PERSON_TYPE_FORWARDING_EMPLOYEE = 5;

    const PERMISSION_VIEW_NONE = 0;
    const PERMISSION_VIEW_MY_REQUESTS = 1;
    const PERMISSION_VIEW_MY_ORGANIZATION = 2;

    const PERMISSION_TYPE_NONE = 0;
    const PERMISSION_TYPE_ALL = 1;
    const PERMISSION_TYPE_GROUP = 2;
    const PERMISSION_TYPE_GROUP_PLUS_INTERACTED = 3;

    const PARTICIPANT_PERMISSION_NONE = 0;
    const PARTICIPANT_PERMISSION_VIEW_ONLY = 1;
    const PARTICIPANT_PERMISSION_VIEW_EDIT = 2;

    const ROLE_TYPE_NONE = 0;
    const ROLE_TYPE_OWNER = 1;
    const ROLE_TYPE_ADMIN = 2;
    const ROLE_TYPE_AGENT_MASTER = 3;
    const ROLE_TYPE_AGENT = 4;
    const ROLE_TYPE_CLIENT = 5;
    const ROLE_TYPE_CORPORATE_PERSON = 6;

    /**
     * Search for contacts.
     * NOTE: This function is for API V1 only.
     *
     * @param array $filters
     * @param array $sort
     * @param integer $page
     * @param integer $limit
     *
     * @return Psr\Http\Message\ResponseInterface
     * @see https://developers.octadesk.com/reference/getall
     */
    public function search($filters = [], $sort = [], $page = 1, $limit = 20)
    {
        $this->setEndpoint('/contacts')->setGet();

        $this->filters = $filters;
        $this->sort = $sort;
        $this->page = $page;
        $this->limit = $limit;

        $response = $this->queryApi();

        return $response;
    }

    /**
     * Creats a new contact.
     *
     * @param array $bodyFields
     *
     * @return Psr\Http\Message\ResponseInterface
     * @see https://developers.octadesk.com/reference/addcontact
     */
    public function create($bodyFields)
    {
        if ($this->apiVersion === OctadeskApi::API_V0) {
            $this->setEndpoint('/persons');
        } else {
            $this->setEndpoint('/contacts');
        }

        $this->setPost();
        $this->bodyFields = $bodyFields;

        $response = $this->queryApi();

        return $response;
    }

    /**
     * Updates a contact.
     *
     * @param string $uuid
     * @param array $bodyFields
     *
     * @return Psr\Http\Message\ResponseInterface
     * @see https://api.octadesk.services/docs/#/person/updatePerson
     */
    public function update($uuid, $bodyFields)
    {
        if ($this->apiVersion === OctadeskApi::API_V0) {
            $this->setEndpoint("/persons/$uuid");
        } else {
            $this->setEndpoint("/contacts/$uuid");
        }

        $this->setPut();
        $this->bodyFields = $bodyFields;

        $response = $this->queryApi();

        return $response;
    }

    /**
     * Patch a contact, updating only the fields passed in the body.
     *
     * @param string $uuid
     * @param array $bodyFields
     *
     * @return Psr\Http\Message\ResponseInterface
     * @see https://api.octadesk.services/docs/#/person/updatePerson
     */
    public function patch($uuid, $bodyFields)
    {
        if ($this->apiVersion === OctadeskApi::API_V0) {
            throw new \Exception('PATCH method not allowed in API V0');
        } else {
            $this->setEndpoint("/contacts/$uuid");
        }

        $this->setPatch();
        $this->bodyFields = $bodyFields;

        $response = $this->queryApi();

        return $response;
    }

    /**
     * Get a contact by ID.
     *
     * @param string $id
     *
     * @return Psr\Http\Message\ResponseInterface
     * @see https://developers.octadesk.com/reference/getcontactbyid
     */
    public function getById($uuid)
    {
        if ($this->apiVersion === OctadeskApi::API_V0) {
            $this->setEndpoint("/persons/$uuid");
        } else {
            $this->setEndpoint("/contacts/$uuid");
        }

        $this->setGet();

        $response = $this->queryApi();

        return $response;
    }

    /**
     * Get a contact by email.
     *
     * @param string $email
     *
     * @return Psr\Http\Message\ResponseInterface
     * @see https://api.octadesk.services/docs/#/person/getPersonByEmail
     */
    public function getByEmail($email)
    {
        if ($this->apiVersion === OctadeskApi::API_V0) {
            $this->setEndpoint('/persons');
        } else {
            $this->setEndpoint('/contacts');
        }

        $this->setGet();

        $this->filters[] = [
            'property' => 'email',
            'operator' => 'eq',
            'value' => htmlentities($email),
        ];

        $response = $this->queryApi();

        return $response;
    }

    /**
     * Get a contact by phone number.
     *
     * @param string $countryCode (2 digits)
     * @param string $phoneNumber
     *
     * @return Psr\Http\Message\ResponseInterface
     * @see https://api.octadesk.services/docs/#/person/getPersonByEmail
     */
    public function getByPhoneNumber($countryCode, $phoneNumber)
    {
        if ($this->apiVersion === OctadeskApi::API_V0) {
            $this->setEndpoint('/persons/filter')->setPost();
        } else {
            $this->setEndpoint('/contacts')->setGet();
        }

        $this->filters = [
            [
                'property' => 'phoneContacts.countryCode',
                'operator' => OctadeskApi::FILTER_OPERATOR_EQ,
                'value' => $countryCode,
            ],
            [
                'property' => 'phoneContacts.number',
                'operator' => OctadeskApi::FILTER_OPERATOR_EQ,
                'value' => $phoneNumber,
            ],
        ];

        $response = $this->queryApi();

        return $response;
    }

    /**
     * Get all agents.
     * NOTE: This function is for API V0 only.
     *
     * @param string $emailOrName
     * @param string $page
     *
     * @return Psr\Http\Message\ResponseInterface
     * @see https://api.octadesk.services/docs/#/person/getEmployees
     */
    public function getAgents($emailOrName = null, $page = 1, $detailed = false)
    {
        $this->setEndpoint('/persons/agents')->setGet();

        $this->page = $page;

        $this->filters = [
            [
                'property' => 'keywork',
                'operator' => 'eq',
                'value' => htmlentities($emailOrName),
            ],
            [
                'property' => 'detailed',
                'operator' => 'eq',
                'value' => htmlentities($detailed),
            ],
        ];

        $response = $this->queryApi();

        return $response;
    }

    /**
     * Updates an agent avatar URL.
     * NOTE: This function is for API V0 only.
     *
     * @param string $uuid
     * @param string $email
     * @param string $url
     *
     * @return Psr\Http\Message\ResponseInterface
     * @see https://api.octadesk.services/docs/#/person/updatePerson
     */
    public function updateAvatarUrl($uuid, $email, $url)
    {
        $this->setEndpoint("/persons/$uuid")->setPut();

        $this->postFields['email'] = $email;
        $this->postFields['thumbUrl'] = $url;

        return $this->queryApi();
    }
}
