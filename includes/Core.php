<?php
namespace SvAutors;

class Core
{
    protected $nonce        = "--sv-authors-tokin--";
    protected $options;
    protected $post_types   = ['post','page','affiliates','vegashero_games'];
    protected $postMetaKey  = 'sv_autor_meta_key';
    protected $postType     = 'sv_authors';
    protected $roles_has_access = ['administrator'];
    protected $excludePages       = ['2151','6647','2240','2234','2271','223'];

    protected function getOptions(){
        // get options

        $defaults = [  // default options
//            'heading_text'                  => isset($_POST['heading_text']) ? stripslashes( trim( $_POST['heading_text'] ) ): '',
//            'visibility_show'               => isset($_POST['visibility_show']) ? stripslashes( trim( $_POST['visibility_show'] ) ): '',
//            'visibility_hide'               => isset($_POST['visibility_hide']) ? stripslashes( trim( $_POST['visibility_hide'] ) ): '',
        ];

        $options       = get_option( 'sv-authors-options', $defaults );

        $this->options = wp_parse_args( $options, $defaults );
        



    }

    protected function save_admin_options() {

        // security check
       if ( ! wp_verify_nonce( @$_POST['sv-authors-options'], SV_AUT_URL ) ) {
           return false;
       }

        $this->options = array_merge(
            $this->options,
            [
                'updatedOn'          => stripslashes( trim( $_POST['updatedOn'] ) ),
                'publishedOn'        => stripslashes( trim( $_POST['publishedOn'] ) ),
                'aboutAuthour'       => stripslashes( trim( $_POST['aboutAuthour'] ) ),
                'otherPosts'         => stripslashes( trim( $_POST['otherPosts'] ) ),
                'followOn'           => stripslashes( trim( $_POST['followOn'] ) )

            ]
        );

        update_option( 'sv-authors-options', $this->options );
        $this->getOptions();
        return true;
    }

    static function view($filename, $data = array(), $template_folder = 'templates/'){

        $filename = SV_AUT_DIR.$template_folder.$filename.'.php';

        ob_start();

        $_ = [];

        $data = array_merge($_, $data);

        foreach ($data as $key=>$d){
            $$key = $d;
        }

        if(file_exists($filename)){
            require ($filename);
        }else{
            die('template load failed');
        }

        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }

    protected function getUsersNamesMeta(){
        global $wpdb;

        $users = $wpdb->get_results("SELECT ID, post_title as name FROM {$wpdb->posts} WHERE post_type = '$this->postType' AND post_status = 'publish' ", ARRAY_A);

         return $users;
    }

    function pluginActivate(){
            $roles = get_editable_roles();

            if ( !empty($roles) && in_array(key($roles), $this->roles_has_access) ) {
                foreach ($GLOBALS['wp_roles']->role_objects as $key => $role) {
                    if (isset($roles[$key]) && $role->has_cap('manage_options')) {
                        $role->add_cap('edit_sv_authors');
                        $role->add_cap('read_sv_authors');
                        $role->add_cap('publish_sv_authors');
                        $role->add_cap('read_private_sv_authors');
                        $role->add_cap('edit_others_sv_authors');
                    }
                }
            }
    }
    
   }// class