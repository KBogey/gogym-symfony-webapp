<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BasicTest extends WebTestCase
{
    public function testEnvironmentIsOk(): void
    {
        $this->assertTrue(true);
    }
}