<?php
/**
 * @file Contains \Drupal\helper\RenderHelper
 */

namespace Drupal\helper;


use Drupal\Core\Entity\EntityTypeManagerInterface;


/**
 * A Class for helping in rendering elements in templates
 *
 */
class RenderHelper {


  protected $entityTypeManager;


  /**
   * RenderHelper constructor.
   * @param EntityTypeManagerInterface $entityTypeManager
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager  = $entityTypeManager;
  }


  /**
   * Return a block
   */
  public function renderBlock () {

    return [
      '#type' => 'markup',
      '#markup' => '<p>This will render a block.</p>',
    ];


  }




  /**
   * Return a block that uncache the page it's included on
   */
  public function renderUncacheBlock () {
    \Drupal::service('page_cache_kill_switch')->trigger();
    return [
      '#type' => 'markup',
      '#markup' => '<span class="no-cache" style="display:none"></span>',
      '#cache' => array(
        'max-age' => 0,
      ),

    ];


  }


  /** Get array of rendered entities
   * @param $ids
   * @param string $entity_type
   * @param string $view_mode
   * @return array
   */
  public function getRenderedEntities( $ids, $view_mode = 'teaser', $entity_type = 'node' ){

    $render = array();
    foreach($ids as $id){
      $render[] = $this->renderEntity($id,$view_mode,$entity_type);
    }
    return $render;
  }


  /** Render entities
   * @param $id
   * @param string $entity_type
   * @param string $view_mode
   * @return array|bool
   */
  public function renderEntity ($id, $view_mode = 'teaser', $entity_type = 'node') {

    // Get the storage object.
    $entity_storage = $this->entityTypeManager->getStorage($entity_type);

    // Get the view builder object
    $view_builder = $this->entityTypeManager->getViewBuilder($entity_type);

    // Load entity
    $entity = $entity_storage->load($id);

    if ( isset($entity) && $entity !== NULL ){
      return $view_builder->view($entity, $view_mode);
    }else{
      return false;
    }


  }


}
