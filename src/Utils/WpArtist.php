<?php

namespace Mokka\Utils;
use Mokka\Utils\Artist;
use Mokka\Utils\Logger;
/**
 * Artist
 * 
 * woocommerce needs to be installed
 * 
 */
class WpArtist extends Artist 
{
    public function __construct(int $artist_id = 0)
    {
        parent::__construct($artist_id);
        $this->setData();
    }

    public static function get_artist_by_id(int $id)
    {
        global $wpdb;
        $artist_number = $wpdb->get_var($wpdb->prepare("SELECT artist_nummer FROM {$wpdb->prefix}artists WHERE id = %d", $id));
        return new WpArtist($artist_number);
    }

    /**
     * get the artist by product id.
     * 
     *
     * Get the artist name by provided product id.
     * It checks if the product is a variation and pulls term_ids from the parent product (simple product).
     *
     * @since 0.2.7
     * @uses get_term_ids_for_product
     * @param int $productId product id
     * @return mixed array | null
     * @throws string does not throw an exception. just returns an empty string
     **/
    public static function get_artist_from_product(int $productId, $child = false)
    {
        global $wpdb;
        try {
            $_product = wc_get_product($productId);
            $is_variation = $_product->get_parent_id();
            
            if ($is_variation) {
                $productId = $_product->get_parent_id();
            }

            $artist_id = get_post_meta($productId, '_artist_id', true);
            
            $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}artists WHERE id = %d", $artist_id);
            $result = $wpdb->get_row($query, ARRAY_A);
    
            if (count($result['artist_name']) === 0) return null;
    
            return $result;
        }
        catch(\Exception $e) {
            Logger::write_log($e->getMessage());
        }
    }

    /**
     * get_all_product_ids_by_artist_number
     * 
     * Due to old implementation we are using artist_nummer (yes - it's denglish)
     * Only returns main products (no variations)
     * 
     * @since 0.2.8
     * @return mixed array | null
     */
    public static function get_all_product_ids_by_artist_number(int $artist_id) 
    {
        try {
            global $wpdb;
            $product_ids = $wpdb->get_col($wpdb->prepare("SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = '_artist_id' AND meta_value = %d", $artist_id));
            return $product_ids;
        }
        catch(\Exception $e) {
            Logger::write_log($e->getMessage());
        }
    }

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

    public function setData()
    {
        global $wpdb;
        $this->artist_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}artists WHERE artist_nummer = %d", $this->artist_id));
        return $this->artist_data;
    }

    public function getData() 
    {
        return $this->artist_data;
    }

    public function maybeGetParentData()
    {
        // error_log(print_r($this, 1));
        $parent_id = $this->artist_data->parent_id;
        if ($parent_id == 0) {
            return $this->artist_data;
        }
        $parent = self::get_artist_by_id((int) $parent_id); 
        return $parent->getData();
    }
}