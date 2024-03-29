<?php
/**
 * @file Contains \Drupal\helper\PathHelper
 */

namespace Drupal\helper;

use Drupal\path_alias\AliasManager;
use Drupal\Core\Path\CurrentPathStack;
use Drupal\Core\Entity\EntityTypeManagerInterface;


/**
 * A Class for helping in rendering elements in templates
 *
 */
class PathHelper {

  protected $pathStack;
  protected $aliasManager;
  protected $entityTypeManager;


  /**
   * Class constructor.
   * @param CurrentPathStack $pathStack
   * @param AliasManager $aliasManager
   * @param EntityTypeManagerInterface $entityTypeManager
   */
  public function __construct( CurrentPathStack $pathStack, AliasManager $aliasManager, EntityTypeManagerInterface $entityTypeManager ) {
    $this->aliasManager = $aliasManager;
    $this->pathStack = $pathStack;
    $this->entityTypeManager  = $entityTypeManager;
  }


  /**
   * Return the parent path of the current node
   */
  public function getPathParent () {

    // Get current Path
    $current_path = $this->pathStack->getPath();
    $current_path_alias = $this->aliasManager->getAliasByPath($current_path);

    // Remove Last item of the path (child page title)
    $arrPath = explode ("/",$current_path_alias);
    $lastElement = array_pop($arrPath);
    $pathParent = implode("/",$arrPath);

    // Check if Alias Path exists
    $aliasExists = $this->aliasManager->getAliasByPath($pathParent, 'en');

    if ( $aliasExists ){
      return $pathParent;
    }else{
      return $current_path_alias;
    }

  }


  /**
   * Return the node object based on path
   */
  public function getNidByPath ($path) {

    // Check if Alias Path exists
    $nodePath = $this->aliasManager->getPathByAlias($path, 'en');

    if ( $nodePath ){
      $arrPath = explode ("/",$nodePath);
      return $arrPath[2];
    }else{
      return false;
    }

  }


  /**
   * Return the path based on nid
   */
  public function getPathByNid ($nid) {

    // Check if Path exists
    $nodePath = $this->aliasManager->getAliasByPath('/node/'.$nid);

    if ( $nodePath != '/node/' ){
      // Return path without leading slash
      return substr($nodePath,1);
    }else{
      return false;
    }

  }


}
