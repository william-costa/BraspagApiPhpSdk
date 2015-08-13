<?php

/**
 * Methods used across services
 *
 * @version 1.0
 * @author pfernandes
 */
class BraspagUtils
{
    public function getResponseValue($from, $propName){
        return property_exists($from, $propName) ? $from->$propName : null;
    }
}
