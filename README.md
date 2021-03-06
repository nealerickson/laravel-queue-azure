Azure Queue driver for Laravel
======================

#Installation

Add the following to your `composer.json` file to include this package as a dependency
```json
{
    "require": {
        "heedworks/laravel-queue-azure": "dev-master"
    },          
    "repositories": [
        {
            "type": "pear",
            "url": "http://pear.php.net"
        }
    ],
    "minimum-stability": "dev"
}
```

Register the Azure Queue service provider by adding it to the providers array in the `app/config/app.php` file.
```
Heedworks\LaravelQueueAzure\LaravelQueueAzureServiceProvider
```

#Configuration

Configure your connection in `app/config/queue.php`:
```php
'azure' => array(
    'driver' => 'azure',    
    'queue' => 'your-queue-name',
    'protocol' => 'your-protocol-choice', // http or https
    'account' => 'your-account-name',
    'key' => 'your-key'
)
```

#Usage
For information on Laravel Queues please refer to the official documentation: http://laravel.com/docs/queues
