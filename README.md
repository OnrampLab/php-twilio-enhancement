# php-twilio-enhancement

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![CircleCI](https://circleci.com/gh/OnrampLab/php-twilio-enhancement.svg?style=shield)](https://circleci.com/gh/OnrampLab/php-twilio-enhancement)
[![Total Downloads](https://img.shields.io/packagist/dt/onramplab/php-twilio-enhancement.svg?style=flat-square)](https://packagist.org/packages/onramplab/php-twilio-enhancement)

If you want to test your twilio/sdk by using Twilio API fake response, you can use this package to make it.

## Requirements

- PHP >= 7.4;
- composer.

## Installation

```bash
composer require onramplab/php-twilio-enhancement
```

## Features

- Able to log Twilio API response
- Able to mock  API response for testing

## Tricks

### How to add custom HTTP Client to Twilio Rest Client

You can check out this document: [Call the Twilio REST API with a custom TwilioRestClient in PHP](https://www.twilio.com/docs/libraries/php/custom-http-clients-php).

Example code for Laravel:

```php
use Onramplab\TwilioEnhancement\CurlClient;
use Psr\Log\LoggerInterface;
use Twilio\Rest\Client;

$logger = app()->make(LoggerInterface::class);
$httpClient = new CurlClient([], $logger);
$twilio = new Client($sid, $token, null, null, $httpClient);
```

## Tech Features

- PSR-4 autoloading compliant structure;
- PSR-2 compliant code style;
- Unit-Testing with PHPUnit 6;
- Comprehensive guide and tutorial;
- Easy to use with any framework or even a plain php file;
- Useful tools for better code included.

## Useful Tools

## Running Tests:

    php vendor/bin/phpunit

 or

    composer test

## Code Sniffer Tool:

    php vendor/bin/phpcs --standard=PSR2 src/

 or

    composer psr2check

## Code Auto-fixer:

    composer psr2autofix
    composer insights:fix
    rector:fix

## Building Docs:

    php vendor/bin/phpdoc -d "src" -t "docs"

 or

    composer docs

## Changelog

To keep track, please refer to [CHANGELOG.md](https://github.com/Onramplab/php-twilio-enhancement/blob/master/CHANGELOG.md).

## Contributing

1. Fork it.
2. Create your feature branch (git checkout -b my-new-feature).
3. Make your changes.
4. Run the tests, adding new ones for your own code if necessary (phpunit).
5. Commit your changes (git commit -am 'Added some feature').
6. Push to the branch (git push origin my-new-feature).
7. Create new pull request.

Also please refer to [CONTRIBUTION.md](https://github.com/Onramplab/php-twilio-enhancement/blob/master/CONTRIBUTION.md).

## License

Please refer to [LICENSE](https://github.com/Onramplab/php-twilio-enhancement/blob/master/LICENSE).
