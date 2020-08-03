<?php

declare(strict_types=1);

namespace SimpleSAML\Module\memcacheMonitor;

use Exception;
use SimpleSAML\Assert\Assert;
use SimpleSAML\Configuration;
use SimpleSAML\Locale\Translate;
use SimpleSAML\Logger;
use SimpleSAML\Memcache;
use SimpleSAML\XHTML\Template;

/**
 * Handles interactions with SSP's memcacheMonitor system.
 */
class MemcacheMonitor
{
    /**
     * The configuration that holds the memcache configuration
     * @var \SimpleSAML\Configuration
     */
    private $config;

    /**
     * An associative array with keys matching the stats, and values pointing to the formatting function for that key
     * @var array
     */
    private $formats = [
        'bytes' => [self::class, 'humanreadable'],
        'bytes_read' => [self::class, 'humanreadable'],
        'bytes_written' => [self::class, 'humanreadable'],
        'limit_maxbytes' => [self::class, 'humanreadable'],
        'time' => [self::class, 'tdate'],
        'uptime' => [self::class, 'hours'],
    ];


    /**
     * @param \SimpleSAML\Configuration $config The configuration to use.
     */
    public function __construct(Configuration $config)
    {
        $this->config = $config;
    }


    /**
     * Retrieve stats, render them into a human readable page
     */
    public function renderStats(): Template
    {
        $statsraw = Memcache::getStats();
        $stats = $statsraw;

        foreach ($stats as $key => &$entry) {
            if (array_key_exists($key, $this->formats)) {
                $func = $this->formats[$key];
                foreach ($entry as $k => $val) {
                    $entry[$k] = call_user_func($func, $val);
                }
            }
        }

        $t = new Template($this->config, 'memcacheMonitor:memcachestat.twig');
        $rowTitles = [
            'accepting_conns' => Translate::noop('{memcacheMonitor:memcachestat:accepting_conns}'),
            'auth_cmds' => Translate::noop('{memcacheMonitor:memcachestat:auth_cmds}'),
            'auth_errors' => Translate::noop('{memcacheMonitor:memcachestat:auth_errors}'),
            'bytes' => Translate::noop('{memcacheMonitor:memcachestat:bytes}'),
            'bytes_read' => Translate::noop('{memcacheMonitor:memcachestat:bytes_read}'),
            'bytes_written' => Translate::noop('{memcacheMonitor:memcachestat:bytes_written}'),
            'cas_badval' => Translate::noop('{memcacheMonitor:memcachestat:cas_badval}'),
            'cas_hits' => Translate::noop('{memcacheMonitor:memcachestat:cas_hits}'),
            'cas_misses' => Translate::noop('{memcacheMonitor:memcachestat:cas_misses}'),
            'cmd_flush' => Translate::noop('{memcacheMonitor:memcachestat:cmd_flush}'),
            'cmd_get' => Translate::noop('{memcacheMonitor:memcachestat:cmd_get}'),
            'cmd_set' => Translate::noop('{memcacheMonitor:memcachestat:cmd_set}'),
            'cmd_touch' => Translate::noop('{memcacheMonitor:memcachestat:cmd_touch}'),
            'connection_structures' => Translate::noop('{memcacheMonitor:memcachestat:connection_structures}'),
            'conn_yields' => Translate::noop('{memcacheMonitor:memcachestat:conn_yields}'),
            'curr_connections' => Translate::noop('{memcacheMonitor:memcachestat:curr_connections}'),
            'curr_items' => Translate::noop('{memcacheMonitor:memcachestat:curr_items}'),
            'decr_hits' => Translate::noop('{memcacheMonitor:memcachestat:decr_hits}'),
            'decr_misses' => Translate::noop('{memcacheMonitor:memcachestat:decr_misses}'),
            'delete_hits' => Translate::noop('{memcacheMonitor:memcachestat:delete_hits}'),
            'delete_misses' => Translate::noop('{memcacheMonitor:memcachestat:delete_misses}'),
            'expired_unfetched' => Translate::noop('{memcacheMonitor:memcachestat:expired_unfetched}'),
            'evicted_unfetched' => Translate::noop('{memcacheMonitor:memcachestat:evicted_unfetched}'),
            'evictions' => Translate::noop('{memcacheMonitor:memcachestat:evictions}'),
            'get_hits' => Translate::noop('{memcacheMonitor:memcachestat:get_hits}'),
            'get_misses' => Translate::noop('{memcacheMonitor:memcachestat:get_misses}'),
            'hash_bytes' => Translate::noop('{memcacheMonitor:memcachestat:hash_bytes}'),
            'hash_is_expanding' => Translate::noop('{memcacheMonitor:memcachestat:hash_is_expanding}'),
            'hash_power_level' => Translate::noop('{memcacheMonitor:memcachestat:hash_power_level}'),
            'incr_hits' => Translate::noop('{memcacheMonitor:memcachestat:incr_hits}'),
            'incr_misses' => Translate::noop('{memcacheMonitor:memcachestat:incr_misses}'),
            'libevent' => Translate::noop('{memcacheMonitor:memcachestat:libevent}'),
            'limit_maxbytes' => Translate::noop('{memcacheMonitor:memcachestat:limit_maxbytes}'),
            'listen_disabled_num' => Translate::noop('{memcacheMonitor:memcachestat:listen_disabled_num}'),
            'pid' => Translate::noop('{memcacheMonitor:memcachestat:pid}'),
            'pointer_size' => Translate::noop('{memcacheMonitor:memcachestat:pointer_size}'),
            'reclaimed' => Translate::noop('{memcacheMonitor:memcachestat:reclaimed}'),
            'reserved_fds' => Translate::noop('{memcacheMonitor:memcachestat:reserved_fds}'),
            'rusage_system' => Translate::noop('{memcacheMonitor:memcachestat:rusage_system}'),
            'rusage_user' => Translate::noop('{memcacheMonitor:memcachestat:rusage_user}'),
            'threads' => Translate::noop('{memcacheMonitor:memcachestat:threads}'),
            'time' => Translate::noop('{memcacheMonitor:memcachestat:time}'),
            'total_connections' => Translate::noop('{memcacheMonitor:memcachestat:total_connections}'),
            'total_items' => Translate::noop('{memcacheMonitor:memcachestat:total_items}'),
            'touch_hits' => Translate::noop('{memcacheMonitor:memcachestat:touch_hits}'),
            'touch_misses' => Translate::noop('{memcacheMonitor:memcachestat:touch_misses}'),
            'uptime' => Translate::noop('{memcacheMonitor:memcachestat:uptime}'),
            'version' => Translate::noop('{memcacheMonitor:memcachestat:version}'),
        ];

        // Identify column headings
        $colTitles = [];
        foreach ($stats as $rowTitle => $rowData) {
            foreach ($rowData as $colTitle => $foo) {
                if (!in_array($colTitle, $colTitles, true)) {
                    $colTitles[] = $colTitle;
                }
            }
        }

        if (array_key_exists('bytes', $statsraw) && array_key_exists('limit_maxbytes', $statsraw)) {
            $usage = [];
            $maxpix = 400;
            foreach ($statsraw['bytes'] as $key => $row_data) {
                $pix = floor($statsraw['bytes'][$key] * $maxpix / $statsraw['limit_maxbytes'][$key]);
                $usage[$key] = $pix . 'px';
            }
            $t->data['maxpix'] = $maxpix . 'px';
            $t->data['usage'] = $usage;
        }

        $t->data['title'] = 'Memcache stats';
        $t->data['rowTitles'] = $rowTitles;
        $t->data['colTitles'] = $colTitles;
        $t->data['statsraw'] = $statsraw;
        $t->data['table'] = $stats;

        return $t;
    }


