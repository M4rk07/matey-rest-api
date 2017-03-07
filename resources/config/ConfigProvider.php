<?php

namespace Resource\Config;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 6.3.17.
 * Time: 18.13
 */
class ConfigProvider
{

    private $modelsConfig;

    public function __construct() {
        $this->modelsConfig = $this->readModelsConfig();
    }

    public function readModelsConfig() {
        $modelsConfig = array();
        $configFile = fopen(__DIR__."/../../files/models_config.txt", "r") or die("Unable to read models configuration.");

        $arr = array();
        $currentModelName = "";
        while(($line = fgets($configFile)) != false) {
            $line = rtrim($line, "\r\n");
            if($line == "") {
                $modelsConfig[$currentModelName] = $arr;
                $arr = array();
                continue;
            }
            $keyValue = explode("=", $line);

            if($keyValue[0] == "model_name")
                $currentModelName = $keyValue[1];
            else
                $arr[$keyValue[0]] = strpos($keyValue[1], ",") ? explode(",", $keyValue[1]) : $keyValue[1];
        }

        return $modelsConfig;
    }

    public function getModelsConfig() {
        return $this->modelsConfig;
    }

}