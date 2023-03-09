<?php
/**
 * Plugin Name: GP Brief
 * Plugin URI:  
 * Description: Add breif collect
 * Version:     1.1.0
 * Author:      Echo GP
 * Author URI:  https://github.com/echo-in-ua
 */

defined ('ABSPATH') or die();

require_once 'components/gp-brief-custom-post-type.php';
require_once 'components/api.php';
require_once 'front/form.php';
require_once 'components/EmptyBriefCleaner.php';
require_once 'components/Logger.php';
require_once 'components/GPBriefSettings.php';

use gp_brief\components\GPBriefCustomPostType;
use gp_brief\components\Api;
use gp_brief\components\Form;
use gp_brief\components\EmptyBriefCleaner;
use gp_brief\components\Logger;
use gp_brief\components\GPBriefSettings;

class GPBrief
{
    private static $instance = null;
    private $logger;

    private function __construct()
    {
        new GPBriefCustomPostType();
        new Form();
        new Api();
        new EmptyBriefCleaner();
        new GPBriefSettings();


        $this->logger = new Logger();
    }
    
    public function test()
    {
        // $cleaner = new EmptyBriefCleaner();
        // $cleaner->clean();
        $slug = get_post_field( 'slug', get_post() );
        $this->logger->write_log($slug,true);
    }

    
    
    static function getInstance(): GPBrief
    {
        if (self::$instance == null)
            self::$instance = new GPBrief();
        return self::$instance;
    } 
}

add_action('plugins_loaded', function ()  {
   $app = GPBrief::getInstance();
   // $app->test();
});