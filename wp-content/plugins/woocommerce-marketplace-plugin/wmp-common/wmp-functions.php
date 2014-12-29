<?php

//count product by seller_id
function count_seller_products( $seller_id ) {
global $wpdb;
$count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts WHERE post_author = $seller_id AND post_type IN ('product') and post_status = 'publish'" );
return $count;
}



//Change default icon for 'Sellers' dashboard menu
function wmp_admin_head(){
?>
<style type="text/css">
#adminmenu #toplevel_page_sellers div.wp-menu-image:before { content: "\f307"; }
#adminmenu #menu-posts-shop_order div.wp-menu-image:before { content: "\f157"; }
#adminmenu #toplevel_page_seller-profile div.wp-menu-image:before { content: "\f110"; }
</style>
<?php
}   

add_action('admin_head', 'wmp_admin_head');




//Save additional data for seller
add_action( 'user_register', 'wmp_seller_additionaldata_save', 10, 1 );

function wmp_seller_additionaldata_save( $user_id ) {
  global $aj_csvimport;

  if ( isset( $_POST['seller_name'] ) ){
    update_user_meta($user_id, 'seller_name', $_POST['seller_name']);
  }

  if ( isset( $_POST['seller_address'] ) ){
    update_user_meta($user_id, 'seller_address', $_POST['seller_address']);
  }


  //if ( isset($_POST['seller_activate'])){
    update_user_meta($user_id, 'seller_activate', '1');
  //}else{
    //update_user_meta($user_id, 'seller_activate', '0');
  //}


    update_user_meta($user_id, 'activate', $_POST['activate']);
    


  }

//Update additional data for seller
add_action( 'edit_user_profile_update', 'wmp_seller_additionaldata_save', 10, 1 );








//Removing seller listing from users table
function wmp_user_query($user_search) {
  $user = wp_get_current_user();
  
    global $wpdb;

    $user_search->query_where = 
        str_replace('WHERE 1=1', 
            "WHERE 1=1 AND {$wpdb->users}.ID IN (
                 SELECT {$wpdb->usermeta}.user_id FROM $wpdb->usermeta 
                    WHERE {$wpdb->usermeta}.meta_key = '{$wpdb->prefix}capabilities'
                    AND {$wpdb->usermeta}.meta_value NOT LIKE '%seller%')", 
            $user_search->query_where
        );
 
}
add_action('pre_user_query','wmp_user_query');






//Author dropdown to product page
add_action('init', 'wpse_74054_add_author_woocommerce', 999 );

function wpse_74054_add_author_woocommerce() {
    add_post_type_support( 'product', 'author' );
}





//Adding seller tab to single product page
add_filter( 'woocommerce_product_tabs', 'wmp_seller_product_tab' );
function wmp_seller_product_tab( $tabs ) {

//checking if product belongs to seller
global $post;
$role = get_user_role($post->post_author);
 
  if($role == 'seller'){

    $tabs['seller_tab'] = array(
        'title'     => __( 'Seller', 'woocommerce' ),
        'priority'  => 50,
        'callback'  => 'wmp_seller_tab_content'
    );
}
    return $tabs;
}

//Seller Tab Content
function wmp_seller_tab_content(){
  global $post;
  $seller_id = $post->post_author;
  $seller_name = get_user_meta( $seller_id, 'seller_name', true );
  echo '<div class="seller-name"><a href="'.get_site_url().'/seller/'.get_the_author().'">'.$seller_name.'</a></div>';
  }



//Get seller name by id
function get_seller_name($seller_id){
  $seller_name = get_user_meta( $seller_id, 'seller_name', true );
  if($seller_name){
    return $seller_name;
  }else{
    $user_info = get_userdata($seller_id);
    return $user_info->user_login;
  }
}


//Get seller id by login name - seller page
function get_query_id(){
if(get_query_var( 'seller' )){
$seller = get_userdatabylogin(get_query_var( 'seller' ));
return $seller->ID;
}
}


