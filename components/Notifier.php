<?php

namespace gp_brief\components;

require_once 'Logger.php';

use gp_brief\components\Logger;

class Notifier
{
	private $apiToken;
	private $chatID;
	private $log;
	public function __construct() {
		$this->apiToken = esc_attr( get_option('telegram_api_token') );
		$this->chatID = esc_attr( get_option('telegram_chat_id') );
		$this->log = new Logger();
	}
	

	private function generateMessage ($json) {

		$brief = json_decode($json);

	  	$msg='Получен бриф.'.PHP_EOL;
	  	$msg.='<b>Имя: </b>'.$brief->name.PHP_EOL;
	  	$msg.='<b>Email: </b>'.$brief->email.PHP_EOL;
	  	$msg.='<b>Телефон: </b>'.$brief->mobile.PHP_EOL;
	  	$msg.='<b>Instagram: </b>'.$brief->instagram.PHP_EOL;
	  	$msg.='<b>Месенджер: </b>'.$brief->messenger.PHP_EOL;
	  	$msg.='<b>Не звонить: </b>'.$this->boolToSymbol( $brief->dontCall ).PHP_EOL;
	  	$msg.='<b>Описание товара: </b>'.$brief->itemsDescription.PHP_EOL;
	  	$msg.='<b>Количество товара: </b>'.$brief->itemsCount.PHP_EOL;
	  	$msg.='<b>Количество ракурсов: </b>'.$brief->foreshortening.PHP_EOL;
		$msg.='<b>Платформа: </b>'.implode(',',$brief->targetPlatform).PHP_EOL;
		$msg.='<b>Детали: </b>'.$brief->platformDetails.PHP_EOL;
		$msg.='<b>Референсы: </b>'.$brief->references.PHP_EOL;
		$msg.='<b>Срочная сьемка: </b>'.$this->boolToSymbol( $brief->urgentShooting ).PHP_EOL;	  	

	  	return $msg;
	}

	private function boolToSymbol(bool $val): string
	{
		return ( $val ) ? '✅' : '➖';
	}
	private function pushTelegramm($msg) {
		$data = [
		    'chat_id' => $this->chatID,
		    'text' => $msg,
		    'parse_mode' => 'HTML',
		];
		$response = file_get_contents("https://api.telegram.org/bot$this->apiToken/sendMessage?" . http_build_query($data) );
		return $response;
	}

	public function notifyByTelegram($json) {
		$msg = $this->generateMessage($json);
		// $this->log->write_log($msg);
		$this->pushTelegramm($msg);
	}
}

