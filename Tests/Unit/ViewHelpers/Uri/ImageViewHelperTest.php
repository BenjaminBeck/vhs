<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Uri;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use FluidTYPO3\Vhs\Tests\Fixtures\Classes\DummyTypoScriptFrontendController;

/**
 * Class ImageViewHelperTest
 */
class ImageViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @test
     */
    public function callsExpectedMethodSequence()
    {
        $GLOBALS['TSFE'] = $this->getMockBuilder(DummyTypoScriptFrontendController::class)->disableOriginalConstructor()->getMock();
        $GLOBALS['TSFE']->absRefPrefix = '';
        $mock = $this->getMockBuilder($this->getViewHelperClassName())->setMethods(['preprocessImage'])->getMock();
        $arguments = $this->buildViewHelperArguments($mock, ['src' => 'foobar']);
        $mock->setArguments($arguments);
        $output = $mock->render();
        $this->assertSame('', $output);
    }
}
