<?php


namespace App\Controller;

use App\Entity\Blog\Post;
use App\Entity\User\User;
use App\Response\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class DefaultController extends AbstractController
{
    public function index(UserPasswordHasherInterface $passwordHasher)
    {
        //echo $passwordHasher->hashPassword(new User(),'1');exit;

        /*
        $repository = $this->getDoctrine()->getRepository(Post::class);
        //$post = $repository->find(1);
        /** @var Post $post * /

        //$post->setContent($post->getContent() . '.');
        //$post->setTitle($post->getTitle() . ',');
        //$post->setCreatedAt(new \DateTimeImmutable());

        $post = new Post();
        $post->setSlug('pre');
        $post->setTitle('PRE');
        $post->setContent('cnt');
        $post->setCreatedAt(new \DateTimeImmutable());
        $this->getDoctrine()->getManager()->persist($post);

        $this->getDoctrine()->getManager()->flush();var_dump($post);

        return new JsonResponse([
            'page' => $post,
        ]);*/
        return $this->render('default/index.html.twig');
    }
}
