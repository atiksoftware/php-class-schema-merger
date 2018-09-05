# PHP Array Merger with Schema
PHP array migrate,replace or fix with schema and with default data

----------
## Installation

### Using Composer

```sh
composer require atiksoftware/php-class-schema-merger
```

```php
require __DIR__.'/../vendor/autoload.php';

use \Atiksoftware\Schema\Merger;
$schemaMerger = new Merger();
```
#### _Example_
```php
    $schemaMerger->setSchema([
        "_id" => [
            "_type"    => "string",
            "_default" => "5f5e100"
        ],
        "name" => [
            "_type"    => "string",
            "_default" => "Yeni başlık",
            "_format"  => "fullname"
        ],
        "title" => [
            "_type"    => "array",
            "TR" => [
                "_type"    => "string",
                "_default" => "Türkçe Başlık",
                "_format"  => "upfirst"
            ],
            "EN" => [
                "_type"    => "string",
                "_default" => "English Title"
            ],
        ], 
        "tags" => [
            "_type"    => "array",
            "_default" => [ ],
        ],
        "date" => [
            "_type"    => "array",
            "edit" => [
                "_type"    => "integer",
                "_default" => time()
            ]
        ],
        "admin" => [
            "_type"    => "boolean",
            "_default" => false
        ],
        "age" => [
            "_type"    => "int",
            "_default" => 15,
            "_min"     => 5,
            "_max"     => 35,
        ],
    ]);

    $item = [
        "name"  => "Mansur atik",
        "title" => [
            "TR" => "Nasıl"
        ],
        "admin" => 1,
        "age"   => 434
    ];

    $result = $schemaMerger->Migrate($item);
    var_dump($result);
```
 
