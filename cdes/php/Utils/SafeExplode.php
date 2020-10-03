<?php

/**
 * Sert.php
 * 
 * @author Tianle Xu <xtl@xtlsoft.top>
 * @package Sert
 * @category language
 * @license MIT
 */

namespace Apiles\Clays\ClaysDescriptor\Utils;

class SafeExplode
{
    public static function explode(string $sep, string $str): array
    {
        $splited = str_split($str);
        $iu = new IgnoreUtil();
        $rslt = [''];
        $cnt = 0;
        foreach ($splited as $ch) {
            if (!$iu->shouldIgnore($ch)) {
                if ($sep === '' ? !self::isBlankCharacter($ch) : $ch !== $sep)
                    $rslt[$cnt] .= $ch;
                else if ($rslt[$cnt] !== '') $rslt[++$cnt] = '';
            } else $rslt[$cnt] .= $ch;
        }
        return $rslt;
    }

    public static function isBlankCharacter(string $char): bool
    {
        return in_array($char, ["\t", ' ', "\n", "\r"]);
    }

    public static function isEmptyLine(string $line): bool
    {
        return trim($line) === '';
    }

    public static function isIdentifyCharacter(string $char): bool
    {
        return in_array(
            $char,
            str_split('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_')
        );
    }
}
