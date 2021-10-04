<?php

namespace ProductBundle\Controller;

use AdminBundle\Controller\CRUDController as Controller;

use BookBundle\Entity\Book;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ProductAdminController
 */
class ProductAdminController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function relatedByTagsAction(Request $request)
    {
        $excludeIds = array_map(function ($value) {
            return (int) $value;
            }, $request->request->get('exclude', []));

        $tags = array_map(function ($value) {
            return (int) $value;
            }, $request->request->get('tags', []));

        $em = $this->container->get('doctrine')->getManager();
        $repository = $em->getRepository($this->admin->getClass());
        $relatedNews = $repository->getRelatedByTagsBooks($tags, $excludeIds, 100);

        $result = array_map(
            function ($item) {
                $authors = [];
                array_map(function ($value) use (&$authors) {
                    $authors[] = $value->getName();

                    return $value;
                }, $item->getAuthors()->getValues());

                return [
                    'id'        => $item->getId(),
                    'name'      => $item->getName(),
                    'author'    => implode(', ', $authors),
                    'views'     => $item->getViews(),
                    'download'  => $item->getDownload(),
                    'rate'      => $item->getRatePlus() - $item->getRateMinus(),
                    'date'      => $item->getCreatedAt()->format('d.m.Y H:i:s'),
                ];
            },
            $relatedNews
        );

        shuffle($result);

        return $this->renderJson($result);
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function findTagsInTextAction(Request $request)
    {
        $excludeTags = array_map(
            function ($value) { return (int) $value; },
            $request->request->get('tags', [])
        );

        $bookFile = array_map(function ($value) {
            return (int) $value;
        }, $request->request->get('bookFile', []));

        $text = $request->request->get('textContent', '');


        $tagFinder = $this->container->get('share.tag.finder');
        $result = $tagFinder->findTagsInText($text, $excludeTags, $bookFile);

        return $this->renderJson($result);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function searchBookAction(Request $request)
    {
        $name = trim($request->get('name'));
        $em = $this->container->get('doctrine')->getManager();
        $router = $this->container->get('router');
        $repository = $em->getRepository(Book::class);

        $qb = $repository->createQueryBuilder('b');
        $books = $qb
            ->where('b.isActive = 1')
            ->andWhere('b.name LIKE :search')
            ->setParameter('search', '%'.$name.'%')
            ->getQuery()->getResult();

        $result = array_map(
            function ($item) use ($router) {
                return [
                    'name' => $item->getName(),
                    'url'  => $router->generate('book_view', ['id' => $item->getId(), 'slug' => $item->getSlug()]),
                ];
            },
            $books
        );

        return $this->renderJson($result);
    }
}
