<?php

namespace pr1\api\providers;

use Exception;
use pr1\api\processor\Processable;

/**
 * Description of Base
 *
 * @author Thurairajah Thujeevan
 */
abstract class Base implements Processable {

    const TABLE_NAME = 'pr1orders';
    
    protected function writeToDb($fields) {
        try {
            $rows = $this->db->insert(self::TABLE_NAME, $fields);
        } catch (Exception $ex) {
            // silently fail
        }
    }

}
