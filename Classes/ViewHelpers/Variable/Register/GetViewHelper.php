<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Variable\Register;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\CompileWithContentArgumentAndRenderStatic;
use FluidTYPO3\Vhs\Core\ViewHelper\AbstractViewHelper;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Frontend\ContentObject\RegisterStack;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * ### Variable\Register: Get
 *
 * ViewHelper used to read the value of a TSFE-register
 * Can be used to read names of variables which contain dynamic parts:
 *
 * ```
 * <!-- if {variableName} is "Name", outputs value of {dynamicName} -->
 * {v:variable.register.get(name: 'dynamic{variableName}')}
 * ```
 */
class GetViewHelper extends AbstractViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;

    /**
     * @var boolean
     */
    protected $escapeChildren = false;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('name', 'string', 'Name of register');
    }

    /**
     * @return mixed
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $name = (string) $renderChildrenClosure();
        return self::getRegisterStack()->current()->get($name);
    }

    private static function getRegisterStack(): RegisterStack
    {
        $request = $GLOBALS['TYPO3_REQUEST'] ?? null;
        if (!$request instanceof ServerRequestInterface) {
            throw new \RuntimeException('Unable to read frontend register without request.', 1774448256);
        }

        $registerStack = $request->getAttribute('frontend.register.stack');
        if (!$registerStack instanceof RegisterStack) {
            throw new \RuntimeException('Unable to read frontend register without register stack.', 1774448257);
        }

        return $registerStack;
    }
}
