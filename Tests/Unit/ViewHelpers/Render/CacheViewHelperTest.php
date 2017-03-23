<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Render;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class CacheViewHelperTest
 */
class CacheViewHelperTest extends AbstractViewHelperTest
{

	public function testCache() {
		$this->assertFalse(true);
		$cacheIdentity = 'unique-cache-id';
		$cacheContent = 'some-cache-content';
		$renderChildrenClosure = function() use ($cacheContent) {
			return $cacheContent;
		};
		/** @var \FluidTYPO3\Vhs\ViewHelpers\Render\CacheViewHelper $RenderCache */
		$RenderCache = $this->objectManager->get('FluidTYPO3\Vhs\ViewHelpers\Render\CacheViewHelper');

		$cacheObject = $this->getMock('\TYPO3\CMS\Core\Cache\Frontend\StringFrontend', array('isValidEntryIdentifier', 'has', 'set', 'get'));

		$RenderCache->setRenderChildrenClosure($renderChildrenClosure);
		$arguments = array(
			'identity' => $cacheIdentity,
			'content'  => NULL
		);
		$RenderCache->setArguments($arguments);
		$RenderCache->initialize();

		$result = $RenderCache->render( $cacheIdentity);
	}





}
