<?php

declare(strict_types=1);

use SimpleSAML\Locale\Translate;
use SimpleSAML\Module;
use SimpleSAML\XHTML\Template;

/**
 * Hook to add the memcacheMonitor module to the config page.
 *
 * @param \SimpleSAML\XHTML\Template &$template The template that we should alter in this hook.
 */
function memcacheMonitor_hook_configpage(Template &$template): void
{
    $template->data['links'][] = [
        'href' => Module::getModuleURL('memcacheMonitor/memcachestat.php'),
        'text' => Translate::noop('Memcache statistics'),
    ];

    $template->getLocalization()->addModuleDomain('memcacheMonitor');
}
