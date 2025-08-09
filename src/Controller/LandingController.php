<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\LandingRepository;
use App\Entity\Landing;
use App\Service\FileUploader;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


final class LandingController extends AbstractController
{


    

    #[Route('/', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $data = [
            'message' => 'api is working',
            'status' => 200,
            'time' => date('Y-m-d H:i:s'),
            'version' => '1.0.0',
            'author' => 'Batuhan ÇİFTÇİ',
            'email' => 'batuhancifci2000@hotmail.com',
            'phone' => '+90 5443123436',
            'address' => 'Antalya, Türkiye',
            'website' => 'https://batuhanciftci.net',
        ];
        return new JsonResponse($data);
    }
    


    #[Route('api/landing', name: 'app_landing')]
    public function getData(Request $request,EntityManagerInterface $entityManager): JsonResponse
    {
        $data = $entityManager->getRepository(Landing::class)->findAll([]);

        if (!$data) {
            return new JsonResponse(['message' => 'Landing not found','status'=>404]);
        }

        return new JsonResponse(['message' => 'Landing found','status'=>200,'data'=>[
            'title'=>$data[0]->getTitle(),
            'subtitle'=>$data[0]->getSubtitle(),
            'text'=>$data[0]->getText(),
            'image'=>$data[0]->getImage()
        ]]);

        return $response;
    }


    #[Route('api/landing/update', methods: ['POST'])]
    public function update(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = $entityManager->getRepository(Landing::class)->findAll();

        if (!$data) {

            $fileUploader = new FileUploader($this->getParameter('kernel.project_dir') . '/public/uploads/landing', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
            $image = $request->files->get('image');
            $upload = $fileUploader->upload($image);


            $landing = new Landing();
            $landing->setTitle($request->request->get('title'));
            $landing->setSubtitle($request->request->get('subtitle'));
            $landing->setText($request->request->get('text'));
            $landing->setImage($upload);
            $entityManager->persist($landing);
            $entityManager->flush();
            if ($landing) {
                return new JsonResponse(['message' => 'Landing created successfully','status'=>200]);
            } else {
                return new JsonResponse(['message' => 'Landing creation failed','status'=>400]);
            }
        }else{

            if($request->files->get('image')){
               
                $fileUploader = new FileUploader($this->getParameter('kernel.project_dir') . '/public/uploads/landing', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                $image = $request->files->get('image');      
                $upload = $fileUploader->upload($image);


                if($upload){
                    $oldImage = $data[0]->getImage();
                    if($oldImage){
                        unlink($this->getParameter('kernel.project_dir') . '/public/uploads/landing/' . $oldImage);
                    }else{
                        return new JsonResponse(['message' => 'Landing update failed','status'=>400]);
                    }
                    $landing = $data[0];
                    $landing->setTitle($request->request->get('title'));
                    $landing->setSubtitle($request->request->get('subtitle'));
                    $landing->setText($request->request->get('text'));
                    $landing->setImage($upload);
                    $entityManager->persist($landing);
                    $entityManager->flush();
                    return new JsonResponse(['message' => 'Landing updated successfully','status'=>200]);
                }else{
                    return new JsonResponse(['message' => 'Landing update failed','status'=>400]);
                }
            }else{
                $landing = $data[0];
                $landing->setTitle($request->request->get('title'));
                $landing->setSubtitle($request->request->get('subtitle'));
                $landing->setText($request->request->get('text'));
                $entityManager->persist($landing);
                $entityManager->flush();
                return new JsonResponse(['message' => 'Landing updated successfully','status'=>200]);
            }

           
        }

        
    }
}
