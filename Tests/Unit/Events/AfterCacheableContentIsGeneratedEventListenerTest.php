<?php
namespace FluidTYPO3\Vhs\Tests\Unit\Events;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Events\AfterCacheableContentIsGeneratedEventListener;
use FluidTYPO3\Vhs\Service\AssetService;
use FluidTYPO3\Vhs\Tests\Unit\AbstractTestCase;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Frontend\Event\AfterCacheableContentIsGeneratedEvent;

class AfterCacheableContentIsGeneratedEventListenerTest extends AbstractTestCase
{
    /**
     * @test
     */
    public function skipsAssetInjectionWhenAssetHandlingIsDisabled(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['vhs']['disableAssetHandling'] = true;

        $assetService = $this->getMockBuilder(AssetService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['buildAll'])
            ->getMock();
        $assetService->expects($this->never())->method('buildAll');

        $event = new AfterCacheableContentIsGeneratedEvent(new ServerRequest(), 'content', 'cache-identifier', true);
        (new AfterCacheableContentIsGeneratedEventListener($assetService))->insertVhsAssetHeaderAndFooterCode($event);

        $this->assertSame('content', $event->getContent());
    }

    /**
     * @test
     */
    public function skipsAssetInjectionWhenLegacyAssetHandlingFlagIsDisabled(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['vhs']['setup']['disableAssetHandling'] = '1';

        $assetService = $this->getMockBuilder(AssetService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['buildAll'])
            ->getMock();
        $assetService->expects($this->never())->method('buildAll');

        $event = new AfterCacheableContentIsGeneratedEvent(new ServerRequest(), 'content', 'cache-identifier', true);
        (new AfterCacheableContentIsGeneratedEventListener($assetService))->insertVhsAssetHeaderAndFooterCode($event);

        $this->assertSame('content', $event->getContent());
    }
}
