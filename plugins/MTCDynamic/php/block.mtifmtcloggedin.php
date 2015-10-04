<?php
function smarty_block_mtifmtcloggedin( $args, $content, &$ctx, &$repeat ) {
    if (!isset( $content ) ) {
        $shop_session = $_COOKIE[ 'shop_session' ];
        if ( $shop_session ) {
            $shop_session = preg_replace( '/^[0-9]*::/', '', $shop_session );
            $mt_path = $ctx->mt->config( 'MTDir' );
            require_once( $mt_path . '/plugins/MTCDynamic/php/class.mtcsession.php' );
            $shop_session = $ctx->mt->db()->escape( $shop_session );
            $where = "session_id='$shop_session'";
            $_session = new MTCSession;
            $session = $_session->Find( $where, FALSE, FALSE, array( 'limit' => 1 ) );
            if ( $session ) {
                $prefix = '';
                if ( isset( $args[ 'prefix' ] ) ) {
                    $prefix = $args[ 'prefix' ];
                }
                $session = $session[ 0 ];
                $member_id = $session->member_id;
                require_once( $mt_path . '/plugins/MTCDynamic/php/class.mtcmember.php' );
                $where = "id='$member_id'";
                $_member = new MTCMember;
                $member = $_member->Find( $where, FALSE, FALSE, array( 'limit' => 1 ) );
                if ( $member ) {
                    $member = $member[ 0 ];
                    if (! $member->activated ) {
                        unset( $member );
                    }
                    $data = $member->GetArray();
                    foreach ( $data as $key => $value ) {
                        $ctx->__stash['vars'][ $prefix . $key ] = $value;
                    }
                    return $ctx->_hdlr_if( $args, $content, $ctx, $repeat, TRUE );
                }
            }
        }
    } else {
        return $ctx->_hdlr_if( $args, $content, $ctx, $repeat, $repeat );
    }
}
?>