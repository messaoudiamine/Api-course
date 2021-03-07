<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;    
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/blog")
 * 
 */
class BlogController extends AbstractController {
 

    const POSTS = [
        ['id' => 1, 'title' => "Formation angular", 'slug' => 'formation-angular'],
        ['id' => 2, 'title' => "Formation react", 'slug' => 'formation-react'],
        ['id' => 3, 'title' => "Formation laravel", 'slug' => 'formation-laravel'],
        ['id' => 4, 'title' => "Formation symfony", 'slug' => 'formation-symfony']
    ];


    /**
     * @Route("/add", name="add-post", methods={"POST"})
     *
     * @param Request $request
     * 
     */
    public function add(Request $request){

        $serializer = $this->get('serializer');
        $post = $serializer->deserialize($request->getContent(), Post::class, 'json');
        $em = $this->getDoctrine()->getManager();
        $em->persist($post);
        $em->flush();

        return $this->json($post);

    }
    /**
     * @Route("/{page}",defaults={"page": 4},name="get-all-posts", methods={"GET"})
     * 
    */
    public function index($page, Request $request)
    {

        $repository = $this->getDoctrine()->getRepository(Post::class);
        $posts = $repository->findAll();

        return $this->json([
            "page" => $page,
            "limite" => $request->get('limite',34),
            "data" => array_map(function(Post $post){
                return [
                    'title' => $post->getTitle(), 
                    'content' => $post->getContent(), 
                    'published' => $post->getPublished(), 
                    'author' => $post->getAuthor(), 
                    'slug' => $this->generateUrl('get-one-post-by-id', ["id" => $post->getId()]) 
                ];
            },$posts)
        ]); 
    }

    /**
     * @Route("/post/{id}",requirements={"id":"\d+"},name="get-one-post-by-id", methods={"GET"})
     * @ParamConverter("post", class="App:Post")
     */
    public function postById($post){

        // $repository = $this->getDoctrine()->getRepository(Post::class);
        // $post = $repository->find($id);

        return $this->json( $post );
    }

    /**
     * @Route("/post/{slug}",name="get-one-post-by-slug", methods={"GET"})
     * @ParamConverter("post", class="App:Post", options={"mapping": {"slug":"slug"}})
     */
    public function postBySlug($post){

        // $repository = $this->getDoctrine()->getRepository(Post::class);
        // $post = $repository->findOneBy(['slug' => $slug ]);

        return $this->json( $post );
    }

    /**
     * @Route("/post/{id}", name="delete-post", methods={"DELETE"})
     * 
    */
    public function destroy(Post $post) {

        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();
        return $this->json(null,204);
    }

}