<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SkillsRepository;
use App\Entity\Skills;
use App\Service\FileUploader;
use App\Service\Dogrulama;
use Symfony\Component\Validator\Constraints\NotBlank;

final class SkillsController extends AbstractController
{
    #[Route('api/skills', name: 'app_skills')]
    public function index(EntityManagerInterface $entityManager): JsonResponse
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

    #[Route('api/skills/create', name: 'app_skills_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
      

        $isEmpty = Dogrulama::isEmpty($request->request->all());
        if(count($isEmpty) > 0){
            return new JsonResponse(['message' => 'Validation errors', 'status' => 400,'errors'=>$isEmpty]);
        }
        
       $title = $request->request->get('title');
       $description = $request->request->get('description');
       $type = $request->request->get('type');


       $data = $entityManager->getRepository(Skills::class)->findBy(['type'=>$type]);

       if($data){
        return new JsonResponse(['message' => 'Skill already exists'],409);
       }


       $skill = new Skills();
       $skill->setTitle($title);
       $skill->setDescription($description);
       $skill->setType($type);
       $entityManager->persist($skill);
       $entityManager->flush();


       if($skill){
        return new JsonResponse(['message' => 'Skill created successfully', 'status' => 200]);
       }else{
        return new JsonResponse(['message' => 'Skill not created', 'status' => 400]);
       }


    }


    #[Route('api/skills/delete/{id}', name: 'app_skills_delete', methods: ['DELETE'])]
    public function delete(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $id = $request->attributes->get('id');
        $data = $entityManager->getRepository(Skills::class)->find($id);
        $entityManager->remove($data);
        $entityManager->flush();
        return new JsonResponse(['message' => 'Skill deleted successfully'],200);
    }

    #[Route('api/skills/update', name: 'app_skills_update', methods: ['POST'])]
    public function update(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $id = $request->request->get('id');
        $title = $request->request->get('title');
        $description = $request->request->get('description');
        $type = $request->request->get('type');

        $data = $entityManager->getRepository(Skills::class)->find($id);
        if(!$data){
            return new JsonResponse(['message' => 'Skill not found'],404);
        }

        $isEmpty = Dogrulama::isEmpty($request->request->all());
        if(count($isEmpty) > 0){
            return new JsonResponse(['message' => 'Validation errors','errors'=>$isEmpty],400);
        }


        $data->setTitle($title);
        $data->setDescription($description);
        $data->setType($type);
        $entityManager->flush();
        if($data){
            return new JsonResponse(['message' => 'Skill updated successfully'],200);
        }else{
            return new JsonResponse(['message' => 'Skill not updated'],400);
        }
    }
}
