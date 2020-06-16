<?php

namespace App\Controller;

use App\Entity\Plan;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PlansController extends AbstractController
{
    public function GetPlans()
    {
        $plans = $this->getDoctrine()
            ->getRepository(Plan::class)
            ->findAll();
        if (!$plans){
            return new Response("Планов размещений нет");
        }
        $arrayCollection = array();

        foreach($plans as $plan) {
            $arrayCollection[] = array(
                'id' => $plan->getId(),
                'name' => $plan->getName(),
                'description' => $plan->getDescription(),
                'activeDayCount' => $plan->getActiveDayCount(),
                'price' => $plan->getPrice(),
            );
        }

        return new JsonResponse($arrayCollection);
    }

    public function GetPlan($id)
    {
        $plan = $this->getDoctrine()
            ->getRepository(Plan::class)
            ->find($id);
        if (!$plan){
            return new Response('План размещения объявления не найден');
        }
        $planJSON = [
            'id' => $plan->getId(),
            'name' => $plan->getName(),
            'description' => $plan->getDescription(),
            'activeDayCount' => $plan->getActiveDayCount(),
            'price' => $plan->getPrice(),
        ];
        return new JsonResponse($planJSON);
    }

    public function PostPlan(Request $request):Response{
        $entityManager = $this->getDoctrine()->getManager();
        $plan = new Plan();
        $plan->setName($request->request->get('name'));
        $plan->setDescription($request->request->get('description'));
        $plan->setActiveDayCount($request->request->get('activeDayCount'));
        $plan->setPrice($request->request->get('price'));
        $entityManager->persist($plan);
        $entityManager->flush();
        return new Response('План размещения объявления был создан с идентификатором '.$plan->getId());
    }

    public function PutPlan($id, Request $request):Response{
        $entityManager = $this->getDoctrine()->getManager();
        $plan = $this->getDoctrine()
            ->getRepository(Plan::class)
            ->find($id);
        if(!$plan){
            $plan = new Plan();
            $plan->setName($request->request->get('name'));
            $plan->setDescription($request->request->get('description'));
            $plan->setActiveDayCount($request->request->get('activeDayCount'));
            $plan->setPrice($request->request->get('price'));
            $entityManager->persist($plan);
            $entityManager->flush();
            return new Response('План размещения объявления был создан с идентификатором '.$plan->getId());
        }else{
            $plan->setName($request->request->get('name'));
            $plan->setDescription($request->request->get('description'));
            $plan->setActiveDayCount($request->request->get('activeDayCount'));
            $plan->setPrice($request->request->get('price'));
            $entityManager->persist($plan);
            $entityManager->flush();
            return new Response('План размещения объявления был изменен с идентификатором '.$plan->getId());
        }
    }

    public function DeletePlan($id, Request $request):Response{
        $entityManager = $this->getDoctrine()->getManager();
        $plan = $entityManager->getRepository(Plan::class)->find($id);
        if (!$plan) return new Response('План размещения не найден');
        $entityManager->remove($plan);
        $entityManager->flush();
        return new Response('План размещения с идентификатором '.$id.' был удален');
    }
}