    /**
     * @param int $input
     * @return string
     */
    private function tdate(int $input): string
    {
        return date(DATE_RFC822, $input);
    }


    /**
     * @param int $input
     * @return string
     */
    private function hours(int $input): string
    {
        if ($input < 60) {
            return number_format($input, 2) . ' sec';
        }

        if ($input < 60 * 60) {
            return number_format(($input / 60), 2) . ' min';
        }

        if ($input < 24 * 60 * 60) {
            return number_format(($input / (60 * 60)), 2) . ' hours';
        }

        return number_format($input / (24 * 60 * 60), 2) . ' days';
    }


    /**
     * @param int $input
     * @return string
     */
    private function humanreadable(int $input): string
    {
        $output = "";
        $input = abs($input);

        if ($input >= (1024 * 1024 * 1024 * 1024 * 1024 * 1024 * 1024 * 100)) {
            $output = sprintf("%5ldEi", $input / (1024 * 1024 * 1024 * 1024 * 1024 * 1024));
        } elseif ($input >= (1024 * 1024 * 1024 * 1024 * 1024 * 1024 * 10)) {
            $output = sprintf("%5.1fEi", $input / (1024.0 * 1024.0 * 1024.0 * 1024.0 * 1024.0 * 1024.0));
        } elseif ($input >= (1024 * 1024 * 1024 * 1024 * 1024 * 1024)) {
            $output = sprintf("%5.2fEi", $input / (1024.0 * 1024.0 * 1024.0 * 1024.0 * 1024.0 * 1024.0));
        } elseif ($input >= (1024 * 1024 * 1024 * 1024 * 1024 * 100)) {
            $output = sprintf("%5ldPi", $input / (1024 * 1024 * 1024 * 1024 * 1024));
        } elseif ($input >= (1024 * 1024 * 1024 * 1024 * 1024 * 10)) {
            $output = sprintf("%5.1fPi", $input / (1024.0 * 1024.0 * 1024.0 * 1024.0 * 1024.0));
        } elseif ($input >= (1024 * 1024 * 1024 * 1024 * 1024)) {
            $output = sprintf("%5.2fPi", $input / (1024.0 * 1024.0 * 1024.0 * 1024.0 * 1024.0));
        } elseif ($input >= (1024 * 1024 * 1024 * 1024 * 100)) {
            $output = sprintf("%5ldTi", $input / (1024 * 1024 * 1024 * 1024));
        } elseif ($input >= (1024 * 1024 * 1024 * 1024 * 10)) {
            $output = sprintf("%5.1fTi", $input / (1024.0 * 1024.0 * 1024.0 * 1024.0));
        } elseif ($input >= (1024 * 1024 * 1024 * 1024)) {
            $output = sprintf("%5.2fTi", $input / (1024.0 * 1024.0 * 1024.0 * 1024.0));
        } elseif ($input >= (1024 * 1024 * 1024 * 100)) {
            $output = sprintf("%5ldGi", $input / (1024 * 1024 * 1024));
        } elseif ($input >= (1024 * 1024 * 1024 * 10)) {
            $output = sprintf("%5.1fGi", $input / (1024.0 * 1024.0 * 1024.0));
        } elseif ($input >= (1024 * 1024 * 1024)) {
            $output = sprintf("%5.2fGi", $input / (1024.0 * 1024.0 * 1024.0));
        } elseif ($input >= (1024 * 1024 * 100)) {
            $output = sprintf("%5ldMi", $input / (1024 * 1024));
        } elseif ($input >= (1024 * 1024 * 10)) {
            $output = sprintf("%5.1fM", $input / (1024.0 * 1024.0));
        } elseif ($input >= (1024 * 1024)) {
            $output = sprintf("%5.2fMi", $input / (1024.0 * 1024.0));
        } elseif ($input >= (1024 * 100)) {
            $output = sprintf("%5ldKi", $input / 1024);
        } elseif ($input >= (1024 * 10)) {
            $output = sprintf("%5.1fKi", $input / 1024.0);
        } elseif ($input >= (1024)) {
            $output = sprintf("%5.2fKi", $input / 1024.0);
        } else {
            $output = sprintf("%5ld", $input);
        }

        return $output;
    }
}
