<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 7.11.16.
 * Time: 00.00
 */

namespace App\MateyModels;


use App\Constants\Defaults\DefaultDates;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use AuthBucket\OAuth2\Model\ModelInterface;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;

abstract class AbstractModel implements ModelInterface
{

    protected $id;
    protected $allFields;

    public function __construct($allFields = null) {
        $this->allFields = $allFields;
    }

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

    public function asArray ($fields = null) {

        $keyValues = array();

        if($fields === null) $fields = $this->allFields;
        if(!is_array($fields)) $fields = array($fields);

        foreach($fields as $field) {
            $props['key'] = $field;
            $thisValue = $this->getSetFunction($props);

            if($thisValue === null && $field == 'group_id') $keyValues[$field] = null;
            if(isset($thisValue)) {
                if($thisValue instanceof \DateTime) $thisValue = $thisValue->format(DefaultDates::DATE_FORMAT);
                $keyValues[$field] = $thisValue;
            }
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