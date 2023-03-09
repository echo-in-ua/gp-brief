<?php

namespace gp_brief\components;
require_once 'Logger.php';

use gp_brief\components\Logger;

class Brief
{
	private $log;
	private $token;
	private $postId;
	private $service, $tags;

	const TOKENS_KEY = 'gp_brief_tokens';
	
	public function __construct($token = 0,$tags=[], $service='object-shooting')
	{
		$this->log = new Logger();
		$this->token = $token;
		$this->tags = $tags;
		$this->service = $service;

		$this->findBriefOrCreateNew();
		
	}

	private function setTaxonomies(): void
	{
		$termsAndTaxonomies = array_map(function($term) {
			return wp_insert_term( $term, 'post_tag');
		},$this->tags);
		wp_set_object_terms( $this->postId, $this->tags, 'post_tag', false );
		wp_set_object_terms( $this->postId, $this->service, 'category', false );	
	}
	public function updateData(string $json): void
	{
		$postData = [
			'ID' => $this->postId,
			'meta_input' => [ '_brief_json' => $json ]
		];
		wp_update_post($postData);
		
		$tokens = json_decode(get_option( self::TOKENS_KEY ), true);
    	$tokens[$this->token]['updatedTimestamp'] = time(); 
    	update_option(self::TOKENS_KEY,json_encode($tokens));
	}

	public function submit(): void
	{
		$tokens = json_decode(get_option( self::TOKENS_KEY ), true);
    	$tokens[$this->token]['updatedTimestamp'] = time();
    	$tokens[$this->token]['submited'] = true; 
    	update_option(self::TOKENS_KEY,json_encode($tokens));
	}

	public function getId(): int
	{
		return $this->postId;
	}

	public function getToken(): string
	{
		return $this->token;
	}

	private function findBriefOrCreateNew()
	{
		if ( $this->token === 0 ) 
		{
			$this->createNewBrief();
		} else 
		{
			$this->findBriefByToken();		
		} 

	}
	private function findBriefByToken()
	{
		$tokens = json_decode( get_option( self::TOKENS_KEY ), true);
		if ( $tokens && isset( $tokens[$this->token]) )
		{
			$this->postId = $tokens[$this->token]['postId'];
		} else
		{
			throw new \Exception('Can`t find brief for token = "'.$this->token.'".', 1);
			
		}
	}

	private function createNewBrief(): void
	{
		$postData = [
			'post_type' => 'gp_brief',
			'post_title' => 'Пустой бриф',
			'post_content' => 'запрос на бриф …',
			'post_status' => 'publish',
			'meta_input' => [ 
				'_brief_json' => '',
				'_brief_status' => 'new' 
			]
		]; 
		$postId = wp_insert_post( $postData, true );
		$this->postId = $postId;
		$this->setTaxonomies();
		$this->addNewTokenForBrief();
		$this->log->write_log('Add new brief with "ID" = '.$postId.'.');

	}

	private function addNewTokenForBrief(): void
	{
		$postId = $this->postId;
		$this->generateToken();
		$token = $this->token;
		switch ( get_option( self::TOKENS_KEY ) ) {
	        case false:
	        	$time = time(); 
	        	$tokens = 
	        		[
		        		$token => 
		        			[
		        				'postId' => $postId,
		        				'createdTimestamp' => $time ,
		        				'updatedTimestamp' => $time,
		        				'submited' => false,
		        				'service' => 'object-shooting'
		        			] 
	        		];

	        	add_option(self::TOKENS_KEY,json_encode($tokens));
	        	$this->log->write_log('Set new tokens "'.json_encode($tokens).'".');
	            break;
	        case true:
	        	$tokens = json_decode(get_option( self::TOKENS_KEY ), true);
	        	$time = time(); 
	        	$tokens[$token] = [
	    				'postId' => $postId,
	    				'createdTimestamp' => $time,
		        		'updatedTimestamp' => $time,
		        		'submited' => false,
	    				'service' => 'object-shooting'
	    			]; 
	        	update_option(self::TOKENS_KEY,json_encode($tokens));
	        	$this->log->write_log('Add new token "'.json_encode($tokens[$token]).'".');
	            break;   
	    }
	}

	private function generateToken()
	{
		$token = openssl_random_pseudo_bytes(16);
		$token = bin2hex($token);
		$this->token = $token;
	}
}
