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

/**
 * Class GetViewHelperTest
 */
class GetViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @test
     */
    public function throwsExceptionWithoutRegisterStack(): void
    {
        $this->expectException(\RuntimeException::class);

        $this->executeViewHelper(['name' => 'name']);
    }

    /**
     * @test
     */
    public function returnsNullIfRegisterDoesNotExist(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest())->withAttribute(
            'frontend.register.stack',
            new RegisterStack()
        );
        $name = uniqid();
        $this->assertEquals(null, $this->executeViewHelper(['name' => $name]));
    }

    /**
     * @test
     */
    public function returnsValueIfRegisterExists(): void
    {
        $registerStack = new RegisterStack();
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest())->withAttribute('frontend.register.stack', $registerStack);
        $name = uniqid();
        $value = uniqid();
        $registerStack->current()->set($name, $value);
        $this->assertEquals($value, $this->executeViewHelper(['name' => $name]));
    }

    /**
     * @test
     */
    public function readsRegisterStackFromRenderingContextRequest(): void
    {
        $name = uniqid();
        $globalRegisterStack = new RegisterStack();
        $globalRegisterStack->current()->set($name, 'outer');
        $subRequestRegisterStack = new RegisterStack();
        $subRequestRegisterStack->current()->set($name, 'inner');

        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest())->withAttribute(
            'frontend.register.stack',
            $globalRegisterStack
        );
        $this->renderingContext = $this->createRenderingContextWithRequest(
            (new ServerRequest())->withAttribute('frontend.register.stack', $subRequestRegisterStack)
        );

        self::assertSame('inner', $this->executeViewHelper(['name' => $name]));
    }
}
