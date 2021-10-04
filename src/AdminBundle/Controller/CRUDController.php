<?php

namespace AdminBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class CRUDController
 */
class CRUDController extends Controller
{
    //TODO: add translations for string values
    const ERROR_MESSAGE = 'actions.message.object_not_found';//'Unable to find the object with id : %s';
    const SUCCESS_MESSAGE = 'actions.message.object_success';//'Moved successfully';

    /**
     * Move item up by decrementing order_num value
     *
     * @return RedirectResponse
     */
    public function moveUpAction()
    {
        $repository = $this->getRepository();

        return $this->objectTransform(function ($object, $admin) use ($repository) {
            if (method_exists($repository, 'getHighestPropertyByPosition')) {
                $secondObject = $repository->getHighestPropertyByPosition('orderNum', $object, 'DESC');
                if ($secondObject) {
                    $object->setOrderNum($secondObject->getOrderNum());
                    $secondObject->setOrderNum($secondObject->getOrderNum() + 1);
                }
            } else {
                $object->setOrderNum($object->getOrderNum() - 1);
            }
        });
    }

    /**
     * Move item down by incrementing order_num value
     *
     * @return RedirectResponse
     */
    public function moveDownAction()
    {
        $repository = $this->getRepository();

        return $this->objectTransform(function ($object, $admin) use ($repository) {
            if (method_exists($repository, 'getHighestPropertyByPosition')) {
                $secondObject = $repository->getHighestPropertyByPosition('orderNum', $object, 'ASC');
                if ($secondObject) {
                    $object->setOrderNum($secondObject->getOrderNum());
                    $secondObject->setOrderNum($secondObject->getOrderNum() - 1);
                }
            } else {
                $object->setOrderNum($object->getOrderNum() + 1);
            }
        });
    }

    /**
     * @return RedirectResponse
     */
    public function recalculateOrderNumAction()
    {
        $repository = $this->getRepository();
        if (method_exists($repository, 'getHighestPropertyByPosition')) {
            $repository->reCountOrderNum();
        }

        return new RedirectResponse($this->admin->generateUrl($this->getListOrTreeUrlName()));
    }

    /**
     * Move item to last position in list
     *
     * @return RedirectResponse
     */
    public function doFirstAction()
    {
        $repository = $this->getRepository();

        if (method_exists($repository, 'getHighestPropertyByOrder')) {
            $firstPosition = $repository->getHighestPropertyByOrder('orderNum', 'ASC');

            return $this->objectTransform(function ($object) use ($firstPosition) {
                if ($firstPosition !== $object->getOrderNum()) {
                    $object->setOrderNum($firstPosition - 1);
                }
            });
        }

        return new RedirectResponse($this->admin->generateUrl($this->getListOrTreeUrlName()));
    }

    /**
     * Move item to last position in list
     *
     * @return RedirectResponse
     */
    public function doLastAction()
    {
        $repository = $this->getRepository();

        if (method_exists($repository, 'getHighestPropertyByOrder')) {
            $lastPosition = $repository->getHighestPropertyByOrder('orderNum', 'DESC');

            return $this->objectTransform(function ($object) use ($lastPosition) {
                if ($lastPosition !== $object->getOrderNum()) {
                    $object->setOrderNum($lastPosition + 1);
                }
            });
        }

        return new RedirectResponse($this->admin->generateUrl($this->getListOrTreeUrlName()));
    }

    /**
     * @param string $action \Closure
     *
     * @return RedirectResponse
     */
    private function objectTransform($action)
    {
        $translator = $this->get('translator');
        $id = $this->getRequest()->get($this->admin->getIdParameter());

        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(
                $translator->trans(self::ERROR_MESSAGE, array('%id%' => $id), 'SonataAdminBundle')
            );
        }

        $action($object, $this->admin);

        $this->admin->update($object);
        $this->addFlash(
            'sonata_flash_success',
            $translator->trans(self::SUCCESS_MESSAGE, array(), 'SonataAdminBundle')
        );

        return new RedirectResponse($this->admin->generateUrl($this->getListOrTreeUrlName()));
    }

    /**
     * @return string
     */
    private function getListOrTreeUrlName()
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

    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    private function getRepository()
    {
        return $this->container->get('doctrine')->getRepository($this->admin->getClass());
    }
}
