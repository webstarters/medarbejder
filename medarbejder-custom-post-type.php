<?php

function medarbejder_custom_post_type() {

// Set UI labels for Custom Post Type
    $labels = array(
        'name'                => _x( 'Medarbejdere', 'Post Type General Name', 'webstarters' ),
        'singular_name'       => _x( 'Medarbejder', 'Post Type Singular Name', 'webstarters' ),
        'menu_name'           => __( 'Medarbejdere', 'webstarters' ),
        'parent_item_colon'   => __( 'Forældre medarbejder', 'webstarters' ),
        'all_items'           => __( 'Alle medarbejdere', 'webstarters' ),
        'view_item'           => __( 'Se medarbejder', 'webstarters' ),
        'add_new_item'        => __( 'Tilføj ny medarbejder', 'webstarters' ),
        'add_new'             => __( 'Tilføj ny medarbejder', 'webstarters' ),
        'edit_item'           => __( 'Redigere medarbejder', 'webstarters' ),
        'update_item'         => __( 'Opdatere medarbejder', 'webstarters' ),
        'search_items'        => __( 'Søg efter medarbejder', 'webstarters' ),
        'not_found'           => __( 'Not Found', 'webstarters' ),
        'not_found_in_trash'  => __( 'Not found in Trash', 'webstarters' ),
    );

// Set other options for Custom Post Type

    $args = array(
        'label'               => __( 'medarbejder', 'webstarters' ),
        'description'         => __( 'Custom post type til byggegrunde', 'webstarters' ),
        'labels'              => $labels,
        // Features this CPT supports in Post Editor
        'supports'            => array( 'title', 'thumbnail', 'custom-fields', ),
        // You can associate this CPT with a taxonomy or custom taxonomy.
        'taxonomies'          => array(),
        /* A hierarchical CPT is like Pages and can have
        * Parent and child items. A non-hierarchical CPT
        * is like Posts.
        */
        'hierarchical'        => false,
        'public'              => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 5,
        'can_export'          => true,
        'has_archive'         => false,
        'exclude_from_search' => false,
        'publicly_queryable'  => false,
        'capability_type'     => 'page',
    );

    // Registering your Custom Post Type
    register_post_type( 'medarbejder', $args );

}

/* Hook into the 'init' action so that the function
* Containing our post type registration is not
* unnecessarily executed.
*/

add_action( 'init', 'medarbejder_custom_post_type', 0 );


add_action('init', function () {

    $labels = [
        'name'                  => __('Kategorier'),
        'singular_name'         => __('Kategori'),
        'add_new'               => 'Tilføj kategori',
        'all_items'             => 'Alle kategorier',
        'add_new_item'          => 'Tilføj kategori',
        'edit_item'             => 'Rediger kategori',
        'new_item'              => 'Ny kategori',
        'view_item'             => 'Se kategori',
        'search_item'           => 'Søg efter kategori',
        'not_found'             => 'Ingen typer fundet',
        'not_found_in_trash'    => 'Ingen typer fundet i skraldespanden',
        'parent_item_colon'     => 'Forælder kategori',
    ];

    register_taxonomy(
        'medarbejder_cat',
        'medarbejder',
        [
            'labels'                => $labels,
            'rewrite'               => ['slug' => 'medarbejder_cat'],
            'hierarchical'          => true,
            'public'                => true,
            'has_archive'           => true,
            'publicly_queryable'    => false,
            'exclude_from_search'   => true,
        ]
    );
});


class wsMedarbejder extends WPBakeryShortCode {

    function __construct() {
        add_action('init', array($this, 'ws_medarbejder_mapping'));
        add_action('init', array($this, 'ws_stor_enkelt_medarbejder_mapping'));
        add_action('init', array($this, 'ws_lille_enkelt_medarbejder_mapping'));
        add_shortcode('list_medarbejder', array($this, 'shortcode_list_medarbejder'));
        add_shortcode('enkel_stor_medarbejder', array($this, 'shortcode_enkel_stor_medarbejder'));
        add_shortcode('enkel_lille_medarbejder', array($this, 'shortcode_enkel_lille_medarbejder'));
    }