//Get products in array
function get_seller_product_ids($seller_id){
$args = array( 'author' => $seller_id, 'post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => -1 );
 $products = query_posts( $args );
 $id = array();
 if ( have_posts() ) : while ( have_posts() ) : the_post();
 $id[] = get_the_ID();
 endwhile; endif;
 return $id;
}



//list seller products
function seller_listing($seller_id){
  if(count(get_seller_product_ids($seller_id))>0){
    $ids = implode(',', get_seller_product_ids($seller_id));
    $seller_shortcode = '[products ids="'.$ids.'"]';
    return do_shortcode($seller_shortcode);
  }else{
    return '<div class="no-products">No products found</div>';
  }

}




//get user role
function get_user_role($uid) {
    global $wpdb;
if ( is_multisite() ) { 
$role = $wpdb->get_var("SELECT meta_value FROM {$wpdb->usermeta} WHERE meta_key = 'wp_".get_current_blog_id()."_capabilities' AND user_id = {$uid}");
}else{
  $role = $wpdb->get_var("SELECT meta_value FROM {$wpdb->usermeta} WHERE meta_key = 'wp_capabilities' AND user_id = {$uid}");
}
  if(!$role) return 'non-user';
$rarr = unserialize($role);
$roles = is_array($rarr) ? array_keys($rarr) : array('non-user');
return $roles[0];
}



//Display seller info after product title on single product page
function wmp_seller_info() {
  global $post;
  $seller_id = $post->post_author;
  $role = get_user_role($seller_id);
  if($role == 'seller'){
    $seller_name = get_user_meta( $seller_id, 'seller_name', true );
    echo '<div class="seller-name">Sold by: <a href="'.get_site_url().'/seller/'.get_the_author().'">'.$seller_name.'</a></div>';
  }
}
add_action( 'woocommerce_single_product_summary', 'wmp_seller_info', 6 );





//get seller rating
function wmp_get_seller_rating( $seller_id ) {
    global $wpdb;

    $sql = "SELECT AVG(cm.meta_value) as average, COUNT(wc.comment_ID) as count FROM $wpdb->posts p
        INNER JOIN $wpdb->comments wc ON p.ID = wc.comment_post_ID
        LEFT JOIN $wpdb->commentmeta cm ON cm.comment_id = wc.comment_ID
        WHERE p.post_author = %d AND p.post_type = 'product' AND p.post_status = 'publish'
        AND ( cm.meta_key = 'rating' OR cm.meta_key IS NULL) AND wc.comment_approved = 1
        ORDER BY wc.comment_post_ID";

    $result = $wpdb->get_row( $wpdb->prepare( $sql, $seller_id ) );

    return array( 'rating' => number_format( $result->average, 2), 'count' => (int) $result->count );
}



//get seller rating with html formatting
function wmp_get_readable_seller_rating( $seller_id ) {
    $rating = wmp_get_seller_rating( $seller_id );

    if ( ! $rating['count'] ) {
        echo __( 'No ratings found yet!', 'wmp' );
        return;
    }

    $long_text = _n( __( '%s rating from %d review', 'wmp' ), __( '%s rating from %d reviews', 'wmp' ), $rating['count'], 'wmp' );
    $text = sprintf( __( 'Rated %s out of %d', 'wmp' ), $rating['rating'], number_format( 5 ) );
    $width = ( $rating['rating']/5 ) * 100;
    ?>
        <span class="seller-rating">
            <span title="<?php echo esc_attr( $text ); ?>" class="star-rating" itemtype="http://schema.org/Rating" itemscope="" itemprop="reviewRating">
                <span class="width" style="width: <?php echo $width; ?>%"></span>
                <span style=""><strong itemprop="ratingValue"><?php echo $rating['rating']; ?></strong></span>
            </span>
        </span>

        <span class="text"><a href="#"><?php printf( $long_text, $rating['rating'], $rating['count'] ); ?></a></span>

    <?php
}

//hook into CSV plugin filter and configure csv upload components
function theme_add_csv_components($defined_csv_components){

    $defined_csv_components['pincodes'] = array('pincode',
                                                'Taluk',
                                                'statename');
    
    $defined_csv_components['seller_pincodes'] = array('Postal Code',
                                                        'City Name',
                                                        'State',
                                                        'Domestic Service',
                                                        'International Services',
                                                        'ODA-OPA / Regular Classification (Dom +Intl)',
                                                        'COD Serviceable (Domestic Only)');
    return $defined_csv_components;

}
add_filter('add_csv_components_filter','theme_add_csv_components',10,1);

// function to import a pincode record by hooking into the CSV plugin filter ajci_import_record_pincodes
function import_csv_pincode_record($import_response,$record){
 
    if(count($record) != 3){
       $import_response['imported'] = false;
       $import_response['reason'] = 'Column count does not match';
    }
    elseif(pincode_exists_db($record[0])){
       $import_response['imported'] = false;
       $import_response['reason'] = 'Pin code already exists';       
    }
    else{
       //insert pincode entry 
       add_pincode_db($record); 
       $import_response['imported'] = true;
    }
    
    return $import_response;
}
add_filter('ajci_import_record_pincodes','import_csv_pincode_record',10,2);

//check if pincode exists in db
function pincode_exists_db($pincode){
    global $wpdb;

    $query = $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}pincodes WHERE pincode LIKE %s", $pincode );
    $record = $wpdb->get_var( $query );

    return is_numeric( $record );
}

