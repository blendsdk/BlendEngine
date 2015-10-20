<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Translation;

use Blend\Core\Application;
use Symfony\Component\Translation\Translator as BaseTranslator;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Loader\XliffFileLoader;

/**
 * Translator that gets the current locale from the BlendEngine application.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Translator extends BaseTranslator {

    protected $application;
    protected $missingTransFile;

    public function trans($id, array $parameters = array(), $domain = null, $locale = null) {
        $value = parent::trans($id, $parameters, $domain, $locale);
        if (!is_null($this->missingTransFile) && $id === $value) {
            file_put_contents($this->missingTransFile, "$id\n", FILE_APPEND);
        }

        return $value;
    }

    public function __construct(Application $application) {
        $this->application = $application;
        $cacheDir = $this->application->isProduction() ? $this->application->getRootFolder('/var/cache') : null;
        parent::__construct(null, new MessageSelector(), $cacheDir);
        $this->addLoader('array', new ArrayLoader());
        $this->addLoader('xliff', new XliffFileLoader());
        if ($this->application->isDevelopment()) {
            $this->missingTransFile = $this->application->getRootFolder('/var/missing_translations.txt');
        } else {
            $this->missingTransFile = null;
        }
    }

    public function getLocale() {
        return $this->application->getLocale();
    }

    public function setLocale($locale) {
        if (null === $locale) {
            return;
        }
        $this->application->setLocale($locale);
        parent::setLocale($locale);
    }

    /**
     * Load the translation resources for the messages folder
     * @param string $path
     */
    public function loadTranslations($path) {
        $searchPath = array_values(array_diff(array(
            realpath("{$path}/messages"),
            realpath("{$path}/../messages"),
            realpath("{$path}/Messages"),
            realpath("{$path}/../Messages"),
                        ), array(false)));
        if (count($searchPath) !== 0) {
            $resources = array_diff(glob("{$searchPath[0]}/{$this->application->getLocale()}/*"), array(false));
            foreach ($resources as $file) {
                $this->loadTranslation($file);
            }
        }
    }

    /**
     * Loads a translation file
     * @param string $file
     */
    private function loadTranslation($file) {
        $fileInfo = pathinfo($file);
        if ($fileInfo['extension'] === 'php') {
            $format = 'array';
            $data = include($file);
        } else {
            $format = $fileInfo['extension'];
            $data = file_get_contents($file);
        }
        $this->application->getTranslator()->addResource($format, $data, $this->application->getLocale(), $fileInfo['filename']);
    }

}
