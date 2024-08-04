<?php

declare(strict_types=1);

namespace SimpleSAML\Module\memcacheMonitor\Controller;

use SimpleSAML\Configuration;
use SimpleSAML\Module\memcacheMonitor\MemcacheMonitor;
use SimpleSAML\Session;
use SimpleSAML\Utils;
use SimpleSAML\XHTML\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller class for the memcacheMonitor module.
 *
 * This class serves the different views available in the module.
 *
 * @package SimpleSAML\Module\memcacheMonitor
 */
class MemcacheMonitorController
{
    /** @var \SimpleSAML\Configuration */
    protected Configuration $config;

    /** @var \SimpleSAML\Session */
    protected Session $session;

    /** @var \SimpleSAML\Utils\Auth */
    protected $authUtils;


    /**
     * Controller constructor.
     *
     * It initializes the global configuration and auth source configuration for the controllers implemented here.
     *
     * @param \SimpleSAML\Configuration              $config The configuration to use by the controllers.
     * @param \SimpleSAML\Session                    $session The session to use by the controllers.
     *
     * @throws \Exception
     */
    public function __construct(
        Configuration $config,
        Session $session,
    ) {
        $this->config = $config;
        $this->session = $session;
        $this->authUtils = new Utils\Auth();
    }


    /**
     * Inject the \SimpleSAML\Utils\Auth dependency.
     *
     * @param \SimpleSAML\Utils\Auth $authUtils
     */
    public function setAuthUtils(Utils\Auth $authUtils): void
    {
        $this->authUtils = $authUtils;
    }


    /**
     * Show memcache info.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \SimpleSAML\XHTML\Template
     *   An HTML template or a redirection if we are not authenticated.
     */
    public function main(Request $request): Template
    {
        $this->authUtils->requireAdmin();

        $memcacheMonitor = new MemcacheMonitor($this->config);
        return $memcacheMonitor->renderStats();
    }
}
