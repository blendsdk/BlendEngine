<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Component\Database\Factory;

use Blend\Component\Database\SQL\SQLString;

/**
 * Description of Schema.
 *
 * @abstract
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class Schema
{
    protected $rel_alias;

    /**
     * @var SQLString;
     */
    protected $rel_name;

    public function __construct($rel_name, $rel_alias)
    {
        $this->rel_name = $rel_name;
        $this->rel_alias = $rel_alias;
    }

    /**
     * @return SQLString
     */
    public function RELATION()
    {
        return sqlstr($this->rel_name);
    }

    /**
     * @param type $name
     *
     * @return SQLString
     */
    protected function column($name)
    {
        return sqlstr($name)->dotPrefix($this->rel_alias);
    }
}
