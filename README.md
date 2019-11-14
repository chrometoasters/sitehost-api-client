# Simple PHP client for SiteHost's API

## Overview

This is a work in progress piece of code that may or may not get extended further as/when needed.

## API documentation

[SiteHost's API documentation](https://docs.sitehost.nz/api/)

## Features

- Get API info
- Get cloud stack info
- Update cloud stack info
- Get API job info

## Installation

`composer install chrometoaster/sitehost-api-client:~0.1`

## Example usage

It is recommended to define the API key and the client ID as environment variables, e.g. through your cloud container
environment variables configuration or via your virtual host configuration.

```php
use Chrometoaster\SiteHost\API\Client as SiteHostAPIClient;

$env_api_key   = getenv('SITEHOST_API_KEY');
$env_client_id = getenv('SITEHOST_CLIENT_ID');

// create a client
$client = new SiteHostAPIClient($env_api_key, $env_client_id);

// read cloud stack info
$info = $client->getStackInfo('ex-myserver', 'abc123def456ab12');

// check the response is valid and has data
if ($info->isValid()) {
    // work with the information here
    ...
    ... 
}
```

## TODO

- Implement methods and handling for other API endpoints
- Add tests.

## Licence

BSD-3-Clause, see the [licence](licence.md) for details.

## Contributing

In general, please be nice to each other. For more details see the [Code of Conduct](code-of-conduct.md).

### Code guidelines

This project follows the standards defined in:

* [PSR-1](http://www.php-fig.org/psr/psr-1/)
* [PSR-2](http://www.php-fig.org/psr/psr-2/)
* [RSR-4](http://www.php-fig.org/psr/psr-4/)

Before submitting a PR, please run the script below to lint and update the code according to the standards above.

```bash
composer run full-check
```
