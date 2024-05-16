<?php
/**
 * User: joao
 * Date: 07/05/24
 */

namespace marqu3s\octadeskApiV1;

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
     * @param array $postFields
     *
     * @return Psr\Http\Message\ResponseInterface
     * @see https://developers.octadesk.com/reference/addcontact
     */
    public function create($bodyFields)
    {
        $this->setEndpoint('/contacts')->setPost();
        $this->bodyFields = $bodyFields;

        $response = $this->queryApi();

        return $response;
    }

    /**
     * Updates a contact.
     *
     * @param string $uuid
     * @param array $postFields
     *
     * @return Psr\Http\Message\ResponseInterface
     * @see https://api.octadesk.services/docs/#/person/updatePerson
     */
    public function update($uuid, $bodyFields)
    {
        if ($this->apiVersion === OctadeskApi::API_V0) {
            $this->setEndpoint("/person/$uuid");
        } else {
            $this->setEndpoint("/contacts/$uuid");
        }

        $this->setPut();
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
            $this->setEndpoint("/person/$uuid");
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
            $this->setEndpoint('/person');
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
}
