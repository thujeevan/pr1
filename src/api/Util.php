<?php

namespace pr1\api;

/**
 * Provides utility functions
 *
 * @author Thurairajah Thujeevan
 */
class Util {

    public static function isAmex($cardNo) {
        return preg_match('/^3[47]\d{13}$/', preg_replace("/\D|\s/", "", $cardNo));
    }

    public static function isValidCardNo($cardNo) {
        $cardNo = preg_replace("/\D|\s/", "", $cardNo);
        $cardLength = strlen($cardNo);
        $parity = $cardLength % 2;
        $sum = 0;
        for ($i = 0; $i < $cardLength; $i++) {
            $digit = $cardNo[$i];
            if ($i % 2 == $parity) {
                $digit = $digit * 2;
            }
            if ($digit > 9) {
                $digit = $digit - 9;
            }
            $sum = $sum + $digit;
        }
        $valid = ($sum % 10 == 0);
        return $valid;
    }

}
