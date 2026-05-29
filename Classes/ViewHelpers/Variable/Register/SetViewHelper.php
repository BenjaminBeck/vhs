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
use FluidTYPO3\Vhs\Utility\RequestResolver;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Frontend\ContentObject\RegisterStack;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * ### Variable\Register: Set
 *
 * Sets a single register in the TSFE-register.
 *
 * Using as `{value -> v:variable.register.set(name: 'myVar')}` makes $GLOBALS["TSFE"]->register['myVar']
 * contain `{value}`.
 */
class SetViewHelper extends AbstractViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;

    /**
     * @var boolean
     */
    protected $escapeChildren = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('value', 'mixed', 'Value to set');
        $this->registerArgument('name', 'string', 'Name of register', true);
    }

    /**
     * @return mixed
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $name = $arguments['name'];
        if (!is_string($name) || $name === '') {
            throw new \RuntimeException('Unable to set frontend register without register name.', 1774448258);
        }

        $value = $renderChildrenClosure();
        if (!is_string($value) && !is_int($value) && !is_bool($value) && !is_float($value)) {
            throw new \RuntimeException('Frontend register values must be scalar.', 1774448259);
        }
        $request = RequestResolver::resolveRequestFromRenderingContext($renderingContext, false);
        self::getRegisterStack($request)->current()->set($name, $value);
        return null;
    }

    private static function getRegisterStack(ServerRequestInterface $request): RegisterStack
    {
        $registerStack = $request->getAttribute('frontend.register.stack');
        if (!$registerStack instanceof RegisterStack) {
            throw new \RuntimeException('Unable to write frontend register without register stack.', 1774448261);
        }

        return $registerStack;
    }
}
