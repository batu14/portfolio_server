<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Projects;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Dogrulama;
use Symfony\Component\HttpFoundation\Request;
use App\Service\FileUploader;
final class ProjectsController extends AbstractController
{
    #[Route('api/projects',methods:['GET'])]
    public function index(EntityManagerInterface $entityManager): JsonResponse
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


    #[Route('api/projects/create',methods:['POST'])]
    public function create(Request $request,EntityManagerInterface $entityManager): JsonResponse
    {
        $isEmpty = Dogrulama::isEmpty($request->request->all());
        if(count($isEmpty) > 0){
            return new JsonResponse(['message' => 'Validation errors','errors'=>$isEmpty,'status'=>400]);
        }


        $title = $request->request->get('title');
        $description = $request->request->get('description');
        $clientName = $request->request->get('clientName');
        $timeline = $request->request->get('timeline');
        $publish_year = $request->request->get('publish_year');
        $github = $request->request->get('github');
        $website = $request->request->get('website');
        $images = $request->files->get('images');
        $technologies = $request->request->get('technologies');

        
        $imagePaths = [];
        foreach($images as $image){
            if($image){
                
                $fileUploader = new FileUploader($this->getParameter('kernel.project_dir') . '/public/uploads/projects', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                $image = $fileUploader->upload($image);
                $imagePaths[] = $image;
                
            }
        }
        

        $project = new Projects();
        $project->setTitle($title);
        $project->setDescription($description);
        $project->setClientName($clientName);
        $project->setTimeline($timeline);
        $project->setPublishYear($publish_year);
        $project->setGithub($github);
        $project->setDemo($website);
        $project->setImages(implode(',',$imagePaths));
        $project->setTecs($technologies);
        $project->setIsActive(true);
        $entityManager->persist($project);
        $entityManager->flush();
        if($project){
            return new JsonResponse(['message' => 'Projects data created','status'=>200]);
        }else{
            return new JsonResponse(['message' => 'Projects data not created','status'=>400]);
        }


   
    }


    #[Route('api/projects/{id}',methods:['GET'])]
    public function show(int $id,EntityManagerInterface $entityManager): JsonResponse
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


    #[Route('api/projects/delete-image',methods:['POST'])]
    public function deleteImage(Request $request,EntityManagerInterface $entityManager): JsonResponse
    {
        $image = $request->request->get('image');
        $id = $request->request->get('id');

        $project = $entityManager->getRepository(Projects::class)->find($id);


        if($project){
            $deletePath = $this->getParameter('kernel.project_dir') . '/public/uploads/projects/' . $image;
            if(file_exists($deletePath)){

                $images = explode(',',str_replace('[', '', str_replace(']', '', $project->getImages())));
                $images = array_filter($images,function($item) use ($image){
                    return $item !== $image;
                });
                $project->setImages(implode(',',$images));
                $entityManager->persist($project);
                $entityManager->flush();
                
                
                return new JsonResponse(['message' => 'Image deleted','status'=>200,'data'=>$images]);
            }else{
                return new JsonResponse(['message' => 'Image not found','status'=>404]);
            }
        }else{
            return new JsonResponse(['message' => 'Project not found','status'=>404]);
        }
       
        return new JsonResponse(['message' => 'Image deleted','status'=>200,'data'=>$project->getImages()]);
    }


    #[Route('api/projects/update',methods:['POST'])]
    public function update(Request $request,EntityManagerInterface $entityManager): JsonResponse
    {
        $id = $request->request->get('id');
        $project = $entityManager->getRepository(Projects::class)->find($id);

       if($project){
            $title = $request->request->get('title');
            $description = $request->request->get('description');
            $clientName = $request->request->get('clientName');
            $timeline = $request->request->get('timeline');
            $publish_year = $request->request->get('publish_year');
            $github = $request->request->get('github');
            $demo = $request->request->get('demo');
            $technologies = $request->request->get('technologies');

            if($request->files->get('images')){
                $images = $request->files->get('images');
                $imagePaths = explode(',',$project->getImages());
                foreach($images as $image){
                    $fileUploader = new FileUploader($this->getParameter('kernel.project_dir') . '/public/uploads/projects', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                    $image = $fileUploader->upload($image);
                    $imagePaths[] = $image;
                }
                
                $project->setImages(implode(',',$imagePaths));
            }

            $project->setTitle($title);
            $project->setDescription($description);
            $project->setClientName($clientName);
            $project->setTimeline($timeline);
            $project->setPublishYear($publish_year);
            $project->setGithub($github);
            $project->setDemo($demo);
            $project->setTecs(json_encode($technologies));
            $entityManager->persist($project);
            $entityManager->flush();
            if($project){
                return new JsonResponse(['message' => 'Project updated','status'=>200]);
            }else{
                return new JsonResponse(['message' => 'Project not updated','status'=>400]);
            }
        }else{
            return new JsonResponse(['message' => 'Project not found','status'=>404]);
        }
        
    }


    #[Route('api/projects/status',methods:['POST'])]
    public function status(Request $request,EntityManagerInterface $entityManager): JsonResponse
    {
        $id = $request->request->get('id');
        $status = $request->request->get('status');
        $project = $entityManager->getRepository(Projects::class)->find($id);
        if($project){
            $project->setIsActive($status);
            $entityManager->persist($project);
            $entityManager->flush();
            return new JsonResponse(['message' => 'Project status updated','status'=>200]);
        }else{
            return new JsonResponse(['message' => 'Project not found','status'=>404]);
        }
    }

    #[Route('api/projects/delete',methods:['POST'])]
    public function delete(Request $request,EntityManagerInterface $entityManager): JsonResponse
    {
        $id = $request->request->get('id');
        $project = $entityManager->getRepository(Projects::class)->find($id);
        
        $images = explode(',',$project->getImages());
        
        foreach($images as $image){
            $deletePath = $this->getParameter('kernel.project_dir') . '/public/uploads/projects/' . $image;
            if(file_exists($deletePath)){
                unlink($deletePath);
            }
        }
        $entityManager->remove($project);
        $entityManager->flush();
        return new JsonResponse(['message' => 'Project deleted','status'=>200]);

    }

}

