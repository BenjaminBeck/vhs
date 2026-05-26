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
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Frontend\ContentObject\RegisterStack;

class SetViewHelperTest extends AbstractViewHelperTestCase
{
    private RegisterStack $registerStack;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registerStack = new RegisterStack();
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest())->withAttribute(
            'frontend.register.stack',
            $this->registerStack
        );
    }

    /**
     * @test
     */
    public function throwsExceptionWithoutRegisterStack(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = new ServerRequest();
        $this->expectException(\RuntimeException::class);

        $this->executeViewHelper(['name' => 'name', 'value' => 'value']);
    }

    /**
     * @test
     */
    public function canSetRegister(): void
    {
        $name = uniqid();
        $value = uniqid();
        $this->executeViewHelper(['name' => $name, 'value' => $value]);
        $this->assertEquals($value, $this->registerStack->current()->get($name));
    }

    /**
     * @test
     */
    public function canSetVariableWithValueFromTagContent(): void
    {
        $name = uniqid();
        $value = uniqid();
        $this->executeViewHelperUsingTagContent($value, ['name' => $name]);
        $this->assertEquals($value, $this->registerStack->current()->get($name));
    }
}
