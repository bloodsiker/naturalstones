<?php

namespace AppBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use OrderBundle\Entity\Order;
use ProductBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\Entity\User;
use UserBundle\Entity\UserDeliveryAddress;

class CabinetController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function indexAction(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();
        $orders = $this->em->getRepository(Order::class)
            ->createQueryBuilder('o')
            ->andWhere('o.user = :user OR o.email = :email')
            ->setParameter('user', $user)
            ->setParameter('email', $user->getEmail())
            ->orderBy('o.createdAt', 'DESC')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult();

        return $this->render('@App/Cabinet/index.html.twig', [
            'user'   => $user,
            'orders' => $orders,
        ]);
    }

    public function orderAction(string $secret): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var User $user */
        $user = $this->getUser();

        $order = $this->em->getRepository(Order::class)->findOneBy(['secret' => $secret]);
        if (
            !$order
            || (
                $order->getUser() !== null
                && $order->getUser()->getId() !== $user->getId()
            )
            || (
                $order->getUser() === null
                && strcasecmp((string) $order->getEmail(), (string) $user->getEmail()) !== 0
            )
        ) {
            throw $this->createNotFoundException();
        }

        return $this->render('@App/Cabinet/order.html.twig', [
            'user'  => $user,
            'order' => $order,
        ]);
    }

    public function profileAction(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var User $user */
        $user  = $this->getUser();
        $error = null;
        $saved = false;

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('profile', $request->request->get('_csrf_token'))) {
                $error = 'frontend.cabinet.profile.error.invalid_csrf';
            } else {
                $firstname = trim($request->request->get('firstname', ''));
                $lastname  = trim($request->request->get('lastname', ''));
                $phone     = trim($request->request->get('phone', ''));

                $user->setFirstname($firstname ?: null);
                $user->setLastname($lastname ?: null);
                $user->setPhone($phone ?: null);

                $this->em->flush();
                $saved = true;
            }
        }

        return $this->render('@App/Cabinet/profile.html.twig', [
            'user'  => $user,
            'error' => $error,
            'saved' => $saved,
        ]);
    }

    public function favoritesAction(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var User $user */
        $user = $this->getUser();

        return $this->render('@App/Cabinet/favorites.html.twig', [
            'user'      => $user,
            'favorites' => $user->getFavorites(),
        ]);
    }

    public function toggleFavoriteAction(int $id): JsonResponse
    {
        if (!$this->isGranted('ROLE_USER')) {
            return new JsonResponse(['error' => 'unauthenticated'], 401);
        }

        $product = $this->em->getRepository(Product::class)->find($id);
        if (!$product) {
            return new JsonResponse(['error' => 'not_found'], 404);
        }

        /** @var User $user */
        $user      = $this->getUser();
        $favorited = $user->hasFavorite($product);

        if ($favorited) {
            $user->removeFavorite($product);
        } else {
            $user->addFavorite($product);
        }

        $this->em->flush();

        return new JsonResponse([
            'favorited' => !$favorited,
            'count'     => $user->getFavorites()->count(),
        ]);
    }

    public function favoriteIdsAction(): JsonResponse
    {
        if (!$this->isGranted('ROLE_USER')) {
            return new JsonResponse([]);
        }

        /** @var User $user */
        $user = $this->getUser();
        $ids  = $user->getFavorites()->map(fn(Product $p) => $p->getId())->toArray();

        return new JsonResponse(array_values($ids));
    }

    public function addressesAction(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var User $user */
        $user = $this->getUser();
        $repo = $this->em->getRepository(UserDeliveryAddress::class);
        $error = null;

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('cabinet_addresses', $request->request->get('_csrf_token'))) {
                $error = 'frontend.cabinet.addresses.error.invalid_csrf';
            } else {
                $address = trim((string) $request->request->get('address', ''));
                $title = trim((string) $request->request->get('title', ''));
                $isDefault = (bool) $request->request->get('is_default');

                if ($address === '') {
                    $error = 'frontend.cabinet.addresses.error.empty_address';
                } else {
                    if ($isDefault) {
                        $this->syncSingleDefaultAddress($repo->findBy(['user' => $user]));
                    }

                    $deliveryAddress = new UserDeliveryAddress();
                    $deliveryAddress->setUser($user);
                    $deliveryAddress->setAddress($address);
                    $deliveryAddress->setTitle($title ?: null);
                    $deliveryAddress->setIsDefault($isDefault);

                    $this->em->persist($deliveryAddress);
                    $this->em->flush();

                    return $this->redirectToRoute('cabinet_addresses', [], Response::HTTP_SEE_OTHER);
                }
            }
        }

        $addresses = $repo->findBy(['user' => $user], ['isDefault' => 'DESC', 'createdAt' => 'DESC']);
        if ($this->syncSingleDefaultAddress($addresses)) {
            $this->em->flush();
        }

        return $this->render('@App/Cabinet/addresses.html.twig', [
            'user' => $user,
            'addresses' => $addresses,
            'error' => $error,
        ]);
    }

    public function addressDefaultAction(int $id, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if (!$this->isCsrfTokenValid('cabinet_address_default_' . $id, $request->request->get('_csrf_token'))) {
            throw $this->createAccessDeniedException();
        }

        /** @var User $user */
        $user = $this->getUser();
        /** @var UserDeliveryAddress|null $address */
        $address = $this->em->getRepository(UserDeliveryAddress::class)->find($id);

        if (!$address || $address->getUser()->getId() !== $user->getId()) {
            throw $this->createNotFoundException();
        }

        $addressesToReset = $this->em->getRepository(UserDeliveryAddress::class)->findBy(['user' => $user]);
        if ($this->syncSingleDefaultAddress($addressesToReset, $address)) {
            $this->em->flush();
        }

        return $this->redirectToRoute('cabinet_addresses', [], Response::HTTP_SEE_OTHER);
    }

    public function addressDeleteAction(int $id, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if (!$this->isCsrfTokenValid('cabinet_address_delete_' . $id, $request->request->get('_csrf_token'))) {
            throw $this->createAccessDeniedException();
        }

        /** @var User $user */
        $user = $this->getUser();
        /** @var UserDeliveryAddress|null $address */
        $address = $this->em->getRepository(UserDeliveryAddress::class)->find($id);

        if (!$address || $address->getUser()->getId() !== $user->getId()) {
            throw $this->createNotFoundException();
        }

        $wasDefault = $address->getIsDefault();
        $this->em->remove($address);
        $this->em->flush();

        if ($wasDefault) {
            $firstAddress = $this->em->getRepository(UserDeliveryAddress::class)->findOneBy(
                ['user' => $user],
                ['createdAt' => 'DESC']
            );
            if ($firstAddress) {
                $addressesToReset = $this->em->getRepository(UserDeliveryAddress::class)->findBy(['user' => $user]);
                if ($this->syncSingleDefaultAddress($addressesToReset, $firstAddress)) {
                    $this->em->flush();
                }
            }
        }

        return $this->redirectToRoute('cabinet_addresses', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @param UserDeliveryAddress[] $addresses
     */
    private function syncSingleDefaultAddress(array $addresses, ?UserDeliveryAddress $preferred = null): bool
    {
        if (!$addresses) {
            return false;
        }

        $selectedId = null;

        if ($preferred !== null) {
            $selectedId = $preferred->getId();
        } else {
            foreach ($addresses as $address) {
                if ($address->getIsDefault()) {
                    $selectedId = $address->getId();
                    break;
                }
            }

            if ($selectedId === null) {
                $selectedId = $addresses[0]->getId();
            }
        }

        $changed = false;
        foreach ($addresses as $address) {
            $shouldBeDefault = $address->getId() === $selectedId;
            if ($address->getIsDefault() !== $shouldBeDefault) {
                $address->setIsDefault($shouldBeDefault);
                $changed = true;
            }
        }

        return $changed;
    }
}
