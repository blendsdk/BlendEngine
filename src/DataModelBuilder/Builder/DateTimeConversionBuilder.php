<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\DataModelBuilder\Builder;

use Blend\DataModelBuilder\Builder\Config\BuilderConfig;

/**
 * DateTimeConversionBuilder creates the DateTimeConversion.php.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class DateTimeConversionBuilder
{
    /**
     * @var BuilderConfig
     */
    protected $config;

    /**
     * @param BuilderConfig $config
     */
    public function __construct(BuilderConfig $config)
    {
        $this->config = $config;
    }

    /**
     * Builds the settings file.
     */
    public function build()
    {
        /**
         * To get a clean PHP source code from the getLocalDateTimeFormat()
         * We use the var_export(...) function.
         */
        $header = '<?php ';
        $settings = var_export(array(
            'datetimeFormat' => $this->config->getLocalDateTimeFormat(),
                ), true);
        $tmpFile = TEMP_DIR.'/'.uniqid().'.php';
        file_put_contents($tmpFile, $header.$settings);
        $settings = php_strip_whitespace($tmpFile);
        unlink($tmpFile);
        render_php_template(dirname(__FILE__).'/Template/datetime.php', array(
            'settings' => trim(str_replace($header, '', $settings)),
            'namespace' => $this->config->getApplicationNamespace().'\\'.$this->config->getModelRootNamespace(),
                ), $this->config->getTargetRootFolder().'/Database/DateTimeConversion.php', false);
    }
}
