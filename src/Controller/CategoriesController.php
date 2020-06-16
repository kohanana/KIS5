<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoriesController extends AbstractController
{
    public function GetCategories()
    {
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();
        if (!$categories){
            return new Response("Категорий товаров нет");
        }
        $arrayCollection = array();

        foreach($categories as $category) {
            $arrayCollection[] = array(
                'id' => $category->getId(),
                'name' => $category->getName(),
                'description' => $category->getDescription(),
            );
        }

        return new JsonResponse($arrayCollection);
    }

    public function GetCategory($id)
    {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->find($id);
        if (!$category){
            return new Response('Категория товара не найдена');
        }
        $planJSON = [
            'id' => $category->getId(),
            'name' => $category->getName(),
            'description' => $category->getDescription(),
        ];
        return new JsonResponse($planJSON);
    }

    public function PostCategory(Request $request):Response{
        $entityManager = $this->getDoctrine()->getManager();
        $category = new Category();
        $category->setName($request->request->get('name'));
        $category->setDescription($request->request->get('description'));
        $entityManager->persist($category);
        $entityManager->flush();
        return new Response('Категория была создана с идентификатором '.$category->getId());
    }

    public function PutCategory($id, Request $request):Response{
        $entityManager = $this->getDoctrine()->getManager();
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->find($id);
        if(!$category){
            $category = new Category();
            $category->setName($request->request->get('name'));
            $category->setDescription($request->request->get('description'));
            $entityManager->persist($category);
            $entityManager->flush();
            return new Response('Категория была создана с идентификатором '.$category->getId());
        }else{
            $category->setName($request->request->get('name'));
            $category->setDescription($request->request->get('description'));
            $entityManager->persist($category);
            $entityManager->flush();
            return new Response('Категория была изменена с идентификатором '.$category->getId());
        }
    }

    public function DeleteCategory($id){
        $entityManager = $this->getDoctrine()->getManager();
        $category = $entityManager->getRepository(Category::class)->find($id);
        if (!$category) return new Response('Категория не найдена');
        $entityManager->remove($category);
        $entityManager->flush();
        return new Response('Категория с идентификатором '.$id.' была удалена');
    }
}
