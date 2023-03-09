<?php

namespace gp_brief\components;

class GPBriefSettings
{
    public function __construct()
    {
        add_action('admin_menu', [$this,'addConfigSubMenue']);
        add_action('admin_menu',[$this,'addSettings']);
    }

    public function addSettings(){
        register_setting('gp_brief_settings_group','telegram_api_token');
        register_setting('gp_brief_settings_group','telegram_chat_id');
        add_settings_section('gp_brief_telegram_options','Налаштування сповіщення в телеграмм.',[$this,'telegramOptionsRender'],'gp_brief_telegram_options_page');
        
        add_settings_field('api_token','Telegram API token', [$this, 'apiTokenFieldRender'],'gp_brief_telegram_options_page', 'gp_brief_telegram_options');

        add_settings_field('chat_id','Telegram chat ID', [$this, 'chatIdFieldRender'],'gp_brief_telegram_options_page', 'gp_brief_telegram_options');
    }

    public function apiTokenFieldRender()
    {
        $apiToken = esc_attr( get_option('telegram_api_token') );
        $html = '<input type="text" name="telegram_api_token" value="'.$apiToken.'" placeholder="Telegram API token" /> ';
        echo $html;
    }

    public function chatIdFieldRender()
    {
        $chatId = esc_attr( get_option('telegram_chat_id') );
        $html = '<input type="text" name="telegram_chat_id" value="'.$chatId .'" placeholder="Telegram chat id"/> ';
        echo $html;
    }
    public function telegramOptionsRender(): void
    {
        
    }
    public function addConfigSubMenue(){
        $parent_slug = 'edit.php?post_type=gp_brief';
        $page_title = 'Settings';
        $menu_title = 'Налаштування';
        $capability = 'manage_options';
        $menu_slug = 'gp_brief_settings';
        $callback = [$this, 'configurationSubMenuRender'];
        $position = null;

        add_submenu_page( $parent_slug, $page_title, $menu_title, $capability,  $menu_slug,  $callback, $position );
    }
    public function configurationSubMenuRender (): void
    {
        settings_errors();
        $html ='<form method="post" action="options.php">';
        echo $html;
        settings_fields( 'gp_brief_settings_group' );
        do_settings_sections('gp_brief_telegram_options_page');
        submit_button();
        $html='</form>';
        echo $html;

    }
}
