# OVHcloud Provider for OAuth 2.0 Client
[![Source Code](https://img.shields.io/badge/source-carsso/oauth2--ovhcloud-blue.svg?style=flat-square)](https://github.com/carsso/oauth2-ovhcloud)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://github.com/carsso/oauth2-ovhcloud/blob/master/LICENSE)
[![Build Status](https://img.shields.io/github/workflow/status/carsso/oauth2-ovhcloud/CI?label=CI&logo=github&style=flat-square)](https://github.com/carsso/oauth2-ovhcloud/actions?query=workflow%3ACI)
[![Codecov Code Coverage](https://img.shields.io/codecov/c/gh/carsso/oauth2-ovhcloud?label=codecov&logo=codecov&style=flat-square)](https://codecov.io/gh/carsso/oauth2-ovhcloud)
[![Total Downloads](https://img.shields.io/packagist/dt/carsso/oauth2-ovhcloud.svg?style=flat-square)](https://packagist.org/packages/carsso/oauth2-ovhcloud)

This package provides OVHcloud OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

## Installation

To install, use composer:

```
composer require carsso/oauth2-ovhcloud
```

## Usage

Usage is the same as The League's OAuth client, using `\Carsso\OAuth2\Client\Provider\Ovhcloud` as the provider.

### Authorization Code Flow

```php
$provider = new Carsso\OAuth2\Client\Provider\Ovhcloud([
    'endpoint'          => '{ovhcloud-endpoint}',
    'clientId'          => '{ovhcloud-client-id}',
    'clientSecret'      => '{ovhcloud-client-secret}',
    'redirectUri'       => 'https://example.com/callback-url',
]);

if (!isset($_GET['code'])) {

    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl();
    $_SESSION['oauth2state'] = $provider->getState();
    header('Location: '.$authUrl);
    exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

    unset($_SESSION['oauth2state']);
    exit('Invalid state');

} else {

    // Try to get an access token (using the authorization code grant)
    $token = $provider->getAccessToken('authorization_code', [
        'code' => $_GET['code']
    ]);

    // Optional: Now you have a token you can look up a users profile data
    try {

        // We got an access token, let's now get the user's details
        $user = $provider->getResourceOwner($token);

        // Use these details to create a new profile
        printf('Hello %s!', $user->getNickname());

    } catch (Exception $e) {

        // Failed to get user details
        exit('Oh dear...');
    }

    // Use this to interact with an API on the users behalf
    echo $token->getToken();
}
```

### Managing Scopes

When creating your OVHcloud authorization URL, you can specify the state and scopes your application may authorize.

```php
$options = [
    'state' => 'OPTIONAL_CUSTOM_CONFIGURED_STATE',
    'scope' => ['openid', 'profile', 'email', 'all'] // array or string
];

$authorizationUrl = $provider->getAuthorizationUrl($options);
```

Here are the default scopes the provider is using :

- openid
- profile
- email
- all


## OVHcloud API calls

Since the OVHcloud API URL can vary, you can use the `getAuthenticatedApiRequest()` method of the provider (or `getApiRequest()` for unauthenticated calls).

```php
$request = $provider->getAuthenticatedApiRequest(
    \Carsso\OAuth2\Client\Provider\Ovhcloud::METHOD_GET,
    '/me',
    $token
);
$response = $provider->getParsedResponse($request);
```

## Testing

``` bash
$ ./vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](https://github.com/carsso/oauth2-ovhcloud/blob/master/CONTRIBUTING.md) for details.


## Credits

- [Germain Carré](https://github.com/carsso)
- [All Contributors](https://github.com/carsso/oauth2-ovhcloud/contributors)


## License

The MIT License (MIT). Please see [License File](https://github.com/carsso/oauth2-ovhcloud/blob/master/LICENSE) for more information.
