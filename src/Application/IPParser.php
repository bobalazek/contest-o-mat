<?php

namespace Application;

class IPParser
{
    public static function parse($ip)
    {
        return json_decode(
            file_get_contents('http://www.geoplugin.net/json.gp?ip='.$ip)
        );
    }
}
