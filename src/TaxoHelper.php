<?php
/**
 * @file Contains \Drupal\helper\TaxoHelper
 */
 
namespace Drupal\helper;


use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Path\AliasManager;

 
/**
 * A Class for helping in rendering elements in templates
 *
 */
class TaxoHelper {


    protected $entityTypeManager;
    protected $aliasManager;


    /**
     * TaxoHelper constructor.
     * @param EntityTypeManagerInterface $entityTypeManager
     * @param AliasManager $aliasManager
     */
    public function __construct(EntityTypeManagerInterface $entityTypeManager, AliasManager $aliasManager) {
        $this->entityTypeManager  = $entityTypeManager;
        $this->aliasManager = $aliasManager;
    }

    
    /**
     * Return taxonomy terms from a specific vocabulary
     */
    public function getTaxoTerms ( $vid, $ignoreTids = NULL ) {

        $terms = $this->entityTypeManager->getStorage('taxonomy_term')->loadTree($vid);

        if($ignoreTids){

            $termsSubset = array();

            foreach($terms as $term){
                if(!in_array($term->tid,$ignoreTids)){
                    array_push($termsSubset,$term);
                }
            }

            return $termsSubset;

        }

        return $terms;

    }



    /**
     * Return label of a taxonomy term
     */
    public function getTaxoName ( $tid ) {

        $term = $this->entityTypeManager->getStorage('taxonomy_term')->load($tid);

        return $term->getName();

    }



    /**
     * Return path alias of a taxonomy term
     */
    public function getTaxoPathAlias ( $tid ) {

        $alias = $this->aliasManager->getAliasByPath('/taxonomy/term/'.$tid);

        return $alias;

    }




}
