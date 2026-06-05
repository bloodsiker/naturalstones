<?php

namespace AdminBundle\Traits;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

trait PreviewAdminActionTrait
{
    public function previewAction(Request $request): Response
    {
        $admin = $this->admin;
        $response = new Response();

        $id = (int) $request->query->get('id');
        $language = $request->query->get('langCode');
        $entity = $admin->getObject($id);

        if ($entity && $entity->getId()) {
            [$bundleName] = explode(':', $this->admin->getBaseControllerName());
            $response->setContent(
                $this->renderView(
                    $bundleName . ':Preview:preview.html.twig',
                    [
                        'entity' => $entity,
                        'language' => $language,
                        'admin' => $admin,
                    ]
                )
            );
        }

        if ($this->container->has('profiler')) {
            $this->container->get('profiler')->disable();
        }

        return $response;
    }
}