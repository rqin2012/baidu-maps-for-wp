<?php
/*
Plugin Name: Baidu maps for wp
Plugin URI: http://www.baidu.com/
Description: Declares a plugin that will create a custom post type displaying maps.
Version: 1.0
Author: Ruoyan Qin
Author URI: http://www.baidu.com/
License: 
*/

add_action( 'init', 'create_baidu_map' );
add_action( 'admin_init', 'my_admin' );  								//called when admin's visited
add_action( 'save_post', 'add_baidu_map_fields', 10, 2 );  
add_filter( 'template_include', 'include_template_function', 1 ); 		//register custom template
add_action( 'init', 'create_my_taxonomies', 0 ); 						//create taxonomy
add_filter( 'manage_edit-baidu_maps_columns', 'my_columns'); 			//add columns in the admin list page
add_action( 'manage_posts_custom_column', 'populate_columns' );			//populate colums
add_filter( 'manage_edit-baidu_maps_sortable_columns', 'sort_me' );		//make columns sortable
add_filter( 'request', 'column_orderby' );								//order custom columns
add_action('wp_head', 'head_settings');

/**
*create custom post type when init
*/
function create_baidu_map() {
    register_post_type( 'baidu_maps', //custom post type name
        array(
            'labels' => array(
                'name' => 'Baidu Maps',
                'singular_name' => 'Baidu Map',
                'add_new' => 'Add New',
                'add_new_item' => 'Add New Baidu Map',
                'edit' => 'Edit',
                'edit_item' => 'Edit Baidu Map',
                'new_item' => 'New Baidu Map',
                'view' => 'View',
                'view_item' => 'View Baidu Map',
                'search_items' => 'Search Baidu Maps',
                'not_found' => 'No Baidu Maps found',
                'not_found_in_trash' => 'No Baidu Maps found in Trash',
                'parent' => 'Parent Baidu Map'
            ),
 
            'public' => true,  //visibility in admin and frontend
            'menu_position' => 15,  
            'supports' => array( 'title', 'editor', 'comments', 'thumbnail'), //feature of custom post type displayed
            'taxonomies' => array( '' ),
            'menu_icon' => plugins_url( 'images/image.png', __FILE__ ),
            'has_archive' => true
        )
    );
}


/**
*add meta box in admin
*/
function my_admin() {
    add_meta_box( 'baidu_map_meta_box', //id
        'Baidu Map Details', //heading of the metabox sec
        'display_baidu_map_meta_box', //callback func
        'baidu_maps', //name of custom post typt
		'normal', //position this sec is shown in editor page
	    'high'  //priority within where this is shown
    );
}

function theme_name_scripts() {
	wp_register_style( 'baidu_map_style', plugins_url( '/style/style.css', __FILE__ ) );
	wp_enqueue_style( 'baidu_map_style');
	wp_register_script( 'baidu_map_api', plugins_url( '/js/baidu_map_api.js', __FILE__ ) );
	wp_enqueue_script( 'baidu_map_api');
}


function display_baidu_map_meta_box( $baidu_map ) {
    // Retrieve current name of the Map name and rating based on ID
    $baidu_map_name = esc_html( get_post_meta( $baidu_map->ID, 'baidu_map_name', true ) );
    $baidu_map_rating = intval( get_post_meta( $baidu_map->ID, 'baidu_map_rating', true ) );
    ?>
    <table>
        <tr>
            <td style="width: 100%">Map Name</td>
            <td><input type="text" size="80" name="form_baidu_map_name" value="<?php echo $baidu_map_name; ?>" /></td>
        </tr>
        <tr>
            <td style="width: 150px">Map Rating</td>
            <td>
                <select style="width: 100px" name="form_baidu_map_rating">
                <?php
                // Generate all items of drop-down list
                for ( $rating = 5; $rating >= 1; $rating -- ) {
                ?>
                    <option value="<?php echo $rating; ?>" <?php echo selected( $rating, $baidu_map_rating ); ?>>
                    <?php echo $rating; ?> stars <?php } ?>
                </select>
            </td>
        </tr>
        <tr>
        	<td style='width: 100%'> Add Markers </td>
        </tr>
        <tr>
        	<td style='width: 100%'> 
            <input type="input" name="bdmap_coordinates" size="55" id="bdmap_coordinates" value="">
            </td>
        </tr>
    </table>
    	<?php add_action( 'admin_enqueue_scripts', 'theme_name_scripts' ); ?>
         <div id="bdmap_container">hahaha</div>
    <?php
}

