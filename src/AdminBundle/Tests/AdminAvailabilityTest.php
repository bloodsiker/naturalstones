<?php

namespace AdminBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class AdminAvailabilityTest
 */
class AdminAvailabilityTest extends WebTestCase
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    private $client;

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

        $this->assertTrue($this->client->getResponse()->isRedirect('http://' . $this->client->getRequest()->getHost() . $redirectTo));
    }

    /**
     * @return array
     */
    public function adminUrlProvider()
    {
        return [
            ['/admin', '/ru/admin/dashboard?parmanent=1'],
            ['/ru/admin/dashboard', '/ru/admin/login'],
            ['/uk/admin/dashboard', '/uk/admin/login'],
            ['/en/admin/dashboard', '/en/admin/login'],
            ['/ru/admin/user/user/list', '/ru/admin/login'],
            ['/uk/admin/user/user/create', '/uk/admin/login'],
        ];
    }
}
