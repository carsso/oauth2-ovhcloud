<?php

namespace Carsso\OAuth2\Client\Provider;

use Carsso\OAuth2\Client\Provider\Exception\OvhcloudIdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use UnexpectedValueException;

class Ovhcloud extends AbstractProvider
{
    use BearerAuthorizationTrait;

    /**
     * Urls to communicate with OVHcloud API
     *
     * @var array
     */
    public static $endpoints = [
        'ovh-eu'        => [
            'apiDomain' => 'https://eu.api.ovh.com/1.0',
            'domain'    => 'https://www.ovh.com',
        ],
        'ovh-ca'        => [
            'apiDomain' => 'https://ca.api.ovh.com/1.0',
            'domain'    => 'https://ca.ovh.com',
        ],
        'ovh-us'        => [
            'apiDomain' => 'https://api.us.ovhcloud.com/1.0',
            'domain'    => 'https://us.ovhcloud.com',
        ],
    ];

    /**
     * Domain
     *
     * @var string
     */
    public $domain;

    /**
     * Api domain
     *
     * @var string
     */
    public $apiDomain;

    /**
     * @inheritdoc
     */
    public function __construct(array $options = [], array $collaborators = [])
    {
        if (empty($options['endpoint'])) {
            throw new UnexpectedValueException("Missing endpoint");
        }
        if (!isset(self::$endpoints[$options['endpoint']])) {
            throw new UnexpectedValueException(sprintf(
                "Invalid endpoint: %s",
                $options['endpoint']
            ));
        }
        $endpoint = self::$endpoints[$options['endpoint']];
        $this->domain = $endpoint['domain'];
        $this->apiDomain = $endpoint['apiDomain'];

        parent::__construct($options, $collaborators);
    }

    /**
     * Returns a PSR-7 request instance that is not authenticated.
     *
     * @param  string $method
     * @param  string $path
     * @param  array $options
     * @return RequestInterface
     */
    public function getApiRequest($method, $path, array $options = [])
    {
        return $this->getRequest($method, $this->apiDomain.$path, $options);
    }

    /**
     * Returns an authenticated PSR-7 request instance.
     *
     * @param  string $method
     * @param  string $path
     * @param  AccessTokenInterface|string $token
     * @param  array $options Any of "headers", "body", and "protocolVersion".
     * @return RequestInterface
     */
    public function getAuthenticatedApiRequest($method, $path, $token, array $options = [])
    {
        return $this->getAuthenticatedRequest($method, $this->apiDomain.$path, $token, $options);
    }

    /**
     * Get authorization url to begin OAuth flow
     *
     * @return string
     */
    public function getBaseAuthorizationUrl()
    {
        return $this->domain . '/auth/oauth2/authorize';
    }

    /**
     * Get access token url to retrieve token
     *
     * @param array $params
     *
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->domain . '/auth/oauth2/token';
    }

    /**
     * Get provider url to fetch user details
     *
     * @param AccessToken $token
     *
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return $this->domain . '/auth/oauth2/user';
    }

    /**
     * Get the default scopes used by this provider.
     *
     * This should not be a complete list of all scopes, but the minimum
     * required for the provider user interface!
     *
     * @return array
     */
    protected function getDefaultScopes()
    {
        return ['openid', 'profile', 'email', 'all'];
    }

    /**
     * @inheritdoc
     */
    protected function getScopeSeparator()
    {
        return ' ';
    }

    /**
     * Check a provider response for errors.
     *
     * @throws IdentityProviderException
     * @param  ResponseInterface $response
     * @param  array             $data     Parsed response data
     * @return void
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if ($response->getStatusCode() >= 400) {
            throw OvhcloudIdentityProviderException::clientException($response, $data);
        } elseif (isset($data['error'])) {
            throw OvhcloudIdentityProviderException::oauthException($response, $data);
        }
    }

    /**
     * Generate a user object from a successful user details request.
     *
     * @param  array       $response
     * @param  AccessToken $token
     * @return \League\OAuth2\Client\Provider\ResourceOwnerInterface
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new OvhcloudResourceOwner($response);
    }
}