    public function ws_medarbejder_mapping() {
        if (!defined('WPB_VC_VERSION')) {
                return;
        }

        $employee_categories = [];
        foreach (get_terms('medarbejder_cat') as $category) {
            array_push($employee_categories, $category->name);
        }

        vc_map([
            'name' => __('List medarbejdere', 'text-domain'),
            'base' => 'list_medarbejder',
            'description' => __('', 'text-domain'),
            'category' => __('Webstarters', 'text-domain'),
            'params' => [
                [
                    'type' => 'dropdown',
                    'heading' => __( 'Medarbejder kategorier', 'text-domain' ),
                    'description' => __('Eksempelvis: Projektleder Jylland & Fyn', 'text-domain'),
                    'param_name' => 'medarbejder-kategori',
                    'holder' => 'p',
                    'value' => $employee_categories,
                    'std' => '---',
                ],
            ],
        ]);
    }

    public function ws_stor_enkelt_medarbejder_mapping() {
        if (!defined('WPB_VC_VERSION')) {
                return;
        }

        $employee_categories = [];
        $args = array(
            'posts_per_page' => -1,
            'post_type' => 'medarbejder',
            'orderby' => 'date',
            'order' => 'DESC',
        );

        $posts = get_posts($args);
        foreach($posts as $post) {
            $employee_categories['id-'.(string)$post->ID] = $post->post_title;
        }

        vc_map([
            'name' => __('Enkel medarbejdere - Stor', 'text-domain'),
            'base' => 'enkel_stor_medarbejder',
            'description' => __('', 'text-domain'),
            'category' => __('Webstarters', 'text-domain'),
            'params' => [
                [
                    'type' => 'dropdown',
                    'heading' => __( 'Medarbejder', 'text-domain' ),
                    'description' => __('Eksempelvis: Hans Hansen', 'text-domain'),
                    'param_name' => 'medarbejder-id',
                    'holder' => 'p',
                    'value' => array_flip($employee_categories),
                    'std' => '---',
                ],
            ],
        ]);
    }

    public function ws_lille_enkelt_medarbejder_mapping() {
        if (!defined('WPB_VC_VERSION')) {
                return;
        }

        $employee_categories = [];
        $args = array(
            'posts_per_page' => -1,
            'post_type' => 'medarbejder',
            'orderby' => 'date',
            'order' => 'DESC',
        );

        $posts = get_posts($args);
        foreach($posts as $post) {
            $employee_categories['id-'.(string)$post->ID] = $post->post_title;
        }

        vc_map([
            'name' => __('Enkel medarbejdere - Lille', 'text-domain'),
            'base' => 'enkel_lille_medarbejder',
            'description' => __('', 'text-domain'),
            'category' => __('Webstarters', 'text-domain'),
            'params' => [
                [
                    'type' => 'dropdown',
                    'heading' => __( 'Medarbejder', 'text-domain' ),
                    'description' => __('Eksempelvis: Hans Hansen', 'text-domain'),
                    'param_name' => 'medarbejder-id',
                    'holder' => 'p',
                    'value' => array_flip($employee_categories),
                    'std' => '---',
                ],
            ],
        ]);
    }



