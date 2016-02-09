<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Tests\DataModelBuilder\Command;

use Blend\DataModelBuilder\Command\ModelBuilderDefaultConfig;

/**
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class CustomizedModelConfig extends ModelBuilderDefaultConfig {

    public function getCustomizedRelationList() {
        return ['sys_order'];
    }

}
