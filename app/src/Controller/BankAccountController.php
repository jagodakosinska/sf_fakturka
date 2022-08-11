<?php

namespace App\Controller;


use App\Entity\BankAccount;
use App\Form\BankAccountFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/bank_account', name: 'bank_account_')]
class BankAccountController extends AbstractController
{
    public const FORM_TITLE_EDIT = 'Edycja rachunku bankowego';
    public const FORM_TITLE_ADD = 'Dodawanie rachunku bankowego';
    public const MESSAGE_ON_UPDATE = 'Poprawnie zmieniono rachunek bankowy!';
    public const MESSAGE_ON_ADD = 'Poprawnie dodano rachunek bankowy!';
    public const MESSAGE_ON_DELETE = 'rachunek bankowy został usunięty!';
    public const ACCESS_DENIED = 'Brak uprawnień!';




    #[Route('/', name: 'list')]
    public function index(): Response
    {

        $accounts = $this->getUser()->getBankAccounts();


        return $this->render('bank_account/index.html.twig', [
            'accounts' => $accounts,
        ]);
    }

    #[Route('/add', name: 'add')]
    public function addBankAccount(Request $request, EntityManagerInterface $em): Response
    {
        $account = new BankAccount();
        $form = $this->createForm(BankAccountFormType::class, $account);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $owner = $this->getUser();
            $account->setOwner($owner);
            $em->persist($account);
            $em->flush();
            $this->addFlash(
                'success',
                self::MESSAGE_ON_ADD
            );
            return $this->redirectToRoute('bank_account_list');
        }
        return $this->render(
            'bank_account/item.html.twig',
            [
                'bank_account_form' => $form->createView(),
                'form_title' => self::FORM_TITLE_ADD
            ]
        );

        // dd($account);
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function editBankAccount(BankAccount $account, Request $request, EntityManagerInterface $em)
    {
        if (!$this->isValidAccountOwner($account)) {
            return $this->redirectToRoute('app_homepage');
        }

        $form = $this->createForm(BankAccountFormType::class, $account);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($account);
            $em->flush();
            $this->addFlash(
                'success',
                self::MESSAGE_ON_UPDATE
            );
            return $this->redirectToRoute('bank_account_list');
        }
        return $this->render(
            'bank_account/item.html.twig',
            [
                'bank_account_form' => $form->createView(),
                'form_title' => self::FORM_TITLE_EDIT,
            ]
        );
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function deleteBankAccount(BankAccount $account, EntityManagerInterface $em)
    {


        if (!$this->isValidAccountOwner($account)) {
            return $this->redirectToRoute('app_homepage');
        }

        $em->remove($account);
        $em->flush();
        $this->addFlash(
            'warning',
            self::MESSAGE_ON_DELETE
        );
        return $this->redirectToRoute('bank_account_list');
    }


    private function isValidAccountOwner(BankAccount $account)
    {
        if ($account->getOwner() !== $this->getUser()) {
            $this->addFlash(
                'error',
                self::ACCESS_DENIED
            );
            return false;
        }
        return true;
    }
}
