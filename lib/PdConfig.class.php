<?php
class PdConfig
{
    protected $gcal_auth_email;
    protected $gcal_p12_keyfile;
    protected $errors;

    public function __construct()
    {
        $this->parse();
    }

    public function parse()
    {
        $this->errors = array();

        $config_file = dirname(__FILE__) . '/../config.ini';
        if (!file_exists($config_file)) {
            $this->errors[] = "config file not found. needs config.ini";
            return;
        }

        $settings = parse_ini_file($config_file);
        if (!array_key_exists('gcal_auth_email', $settings)) {
            $this->errors[] = "gcal_auth_email value not found in config.ini";
            exit(1);
        }
        $this->gcal_auth_email = $settings['gcal_auth_email'];

        if (!array_key_exists('gcal_p12_keyfile', $settings)) {
            $this->errors[] = "gcal_p12_keyfile value not found in config.ini";
        }
        $p12_keyfile = dirname(__FILE__) . '/../' . $settings['gcal_p12_keyfile'];
        if (!file_exists($p12_keyfile)) {
            $this->errors[] = $p12_keyfile . " not found";
        }
        $this->gcal_p12_keyfile = $p12_keyfile;
    }

    public function getGcalAuthEmail()
    {
        return $this->gcal_auth_email;
    }

    public function getGcalP12KeyfilePath()
    {
        return $this->gcal_p12_keyfile;
    }

    public function hasErrors()
    {
        return count($this->errors) != 0;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getErrorMessageText()
    {
        return implode('\n', $this->errors) . "\n";
    }
}
