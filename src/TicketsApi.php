<?php
/**
 * User: joao
 * Date: 07/05/24
 */

namespace marqu3s\octadeskApi;

/**
 * Class TicketsApi
 *
 * @see https://developers.octadesk.com/reference/tickets
 */
class TicketsApi extends OctadeskApi
{
    const TICKET_STATUS_NEW = 'novo';
    const TICKET_STATUS_NEW_UUID = 'ab59f270-ccb3-4eac-b4e3-5f2d23961dba';
    const TICKET_STATUS_PENDING = 'pendente';
    const TICKET_STATUS_PENDING_UUID = '09350a7d-886d-4490-ae88-f3a3605ff587';
    const TICKET_STATUS_SOLVED = 'resolvido';
    const TICKET_STATUS_SOLVED_UUID = 'b2c80a95-840c-4cd0-baa3-f266f303d626';
    const TICKET_STATUS_OPEN = 'andamento';
    const TICKET_STATUS_OPEN_UUID = 'b2c80a95-840c-4cd0-baa3-f266f303d626';
    const TICKET_STATUS_HOLD = 'em espera';
    const TICKET_STATUS_HOLD_UUID = '8543caa3-23d2-47f0-bb60-7ccfba62e7b4';
    const TICKET_STATUS_REJECTED = 'rejeitado';
    const TICKET_STATUS_REJECTED_UUID = '62c696e4-ef22-314a-9f84-5c09153db659';
    const TICKET_STATUS_CANCELED = 'cancelado';
    const TICKET_STATUS_CANCELED_UUID = '0cac3ff6-6039-4581-a727-c4dff94d5367';

    const TICKET_SORTBY_NUMBER = 'number';
    const TICKET_SORTBY_LASTDATEUPDATE = 'lastDateUpdate';
    const TICKET_SORTBY_OPENDATE = 'openDate';
    const TICKET_SORTBY_SLADUEDATE = 'slaDueDate';

    const TICKET_SORTDIRECTION_ASC = 'asc';
    const TICKET_SORTDIRECTION_DESC = 'desc';

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

        $this->filters = [];
        $this->sort = [];
        $this->page = null;
        $this->limit = null;

        $response = $this->queryApi();

        return $response;
    }

    /**
     * Get interactions for a ticket.
     *
     * @param integer $ticketNumber
     *
     * @return Psr\Http\Message\ResponseInterface
     */
    public function getInteractions($number)
    {
        $this->setEndpoint("/tickets/$number/interactions")->setGet();

        $this->filters = [];
        $this->sort = [];
        $this->page = null;
        $this->limit = null;

        return $this->queryApi();
    }
}
