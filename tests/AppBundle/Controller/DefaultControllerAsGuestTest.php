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

        // Check database queries
        // Need at least one query to fetch the polls but the table should be empty
        $this->assertEquals(1, $client->getProfile()->getCollector('db')->getQueryCount());
    }

    public function testNonExistingPoll()
    {
        $client = static::createClient();
        $client->enableProfiler();

        $crawler = $client->request('GET', '/poll/100');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());

        $this->assertEquals(1, $client->getProfile()->getCollector('db')->getQueryCount());
    }

    public function testRegistration()
    {
        $client = static::createClient();
        $client->enableProfiler();

        $crawler = $client->request('GET', '/registration');

        // Check content
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('User registration', $crawler->filter('h2')->text());

        // Check database queries - No database access required
        $this->assertEquals(0, $client->getProfile()->getCollector('db')->getQueryCount());
    }

    public function testLogin()
    {
        $client = static::createClient();
        $client->enableProfiler();

        $crawler = $client->request('GET', '/login');

        // Check content
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Login', $crawler->filter('h2')->text());

        // Check database queries - No database access required
        $this->assertEquals(0, $client->getProfile()->getCollector('db')->getQueryCount());
    }

    public function testLogout()
    {
        $client = static::createClient();
        $client->enableProfiler();

        $crawler = $client->request('GET', '/logout');

        // Check content
        $this->assertTrue($client->getResponse()->isRedirect('/'));

        // Check database queries - No database access required
        $this->assertEquals(0, $client->getProfile()->getCollector('db')->getQueryCount());
    }

    public function testCreatePoll()
    {
        $client = static::createClient();
        $client->enableProfiler();

        $crawler = $client->request('GET', '/create');

        // Check content
        $this->assertTrue($client->getResponse()->isRedirect('/'));

        // Check database queries - No database access required
        $this->assertEquals(0, $client->getProfile()->getCollector('db')->getQueryCount());
    }

    public function testVote()
    {
        $client = static::createClient();
        $client->enableProfiler();

        $crawler = $client->request('GET', '/vote');

        // Check content
        $this->assertTrue($client->getResponse()->isRedirect('/'));

        // Check database queries - No database access required
        $this->assertEquals(0, $client->getProfile()->getCollector('db')->getQueryCount());
    }

    public function testVoteAsGuest()
    {
        $client = static::createClient();
        $client->enableProfiler();

        $crawler = $client->request('GET', '/vote/0/1');

        // Check content
        $this->assertTrue($client->getResponse()->isRedirect('/'));

        // Check database queries - No database access required
        $this->assertEquals(0, $client->getProfile()->getCollector('db')->getQueryCount());
    }
}
