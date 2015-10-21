<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Core;

use Blend\Core\Application;

/**
 * Common Exception type
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class BlendEngineException extends \Exception {

    public function __construct($message, Application $application, $context = array(), $code = null, $previous = null) {
        parent::__construct($message, $code, $previous);
        $logger = $application->getLogger();
        if ($logger) {
            $logger->error($message);
            if ($application->isDevelopment()) {
                $logger->debug($message, $context);
            }
        }
    }

    public static function newInstance(Application $application, $message, $context = array(), $code = null, $previous = null) {
        return new BlendEngineException($message, $application, $context, $code, $previous);
    }

}
