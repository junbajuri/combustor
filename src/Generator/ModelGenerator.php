<?php

namespace Rougin\Combustor\Generator;

use Rougin\Describe\Describe;
use Rougin\Combustor\Common\Tools;
use Rougin\Combustor\Common\Inflector;

/**
 * Model Generator
 *
 * Generates CodeIgniter-based models.
 * 
 * @package Combustor
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class ModelGenerator implements GeneratorInterface
{
    /**
     * @var \Rougin\Describe\Describe
     */
    protected $describe;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @param \Rougin\Describe\Describe $describe
     * @param array $data
     */
    public function __construct(Describe $describe, array $data)
    {
        $this->describe = $describe;
        $this->data = $data;
    }

    /**
     * Prepares the data before generation.
     * 
     * @param  array &$data
     * @return void
     */
    public function prepareData(array &$data)
    {
        $data['camel'] = [];
        $data['columns'] = [];
        $data['indexes'] = [];
        $data['primaryKeys'] = [];
        $data['underscore'] = [];
        $data['columns'] = $this->describe->getTable($data['name']);
        $data['primaryKey'] = $this->describe->getPrimaryKey($data['name']);
    }

    /**
     * Generates set of code based on data.
     * 
     * @return array
     */
    public function generate()
    {
        $this->prepareData($this->data);

        foreach ($this->data['columns'] as $column) {
            $field = strtolower($column->getField());
            $accessor = 'get_'.$field;
            $mutator = 'set_'.$field;

            $this->data['camel'][$field] = [
                'field' => lcfirst(Inflector::camelize($field)),
                'accessor' => lcfirst(Inflector::camelize($accessor)),
                'mutator' => lcfirst(Inflector::camelize($mutator))
            ];

            $this->data['underscore'][$field] = [
                'field' => lcfirst(Inflector::underscore($field)),
                'accessor' => lcfirst(Inflector::underscore($accessor)),
                'mutator' => lcfirst(Inflector::underscore($mutator))
            ];

            if ($column->isForeignKey()) {
                $field = $column->getField();

                array_push($this->data['indexes'], $field);

                $this->data['primaryKeys'][$field] = 'get_'. 
                    $this->describe->getPrimaryKey(
                        $column->getReferencedTable()
                    );

                if ($this->data['isCamel']) {
                    $this->data['primaryKeys'][$field] = Inflector::camelize(
                        $this->data['primaryKeys'][$field]
                    );
                }
            }

            $column->setReferencedTable(
                Tools::stripTableSchema($column->getReferencedTable())
            );
        }

        return $this->data;
    }
}
