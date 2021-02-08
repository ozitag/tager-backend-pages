<?php

namespace OZiTAG\Tager\Backend\Pages\Structures;

use OZiTAG\Tager\Backend\Fields\Base\Field;
use OZiTAG\Tager\Backend\Fields\Fields\GroupField;
use OZiTAG\Tager\Backend\Fields\Utils\ConfigLoader;

class Template
{
    private ?string $id = null;

    private string $label;

    /** @var Field[] */
    private array $fields;

    public function __construct(string $label, array $fields = [])
    {
        $this->label = (string)$label;

        $this->fields = ConfigLoader::loadFieldsFromConfig($fields);
    }

    public function setId(string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return Field[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    public function getJson(): array
    {
        return [
            'id' => $this->id,
            'label' => $this->getLabel()
        ];
    }

    public function getFullJson(): array
    {
        $result = [
            'id' => $this->getId(),
            'label' => $this->getLabel(),
            'fields' => []
        ];

        foreach ($this->getFields() as $param => $field) {
            $result['fields'][] = $field->getJson();
        }

        return $result;
    }

    /**
     * @param $name
     * @return Field|null
     */
    public function getField($name): ?Field
    {
        if (isset($this->fields[$name])) {
            return $this->fields[$name];
        }

        if (substr($name, 0, 6) == 'group_') {
            $groupIndex = intval(substr($name, 6));
            if ($groupIndex && isset($this->fields[$groupIndex - 1]) && $this->fields[$groupIndex - 1] instanceof GroupField) {
                return $this->fields[$groupIndex - 1];
            }
        }

        foreach ($this->fields as $field) {
            if ($field instanceof GroupField) {
                $groupFields = $field->getFields();
                if (isset($groupFields[$name])) {
                    return $groupFields[$name];
                }
            }
        }

        return null;
    }
}
