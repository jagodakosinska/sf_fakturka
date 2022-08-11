<?php

namespace App\Tests;

use App\Controller\ItemController;
use App\Repository\ItemRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ItemTest extends WebTestCase
{

    private KernelBrowser $client;
    private UserRepository $userRepo;
    private ItemRepository $itemRepo;
    private UrlGeneratorInterface $urlRouter;


    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->userRepo = static::getContainer()->get(UserRepository::class);
        $this->itemRepo = static::getContainer()->get(ItemRepository::class);
        $this->urlRouter = static::getContainer()->get(UrlGeneratorInterface::class);
    }

    /** @test */
    public function is_set_item_for_user()
    {
        $user = $this->userRepo->findOneByEmail('user1@test.pl');
        $this->client->loginUser($user);
        $this->client->request('GET', '/item/');

        $resp = $this->client->getResponse();
        $this->assertResponseIsSuccessful();
        $this->assertEquals(1, substr_count($resp, 'Usługi programistyczne'));
    }


    /** @test */
    public function one_user_see_only_own_account()
    {
        $user = $this->userRepo->findOneByEmail('user2@test.pl');
        $this->client->loginUser($user);
        $this->client->request('GET', '/item/');

        $resp = $this->client->getResponse();
        $this->assertResponseIsSuccessful();
        $this->assertEquals(0, substr_count($resp, 'Usługi programistyczne'));
        $this->assertSelectorTextContains('.info_item', 'Brak dodanych towarów i usług');
    }

    /** @test */
    public function one_user_can_not_modify_another_user_account()
    {
        $user = $this->userRepo->findOneByEmail('user2@test.pl');
        $this->client->loginUser($user);
        $item = $this->itemRepo->findOneByName('Usługi programistyczne'); //
        $this->client->request('GET', '/item/edit/' . $item->getId());
        // $resp = $this->client->getResponse();
        // echo $resp;
        /** @var Session */
        $session = $this->client->getRequest()->getSession();
        $flash = $session->getFlashBag();
        $this->assertTrue($flash->has('error'));
        $this->assertContains(ItemController::ACCESS_DENIED, $flash->get('error'));
        $url = $this->urlRouter->generate('app_homepage');
        $this->assertResponseRedirects($url);
    }

    /** @test */
    public function one_user_can_not_delete_another_user_item()
    {
        $user = $this->userRepo->findOneByEmail('user2@test.pl');
        $this->client->loginUser($user);
        $item = $this->itemRepo->findOneByName('Usługi programistyczne');
        $this->client->request('GET', '/item/delete/' . $item->getId());

        /** @var Session */
        $session = $this->client->getRequest()->getSession();
        $flash = $session->getFlashBag();
        $this->assertTrue($flash->has('error'));
        $this->assertContains(ItemController::ACCESS_DENIED, $flash->get('error'));
        $url = $this->urlRouter->generate('app_homepage');
        $this->assertResponseRedirects($url);
        // $resp = $this->client->getResponse();
        // echo $resp;
    }

    /** @test */
    public function user_can_edit_item()
    {
        $user = $this->userRepo->findOneByEmail('user1@test.pl');
        $this->client->loginUser($user);
        $item = $this->itemRepo->findOneByName('Usługi programistyczne');
        $crowler = $this->client->request('GET', "/item/edit/{$item->getId()}");
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextSame('h1', ItemController::FORM_TITLE_EDIT);

        $form = $crowler->selectButton('Zapisz')->form();
        $form_name = $form->getName();
        $this->assertTrue($form->has($form_name . '[name]'));
        $this->assertTrue($form->has($form_name . '[description]'));
        $newName = 'Updated service name';
        $newDescrption = 'Updated additional description for service';
        $form[$form_name . '[name]'] = $newName;
        $form[$form_name . '[description]'] = $newDescrption;
        $this->client->submit($form);

        $item = $this->itemRepo->findOneByName($newName);
        $this->assertEquals($newName, $item->getName());
        $this->assertEquals($newDescrption, $item->getDescription());
        /** @var Session */
        $session = $this->client->getRequest()->getSession();
        $flash = $session->getFlashBag();
        $this->assertTrue($flash->has('success'));
        $this->assertContains(ItemController::MESSAGE_ON_UPDATE, $flash->get('success'));

        $url = $this->urlRouter->generate('item_list');
        $this->assertResponseRedirects($url);
    }

    /** @test */
    public function can_user_add_his_item()
    {
        $user = $this->userRepo->findOneByEmail('user1@test.pl');
        $this->client->loginUser($user);


        $crowler = $this->client->request('GET', '/item/add');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextSame('h1', ItemController::FORM_TITLE_ADD);
        $counter = $this->itemRepo->countByVendor($user);

        $form = $crowler->selectButton('Zapisz')->form();
        $form_name = $form->getName();
        $this->assertTrue($form->has($form_name . '[name]'));
        $this->assertTrue($form->has($form_name . '[description]'));

        $newName = 'New service name';
        $newDescrption = 'New additional description for service';
        $form[$form_name . '[name]'] = $newName;
        $form[$form_name . '[description]'] = $newDescrption;
        $this->client->submit($form);
        /** @var Session */
        $session = $this->client->getRequest()->getSession();
        $flash = $session->getFlashBag();
        $this->assertTrue($flash->has('success'));
        $this->assertContains(ItemController::MESSAGE_ON_ADD, $flash->get('success'));

        $item = $this->itemRepo->findOneByName($newName);
        $this->assertEquals($newName, $item->getName());
        $this->assertEquals($newDescrption, $item->getDescription());
        $this->assertEquals($user->getId(), $item->getVendor()->getId());
        $counter2 = $this->itemRepo->countByVendor($user);
        $this->assertEquals($counter + 1, $counter2);
    }
}
