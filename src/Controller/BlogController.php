<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


use Symfony\Component\HttpFoundation\Request;

class BlogController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('blog/home.html.twig', [
            'controller_name' => 'BlogController',
        ]);
    }

    /**
     * @Route("/blog", name="blog")
     */
    public function articles()
    {
        $repository = $this->getDoctrine()->getRepository(Article::class);
        $articles = $repository -> findAll();
        return $this->render('blog/articles.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/blog/new", name="blog-new")
     * @Route("/blog/{id}/edit", name="blog-edit")
     */
    public function new(Article $article = null, Request $request)
    {

        $labelButton = "Modifier";
        if(!$article){
            $labelButton = "Créer un article";
            $article = new Article();
        }

        $form = $this->createFormBuilder($article)
                     ->add("title",TextType::class)
                     ->add("content")
                     ->add("image")
                     ->add("category",EntityType::class, [
                        'class' => Category::class,
                        'choice_label' => 'title'
                     ])
                     ->add('save', SubmitType::class)
                     ->getForm();

        $form->handleRequest($request);

        if($form -> isSubmitted() && $form -> isValid()){
          $article->setCreatedAt(new \DateTime());
          $entityManager = $this->getDoctrine()->getManager();
          $entityManager->persist($article);
          $entityManager->flush();

          return $this->redirectToRoute('blog-show',['id' => $article->getId()]);
        }

        return $this->render('blog/new.html.twig', [
            'form' => $form->createView(),
            'labelButton'=> $labelButton
        ]);
    }

    /**
     * @Route("/blog/{id}", name="blog-show")
     */
    public function show($id, Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Article::class);
        $article = $repository -> find($id);

        $comment = new Comment();
        $form = $this -> createFormBuilder($comment)
                      -> add("title")
                      ->add("author")
                      ->add("content")
                      ->add("save",SubmitType::class,['label'=> 'Ajouter un commentaire'])
                      ->getForm();
        $form->handleRequest($request);

        if($form -> isSubmitted() && $form -> isValid()){
            $comment->setCreatedAt(new \DateTime());
            $comment -> setArticle($article);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();
            return $this->redirectToRoute('blog-show',['id' => $article->getId()]);
        }
        
        return $this->render('blog/show.html.twig', [
            'article' => $article,
            'form' => $form->createView()
        ]);
    }
}
