<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Service\UtilSanitizeXss\Dependency\External;

use Generated\Shared\Transfer\HtmlSanitizerConfigTransfer;
use Spryker\Service\UtilSanitizeXss\Parser\NativeHtmlParser;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

class UtilSanitizeToSymfonyHtmlSanitizerAdapter implements UtilSanitizeToHtmlSanitizerInterface
{
    public function sanitize(string $text, HtmlSanitizerConfigTransfer $htmlSanitizerConfigTransfer): string
    {
        $config = $this->buildHtmlSanitizerConfig($htmlSanitizerConfigTransfer);
        $parser = \PHP_VERSION_ID >= 80400 ? new NativeHtmlParser() : null;

        return (new HtmlSanitizer($config, $parser))->sanitize($text);
    }

    protected function buildHtmlSanitizerConfig(HtmlSanitizerConfigTransfer $htmlSanitizerConfigTransfer): HtmlSanitizerConfig
    {
        $config = new HtmlSanitizerConfig();

        if ($htmlSanitizerConfigTransfer->getIsAllowSafeElements()) {
            $config = $config->allowSafeElements();
        }

        if ($htmlSanitizerConfigTransfer->getIsAllowStaticElements()) {
            $config = $config->allowStaticElements();
        }

        foreach ($htmlSanitizerConfigTransfer->getAllowedElements() as $allowedElement) {
            $allowedAttributes = $allowedElement->getAllowedAttributes();
            $attributes = $allowedAttributes === ['*'] ? '*' : array_values($allowedAttributes);
            $config = $config->allowElement($allowedElement->getElementOrFail(), $attributes);
        }

        foreach ($htmlSanitizerConfigTransfer->getAllowedAttributes() as $allowedAttribute) {
            $allowedElements = $allowedAttribute->getAllowedElements();
            $elements = $allowedElements === ['*'] ? '*' : array_values($allowedElements);
            $config = $config->allowAttribute($allowedAttribute->getAttributeOrFail(), $elements);
        }

        if ($htmlSanitizerConfigTransfer->getAllowedLinkSchemes()) {
            /** @var list<string> $schemes */
            $schemes = array_values($htmlSanitizerConfigTransfer->getAllowedLinkSchemes());
            $config = $config->allowLinkSchemes($schemes);
        }

        if ($htmlSanitizerConfigTransfer->getAllowedLinkHosts()) {
            /** @var list<string> $hosts */
            $hosts = array_values($htmlSanitizerConfigTransfer->getAllowedLinkHosts());
            $config = $config->allowLinkHosts($hosts);
        }

        if ($htmlSanitizerConfigTransfer->getIsAllowRelativeLinks()) {
            $config = $config->allowRelativeLinks();
        }

        if ($htmlSanitizerConfigTransfer->getAllowedMediaSchemes()) {
            /** @var list<string> $mediaSchemes */
            $mediaSchemes = array_values($htmlSanitizerConfigTransfer->getAllowedMediaSchemes());
            $config = $config->allowMediaSchemes($mediaSchemes);
        }

        if ($htmlSanitizerConfigTransfer->getAllowedMediaHosts()) {
            /** @var list<string> $mediaHosts */
            $mediaHosts = array_values($htmlSanitizerConfigTransfer->getAllowedMediaHosts());
            $config = $config->allowMediaHosts($mediaHosts);
        }

        if ($htmlSanitizerConfigTransfer->getIsAllowRelativeMedias()) {
            $config = $config->allowRelativeMedias();
        }

        if ($htmlSanitizerConfigTransfer->getIsForceHttpsUrls()) {
            $config = $config->forceHttpsUrls();
        }

        if ($htmlSanitizerConfigTransfer->getMaxInputLength() !== null) {
            $config = $config->withMaxInputLength($htmlSanitizerConfigTransfer->getMaxInputLength());
        }

        return $config;
    }
}
