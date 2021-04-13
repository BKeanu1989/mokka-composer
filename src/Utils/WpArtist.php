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
     * @return mixed array | null
     * @throws string does not throw an exception. just returns an empty string
     **/
    public static function get_artist_from_product(int $productId, $child = false)
    {
        global $wpdb;
        try {
            $artist_id = get_post_meta($productId, '_artist_id', true);
    
            if (empty($artist_id)) {
                $term_ids = self::get_term_ids_for_product($productId);
                $in_array_sql = implode("', '", $term_ids);
        
                $query = "SELECT * FROM {$wpdb->prefix}artists WHERE term_id IN ('$in_array_sql')";
                if ($child) $query .= " AND parent_id <> '0'";
                if (!$child) $query .= " AND parent_id = 0";
                $result = $wpdb->get_row($query, ARRAY_A);
            } else {
                $_product_id = $productId;
                $_product = wc_get_product($productId);
                // Logger::write_log($_product);
                $is_variation = $_product->get_parent_id();
                
                if ($is_variation) {
                    $_product_id = $_product->get_parent_id();
                    Logger::write_log("we are here");
                }
                $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}artists WHERE id = %d", $artist_id);
                $result = $wpdb->get_row($query, ARRAY_A);
            }
    
            if (count($result['artist_name']) === 0) return null;
    
            return $result;
        }
        catch(Exception $e) {
            Logger::write_log($e->getMessage());
        }

    }

    public static function get_artist_id_from_product(int $productId, $child = false) :string
    {
        global $wpdb;

        $term_ids = self::get_term_ids_for_product($productId);
        $in_array_sql = implode("', '", $term_ids);

        $query = "SELECT * FROM {$wpdb->prefix}artists WHERE term_id IN ('$in_array_sql')";
        if ($child) $query .= " AND parent_id <> '0'";
        if (!$child) $query .= " AND parent_id = 0";
        $result = $wpdb->get_row($query, ARRAY_A);

        if (!isset($result['artist_name'])) return '';

        return $result["artist_name"];
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