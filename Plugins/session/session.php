<?php

defined( 'TExec' ) or die( 'Access Denied' );

#[AllowDynamicProperties]
class sessionPlugin extends Ted\Plugin {

	private $oldId ;

	private $oldName ;

	private $oldData ;

	private $oldParams ;
	
	public function __construct( ){}
	
	public function __call( $name , $param = null ){ }
	
	public function __get( $name ) {

		$selfi = isset( $this->{$name} ) ? $this->{$name} : null ;

		$self = ( isset( $_SESSION[ $name ] ) ) ? $_SESSION[ $name ] : $selfi ;

		if ( ! is_null( $self ) ) $_SESSION[ $name ] = $self ;

		return $self ;
	
	}

	public function __set( $prop , $value ) {

		if ( ! is_null( $value ) ) $_SESSION[ $prop ] = $this->{$prop} = $value ;

		return $this->{$prop} ;
	
	}

	public function __unset( $name ){

		$this->{$name} = null ;

		if ( isset( $_SESSION[ $name ] ) ) unset( $_SESSION[ $name ] );

		return $this ;

	}

	public function unsetVars( ){

		foreach ( func_get_args() as $value )

			if ( ! is_null( $value ) )  {

				$this->{$value} = null ;

				if ( isset( $_SESSION[ $value ] ) ) unset( $_SESSION[ $value ] );

			} 

	}

	public function start( $name = null , $id = null , $cookies = array() , $reset = false ){ 
	    
		if( $reset ) { // Reset Old Session
		    
		    $this->oldId = session_id();
    
    		$this->oldName = session_name();
    
    		if ( strlen( $this->oldId ) ){
    
    			$this->oldData = session_encode();
    
    			$this->oldParams = session_get_cookie_params();
    
    		} session_write_close();

    		session_abort(); // Finish Old Session
    		
		} 
		
		/// Start New Session
		$cookies = ( ! empty( $cookies ) ) ? 
			$cookies : [ "600" , TWeb_Path , TWeb_Domain , null , null ] ;
		$this->Cookie( $cookies );
	
	    
		$name = ( $name !== null ) ? $name : "TedSsid" ;
		$this->Name( $name );

		$id = ( $id !== null ) ? $id : null ;
		$this->Id( $id ) ;
		
		session_start();

	}

	public function stop(){ session_write_close(); }

	public function close(){ session_write_close(); }

	public function destroy( $startOver = false ){

		session_destroy();

		if ( $startOver ) {

			$this->Cookie( [ 0 , TWeb_Path , TWeb_Domain , null , null ] );

			session_start( );

			session_regenerate_id( true );

		}

	}

	public function Id( $id = null ){

		is_string( $id ) && strlen( $id ) >= 10 && session_id( $id ) ;

		return session_id();

	}

	public function Name( $name = null ) {

		if( is_string( $name ) && strlen( $name ) >= 1 ){
		    
		    ini_set( 'session.name' , $name );
		    
		    session_name( $name ) ;
		    
		} return session_name();

	}

	public function Cookie( ){

		$params = func_get_args() ;

		$params = ( isset( $params[0] ) && is_array( $params[0] ) && count( $params ) == 1 ) ? $params[0] : $params;

		$secure = ( TWeb_Schame === "https" ) ? true : false ;

		$args = array( "600" , TWeb_Path , TWeb_Domain , $secure , true ) ;

		foreach ( $params as $key => $value ) {

			switch ( $key ) {

				case 'lifetime':

					$args[0] = ( int ) $value ;

					unset( $params[ $key ] );

				break;

				case 'path':

					$args[1] = ( string ) $value ;

					unset( $params[ $key ] );
					
				break;
				
				case 'domain':

					$args[2] = ( string ) $value ;

					unset( $params[ $key ] );
					
				break;
				
				case 'secure':

					$args[3] = ( boolean ) $value ;

					unset( $params[ $key ] );
					
				break;
				
				case 'httponly':

					$args[4] = ( boolean ) $value ;

					unset( $params[ $key ] );
					
				break;

				case is_int( $key ) :

					if ( ! is_null( $value ) ) $args[ $key ] = $value ;

					unset( $params[ $key ] );

				break ;
				
			}

		}

		call_user_func_array( "session_set_cookie_params" , $args ) ;

		return session_get_cookie_params();

	}

}