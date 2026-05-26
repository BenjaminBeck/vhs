<?php

namespace FluidTYPO3\Vhs\Tests\Fixtures\Classes;

use TYPO3\CMS\Frontend\Controller\FrontendController;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

if (!class_exists(TypoScriptFrontendController::class) && class_exists(FrontendController::class)) {
    class_alias(FrontendController::class, TypoScriptFrontendController::class);
}

class DummyTypoScriptFrontendController extends TypoScriptFrontendController
{
    public function __construct()
    {
        $this->id = 1;
    }
}
