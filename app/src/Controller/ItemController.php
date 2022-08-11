<?php

namespace App\Controller;

use App\Entity\Item;
use App\Form\ItemFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/item', name: 'item_')]
class ItemController extends AbstractController
{

    public const FORM_TITLE_EDIT = 'Edycja towaru/usługi';
    public const FORM_TITLE_ADD = 'Dodawanie towaru/usługi';
    public const MESSAGE_ON_UPDATE = 'Poprawnie zmieniono pozycję!';
    public const MESSAGE_ON_ADD = 'Poprawnie dodano nową pozycję!';
    public const MESSAGE_ON_DELETE = 'Pozycja została usunięta!';
    public const ACCESS_DENIED = 'Brak uprawnień!';


    #[Route('/', name: 'list')]
    public function index(): Response
    {
        $items = $this->getUser()->getItems();

        return $this->render('item/index.html.twig', [
            'items' => $items,
        ]);
    }

    #[Route('/add', name: "add")]
    public function addItem(Request $request, EntityManagerInterface $em): Response
    {
        $item = new Item();
        $form = $this->createForm(ItemFormType::class, $item);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $vendor = $this->getUser();
            $item->setVendor($vendor);
            $em->persist($item);
            $em->flush();
            $this->addFlash('success', self::MESSAGE_ON_ADD);
            return $this->redirectToRoute('item_list');
        }

        return $this->render('item/item.html.twig', ['item_form' => $form->createView(), 'form_title' => self::FORM_TITLE_ADD]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function editItem(Request $request, Item $item, EntityManagerInterface $em)
    {
        if (!$this->isValidVendor($item)) {
            return $this->redirectToRoute('app_homepage');
        }
        $form = $this->createForm(ItemFormType::class, $item);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($item);
            $em->flush();
            $this->addFlash('success', self::MESSAGE_ON_UPDATE);

            return $this->redirectToRoute('item_list');
        }
        return $this->render('item/item.html.twig', ['item_form' => $form->createView(), 'form_title' => self::FORM_TITLE_EDIT]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function deleteItem(Item $item, EntityManagerInterface $em)
    {
        if (!$this->isValidVendor($item)) {
            return $this->redirectToRoute('app_homepage');
        }
        $em->remove($item);
        $em->flush();
        $this->addFlash('warning', self::MESSAGE_ON_DELETE);
        return $this->redirectToRoute('item_list');
    }

    private function isValidVendor($item)
    {
        if ($item->getVendor() !== $this->getUser()) {
            $this->addFlash(
                'error',
                self::ACCESS_DENIED
            );
            return false;
        }
        return true;
    }
}
