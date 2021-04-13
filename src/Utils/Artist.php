<?php

namespace Mokka\Utils;

use Mokka\Interfaces\ArtistInterface;

/**
 * Artist
 * 
 * woocommerce needs to be installed
 * 
 */
abstract class Artist extends ArtistInterface
{
    protected $artist_id;
    protected $artist_data;
    public function __construct( int $artist_id = 0)
    {
        $this->artist_id = $artist_id;
        $this->artist_data = [];
    }

    public function setup() 
    {
        $this->setArtist();
    }

    public function setArtist()
    {
        
    }

    public function getArtist() 
    {
        return $this->artist_data;
    }


    /**
     * get the artist by product id.
     * 
     *
     * Get the artist name by provided product id.
     * It checks if the product is a variation and pulls term_ids from the parent product (simple product).
     *
     * @uses get_term_ids_for_product
     * @param int $productId product id
     * @return string
     * @throws string does not throw an exception. just returns an empty string
     **/
    public static function get_artist_from_product(int $productId, $child = false)
    {
    }
    ///
    /**
     * gets all registered term_ids by product_id
     *
     * Undocumented function long description
     *
     * @param int $productId product id
     * @return array
     * @throws conditon
     **/
    public static function get_term_ids_for_product( int $productId) 
    {
        global $wpdb;

        $id = 0;
        $product = wc_get_product($productId);
        // if ($product->is_type('variable')) {
        if ($product->{'post_type'} === 'product_variation') {
            $id = $product->get_parent_id();
        } else {
            $id = $productId;
        }
        
        $term_ids = $wpdb->get_col($wpdb->prepare("SELECT term_taxonomy_id FROM {$wpdb->prefix}term_relationships WHERE object_id = %d", $id));
        return $term_ids;
    }
}