<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Framework\Console\Command\Orm;

use Blend\Component\Database\Schema\Record;

/**
 * Represents a Method to be used in a template.
 */
class Method extends Record
{
    public function __construct(array $data = array())
    {
        parent::__construct($data);
        $this->setValue('parameters', array());
    }

    /**
     * Sets the method description.
     *
     * @param string $value
     */
    public function setDescription($value)
    {
        $this->setValue('method_description', $value);
    }

    /**
     * Sets the method name.
     *
     * @param string $value
     */
    public function setName($value)
    {
        $this->setValue('method_name', $value);
    }

    /**
     * Generates an array to be used as call argument as PHP sources.
     *
     * @return string
     */
    public function getCallArgumentArray()
    {
        $result = array();
        $params = $this->getValue('parameters');
        foreach ($params as $param_name => $param_type) {
            $result[] = "'$param_name' => \$$param_name";
        }
        $code = 'array(' . implode(',', $result) . ')';
        $this->setValue('call_params_array', $code);

        return $code;
    }

    /**
     * Sets the method content.
     *
     * @param string $value
     */
    public function setContent($value)
    {
        $this->setValue('method_content', $value);
    }

    /**
     * Adds a parameters for this Method.
     *
     * @param string       $name
     * @param string/array $type
     */
    public function addParameter($name, $type)
    {
        $call = array();
        $params = $this->getValue('parameters');
        $params[$name] = str_replace(' ', '_', $type);
        $this->setValue('parameters', $params);
        foreach ($params as $name => $type) {
            if (is_array($type)) {
                $call[] = '$' . "$name = $type[1]";
            } else {
                $call[] = '$' . $name;
            }
        }
        $this->setValue('method_call_params', implode(',', $call));
    }

    /**
     * Get the call structure arguments of this Method.
     *
     * @return type
     */
    public function getCallSignature()
    {
        return $this->getValue('method_call_params');
    }
}
