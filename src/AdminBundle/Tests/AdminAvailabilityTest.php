<?php

namespace AdminBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class AdminAvailabilityTest
 * @package AdminBundle\Tests
 */
class AdminAvailabilityTest extends WebTestCase
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    private $client;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->client = self::createClient();
    }

    /**
     * Redirect unauthorized users to login page
     *
     * @param string $url
     * @param string $redirectTo
     *
     * @dataProvider adminUrlProvider
     */
    public function testRedirectUnauthorizedUsersToLoginPage($url, $redirectTo)
    {
        $this->client->request('GET', $url);

        $this->assertTrue($this->client->getResponse()->isRedirect('http://'.$this->client->getRequest()->getHost().$redirectTo));
    }

    /**
     * @return array
     */
    public function adminUrlProvider()
    {
        return array(
            array('/admin', '/ru/admin/dashboard?parmanent=1'),
            array('/ru/admin/dashboard', '/ru/admin/login'),
            array('/uk/admin/dashboard', '/uk/admin/login'),
            array('/en/admin/dashboard', '/en/admin/login'),
            array('/ru/admin/user/user/list', '/ru/admin/login'),
            array('/uk/admin/user/user/create', '/uk/admin/login'),
        );
    }
}
