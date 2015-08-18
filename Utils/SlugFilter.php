<?php

namespace Thrace\MediaBundle\Utils;

class SlugFilter
{
    public static function filter ($filename = '')
    {
        $filename = strip_tags($filename);
        $filename = preg_replace('/[\r\n\t ]+/', ' ', $filename);
        $filename = preg_replace('/[\"\*\/\:\<\>\?\'\|]+/', ' ', $filename);
        $filename = strtolower($filename);
        $filename = html_entity_decode( $filename, ENT_QUOTES, "utf-8" );
        $filename = htmlentities($filename, ENT_QUOTES, "utf-8");
        $filename = preg_replace("/(&)([a-z])([a-z]+;)/i", '$2', $filename);
        $filename = str_replace(' ', '-', $filename);
        $filename = rawurlencode($filename);
        $filename = str_replace('%', '-', $filename);

        return $filename;
    }

}