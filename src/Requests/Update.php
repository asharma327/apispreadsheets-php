<?php

namespace ApiSpreadsheets\Requests;

use ApiSpreadsheets\SpreadsheetRequest;

class Update extends SpreadsheetRequest
{
    /**
     * @var array
     */
    private $data;

    /**
     * @var string
     */
    private $query;

    public function __construct(string $file_id, array $data, string $query, $access_key = null, $secret_key = null)
    {
        $this->data  = $data;
        $this->query = $query;

        parent::__construct($file_id, $access_key, $secret_key);
    }

    /**
     * Returns the request type.
     *
     * @return string
     */
    public function getRequestType(): string
    {
        return 'POST';
    }

    /**
     * Handles the successful response.
     *
     * @return array
     */
    public function handleSuccessfulResponse(): array
    {
        if (!$response = $this->getResponse()) {
            $response = ['message' => 'Your rows were updated successfully'];
        }

        return array_merge($response, ['status_code' => $this->getStatusCode()]);
    }

    /**
     * Validate the given inputs.
     *
     * @return void
     * @throws \Exception
     */
    public function validate()
    {
        if (empty($this->data)) {
            throw new \Exception('Please enter data to be created');
        }

        if (empty($this->query)) {
            throw new \Exception('Please enter a query');
        }
    }

    /**
     * Returns the parameters that should be appended to the resource url.
     *
     * @return string
     * @throws \Exception
     */
    public function getResourceUrlParameters(): string
    {
        return $this->file_id;
    }

    /**
     * Returns the data that should be sent in the request body.
     *
     * @return array
     */
    public function dataShouldBeSent()
    {
        return [
            'data'  => $this->data,
            'query' => $this->query
        ];
    }
}