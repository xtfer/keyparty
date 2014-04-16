
## About KeyParty

KeyParty is a simple key-value store using JSON files for storage and written
for PHP 5.4 and above.

*Example:*

```php
// Start KeyParty and add a JSON Jar to store data in.
$keyparty = new \Keyparty\KeyParty()->addJar('test');

// Create some data to store.
$my_data = array('foo', 'bar');

// Set the data
$keyparty->set('test', 'some_key', $my_data);

// You can also do it this way...
$jar = $keyparty->useJar('test');

$jar->insert('some_key', $my_data);
// or
$jar->update('some_key', $my_data);
// or
$jar->upsert('some_key', $my_data);

// Get your data back
$result = $keyparty->get('test', 'some_key');
// or
$result = $jar->select('some_key');

```

## Installation and dependencies

KeyParty requires Gaufrette, but only if you intend to use the default JSON
storage. This is not required if you plan to write your own adapter.

The easiest way to install all of these is using Composer. KeyParty is available
via Packagist.

```
{
"require": {
        "xtfer/keyparty": "1.0.*"
    }
}
```