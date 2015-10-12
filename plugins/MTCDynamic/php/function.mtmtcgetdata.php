<?php
function smarty_function_mtmtcgetdata( $args, &$ctx ) {
    $member = $ctx->stash( 'mtc_member' );
    $get = $args[ 'get' ];
    if (! $get ) {
        return '';
    }
    $prefix = '';
    if ( isset( $args[ 'prefix' ] ) ) {
        $prefix = $args[ 'prefix' ];
    }
    if ( isset( $args[ 'id' ] ) ) {
        $id = $args[ 'id' ];
    }
    if ( $id ) {
        if (! ctype_digit( $id ) ) {
            return '';
        }
        if ( $get == 'goods' ) {
            require_once( 'class.mtcgoods.php' );
            $_mtc_object = new MTCGoods;
        }
        if (! $_mtc_object ) {
            return '';
        }
        $cols = $_mtc_object->GetAttributeNames();
        $_mtc_object->Load( $id );
        if ( $_mtc_object->Load( $id ) ) {
            $data = $_mtc_object->GetArray();
            $_data = array();
            foreach( $data as $key => $value ) {
                $_data[ $prefix . $key ] = $value;
                $ctx->__stash[ 'vars' ][ $prefix . $key ] = $value;
            }
            $data = $_data;
            $ctx->__stash[ 'vars' ][ $args[ 'set' ] ] = $data;
        } else {
            foreach ( $cols as $col ) {
                unset( $ctx->__stash[ 'vars' ][ $prefix . $key ] );
            }
            $ctx->__stash[ 'vars' ][ $args[ 'set' ] ] = array();
        }
        // var_dump( $ctx->__stash[ 'vars' ] );
        return '';
    }
}
?>