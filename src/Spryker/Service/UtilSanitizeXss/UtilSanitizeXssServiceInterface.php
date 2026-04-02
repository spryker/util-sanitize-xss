<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Service\UtilSanitizeXss;

use Generated\Shared\Transfer\HtmlSanitizerConfigTransfer;

interface UtilSanitizeXssServiceInterface
{
    /**
     * Specification:
     *  - Sanitizes untrusted HTML to prevent XSS attacks.
     *
     * @api
     *
     * @param string $text
     * @param list<string> $allowedAttributes
     * @param list<string> $allowedHtmlTags
     *
     * @return string
     */
    public function sanitizeXss(string $text, array $allowedAttributes = [], array $allowedHtmlTags = []): string;

    /**
     * Specification:
     * - Sanitizes untrusted HTML using the Symfony HtmlSanitizer with the full configuration.
     * - Applies element allow-lists, attribute allow-lists, link/media scheme restrictions,
     *   baselines (safe/static), and URL rewriting based on `HtmlSanitizerConfigTransfer`.
     *
     * @api
     */
    public function sanitize(string $text, HtmlSanitizerConfigTransfer $htmlSanitizerConfigTransfer): string;
}
