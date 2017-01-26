<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Framework\Security\Provider;

use Symfony\Component\HttpFoundation\Request;

/**
 * Base class for a SecurityProvider.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class SecurityProvider implements SecurityProviderInterface
{
    const REFERER_URL = '_http_referer';

    /**
     * @var Request
     */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
}
