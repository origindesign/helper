<?php
/**
 * @file Contains \Drupal\helper\DateFieldHelper
 */

namespace Drupal\helper;

use Drupal\Core\Render\Renderer;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;


/**
 * A Class for helping rendering fields in templates
 *
 */
class DateFieldHelper {


  protected $rendererService;


  /**
   * Class constructor.
   * @param Renderer $rendererService
   */
  public function __construct( Renderer $rendererService) {
    $this->rendererService = $rendererService;

  }


  public function getRawDates ($node, $fieldName){

    if ( isset($node->{$fieldName}) ){

      $result = array();

      $date_field = ($node->{$fieldName}->getValue());

      $fromDate = new DrupalDateTime($date_field[0]['value']);
      $fromDate->setTimezone(new \DateTimezone(DateTimeItemInterface::STORAGE_TIMEZONE));

      $result['from'] = $fromDate->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);
      $result['to'] = false;

      if ( isset($date_field[0]['end_value']) ){
        $toDate = new DrupalDateTime($date_field[0]['end_value']);
        $toDate->setTimezone(new \DateTimezone(DateTimeItemInterface::STORAGE_TIMEZONE));
        $result['to'] =  $toDate->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);
      }

      return $result;

    }

    return false;

  }



}
