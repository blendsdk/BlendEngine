<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Framework\Locale;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Blend\Component\HttpKernel\KernelEvents;
use Blend\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Request;
use Blend\Component\Configuration\Configuration;
use Blend\Component\Exception\InvalidConfigException;
use Blend\Component\Routing\RouteAttribute;

/**
 * LocaleService provides automatic locale recognition to be used
 * by a translation service or other locale aware service in the
 * application. The current active locale can be read from the
 * Container RouteAttribute::LOCALE key
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class LocaleService implements EventSubscriberInterface {

    /**
     * @var Configuration
     */
    protected $config;

    /**
     * @var string
     */
    protected $defaultLocale;

    /**
     * @var array
     */
    protected $availableLocales;

    /**
     * @var string
     */
    protected $localeParamName;

    /**
     * @var Request
     */
    protected $request;

    public function __construct(Configuration $config) {
        $this->availbleLocales = [];
        $this->config = $config;
        $this->assertLocaleConfig();
    }

    public function onRequest(GetResponseEvent $event) {
        $request = $event->getRequest();

        /*
         * Check URL _locale
         *  default to -> Session _locale
         *  default to -> browser locale
         *  default to -> translation.defaultLocale
         */
        $locales = [];
        foreach (array(RouteAttribute::LOCALE, $this->localeParamName) as $item) {
            $locales[] = $request->attributes->get($item, null);
            $locales[] = $request->getSession()->get($item, null);
            $locales[] = $request->query->get($item, null);
        }

        $locales = array_unique($locales);

        $locale = $this->defaultLocale;

        foreach ($locales as $item) {
            if (in_array($item, $this->availableLocales)) {
                $locale = $item;
                break;
            }
        }

        if ($this->config->get('tranalstion.persistInSession', false) === true) {
            $request->getSession()->set(RouteAttribute::LOCALE, $locale);
        }
        $request->attributes->set(RouteAttribute::LOCALE, $locale);
        $request->setLocale($locale);
        $event->getContainer()->setScalar(RouteAttribute::LOCALE, $locale);
    }

    private function assertLocaleConfig() {
        $this->localeParamName = $this->config->get('translation.localeParameterName', "_lang");
        $this->defaultLocale = $this->config->get('translation.defaultLocale', null);
        $this->availableLocales = $this->config->get('translation.availableLocales', []);
        if (empty($this->availableLocales)) {
            throw new InvalidConfigException(
            "Invalid or missing translation.availableLocales configuration!");
        }
        if (empty($this->defaultLocale)) {
            throw new InvalidConfigException(
            "Invalid or missing translation.defaultLocale configuration!");
        }
    }

    public static function getSubscribedEvents() {
        return [
            KernelEvents::REQUEST => ['onRequest'
                , KernelEvents::PRIORITY_HIGHT + 1000]
        ];
    }

}
