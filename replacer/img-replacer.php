<?php
/**
* Author: Shourya Chowdhury
* Description: Replacing the image
*/


add_action( 'admin_post_wbfir_import', 'wbfir_import' );

function wbfir_import() {
    if ( isset ( $_GET['import_bulk_replacer'] ) )
         $img_path = get_option('wbfir_media_image_path');

    $pro_cat = get_option('wbfir_product_cat');

    $img_replacer = get_option('wbfir_replacer_index');
    
    
    
	$wbfir_admin_url =  admin_url(). 'admin.php?page=wbfir_img_replacer';
	


        $args = array(
		    'post_type' => 'product',
		    'tax_query' => array(
		        array(
		        'taxonomy' => 'product_cat',
		        'field' => 'id',
		        'terms' => $pro_cat
		         )
		      )
    );


    $loop = new WP_Query( $args );

    while ( $loop->have_posts() ) : $loop->the_post(); 
    global $product; 
    $product_id = get_the_ID();
    
    $image_url_type1=""; // for type1
    $image_url_type2=""; // for type2
    $image_url_type3=""; // for type3
    $get_type="";

    
    if($img_replacer == "Sku")
    {
        $img_index = $product->get_sku(); // if select sku
    }

    if($img_replacer == "Name")
    {
        $img_index = get_the_title(); // if select title
    }

    if($img_replacer == "Id")
    {
        $img_index = get_the_ID(); // if select id
    }


     $image_url_type1 = $img_path."/".$img_index.".jpg";

     $image_url_type2 = $img_path."/".$img_index.".png";

     $image_url_type3 = $img_path."/".$img_index.".jpeg";

     if(file_exists($image_url_type1))
     {
        $get_type = $image_url_type1;
       
       }

      if(file_exists($image_url_type2))
      {
         $get_type = $image_url_type2;
      } 

      if(file_exists($image_url_type3))
      {
         $get_type = $image_url_type3;
      } 


    

       // echo "here";
       
       if($get_type !="")
       {
	   	
	   

        $post_id = get_the_ID();
        $upload_dir = wp_upload_dir();
        $image_data = file_get_contents($get_type);
        $filename = basename($get_type);
        if(wp_mkdir_p($upload_dir['path']))     $file = $upload_dir['path'] . '/' . $filename;
        else                                    $file = $upload_dir['basedir'] . '/' . $filename;
        file_put_contents($file, $image_data);

        $wp_filetype = wp_check_filetype($filename, null );
        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => sanitize_file_name($filename),
            'post_content' => '',
            'post_status' => 'inherit'
        );
        $attach_id = wp_insert_attachment( $attachment, $file, $post_id );
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
        $res1= wp_update_attachment_metadata( $attach_id, $attach_data );
        $res2= set_post_thumbnail( $post_id, $attach_id );
        } 

    endwhile; 
    
    

    

    wp_redirect( $wbfir_admin_url );

    //die( __FUNCTION__ );
}