//add pincode to the custom pincodes table
function add_pincode_db($record){
    global $wpdb;
    $pincode_data = array('pincode'=>$record[0],'city'=>$record[1],'state'=>$record[2]);
    $wpdb->insert( $wpdb->prefix . "pincodes",
    $pincode_data );

    return $wpdb->insert_id;
}

// update a seller id to a pincode
function update_seller_to_pincode($user_id,$record){
    global $wpdb;

    $flag = 0;
    $update_seller_ids = array();
    $query = $wpdb->prepare( "SELECT seller_id FROM {$wpdb->prefix}pincodes WHERE pincode LIKE %s", $record[0] );
    $seller_ids = $wpdb->get_var( $query );
    if(is_null($seller_ids)){
        $sellers_data = array();
        $temparray =  array('user_id' => (string) $user_id,'cod'=> (bool) $record[6]);
        array_push($sellers_data, $temparray);
        $flag = 1;
    }
    else{
        $sellers_data = maybe_unserialize($seller_ids);
        foreach($sellers_data as $seller_data){
            $update_seller_ids[] = $seller_data['user_id'];
        }
        
        if(!in_array($record[0], $update_seller_ids)){
            $temparray = array('user_id' => (string) $user_id,'cod'=> (bool) $record[6]);
            $flag = 1;
            array_push($sellers_data, $temparray);
        }
    }
    
    // update only if seller was not associated to pincode
    if($flag == 1){
        $wpdb->update( $wpdb->prefix . "pincodes",array('seller_id' => maybe_serialize($sellers_data)),array('pincode'=>$record[0]) );
    }
}

function unset_seller_pincodes($user_id){
    global $wpdb;
    
    $like_string = '%"'.$user_id.'";%';
    $query = $wpdb->prepare( "SELECT id,seller_id FROM {$wpdb->prefix}pincodes WHERE seller_id LIKE %s", $like_string );
    $seller_pincodes = $wpdb->get_results( $query );
    
    foreach($seller_pincodes as $pincode){
        $seller_ids = array();
        $sellers_data = maybe_unserialize($pincode->seller_id);
        foreach($sellers_data as $key => $seller_data){
            $seller_ids[$key] = $seller_data['user_id'];
        }
        if (($key = array_search($user_id, $seller_ids)) !== FALSE) {
            unset($sellers_data[$key]);
        }
        
        $wpdb->update( $wpdb->prefix . "pincodes",array('seller_id' => maybe_serialize($sellers_data)),array('id'=>$pincode->id) );
    }
    
}


