<?php
if(!defined('WP_UNINSTALL_PLUGIN') ){    
  exit () ;   
}  

delete_option( 'pta_directory_options' );
delete_option( 'pta_member_categories');