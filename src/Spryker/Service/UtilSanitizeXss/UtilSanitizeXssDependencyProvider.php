<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Service\UtilSanitizeXss;

use Spryker\Service\Kernel\AbstractBundleDependencyProvider;
use Spryker\Service\Kernel\Container;
use Spryker\Service\UtilSanitizeXss\Dependency\External\UtilSanitizeToAntiXssAdapter;

class UtilSanitizeXssDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const XSS_SANITIZER = 'XSS_SANITIZER';

    public function provideServiceDependencies(Container $container): Container
    {
        $container = parent::provideServiceDependencies($container);
        $container = $this->addXssSanitizer($container);

        return $container;
    }

    public function addXssSanitizer(Container $container): Container
    {
        $container->set(static::XSS_SANITIZER, function () {
            return new UtilSanitizeToAntiXssAdapter();
        });

        return $container;
    }
}
