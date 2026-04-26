<?php

defined( 'TExec' ) or die( 'Access Denied' );

#[AllowDynamicProperties]
class httpRequestPlugin extends Ted\Plugin {

    private $ignoreSSL = false ;

    public function ignoreSSL( $ignore = true ){
        $this->ignoreSSL = $ignore ; }

	public function Request( $url , $data = array() , $method = 'GET' , $customContext = array() ){

        // jquery or direct-query
        if( is_string( $data ) ) {
            $jq = json_decode( $data , true );
            if( is_array( $jq ) )
                $data = $jq ;
        }

        // build query or accept direct-query
		$query = is_array( $data ) ? http_build_query( $data ) : $data ;

		// set method
		$method = $method ? strtoupper( $method ) : 'GET' ;

		// Create Http context details
		$contextData = array ( 'method' => $method );

        // set headers and post data
        $cHeaders = array( 'Connection: close' );
        if( $method === 'POST' && $query && ! empty( $query ) ) :
            $cHeaders[] = 'Content-Length: ' . strlen( $query ) ;
            $cHeaders[] = 'Content-Type: application/x-www-form-urlencoded' ;
            $contextData['content'] = $query ;
        endif ;
        $contextData['header'] = implode( "\r\n" , $cHeaders ) ;

        // ignore ssl+tls
        if( $this->ignoreSSL )
            $contextData['ssl'] = [ 'verify_peer' => false , 'verify_peer_name' => false ] ;

        // set custom context settings like token , content-type , etc
        if( is_array( $customContext ) && ! empty( $customContext ) )
            $contextData = array_merge( $contextData , $customContext );

		// Create context resource for our request
		$context = stream_context_create(array( 'http' => $contextData ));

		// Read page rendered as result of your POST request
		$response = null ;
        try{
            $response = file_get_contents( $url , false , $context ) ;
        } catch( Exception $e ){
            $response = array(
                'status' => -1 ,
                'message' => $e->getMessage() ,
                'error' => $e ,
                'fail' => true ,
            );
        }

	}

    public function RequestCurl( $url , $data = array() , $method = 'GET' , $customOptions = array() ) {

        // jquery or direct-query
        if( is_string( $data ) ) {
            $jq = json_decode( $data , true );
            if( is_array( $jq ) )
                $data = $jq ;
        }

        // build query or accept direct-query
		$query = is_array( $data ) ? http_build_query( $data ) : $data ;

		// set method
		$method = $method ? strtoupper( $method ) : 'GET' ;

        // create curl options
		$cOpts = [ CURLOPT_RETURNTRANSFER => true ];

        // set [curl option] headers and post data
        $cHeaders = array( 'Connection: close' );
        if( $method === 'POST' && $query && ! empty( $query ) ) :
            $cHeaders[] = 'Content-Length: ' . strlen( $query ) ;
            $cHeaders[] = 'Content-Type: application/x-www-form-urlencoded' ;
			$cOpts[CURLOPT_POST] = true ;
			$cOpts[CURLOPT_POSTFIELDS] = $query ;
        endif ;
        $cOpts[CURLOPT_HTTPHEADER] = $cHeaders ;

        // ignore ssl+tls
        if( $this->ignoreSSL ){
		    $cOpts[CURLOPT_SSL_VERIFYPEER] = false ;
		    $cOpts[CURLOPT_SSL_VERIFYHOST] = false ;
        }

        // set custom context settings like token , header content-type , etc
        if( is_array( $customOptions ) && ! empty( $customOptions ) )
            $cOpts = array_merge( $cOpts , $customOptions );

        // init curl
		$cuh = curl_init( $url );

        // set options
		curl_setopt_array( $cuh , $cOpts );

        // exec request
		$response = null ;
		try{
			$response = curl_exec($cuh);
			curl_close($cuh);
		} catch( Exception $e ){
		    $response = array(
		        'status' => -1 ,
		        'message' => $e->getMessage() ,
		        'error' => $e ,
		        'fail' => true ,
		    );
		}

		return $response;

	}

}