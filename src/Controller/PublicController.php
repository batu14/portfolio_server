<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Service\Dogrulama;

// Entities
use App\Entity\Landing;
use App\Entity\Projects;
use App\Entity\Skills;
use App\Entity\Timeline;
use App\Entity\About;
use App\Entity\Mail;

final class PublicController extends AbstractController
{
    #[Route('/public', name: 'app_public')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to my portfolio api',
            'author'=>'Batuhan ÇİFTÇİ',
            'version'=>'1.0.0',
            'status'=>200
        ]);
    }


    #[Route('/hero', name: 'app_hero')]
    public function hero(EntityManagerInterface $entityManager): JsonResponse
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

    #[Route('/projects', name: 'app_projects')]
    public function projects(EntityManagerInterface $entityManager): JsonResponse
    {
        $projects = $entityManager->getRepository(Projects::class)->findAll();
        $projects = array_map(function($item){
            return [
                'id'=>$item->getId(),
                'title'=>$item->getTitle(),
                'description'=>$item->getDescription(),
                'clientName'=>$item->getClientName(),
                'timeline'=>$item->getTimeline(),
                'publishYear'=>$item->getPublishYear(),
                'github'=>$item->getGithub(),
                'demo'=>$item->getDemo(),
                'images'=>$item->getImages(),
                'tecs'=>$item->getTecs(),
                'is_active'=>$item->isActive()
            ];
        },$projects);
        if(count($projects) > 0){
            return new JsonResponse(['message' => 'Projects data found','data'=>$projects,'status'=>200]);
        }else{
            return new JsonResponse(['message' => 'Projects data not found','status'=>404]);
        }
    }


    #[Route('/about', name: 'app_about')]
    public function about(EntityManagerInterface $entityManager): JsonResponse
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


    #[Route('/skills', name: 'get_skills',methods:['GET'])]
    public function skills(EntityManagerInterface $entityManager): JsonResponse
    {
        $data = $entityManager->getRepository(Skills::class)->findBy([],['id'=>'DESC']);

        $data = array_map(function($item){
            return [
                'id'=>$item->getId(),
                'title'=>$item->getTitle(),
                'description'=>$item->getDescription(),
                'type'=>$item->getType()
            ];
        },$data);
        return new JsonResponse(['message' => 'Skills fetched successfully','data'=>$data],200);
    }


    #[Route('/timeline', name: 'get_timeline',methods:['GET'])]
    public function timeline(EntityManagerInterface $entityManager): JsonResponse
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


    #[Route('/project/{id}', name: 'get_project',methods:['GET'])]
    public function project(int $id,EntityManagerInterface $entityManager): JsonResponse
    {
        $project = $entityManager->getRepository(Projects::class)->find($id);
        
        if($project){
            return new JsonResponse(['message' => 'Projects data found','data'=>[
                'id'=>$project->getId(),
                'title'=>$project->getTitle(),
                'description'=>$project->getDescription(),
                'clientName'=>$project->getClientName(),
                'timeline'=>$project->getTimeline(),
                'publishYear'=>$project->getPublishYear(),
                'github'=>$project->getGithub(),
                'demo'=>$project->getDemo(),
                'images'=>$project->getImages(),
                'tecs'=>$project->getTecs(),
                'is_active'=>$project->isActive()
            ],'status'=>200]);
        }else{
            return new JsonResponse(['message' => 'Projects data not found','status'=>404]);
        }
     
    }


        #[Route('/mail', name: 'get_mail',methods:['POST'])]
    public function mail(Request $request,EntityManagerInterface $entityManager): JsonResponse
    {
        
        $errors = Dogrulama::isEmpty([
            'name' => $request->request->get('name'),
            'email' => $request->request->get('email'),
            'subject' => $request->request->get('subject'),
            'message' => $request->request->get('message'),
        ]);
        if(count($errors) > 0){
            return $this->json(['message' => $errors,'status'=>400]);
        }

        $isMailValid = Dogrulama::isMailValid($request->request->get('email'));
        if(!$isMailValid){
            return $this->json(['message' => 'Geçersiz email adresi','status'=>400]);
        }

        $name = $request->request->get('name');
        $email = $request->request->get('email');
        $subject = $request->request->get('subject');
        $message = $request->request->get('message');
        $date = new \DateTime();
        $isRead = false;
        $isStarred = false;
        $isArchived = false;
        $isDeleted = false;

        $mail = new Mail();
        $mail->setName($name);
        $mail->setEmail($email);
        $mail->setSubject($subject);
        $mail->setMessage($message);
        $mail->setDate($date);
        $mail->setIsRead($isRead);
        $mail->setIsStarred($isStarred);
        $mail->setIsArchived($isArchived);
        $mail->setIsDeleted($isDeleted);
        $entityManager->persist($mail);
        $entityManager->flush();

        if($mail){
            return $this->json(['message' => 'Mail created','status'=>200]);
        }else{
            return $this->json(['message' => 'Mail not created','status'=>400]);
        }
    }
    
}