function head_settings(){
	?>
	<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=1b5ea53022a22b9b748e1502fe5a1061"></script>
		
	<?php
}

/**
*callback func for save
*form_baidu_map_name is used for post form
*baidu_map_name is key for database
*/
function add_baidu_map_fields( $baidu_map_id, $baidu_map ) {
    // Check post type for baidu maps
    if ( $baidu_map->post_type == 'baidu_maps' ) {
        // Store data in post meta table if present in post data
        if ( isset( $_POST['form_baidu_map_name'] ) && $_POST['form_baidu_map_name'] != '' ) {
            update_post_meta( $baidu_map_id, 'baidu_map_name', $_POST['form_baidu_map_name'] );
        }
        if ( isset( $_POST['form_baidu_map_rating'] ) && $_POST['form_baidu_map_rating'] != '' ) {
            update_post_meta( $baidu_map_id, 'baidu_map_rating', $_POST['form_baidu_map_rating'] );
        }
    }
}


/**
*change default behavior and register a custom template form custom post type
*/
function include_template_function( $template_path ) {
    if ( get_post_type() == 'baidu_maps' ) {
        if ( is_single() ) {
            // checks if the file exists in the theme first,
            // otherwise serve the file from the plugin
            if ( $theme_file = locate_template( array ( 'single-baidu_maps.php' ) ) ) {
                $template_path = $theme_file;
            } else {
                $template_path = plugin_dir_path( __FILE__ ) . '/single-baidu_maps.php';
            }
        }
    }
    return $template_path;
}

/**
*register custom taxonomy
*/
function create_my_taxonomies() {
    register_taxonomy(
        'baidu_maps_type',
        'baidu_maps',
        array(
            'labels' => array(
                'name' => 'Map Type',
                'add_new_item' => 'Add New Map Type',
                'new_item_name' => "New Map Type"
            ),
            'show_ui' => true, //make taxonomy editor visible in dashboard
            'show_tagcloud' => false, //tag cloud
            'hierarchical' => true  //format of custom taxonomy
        )
    );
}

/**
*add colums to admin list page
*/
function my_columns( $columns ) {
    $columns['baidu_maps_name'] = 'Map Name';
    $columns['baidu_maps_rating'] = 'Rating';
    unset( $columns['comments'] );
    return $columns;
}

function populate_columns( $column ) {
    if ( 'baidu_maps_name' == $column ) {
        $baidu_map_name = esc_html( get_post_meta( get_the_ID(), 'baidu_map_name', true ) );
        echo $baidu_map_name;
    }
    elseif ( 'baidu_maps_rating' == $column ) {
        $baidu_map_rating = get_post_meta( get_the_ID(), 'baidu_map_rating', true );
        echo $baidu_map_rating . ' stars';
    }
}

function sort_me( $columns ) {
    $columns['baidu_maps_name'] = 'baidu_maps_name';
    $columns['baidu_maps_rating'] = 'baidu_maps_rating';
 
    return $columns;
}

/**
*order custom column
*/
function column_orderby ( $vars ) {
    if ( !is_admin() )
        return $vars;
    if ( isset( $vars['orderby'] ) && 'baidu_maps_name' == $vars['orderby'] ) {
        $vars = array_merge( $vars, array( 'meta_key' => 'baidu_map_name', 'orderby' => 'meta_value' ) );
    }
    elseif ( isset( $vars['orderby'] ) && 'baidu_maps_rating' == $vars['orderby'] ) {
        $vars = array_merge( $vars, array( 'meta_key' => 'baidu_map_rating', 'orderby' => 'meta_value_num' ) );
    }
    return $vars;
}

