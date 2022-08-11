<?php

namespace App\Controller;

use App\Entity\CustomerAddress;
use App\Form\CustomerAddressFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/customer/address', name: 'customer_address_')]
class CustomerAddressController extends AbstractController
{

    public const FORM_TITLE_EDIT = 'Edycja adresu';
    public const FORM_TITLE_ADD = 'Dodawanie adresu';
    public const MESSAGE_ON_UPDATE = 'Poprawnie zmieniono adres!';
    public const MESSAGE_ON_ADD = 'Poprawnie dodano adres!';
    public const MESSAGE_ON_DELETE = 'Adres został usunięty!';
    public const ACCESS_DENIED = 'Brak uprawnień!';
    public const MESSAGE_DELETE_MAIN = 'Nie można usunąć głównego adresu!';


    #[Route('/edit/{id}', name: 'edit')]
    public function editAddress(CustomerAddress $address, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isValidCustomer($address)) {
            return $this->redirectToRoute('app_homepage');
        }
        $id = $address->getCustomer()->getId();
        $form = $this->createForm(CustomerAddressFormType::class, $address);
        $form->add('submit', SubmitType::class, ['label' => 'zapisz']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($address);
            $em->flush();
            $this->addFlash('success', self::MESSAGE_ON_UPDATE);
            return $this->redirectToRoute("customer_edit", ['id' => $id]);
        }

        return $this->render('customer_address/item.html.twig', ['form' => $form->createView(), 'form_title' => self::FORM_TITLE_EDIT]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function deleteAddress(CustomerAddress $address, EntityManagerInterface $em)
    {

        if (!$this->isValidCustomer($address)) {
            return $this->redirectToRoute('app_homepage');
        }
        $id = $address->getCustomer()->getId();
        /** @todo if main dont delete */
        if ($address->getIsMain()) {
            $this->addFlash('error', self::MESSAGE_DELETE_MAIN);
            return $this->redirectToRoute("customer_edit", ['id' => $id]);
        }

        $em->remove($address);
        $em->flush();
        $this->addFlash('warning', self::MESSAGE_ON_DELETE);
        return $this->redirectToRoute("customer_edit", ['id' => $id]);
    }

    private function isValidCustomer(CustomerAddress $address)
    {
        if ($address->getCustomer()->getVendor() !== $this->getUser()) {
            $this->addFlash(
                'error',
                self::ACCESS_DENIED
            );
            return false;
        }
        return true;
    }
}
