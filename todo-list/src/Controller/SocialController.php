<?php
namespace App\Controller;

use App\Entity\ContactMessage;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SocialController extends AbstractController
{
    #[Route('/social', name: 'social_dashboard')]
    public function social(Request $request, EntityManagerInterface $entityManager): Response
    {
        
        return $this->render('social/dashboard.html.twig', [
            'messages' => 'Hello World',
            'title' => 'Réseau Tout doux'
        ]);
    }
}