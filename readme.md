
## About KeyParty

KeyParty is a simple key-value store using JSON files for storage and written
for PHP 5.4 and above.

```php
// Start KeyParty and add a JSON Jar to store data in.
$keyparty = new \Keyparty\KeyParty()->addJar('test');

// Create some data to store.
$my_data = array('foo', 'bar');

// Set the data
$keyparty->set('test', 'some_key', $my_data);
```

## Features

* Simple interface (get(), set(), empty() etc) for adding and removing data from KV stores
* Swappable backends ("jars") (use multiple KV stores using the same interface)
* Swappable caching and data validation backends
* JSON flat-file KV store included
* Good test coverage

## Full example

*Example:*

```php
// Start KeyParty and add a JSON Jar to store data in.
$keyparty = new \Keyparty\KeyParty()->addJar('test');

// Create some data to store.
$my_data = array('foo', 'bar');

// Set the data
$keyparty->set('test', 'some_key', $my_data);

// Set data without overwriting existing data.
// This will throw a RecordExistsException...
$keyparty->set('test', 'some_key', $my_data);

// You can also do it this way, however this won't use Object Converters, so you may 
lose some functionality, such as the ability to return properly classed objects from
the JSON store.
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
        "xtfer/keyparty": "0.3.*"
    }
}
```

## About the JSON Jar

By default, KeyParty will store your data in a flat-file JSON data store. This store is designed for rapid prototyping, simplicity, and portability. 

- Flat-file, one file per KV store.
- Ability to store and recreate objects similar to PHP serialize(), when using the provided ObjectConverter (i.e. the default configuration)
- Auto-creates KV stores on-the-fly

The JSON Jar has some limitations:

- Data is stored in files on disk, and is only cached per request. You shouldn't
hit the disk twice for two reads on the same store (from the same KeyParty instance), but there is currently no other caching layer.
- Every read loads the entire file
- Every insert loads and writes the entire file
- The store is not transaction safe. There is no logging and no roll-back.
- JSON keys cannot be integers. There is no serial key concept. All items must have a valid non-integer key.


## Other Key-Value stores

KeyParty uses two classes to define a Key-Value store, a JarType and a StoreAdapter. The JarType defines the Jar to KeyParty, returning the correct Serializer, Cache, Validator and Store Adapter. The Store Adapter handles the actual creation and removal of store instances and data.

If you wish to create your own type and adapter, look at the JsonStoreAdapter and the JsonJarType. Note that the current class structure may get a clean-up in the near future.

Pull requests for new store types are welcome.

Once you've added the correct classes, you must register your store with KeyParty using the registerStore() method.

## Disclaimer

No liability is accepted for loss of data as a result of using KeyParty and any
packaged key-value store. You use KeyParty entirely at your own risk.