    public function shortcode_list_medarbejder($atts) {


        extract(
            shortcode_atts(
                [
                    'medarbejder-kategori' => 'X',
                ],
                $atts
            )
        );

       $args = array(
            'posts_per_page' => -1,
            'post_type' => 'medarbejder',
            'orderby' => 'date',
            'order' => 'DESC',
            'tax_query' => array(
                array(
                    'taxonomy' => 'medarbejder_cat',
                    'field' => 'slug',
                    'terms' => $atts['medarbejder-kategori'],
                    'operator' => 'IN'
                )
            )
        );
        $posts = get_posts($args);

            $generate_shortcode .= '[vc_row type="in_container" equal_height="yes" class="webstarters-medarbejder-containers" full_screen_row_position="top" scene_position="top" text_color="dark" text_align="left" overlay_strength="0.3" shape_divider_position="bottom" shape_type=""]';

            foreach($posts as $post) {
                $medarbejder_navn = $post->post_title;
                $medarbejder_stilling = get_field('stilling',$post->ID);
                if(get_field('telefon',$post->ID)){
                    $medarbejder_telefon = "T: ".get_field('telefon',$post->ID);
                }

                if(get_field('mobil_telefon',$post->ID)){
                    $medarbejder_mobil_telefon = "M: ".get_field('mobil_telefon',$post->ID);
                }

                if(get_field('e-mail_adresse',$post->ID)){
                    $medarbejder_e_mail_adresse = get_field('e-mail_adresse',$post->ID);
                }

                $medarbejder_billede = get_post_thumbnail_id($post->ID);

                // placeholder billede til medarbejder
                if (empty($medarbejder_billede)) {
                    $medarbejder_billede = 2215;
                }

                $generate_shortcode .= '[vc_column top_margin="30" el_class="medarbejder-list-view-container" width="1/2" offset="vc_col-md-4 vc_col-xs-6" column_padding="no-extra-padding" column_padding_position="all" background_color_opacity="1" background_hover_color_opacity="1" column_shadow="none" column_border_radius="none"  tablet_text_alignment="default" phone_text_alignment="default" column_border_width="none" column_border_style="solid"]
                [image_with_animation custom_size="portfolio-thumb" image_url="'.$medarbejder_billede.'" alignment="" animation="Fade In" border_radius="none" box_shadow="none" max_width="100%"][vc_column_text]<strong>'.$medarbejder_navn.'</strong>
                    '.$medarbejder_stilling.'

                    '.$medarbejder_telefon.'
                    '.$medarbejder_mobil_telefon.'
                    '.$medarbejder_e_mail_adresse.'[/vc_column_text][/vc_column]';

                $medarbejder_mobil_telefon = "";
                $medarbejder_e_mail_adresse = "";
                $medarbejder_telefon = "";
                $medarbejder_stilling = "";
            }

            $generate_shortcode .= '[/vc_row]';


        return do_shortcode($generate_shortcode);
    }


    public function shortcode_enkel_stor_medarbejder($atts) {


        extract(
            shortcode_atts(
                [
                    'medarbejder-id' => 'X',
                ],
                $atts
            )
        );
       $string = str_replace('id-', '', $atts['medarbejder-id']);
       $args = array(
            'posts_per_page' => 1,
            'post_type' => 'medarbejder',
            'orderby' => 'date',
            'order' => 'DESC',
            'post__in' => array($string)
        );
        $posts = get_posts($args);

            $generate_shortcode .= '[vc_row type="in_container" class="enkel-store-medarbejder" equal_height="yes" full_screen_row_position="middle" bg_color="#f5f5f5" scene_position="center" text_color="dark" text_align="left" overlay_strength="0.3" shape_divider_position="bottom" shape_type=""]';

            foreach($posts as $post) {
                $medarbejder_navn = $post->post_title;
                $medarbejder_stilling = get_field('stilling',$post->ID);
                $medarbejder_telefon = "T: ".get_field('telefon',$post->ID);
                if(get_field('mobil_telefon',$post->ID)){
                    $medarbejder_mobil_telefon = "M: ".get_field('mobil_telefon',$post->ID);
                }
                $medarbejder_e_mail_adresse = get_field('e-mail_adresse',$post->ID);
                $medarbejder_billede = get_post_thumbnail_id($post->ID);

                // placeholder billede til medarbejder
                if (empty($medarbejder_billede)) {
                    $medarbejder_billede = 2215;
                }

                $generate_shortcode .= '[vc_column column_padding="padding-3-percent" column_padding_position="all" background_color_opacity="1" background_hover_color_opacity="1" column_shadow="none" column_border_radius="none" width="1/2"  offset="vc_col-md-8 vc_col-xs-6" tablet_text_alignment="default" phone_text_alignment="default" column_border_width="none" column_border_style="solid"][vc_column_text]<strong>'.$medarbejder_navn.'</strong>[/vc_column_text][vc_row_inner column_margin="default" text_align="left"][vc_column_inner column_padding="no-extra-padding" column_padding_position="all" background_color_opacity="1" background_hover_color_opacity="1" column_shadow="none" column_border_radius="none" width="1/2" column_border_width="none" column_border_style="solid"][vc_column_text]'.$medarbejder_stilling.'[/vc_column_text][/vc_column_inner][vc_column_inner column_padding="no-extra-padding" column_padding_position="all" background_color_opacity="1" background_hover_color_opacity="1" column_shadow="none" column_border_radius="none" width="1/2" column_border_width="none" column_border_style="solid"][vc_column_text]'.$medarbejder_telefon.'
'.$medarbejder_mobil_telefon.''.$medarbejder_e_mail_adresse.'[/vc_column_text][/vc_column_inner][/vc_row_inner][/vc_column]
[vc_column column_padding="no-extra-padding" column_padding_position="all"  background_image="'.$medarbejder_billede.'" enable_bg_scale="true" background_color_opacity="1" background_hover_color_opacity="1" column_shadow="none" column_border_radius="none" width="1/2"  offset="vc_col-md-4 vc_col-xs-6" tablet_text_alignment="default" phone_text_alignment="default" column_border_width="none" column_border_style="solid"]
[divider line_type="No Line" custom_height="300"][/vc_column]';

                $medarbejder_mobil_telefon = "";
                $medarbejder_e_mail_adresse = "";
                $medarbejder_telefon = "";
                $medarbejder_stilling = "";

            }

            $generate_shortcode .= '[/vc_row]';


        return do_shortcode($generate_shortcode);
    }


