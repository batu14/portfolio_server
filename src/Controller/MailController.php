<?php

namespace App\Controller;

use App\Entity\Mail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Service\Dogrulama;

final class MailController extends AbstractController
{
    #[Route('api/mail', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): JsonResponse
    {
        $mails = $entityManager->getRepository(Mail::class)->findAll();
        $mailsArray = array_map(function($mail) {
            return [
                'id' => $mail->getId(),
                'from' => $mail->getName(),
                'email' => $mail->getEmail(),
                'subject' => $mail->getSubject(),
                'message' => $mail->getMessage(),
                'date' => $mail->getDate()->format('Y-m-d'),
                'read' => $mail->isRead(),
                'starred' => $mail->isStarred(),
                'archived' => $mail->isArchived(),
                'deleted' => $mail->isDeleted()
            ];
        }, $mails);
        
        return $this->json(['data' => $mailsArray, 'status' => 200]);
    }

    #[Route('api/mail/create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
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

    #[Route('api/mail/read', methods: ['POST'])]
    public function read(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $id = $request->request->get('id');
        $mail = $entityManager->getRepository(Mail::class)->find($id);
        $mail->setIsRead(true);
        $entityManager->flush();
        return $this->json(['message' => 'Mail read','status'=>200]);
    }

    #[Route('api/mail/star', methods: ['POST'])]
    public function star(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $id = $request->request->get('id');
        $mail = $entityManager->getRepository(Mail::class)->find($id);
        $mail->setIsStarred(!$mail->isStarred());
        $entityManager->flush();
        return $this->json(['message' => 'Mail changed','status'=>200]);
    }
}
