<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Category;
use App\Entity\Plan;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdsController extends AbstractController
{
    public function GetAds()
    {
        $ads = $this->getDoctrine()
            ->getRepository(Ad::class)
            ->findAll();
        if (!$ads){
            return new Response("Объявлений нет");
        }
        $arrayCollection = array();

        foreach($ads as $ad) {
            $arrayCollection[] = array(
                'id' => $ad->getId(),
                'name' => $ad->getName(),
                'description' => $ad->getDescription(),
                'price' => $ad->getPrice(),
                'locality' => $ad->getLocality(),
                'isActive' => $ad->getIsActive(),
                'user' => $ad->getUser(),
                'category' => $ad->getCategory(),
                'plan' => $ad->getPlan(),
                'created_at' => $ad->getCreatedTime(),
            );
        }

        return new JsonResponse($arrayCollection);
    }

    public function GetAdsOfUser($userId)
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($userId);
        if(!$user) return new Response('Пользователь с идентификатором '.$userId.' не найден');
        $ads = $user->getAds();
        if(count($ads)===0) return new Response('Пользователь с идентификатором '.$userId.' не опубликовал ни одного объявления');
        $arrayCollection = array();

        foreach($ads as $ad) {
            $arrayCollection[] = array(
                'id' => $ad->getId(),
                'name' => $ad->getName(),
                'description' => $ad->getDescription(),
                'price' => $ad->getPrice(),
                'locality' => $ad->getLocality(),
                'isActive' => $ad->getIsActive(),
                'user' => $userId,
                'category' => $ad->getCategory(),
                'plan' => $ad->getPlan(),
                'created_at' => $ad->getCreatedTime(),
            );
        }

        return new JsonResponse($arrayCollection);
    }

    public function GetAdsOfCategory($categoryId)
    {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->find($categoryId);
        if(!$category) return new Response('Категория с идентификатором '.$categoryId.' не найдена');
        $ads = $category->getAds();
        if(count($ads)===0) return new Response('Категория с идентификатором '.$categoryId.' не содержит ни одного объявления');
        $arrayCollection = array();

        foreach($ads as $ad) {
            $arrayCollection[] = array(
                'id' => $ad->getId(),
                'name' => $ad->getName(),
                'description' => $ad->getDescription(),
                'price' => $ad->getPrice(),
                'locality' => $ad->getLocality(),
                'isActive' => $ad->getIsActive(),
                'user' => $ad->getUser(),
                'category' => $categoryId,
                'plan' => $ad->getPlan(),
                'created_at' => $ad->getCreatedTime(),
            );
        }

        return new JsonResponse($arrayCollection);
    }



    public function GetAd($id)
    {
        $ad = $this->getDoctrine()
            ->getRepository(Ad::class)
            ->find($id);
        if (!$ad){
            return new Response('Объявление не найдено');
        }
        $fileJSON = [
            'id' => $ad->getId(),
            'name' => $ad->getName(),
            'description' => $ad->getDescription(),
            'price' => $ad->getPrice(),
            'locality' => $ad->getLocality(),
            'isActive' => $ad->getIsActive(),
            'user' => $ad->getUser(),
            'category' => $ad->getCategory(),
            'plan' => $ad->getPlan(),
            'created_at' => $ad->getCreatedTime(),
        ];
        return new JsonResponse($fileJSON);
    }

    public function PostAd(Request $request):Response{
        $entityManager = $this->getDoctrine()->getManager();
        $ad = new Ad();
        $userId = $request->request->get('userId');
        $planId = $request->request->get('planId');
        $categoryId = $request->request->get('categoryId');
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($userId);
        if(!$user){
            return new Response('Пользователь не найден');
        }
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->find($categoryId);
        if(!$category){
            return new Response('Категория не найдена');
        }
        $plan = $this->getDoctrine()
            ->getRepository(Plan::class)
            ->find($planId);
        if(!$plan){
            return new Response('План не найден');
        }

        $ad->setName($request->request->get('name'));
        $ad->setDescription($request->request->get('description'));
        $ad->setPrice($request->request->get('price'));
        $ad->setIsActive($request->request->get('isActive'));
        $ad->setLocality($request->request->get('locality'));
        $ad->setCreatedTime(new \DateTime('now'));
        $ad->setUser($user);
        $ad->setCategory($category);
        $ad->setPlan($plan);

        $entityManager->persist($ad);
        $entityManager->flush();
        return new Response('Объявление с идентификатором '.$ad->getId().' было успешно создано');
    }

    public function PutAd($id, Request $request):Response{
        $entityManager = $this->getDoctrine()->getManager();
        $ad = $this->getDoctrine()
            ->getRepository(Ad::class)
            ->find($id);
        if (!$ad){
            $ad = new Ad();
            $userId = $request->request->get('userId');
            $planId = $request->request->get('planId');
            $categoryId = $request->request->get('categoryId');
            $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->find($userId);
            if(!$user){
                return new Response('Пользователь не найден');
            }
            $category = $this->getDoctrine()
                ->getRepository(Category::class)
                ->find($categoryId);
            if(!$category){
                return new Response('Категория не найдена');
            }
            $plan = $this->getDoctrine()
                ->getRepository(Plan::class)
                ->find($planId);
            if(!$plan){
                return new Response('План не найден');
            }

            $ad->setName($request->request->get('name'));
            $ad->setDescription($request->request->get('description'));
            $ad->setPrice($request->request->get('price'));
            $ad->setIsActive($request->request->get('isActive'));
            $ad->setLocality($request->request->get('locality'));
            $ad->setCreatedTime(new \DateTime('now'));
            $ad->setUser($user);
            $ad->setCategory($category);
            $ad->setPlan($plan);

            $entityManager->persist($ad);
            $entityManager->flush();
            return new Response('Объявление с идентификатором '.$ad->getId().' было успешно создано');
        }
        else{
            $userId = $request->request->get('userId');
            $planId = $request->request->get('planId');
            $categoryId = $request->request->get('categoryId');
            $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->find($userId);
            if(!$user){
                return new Response('Пользователь не найден');
            }
            $category = $this->getDoctrine()
                ->getRepository(Category::class)
                ->find($categoryId);
            if(!$category){
                return new Response('Категория не найдена');
            }
            $plan = $this->getDoctrine()
                ->getRepository(Plan::class)
                ->find($planId);
            if(!$plan){
                return new Response('План не найден');
            }

            $ad->setName($request->request->get('name'));
            $ad->setDescription($request->request->get('description'));
            $ad->setPrice($request->request->get('price'));
            $ad->setIsActive($request->request->get('isActive'));
            $ad->setLocality($request->request->get('locality'));
            $ad->setCreatedTime(new \DateTime('now'));
            $ad->setUser($user);
            $ad->setCategory($category);
            $ad->setPlan($plan);

            $entityManager->persist($ad);
            $entityManager->flush();
            return new Response('Объявление с идентификатором '.$ad->getId().' было успешно изменено');
        }
    }

    public function DeleteAd($id){
        $entityManager = $this->getDoctrine()->getManager();
        $ad = $entityManager->getRepository(Ad::class)->find($id);
        if (!$ad) return new Response('Объявление не найдено');
        $entityManager->remove($ad);
        $entityManager->flush();
        return new Response('Объявление с идентификатором '.$id.' было удалено');
    }
}
