<?php

namespace App\Tests;

use App\Controller\CustomerAddressController;
use App\Repository\UserRepository;
use App\Controller\CustomerController;
use App\Repository\CustomerRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/** @group customer */
class CustomerTest extends WebTestCase
{
    private KernelBrowser $client;
    private UserRepository $userRepo;
    private CustomerRepository $customerRepo;
    private UrlGeneratorInterface $urlRouter;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->userRepo = static::getContainer()->get(UserRepository::class);
        $this->customerRepo = static::getContainer()->get(CustomerRepository::class);
        $this->urlRouter = static::getContainer()->get(UrlGeneratorInterface::class);
    }

    /** @test */
    public function logged_user_see_his_customer()
    {
        $user = $this->userRepo->findOneByEmail('user1@test.pl');
        $this->client->loginUser($user);
        $this->client->request('GET', '/customer/');
        $resp = $this->client->getResponse();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(1, substr_count($resp, 'ABC Sp. z o.o.'));
    }

    /** @test */
    public function user_can_add_new_customer()
    {
        $user = $this->userRepo->findOneByEmail('user1@test.pl');
        $this->client->loginUser($user);
        $countCustomer = $this->customerRepo->countByVendor($user);
        $crowler = $this->client->request('POST', '/customer/add');
        $response = $this->client->getResponse();
        $this->assertResponseIsSuccessful();
        $this->assertEquals(1, substr_count($response, CustomerController::FORM_TITLE_ADD));
        // dd($response);
        $form = $crowler->selectButton('Zapisz')->form();

        $form_name = $form->getName();
        $this->assertTrue($form->has($form_name . '[name]'));
        $this->assertTrue($form->has($form_name . '[description]'));
        $this->assertTrue($form->has($form_name . '[nip]'));

        $newName = 'New Name';
        $newDescrption = 'New description';
        $newNip = 'New nip';
        $newCity = 'New city';
        $newZip = 'New zip';
        $newStreet = 'New street';

        $form[$form_name . '[name]'] = $newName;
        $form[$form_name . '[description]'] = $newDescrption;
        $form[$form_name . '[nip]'] = $newNip;
        $form[$form_name . '[new_address][city]'] = $newCity;
        $form[$form_name . '[new_address][zipCode]'] = $newZip;
        $form[$form_name . '[new_address][street]'] = $newStreet;
        $this->client->submit($form);

        /** @var Session */
        $session = $this->client->getRequest()->getSession();
        $flash = $session->getFlashBag();
        $this->assertTrue($flash->has('success'));
        $this->assertContains(CustomerController::MESSAGE_ON_ADD, $flash->get('success'));

        $countCustomer2 = $this->customerRepo->countByVendor($user);
        $this->assertEquals($countCustomer + 1, $countCustomer2);

        $newCustomer = $this->customerRepo->findOneByName('New Name');
        $this->assertEquals($newName, $newCustomer->getName());
        $this->assertSame($newNip, $newCustomer->getNip());
        $this->assertSame($user->getId(), $newCustomer->getVendor()->getId());
    }


    /** @test */
    public function user_can_edit_his_customer()
    {
        $user = $this->userRepo->findOneByEmail('user1@test.pl');
        $this->client->loginUser($user);
        $customer = $this->customerRepo->findOneByName('ABC Sp. z o.o.');
        $crowler = $this->client->request('POST', '/customer/edit/' . $customer->getId());
        $response = $this->client->getResponse();
        $this->assertResponseIsSuccessful();
        $this->assertEquals(1, substr_count($response, CustomerController::FORM_TITLE_EDIT));
        $form = $crowler->selectButton('Zapisz')->form();
        $form_name = $form->getName();
        $this->assertTrue($form->has($form_name . '[name]'));
        $this->assertTrue($form->has($form_name . '[description]'));
        $this->assertTrue($form->has($form_name . '[nip]'));
        $updatedName = 'Updated Name';
        $updatedDescrption = 'Updated description';

        $form[$form_name . '[name]'] = $updatedName;
        $form[$form_name . '[description]'] = $updatedDescrption;
        $this->client->submit($form);
        $newCustomer = $this->customerRepo->findOneByName('Updated Name');
        /** @var Session */
        $session = $this->client->getRequest()->getSession();
        $flash = $session->getFlashBag();
        $this->assertTrue($flash->has('success'));
        $this->assertContains(CustomerController::MESSAGE_ON_UPDATE, $flash->get('success'));

        $updatedCustomer = $this->customerRepo->findOneByName('Updated Name');
        $this->assertEquals($updatedName, $updatedCustomer->getName());
        $this->assertSame($updatedDescrption, $updatedCustomer->getDescription());
        $this->assertSame($user->getId(), $updatedCustomer->getVendor()->getId());
    }

    /** @test */
    public function user_can_delete_his_customer()
    {
        $user = $this->userRepo->findOneByEmail('user1@test.pl');
        $this->client->loginUser($user);
        // $customer = $this->userRepo->findOneByName('ABC Sp. z o.o.');
        // $crowler = $this->client->request('POST', '/customer/delete/' . $customer->getId());
    }
    public function user_can_change_main_address()
    {
    }
    public function user_can_delete_main_address()
    {
    }
    public function user_can_edit_address()
    {
    }
    public function user_can_add_new_address()
    {
    }
}
