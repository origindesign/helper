<?php
/**
 * @file Contains \Drupal\helper\ImageFieldHelper
 */
 
namespace Drupal\helper;

use Drupal\Core\Image\ImageFactory;
use Drupal\Core\Render\Renderer;


 
/**
 * A Class for helping rendering fields in templates
 *
 */
class ImageFieldHelper {
    
    
    protected $imageService;
    protected $rendererService;


    /**
     * Class constructor.
     * @param ImageFactory $imageFactoryService
     * @param Renderer $rendererService
     */
    public function __construct(ImageFactory $imageFactoryService, Renderer $rendererService) {
        $this->imageService = $imageFactoryService;
        $this->rendererService = $rendererService;
        
    }
    
    


    /**
     * Return a raw file URI based on a node and a field name
     *
     * @param object $node
     *   The node object.
     * @param string $fieldName
     *   The name of the field we want to get the file from
     * @return object
     *   Return the file object
     */
    protected function getImageFile ($node, $fieldName) {

        if($node->__isset($fieldName)) {

            if (!$node->get($fieldName)->isEmpty()) {

                return $node->$fieldName->entity;

            }
            return false;
        }
        return false;

    }




    /**
     * Return a rendered image field
     *
     * @param object $node
     *  The node object.
     * @param string $fieldName
     *  The name of the field we want to get the file from
     * @param $style
     *  The responsive style machine name
     * @return bool|mixed|null
     *  Return formatted image field or false if not available
     */
    public function getImageField ($node, $fieldName, $style) {

        $file = $this->getImageFile($node, $fieldName);

        if($file) {

            $fileURI = $file->getFileUri();
            $image = $this->imageService->get($fileURI);

            if ($image->isValid()) {

                // Set up the render array.
                $image_build = [
                    '#theme' => 'responsive_image',
                    '#width' => $image->getWidth(),
                    '#height' => $image->getHeight(),
                    '#responsive_image_style_id' => $style,
                    '#uri' => $file->getFileUri(),
                ];

                // Cache logic.
                $renderer = $this->rendererService;
                $renderer->addCacheableDependency($image_build, $file);

                // Render image.
                return render($image_build);

            }

            return false;

        }

        return false;
 
    }


}
