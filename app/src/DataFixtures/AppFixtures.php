<?php

namespace App\DataFixtures;

use App\Entity\Item;
use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Invoice;
use App\Entity\Customer;
use App\Entity\PaidType;
use App\Entity\BankAccount;
use App\Entity\InvoiceItem;
use App\Entity\CustomerAddress;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{


    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager)
    {


        // $faker = Factory::create('pl_PL');

        $admin = new User();
        $admin->setEmail('admin@admin.pl');
        $password = $this->passwordHasher->hashPassword($admin, 'pass');
        $admin->setPassword($password);
        $admin->setRoles([User::ROLE_ADMIN]);
        $manager->persist($admin);

        $user = new User();
        $user->setEmail('user1@test.pl');
        $password1 = $this->passwordHasher->hashPassword($user, 'test');
        $user->setPassword($password1);
        $user->setRoles(['ROLE_USER']);
        $manager->persist($user);

        $user1 = new User();
        $user1->setEmail('user2@test.pl');
        $password2 = $this->passwordHasher->hashPassword($user1, 'test');
        $user1->setPassword($password2);
        $user1->setRoles(['ROLE_USER']);
        $manager->persist($user1);

        //BankAccount
        $bankAcc = new BankAccount();
        $bankAcc->setname('user1_bank_name');
        $bankAcc->setNumber('PL 1214-5632-5962-2333');
        $bankAcc->setOwner($user);
        $manager->persist($bankAcc);


        //Customer
        $customer = new Customer();
        $customer->setName('ABC Sp. z o.o.');
        $customer->setDescription('');
        $customer->setNip('8991567065');
        $customer->setVendor($user);
        $manager->persist($customer);

        //CustomerAddress
        $customerAddress = new CustomerAddress();
        $customerAddress->setCity('Wrocław');
        $customerAddress->setZipCode('50-040');
        $customerAddress->setStreet('Błękitna 3');
        $customerAddress->setCustomer($customer);
        $customerAddress->setIsMain(true);
        $customerAddress->setValidFrom(new DateTimeImmutable());
        $manager->persist($customerAddress);

        //PaidType
        $paidType = new PaidType();
        $paidType->setName('gotówka');
        $paidType->setDescription('płatność gotówką');
        $paidType->setLabel('CASH');
        $manager->persist($paidType);

        //PaidType
        $paidType2 = new PaidType();
        $paidType2->setName('przelew');
        $paidType2->setDescription('płatność przelewem');
        $paidType2->setLabel('TRANSFER');
        $manager->persist($paidType2);

        //Invoice
        $invoice = new Invoice();
        $invoice->setNumber('1/01/2021');
        $invoice->setSellDate(new DateTimeImmutable());
        $invoice->setIssueDate(new DateTimeImmutable());
        $invoice->setPlace('Wrocław');
        $invoice->setToPay(123000);
        $invoice->setPaid(0);
        $invoice->setAmount(123000);
        $invoice->setVendor($user);
        $invoice->setCustomer($customer);
        $invoice->setPaidType($paidType);
        $manager->persist($invoice);

        //Item
        $item = new Item();
        $item->setVendor($user);
        $item->setName('Usługi programistyczne');
        $item->setDescription('');
        $manager->persist($item);

        //InvoiceItem
        $invoiceItem = new InvoiceItem();
        $invoiceItem->setQuantity(1);
        $invoiceItem->setQuantityType('szt');
        $invoiceItem->setVatRate(2300);
        $invoiceItem->setUnitPrice(100000);
        $invoiceItem->setTotalPrice(100000);
        $invoiceItem->setInvoice($invoice);
        $invoiceItem->setItem($item);
        $invoiceItem->setItemOrder(1);
        $manager->persist($item);

        $manager->flush();
    }
}
