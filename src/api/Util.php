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

    public static function getCardType($cardNo) {
        $cards = array(
            "visa" => "(4\d{12}(?:\d{3})?)",
            "amex" => "(3[47]\d{13})",
            "mastercard" => "(5[1-5]\d{14})"
        );
        $names = array("visa", "amex", "mastercard");
        $matches = array();
        $pattern = "#^(?:" . implode("|", $cards) . ")$#";
        $result = preg_match($pattern, str_replace(" ", "", $cardNo), $matches);
        return ($result > 0) ? $names[sizeof($matches) - 2] : false;
    }
    
    public static function sanitize(array &$arr, $filter = FILTER_SANITIZE_STRING, $options = FILTER_FLAG_NO_ENCODE_QUOTES) {
        array_walk_recursive($arr, function(&$value, $key) use ($filter, $options) {
            $value = filter_var(trim($value), $filter, $options);
        });
    }
}
