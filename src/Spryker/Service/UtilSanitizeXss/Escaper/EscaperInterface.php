<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Service\UtilSanitizeXss\Escaper;

interface EscaperInterface
{
    public function escape(string $text): string;

    public function restore(string $text): string;
}
