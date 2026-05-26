<?php
namespace FluidTYPO3\Vhs\Traits;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

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
        parent::registerArgument($name, $type, $description, $required, $defaultValue, $escape);
    }
}
