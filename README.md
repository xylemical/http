# RFC 8941 Structured Fields

Provides for parsing and serializing structured fields according to [RFC8941](https://www.rfc-editor.org/rfc/rfc8941.html).

## Install

The recommended way to install this library is [through composer](http://getcomposer.org).

```sh
composer require xylemical/http
```

## Usage

```php
<?php

use Xylemical\Http;

use Xylemical\Http\StructuredField\Field;

$request = ...; // Using Psr\Http\Message\RequestInterface

$header = Field::parse('list', $request->getHeaderLine('Accept'));
$header = Field::parse('item', $request->getHeaderLine('Origin'));
$header = Field::parse('dictionary', $request->getHeaderLine());
$value = $header->toInternal(); // All structured fields support toInternal().

// Example of a list header.
$header = Field::fromInternal(['application/json', 'application/xml']);

print $header[0];     // prints 'application/json';
print count($header); // prints 2.

// Example of a dictionary header.
$header = Field::fromInternal(['q' => 'foo', 't' => 'bar']);

print_r($header['q']); // prints ['foo']

// Manipulate the parameters for the 'q' map entry.
$header->get('q')
  ->getParameters()
  ->set('test' => 'bar');

print_r($header['q']);  // ['foo', '@attributes' => ['test' => 'bar']]

print $header->get('q')->toInternal(); // prints 'foo'

// Example of creation of the 'Content-Type' header. 
$header = Item::fromIternal([
  'application/json', 
  '@attributes' => ['content-encoding' => 'utf-8']
]);

// When using Item.
print $header->getValue();  // prints 'application/json'
print (string)$header;      // prints 'application/json; content-encoding=utf-8'
print $header->serialize(); // will throw SyntaxException if data in header invalid. 

```

## License

MIT, see LICENSE.
