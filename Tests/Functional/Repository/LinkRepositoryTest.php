<?php

namespace Ecodev\Newsletter\Tests\Functional\Repository;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
require_once __DIR__ . '/../AbstractFunctionalTestCase.php';

/**
 * Functional test for the \Ecodev\Newsletter\Domain\Repository\LinkRepository
 */
class LinkRepositoryTest extends \Ecodev\Newsletter\Tests\Functional\AbstractFunctionalTestCase
{
    /** @var \Ecodev\Newsletter\Domain\Repository\LinkRepository */
    private $linkRepository;

    /** @var \Ecodev\Newsletter\Domain\Repository\EmailRepository */
    private $emailRepository;

    public function setUp()
    {
        parent::setUp();
        $this->linkRepository = $this->objectManager->get('Ecodev\\Newsletter\\Domain\\Repository\\LinkRepository');
        $this->emailRepository = $this->objectManager->get('Ecodev\\Newsletter\\Domain\\Repository\\EmailRepository');

        // When testing we need to help the core by filling HTTP_HOST variable to be able to build correct URL
        $_SERVER['HTTP_HOST'] = 'example.com';
        $_SERVER['SCRIPT_NAME'] = '/index.php';
    }

    public function testFindAllByNewsletter()
    {
        $this->assertCount(0, $this->linkRepository->findAllByNewsletter(10, 0, 999));

        $links = $this->linkRepository->findAllByNewsletter(30, 0, 999);
        $this->assertCount(2, $links);
        $this->assertEquals(3001, $links[0]->getUid());
        $this->assertEquals(3002, $links[1]->getUid());

        $links = $this->linkRepository->findAllByNewsletter(30, 1, 999);
        $this->assertCount(1, $links);
        $this->assertEquals(3002, $links[0]->getUid());

        $links = $this->linkRepository->findAllByNewsletter(30, 2, 999);
        $this->assertCount(0, $links);

        $links = $this->linkRepository->findAllByNewsletter(30, 0, 1);
        $this->assertCount(1, $links);
        $this->assertEquals(3001, $links[0]->getUid());

        $links = $this->linkRepository->findAllByNewsletter(30, 1, 1);
        $this->assertCount(1, $links);
        $this->assertEquals(3002, $links[0]->getUid());

        $links = $this->linkRepository->findAllByNewsletter(30, 2, 1);
        $this->assertCount(0, $links);
    }

    public function testGetCount()
    {
        $this->assertEquals(0, $this->linkRepository->getCount(10));
        $this->assertEquals(2, $this->linkRepository->getCount(30));
    }

    public function testRegisterClick()
    {
        $authCodeForLink = md5($this->authCode . 3001);
        $url = $this->linkRepository->registerClick(30, $authCodeForLink, false);
        $this->assertEquals('http://example.com/index.php?id=1&tx_newsletter_p%5Baction%5D=show&tx_newsletter_p%5Bcontroller%5D=Email&type=1342671779&c=87c4e9b09085befbb7f20faa7482213a', $url, 'the URL returned must have markers substituted');

        $link = $this->linkRepository->findByUid(3001);
        $this->assertEquals(1, $link->getOpenedCount(), 'the link opened count must have been incrementated');
        $this->assertRecipientListCallbackWasCalled('clicked recipient2@example.com');

        $db = $this->getDatabaseConnection();
        $count = $db->exec_SELECTcountRows('*', 'tx_newsletter_domain_model_linkopened', 'link = 3001 AND email = 302');
        $this->assertEquals(1, $count, 'must have exactly 1 linkopened record for this link');

        $email = $this->emailRepository->findByUid(302);
        $this->assertTrue($email->isOpened(), 'email should be marked as open, even if the open spy did not work, because a link was clicked');
        $this->assertRecipientListCallbackWasCalled('opened recipient2@example.com');
    }
}
