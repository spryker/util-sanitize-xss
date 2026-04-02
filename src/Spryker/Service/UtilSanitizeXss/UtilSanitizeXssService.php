<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Service\UtilSanitizeXss;

use Generated\Shared\Transfer\HtmlSanitizerConfigTransfer;
use Spryker\Service\Kernel\AbstractService;

/**
 * @method \Spryker\Service\UtilSanitizeXss\UtilSanitizeXssServiceFactory getFactory()
 */
class UtilSanitizeXssService extends AbstractService implements UtilSanitizeXssServiceInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $text
     * @param list<string> $allowedAttributes
     * @param list<string> $allowedHtmlTags
     *
     * @return string
     */
    public function sanitizeXss(string $text, array $allowedAttributes = [], array $allowedHtmlTags = []): string
    {
        return $this->getFactory()
            ->createSanitizer()
            ->sanitize($text, $allowedAttributes, $allowedHtmlTags);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function sanitize(string $text, HtmlSanitizerConfigTransfer $htmlSanitizerConfigTransfer): string
    {
        return $this->getFactory()->getHtmlSanitizerAdapter()->sanitize($text, $htmlSanitizerConfigTransfer);
    }
}
