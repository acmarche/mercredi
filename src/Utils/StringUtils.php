<?php

namespace AcMarche\Mercredi\Utils;

use SetBased\Rijksregisternummer\RijksregisternummerHelper;

class StringUtils
{

    public static function cleanNationalRegister(?string $nationalRegister, bool $validate = false): ?string
    {
        if (!$nationalRegister) {
            return null;
        }

        $nationalRegister = RijksregisternummerHelper::clean($nationalRegister);

        if ($validate) {
            if (!RijksregisternummerHelper::isValid($nationalRegister)) {
                return null;
            }
        }

        return $nationalRegister;
    }
}