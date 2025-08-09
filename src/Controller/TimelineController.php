<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Timeline;
use App\Service\Dogrulama;

final class TimelineController extends AbstractController
{
    #[Route('api/timeline', methods:['GET'])]
    public function index(Request $request,EntityManagerInterface $entityManager): JsonResponse
    {
        $timelines = $entityManager->getRepository(Timeline::class)->findAll();
        
        $data = [];
        foreach ($timelines as $timeline) {
            $data[] = $timeline->getAll();
        }

        if(count($data) > 0){
            return new JsonResponse(['message' => 'Timeline data found','data'=>$data,'status'=>200]);
        }else{
            return new JsonResponse(['message' => 'Timeline data not found','status'=>404]);
        }
    }

    #[Route('api/timeline/create', methods:['POST'])]
    public function create(Request $request,EntityManagerInterface $entityManager): JsonResponse
    {
        $isEmpty = Dogrulama::isEmpty($request->request->all());
        if(count($isEmpty) > 0){
            return new JsonResponse(['message' => 'Validation errors','errors'=>$isEmpty,'status'=>400]);
        }

        $title = $request->request->get('title');
        $company = $request->request->get('company');
        $start = $request->request->get('start');
        $end = $request->request->get('end');
        $location = $request->request->get('location');
        $text = $request->request->get('text');

       
            $timeline = new Timeline();
            $timeline->setTitle($title);
            $timeline->setCompany($company);
            $timeline->setStart($start);
            $timeline->setEnd($end);
            $timeline->setLocation($location);
            $timeline->setText($text);
            $entityManager->persist($timeline);
            $entityManager->flush();
            if($timeline){
                return new JsonResponse(['message' => 'Timeline data created successfully','status'=>200]);
            }else{
                return new JsonResponse(['message' => 'Timeline data creation failed','status'=>400]);
            }
        
    }


    #[Route('api/timeline/update',methods:['POST'])]
    public function update(Request $request,EntityManagerInterface $entityManager): JsonResponse
    {
        $isEmpty = Dogrulama::isEmpty($request->request->all());
        if(count($isEmpty) > 0){
            return new JsonResponse(['message' => 'Validation errors','errors'=>$isEmpty,'status'=>400]);
        }

        $id = $request->request->get('id');
        $title = $request->request->get('title');
        $company = $request->request->get('company');
        $start = $request->request->get('start');
        $end = $request->request->get('end');
        $location = $request->request->get('location');
        $text = $request->request->get('text');

        $timeline = $entityManager->getRepository(Timeline::class)->find($id);
        if(!$timeline){
            return new JsonResponse(['message' => 'Timeline data not found','status'=>404]);
        }

        $timeline->setTitle($title);
        $timeline->setCompany($company);
        $timeline->setStart($start);
        $timeline->setEnd($end);
        $timeline->setLocation($location);
        $timeline->setText($text);
        $entityManager->persist($timeline);
        $entityManager->flush();
        if($timeline){
            return new JsonResponse(['message' => 'Timeline data updated successfully','status'=>200]);
        }else{
            return new JsonResponse(['message' => 'Timeline data update failed','status'=>400]);
        }
    }

    #[Route('api/timeline/delete',methods:['POST'])]
    public function delete(Request $request,EntityManagerInterface $entityManager): JsonResponse
    {
        $id = $request->request->get('id');
        $timeline = $entityManager->getRepository(Timeline::class)->find($id);
        if(!$timeline){
            return new JsonResponse(['message' => 'Timeline data not found','status'=>404]);
        }
        $entityManager->remove($timeline);
        $entityManager->flush();
        if($timeline){
            return new JsonResponse(['message' => 'Timeline data deleted successfully','status'=>200]);

        }else{
            return new JsonResponse(['message' => 'Timeline data deletion failed','status'=>400]);
        }
    }
}
