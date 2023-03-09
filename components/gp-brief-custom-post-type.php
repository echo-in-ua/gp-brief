<?php

namespace gp_brief\components;

class GPBriefCustomPostType
{
    public function __construct()
    {
        add_action('init',[$this, 'gpBriefPostType']);
        add_action('save_post',[$this, 'sevePostMetaBoxes'],10,3);
        add_filter('manage_gp_brief_posts_columns' , [$this,'filterCustomColums']);
        add_action('manage_gp_brief_posts_custom_column',[$this, 'addColumtToList'],10,2);
    }

    public function gpBriefPostType(): void
    {
        $labels = [
            'name' => 'Брифи',
            'singular_name' => 'Бриф',
            'add_new' => 'Додати бриф',
            'add_new_item' => 'Додати бриф',
            'edit_item' => 'Редактировать бриф',
            'new_item' => 'Новий бриф',
            'all_items' => 'Всі брифи',
            'view_item' => 'Переглянути бриф',
            'search_items' => 'Пошук брифів',
            'not_found' =>  'Брифів не знайдено',
            'not_found_in_trash' => 'В корзині брифів не знайдено', 
            'parent_item_colon' => '',
            'menu_name' => 'БРИФ',
        ];
        $args = [
            'labels' => $labels,
            'public' => false,
            'exclude_from_search' => true,
            'has_archive' => false,
            'show_ui' => true,
            'capability_type' => 'post',
            'hierarchical' => false,
            'rewrite' => array('slug' => 'brief'),
            'query_var' => true,
            'register_meta_box_cb' => [$this, 'addMetaBoxes'],
            'menu_icon' => 'dashicons-randomize',
            'supports' => [
                'title',
                'editor',
                'excerpt',
                'trackbacks',
                'custom-fields',
                'author',
                'page-attributes'
            ],
            'taxonomies' => ['category', 'post_tag']
        ];
        register_post_type( 'gp_brief', $args );
    }

    public function addMetaBoxes($post): void
    {
        add_meta_box('gp_brief_metabox_json','JSON', [$this, 'metaBoxContentJSON'],'gp_brief','advanced','high');
        add_meta_box('gp_brief_metabox_status','Статус', [$this, 'metaBoxContentStatus'],'gp_brief','advanced','high');
    }

    public function metaBoxContentJSON($post): void
    {
        $json = get_post_meta( $post->ID, '_brief_json', true );
        $jsonHTML = esc_js($json);
        echo '<input type="text" name="_brief_json" value="'.$jsonHTML.'" placeholder="Valid JSON"></input>';
    }

    public function metaBoxContentStatus($post): void
    {
        $customFields = get_post_custom($post->ID);
        $status = (isset($customFields['_brief_status'])) ? $customFields['_brief_status'][0] : '';
        $html='
            <select name="_brief_status">
              <option value="new" '.($status === "new" ||  $status === "" ? "selected" : "").'>Новое</option>
              <option value="in-progress" '.($status === "in-progress" ? "selected" : "").'>В работе</option>
              <option value="closed" '.($status === "closed" ? "selected" : "").'>Закрыто</option>
            </select>
        ';
        echo $html;
    }

    public function sevePostMetaBoxes($postId, $post, $update): void
    { 
        if (    $this->isNotGPBriefPostType($post) || 
                $this->isAutoSave() || 
                $this->isPostMoveToTrash($post) || 
                $this->isNewPost($update) 
            )
        {
            return;
        }
        if ( is_admin() )
        {
            update_post_meta($postId,'_brief_json',$_POST['_brief_json']);
            update_post_meta($postId,'_brief_status',sanitize_text_field($_POST['_brief_status'])); 
        }
        
        remove_action( 'save_post', [$this, 'sevePostMetaBoxes']);
            $json = get_post_meta( $postId, '_brief_json', true );
            $data = json_decode($json);
            $name = ( $data && property_exists($data, 'name') ) ? 'от '.$data->name : '';
            $mobile = ( $data && property_exists($data, 'mobile') && $data->mobile != '' ) ? '- '.$data->mobile : '';
            $post->post_title = 'Бриф '.$name. ' '.$mobile;
            $post->post_content = $this->generatePostContentFromJSON($data);
            wp_update_post( $post );
        add_action( 'save_post', [$this, 'sevePostMetaBoxes'] );

    }
    private function isNotGPBriefPostType($post)
    {
        return ( $post->post_type === 'gp_brief' ) ? false : true;
    }
    private function isAutoSave(): bool
    {
        return (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE );
    }
    private function isPostMoveToTrash($post): bool
    {
        return ( 'trash' === $post->post_status);
    }
    private function isNewPost($update): bool
    {
        return ( $update == false );
    }

    public function filterCustomColums($columns): array
    {
        $afterIndex = 1;
        $status = ['_brief_status' => 'Статус'];
        $columns = array_merge(array_slice($columns,0,$afterIndex+1), $status, array_slice($columns,$afterIndex+1));
        return $columns;
    }
    public function addColumtToList($columnKey, $postId)
    {
        if ($columnKey == '_brief_status') 
        {
            $status = get_post_meta($postId, '_brief_status', true);
            echo $status;
        }
    }

    private function generatePostContentFromJSON($data): string
    {
    	if ( is_null($data) )
    	{
    		return 'Нет данных брифа';    
    	}
        $table = '
        <table class="json-table" width="100%">
        ';
        foreach ($data as $key => $value) {
            $table .= '
            <tr valign="top">
            ';
            if ( ! is_numeric($key)) {
                $table .= '
                <td>
                    <strong>'. $key .':</strong>
                </td>
                <td>
                ';
            } else {
                $table .= '
                <td colspan="2">
                ';
            }
            if (is_object($value) || is_array($value)) {
                $table .= $this->generatePostContentFromJSON($value);
            } else {
                $table .= $value;
            }
            $table .= '
                </td>
            </tr>
            ';
        }
        $table .= '
        </table>
        ';
        return $table;
    }
}

?>