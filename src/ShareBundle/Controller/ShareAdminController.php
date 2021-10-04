<?php

namespace ShareBundle\Controller;

use AdminBundle\Controller\CRUDController as Controller;

use ShareBundle\Entity\Author;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ShareAdminController
 */
class ShareAdminController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function searchAuthorAction(Request $request)
    {
        $name = trim($request->get('name'));
        $em = $this->container->get('doctrine')->getManager();
        $router = $this->container->get('router');
        $repository = $em->getRepository(Author::class);

        $qb = $repository->createQueryBuilder('a');
        $authors = $qb
            ->where('a.isActive = 1')
            ->andWhere('a.name LIKE :search')
            ->setParameter('search', '%'.$name.'%')
            ->getQuery()->getResult();

        $result = array_map(
            function ($item) use ($router) {
                return [
                    'name' => $item->getName(),
                    'url'  => $router->generate('author_books', ['slug' => $item->getSlug()]),
                ];
            },
            $authors
        );

        return $this->renderJson($result);
    }
}
