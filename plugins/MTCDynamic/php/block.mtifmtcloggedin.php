<?php
function smarty_block_mtifmtcloggedin( $args, $content, &$ctx, &$repeat ) {
    if (!isset( $content ) ) {
        $member = $ctx->stash( 'mtc_member' );
        if ( $member ) {
            return $ctx->_hdlr_if( $args, $content, $ctx, $repeat, TRUE );
        }
        $shop_session = $_COOKIE[ 'shop_session' ];
        if ( $shop_session ) {
            $shop_session = preg_replace( '/^[0-9]*::/', '', $shop_session );
            $shop_session = $ctx->mt->db()->escape( $shop_session );
            $prefix = '';
            if ( isset( $args[ 'prefix' ] ) ) {
                $prefix = $args[ 'prefix' ];
            }
            require_once( 'class.mtcmember.php' );
            $where = '';
            $_member = new MTCMember;
            $extras[ 'join' ] = array(
                'shop_session' => array(
                    'condition' => "(member.id=shop_session.member_id AND shop_session.session_id='$shop_session') LIMIT 1"
                )
            );
            $member = $_member->Find( $where, FALSE, FALSE, $extras );
            if ( $member ) {
                $member = $member[ 0 ];
                if (! $member->activated ) {
                    unset( $member );
                }
                $ctx->stash( 'mtc_member', $member );
                $data = $member->GetArray();
                $ctx->__stash[ 'vars' ][ 'member' ] = $data;
                foreach ( $data as $key => $value ) {
                    $ctx->__stash[ 'vars' ][ $prefix . $key ] = $value;
                }
                return $ctx->_hdlr_if( $args, $content, $ctx, $repeat, TRUE );
            }
        }
    } else {
        return $ctx->_hdlr_if( $args, $content, $ctx, $repeat, $repeat );
    }
}
?>