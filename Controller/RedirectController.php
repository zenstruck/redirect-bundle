<?php

/*
 * This file is part of the ZenstruckRedirectBundle package.
 *
 * (c) Kevin Bond <http://zenstruck.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\Bundle\RedirectBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class RedirectController
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function doRedirectAction(Request $request)
    {
        $source = $request->get('url');

        if (!$source) {
            throw new NotFoundHttpException();
        }

        $baseUrl = $this->container->get('request')->getBaseUrl();

        $response = $this->container->get('zenstruck_redirect.manager')->getResponse($source, $baseUrl, false);

        if ($response instanceof RedirectResponse) {
            return $response;
        }

        throw new NotFoundHttpException();
    }
}
