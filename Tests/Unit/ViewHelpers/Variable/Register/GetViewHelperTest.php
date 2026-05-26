<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Variable\Register;

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
 * Class GetViewHelperTest
 */
class GetViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @test
     */
    public function silentlyIgnoresMissingFrontendController()
    {
        $result = $this->executeViewHelper(['name' => 'name']);
        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function returnsNullIfRegisterDoesNotExist()
    {
        $GLOBALS['TSFE'] = new DummyTypoScriptFrontendController();
        $name = uniqid();
        $this->assertEquals(null, $this->executeViewHelper(['name' => $name]));
    }

    /**
     * @test
     */
    public function returnsValueIfRegisterExists()
    {
        $GLOBALS['TSFE'] = new DummyTypoScriptFrontendController();
        $name = uniqid();
        $value = uniqid();
        $GLOBALS['TSFE']->register[$name] = $value;
        $this->assertEquals($value, $this->executeViewHelper(['name' => $name]));
    }
}
