<?php

namespace OZiTAG\Tager\Backend\Pages\Structures;

use OZiTAG\Tager\Backend\Fields\Base\Field;
use OZiTAG\Tager\Backend\Fields\Utils\ConfigLoader;

class Template
{
    private $id;

    private $label;

    /** @var Field[] */
    private $fields;

    public function __construct($label, $fields = [])
    {
        $this->label = (string)$label;

        $this->fields = ConfigLoader::loadFieldsFromConfig($fields);
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return Field[]
     */
    public function getFields()
    {
        return $this->fields;
    }


    public function getJson()
    {
        return [
            'id' => $this->id,
            'label' => $this->getLabel()
        ];
    }

    public function getFullJson()
    {
        $result = [
            'id' => $this->getId(),
            'label' => $this->getLabel(),
            'fields' => []
        ];

        foreach ($this->getFields() as $param => $field) {
            $result['fields'][] = array_merge([
                'name' => $param,
            ], $field->getJson());
        }

        return $result;
    }

    /**
     * @param $name
     * @return Field|null
     */
    public function getField($name)
    {
        return isset($this->fields[$name]) ? $this->fields[$name] : null;
    }
}
