<?php

defined( 'TExec' ) or die( 'Access Denied' );

#[AllowDynamicProperties]
class accessPlugin extends Ted\Plugin {

    private $users ; /* array(
        'admin' => array( 'username' => 'password' ) ,
        'user' => array( 'guestname' => 'guestpass' ) ,
    ); */
    private $level ; // admin || user

    public function init( $users ){ $this->users = $users ; }

    public function getLevel(){ return $this->level ; }
    public function setLevel( $level ){
        if( is_array( $this->users ) && isset( $this->users[ $level ] ) ){
            $this->level = strtolower( $level ) ;
            return true ;
        } else return false ;
    }

    public function setUser( $user , $pass ){
        $level = $this->control( $user , $pass );
        if( $level === false )
            return false ;
        $this->setLevel( $level ) ;
        return true ;
    }

    public function control( $user , $pass ){
        foreach( $this->users as $level => $list )
            foreach( $list as $uName => $uPass )
                if( $uName === $user && $uPass === $pass )
                    return $level ;
        return false ;
    }

    public function is( $level ){
        return $this->level === strtolower( $level ) ? true : false ; }

}