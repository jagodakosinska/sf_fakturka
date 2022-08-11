<?php

namespace App\Tests;

use App\Repository\UserRepository;
use App\Controller\BankAccountController;
use App\Repository\BankAccountRepository;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BankAccountTest extends WebTestCase
{

    private KernelBrowser $client;
    private UserRepository $userRepo;
    private BankAccountRepository $bankAccRepo;
    private UrlGeneratorInterface $urlRouter;


    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->userRepo = static::getContainer()->get(UserRepository::class);
        $this->bankAccRepo = static::getContainer()->get(BankAccountRepository::class);
        $this->urlRouter = static::getContainer()->get(UrlGeneratorInterface::class);
    }

    /** @test */
    public function is_set_bank_account_for_user()
    {
        $user = $this->userRepo->findOneByEmail('user1@test.pl');
        $this->client->loginUser($user);
        $this->client->request('GET', '/bank_account/');

        $resp = $this->client->getResponse();
        $this->assertResponseIsSuccessful();
        $this->assertEquals(1, substr_count($resp, 'user1_bank_name'));
    }

    /** @test */
    public function one_user_see_only_own_account()
    {
        $user = $this->userRepo->findOneByEmail('user2@test.pl');
        $this->client->loginUser($user);
        $this->client->request('GET', '/bank_account/');

        $resp = $this->client->getResponse();
        $this->assertResponseIsSuccessful();
        $this->assertEquals(0, substr_count($resp, 'user1_bank_name'));
        $this->assertSelectorTextContains('.info_acc', 'Brak rachunków bankowych');
    }

    /** @test */
    public function one_user_can_not_modify_another_user_account()
    {
        $user = $this->userRepo->findOneByEmail('user2@test.pl');
        $this->client->loginUser($user);
        $bankAccount = $this->bankAccRepo->findOneByName('user1_bank_name'); //
        $this->client->request('GET', '/bank_account/edit/' . $bankAccount->getId());
        // $resp = $this->client->getResponse();
        // echo $resp;
        /** @var Session */
        $session = $this->client->getRequest()->getSession();
        $flash = $session->getFlashBag();
        $this->assertTrue($flash->has('error'));
        $this->assertContains(BankAccountController::ACCESS_DENIED, $flash->get('error'));
        $url = $this->urlRouter->generate('app_homepage');
        $this->assertResponseRedirects($url);
    }

    /** @test */
    public function one_user_can_not_delete_another_user_account()
    {
        $user = $this->userRepo->findOneByEmail('user2@test.pl');
        $this->client->loginUser($user);
        $bankAccount = $this->bankAccRepo->findOneByName('user1_bank_name'); //
        $this->client->request('GET', '/bank_account/delete/' . $bankAccount->getId());

        /** @var Session */
        $session = $this->client->getRequest()->getSession();
        $flash = $session->getFlashBag();
        $this->assertTrue($flash->has('error'));
        $this->assertContains(BankAccountController::ACCESS_DENIED, $flash->get('error'));
        $url = $this->urlRouter->generate('app_homepage');
        $this->assertResponseRedirects($url);
        // $resp = $this->client->getResponse();
        // echo $resp;
    }

    /** @test */
    public function can_user_edit_his_bank_account()
    {
        $user = $this->userRepo->findOneByEmail('user1@test.pl');
        $this->client->loginUser($user);
        $bankAccount = $this->bankAccRepo->findOneByName('user1_bank_name'); //
        $crowler = $this->client->request('GET', '/bank_account/edit/' . $bankAccount->getId());
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextSame('h1', BankAccountController::FORM_TITLE_EDIT);
        $form = $crowler->selectButton('Zapisz')->form();
        $form_name = $form->getName();
        $this->assertTrue($form->has($form_name . '[name]'));
        $this->assertTrue($form->has($form_name . '[number]'));
        $newName = 'Updated bank name';
        $newNumber = 'Updated bank number';
        $form[$form_name . '[name]'] = $newName;
        $form[$form_name . '[number]'] = $newNumber;
        $this->client->submit($form);
        $updatedBankAcc = $this->bankAccRepo->findOneById($bankAccount->getId());
        $this->assertEquals($newName, $updatedBankAcc->getName());
        $this->assertEquals($newNumber, $updatedBankAcc->getNumber());
    }

    /** @test */
    public function can_user_add_his_bank_account()
    {
        $user = $this->userRepo->findOneByEmail('user1@test.pl');
        $this->client->loginUser($user);
        $count_acc = $this->bankAccRepo->countByOwner($user);

        $crowler = $this->client->request('GET', '/bank_account/add');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextSame('h1', BankAccountController::FORM_TITLE_ADD);

        $form = $crowler->selectButton('Zapisz')->form();
        $form_name = $form->getName();
        $this->assertTrue($form->has($form_name . '[name]'));
        $this->assertTrue($form->has($form_name . '[number]'));
        $newName = 'New bank name';
        $newNumber = 'New bank number';
        $form[$form_name . '[name]'] = $newName;
        $form[$form_name . '[number]'] = $newNumber;
        $this->client->submit($form);
        /** @var Session */
        $session = $this->client->getRequest()->getSession();
        $flash = $session->getFlashBag();
        $this->assertTrue($flash->has('success'));
        $this->assertContains(BankAccountController::MESSAGE_ON_ADD, $flash->get('success'));

        $newBankAcc = $this->bankAccRepo->findOneByName('New bank name');
        $this->assertEquals($newName, $newBankAcc->getName());
        $this->assertEquals($newNumber, $newBankAcc->getNumber());
        $this->assertEquals($user->getId(), $newBankAcc->getOwner()->getId());

        $count_acc2 = $this->bankAccRepo->countByOwner($user);
        $this->assertEquals($count_acc + 1, $count_acc2);
    }
}
