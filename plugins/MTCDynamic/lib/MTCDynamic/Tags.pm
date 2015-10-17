package MTCDynamic::Tags;

use strict;
use warnings;
use Data::Dumper;

sub _hdlr_if_mtc_logged_in {
    my ( $ctx, $args, $cond ) = @_;
    require MTC::Model::Member;
    require MTC::Model::ShopSession;
    require MTC::Session;
    my $app = MT->instance();
    my $shop_session = $app->cookie_val( 'shop_session' );
    my ( $id, $token );
    if ( $shop_session =~ m/(.*?)::(.*$)/ ) {
        $id = $1;
        $token = $2
    }
    my $expired = MTC::Session->session_expired_date();
    my ( $session ) = MTC::Model::ShopSession->search(
        { id => $id, last_activity => { op => '>', value => "$expired" } },
        { for_update => 1 } );
    if (! $session ) {
        return;
    }
    if ( $session->session_id ne $token ) {
        $session = undef;
        return;
    }
    my $member = MTC::Model::Member->lookup_un_throwable( $session->member_id );
    if ( (! $member ) || (! $member->activated ) ) {
        return;
    }
    if (! $args->{ ignore_delete } ) {
        if ( $member->delete_flag ) {
            return;
        }
    }
    $ctx->stash( 'vars' )->{ 'shop_session' } = $shop_session;
    my $column_values = $member->column_values;
    $ctx->stash( 'vars' )->{ 'member' } = $column_values;
    $ctx->stash( 'mtc_member', $member );
    my $prefix = $args->{ prefix } || '';
    for my $key ( keys %$column_values ) {
        $ctx->stash( 'vars' )->{ $prefix . $key } = $column_values->{ $key };
    }
    return 1;
}

sub _hdlr_mtc_get_member_data {
    my ( $ctx, $args, $cond ) = @_;
    my $member = $ctx->stash( 'mtc_member' );
    my $get = $args->{ get };
    if ( (! $get ) || (! $member ) ) {
        return '';
    }
    my $shop_session = $ctx->stash( 'vars' )->{ 'shop_session' };
    my $direction = $args->{ sort_order };
    my $sort = $args->{ sort_by };
    my $offset = $args->{ offset };
    my $limit = $args->{ limit };
    if ( $get eq 'favorite' ) {
        # MTC::Model::Goods;
    } elsif ( $get eq 'member_coupon' ) {
        # MTC::Model::Coupon;
    } elsif ( $get eq 'cart_item' ) {
        # MTC::Model::Cart;
        # MTC::Model::Variation;
    }
}

1;