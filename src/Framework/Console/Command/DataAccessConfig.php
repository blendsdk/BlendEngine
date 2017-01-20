<?php

namespace Blend\Framework\Console\Command;

/**
 * class to configure the data access builder
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class DataAccessConfig
{
    protected $output_folder;

    public function setOutputFolder($folder)
    {
        $this->output_folder = $folder;
        return $this;
    }
}
