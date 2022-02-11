<?php

namespace ApiSpreadsheets;

abstract class SpreadsheetRequest
{
    /**
     * @var string
     */
    private $base_url = 'https://api.apispreadsheets.com/data/';

    /**
     * @var string
     */
    protected $file_id;

    /**
     * @var string|null
     */
    protected $access_key;

    /**
     * @var string|null
     */
    protected $secret_key;


    const ERRORS = [
        400 => "The required parameter data was not present OR the column names did not match what's in the file",
        401 => "The data is private and incorrect access and secret keys were provided",
        402 => "You are over the row limit per file for your plan",
        404 => "The fileID does not exist",
        406 => "The format of data is not correct",
        500 => "There was something wrong with our server",
        502 => "Something went wrong with the Google Sheets or DropBox Servers"
    ];

    /**
     * @var int $status_code
     */
    private $status_code;

    /**
     * @var array $response
     */
    private $response;

    /**
     * @return array
     */
    abstract public function handleSuccessfulResponse(): array;

    /**
     * @return string
     */
    abstract public function getRequestType(): string;

    /**
     * @return string
     * @throws \Exception
     */
    abstract public function getResourceUrlParameters(): string;

    /**
     * @param string      $file_id
     * @param string|null $access_key
     * @param string|null $secret_key
     *
     * @throws \Exception
     */
    public function __construct(string $file_id, $access_key, $secret_key)
    {
        $this->file_id    = $file_id;
        $this->access_key = $access_key;
        $this->secret_key = $secret_key;

        $this->ensureFileIdIsValid();
        $this->ensureApiKeysAreValidIfExist();
    }


    /**
     * Execute the api request.
     *
     * @return array
     * @throws \Exception
     */
    public function execute(): array
    {
        $this->ensureInputsIsValidated();

        $ch = curl_init($this->getResourceUrl());

        if ($this->getRequestType() === 'POST' && method_exists($this, 'dataShouldBeSent')) {
            $body = $this->dataShouldBeSent();
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($body) ? json_encode($body) : $body);
        }

        $headers = ['Content-type: application/json'];

        if (!empty($this->access_key) && !empty($this->secret_key)) {
            $headers = array_merge($headers, [
                'accessKey: ' . $this->access_key,
                'secretKey: ' . $this->secret_key
            ]);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        $this->status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $this->response = json_decode($result, true);

        if (curl_errno($ch) !== 0) {
            throw new \Exception(!empty(curl_error($ch)) ? curl_error($ch) : 'Curl error number ' . curl_errno($ch));
        }

        curl_close($ch);

        return $this->formatResponse();
    }

    /**
     * Ensures that file id is valid.
     *
     * @return void
     * @throws \Exception
     */
    protected function ensureFileIdIsValid()
    {
        if (empty($this->file_id)) {
            throw new \Exception('Please enter a File ID');
        }
    }

    /**
     * Ensures that Api keys are valid if provided.
     *
     * @return void
     * @throws \Exception
     */
    private function ensureApiKeysAreValidIfExist()
    {
        if (!empty($this->access_key) && !empty($this->secret_key)) {
            if (!is_string($this->access_key) || !is_string($this->secret_key)) {
                throw new \Exception('API keys must be string');
            }
        }

        if (!empty($this->access_key) || !empty($this->secret_key)) {
            $keys = [$this->access_key, $this->secret_key];

            if (count(array_filter($keys)) !== count($keys)) {
                throw new \Exception('Please provide both access and secret key if your file is private');
            }
        }
    }

    /**
     * Formats unsuccessful response.
     *
     * @return array
     */
    protected function handleUnsuccessfulResponse(): array
    {
        return [
            'error'         => $this->getErrorFromResponse(),
            'status_code'   => $this->status_code
        ];
    }

    /**
     * Returns error string from response code.
     *
     * @return string
     */
    private function getErrorFromResponse(): string
    {
        if (!empty($this->response['error'])) {
            return $this->response['error'];
        }

        if (array_key_exists($this->status_code, self::ERRORS)) {
            return self::ERRORS[$this->status_code];
        }

        return 'Something went wrong';
    }

    /**
     * Returns response status code for the request.
     *
     * @return int
     */
    protected function getStatusCode(): int
    {
        return $this->status_code;
    }

    /**
     * Returns the response for the request.
     *
     * @return array|null
     */
    protected function getResponse()
    {
        return  $this->response;
    }

    /**
     * Formats and return the response.
     *
     * @return array
     */
    protected function formatResponse(): array
    {
        if ($this->successful()) {
            return $this->handleSuccessfulResponse();
        }

        return $this->handleUnsuccessfulResponse();
    }

    /**
     * Check if the request is successful.
     *
     * @return bool
     */
    protected function successful(): bool
    {
        return $this->status_code < 400;
    }

    /**
     * Validate the inputs if applicable.
     *
     * @return void
     */
    private function ensureInputsIsValidated()
    {
        if (method_exists($this, 'validate')) {
            $this->validate();
        }
    }

    /**
     * Returns the resource url.
     *
     * @return string
     * @throws \Exception
     */
    private function getResourceUrl(): string
    {
        return $this->base_url . $this->getResourceUrlParameters();
    }
}