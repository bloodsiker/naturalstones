<?php

namespace AdminBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use ShareBundle\Services\TagFinder;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Sonata\AdminBundle\Templating\MutableTemplateRegistryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CRUDController extends Controller
{
    // TODO: add translations for string values
    public const ERROR_MESSAGE = 'Unable to find the object with id : %s';
    public const SUCCESS_MESSAGE = 'Moved successfully';

    private const CKEDITOR_TEMPLATE = '@Admin/Ckeditor/ajax.html.twig';

    public static function getSubscribedServices(): array
    {
        return [
            'doctrine' => ManagerRegistry::class,
            'doctrine.orm.default_entity_manager' => '?' . EntityManagerInterface::class,
            'share.tag.finder' => '?' . TagFinder::class,
        ] + parent::getSubscribedServices();
    }

    public function listAction(Request $request): Response
    {
        $this->applyCKEditorTemplate($request, 'list');

        return parent::listAction($request);
    }

    public function editAction(Request $request): Response
    {
        $this->applyCKEditorTemplate($request, 'edit');

        return parent::editAction($request);
    }

    private function applyCKEditorTemplate(Request $request, string $action): void
    {
        if (!$request->query->has('CKEditor')) {
            return;
        }

        $registry = $this->admin->getTemplateRegistry();
        if ($registry instanceof MutableTemplateRegistryInterface) {
            $registry->setTemplate($action, self::CKEDITOR_TEMPLATE);
        }
    }

    /**
     * Move item up by decrementing order_num value.
     */
    public function moveUpAction(): RedirectResponse
    {
        return $this->moveByPosition('DESC', static function (object $object, object $neighbour): void {
            if ($neighbour->getOrderNum() === $object->getOrderNum()) {
                $neighbour->setOrderNum($object->getOrderNum() + 1);
            } else {
                self::swapOrderNum($object, $neighbour);
            }
        }, static fn (object $object) => $object->setOrderNum($object->getOrderNum() - 1));
    }

    /**
     * Move item down by incrementing order_num value.
     */
    public function moveDownAction(): RedirectResponse
    {
        return $this->moveByPosition('ASC', static function (object $object, object $neighbour): void {
            if ($neighbour->getOrderNum() === $object->getOrderNum()) {
                $object->setOrderNum($object->getOrderNum() + 1);
            } else {
                self::swapOrderNum($object, $neighbour);
            }
        }, static fn (object $object) => $object->setOrderNum($object->getOrderNum() + 1));
    }

    public function recalculateOrderNumAction(): RedirectResponse
    {
        $repository = $this->getRepository();
        if (method_exists($repository, 'getHighestPropertyByPosition')) {
            $repository->reCountOrderNum();
        }

        return new RedirectResponse($this->admin->generateUrl($this->getListOrTreeUrlName()));
    }

    /**
     * Move item to first position in list.
     */
    public function doFirstAction(): RedirectResponse
    {
        return $this->moveToBoundary('ASC', static fn (int $first) => $first - 1);
    }

    /**
     * Move item to last position in list.
     */
    public function doLastAction(): RedirectResponse
    {
        return $this->moveToBoundary('DESC', static fn (int $last) => $last + 1);
    }

    protected function getRepository(): ObjectRepository
    {
        return $this->container->get('doctrine')->getRepository($this->admin->getClass());
    }

    private function moveByPosition(string $direction, \Closure $whenNeighbourFound, \Closure $fallback): RedirectResponse
    {
        $repository = $this->getRepository();

        return $this->objectTransform(function (object $object) use ($repository, $direction, $whenNeighbourFound, $fallback): void {
            if (!method_exists($repository, 'getHighestPropertyByPosition')) {
                $fallback($object);

                return;
            }

            $neighbour = $repository->getHighestPropertyByPosition('orderNum', $object, $direction);
            if ($neighbour) {
                $whenNeighbourFound($object, $neighbour);
            }
        });
    }

    private function moveToBoundary(string $direction, \Closure $computeNewOrder): RedirectResponse
    {
        $repository = $this->getRepository();
        if (!method_exists($repository, 'getHighestPropertyByOrder')) {
            return new RedirectResponse($this->admin->generateUrl($this->getListOrTreeUrlName()));
        }

        $boundary = $repository->getHighestPropertyByOrder('orderNum', $direction);

        return $this->objectTransform(static function (object $object) use ($boundary, $computeNewOrder): void {
            if ($boundary !== $object->getOrderNum()) {
                $object->setOrderNum($computeNewOrder($boundary));
            }
        });
    }

    private function objectTransform(\Closure $action): RedirectResponse
    {
        $translator = $this->container->get('translator');
        $id = $this->getRequest()->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(
                $translator->trans(self::ERROR_MESSAGE, ['%id%' => $id], 'SonataAdminBundle')
            );
        }

        $action($object, $this->admin);

        $this->admin->update($object);
        $this->addFlash('sonata_flash_success', $translator->trans(self::SUCCESS_MESSAGE, [], 'SonataAdminBundle'));

        return new RedirectResponse($this->admin->generateUrl('list'));
    }

    private function getListOrTreeUrlName(): string
    {
        $mappings = $this->admin->getAccessMapping();
        if ($mappings && array_key_exists('tree', $mappings)) {
            $treeUrl = $this->admin->generateUrl('tree');
            $referer = $this->getRequest()->headers->get('referer');

            if (parse_url($referer, PHP_URL_PATH) === parse_url($treeUrl, PHP_URL_PATH)) {
                return 'tree';
            }
        }

        return 'list';
    }

    private static function swapOrderNum(object $a, object $b): void
    {
        $aOrder = $a->getOrderNum();
        $a->setOrderNum($b->getOrderNum());
        $b->setOrderNum($aOrder);
    }
}