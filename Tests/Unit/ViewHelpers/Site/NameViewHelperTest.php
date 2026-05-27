<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Site;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;

/**
 * Class NameViewHelperTest
 */
class NameViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @test
     */
    public function rendersSiteName(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'] = 'test';
        $test = $this->executeViewHelper();
        unset($GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename']);
        $this->assertSame('test', $test);
    }

    /**
     * @test
     */
    public function rendersCurrentLanguageWebsiteTitle(): void
    {
        $language = $this->getMockBuilder(SiteLanguage::class)
            ->onlyMethods(['getWebsiteTitle'])
            ->disableOriginalConstructor()
            ->getMock();
        $language->method('getWebsiteTitle')->willReturn('Language title');
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest())->withAttribute('language', $language);

        self::assertSame('Language title', $this->executeViewHelper());
    }

    /**
     * @test
     */
    public function rendersSiteWebsiteTitle(): void
    {
        $site = new Site('test', 1, [
            'base' => '/',
            'websiteTitle' => 'Site title',
            'languages' => [],
        ]);
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest())->withAttribute('site', $site);

        self::assertSame('Site title', $this->executeViewHelper());
    }
}
