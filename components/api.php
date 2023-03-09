<?php

namespace gp_brief\components;

require_once 'Logger.php';
require_once 'brief.php';
require_once 'Notifier.php';

use gp_brief\components\Logger;
use gp_brief\components\Brief;
use gp_brief\components\Notifier;

class Api
{
	public $log;
	const DB_TOKEN_KEY = 'gp_1c_exchange_token';
	const TOKEN_LIFE_TIME = 3600;

	public function __construct()
	{
		$this->log = new Logger();
		add_action('rest_api_init',[$this,'init']);
		// add_action('skipToken',[$this,'deleteToten']);
	}

	public function init()
	{
		register_rest_route( 'gp-brief/v1','/pushBrief', [
			'methods' => 'POST',
			'callback' => [$this,'pushBrief'],
			'permission_callback' => [$this, 'checkBearer']
		]);
		register_rest_route( 'gp-brief/v1','/submitBrief', [
			'methods' => 'POST',
			'callback' => [$this,'submitBrief'],
			'permission_callback' => [$this, 'checkBearer']
		]);
	}

	public function submitBrief($request)
	{
		$data = $request->get_body();
		$token = $this->getAuthorizationToken($request->get_header('Authorization'));
		$breaf = new Brief($token);
		$postId = $breaf->getId();
		$notifier = new Notifier();
		$notifier->notifyByTelegram($data);
		$breaf->submit();
		return 'ok';
	}

	public function pushBrief($request)
	{
		$data = $request->get_body();
		$token = $this->getAuthorizationToken($request->get_header('Authorization'));
		$breaf = new Brief($token);
		$breaf->updateData($data);
		return 'ok';
	}

	public function checkBearer($request)
	{
		$autorization = explode(' ',$request->get_header('Authorization'),2);
		if (count($autorization) < 2){
			return false;
		}
		$autorizationType = $autorization[0];
		$autorizationToken = $autorization[1];

		return ( $autorizationType === 'Bearer' && $this->checkToken($autorizationToken) ) ? true : false; 
	}
	
	private function getAuthorizationToken($autorization): string
	{
		$autorization = explode(' ',$autorization,2);
		if (count($autorization) < 2){
			return '';
		}
		$autorizationToken = $autorization[1];
		return $autorizationToken;	
	}
	
	private function checkToken(string $token): bool
	{
		return true;
	}
	
}