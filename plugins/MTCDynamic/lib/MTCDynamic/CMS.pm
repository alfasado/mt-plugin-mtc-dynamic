package MTCDynamic::CMS;

use strict;
use warnings;

sub _mtc_test {
    my $app = shift;
    my $component = MT->component( 'MTCDynamic' );
    my $tmpl = File::Spec->catfile( $component->path, 'tmpl', '_mtc_test.tmpl' );
    my $param = {};
    return $app->build_page( $tmpl, $param );
}

1;