<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use TYPO3\CMS\Core\Http\ServerRequest;

/**
 * Class StaticPrefixViewHelperTest
 */
class StaticPrefixViewHelperTest extends AbstractViewHelperTestCase
{
    public function testRender(): void
    {
        $this->assertEmpty($this->executeViewHelper());
    }

    public function testRenderReturnsConfiguredPrefix(): void
    {
        $frontendTypoScript = new class () {
            public function hasSetup(): bool
            {
                return true;
            }

            public function getSetupArray(): array
            {
                return ['plugin.' => ['tx_vhs.' => ['settings.' => ['prependPath' => '/static/']]]];
            }
        };
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest())->withAttribute('frontend.typoscript', $frontendTypoScript);

        self::assertSame('/static/', $this->executeViewHelper());
    }
}
