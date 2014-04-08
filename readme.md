
## About KeyParty

KeyParty is a simple key-value store using JSON files for storage and written
for PHP 5.4 and above.

*Example:*

```php
$keyparty = new \Keyparty\KeyParty('test');
$my_data = array('foo', 'bar');
$keyparty->set('some_key', $my_data);
```

## Installation and dependencies

KeyParty requires Pimple for dependency injection and Gaufrette for
file system actions.

The easiest way to install all of these is using Composer. KeyParty is available
via Packagist.

```
{
"require": {
        "xtfer/keyparty": "1.0.*"
    }
}
```

## Usage

```php

use Keyparty\Keyparty;

// Create an new Database file (equivalent to a table).
$keyparty = new KeyParty('test');

// Empty the database.
$keyparty->emptyDatabase();

// Test both get and set.
$data = array(
  'integer' => 1,
  'float' => 1.34,
  'string' => 'some string',
  'array' => array('one', 'two', 'three'),
  'object' => new \stdClass(),
);

// Write the data.
$keyparty->set('1', $data);

// Fetch it again.
$item = $keyparty->get('1');

// Change the value and add it again under a new key.
$data['another_key'] = 'foo';

// Write a new object.
$keyparty->set('2', $data);

// Get all the objects.
$items = $keyparty->getKeys();

// Delete the first object.
$keyparty->delete('1');

// Completely remove the database from the filesystem.
$keyparty->removeDatabase();
```