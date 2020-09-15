<?php

use Webmozart\Assert\Assert;

/**
 * Hook to add the simple consenet admin module to the frontpage.
 *
 * @param array &$links  The links on the frontpage, split into sections.
 */
function memcacheMonitor_hook_frontpage(array &$links): void
{
    $links['config'][] = [
        'href' => SimpleSAML\Module::getModuleURL('memcacheMonitor/memcachestat.php'),
        'text' => '{memcacheMonitor:memcachestat:link_memcacheMonitor}',
    ];
}
