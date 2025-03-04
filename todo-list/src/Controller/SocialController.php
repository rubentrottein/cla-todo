<?php
namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class SocialController extends AbstractController
{
    #[Route('/social', name: 'social_dashboard')]
    public function social(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, PostRepository $postRepository): Response
    {
        // Check if user is logged in
        $admin = false;
        if ($this->getUser()) {
            $admin = true;
        }
        
        // Create new post form
        $post = new Post();
        if ($admin) {
            $post->setAuthor($this->getUser());
        }
        
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Handle image upload if present
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                
                try {
                    $imageFile->move(
                        $this->getParameter('posts_directory'),
                        $newFilename
                    );
                    $post->setImageFilename($newFilename);
                } catch (FileException $e) {
                    // Handle exception
                }
            }
            
            $entityManager->persist($post);
            $entityManager->flush();
            
            return $this->redirectToRoute('social_dashboard');
        }
        
        // Get recent posts
        $posts = $postRepository->findRecentPosts();
        
        return $this->render('social/dashboard.html.twig', [
            'post_form' => $form->createView(),
            'posts' => $posts,
            'job' => 'Developpeur, Concepteur, Formateur, JavaScript, PHP, Java, Python, C# ',
            'title' => 'Réseau Tout doux',
            'followers' => '138',
            'following' => '16',
            'quote' => 'Mon seul critère : La paisibilité du travail',
            'admin' => $admin
        ]);
    }
    
    #[Route('/social/like/{id}', name: 'social_like_post')]
    public function likePost(Post $post, EntityManagerInterface $entityManager): Response
    {
        // Simple like functionality
        $post->setLikes($post->getLikes() + 1);
        $entityManager->flush();
        
        return $this->redirectToRoute('social_dashboard');
    }
    
    #[Route('/social/post/{id}', name: 'social_view_post')]
    public function viewPost(Post $post): Response
    {
        return $this->render('social/post.html.twig', [
            'post' => $post,
            'title' => 'Réseau Tout doux - View Post'
        ]);
    }
}