<?php
/**
 * CampaignMonitor strategy for Opauth
 *
 * Based on work by U-Zyn Chua (http://uzyn.com)
 *
 * More information on Opauth: http://opauth.org
 *
 * @author       Timm Stokke <timm@stokke.me>
 * @link         http://opauth.org
 * @package      Opauth.CampaignMonitorStrategy
 * @license      MIT License
 */

class CampaignMonitorStrategy extends OpauthStrategy {

	/**
	 * Compulsory config keys, listed as unassociative arrays
	 */
	public $expects = array('client_id', 'client_secret', 'scope');

	/**
	 * Optional config keys, without predefining any default values.
	 */
	public $optionals = array('redirect_uri', 'scope', 'state');

	/**
	 * Optional config keys with respective default values, listed as associative arrays
	 * eg. array('scope' => 'email');
	 */
	public $defaults = array(
		'redirect_uri' => '{complete_url_to_strategy}oauth2callback'
	);

	/**
	 * Auth request
	 */
	public function request() {
		$url = 'https://api.createsend.com/oauth';
		$params = array(
			'type' => 'web_server',
			'client_id' => $this->strategy['client_id'],
			'redirect_uri' => $this->strategy['redirect_uri'],
			'scope' => $this->strategy['scope']
		);

		foreach ($this->optionals as $key) {
			if (!empty($this->strategy[$key])) $params[$key] = $this->strategy[$key];
		}

		$this->clientGet($url, $params);
	}

	/**
	 * Internal callback, after OAuth
	 */
	public function oauth2callback() {
		if (array_key_exists('code', $_GET) && !empty($_GET['code'])) {
			$code = $_GET['code'];
			$url = 'https://api.createsend.com/oauth/token';

			$params = array(
				'code' => $code,
				'client_id' => $this->strategy['client_id'],
				'client_secret' => $this->strategy['client_secret'],
				'redirect_uri' => $this->strategy['redirect_uri'],
				'grant_type' => 'authorization_code',
			);
			if (!empty($this->strategy['state'])) $params['state'] = $this->strategy['state'];

			$response = $this->serverPost($url, $params, null, $headers);

			//parse_str($response, $results);
			$results = json_decode($response, true);

			if (!empty($results) && !empty($results['access_token'])) {

				$client = $this->clientInfo($results['access_token']);

				$this->auth = array(
					'uid' => $client['EmailAddress'],
					'info' => array(),
					'credentials' => array(
						'token' => $results['access_token']
					),
					'raw' => $client
				);

				$this->mapProfile($client, 'EmailAddress', 'uid');
				$this->mapProfile($client, 'Name', 'info.name');
				$this->mapProfile($client, 'EmailAddress', 'info.email');
				$this->callback();
			}
			else {
				$error = array(
					'code' => 'access_token_error',
					'message' => 'Failed when attempting to obtain access token',
					'raw' => array(
						'response' => $response,
						'headers' => $headers
					)
				);

				$this->errorCallback($error);
			}
		}
		else {
			$error = array(
				'code' => 'oauth2callback_error',
				'raw' => $_GET
			);

			$this->errorCallback($error);
		}
	}

	/**
	 * Queries CampaignMonitor API for client info
	 *
	 * @param string $access_token
	 * @return array Parsed JSON results
	 */
	private function clientInfo($access_token) {

		// Query for client details:
		$options['http']['header'] = 'Authorization: Bearer: '.$access_token;
		$client = $this->serverGet('https://api.createsend.com/api/v3.1/me.json', [], $options, $headers);

		if (!empty($client)) {
			return $this->recursiveGetObjectVars(json_decode($client));
		}
		else {
			$error = array(
				'code' => 'userinfo_error',
				'message' => 'Failed when attempting to query CampaignMonitor API for account information',
				'raw' => array(
					'response' => $clients,
					'headers' => $headers
				)
			);

			$this->errorCallback($error);
		}
	}
}
