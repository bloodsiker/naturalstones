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
            $response->setContent(
                $this->renderView(
                    $this->getPreviewTemplate(),
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

    /**
     * Derives the bundle's Twig namespace from the admin's class
     * (e.g. `MediaBundle\Admin\MediaImageAdmin` → `@Media/Preview/preview.html.twig`).
     * Symfony auto-registers `@<ShortName>` where short name strips the "Bundle" suffix.
     */
    private function getPreviewTemplate(): string
    {
        $bundleName = strstr($this->admin::class, '\\', true);
        $shortName = preg_replace('/Bundle$/', '', $bundleName);

        return sprintf('@%s/Preview/preview.html.twig', $shortName);
    }
}