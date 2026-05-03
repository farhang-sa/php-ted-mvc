<?php

defined( 'TExec' ) or die( 'Access Denied' );

class File_Interface extends Ted\TedInterface {
	
	public function Respond( $directRespond = false ){ 
		
		parent::Respond();

		return true ;
		
	}

	public function Response( ){
	
		$Args = func_get_args();

		$Args = ( count( $Args ) === 1 ) ? array_shift( $Args ) : $Args ;

		$file ; $basename ; $size ;

		if ( is_string( $Args ) && is_file( $Args ) ) {

			$file = $Args ;

			$basename = basename( $file ) ;

			$size = filesize( $file ) ;

			$type = filetype( $file ) ;

		} else foreach ( $Args as $key => $value ) switch ( strtolower( ( string ) $key ) ) {

			case '0' :
			case 'file' :
			case 'address' :
			case 'path' :
			case 'url' :

				$file = $value ;

			break;
			case '1' :
			case 'name' :
			case 'basename' :
			case 'dataname' :
			case 'readname' :
			case 'writename' :
			case 'streamname' :
			case 'base_name' :
			case 'data_name' :
			case 'read_name' :
			case 'write_name' :
			case 'stream_name' :

				$basename = $value ;

			break;

		} $size = isset( $size ) ? $size : strlen( $file ) ;
	
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename=' . $basename );
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . $size );

		@ob_clean();
		@flush();
		if ( @is_file( $file ) ) 
			readfile( $file );
		else echo $file ;
		
	}

}

?>