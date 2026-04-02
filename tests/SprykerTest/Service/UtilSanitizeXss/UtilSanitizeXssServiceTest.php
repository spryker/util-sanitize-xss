<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Service\UtilSanitizeXss;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\HtmlSanitizerAllowedAttributeTransfer;
use Generated\Shared\Transfer\HtmlSanitizerAllowedElementTransfer;
use Generated\Shared\Transfer\HtmlSanitizerConfigTransfer;
use Spryker\Service\UtilSanitizeXss\UtilSanitizeXssService;
use Spryker\Service\UtilSanitizeXss\UtilSanitizeXssServiceInterface;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Service
 * @group UtilSanitizeXss
 * @group UtilSanitizeXssServiceTest
 * Add your own group annotations below this line
 */
class UtilSanitizeXssServiceTest extends Unit
{
    /**
     * @var string
     */
    protected const HTML_TAG_IFRAME = 'iframe';

    /**
     * @var string
     */
    protected const ATTRIBUTE_STYLE = 'style';

    public function testSanitizeXssWillSanitizeScriptTag(): void
    {
        //Arrange
        $harmString = '<script>alert("Hack");</script>';

        //Act
        $result = $this->getUtilSanitizeXssService()->sanitizeXss($harmString);

        //Assert
        $this->assertSame('', $result);
    }

    public function testSanitizeXssWillSanitizeScriptTagInsideAnotherTag(): void
    {
        //Arrange
        $harmString = '<span style="font-size: 36px;"><b>&lt;script&gt;alert("Hack");&lt;/script&gt;</b></span>';

        //Act
        $result = $this->getUtilSanitizeXssService()->sanitizeXss($harmString);

        //Assert
        $this->assertSame('<span ><b>alert&#40;"Hack"&#41;;</b></span>', $result);
    }

    public function testSanitizeXssWillSanitizeHexEncodedScriptTag(): void
    {
        //Arrange
        $harmString = '<img SRC=&#x6A&#x61&#x76&#x61&#x73&#x63&#x72&#x69&#x70&#x74&#x3A&#x61&#x6C&#x65&#x72&#x74&#x28&#x27&#x58&#x53&#x53&#x27&#x29 />';

        //Act
        $result = $this->getUtilSanitizeXssService()->sanitizeXss($harmString);

        //Assert
        $this->assertSame('<img  />', $result);
    }

    public function testSanitizeXssWillSanitizeAttributeWithoutRemovingIt(): void
    {
        //Arrange
        $harmString = '<iframe width="560" onclick="alert(\'xss\')" height="315" src="http://some-site"></iframe>';

        //Act
        $result = $this->getUtilSanitizeXssService()->sanitizeXss($harmString, [], [static::HTML_TAG_IFRAME]);

        //Assert
        $this->assertSame('<iframe width="560"  height="315" src="http://some-site"></iframe>', $result);
    }

    public function testSanitizeXssWillSanitizeHtmlTagWithoutRemovingIt(): void
    {
        //Arrange
        $harmString = '<div style="list-style-image: url(javascript:alert(0)); margin=0"></div>';

        //Act
        $result = $this->getUtilSanitizeXssService()->sanitizeXss($harmString, [static::ATTRIBUTE_STYLE]);

        //Assert
        $this->assertThat(
            $result,
            $this->logicalOr(
                '<div style="list-style-image: url(alert&#40;0&#41;); margin=0"></div>',
                '<div style="list-style-image: url((0)); margin=0"></div>',
            ),
        );
    }

    /**
     * @dataProvider getSanitizeXssTwigFunctionsDataProvider
     *
     * @param string $text
     * @param string $expectedResult
     *
     * @return void
     */
    public function testSanitizeXssTwigFunctions(string $text, string $expectedResult): void
    {
        // Act
        $sanitizedText = $this->getUtilSanitizeXssService()->sanitizeXss($text);

        // Assert
        $this->assertSame($expectedResult, $sanitizedText);
    }

