<?php

namespace Scp\WhmcsReseller\Server\Usage;

use Scp\Api\ApiError;
use Scp\Server\Server;
use Scp\Server\ServerRepository;
use Scp\WhmcsReseller\Database\Database;
use Scp\WhmcsReseller\LogFactory;

/**
 * Testing the UsageUpdater:
 * 
 * ssh whmcstes@scp-whmcs /opt/cpanel/ea-php72/root/usr/bin/php /home/whmcstes/public_html/crons/cron.php do --UpdateServerUsage -vvv
 */
class UsageUpdater {
  /**
   * @var LogFactory
   */
  private $log;

  /**
   * @var UsageFormatter
   */
  private $format;

  /**
   * @var ServerRepository
   */
  private $servers;

  /**
   * @var Database
   */
  private $database;

  public function __construct(
    Database $database, LogFactory $log, UsageFormatter $format, ServerRepository $servers
  ) {
    $this->log = $log;
    $this->format = $format;
    $this->servers = $servers;
    $this->database = $database;
  }

  /**
   * @return bool
   */
  public function runAndLogErrors() {
    try {
      $this->run();

      return true;
    } catch (ApiError $exc) {
      $this->log->activity(
        'SynergyCP: Error running usage update: %s',
        $exc->getMessage()
      );
    }

    return false;
  }

  /**
   * @return bool
   */
  public function run() {
    $fail = $this->syncBandwidthUsage();

    $this->log->activity('SynergyCP: Completed usage update');

    return !$fail;
  }

  private function syncBandwidthUsage() {
    $fail = false;
    $this->servers->query()->where('integration_id', 'me')->chunk(
      100,
      function ($servers) use (&$fail) {
        $servers->map(
          function (Server $server) use (&$fail) {
            if (!$server->billing) {
              return;
            }
            try {
              $this->database
                ->table('tblhosting')
                ->where('id', $server->billing->id)
                ->whereNotIn('domainstatus', ['Terminated'])
                ->update($this->prepareUpdates($server));
            } catch (\Exception $exc) {
              $this->log->activity(
                'SynergyCP: Usage Update failed: %s',
                $exc->getMessage()
              );
              $fail = true;
            }
          }
        );
      }
    );

    return $fail;
  }

  /**
   * @param Server $server
   *
   * @return array
   */
  private function prepareUpdates(Server $server) {
    $usage = $server->usage;
    $access = $server->access;
    $status = $access && $access->is_active ? 'Active' : 'Suspended';

    return [
      'domainstatus' => $status,
      'bwusage' => $usage ? $this->format->bitsToMB($usage->used, 3) : 0,
      'bwlimit' => $usage ? $this->format->bitsToMB($usage->max, 3) : 0,
    ];
  }
}
