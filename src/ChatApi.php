<?php
/**
 * User: joao
 * Date: 07/05/24
 */

namespace marqu3s\octadeskApi;

/**
 * Class ChatApi
 *
 * @see https://developers.octadesk.com/reference/chat
 */
class ChatApi extends OctadeskApi
{
    /**
     * Searchs for chats.
     *
     * @param array $filters
     * @param array $sort
     * @param integer $page
     * @param integer $limit
     *
     * @return Psr\Http\Message\ResponseInterface
     * @see https://developers.octadesk.com/reference/getalltickets
     */
    public function search($filters = [], $sort = [], $page = 1, $limit = 20)
    {
        $this->setEndpoint('/chat')->setGet();

        $this->filters = $filters;
        $this->sort = $sort;
        $this->page = $page;
        $this->limit = $limit;

        $response = $this->queryApi();

        return $response;
    }

    /**
     * Creates a new chat using a template.
     *
     * @param array $bodyFields
     *
     * @return Psr\Http\Message\ResponseInterface
     * @see https://developers.octadesk.com/reference/sendtemplate
     */
    public function sendTemplate($bodyFields)
    {
        $this->setEndpoint('/chat/send-template')->setPost();
        $this->bodyFields = $bodyFields;

        $response = $this->queryApi();

        return $response;
    }

    /**
     * Get a chat by ID.
     *
     * @param string $id
     *
     * @return Psr\Http\Message\ResponseInterface
     * @see https://developers.octadesk.com/reference/getchatbyid
     */
    public function getById($id)
    {
        $this->setEndpoint("/chat/$id")->setGet();

        $response = $this->queryApi();

        return $response;
    }
}
