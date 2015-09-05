<?php
/*
	RedisCache.php - Redis cache middleware for Slim framework
	Copyright 2015 abouvier <abouvier@student.42.fr>

	Licensed under the Apache License, Version 2.0 (the "License");
	you may not use this file except in compliance with the License.
	You may obtain a copy of the License at

		http://www.apache.org/licenses/LICENSE-2.0

	Unless required by applicable law or agreed to in writing, software
	distributed under the License is distributed on an "AS IS" BASIS,
	WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
	See the License for the specific language governing permissions and
	limitations under the License.
*/

namespace Slim\Middleware;

use \Predis\ClientInterface;

class RedisCache extends \Slim\Middleware
{
	protected $client;
	protected $settings;

	public function __construct(ClientInterface $client, array $settings = [])
	{
		$this->client = $client;
		$this->settings = $settings;
	}

	public function call()
	{
		$app = $this->app;
		$env = $app->environment;
		$key = $env['SCRIPT_NAME'] . $env['PATH_INFO'];
		if (!empty($env['QUERY_STRING']))
			$key .= '?' . $env['QUERY_STRING'];
		$response = $app->response;

		if ($this->client->exists($key))
		{
			$response->setBody($this->client->get($key));
			return;
		}

		$this->next->call();

		if ($response->getStatus() == 200)
		{
			$this->client->set($key, $response->getBody());
			if (array_key_exists('timeout', $this->settings))
				$this->client->expire($key, $this->settings['timeout']);
		}
	}
}