    /**
     * @return array<string, list<string>>
     */
    protected function getSanitizeXssTwigFunctionsDataProvider(): array
    {
        return [
            'Should not remove valid twig functions.' => [
                '{{ twig_file() }} {{twig_file([1, 2, 3])}} {{ twigFile("arguments") }}',
                '{{ twig_file() }} {{twig_file([1, 2, 3])}} {{ twigFile("arguments") }}',
            ],
            'Should sanitize invalid twig functions.' => [
                '{{ <script>alert("Hack");</script> }}{{<span style="font-size: 36px;"><b>&lt;script&gt;alert("Hack");&lt;/script&gt;</b></span>}}{{ <img SRC=&#x6A&#x61&#x76&#x61&#x73&#x63&#x72&#x69&#x70&#x74&#x3A&#x61&#x6C&#x65&#x72&#x74&#x28&#x27&#x58&#x53&#x53&#x27&#x29 /> }}{{twig_file([1, 2, 3])}}',
                '{{  }}{{<span ><b>alert&#40;"Hack"&#41;;</b></span>}}{{ <img  /> }}{{twig_file([1, 2, 3])}}',
            ],
            'Should sanitize twig function arguments.' => [
                '{{twig-function(<script>alert("Hack");</script>)}}{{ twig_function(<span style="font-size: 36px;"><b>&lt;script&gt;alert("Hack");&lt;/script&gt;</b></span>) }}{{ twig_file(<script>alert("Hack");</script>) }} {{ twigFile(<img SRC=&#x6A&#x61&#x76&#x61&#x73&#x63&#x72&#x69&#x70&#x74&#x3A&#x61&#x6C&#x65&#x72&#x74&#x28&#x27&#x58&#x53&#x53&#x27&#x29 />) }}',
                '{{twig-function()}}{{ twig_function(<span ><b>alert&#40;"Hack"&#41;;</b></span>) }}{{ twig_file&#40;&#41; }} {{ twigFile(<img  />) }}',
            ],
            'Should not remove valid functions in comments.' => [
                '<!-- function() --> <!-- {{twig_file([1, 2, 3])}} --> <!-- <p>comment</p> -->',
                '<!-- function() --> <!-- {{twig_file([1, 2, 3])}} --> <!-- <p>comment</p> -->',
            ],
            'Should sanitize invalid comments.' => [
                '<!-- <script>alert("Hack");</script> --> <!-- <span style="font-size: 36px;"><b>&lt;script&gt;alert("Hack");&lt;/script&gt;</b></span> --> <!-- <img SRC=&#x6A&#x61&#x76&#x61&#x73&#x63&#x72&#x69&#x70&#x74&#x3A&#x61&#x6C&#x65&#x72&#x74&#x28&#x27&#x58&#x53&#x53&#x27&#x29 /> -->',
                '<!--  --> <!-- <span ><b>alert&#40;"Hack"&#41;;</b></span> --> <!-- <img  /> -->',
            ],
        ];
    }

    public function testSanitizeStripsAllHtmlWithEmptyConfig(): void
    {
        // Arrange — text nodes directly in the body survive; elements are dropped WITH children (Drop action)
        $config = new HtmlSanitizerConfigTransfer();

        // Act
        $result = $this->getUtilSanitizeXssService()->sanitize(
            'Hello <script>evil()</script> world',
            $config,
        );

        // Assert — text nodes survive, <script> and its content are dropped
        $this->assertSame('Hello  world', $result);
    }

    public function testSanitizeStripsScriptTagsWithEmptyConfig(): void
    {
        // Arrange
        $config = new HtmlSanitizerConfigTransfer();

        // Act & Assert
        $this->assertSame('', $this->getUtilSanitizeXssService()->sanitize(
            "<script>alert('ok');</script>",
            $config,
        ));
    }

    public function testSanitizeStripsNestedScriptTag(): void
    {
        // Arrange
        $config = new HtmlSanitizerConfigTransfer();

        // Act & Assert
        $this->assertSame('', $this->getUtilSanitizeXssService()->sanitize(
            '<scr<script>ipt>alert(1)</script>',
            $config,
        ));
    }

    public function testSanitizeAllowsConfiguredElementsWithNoAttributes(): void
    {
        // Arrange
        $config = (new HtmlSanitizerConfigTransfer())
            ->addAllowedElement(
                (new HtmlSanitizerAllowedElementTransfer())->setElement('strong')->setAllowedAttributes([]),
            )
            ->addAllowedElement(
                (new HtmlSanitizerAllowedElementTransfer())->setElement('em')->setAllowedAttributes([]),
            );

        // Act — input has no outer disallowed wrapper so allowed children are not dropped
        $result = $this->getUtilSanitizeXssService()->sanitize(
            '<strong class="bold">Bold</strong> and <em>italic</em>',
            $config,
        );

        // Assert — elements preserved, class attribute stripped (not in allowed list)
        $this->assertSame('<strong>Bold</strong> and <em>italic</em>', $result);
    }

    public function testSanitizeAllowsConfiguredElementsWithSpecificAttributes(): void
    {
        // Arrange
        $config = (new HtmlSanitizerConfigTransfer())
            ->addAllowedElement(
                (new HtmlSanitizerAllowedElementTransfer())->setElement('a')->setAllowedAttributes(['href']),
            );

        // Act
        $result = $this->getUtilSanitizeXssService()->sanitize(
            '<a href="https://example.com" class="btn" onclick="evil()">Link</a>',
            $config,
        );

        // Assert
        $this->assertSame('<a href="https://example.com">Link</a>', $result);
    }

