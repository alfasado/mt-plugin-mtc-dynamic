<?php
require_once( "class.baseobject.php" );
class MTCSession extends BaseObject
{
    public $_table = 'shop_session';
    protected $_prefix = 'shop_';
    private $_data = null;
}
?>