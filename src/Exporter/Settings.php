<?php

namespace Mokka\Exporter;

class Settings 
{
    protected $options;
    protected $database_entry = false;
    protected $send_file = false;
    protected $create_file = false;


    public function __construct($options) {
        $this->options = $options;

        $this->handleOptions();
    }

    public function handleOptions() {
        if (count($this->options) > 0) {
            extract($this->options);
        }

        if (isset($database_entry) && $database_entry === true) {
            $this->set_database_entry($database_entry);
        }

        if (isset($send_file) && $send_file === true) {
            $this->set_send_export($send_file);
        }

        if (isset($create_file) && $create_file === true) {
            $this->set_create_file($create_file);
        }
    }
    private function set_database_entry($value) {
        $this->database_entry = $value;
    }

    private function set_send_export($value) {
        $this->send_file =  $value;
    }

    private function set_create_file($value) {
        $this->create_file = $value;
    }

    public function get_database_entry() {
        return $this->database_entry;
    }

    public function get_send_export() {
        return $this->send_file;
    }

    public function get_create_file() {
        return $this->create_file;
    }

    public function get_options() :array {
        return [
            "database_entry" => $this->get_database_entry(),
            "send_file" => $this->get_send_export(),
            "create_File" => $this->get_create_file()
        ];
    }
}