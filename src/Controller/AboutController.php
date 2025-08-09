<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\About;
use App\Service\Dogrulama;

final class AboutController extends AbstractController
{
    #[Route('api/about', name: 'app_about',methods:['GET'])]
    public function index(Request $request,EntityManagerInterface $entityManager): JsonResponse
    {
      $about = $entityManager->getRepository(About::class)->findAll();
      if(count($about) > 0){
        return new JsonResponse(
        [
            'message' => 'About data found',
            'data'=>$about[0]->getAll(),
            'status'=>200
        ]
    );
      }else{
        return new JsonResponse(['message' => 'About data not found','status'=>404]);
      }
    }


    #[Route('api/about/create', name: 'app_about_create',methods:['POST'])]
    public function create(Request $request,EntityManagerInterface $entityManager): JsonResponse
    {

        $data = $entityManager->getRepository(About::class)->findAll();
        $isEmpty = Dogrulama::isEmpty($request->request->all());
        if(count($isEmpty) > 0){
            return new JsonResponse(['message' => 'Validation errors','errors'=>$isEmpty,'status'=>400]);
        }

        if(count($data) > 0){
            $about = $data[0];
            $about->setTitle($request->request->get('title'));
            $about->setText($request->request->get('text'));
            $entityManager->persist($about);
            $entityManager->flush();
            if($about){
                return new JsonResponse(['message' => 'About data updated successfully','status'=>200]);
            }else{
                return new JsonResponse(['message' => 'About data update failed','status'=>400]);
            }
        }else{

        $title = $request->request->get('title');
        $text = $request->request->get('text');

        $about = new About();
        $about->setTitle($title);
        $about->setText($text);
        $entityManager->persist($about);
        $entityManager->flush();
        if($about){
            return new JsonResponse(['message' => 'About data created successfully','status'=>200]);
        }else{
            return new JsonResponse(['message' => 'About data creation failed','status'=>400]);
        }
        }

    }
}
