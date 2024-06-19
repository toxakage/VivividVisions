<?php 

if( isset( $_POST['my_file_upload'] ) ){

	$uploaddir = '../img';

	$files = $_FILES; 
	$done_files = array();
    $id = $_POST['id'];

	foreach( $files as $file ){
		$file_name = $file['name'];

		if( move_uploaded_file( $file['tmp_name'], "$uploaddir/product_$id.png" ) ){
			$done_files[] = realpath( "$uploaddir/$file_name" );
		}
	}
}

?>