<?php
/**
 * Plugin Name: SV AUTORS
 * Plugin URL:
 * Description: Custom author box
 * Version: 2.9
 * Author: Synced Vision
 * Author URI: https://synced.vision
 * Contributors:
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

define( 'SV_AUT_URL', plugin_dir_url( __FILE__ ) );
define( 'SV_AUT_DIR', plugin_dir_path( __FILE__ ) );
define( 'SV_AUT_INCLUDES', SV_AUT_DIR.'includes/' );
define( 'SV_AUT_DATA', $plugin_data = get_plugin_data( __FILE__ ) );



spl_autoload_register(function ($class){
    $className = str_replace('SvAutors\\', '', $class);


    $filename = SV_AUT_INCLUDES.$className.'.php';
    if (file_exists($filename)) {
        require_once ($filename);
    }
});

if ( ! class_exists( 'SvAutors' ) ) {
    class SvAutors extends SvAutors\Core
    {
        function __construct()
        {
            ini_set('display_errors', 'on');
            error_reporting(E_ALL);
            $this->getOptions();

            add_action('init', array($this, 'custom_post_type_sv_authors'),0,20);

            add_action( 'admin_menu', [$this,'sv_autor_add_metabox'] );
            add_action( 'admin_menu', [$this,'sv_author_menu_settings'] );
            add_action( 'save_post', [$this, 'sv_autor_meta_box_save'] );

            add_action( 'admin_menu', [$this,'sv_social_author_metabox'] );
            add_action( 'save_post', [$this, 'sv_social_author_meta_box_save'] );

            
            add_action( 'sv_add_autorbox_post', [$this,'autorbox_post_add_settings']);
            add_action( 'sv_add_autorbox_post_top', [$this,'sv_add_autorbox_post_top_add_settings']);
            //add_filter( 'the_title',[$this,'sv_add_autorbox_post_top'], 10, 2 );


            add_action( 'init',[$this, 'custom_post_type', 0,30 ]);

            add_filter('single_template', [$this, 'single_author']);

            //add_image_size( "mordo4kf-saida", 200, 300, true );

            register_activation_hook( __FILE__, array($this, 'pluginActivate'));
        }// construct


        function custom_post_type_sv_authors() {

            $roles = get_editable_roles();

            $labels = array(
                'name'                => __( 'Authors'),
                'singular_name'       => __( 'Author' ),
                'menu_name'           => __( 'SV AUTHORS' ),
                'parent_item_colon'   => __( 'Parent Author'),
                'all_items'           => __( 'All Authors'),
                'view_item'           => __( 'View Author' ),
                'add_new_item'        => __( 'Add New Author'),
                'add_new'             => __( 'Add New' ),
                'edit_item'           => __( 'Edit Author'),
                'update_item'         => __( 'Update Author'),
                'search_items'        => __( 'Search Author' ),
                'not_found'           => __( 'Not Found' ),
                'not_found_in_trash'  => __( 'Not found in Trash'),
            );

            $args = array(
                'label'               => __( 'Authors' ),
                'description'         => __( 'Authors news and reviews' ),
                'labels'              => $labels,

                'supports'            => array( 'title', 'editor',  'author', 'thumbnail'),
                'taxonomies'          => array( '' ),
                'hierarchical'        => false,
                'public'              => true,
                'show_ui'             => true,
                'show_in_menu'        => true,
                'show_in_nav_menus'   => true,
                'show_in_admin_bar'   => true,
                'menu_position'       => 28,
                'can_export'          => true,
                'has_archive'         => false,
                'exclude_from_search' => false,
                'publicly_queryable'  => true,
                'capability_type'     => 'sv_authors',
                'capabilities' => array(
                    'edit_post'          => 'edit_sv_authors',
                    'read_post'          => 'read_sv_authors',
                    'delete_post'        => 'delete_sv_authors',
                    'edit_posts'         => 'edit_sv_authors',
                    'edit_others_posts'  => 'edit_others_sv_authors',
                    'delete_posts'       => 'delete_sv_authors',
                    'publish_posts'      => 'publish_sv_authors',
                    'read_private_posts' => 'read_private_sv_authors',
                    'create_posts'       => 'edit_sv_authors',
                ),
                'show_in_rest' => true,
                'menu_icon' => 'dashicons-admin-users',
                'rewrite' => [
                    'slug' =>    'authors'
                ]
            );

            register_post_type( $this->postType, $args );

        }

       function sv_author_menu_settings(){

            add_submenu_page(
                'edit.php?post_type=sv_authors',
                'Authors Settings',
                'Settings',
                'administrator',
                'sv_authors_settings',
                array($this, 'sv_submenu_author_settings_callback')
            );

       }

        function sv_submenu_author_settings_callback(){
            $data['msg'] = '';
            if ( isset( $_GET['update'] ) ) {
                if ( $this->save_admin_options() ) {
                    $data['msg'] = '<div id="message" class="updated fade"><p>' . __( 'Options saved.', 'sv-authors' ) . '</p></div>';
                } else {
                    $data['msg'] = '<div id="message" class="error fade"><p>' . __( 'Save failed.', 'sv-authors' ) . '</p></div>';
                }
            }

            $data['options'] = $this->options;


            echo self::view('settings', $data);


        }


        function single_author($original){
            global $wpdb, $post;


            if($post->post_type == $this->postType){
                $GLOBALS['autorPosts'] =  $wpdb->get_results("SELECT * FROM {$wpdb->posts} WHERE ID in (SELECT post_id FROM wp_postmeta WHERE meta_key = '{$this->postMetaKey}' AND meta_value = $post->ID )");
                
                
                return SV_AUT_DIR . 'templates/single-aut.php';
            }
            return $original;
        }

        //setting for TOP author box hook




          function sv_add_autorbox_post_top_add_settings(){
              if(!get_the_ID() || in_array($this->excludePages)) return false;
              global $wpdb;


              $author_id = get_post_meta( get_the_ID(), $this->postMetaKey, true );

              if(!$author_id ){
                  return false;
              }

              if(intval($author_id)){
                  $autor_r =  $wpdb->get_results("SELECT * FROM {$wpdb->posts} WHERE post_type = '{$this->postType}' AND ID = $author_id AND  post_status = 'publish'", ARRAY_A)[0];
              }else{
                  $autor_r =  $wpdb->get_results("SELECT * FROM {$wpdb->posts} WHERE post_type = '{$this->postType}' AND post_title = '$author_id' AND  post_status = 'publish'", ARRAY_A)[0];
              }

              if(empty($autor_r)){
                  return false;
              }

              ?>
              <style>
                  .autor-box-top{
                      display:flex;
                      padding-bottom: 40px;
                      border-bottom: 1px solid #e3e1e1;
                      transform: translateY(5px);
                  }

                  .autorbox-top-image img{
                      height: 50px;
                      border-radius: 50%;
                      margin-right: 15px;
                      transform: translateY(-4px);
                  }
                  .autor-box-title > h4{
                      font-size:28px;
                      font-weight:700;
                      text-align:left;
                      white-space: nowrap;
					  font-family: 'Gabriela', serif;
                  }
                  span.author-top{
                      margin-bottom: 15px;
                      color: #a8a8a6;
                      font-weight: 700;
                      font-size: 14px;
                  }
                  .autorbox-info {
                      padding-top:0px;
                      font-size: 18px;
                      font-weight: 600;
                  }
                  .autorbox-info .subtitle{
                      color: #a8a8a6;
                      font-weight: 400;
                      font-size: 14px;
                      padding-top:0px;
                  }
                  .autorbox-fullwidth-top{
                      margin-top: 40px;
                      margin-bottom: 40px;
                  }
                  .autorbox-fullwidth-top.light-autorbox {
                      color:white;
                      margin-bottom:0;
                      margin-top: 0;
                  }
                  .autorbox-fullwidth-top.light-autorbox > .autor-box-top{
                    border-bottom:none;
                  }

              </style>
              <div class="autor-box-top">
                  <div class="autorbox-top-image">
                      <?php
                      
                      if (has_post_thumbnail( $autor_r['ID'] ) ): ?>
                          <?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $autor_r['ID'] ), 'single-post-thumbnail' ); ?>
                          <img src="<?php echo $image[0]; ?>" alt="">
                      <?php endif; ?>
                  </div>
                  <div class="autorbox-info">
                      <span class="author-top"> </span><?php echo $autor_r['post_title']; ?>

                     <?php global $post;
                     if( get_field('published_or_edited_date') == 'Updated on' ) {
                         ?>  <div class="subtitle"><?php echo $this->options['updatedOn'];?> <?php

                             if(isset($post->post_modified) && $post->post_modified){
                                 $datePublishedPost = new DateTime($post->post_modified);
                                 echo $datePublishedPost->format('d-m-Y');
                             }


                         ?></div> <?php
                      } else{ ?>
                      <div class="subtitle"><?php echo $this->options['publishedOn']; ?> <?php
                          if(isset($post->post_date) && $post->post_date){
                              $datePublishedPost = new DateTime($post->post_date);
                              echo $datePublishedPost->format('d-m-Y');
                          }
                          ?></div> <?php
                     }?>

                  </div>
              </div>

              <?php

          }

        //setting for bottom author box hook
        function autorbox_post_add_settings(){
            if(!get_the_ID() || in_array($this->excludePages)) return false;
            global $wpdb;

            $author_id = get_post_meta( get_the_ID(), $this->postMetaKey, true );

            if(!$author_id ){
				echo "</div>";
                return false;
            }

            if(intval($author_id)){
                $autor_r =  $wpdb->get_results("SELECT * FROM {$wpdb->posts} WHERE post_type = '{$this->postType}' AND ID = $author_id AND  post_status = 'publish'", ARRAY_A)[0];
            }else{
                $autor_r =  $wpdb->get_results("SELECT * FROM {$wpdb->posts} WHERE post_type = '{$this->postType}' AND post_title = '$author_id' AND  post_status = 'publish'", ARRAY_A)[0];
            }

            if(empty($autor_r)){
                return false;
            }



            ?>
            <style>
                .main-aut-box-wrap{
                    margin-top: 40px;
                    padding-top: 40px;
                    border-top: 1px solid #e3e1e1;
                }
                .autorbox-fullwidth-top {
                    margin-top: 40px;
                    margin-bottom: 40px;
                }
                 a.title-aut-link{
                    padding-top:0px;
                    font-size: 20px;
                    font-weight: 600;
                     margin-bottom: 10px;
                }
                .autor-box{
                    display:flex;
                    flex-wrap: wrap;
                }
                .autor-box > div.left-sv-aut{
                    flex-basis:30%;
                }
                .autor-box > div.right-sv-aut{
                    flex-basis:70%;
                    padding-left: 20px;
                }


                .autorbox-image img{
                    height:205px;
                    border-radius:50%;
                    transform: translateY(-10px);
                }

                .main-aut-box-wrap > .autor-box-title{
                    font-size:28px;
                    font-weight:700;
                    text-align:left;
                    white-space: nowrap;
                    margin-bottom: 40px;
					font-family: 'Gabriela', serif;
                }
                .title-aut-link:hover{
                    color:#FF0000;
                }

                .autorbox-info > p{
                    font-family: 'Gabriela', serif;
                    font-weight:400;
                    font-size:16px;
                    line-height:31px;
                    margin-top: 20px;

                }
                .aut-sign{
                    font-family: 'Gabriela', serif;
                    font-weight:600;
                    font-size:16px;
                    line-height:19px;
                    margin-top: 4px;
                }
                a.aut-link-small{
                    font-family: 'Gabriela', serif;
                    font-weight:600;
                    font-size:16px;
                    line-height:19px;
                    color:#000000 !important;
                }
                a.aut-link-small:hover{
                    color:#FF0000 !important;
                }
                a.sv-aut-follow-link{
                    padding-left: 20px;
                    font-family: 'Gabriela', serif;
                    font-weight:400;
                    font-size:16px;
                    color: #FF0000;
                }
                .autorbox-fullwidth-bottom{
                    margin-bottom: 50px;
                }
                .autorbox-fullwidth-top.box-post{
                    margin-bottom: 0;
                    border-bottom:none;
                }
                .autor-subtitle4{
                    padding-top: 0px;
                    font-size: 20px;
                    font-weight: 600;
					font-family: 'Gabriela', serif;
                }
                .mt-40{
                    margin-top: 40px;
                }

                @media screen and (max-width: 1200px){
                    .autor-box > div.left-sv-aut{
                        flex-basis:40%;
                    }
                    .autor-box > div.right-sv-aut{
                        flex-basis:60%;
                        padding-left: 10px;
                    }
                    .social-aut-links.right-sv-aut > a:nth-child(4){
                        margin-left: 95px;
                    }

                    .social-aut-links.right-sv-aut > a:nth-child(5){
                        margin-left: 20px;
                    }
                    .autorbox-fullwidth-bottom .social-aut-links.right-sv-aut > a:nth-child(4){
                        margin-left: 0px;
                    }
                    .autorbox-fullwidth-bottom .social-aut-links.right-sv-aut > a:nth-child(5){
                        margin-left: 0px;
                    }
                    .social-aut-links.right-sv-aut{
                        line-height:35px;
                    }

                    .autorbox-fullwidth-bottom .aut-sign {
                        transform: translateY(5px);
                    }
                }
                @media only screen and (max-width: 1200px) and (min-width: 992px)  {
                    .autorbox-fullwidth-bottom .aut-sign {
                        margin-right: 20%;
                    }
                    .aut-sign{
                        margin-right: 10%;
                        transform: translateY(5px);
                    }
                }
                @media only screen and (max-width: 991px) and (min-width: 580px)  {
                    .autorbox-fullwidth-bottom .aut-sign {
                        margin-right: 10%;

                    }
                    .aut-sign{
                        transform: translateY(5px);
                    }
                }
                @media screen and (max-width: 992px){
                  .autorbox-image.left-sv-aut > div > img{
                        height: 160px;
                    }
                    .autorbox-fullwidth-bottom .autorbox-image.left-sv-aut > div > img{
                        height: 205px;
                    }
                    .social-aut-links.right-sv-aut > a:nth-child(4) {
                        margin-left: 95px;
                    }
                    .social-aut-links.right-sv-aut > a:nth-child(5){
                        margin-left: 20px;
                    }


                    a.sv-aut-follow-link {
                        padding-left: 10px;
                    }
                }
                @media screen and (max-width: 768px){
                    .autorbox-fullwidth-bottom .autorbox-image.left-sv-aut > div > img{
                        height: 160px;
                    }

                    .autorbox-fullwidth-bottom .social-aut-links.right-sv-aut > a:nth-child(4) {
                        margin-left: 95px;
                    }
                }
                @media screen and (max-width: 580px){
                    .aut-sign {
                        transform: translateY(0px);
                        margin-top:25px;
                        margin-bottom: 45px;
                        width: 40%;
                    }
                    .autor-box > div.left-sv-aut{
                        flex-basis:100%;
                    }
                    .autor-box > div.right-sv-aut{
                        flex-basis:100%;
                        padding-left: 0px;
                        order:4;
                    }

                    .autor-box > div.left-sv-aut:nth-child(2){

                    }
                    .autor-box > div.right-sv-aut:nth-child(1){
                        order:2;
                    }
                    .autor-box > div.right-sv-aut:nth-child(2){
                        order:3;
                    }
                    .autorbox-image.left-sv-aut > div > img {
                        height: 200px;
                    }
                    .social-aut-links.right-sv-aut > a:nth-child(4) {
                        margin-left: 0px;
                    }
                    .social-aut-links.right-sv-aut > a:nth-child(5) {
                        margin-left: 0px;
                    }
                    a.sv-aut-follow-link {
                        padding-left: 15px;
                    }
                    .autorbox-fullwidth-bottom .aut-sign {
                        transform: translateY(5px);
                    }
                    .autorbox-fullwidth-bottom .social-aut-links.right-sv-aut > a:nth-child(4) {
                        margin-left: 0px;
                    }
                    .text-area p a, .text-area div > div > a {
                        color: #00000000 !important;
                        text-decoration: none;
                    }
                }
                @media screen and (max-width: 490px){
                    .aut-sign {
                        width: 50%;
                    }
                }

                @media screen and (max-width: 440px){
                    .social-aut-links.right-sv-aut > a:nth-child(4) {
                        margin-left: 92px;
                    }
                    .social-aut-links.right-sv-aut > a:nth-child(5) {
                        margin-left: 20px;
                    }
                    a.sv-aut-follow-link {
                        padding-left: 30px;
                    }
                }
                @media screen and (max-width: 360px){
                    .autor-box > div.left-sv-aut > div{
                        text-align: center;
                    }
                }
                @media screen and (max-width: 340px){
                    .aut-sign {
                        width: 70%;
                    }
                    a.sv-aut-follow-link {
                        padding-left: 20px;
                    }
                }

            </style>

            <div class="main-aut-box-wrap">
                <div class="autor-box-title"><h4><?php
                        $aboutAuthor = $this->options['aboutAuthour'];
                        echo $aboutAuthor; ?></h4>
                </div>
            <div class="autor-box">
              <div class="autorbox-image left-sv-aut">
               <div><?php
                if (has_post_thumbnail( $autor_r['ID'] ) ): ?>
                    <?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $autor_r['ID'] ), 'single-post-thumbnail' ); ?>
                        <img src="<?php echo $image[0]; ?>" alt="">
                    <?php endif; ?>
               </div>

              </div>
               <div class="autorbox-info right-sv-aut">
                        <div class = "autor-subtitle4"><?php echo $autor_r['post_title']; ?></div>
                        <p><?php echo $autor_r['post_content']; ?></p>
                </div>

                       <div class="left-sv-aut">
                        <div class="aut-sign "><?php echo $this->options['otherPosts']; ?> <a class="aut-link-small" style = "color:#000 !important;text-decoration: none;" href="<?php echo get_permalink($autor_r['ID']); ?>"><?php echo $autor_r['post_title']; ?></a></div>
                       </div>

                    
                   <?php if( have_rows('social_links_arr',$autor_r['ID'])) : ?>
                        <div class="social-aut-links right-sv-aut">
                            <span style="font-weight:700; font-size:18px;"><?php echo $this->options['followOn']; ?> </span>
                            <?php
                            // loop through the rows of data
                            while ( have_rows('social_links_arr',$autor_r['ID']) ) : the_row(); ?>
                                <a class="sv-aut-follow-link" href="<?php  if(get_sub_field('social_name')['url']) { echo get_sub_field('social_name')['url'];} else { echo '#';} ?>">
                                    <?php echo get_sub_field('social_name')['title']; ?>
                                </a>
                            <?php endwhile; ?>
                        </div>
                    <?php endif; ?>
                </div>
              </div>
            </div>

                           <?php

        }



      //metabox for displaying select of authors on the page,post,review
        function sv_autor_add_metabox() {



            add_meta_box(
                'sv_autor_metabox', // metabox ID
                'Custom Post Author', // title
                [$this,'sv_autor_metabox_callback'], // callback function
                  $this->post_types, // post type or post types in array
                'side', // position (normal, side, advanced)
                'default' // priority (default, low, high, core)
            );

        }


        // it is a callback function which actually displays the content of the meta box
        function sv_autor_metabox_callback( $post ) {
            $value = get_post_meta( $post->ID, $this->postMetaKey, true );

            
            $users = $this->getUsersNamesMeta();
            ?>
            <label for="wporg_field">Select Author</label>
            <select name="<?= $this->postMetaKey ?>" class="postbox">
            <?php
                echo '<option></option>';
               foreach ($users as $u): ?>
                <option value="<?= $u['ID'] ?>" <?= (int)$value === (int)$u['ID'] ? 'selected' : '' ?>><?php echo $u['name']; ?> </option>
                <?php
               endforeach; ?>
            </select>
            <?php
        }

        //saving metabox author select
        function sv_autor_meta_box_save($post_id ){

            if(in_array($_POST['post_type'], $this->post_types)) {
                if (!add_post_meta($post_id , $this->postMetaKey, $_POST[$this->postMetaKey], true)) {
                    update_post_meta($post_id , $this->postMetaKey, $_POST[$this->postMetaKey]);
                }
            }
        }


        //metabox for displaying SOCIAL LINKS on author page

        function sv_social_author_add_metabox() {



            add_meta_box(
                'sv_social_author_metabox', // metabox ID
                'Author Social Links', // title
                [$this,'sv_social_author_metabox_callback'], // callback function
                'sv_authors', // post type or post types in array
                'side', // position (normal, side, advanced)
                'default' // priority (default, low, high, core)
            );

        }



        // it is a callback function which actually displays the content of the meta box
        function sv_social_author_metabox_callback( $post ) {
         $value = get_post_meta( $post->ID, $this->postMetaKey, true );


          $users = $this->getUsersNamesMeta();
            echo ' SOCIAL LINKS AUTHORS'
            ?>


            <select name="<?= $this->postMetaKey ?>" class="postbox">
                <?php
                echo '<option>----</option>';
                foreach ($users as $u): ?>
                    <option value="<?= $u['ID'] ?>" <?= (int)$value === (int)$u['ID'] ? 'selected' : '' ?>><?php echo $u['name']; ?> </option>
                <?php
                endforeach; ?>
            </select>
            <?php



        }

        //saving metabox author social
        function sv_social_author_meta_box_save($post_id ){

            if(in_array($_POST['post_type'], $this->post_types)) {
                if (!add_post_meta($post_id , $this->postMetaKey, $_POST[$this->postMetaKey], true)) {
                    update_post_meta($post_id , $this->postMetaKey, $_POST[$this->postMetaKey]);
                }
            }

            if($_POST['post_type'] === $this->postType){
                $this->saveAuthorsIntoSheetEditor();
            }
        }

        protected function saveAuthorsIntoSheetEditor(){
            $sheetEditor = get_option('vgse_columns_manager');
            
            if(!$sheetEditor){
                return false;
            }
            
            $authorsData = '';
            foreach ($this->getUsersNamesMeta() as $author) {
                $authorsData .= $author['ID'] . ":" . $author['name']. "
";
            }

            foreach ($sheetEditor as &$sheet) {
                if(array_key_exists($this->postMetaKey,$sheet)){
                    if(array_key_exists('allowed_values', $sheet['sv_autor_meta_key'])){
                        $sheet['sv_autor_meta_key']['allowed_values'] = $authorsData;
                    }
                }
            }

            update_option( 'vgse_columns_manager', $sheetEditor );

        }// saveAuthorsIntoSheetEditor

    }// class AUT


    // instantiate the plugin class
    $sv_aut = new SvAutors();


}