    public function testSanitizeAllowsElementWithAllSafeAttributesViaWildcard(): void
    {
        // Arrange
        $config = (new HtmlSanitizerConfigTransfer())
            ->addAllowedElement(
                (new HtmlSanitizerAllowedElementTransfer())->setElement('a')->setAllowedAttributes(['*']),
            );

        // Act
        $result = $this->getUtilSanitizeXssService()->sanitize(
            '<a href="https://example.com" title="Visit">Link</a>',
            $config,
        );

        // Assert
        $this->assertStringContainsString('href="https://example.com"', $result);
        $this->assertStringContainsString('title="Visit"', $result);
    }

    public function testSanitizeStripsJavascriptProtocolEvenWhenHrefAllowed(): void
    {
        // Arrange
        $config = (new HtmlSanitizerConfigTransfer())
            ->addAllowedElement(
                (new HtmlSanitizerAllowedElementTransfer())->setElement('a')->setAllowedAttributes(['href']),
            );

        // Act
        $result = $this->getUtilSanitizeXssService()->sanitize(
            "<a href=\"javascript:alert('ok')\">Test</a>",
            $config,
        );

        // Assert
        $this->assertSame('<a>Test</a>', $result);
    }

    public function testSanitizeStripsEventHandlersEvenWithWildcardAttributes(): void
    {
        // Arrange
        $config = (new HtmlSanitizerConfigTransfer())
            ->addAllowedElement(
                (new HtmlSanitizerAllowedElementTransfer())->setElement('a')->setAllowedAttributes(['*']),
            );

        // Act
        $result = $this->getUtilSanitizeXssService()->sanitize(
            '<a href="https://example.com" onclick="evil()">Link</a>',
            $config,
        );

        // Assert
        $this->assertStringNotContainsString('onclick', $result);
        $this->assertStringContainsString('href', $result);
    }

    public function testSanitizeAllowsSafeElementsBaseline(): void
    {
        // Arrange
        $config = (new HtmlSanitizerConfigTransfer())->setIsAllowSafeElements(true);

        // Act
        $result = $this->getUtilSanitizeXssService()->sanitize(
            '<p>Hello <script>evil()</script> <strong>world</strong></p>',
            $config,
        );

        // Assert
        $this->assertStringNotContainsString('<script>', $result);
        $this->assertStringContainsString('<p>', $result);
        $this->assertStringContainsString('<strong>world</strong>', $result);
    }

    public function testSanitizeForcesHttpsUrls(): void
    {
        // Arrange
        $config = (new HtmlSanitizerConfigTransfer())
            ->setIsForceHttpsUrls(true)
            ->addAllowedElement(
                (new HtmlSanitizerAllowedElementTransfer())->setElement('a')->setAllowedAttributes(['href']),
            );

        // Act
        $result = $this->getUtilSanitizeXssService()->sanitize(
            '<a href="http://example.com">Link</a>',
            $config,
        );

        // Assert
        $this->assertSame('<a href="https://example.com">Link</a>', $result);
    }

    public function testSanitizeAllowsAttributeGloballyViaAllowAttributes(): void
    {
        // Arrange
        $config = (new HtmlSanitizerConfigTransfer())
            ->addAllowedElement(
                (new HtmlSanitizerAllowedElementTransfer())->setElement('p')->setAllowedAttributes([]),
            )
            ->addAllowedElement(
                (new HtmlSanitizerAllowedElementTransfer())->setElement('a')->setAllowedAttributes([]),
            )
            ->addAllowedAttribute(
                (new HtmlSanitizerAllowedAttributeTransfer())->setAttribute('class')->setAllowedElements(['*']),
            );

        // Act
        $result = $this->getUtilSanitizeXssService()->sanitize(
            '<p class="lead"><a class="btn">Link</a></p>',
            $config,
        );

        // Assert
        $this->assertSame('<p class="lead"><a class="btn">Link</a></p>', $result);
    }

    public function testSanitizeStripsStyleTag(): void
    {
        // Arrange
        $config = new HtmlSanitizerConfigTransfer();

        // Act — text node is directly in body (not wrapped in a dropped element)
        $result = $this->getUtilSanitizeXssService()->sanitize(
            'Text<style>body { background: red; }</style>',
            $config,
        );

        // Assert — plain text survives, <style> element and its content are dropped
        $this->assertSame('Text', $result);
    }

    public function testSanitizeStripsHtmlCommentsWithInjection(): void
    {
        // Arrange
        $config = new HtmlSanitizerConfigTransfer();

        // Act
        $result = $this->getUtilSanitizeXssService()->sanitize(
            'Lorem ipsum<!--if[true]> <script>alert(1337)</script> -->',
            $config,
        );

        // Assert
        $this->assertSame('Lorem ipsum', $result);
    }

    protected function getUtilSanitizeXssService(): UtilSanitizeXssServiceInterface
    {
        return new UtilSanitizeXssService();
    }
}
