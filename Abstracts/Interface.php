<?php

namespace Ted ;

defined( 'TExec' ) or die( 'Access Denied' );

abstract class TedInterface {
	
	protected $App;
	protected $Root ;
	protected $Www ;

	public abstract function Response();
	
	public function __construct( $App = null ){ $this->setApp( $App ); }
	
	protected function setApp( $AppName = null , $InterfaceSearchList = array() ){

		$App = Application::getInstance( $AppName );
		
		if ( is_object( $App ) ) {
			
			$this->App = $AppName ;
			
			$this->Www = $App->AppWww() ;
			
			$interfaceRoot = FindDirectory( $App->AppRoot() , $InterfaceSearchList ) ;
			
			$interfaceRoot = ( $interfaceRoot ) ? $interfaceRoot : $App->AppRoot() ;
			
			$this->setRoot( $interfaceRoot ) ;
			
		}

		return true ;
		
	}
	
	protected function setRoot( $Root = null ){ if( $Root && is_dir( $Root ) ) $this->Root = $Root ; }
	
	public function App() { return Application::getInstance( $this->App ); }
	
	public function Root() { return $this->Root; }

	public function getApp(){ return $this->App(); }

	public function getRoot(){ return $this->Root(); }

	public function LinkRoute( $Component = null ){

		if ( ! $Component ) return $this->App()->AppWww() ;

		$Component = str_ireplace( "/" , DIRECTORY_SEPARATOR , trim( $Component , " /\\") ) ;

		$Component = str_ireplace( "\\" , DIRECTORY_SEPARATOR , $Component ) ;

		$Component = explode( DIRECTORY_SEPARATOR , $Component ) ;

		$Link = $this->App()->AppWww() . "/" . implode( "/" , $Component );

		return trim( $Link , " /\\" );

	}

	public function EchoLink( $Component = null ){ print $this->LinkRoute( $Component ); }

	public function PrintLink( $Component = null ){ print $this->EchoLink( $Component ); }

	public function ELink( $Component = null ){ print $this->EchoLink( $Component ); }

	public function PLink( $Component = null ){ print $this->EchoLink( $Component ); }

	public function GetLink( $Component = null ){ return $this->LinkRoute( $Component ); }

	public function Respond( $DirectResponse = true ){
		
		// If Html Based UI
		if ( method_exists( $this , "Connect" ) ){
		
			$DirectResponse = is_bool( $DirectResponse ) ? $DirectResponse : true ;
				
			// public function Connect( $directResponse = false ){ ... $this->Response() ... }
			// Having 'Connect' Means That , The Interface Uses It's Own Routing And Responses !
			return $this->Connect( $DirectResponse );

		} else // CLI/JSON/Direct-PHP Execute
			return $this->App()->Execute();

		exit();

	}
	
}

?>