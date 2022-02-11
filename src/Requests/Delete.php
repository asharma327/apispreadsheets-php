<?php

namespace ApiSpreadsheets\Requests;

use ApiSpreadsheets\SpreadsheetRequest;

class Delete extends SpreadsheetRequest
{
    /**
     * @var string
     */
    protected $query;

    /**
     * @var array
     */
    protected $params = [];


    public function __construct(string $file_id, string $query, $access_key = null, $secret_key = null)
    {
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
        return 'GET';
    }

    /**
     * Handles the successful response.
     *
     * @return array
     */
    public function handleSuccessfulResponse(): array
    {
        if (!$response = $this->getResponse()) {
            $response = [];
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
        $this->params['query'] = $this->query;

        if (!empty($this->access_key) && !empty($this->secret_key)) {
            $this->params['accessKey'] = $this->access_key;
            $this->params['secretKey'] = $this->secret_key;
        }

        return $this->file_id . '/?' . http_build_query($this->params);
    }
}