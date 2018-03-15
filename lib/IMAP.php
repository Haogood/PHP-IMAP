<?php

namespace IMAP;

Class IMAP
{

    public static function decodeToUTF8($stringQP, $base = 'windows-1252')
    {
        $pairs = array(
            '?x-unknown?' => "?$base?"
        );
        $stringQP = strtr($stringQP, $pairs);
        return imap_utf8($stringQP);
    }

    public static function Decode($string, $encoding, $charset = 'UTF-8') {
        switch ($encoding) {
            case 0: // 7BIT
            case 1: // 8BIT
            case 2: // BINARY
                return $string;

            case 3: // BASE-64
                return base64_decode($string);

            case 4: // QUOTED-PRINTABLE
                $string = quoted_printable_decode($string);
                $string = mb_convert_encoding($string, 'UTF-8', $charset);
                return $string;
        }
        throw new \Exception('decode paremeter not correct.');
    }
    
    public static function getAttribute($params, $name)
    {
        foreach ($params as $object)
        {
            if ($object->attribute == $name) {
                return self::decodeToUTF8($object->value);
            }
        }
        return NULL;
    }
}