<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\AppBundle\DatabasePrimer;

class DefaultControllerTest extends WebTestCase
{
    public function setUp()
    {
        self::bootKernel();

        DatabasePrimer::prime(self::$kernel);
    }

    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Hello guest,', $crawler->filter('.container')->text());
    }

    public function testLogin()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Voting board', $crawler->filter('h1')->text());
        $this->assertContains('Login', $crawler->filter('h2')->text());
    }

    public function testRegistration()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/registration');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Voting board', $crawler->filter('h1')->text());
        $this->assertContains('User registration', $crawler->filter('h2')->text());
    }
}
