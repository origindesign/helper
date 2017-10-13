<?php
/**
 * @file Contains \Drupal\helper\NodeHelper
 */
 
namespace Drupal\helper;


use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityFieldManager;

 
/**
 * A Class for helping in rendering elements in templates
 *
 */
class NodeHelper {


    protected $entityTypeManager;
    protected $entityFieldManager;


    /**
     * EfqQueryEntities constructor.
     * @param EntityTypeManagerInterface $entityTypeManager
     * @param EntityFieldManager $entityFieldManager
     */
    public function __construct(EntityTypeManagerInterface $entityTypeManager, EntityFieldManager $entityFieldManager) {
        $this->entityTypeManager  = $entityTypeManager;
        $this->entityFieldManager  = $entityFieldManager;
    }

    
    /**
     * Return node variables from a specific node ID ti be used in hook page template
     */
    public function getNodeFields ( $nid, $node_type ) {

        // Get the node storage object.
        $node_storage = $this->entityTypeManager->getStorage('node');

        // Get the view builder object
        $view_builder = $this->entityTypeManager->getViewBuilder('node');

        // We use the load function to load a single node object.
        $node = $node_storage->load($nid);
        $fields_def = $this->entityFieldManager->getFieldDefinitions('node', $node_type);

        $fields = [];

        foreach ($fields_def as $field_name => $field_definition) {


            // Get Only Custom Fields
            if (!empty($field_definition->getTargetBundle())) {

                $field = $node->get($field_name);
                $format = $field->format;

                // Format Field only if get format or if it's a link or file
                if (!is_null($format) || $field_definition->getType() == "link" || $field_definition->getType() == "file" ) {

                    $fields[$field_name] = $view_builder->viewField($field, array('label' => 'hidden'));

                    // Otherwise, return raw value
                } else {

                    $fields[$field_name] = $field->value;

                }

            }

        }

        return $fields;

    }


  /**
   * Return file URL from field
   */
  public function getFileUrl ( $nid, $field ) {

    // Get storage objects
    $node_storage = $this->entityTypeManager->getStorage('node');
    $file_storage = $this->entityTypeManager->getStorage('file');

    // Load node and field from vars
    $node = $node_storage->load($nid);

    $node_field = $node->get($field);
    $value = $node_field->getValue();

    if($value){
      // Get File from $field value
      $fid = $value[0]['target_id'];

      // Load File entity from id
      $file = $file_storage->load($fid);

      // Return URL
      return file_create_url($file->getFileUri());

    }

    return false;

  }








}
