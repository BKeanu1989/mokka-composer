<?php

namespace Mokka\Exporter;

use Mokka\Exporter\Adapter;
use Mokka\Exporter\Builder;

use Mokka\Utils\WpArtist;
class Handler 
{
    protected $send_to;
    protected $order;

    public function __construct($orderId, $options)
    {
        $this->order = wc_get_order($orderId);
        $this->settings = new Settings($options);
        $this->builder = new Builder($this->order);

        $this->config = null;
        $this->adapter = null;

        $this->setup();
    }

    public function setup()
    {
        $this->setConfigForProducer();
        $this->setAdapter();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function setConfigForProducer()
    {
        $artists = [];
        $artist_ids = array_unique(array_column($this->builder->getData(), '_artist_id'));
        foreach ($artist_ids as $artistId) {
            $artist = new WpArtist( (int) $artistId);
            $artistData = $artist->maybeGetParentData();
            $artists[] = $artistData;
        }
        $artists = array_filter($artists);

        $conf = parse_ini_file(ABSPATH . 'conf.ini', true);
        if (count($artists) === 0) {
            $this->config = $conf['Halle'];
            return $conf['Halle'];
        } 
        $uniqueUpperCasedArtistNames = array_unique(array_map(function($x) {
            return ucfirst(strtolower($x->artist_name));
        },$artists));

        $counted = count($uniqueUpperCasedArtistNames);
        if ($counted === 1) {
            if (in_array($uniqueUpperCasedArtistNames[0], ['Nordachse', 'Nordberliner Pils'])) {
                $config_to_use = 'Nordachse';
                $this->config = $conf[$config_to_use]; 
                return $this->config;
            }
        } else {
            $this->config = $conf['Halle']; 
            return $this->config;
        }
    }

    public function getAdapter()
    {
        return $this->adapter;
    }

    public function setAdapter()
    {
        $defaultName = 'Halle';
        $configName = $this->config['name'];
        $name = $configName === '' ? $defaultName : $configName;
        $className = "Mokka\Exporter\Adapter\\" . $name;
        $this->adapter = new $className($this->builder);

        return $this->adapter;
    }

}