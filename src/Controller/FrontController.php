<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PostRepository;
use App\Repository\CommentRepository;
use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Form\CommentType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class FrontController extends AbstractController
{
    #[Route('/', name: 'app_front')]
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('front/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

    #[Route('/post/{id}', name: 'app_front_show')]
    public function post(PostRepository $postRepository, $id, Request $request, CommentRepository $commentRepository): Response
    {
        $comment= new Comment();
        $form = $this-> createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        $user = $this->getUser();
        $post = $postRepository->findOneBy(['id'=>$id]);
        
        if($form->isSubmitted() && $form->isValid()){
            if ($user) {
                $comment->setCommentBy($user)->setPost($post);
                $commentRepository->save($comment, true);
                return $this->redirectToRoute('app_font_show', ['id'=>$id], Response::HTTP_SEE_OTHER);
            } else {
                // Gérer le cas où l'utilisateur n'est pas connecté ou n'existe pas
            }
            return $this->redirectToRoute('app_font_show', ['id'=>$id], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('front/post.html.twig', [
            'post' => $post,
            'form' => $form
        ]);
    }
}
