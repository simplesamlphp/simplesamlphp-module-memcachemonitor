<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Module\memcacheMonitor\Controller;

use PHPUnit\Framework\TestCase;
use SimpleSAML\Configuration;
use SimpleSAML\Error;
use SimpleSAML\Module\memcacheMonitor\Controller\MemcacheMonitorController;
use SimpleSAML\Session;
use SimpleSAML\Utils;
use SimpleSAML\XHTML\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Set of tests for the controllers in the "memcacheMonitor" module.
 *
 * @package SimpleSAML\Test
 */
class memcacheMonitorTest extends TestCase
{
    /** @var \SimpleSAML\Configuration */
    protected $config;

    /** @var \SimpleSAML\Session */
    protected $session;

    /** @var \SimpleSAML\Utils\Auth */
    protected $authUtils = Utils\Auth::class;


    /**
     * Set up for each test.
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->config = Configuration::loadFromArray(
            [
                'module.enable' => ['memcacheMonitor' => true],
                'memcache_store.servers' => []
            ],
            '[ARRAY]',
            'simplesaml'
        );

        $this->session = Session::getSessionFromRequest();


        Configuration::setPreLoadedConfig(
            Configuration::loadFromArray(
                [
                    'admin' => ['core:AdminPassword'],
                ],
                '[ARRAY]',
                'simplesaml'
            ),
            'authsources.php',
            'simplesaml'
        );

        $this->authUtils = new class () extends Utils\Auth {
            public static function requireAdmin(): void
            {
                // stub
            }
        };
    }


    /**
     * @return void
     */
    public function testAuthenticated(): void
    {
        $_SERVER['REQUEST_URI'] = '/module.php/memcacheMonitor/';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';

        $request = Request::create(
            '/',
            'GET'
        );

        $c = new MemcacheMonitorController($this->config, $this->session);
        $c->setAuthUtils($this->authUtils);
        $response = $c->main($request);

        $this->assertTrue($response->isSuccessful());
    }


    /**
     * @return void
     */
    public function testUnauthenticated(): void
    {
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';

        $request = Request::create(
            '/',
            'GET'
        );

        $c = new MemcacheMonitorController($this->config, $this->session);

        $this->expectException(Error\Exception::class);
        $this->expectExceptionMessage('Cannot find "admin" auth source, and admin privileges are required.');

        $response = $c->main($request);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertTrue($response->isRedirection());
    }
}
