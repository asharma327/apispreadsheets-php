# API Spreadsheets

PHP library for [API Spreadsheets](https://www.apispreadsheets.com/docs)

## Requirements
- PHP >= 7.0

## Installation

```
composer require asharma327/apispreadsheets-php
```

## Usage

### 1. Create

The create method will create new rows in the file. The required parameters are `string: fileId` and `array: data`

```php
require 'vendor/autoload.php';

use ApiSpreadsheets\Spreadsheet;

$response = Spreadsheet::create('fileId', [
    'Id' => 1,
    'Name' => 'John Doe'
]);
```

If the file is private you can provide API keys as extra parameters:

```php
$response = Spreadsheet::create('fileId', [
    'Id' => 1,
    'Name' => 'John Doe'
], 'accessKey', 'secretKey');
````

The following array will be returned if the call was successful:

```php
['message' = 'Your rows were created successfully', 'status_code' => 201]
```

And in case of any error returned from the API:

```php
['error' = 'Error message', 'status_code' => 401]
```