<?php
function smarty_block_mtifmtcloggedin( $args, $content, &$ctx, &$repeat ) {
    if (!isset( $content ) ) {
        $member = $ctx->stash( 'mtc_member' );
        if ( $member ) {
            return $ctx->_hdlr_if( $args, $content, $ctx, $repeat, TRUE );
        }
        $shop_session = $_COOKIE[ 'shop_session' ];
        if ( $shop_session ) {
            $mt_path = $ctx->mt->config( 'MTDir' );
            $separator = DIRECTORY_SEPARATOR;
            $cfg = $mt_path . $separator . 'addons' . $separator . 
                'Commerce.pack' . $separator . 'mtc-config.yaml';
            $spyc = $mt_path . $separator . 'plugins' . $separator . 
                'MTCDynamic' . $separator . 'php' . $separator . 'extlib' .
                $separator . 'spyc' . $separator . 'spyc.php';
            require_once( $spyc );
            $config = Spyc::YAMLLoad( $cfg );
            if ( isset( $config[ 'SESSION_EXPIRES' ] ) ) {
                $expires = $config[ 'SESSION_EXPIRES' ];
            } else {
                $expires = 43200;
            }
            $expires *= 60;
            require_once( 'MTUtil.php' );
            $t = time();
            $ts = offset_time_list( $t );
            $ts = sprintf( "%04d%02d%02d%02d%02d%02d",
                            $ts[5]+1900, $ts[4]+1, $ts[3], $ts[2], $ts[1], $ts[0] );
            $ts = datetime_to_timestamp( $ts );
            $ts = $ts - $expires;
            $ts = date( "YmdHis",$ts );
            $ts = $ctx->mt->db()->ts2db( $ts );
            $shop_session = preg_replace( '/^[0-9]*::/', '', $shop_session );
            $shop_session = $ctx->mt->db()->escape( $shop_session );
            $prefix = '';
            if ( isset( $args[ 'prefix' ] ) ) {
                $prefix = $args[ 'prefix' ];
            }
            require_once( 'class.mtcmember.php' );
            $where = "member.delete_flag=0 AND shop_session.last_activity > '${ts}'";
            if ( isset( $args[ 'ignore_delete' ] ) ) {
                if ( $args[ 'ignore_delete' ] ) $where = "shop_session.last_activity > '${ts}'";
            }
            $condition = "(member.id=shop_session.member_id AND shop_session.session_id='$shop_session') LIMIT 1";
            if ( $where ) {
                $condition = "${where} AND ${condition}";
            }
            $_member = new MTCMember;
            $extras[ 'join' ] = array(
                'shop_session' => array(
                    'condition' => $condition
                )
            );
            $member = $_member->Find( '', FALSE, FALSE, $extras );
            if ( $member ) {
                $ctx->__stash[ 'vars' ][ 'shop_session' ] = $shop_session;
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
            } else {
                require_once( 'class.mtcsession.php' );
                $_session = new MTCSession;
                $session = $_session->Find( "session_id = '${shop_session}' AND last_activity > '${ts}'" );
            }
        }
    } else {
        return $ctx->_hdlr_if( $args, $content, $ctx, $repeat, $repeat );
    }
}
?>