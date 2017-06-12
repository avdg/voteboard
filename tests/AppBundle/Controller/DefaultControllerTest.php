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
        DatabasePrimer::truncateAll(self::$kernel);
    }

    public function testIndex()
    {
        $client = static::createClient();
        $client->enableProfiler();

        $crawler = $client->request('GET', '/');

        // Check content
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Hello guest,', $crawler->filter('.container')->text());

        // Check database queries - Need at least one to fetch the polls
        $this->assertGreaterThan(0, $client->getProfile()->getCollector('db')->getQueryCount());
    }

    public function testLogin()
    {
        $client = static::createClient();
        $client->enableProfiler();

        $crawler = $client->request('GET', '/login');

        // Check content
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Voting board', $crawler->filter('h1')->text());
        $this->assertContains('Login', $crawler->filter('h2')->text());

        // Check database queries - No database access required
        $this->assertEquals(0, $client->getProfile()->getCollector('db')->getQueryCount());
    }

    public function testRegistration()
    {
        $client = static::createClient();
        $client->enableProfiler();

        $crawler = $client->request('GET', '/registration');

        // Check content
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Voting board', $crawler->filter('h1')->text());
        $this->assertContains('User registration', $crawler->filter('h2')->text());

        // Check database queries - No database access required
        $this->assertEquals(0, $client->getProfile()->getCollector('db')->getQueryCount());
    }
}
