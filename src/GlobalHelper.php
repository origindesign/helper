<?php
/**
 * @file Contains \Drupal\helper\GlobalHelper
 */
 
namespace Drupal\helper;

 
/**
 * A Class for storing global variables
 *
 */
class GlobalHelper {


    protected $globalData = [];


    /**
     * @return mixed
     */
    public function getGlobalData( $label )
    {
        if( array_key_exists($label, $this->globalData) ){
            return $this->globalData[$label];
        }
        return false;
    }


    /**
     * @param $label
     * @param $data
     */
    public function setGlobalData($label, $data)
    {
        $this->globalData[$label] = $data;
    }


}