// seller pincodes csv file upload function 
function pincode_csv_upload(){
    global $aj_csvimport;
    $parse_valid_csv = $aj_csvimport->csv_validate();
    if($parse_valid_csv['success']){
        $preview_data = ajci_display_csv_preview($_POST['csv_component'],$parse_valid_csv);
        wp_send_json($preview_data);
    }else{
       $response = $parse_valid_csv['msg'];
       wp_send_json($response);
    }
}
add_action("wp_ajax_pincode_csv_upload", "pincode_csv_upload");

function pincode_csv_upload_confirm(){
    global $aj_csvimport;
    
    $uniquefilename = $_POST['uniquename'];
    $realfilename = $_POST['realname'];
    $component = $_POST['csv_component'];       
    $meta = array();
    $meta['header'] = (isset($_POST['csv_header']))? true:false ;
    $meta['user_id'] = (int) $_POST['user_id']; 
    $id = $aj_csvimport->init_csv_data($uniquefilename,$realfilename,$component,$meta);
    if(!is_wp_error($id)){
        $response = array('csv_id'=>$id);
        
        wp_send_json($response);
    }
    else{
        wp_send_json($id);
    }
    
}
add_action("wp_ajax_pincode_csv_upload_confirm", "pincode_csv_upload_confirm");

// function to add a pincode to a seller by hooking into the CSV plugin filter ajci_import_record_seller_pincodes
function import_csv_seller_pincode_record($import_response,$record,$csv_master_info){
 
    if(count($record) != 7){
       $import_response['imported'] = false;
       $import_response['reason'] = 'Column count does not match';
    }
    else{
        $metadata = maybe_unserialize($csv_master_info->meta);
       //add a seller to a pincode $record[0]
       update_seller_to_pincode($metadata['user_id'],$record);
       $import_response['imported'] = true;
    }
    
    return $import_response;
}
add_filter('ajci_import_record_seller_pincodes','import_csv_seller_pincode_record',10,3);


//ajax method to reset a seller pincodes
function ajax_reset_seller_pincodes(){
    if(isset($_POST['user_id']) && intval($_POST['user_id']) > 0){
        unset_seller_pincodes($_POST['user_id']);
        $response= array('code'=>'OK','msg'=>'Pincodes reseted for seller');
        wp_send_json($response);
    }else{
        $response= array('code'=>'ERROR','msg'=>'Invalid user id');
        wp_send_json($response);
    }
}
add_action("wp_ajax_reset_seller_pincodes", "ajax_reset_seller_pincodes");

//ajax method to get a seller information for a pincode
function ajax_get_seller_pincode_info(){
    global $wpdb;
    
    $pincode = $_POST['pincode'];
    $user_id = $_POST['user_id'];
    $query = $wpdb->prepare( "SELECT seller_id FROM {$wpdb->prefix}pincodes WHERE pincode LIKE %s", $pincode );
    $sellers_info = $wpdb->get_var( $query );  
    if(is_null($sellers_info)){
        $response = array('code'=>'OK','msg'=>'Pincode not assigned to seller');
    }else{
        $sellers_info = maybe_unserialize($sellers_info);
        $found = 0;
        foreach ($sellers_info as  $seller_info){
            if((int)$seller_info['user_id'] != $user_id){
                continue;
            }else{
                $cod = ($seller_info['cod'])? 'on' : 'off';
                $msg = 'Pincode is assigned to seller , COD: '.$cod;
                $response = array('code'=>'OK','msg'=>$msg);
                $found = 1;
                break;
            }
        }
        
        if($found == 0)
            $response = array('code'=>'OK','msg'=>'Pincode not assigned to seller');
    }
    
    
    wp_send_json($response);
    
}
add_action("wp_ajax_get_seller_pincode_info", "ajax_get_seller_pincode_info");

