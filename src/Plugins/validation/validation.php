<?php

defined( 'TExec' ) or die( 'Access Denied' );

#[AllowDynamicProperties]
class validationPlugin extends Ted\Plugin {
	
	public function validate( $input , $conditions ){

		if( is_null( $input) || empty( $input ) )

		if( empty( $conditions ) )
			return $input ;

		$conditions = strtolower( $conditions );

		$ex = explode( '|' , $conditions );

		foreach( $ex as $condition ):

			$ch = explode( ':' , $condition );

			$check = $ch[0] ;

			$conRes = null ;

			// if it is php function for eval
			if( stristr( $check , '$' ) !== false )
				$conRes = @eval( 'return ' . str_ireplace( '$' , "'{$input}'" , $check ) . ';' );

			else if( function_exists( $check ) )
				$conRes = $check( $input );

			// no deep check : just checking condition value
			// example : is_array
			if( count( $ch ) === 1 ){

				if( is_bool( $conRes ) && ! $conRes )
					return false ;

				else $input = $conRes ; // trim , substr , ...

				continue ;

			} // else 

			$conResValCondition = $ch[1] ;

			if( is_numeric( $conRes ) ){

				// strlen:10-15 | strlen:[10-15] | strlen:>=10 
				// strlen:>=10&<=20 | strlen:10 | strlen:10-15|20-23 

				if( stristr( $conResValCondition , '&' ) !== false ){ // and

					$conChList = explode( '&' , $conResValCondition );

					foreach( $conChList as $cond )
						if( ! $this->checkNumbersCondition( $cond , $conRes ) )
							return false ;

				} if( stristr( $conResValCondition , '|' ) !== false ){ // or

					$conChList = explode( '|' , $conResValCondition );

					$ok = false ;

					foreach( $conChList as $cond )
						if( $this->checkNumbersCondition( $cond , $conRes ) )
							$ok = true ;

					if( $ok )
						continue ;

				} else  // single condition
					if( ! $this->checkNumbersCondition( $conResValCondition , $conRes ) )
						return false ;

			} else if( is_string( $conRes ) ){

				if( $conResValCondition[0] === '!' ){

					if( $conRes === substr( $conResValCondition , 1 ) )
						return false ;
					
				} else if( $conRes === $conResValCondition )
					continue ;

			}
			
		endforeach;

		return $input ;

	}

	protected function checkNumbersCondition( $condition , $result ){

		// 10-14 | 10-15-20 -> not 15
		if( stristr( $condition , '-' ) !== false ){

			$range = explode( '-' , $condition );

			if( count( $range ) > 2 ){ //strlen:10-14-18 : 

				if( ! empty( $range[0] ) && $result < $range[0] )
					return false ;

				if( ! empty( $range[(count($range)-1)] ) && $result > $range[(count($range)-1)] )
					return false ;

				array_pop( $range );
				array_shift( $range );

				foreach( $range as $not )
					if( $result == $not ) // just checking values not types!
						return false ;

			} else { // count === 2 

				if( ! empty( $range[0] ) && $result < $range[0] )
					return false ;

				if( ! empty( $range[1] ) && $result > $range[1] )
					return false ;

			} return true ;

		} if( stristr( $condition , '>=' ) ){

			$val = str_ireplace( '>=' , '' , $condition );

			if( $result >= $val )
				return true ;

		} if( stristr( $condition , '<=' ) ){

			$val = str_ireplace( '<=' , '' , $condition );

			if( $result <= $val )
				return true ;

		} if( stristr( $condition , '=' ) ){

			$val = str_ireplace( '=' , '' , $condition );

			if( $result == $val )
				return true ;

		} if( stristr( $condition , '!=' ) || stristr( $condition , '!' ) ){
			
			$val = str_ireplace( '!=' , '' , $condition );
			$val = str_ireplace( '!' , '' , $condition );

			if( $result != $val )
				return true ;
			
		} return false ;

	}

}

?>