<?php

namespace Ted ;

defined( 'TExec' ) or die( 'Access Denied' );

abstract class Plugin {

	private $Root = null;

	private $Name = null;

	private $App = null;
	
	public function Name( $Name = null ) {

		if ( $Name && $this->Name === null ) $this->Name = $Name;
		
		return $this->Name;

	}

	public function Root( $Root = null ) {

		if ( is_dir( $Root ) && $this->Root === null ) $this->Root = $Root;
		
		return $this->Root;
	
	}

	public function App( $App = null ) {

		if ( $App && $this->App === null ) $this->App = $App;
		
		return $this->App ;
	
	}
	
	public function OwnerApp(){
		
		return Application::getInstance( $this->App );
		
	}

	public function __call( $name , $params ) {
		
		$theModule = ucfirst( $name );
		
		if ( ! empty( $params ) ) 
			
			foreach( $params as $newP ) 
			
				$theModule .= "." . ucfirst( $newP );
		
		$theModule = trim( $theModule , " ./\\" ) ;

		return $this->AccessModule( $theModule , false );

	}

	public function __get( $name ) {

		return $this->AccessModule( ucfirst( $name ) , false );
	
	}

	protected function AccessModule( $className , $Direct = true ) {

		if ( $Direct ) return $this->OwnerApp( )->{$className} ;

		$cHolder = get_class( $this );
				
		$nHolder = array ();
				
		while( true ) {
				
			$p = substr( $cHolder , 0 , - 9 );
			
			if ( $p == false ) break;
					
			$nHolder[] = $p ;
					
			$cHolder = get_parent_class( $cHolder );
				
		}
		
		$acts = array_values( $nHolder );
		
		$insis = $acts[count( $acts ) - 1];
		
		unset( $acts[count( $acts ) - 1] );
		
		$name = "";
		
		foreach( $acts as $nAc ) $name .= ucfirst( $nAc );
		
		$name .= ucfirst( $className );
		
		$name = trim( $name , "." );
		
		$name = trim( $name , " " );
		
		$name = $insis . $name;
		
		return $this->OwnerApp( )->$name;
	
	}

	protected function AccessVariables( $name , $dataObject ) {

		if ( is_object( $dataObject ) && isset( $dataObject->{$name} ) ) return $dataObject->{$name};
		
		else if ( isset( $this->{$name} ) ) return $this->{$name};
		
		else return null;
	
	}

}