<?php

namespace Carsso\OAuth2\Client\Provider;

use League\OAuth2\Client\Tool\ArrayAccessorTrait;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class OvhcloudResourceOwner implements ResourceOwnerInterface
{
    use ArrayAccessorTrait;

    /**
     * Domain
     *
     * @var string
     */
    protected $domain;

    /**
     * Raw response
     *
     * @var array
     */
    protected $response;

    /**
     * Creates new resource owner.
     *
     * @param array $response
     */
    public function __construct(array $response = array())
    {
        $this->response = $response;
    }

    /**
     * Get resource owner id
     *
     * @return string|null
     */
    public function getId()
    {
        return $this->getNichandle();
    }

    /**
     * Get resource owner nichandle
     *
     * @return string|null
     */
    public function getNichandle()
    {
        return $this->getValueByKey($this->response, 'sub');
    }

    /**
     * Get resource owner email
     *
     * @return string|null
     */
    public function getEmail()
    {
        return $this->getValueByKey($this->response, 'email');
    }

    /**
     * Get resource owner name
     *
     * @return string|null
     */
    public function getFirstName()
    {
        return $this->getValueByKey($this->response, 'given_name');
    }

    /**
     * Get resource owner name
     *
     * @return string|null
     */
    public function getLastName()
    {
        return $this->getValueByKey($this->response, 'family_name');
    }

    /**
     * Get resource owner name
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->getFirstName().' '.$this->getLastName();
    }

    /**
     * Return all of the owner details available as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->response;
    }
}
