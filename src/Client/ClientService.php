<?php

namespace Scp\WhmcsReseller\Client;

use Scp\Api\ApiKey;
use Scp\Client\Client;
use Scp\Client\ClientRepository;
use Scp\Support\Arr;
use Scp\WhmcsReseller\Whmcs\Whmcs;

class ClientService {
	const NOT_CHECKED = -1;

	/**
	 * @var ClientRepository
	 */
	protected $clients;

	/**
	 * @var Whmcs
	 */
	protected $whmcs;

	/**
	 * @var Client|int
	 */
	protected $client = self::NOT_CHECKED;

	/**
	 * @var ApiKey|null
	 */
	protected $apiKey;

	public function __construct(
		Whmcs $whmcs,
		ClientRepository $clients
	) {
		$this->whmcs = $whmcs;
		$this->clients = $clients;
	}

	/**
	 * Get Synergy information for current Client,
	 * and create the client on Synergy if they do not exist yet.
	 *
	 * @return Client
	 */
	public function getOrCreate() {
		return $this->client === self::NOT_CHECKED ? $this->create() : $this->client;
	}

	/**
	 * Create a new Client on Synergy,
	 * using the currently authed Client's information.
	 *
	 * @return Client
	 */
	public function create() {
		$params = $this->whmcs->getParams();

		return $this->client = $this->clients->create([
			'email' => $params['clientsdetails']['email'],
			'first' => $params['clientsdetails']['firstname'],
			'last' => $params['clientsdetails']['lastname'],
			'billing_id' => $params['userid'],
		]);
	}
}
