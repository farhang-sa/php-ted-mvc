<?php

defined( 'TExec' ) or die( 'Access Denied' );

#[AllowDynamicProperties]
class httpRequestPlugin extends Ted\Plugin {

    private $ignoreSSL = false ;

    public function ignoreSSL( $ignore = true ){
        $this->ignoreSSL = $ignore ; }

    public function get( $url , $customContext = array() ){
        return $this->Request( $url , null , 'GET' , $customContext ); }

    public function post( $url , $data , $customContext = array() ){
        return $this->Request( $url , $data , 'POST' , $customContext ); }

    public function curlGet( $url , $customContext = array() ){
        return $this->RequestCurl( $url , null , 'GET' , $customContext ); }

    public function curlPost( $url , $data , $customContext = array() ){
        return $this->RequestCurl( $url , $data , 'POST' , $customContext ); }

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
        if( $query && ! empty( $query ) ) :
            $cHeaders[] = 'Content-Length: ' . strlen( $query ) ;
            if( $method === 'POST' )
                $cHeaders[] = 'Content-Type: application/x-www-form-urlencoded' ;
            $contextData['content'] = $query ;
        endif ;
        $contextData['header'] = implode( "\r\n" , $cHeaders ) ;

        // ignore ssl+tls
        if( $this->ignoreSSL )
            $contextData['ssl'] = [ 'verify_peer' => false , 'verify_peer_name' => false ] ;

        // set custom context settings like token , content-type , etc
        if( is_array( $customContext ) && ! empty( $customContext ) )
            foreach ($customContext as $k => $v)
                $contextData[$k] = $v ;

        // Create context resource for our request
        $context = stream_context_create(array( 'http' => $contextData ));

        // Read page
        $response = null ;
        try{
            $response = file_get_contents( $url , false , $context ) ;
        } catch( Exception $e ){
            $response = json_encode( array(
                'status' => -1 ,
                'message' => $e->getMessage() ,
                'error' => $e ,
                'fail' => true ,
            ));
        }

        return $response ;

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
        if( $query && ! empty( $query ) ) :
            $cHeaders[] = 'Content-Length: ' . strlen( $query ) ;
            if( $method === 'POST' ) :
                $cHeaders[] = 'Content-Type: application/x-www-form-urlencoded' ;
                $cOpts[CURLOPT_POST] = true ;
                $cOpts[CURLOPT_POSTFIELDS] = $query ;
            else :
                // set data in url
                $url .= '?' . $query ;
                $cOpts[CURLOPT_CUSTOMREQUEST] = $method ;
            endif ;
        endif ;
        $cOpts[CURLOPT_HTTPHEADER] = $cHeaders ;

        // ignore ssl+tls
        if( $this->ignoreSSL ){
            $cOpts[CURLOPT_SSL_VERIFYPEER] = false ;
            $cOpts[CURLOPT_SSL_VERIFYHOST] = false ;
        }

        // set custom context settings like token , header content-type , etc
        if( is_array( $customOptions ) && ! empty( $customOptions ) )
            foreach ($customOptions as $k => $v)
                if( ! is_array( $v ) ) :
                    $cOpts[$k] = $v ;
                elseif( ! isset( $cOpts[$k] ) ) :
                    $cOpts[$k] = $v ;
                elseif( is_array( $cOpts[$k] ) ) :
                    foreach( $v as $opt => $val ) :
                        if( $val === null && isset( $cOpts[$k][$opt] ) ) :
                            unset( $cOpts[$k][$opt] );
                        else :
                            $cOpts[$k][$opt] = $val ;
                        endif ;
                    endforeach ;
                else :
                    $cOpts[$k] = $v ;
                endif ;

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
            $response = json_encode( array(
                'status' => -1 ,
                'message' => $e->getMessage() ,
                'error' => $e ,
                'fail' => true ,
            ));
        }

        return $response;

    }

}