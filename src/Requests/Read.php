<?php

namespace ApiSpreadsheets\Requests;

use ApiSpreadsheets\SpreadsheetRequest;

class Read extends SpreadsheetRequest
{
    /**
     * @var array
     */
    private $params;


    public function __construct(string $file_id, array $params = [], $access_key = null, $secret_key = null)
    {
        $this->params = $params;

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
        if (empty($this->params)) {
            return;
        }

        if (array_key_exists('dataFormat', $this->params)) {
            if (!is_string($this->params['dataFormat'])) {
                throw new \Exception('Date format must be a string');
            }
        }

        if (array_key_exists('query', $this->params)) {
            if (!is_string($this->params['query'])) {
                throw new \Exception('Query must be a string');
            }
        }

        if (array_key_exists('count', $this->params)) {
            if ($this->params['count'] === 'true' || $this->params['count'] === 'false') {
                $this->params['count'] = (bool)$this->params['count'];
            }

            if (!is_bool($this->params['count'])) {
                throw new \Exception('Count must be a boolean');
            }

            if ($this->params['count'] === false) {
                unset($this->params['count']);
            }
        }

        if (array_key_exists('limit', $this->params)) {
            if (is_string($this->params['limit'])) {
                $this->params['limit'] = trim($this->params['limit']);
            }

            if (!is_numeric($this->params['limit'])) {
                throw new \Exception('Limit must be a number');
            }
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
        if (!empty($this->access_key) && !empty($this->secret_key)) {
            $this->params['accessKey'] = $this->access_key;
            $this->params['secretKey'] = $this->secret_key;
        }

        if (array_key_exists('dataFormat', $this->params)) {
            if (empty($this->params['dataFormat'])) {
                unset($this->params['dataFormat']);
            } else {
                $this->params['dataFormat'] = strtolower($this->params['dataFormat']);
            }
        }

        return $this->file_id . '/?' . http_build_query($this->params);
    }
}