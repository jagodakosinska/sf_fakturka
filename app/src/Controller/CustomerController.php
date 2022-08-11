<?php

namespace App\Controller;


use App\Entity\Customer;
use App\Form\CustomerFormType;
use App\Entity\CustomerAddress;
use App\Form\CustomerAddressFormType;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/customer', name: 'customer_')]
class CustomerController extends AbstractController
{

    public const FORM_TITLE_EDIT = 'Edycja kontrahenta';
    public const FORM_TITLE_ADD = 'Dodawanie kontrahenta';
    public const MESSAGE_ON_UPDATE = 'Poprawnie zmieniono dane kontrahenta!';
    public const MESSAGE_ON_ADD = 'Poprawnie dodano kontrahenta!';
    public const MESSAGE_ON_DELETE = 'Kontrahent został usunięty!';
    public const ACCESS_DENIED = 'Brak uprawnień!';



    #[Route('/', name: 'list')]
    public function index(): Response
    {
        $customers = $this->getUser()->getCustomers();

        return $this->render('customer/index.html.twig', [

            'customers' =>  $customers,

        ]);
    }

    #[Route('/add', name: 'add')]
    public function addCustomer(Request $request, EntityManagerInterface $em): Response
    {
        $customer = new Customer();
        $address = new CustomerAddress();
        $form = $this->createForm(CustomerFormType::class, $customer);

        $form->add(
            'new_address',
            CustomerAddressFormType::class,
            [
                'label' => false,
                'mapped' => false,
                'data' => $address,
                'required' => true,
                'constraints' => new Valid(),
            ]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // dd($request);
            $vendor = $this->getUser();
            $customer->setVendor($vendor);
            $address->setCustomer($customer)->setIsMain(true);
            $em->persist($customer);
            $em->persist($address);
            $em->flush();
            $this->addFlash('success', self::MESSAGE_ON_ADD);
            return $this->redirectToRoute('customer_list');
        }
        return $this->render('customer/item.html.twig', ['customer_form' => $form->createView(), 'form_title' => self::FORM_TITLE_ADD, 'addresses_list' => '']);
    }

    #[Route('/edit/{id}', name: "edit")]
    public function editCustomer(Customer $customer, EntityManagerInterface $em, Request $request): Response
    {
        if (!$this->isValidVendor($customer)) {
            return $this->redirectToRoute('app_homepage');
        }
        $address = new CustomerAddress();
        $address->setCustomer($customer)->setIsMain(false);

        $form = $this->createForm(CustomerFormType::class, $customer, ['unset_empty_form' => 'new_address']);
        $form->add(
            'new_address',
            CustomerAddressFormType::class,
            [
                'label' => false,
                'mapped' => false,
                'data' => $address,
                'required' => false,
                'constraints' => new Valid(),
            ]
        );
        $form->add('main_addres', ChoiceType::class, [
            'expanded' => true, 'multiple' => false, 'mapped' => false, 'label' => 'Główny',
            'choices' => $customer->getAddresses(),
            'choice_value' => 'id',
            // 'choice_label' => function (CustomerAddress $address) {
            //     return $address->getCity() . ' ' . $address->getStreet();
            // },
            'choice_label' => false,
            'data' => $customer->getMainAddress(),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->get('main_addres')->getData() != $customer->getMainAddress()) {

                $old_main_address = $customer->getMainAddress()->setIsMain(false);
                $new_main_address = $form->get('main_addres')->getData()->setIsMain(true);
                $em->persist($old_main_address);
                $em->persist($new_main_address);
            }

            $em->persist($customer);

            if ($form->has('new_address')) {
                $em->persist($address);
            };


            $em->flush();
            $id = $address->getCustomer()->getId();
            $this->addFlash('success', self::MESSAGE_ON_UPDATE);
            return $this->redirectToRoute("customer_edit", ['id' => $id]);
        }
        return $this->render('customer/item.html.twig', ['customer_form' => $form->createView(), 'form_title' => self::FORM_TITLE_EDIT,  'addresses_list' => $customer->getAddresses(),]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function deleteCustomer(Customer $customer, EntityManagerInterface $em)
    {
        if (!$this->isValidVendor($customer)) {
            return $this->redirectToRoute('app_homepage');
        }
        $em->remove($customer);
        $em->flush();
        $this->addFlash('warning', self::MESSAGE_ON_DELETE);
        return $this->redirectToRoute('customer_list');
    }

    private function isValidVendor($customer)
    {
        if ($customer->getVendor() !== $this->getUser()) {
            $this->addFlash('error', self::ACCESS_DENIED);
            return false;
        }
        return true;
    }
}
