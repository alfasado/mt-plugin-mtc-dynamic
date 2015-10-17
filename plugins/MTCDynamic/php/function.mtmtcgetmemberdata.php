<?php
function smarty_function_mtmtcgetmemberdata( $args, &$ctx ) {
    $member = $ctx->stash( 'mtc_member' );
    $get = $args[ 'get' ];
    if ( (! $get ) || (! $member ) ) {
        return '';
    }
    $prefix = '';
    if ( isset( $args[ 'prefix' ] ) ) {
        $prefix = $args[ 'prefix' ];
    }
    if ( isset( $args[ 'limit' ] ) ) {
        $limit = $args[ 'limit' ];
    } else if ( isset( $args[ 'lastn' ] ) ) {
        $limit = $args[ 'lastn' ];
    }
    if ( $limit && (! ctype_digit( $limit ) ) ) {
        $limit = NULL;
    }
    if ( isset( $args[ 'offset' ] ) ) {
        $offset = $args[ 'offset' ];
    }
    if ( $offset && (! ctype_digit( $offset ) ) ) {
        $offset = 0;
    } else {
        $offset = 0;
    }
    if ( isset( $args[ 'sort_order' ] ) ) {
        $sort_order = strtolower( $args[ 'sort_order' ] );
        if ( ( $sort_order != 'ascend' ) && ( $sort_order != 'descend' ) ) {
            $sort_order = '';
        } else {
            if ( $sort_order == 'ascend' ) {
                $sort_order = 'asc';
            } else {
                $sort_order = 'desc';
            }
        }
    }
    if ( isset( $args[ 'sort_by' ] ) ) {
        $sort_by = $args[ 'sort_by' ];
    }
    $extra = ' ';
    if ( $limit ) {
        $extra .= "limit ${offset},${limit} ";
    }
    $object_id = $member->id;
    $search_key = 'member_id';
    if ( $get == 'favorite' ) {
        $table = 'goods';
        require_once( 'class.mtcgoods.php' );
        $_mtc_object = new MTCGoods;
    } elseif ( $get == 'member_coupon' ) {
        $table = 'coupon';
        require_once( 'class.mtcmembercoupon.php' );
        $_mtc_object = new MTCMemberCoupon;
    } elseif ( $get == 'cart_item' ) {
        require_once( 'class.mtccart.php' );
        $_cart = new MTCCart;
        $cart = $_cart->Find( "member_id=${object_id}", FALSE, FALSE, array( 'limit' => 1 ) );
        if ( is_array( $cart ) ) {
            $cart = $cart[ 0 ];
        } else {
            $ctx->__stash[ 'vars' ][ $args[ 'set' ] ] = array();
            return '';
        }
        $table = 'variation';
        require_once( 'class.mtcvariation.php' );
        $_mtc_object = new MTCVariation;
        $object_id = $cart->id;
        $search_key = 'cart_id';
    }
    if (! $_mtc_object ) {
        $ctx->__stash[ 'vars' ][ $args[ 'set' ] ] = array();
        return '';
    }
    $condition = "(${get}.${search_key}=${object_id} AND ${table}.id=${get}.${table}_id)";
    if ( $sort_by && ( $_mtc_object->has_column( $sort_by ) ) ) {
        $sort_by = "${table}.${sort_by}";
        $extra = " order by ${sort_by} ${sort_order} ${extra}";
    }
    $extras[ 'join' ] = array(
        $get => array(
            'condition' => $condition . $extra
        )
    );
    $where = '';
    $mtc_objects = $_mtc_object->Find( $where, FALSE, FALSE, $extras );
    $_mtc_objects = array();
    if ( is_array( $mtc_objects ) ) {
        foreach( $mtc_objects as $obj ) {
            $data = $obj->GetArray();
            if ( $prefix ) {
                $_data = array();
                foreach( $data as $key => $value ) {
                    $_data[ $prefix . $key ] = $value;
                }
                $data = $_data;
            }
            array_push( $_mtc_objects, $data );
        }
    }
    $ctx->__stash[ 'vars' ][ $args[ 'set' ] ] = $_mtc_objects;
}
?>