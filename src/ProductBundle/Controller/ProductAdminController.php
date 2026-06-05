<?php

namespace ProductBundle\Controller;

use AdminBundle\Controller\CRUDController as Controller;
use ProductBundle\Entity\ProductHasImage;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductAdminController extends Controller
{
    public function cloneAction(): RedirectResponse
    {
        $translator = $this->get('translator');
        $em = $this->get('doctrine.orm.default_entity_manager');
        $id = $this->getRequest()->get($this->admin->getIdParameter());

        $object = $this->admin->getObject($id);
        $clone = clone $object;

        $em->persist($clone);
        $em->flush();

        foreach ($object->getProductHasImage() as $image) {
            $productHasImage = new ProductHasImage();
            $productHasImage->setImage($image->getImage());
            $productHasImage->setProduct($clone);
            $clone->addProductHasImage($productHasImage);
            $em->persist($clone);
            $em->flush();
        }

        $this->addFlash(
            'sonata_flash_success',
            $translator->trans('object_clone_success', [], 'SonataAdminBundle')
        );

        return new RedirectResponse($this->admin->generateUrl('edit', ['id' => $clone->getId()]));
    }

    public function relatedByTagsAction(Request $request): Response
    {
        $excludeIds = array_map('intval', $request->request->get('exclude', []));
        $tags = array_map('intval', $request->request->get('tags', []));

        $em = $this->container->get('doctrine')->getManager();
        $repository = $em->getRepository($this->admin->getClass());
        $related = $repository->getRelatedByTagsBooks($tags, $excludeIds, 100);

        $result = array_map(static function ($item) {
            $authors = array_map(static fn ($author) => $author->getName(), $item->getAuthors()->getValues());

            return [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'author' => implode(', ', $authors),
                'views' => $item->getViews(),
                'download' => $item->getDownload(),
                'rate' => $item->getRatePlus() - $item->getRateMinus(),
                'date' => $item->getCreatedAt()->format('d.m.Y H:i:s'),
            ];
        }, $related);

        shuffle($result);

        return $this->renderJson($result);
    }

    /**
     * @throws \Exception
     */
    public function findTagsInTextAction(Request $request): Response
    {
        $excludeTags = array_map('intval', $request->request->get('tags', []));
        $bookFile = array_map('intval', $request->request->get('bookFile', []));
        $text = $request->request->get('textContent', '');

        $tagFinder = $this->container->get('share.tag.finder');

        return $this->renderJson($tagFinder->findTagsInText($text, $excludeTags, $bookFile));
    }
}