    public function shortcode_enkel_lille_medarbejder($atts) {


        extract(
            shortcode_atts(
                [
                    'medarbejder-id' => 'X',
                ],
                $atts
            )
        );

       $string = str_replace('id-', '', $atts['medarbejder-id']);
       $args = array(
            'posts_per_page' => 1,
            'post_type' => 'medarbejder',
            'orderby' => 'date',
            'order' => 'DESC',
            'post__in' => array($string)
        );
        $posts = get_posts($args);

            $generate_shortcode .= '[vc_row type="in_container" class="lille_medarbejder_container" full_screen_row_position="middle" equal_height="yes" content_placement="middle" bg_color="#f5f5f5" scene_position="center" text_color="dark" text_align="left" overlay_strength="0.3" shape_divider_position="bottom" shape_type=""]';

            foreach($posts as $post) {
                $medarbejder_navn = $post->post_title;
                $medarbejder_stilling = get_field('stilling',$post->ID);
                $medarbejder_telefon = "T: ".get_field('telefon',$post->ID);
                $medarbejder_mobil_telefon = "M: ".get_field('mobil_telefon',$post->ID);
                $medarbejder_e_mail_adresse = get_field('e-mail_adresse',$post->ID);
                $medarbejder_billede = get_post_thumbnail_id($post->ID);

                // placeholder billede til medarbejder
                if (empty($medarbejder_billede)) {
                    $medarbejder_billede = 2215;
                }

                $generate_shortcode .= '[vc_column column_padding="padding-4-percent" column_padding_position="all" background_color_opacity="1" background_hover_color_opacity="1" column_shadow="none" column_border_radius="none" width="1/2" tablet_text_alignment="default" phone_text_alignment="default" column_border_width="none" column_border_style="solid"][vc_column_text]<strong>'.$medarbejder_navn.'</strong>
'.$medarbejder_stilling.'

'.$medarbejder_telefon.'
'.$medarbejder_mobil_telefon.'
'.$medarbejder_e_mail_adresse.'[/vc_column_text][/vc_column][vc_column background_image="'.$medarbejder_billede.'" enable_bg_scale="true" column_padding="no-extra-padding" column_padding_position="all" background_color_opacity="1" background_hover_color_opacity="1" column_shadow="none" column_border_radius="none" width="1/2" tablet_text_alignment="default" phone_text_alignment="default" column_border_width="none" column_border_style="solid"][divider line_type="No Line" custom_height="150"][/vc_column]';


            $medarbejder_mobil_telefon = "";
            $medarbejder_e_mail_adresse = "";
            $medarbejder_telefon = "";
            $medarbejder_stilling = "";

            }

            $generate_shortcode .= '[/vc_row]';


        return do_shortcode($generate_shortcode);
    }


}

new wsMedarbejder();