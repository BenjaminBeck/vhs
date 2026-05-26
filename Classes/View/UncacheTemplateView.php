<?php
namespace FluidTYPO3\Vhs\View;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;
use TYPO3\CMS\Extbase\Mvc\ExtbaseRequestParameters;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Fluid\Compatibility\TemplateParserBuilder;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextFactory;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

if (!class_exists(__NAMESPACE__ . '\\CompatTemplateView')) {
    class_alias(
        class_exists('\\TYPO3\\CMS\\Fluid\\View\\TemplateView')
            ? '\\TYPO3\\CMS\\Fluid\\View\\TemplateView'
            : '\\TYPO3Fluid\\Fluid\\View\\TemplateView',
        __NAMESPACE__ . '\\CompatTemplateView'
    );
}

class UncacheTemplateView extends CompatTemplateView
{
    public function callUserFunction(string $postUserFunc, array $conf): string
    {
        $partial = $conf['partial'] ?? null;
        $section = $conf['section'] ?? null;
        $arguments = $conf['arguments'] ?? [];
        $parameters = $conf['controllerContext'] ?? null;
        $extensionName = $parameters instanceof ExtbaseRequestParameters
            ? $parameters->getControllerExtensionName()
            : $parameters['extensionName'] ?? null;

        if (empty($partial)) {
            return '';
        }

        if (class_exists(RenderingContextFactory::class)) {
            $request = null;
            if (
                property_exists($this, 'renderingContext')
                && $this->renderingContext instanceof RenderingContextInterface
                && method_exists($this->renderingContext, 'getRequest')
            ) {
                $request = $this->renderingContext->getRequest();
            }
            if (!$request instanceof ServerRequestInterface && isset($GLOBALS['TYPO3_REQUEST'])) {
                $request = $GLOBALS['TYPO3_REQUEST'];
            }
            if ($parameters instanceof ExtbaseRequestParameters && $request instanceof ServerRequestInterface) {
                $request = $request->withAttribute('extbase', $parameters);
            }
            $renderingContext = $this->createRenderingContextWithRenderingContextFactory(
                // TYPO3 v11.x needs the ServerRequest wrapped in an Extbase Request.
                version_compare(VersionNumberUtility::getCurrentTypo3Version(), '12.0', '<') && $request instanceof ServerRequestInterface
                    ? new Request($request)
                    : $request
            );
        } else {
            /** @var ControllerContext $controllerContext */
            $controllerContext = GeneralUtility::makeInstance(ControllerContext::class);
            /** @var Request $request */
            $request = GeneralUtility::makeInstance(Request::class);
            $controllerContext->setRequest($request);

            /** @var UriBuilder $uriBuilder */
            $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
            $uriBuilder->setRequest($request);
            $controllerContext->setUriBuilder($uriBuilder);

            if ($parameters) {
                if (method_exists($request, 'setControllerActionName')) {
                    $request->setControllerActionName($parameters['actionName']);
                }

                if (method_exists($request, 'setControllerExtensionName')) {
                    $request->setControllerExtensionName($parameters['extensionName']);
                }

                if (method_exists($request, 'setControllerName')) {
                    $request->setControllerName($parameters['controllerName']);
                }

                if (method_exists($request, 'setControllerObjectName')) {
                    $request->setControllerObjectName($parameters['controllerObjectName']);
                }

                if (method_exists($request, 'setPluginName')) {
                    $request->setPluginName($parameters['pluginName']);
                }

                if (method_exists($request, 'setFormat')) {
                    $request->setFormat($parameters['format']);
                }
            }

            /** @var RenderingContext $renderingContext */
            $renderingContext = GeneralUtility::makeInstance(RenderingContext::class);
        }

        $this->prepareContextsForUncachedRendering($renderingContext);
        if (!empty($conf['partialRootPaths'])) {
            $renderingContext->getTemplatePaths()->setPartialRootPaths($conf['partialRootPaths']);
        } elseif ($extensionName) {
            $extensionKey = GeneralUtility::camelCaseToLowerCaseUnderscored($extensionName);
            $renderingContext->getTemplatePaths()->fillDefaultsByPackageName($extensionKey);
        }
        return $this->renderPartialUncached($renderingContext, $partial, $section, $arguments);
    }

    protected function prepareContextsForUncachedRendering(RenderingContextInterface $renderingContext): void
    {
        $this->setRenderingContext($renderingContext);
    }

    protected function renderPartialUncached(
        RenderingContextInterface $renderingContext,
        string $partial,
        ?string $section = null,
        array $arguments = []
    ): string {
        $this->renderingStack[] = [
            'type' => static::RENDERING_TEMPLATE,
            'parsedTemplate' => $this->getCurrentParsedTemplate(),
            'renderingContext' => $renderingContext,
        ];
        /** @var string $rendered */
        $rendered = $this->renderPartial($partial, $section, $arguments);
        array_pop($this->renderingStack);
        return $rendered;
    }

    /**
     * @codeCoverageIgnore
     */
    protected function createRenderingContextWithRenderingContextFactory(?ServerRequestInterface $request = null): RenderingContextInterface
    {
        /** @var RenderingContextFactory $renderingContextFactory */
        $renderingContextFactory = GeneralUtility::makeInstance(RenderingContextFactory::class);
        return $renderingContextFactory->create([], $request);
    }
}
