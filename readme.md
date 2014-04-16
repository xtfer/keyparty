
## About KeyParty

KeyParty is a simple, pluggable key-value store wrapper for PHP 5.4 and above. It comes with a native KV store (or "jar") using JSON files.

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
        "xtfer/keyparty": "0.2.*"
    }
}
```

## About the JSON Jar

By default, KeyParty will store your data in a flat-file JSON data store. This store is designed for rapid prototyping, simplicity, and portability. As such, it has some limitations:
- Data is stored in files on disk, and is only cached per request. You shouldn't
hit the disk twice for two reads on the same store (from the same KeyParty instance), but there is currently no other caching layer.
- Every read loads the entire file
- Every insert loads and writes the entire file
- The store is not transaction safe. There is no logging and no roll-back.
- JSON keys cannot be integers. There is no serial key concept. All items must have a valid non-integer key.
- Objects are stored without object information, and are returned as stdClass(). If you want to return the original objects, you need to serialize them into a string first.

## Other Key-Value stores

KeyParty uses two classes to define a Key-Value store, a JarType and a StoreAdapter. The JarType defines the Jar to KeyParty, returning the correct Serializer, Cache, Validator and Store Adapter. The Store Adapter handles the actual creation and removal of store instances and data.

If you wish to create your own type and adapter, look at the JsonStoreAdapter and the JsonJarType. Note that the current class structure may get a clean-up in the near future.

Pull requests for new store types are welcome.

Once you've added the correct classes, you must register your store with KeyParty using the registerStore() method.

## Disclaimer

No liability is accepted for loss of data as a result of using KeyParty and any
packaged key-value store. You use KeyParty entirely at your own risk.