<?php

namespace Carsso\OAuth2\Client\Test\Provider;

use Carsso\OAuth2\Client\Provider\OvhcloudResourceOwner;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class OvhcloudResourceOwnerTest extends TestCase
{
    public function testNameIsFirstNameAndLastName(): void
    {
        $given_name = uniqid();
        $family_name = uniqid();

        $user = new OvhcloudResourceOwner(['given_name' => $given_name, 'family_name' => $family_name]);

        $name = $user->getName();

        $this->assertEquals($given_name.' '.$family_name, $name);
    }
}
