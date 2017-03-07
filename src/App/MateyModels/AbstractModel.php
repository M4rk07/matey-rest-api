<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 7.11.16.
 * Time: 00.00
 */

namespace App\MateyModels;


use App\Constants\Defaults\DefaultDates;
use AuthBucket\OAuth2\Model\ModelInterface;

abstract class AbstractModel implements ModelInterface
{

    protected $id;
    protected $dateFormat = 'Y-m-d H:i:s';

    public function getId(){
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function createDateTimeFromString ($dateTimeString) {
        return \DateTime::createFromFormat(DefaultDates::DATE_FORMAT, $dateTimeString);
    }

    public function createArrayFromString ($string) {
        return explode(" ", $string);
    }

    public function createStringFromArray ($array) {
        return implode(" ", $array);
    }

    public abstract function getSetFunction(array $props, $type = 'get');

    public function asArray ($fields) {

        $keyValues = array();

        if(!is_array($fields)) $fields = array($fields);

        foreach($fields as $field) {
            $props['key'] = $field;
            $thisValue = $this->getSetFunction($props);
            if(!empty($thisValue)) $keyValues[$field] = $thisValue;
        }

        return $keyValues;
    }

    public function setValuesFromArray($values)
    {

        foreach($values as $key => $value) {
            $props['key'] = $key;
            $props['value'] = $value;
            $this->getSetFunction($props, 'set');
        }

    }

}