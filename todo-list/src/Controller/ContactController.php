<?php
namespace App\Controller;

use App\Entity\ContactMessage;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact_form')]
    public function contact(Request $request, EntityManagerInterface $entityManager): Response
    {
        $contactMessage = new ContactMessage();
        $form = $this->createForm(ContactType::class, $contactMessage);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($contactMessage);
            $entityManager->flush();

            return $this->redirectToRoute('contact_messages'); // Redirige vers la liste des messages
        }

        return $this->render('contact/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/contact/messages', name: 'contact_messages')]
    public function messages(EntityManagerInterface $entityManager): Response
    {
        $messages = $entityManager->getRepository(ContactMessage::class)->findBy([], ['createdAt' => 'DESC']);

        return $this->render('contact/messages.html.twig', [
            'messages' => $messages,
        ]);
    }
}