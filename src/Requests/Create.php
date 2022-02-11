<?php

declare(strict_types=1);

namespace ApiSpreadsheets\Requests;

use ApiSpreadsheets\SpreadsheetRequest;

class Create extends SpreadsheetRequest
{
    private $data;

    public function __construct(string $file_id, array $data, $access_key = null, $secret_key = null)
    {
        $this->data = $data;

        parent::__construct($file_id, $access_key, $secret_key);
    }

    public function getRequestType(): string
    {
        return 'POST';
    }

    public function handleSuccessfulResponse(): array
    {
        if ($response = $this->getResponse()) {
            return $response;
        }

        return [
            'message' => 'Your rows were created successfully',
            'status_code' => $this->getStatusCode()
        ];
    }

    public function validate()
    {
        if (empty($this->data)) {
            throw new \Exception('Please enter data to be created');
        }
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getResourceUrl(): string
    {
        return $this->base_url . $this->file_id;
    }

    public function sendPostData()
    {
        return $this->data;
    }
}