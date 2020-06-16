<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends AbstractController
{
    public function GetUsers()
    {
        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();
        if (!$users){
            return new Response("Пользователей нет");
        }
        $arrayCollection = array();

        foreach($users as $user) {
            $arrayCollection[] = array(
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'phone' => $user->getPhoneNumber()
            );
        }

        return new JsonResponse($arrayCollection);
    }

    public function GetUsero($id)
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($id);
        if (!$user){
            return new Response('Пользователь не найден');
        }
        $userJSON = [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'phone' => $user->getPhoneNumber()
        ];
        return new JsonResponse($userJSON);
    }

    public function PostUser(Request $request):Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = new User();
        $user->setName($request->request->get('name'));
        $user->setEmail($request->request->get('email'));
        $user->setPhoneNumber($request->request->get('phoneNumber'));
        $user->setCreatedTime(new \DateTime('now'));
        $entityManager->persist($user);
        $entityManager->flush();
        return new Response('Пользователь был создан с идентификатором '.$user->getId());
    }

    public function PutUser($id, Request $request):Response{
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($id);
        if(!$user){
            $user = new User();
            $user->setName($request->request->get('name'));
            $user->setEmail($request->request->get('email'));
            $user->setPhoneNumber($request->request->get('phoneNumber'));
            $user->setCreatedTime(new \DateTime('now'));
            $entityManager->persist($user);
            $entityManager->flush();
            return new Response('Создан новый пользователь с идентификатором: '.$user->getId());
        }else{
            $user->setName($request->request->get('name'));
            $user->setEmail($request->request->get('email'));
            $user->setPhoneNumber($request->request->get('phoneNumber'));
            $user->setUpdatedTime(new \DateTime('now'));
            $entityManager->persist($user);
            $entityManager->flush();
            return new Response('Информация изменена о пользователе с идентификатором: '.$user->getId());
        }
    }

    public function DeleteUser($id){
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);
        if (!$user) return new Response('Пользователь не найден');
        $entityManager->remove($user);
        $entityManager->flush();
        return new Response('Пользователь с идентификатором '.$id.' был удален');
    }
}
