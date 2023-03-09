<?php

namespace gp_brief\components;

require_once (WP_PLUGIN_DIR.'/gp-brief/components/Logger.php');
require_once (WP_PLUGIN_DIR.'/gp-brief/components/brief.php');

use gp_brief\components\Logger;
use gp_brief\components\Brief;

class Form
{
	private $targetPage, $log, $brief, $shordcode='gp_brief';
	public function __construct($targetPage=109)
    {
    	$this->log = new Logger();
    	$this->targetPage = $targetPage;

        add_shortcode( 'gp_brief', [$this, 'loadApp'] );
		add_action('wp_enqueue_scripts',[$this, 'loadReactApp']);
        add_action('init',[$this,'addUTMLabels']);
        
    }

    public function findPageWhereUsedShortcode(): array
    {
    	$pages = get_pages();
  		$pattern = get_shortcode_regex();

		foreach($pages as $page) 
		{
			if ( 	preg_match_all( '/'. $pattern .'/s', $page->post_content, $matches )
		      		&& array_key_exists( 2, $matches )
		      		&& in_array( $this->shordcode, $matches[2] ) 
		      	)
		    {
		      $pagesWithShortcode[] = $page->ID;
		    }
		}
		return $pagesWithShortcode;

    }
   	public function loadApp(): string
   	{
   		$html = '<div id="gp-brief-app" style="max-width: 100%;"></div>';
        return $html;	
   	}

    public function addUTMLabels(): void
    {
    	global $wp;
    	$utm = ['utm_source','utm_medium','utm_campaign'];
    	array_walk($utm, function ($var) use ($wp) { $wp->add_query_var($var); });
    } 
    public function setQueryParams(array $queryVars ): array
    {
    	$this->log->write_log($queryVars);
    	return $queryVars;
    }
    public function loadReactApp(): void
    {

    	if ( !in_array(get_the_ID() , $this->findPageWhereUsedShortcode() ) )
    	{
			return;    
		}

		// Setting path variables.
		$pluginAppDirUrl =plugin_dir_url( __FILE__ );
		$category = $pluginAppDirUrl.'object-shooting/';
		$reactAppBuild = $category .'build/';
		$manifestUrl = $reactAppBuild. 'asset-manifest.json';

		error_log('$manifestUrl = '.$manifestUrl,true);
		
		$request = file_get_contents( $manifestUrl );
		if( !$request )
		{
			error_log(print_r('Can`t load '.$manifestUrl,true));
			return;	
		}
		// Convert json to php array.
		$filesData = json_decode($request);
		if($filesData === null)
		{
			error_log(print_r('No data found in '.$manifestUrl,true));
			return;
		}

		if(!property_exists($filesData,'entrypoints'))
		{
			error_log(print_r('No "entrypoints" in '.$manifestUrl,true));
			return;	
		}
		// Get assets links.
		$assetsFiles = $filesData->entrypoints;
		
		$jsFiles = array_filter($assetsFiles,[$this, 'filterJsFiles']);
		$cssFiles = array_filter($assetsFiles,[$this, 'filterCssFiles']);

		// Load css files.
		foreach ($cssFiles as $index => $cssFile){
			wp_enqueue_style('gp-brief-'.$index, $reactAppBuild . $cssFile);
		}

		// Load js files.
		foreach ($jsFiles as $index => $jsFile){
			wp_enqueue_script('gp-brief-'.$index, $reactAppBuild . $jsFile, array(), 1, true);
		}

		$this->log->write_log(get_query_var('utm_campaign','false'));
		
		$utms = ['utm_source','utm_medium','utm_campaign'];
		$tags = array_map(function($utm) { return get_query_var($utm,false); }, $utms);
		$tags = array_filter($tags,function($tag) {return ( $tag ) ? true : false; } );
		// Variables for app use.
		$this->log->write_log($tags);
		$this->brief = new Brief(0,$tags);
		wp_localize_script('gp-brief-0', 'gpBriefPlugin',
			[
				'appSelector' => '#gp-brief-app',
				'token' => $this->brief->getToken()
			]
		);
		
    }

    public function filterJsFiles (string $fileString): string
    {
		return pathinfo($fileString, PATHINFO_EXTENSION) === 'js';
	}
	public function filterCssFiles (string $fileString): string 
	{
		return pathinfo( $fileString, PATHINFO_EXTENSION ) === 'css';
	}
}