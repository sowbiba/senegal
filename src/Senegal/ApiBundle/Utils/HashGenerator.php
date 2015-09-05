<?php

namespace Senegal\ApiBundle\Utils;

final class HashGenerator
{
    public static function generate()
    {
        return base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
    }
}
