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

The create method will create new rows in the file. 

The required parameters are:

- `fileId`:`string` This id of the spreadsheet.
- `data`:`array` The rows that you want to create.

```php
require 'vendor/autoload.php';

use ApiSpreadsheets\Spreadsheet;

// Data can have 3 formats:
$data = [ 
    'Id' => 1,
    'Name' => 'AT&T'
];

// Or:
$data = [[
    'Id' => 1,
    'Name' => 'AT&T'
], [
    'Id' => 2,
    'Name' => 'Apple'
]];

// Or:
$data = [
    'Id' => [1, 2],
    'Name' => ['AT&T', 'Apple']
];

$response = Spreadsheet::create('fileId', $data);

// Or if the file is private you can provide API keys as extra parameters:
$response = Spreadsheet::create('fileId', $data, 'accessKey', 'secretKey');

// ['message' => 'Your rows were created successfully', 'status_code' => 201]
```


### 2. Update

The update method will update values in columns.

The required parameters are:

- `fileId`:`string` This id of the spreadsheet.
- `data`:`array` The data you want to update.
- `query`: `string` SQL style query to update the rows that you want.

```php
require 'vendor/autoload.php';

use ApiSpreadsheets\Spreadsheet;

$data = [ 
    'Id' => 1,
    'Name' => 'AT&T'
];

$query = "select*from[fileId]whereId=1";

$response = Spreadsheet::update('fileId', $data, $query);

// Or if the file is private you can provide API keys as extra parameters:
$response = Spreadsheet::update('fileId', $data, $query, 'accessKey', 'secretKey');

// ['message' => 'Your rows were updated successfully', 'status_code' => 201]
```

### 3. Read

The read method will read rows from your file.

The required parameter is:

- `fileId`:`string` This id of the spreadsheet.

```php
require 'vendor/autoload.php';

use ApiSpreadsheets\Spreadsheet;

// An optional array to filter or change how the returned rows look like
$params = [ 
    'dataFormat' => 'column', // Can be matrix or column, the default is row.
    'limit' => 10, // limit the returned rows
    'count' => true, // only return the count of the result
    'query' => "select*from{$file_id}whereId=1" // SQL style query to get a subset of rows.
];

$response = Spreadsheet::read('fileId', $params);

// Or if the file is private you can provide API keys as extra parameters:
$response = Spreadsheet::read('fileId', $params, 'accessKey', 'secretKey');

// Response:

// in case of dataFormat is row (default):
// ['data' => [['Id' => 1, 'Name' => 'Apple'], [...]], 'status_code' => 200]

// column dataFormat:
// ['Id' => [1, ...], 'Name' => ['Apple', ...], 'status_code' => 200]

// matrix dataFormat:
// ['data' => [[1, 'Apple'], [...]], 'status_code' => 200]

// In case of count is true
// ['count' => 10, 'status_code' => 200]
```

### 4. Delete

The delete method will delete rows from your file.

The required parameters are:

- `fileId`:`string` This id of the spreadsheet.
- `query`: `string` SQL style DELETE query to specify which rows to delete.

```php
require 'vendor/autoload.php';

use ApiSpreadsheets\Spreadsheet;

$query = "delete*from[fileID]whereId=1";

$response = Spreadsheet::delete('fileId', $query);

// Or if the file is private you can provide API keys as extra parameters:
$response = Spreadsheet::delete('fileId', $query, 'accessKey', 'secretKey');


// ['message' => '2 rows were deleted', 'status_code' => 200]
```


## API Error Handling

If the API request has an error, an array with `error` and `status_code` key pairs will be returned, for example: 
```php
['error' = 'Error message', 'status_code' => 400]
```

For More information please consult the API docs: [https://www.apispreadsheets.com/docs](https://www.apispreadsheets.com/docs) 

## License
The MIT License (MIT). Please see [LICENSE](../master/LICENSE) for more information.
