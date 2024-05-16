<?php
/**
 * User: joao
 * Date: 07/05/24
 */

namespace marqu3s\octadeskApiV1;

/**
 * Class TicketsApi
 *
 * @see https://developers.octadesk.com/reference/tickets
 */
class TicketsApi extends OctadeskApi
{
    const TICKET_STATUS_NEW = 'novo';
    const TICKET_STATUS_PENDING = 'pendente';
    const TICKET_STATUS_SOLVED = 'resolvido';
    const TICKET_STATUS_OPEN = 'andamento';
    const TICKET_STATUS_OPEN_UUID = '18d082d0-4a24-4620-9d96-a827323dfc8b';
    const TICKET_STATUS_REJECTED = 'rejeitado';
    const TICKET_STATUS_CANCELED = 'cancelado';

    /**
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
        if ($this->apiVersion === self::API_V0) {
            $this->setEndpoint('/tickets/search');
        } else {
            $this->setEndpoint('/tickets');
        }

        $this->setGet();
        $this->filters = $filters;
        $this->sort = $sort;
        $this->page = $page;
        $this->limit = $limit;

        $response = $this->queryApi();

        return $response;
    }

    /**
     * Creates a new ticket.
     *
     * @param array $bodyFields
     *
     * @return Psr\Http\Message\ResponseInterface
     * @see https://developers.octadesk.com/reference/addticket
     */
    public function create($fields)
    {
        $this->setEndpoint('/tickets')->setPost();
        $this->bodyFields = $fields;

        $response = $this->queryApi();

        return $response;
    }

    /**
     * Updates a ticket.
     *
     * @param integer $number The ticket number.
     * @param array $bodyFields
     *
     * @return Psr\Http\Message\ResponseInterface
     * @see https://developers.octadesk.com/reference/addticket
     */
    public function update($number, $fields)
    {
        $this->setEndpoint("/tickets/$number")->setPut();
        $this->bodyFields = $fields;

        $response = $this->queryApi();

        return $response;
    }

    /**
     * Get a ticket by number.
     *
     * @param integer $number
     *
     * @return Psr\Http\Message\ResponseInterface
     * @see https://developers.octadesk.com/reference/getbynumber
     */
    public function getByNumber($number)
    {
        $number = (int) $number;

        $this->setEndpoint("/tickets/$number")->setGet();

        $response = $this->queryApi();

        return $response;
    }
}
