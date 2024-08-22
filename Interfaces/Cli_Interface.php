<?php

defined( 'TExec' ) or die( 'Access Denied' );

class Cli_Interface extends Ted\TedInterface {

	private $std = null;

	private $ent = PHP_EOL;

	private $pro = ":";
	
	public function Respond( $directRespond = false ){ 
		
		if ( ! Ted\IsCli( ) ) header( 'Content-Type: text/html; charset=UTF-8' );
	
		parent::Respond(); 

		return true ;
		
	}

	public function __construct( $a = null ) {

		$this->std = fopen( 'php://stdin' , 'a' );
		
		$this->prompt( "TED > " );
	
	}

	public function prompt( $prompt = null ) {

		$this->pro = ( $prompt !== null ) ? $prompt : $this->pro;
		
		return $this->pro;
	
	}

	public function Connect( $directRespond = false ) {
		
		$welcomeMSG = "Hello There ! How Can I Help You =)" ;

		$read = ( $directRespond ) ? $this->CliNewRead( $welcomeMSG ) : $this->CliRead( "" );

		$this->parseTerminal( $read );

		return true ;
	
	}

	public function Response( ) {
		
		$ARGUMENTS = func_get_args();
		
		if ( empty( $ARGUMENTS ) ) array_push( $ARGUMENTS , [ "execute" => "false" ] );
		
		$ARGUMENTS = ( count( $ARGUMENTS ) == 1 && is_array( $ARGUMENTS[0] ) ) ? $ARGUMENTS[0] : $ARGUMENTS ;
		
		//Response Has 3 Main Requirments
		$VIEW = null ; // The View HTML Page Name
		
		$EXECUTE = null ; // EXECUTE Of Application Work
		
		$EXTRA = array() ; // Extra Variables That Passed On
		
		$viewSearchList = [ 'view' ,'html' ,'layout' ,'page' ];
		
		foreach( $ARGUMENTS as $k => $v ){
			
			if ( is_array( $v ) ){
				
				foreach( $viewSearchList as $m ){
					
					foreach ( array_keys( $v ) as $newKey ) {
						
						if ( stristr( $newKey , $m ) ){
							
							$VIEW = $v[$newKey] ;
						}
						
					}
					
				}
				
			} if ( is_int( $k ) && ( is_bool( $v ) || is_string( $v ) ) ) {
				
				if ( is_bool( $v ) ) $EXECUTE = $v ;
				
				else if ( is_string( $v ) ) $VIEW = ( string ) $v ;
				
				unset( $ARGUMENTS[ $k ] );
				
				continue ;
				
			} else if ( is_string( $k ) && is_string( $v ) ) {
				
				foreach( $viewSearchList as $m ){
					
					if ( strtoupper( $k ) === $m ) {
						
						$VIEW = $v ;
						
						unset( $ARGUMENTS[ $k ] );
						
						continue ;
						
					}
					
				}
				
				
			} $EXTRA[ $k ] = $ARGUMENTS[ $k ] ;
			
			unset( $ARGUMENTS[ $k ] );
			
		} $EXTRA = ( is_array( $EXTRA ) ) ? $EXTRA : array() ;

		print "Response" ; return false;

		$result = "[Result:Null]";
	
		if ( $RESULT ) {
				
			$res = $RESULT ;
				
			$result = "[Result:{$res}]";
	
		} if ( count( $NEED ) > 0 ) {
			
			$Needs = " You Need [ " . implode( ',' , $NEED ) . " ] ";
				
			$request = "{$result} {$CODE}:{$MESSAGES}, {$Needs}";
				
			$this->prite( $request );
				
			$this->br( 1 );
				
			if ( count( $NEED ) > 0 ) {
	
				foreach( $NEED as $k => $V ) {
						
					Read : { $read = self::CliRead( "{$V}" ); }
						
					if ( strlen( $read ) > 0 ) {
	
						$this->App( )->data->{$V} = $read;
	
						unset( $NEED[$k] );
							
					} else if ( Ted\IsCli( ) ) goto Read ;
	
				}
	
				if ( count( $NEED ) == 0 ) $this->App( )->print->flush = false;
	
				return true;
					
			}
	
		} else if ( count( $DUMP ) > 0 ) {
				
			$request = "{$result} {$CODE}:{$MESSAGES} ";
				
			$this->prite( $request );
				
			$this->br( 1 );
				
			foreach( $DUMP as $k => $V ) {
	
				$printAble = $V;
	
				printer : {
						
					if ( is_string( $printAble ) ) {
	
						$this->prite( $printAble );
	
						continue;
							
					}
	
				} if ( is_array( $V ) ) {
						
					foreach( $V as $k2 => $V2 ) {
	
						$printAble = $V2;
	
						goto printer ;
							
					}
	
				}
					
			}
	
		} $orders = "";

		if ( self::$ini == false ){
			
			self::$ini = true ;
			
			$orders = "Hello How Can I Help You !" ;
			
		}
		
		return $this->Connect( $orders , true );
	
	}

