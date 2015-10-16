<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Console\StatementConfig;

namespace Blend\Console\StatementConfig;

use Blend\Console\StatementConfig\StatementConfig;

/**
 * Description of SelectStatementConfig
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class SelectStatementConfig extends StatementConfig {

    protected $sql;

    public function setSQL($sql) {
        $this->sql = $sql;
    }

    public function getSQL() {
        return $this->sql;
    }

    public function __construct($name, $namespace, $outfolder, $description = null, $overwrite = true) {
        parent::__construct($name, $namespace, $outfolder, $description, $overwrite);
        $this->addUse('Blend\Database\SelectStatement');
        $this->sql = "";
    }

}
