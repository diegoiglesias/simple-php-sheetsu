#Simple PHP Class for Sheetsu API

Allows interaction with [Sheetsu](https://sheetsu.com/) API via PHP. Feedback or bug reports are appreciated.

Turn a Google Spreadsheet into an API.
Without any setup or plugins. Use it anywhere
[https://sheetsu.com/](https://sheetsu.com/)

API docs: [https://sheetsu.com/docs/beta](https://sheetsu.com/docs/beta)

##Requirements

- PHP 5.3 or higher
- cURL
- Sheetsu API URL

##Install via Composer

```
composer require diegoiglesias/simple-php-sheetsu
```

``` json
{
    "require": {
        "diegoiglesias/simple-php-sheetsu": "*"
    }
}
```

NOTE: If you don't want to use Composer simply download *src/SimpleSheetsu/Sheetsu.php* file and include it on your script.

##Initialize the class

```php
use SimpleSheetsu\Sheetsu

$object = new Sheetsu([
		'apiId' => SHEETSU_API_ID,
		'apiKey' => SHEETSU_API_KEY (optional),
		'apiSecret' => SHEETSU_API_SECRET (optional)
	]);
```

Note that HTTP Basic auth is optional.

##Available methods

###Get spreadsheet

```php
getSpreadsheet(<$column>, <$value>, <$fields>);
```

Return array of all rows from spreadsheet. Optionally get results where column's value of `$column` equals `$value`.
If fields parameter is given, only those column will be returned in the row hash. Values in fields need to be comma separated.
It's a blend of two Sheetsu's API methods: `GET /` and `GET /:column/:value`

###Search

```php
search($array, <$fields>);
```
Returns array of all rows from spreadsheet where column's value equals given value.

```php
$object->search(['column1'=>'some value']);
```

###Create row(s)

```php
createRows($rows);
```
Creates row (or rows) from a given array. Keys should be column's names. Values should be row's values. Adds it to the end of spreadsheet. You can add rows in bulk in single request.

Returns *true* if success. Otherwise it throws a new exception.

```php

$newRow = [
	'column1'=>'value1',
	'column2'=>'value2',
	'column3'=>'value3'
];

try {
	$object->createRows($newRow);
	
} catch (Exception $e) {
    echo $e->getMessage();
}


```

###Update row(s)

```php
updateRows($column, $value, $newRow, <$overwrite>);
```
Updates whole row (or rows) with a given array. Keys should be column's names. Values should be row's values. All rows where column `$column` equals `$value` are updated. If `$overwrite` param equals true, updates the whole row. If you don't provide all fields, some of the fields will be empty. `$overwrite` is *false* by default so only provided columns are updated.

Returns *true* if success. Otherwise it throws a new exception.

```php

$column = 'column1';
$value = 'current column1 value';
$newRow = [
	'column1' => 'new value on column1',
	'column2' => 'new value on column2'
];

try{
	$object->updateRows($column, $value, $newRow);
	
} catch (Exception $e) {
    echo $e->getMessage();
}	
```

###Delete row(s)

```php
deleteRows($column, $value);
```
Deletes row(s) where column `$column` value equals `$value`. 

Returns *true* if success. Otherwise it throws a new exception.

```php

$column = 'column1';
$value = 'current column1 value';

try{
	$object->deleteRows($column, $value); 

} catch (Exception $e) {
    echo $e->getMessage();
}

```

##Disclaimer
Just made this for personal use. It's not an official class and I'm not part of the Sheetsu team.

##Questions, suggestions
Twitter: [@diego_iglesias](https://twitter.com/diego_iglesias)