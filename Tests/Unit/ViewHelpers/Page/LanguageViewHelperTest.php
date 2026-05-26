<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Service\PageService;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Frontend\Page\PageInformation;

/**
 * Class LanguageViewHelperTest
 */
class LanguageViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @var PageService&MockObject
     */
    private $pageService;

    protected function setUp(): void
    {
        $this->pageService = $this->singletonInstances[PageService::class] = $this->getMockBuilder(PageService::class)
            ->setMethods(['hidePageForLanguageUid'])
            ->disableOriginalConstructor()
            ->getMock();

        parent::setUp();

        $pageInformation = new PageInformation();
        $pageInformation->setId(1);
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest())
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE)
            ->withAttribute('frontend.page.information', $pageInformation);
    }

    public function testRender(): void
    {
        $this->pageService->method('hidePageForLanguageUid')->willReturn(false);
        $this->assertEmpty($this->executeViewHelper());
    }
}