	/* //////////////////// //////////////////// */
	
	protected final function CliNewRead( $orders = null ) {
	
		$read = null;
	
		$orders = ( $orders ) ? $orders : '';
	
		$this->prite( $orders , 10000 , 0 , 1 );
	
		$read = $this->read( '' );
	
		return $read;
	
	}

	protected final function CliRead( $orders = null ) {
	
		$read = null;
	
		$orders = ( $orders ) ? $orders : '' ;
	
		$read = $this->read( $orders );

		return $read;
	
	}
	
	protected function parseTerminal( $reads = "help" ){

		if ( $reads == "__ Finish __" ) return true ;

		else if ( $reads == "exit" ) {

			$this->write( "  Bye" , null , 1 , 1 );

			return true ;

		} $this->Connect();

	}

	/* //////////////////// //////////////////// */

	public function read( $prompt = null , $time = null ) {

		$prompt = trim( $prompt );
		
		if ( strlen( $prompt ) > 0 ) 

		$prompt = ( substr( $prompt , - 1 ) == ":" ) ? "$prompt " : "$prompt : ";
		
		else $prompt = "";

		$time = ( is_int( $time ) ) ? $time : 25000 ;
		
		$this->prite( $prompt , $time , 0 , 0 );
		
		$line = "";
		
		if ( Ted\IsCli( ) ) {
			
			$line = fgets( $this->std );
			
			$line = str_ireplace( $this->ent , "" , $line );
		
		} else {

			$line = Intel::GetVar( 'cli' , null , 'USER' , false );

			Intel::SetVar( 'cli' , null , 'USER' , true );

			$line = ( $line ) ? $line : "__ Finish __" ;

			$this->write( $line , 0 , 0 , 1 ) ;

		}

		return $line;
	
	}

	public function write($text = null , $time = null , $lnf = 1 , $lns = 1 ) {

		$p = $this->prompt();

		$this->prompt( "" );

		$this->prite( $text , $time , $lnf , $lns );

		$this->prompt( $p );

		return true ;

	}

	public function prite( $text = null , $time = null , $lnf = 1 , $lns = 1 ){

		$text = trim( ( string ) $text );
			
		$this->ln( $lnf );
		
		print $this->prompt( );
		
		if ( ! Ted\IsCli( ) ) {
			
			print $text ;
			
			$this->ln( $lns );
			
			return $text ;
		
		}
		
		if ( strlen( $text ) > 0 ) $text .= " ";
		
		$leng = strlen( $text );

		$time = ( is_int( $time ) ) ? $time : 25000 ;
		
		for( $i = 0 ; $i <= $leng - 1 ; $i ++ ) {
			
			print $text[$i];
			
			usleep( $time );
		
		}
			
		$this->ln( $lns );
		
		return $text;
	
	}

	public function br( $c = 1 ) {

		$r = "" ;

		for ($i=1; $i <= $c ; $i++) $r .= PHP_EOL ;

		print $r ;

		return true ;
	
	}

	public function ln( $c = 1 ) {

		$this->br( $c );

	}

}

?>