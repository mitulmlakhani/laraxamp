<?php
namespace App\Libraries;

use Gidkom\OpenFireRestApi\OpenFireRestApi;

class Openfire extends OpenFireRestApi
{

    public function __construct()
    {
        parent::__construct();

        $this->secret = env('OPENFIRE_KEY');
        $this->host = env('OPENFIRE_HOST','localhost');
        $this->port = env('OPENFIRE_PORT',9090); # default 9090

        # Optional parameters (showing default values)

        $this->useSSL = false;
        $this->plugin = "/plugins/restapi/v1"; # plugin
    }

    public function get($key)
    {
        return $this->{$key};
    }
}
