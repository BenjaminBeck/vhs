<?php

namespace FluidTYPO3\Vhs\Events;

use FluidTYPO3\Vhs\Service\AssetService;
use TYPO3\CMS\Frontend\Event\AfterCacheableContentIsGeneratedEvent;

class AfterCacheableContentIsGeneratedEventListener
{
    private AssetService $assetService;

    public function __construct(AssetService $assetService)
    {
        $this->assetService = $assetService;
    }

    public function insertVhsAssetHeaderAndFooterCode(AfterCacheableContentIsGeneratedEvent $event): void
    {
        if ($this->isAssetHandlingDisabled()) {
            return;
        }
        $content = $event->getContent();
        $this->assetService->buildAll([], $event->getRequest(), $event->isCachingEnabled(), $content);
        $event->setContent($content);
    }

    private function isAssetHandlingDisabled(): bool
    {
        $disabled = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['vhs']['disableAssetHandling']
            ?? $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['vhs']['setup']['disableAssetHandling']
            ?? false;

        return filter_var($disabled, \FILTER_VALIDATE_BOOL);
    }
}
