<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Service\UtilSanitizeXss\Parser;

use Dom\HTMLDocument;
use Dom\Node;
use Symfony\Component\HtmlSanitizer\Parser\ParserInterface;

/**
 * Wraps PHP 8.4's Dom\HTMLDocument parser with LIBXML_NOERROR to suppress parse
 * warnings for malformed HTML entities that would otherwise propagate as ErrorException
 * through Spryker's custom error handler.
 */
class NativeHtmlParser implements ParserInterface
{
    /**
     * @phpstan-ignore class.notFound
     */
    public function parse(string $html, string $context = 'body'): ?Node
    {
        // @phpstan-ignore class.notFound
        $document = HTMLDocument::createFromString(
            sprintf('<!DOCTYPE html><%s>%s</%1$s>', $context, $html),
            LIBXML_NOERROR,
        );

        $element = $document->getElementsByTagName($context)->item(0);

        return $element->hasChildNodes() ? $element : null;
    }
}
