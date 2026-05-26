<?php
namespace FluidTYPO3\Vhs\Traits;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\VersionNumberUtility;

trait ArgumentOverride
{
    protected function overrideArgument(
        string $name,
        string $type,
        string $description,
        bool $required = false,
        mixed $defaultValue = null,
        ?bool $escape = null
    ): void {
        if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '13.4', '>=')) {
            parent::registerArgument($name, $type, $description, $required, $defaultValue, $escape);
            return;
        }

        /** @noinspection PhpMethodParametersCountMismatchInspection */
        parent::overrideArgument($name, $type, $description, $required, $defaultValue, $escape);
    }
}
