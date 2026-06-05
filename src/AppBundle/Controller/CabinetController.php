<?php

namespace AppBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use OrderBundle\Entity\Order;
use ProductBundle\Entity\Product;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\Entity\User;
use UserBundle\Entity\UserDeliveryAddress;

class CabinetController extends BaseController
{
    private const RECENT_ORDERS_LIMIT = 20;

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function indexAction(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getCurrentUser();
        $orders = $this->em->getRepository(Order::class)
            ->createQueryBuilder('o')
            ->andWhere('o.user = :user OR o.email = :email')
            ->setParameter('user', $user)
            ->setParameter('email', $user->getEmail())
            ->orderBy('o.createdAt', 'DESC')
            ->setMaxResults(self::RECENT_ORDERS_LIMIT)
            ->getQuery()
            ->getResult();

        return $this->render('@App/Cabinet/index.html.twig', [
            'user' => $user,
            'orders' => $orders,
        ]);
    }

    public function orderAction(string $secret): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getCurrentUser();
        $order = $this->em->getRepository(Order::class)->findOneBy(['secret' => $secret]);

        if (!$order || !$this->orderBelongsToUser($order, $user)) {
            throw $this->createNotFoundException();
        }

        return $this->render('@App/Cabinet/order.html.twig', [
            'user' => $user,
            'order' => $order,
        ]);
    }

    public function profileAction(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getCurrentUser();
        $error = null;
        $saved = false;

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('profile', $request->request->get('_csrf_token'))) {
                $error = 'frontend.cabinet.profile.error.invalid_csrf';
            } else {
                $user->setFirstname(trim($request->request->get('firstname', '')) ?: null);
                $user->setLastname(trim($request->request->get('lastname', '')) ?: null);
                $user->setPhone(trim($request->request->get('phone', '')) ?: null);

                $this->em->flush();
                $saved = true;
            }
        }

        return $this->render('@App/Cabinet/profile.html.twig', [
            'user' => $user,
            'error' => $error,
            'saved' => $saved,
        ]);
    }

    public function favoritesAction(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getCurrentUser();

        return $this->render('@App/Cabinet/favorites.html.twig', [
            'user' => $user,
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

        $user = $this->getCurrentUser();
        $favorited = $user->hasFavorite($product);

        if ($favorited) {
            $user->removeFavorite($product);
        } else {
            $user->addFavorite($product);
        }

        $this->em->flush();

        return new JsonResponse([
            'favorited' => !$favorited,
            'count' => $user->getFavorites()->count(),
        ]);
    }

    public function favoriteIdsAction(): JsonResponse
    {
        if (!$this->isGranted('ROLE_USER')) {
            return new JsonResponse([]);
        }

        $ids = $this->getCurrentUser()->getFavorites()
            ->map(fn (Product $p) => $p->getId())
            ->toArray();

        return new JsonResponse(array_values($ids));
    }

    public function addressesAction(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getCurrentUser();
        $repo = $this->em->getRepository(UserDeliveryAddress::class);
        $error = null;

        if ($request->isMethod('POST')) {
            $error = $this->handleAddressCreate($request, $user, $repo->findBy(['user' => $user]));

            if (null === $error) {
                return $this->redirectToAddresses($request);
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

        $user = $this->getCurrentUser();
        $address = $this->findOwnedAddressOrThrow($id, $user);

        if ($this->syncSingleDefaultAddress($this->findAddressesForUser($user), $address)) {
            $this->em->flush();
        }

        return $this->redirectToAddresses($request);
    }

    public function addressDeleteAction(int $id, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if (!$this->isCsrfTokenValid('cabinet_address_delete_' . $id, $request->request->get('_csrf_token'))) {
            throw $this->createAccessDeniedException();
        }

        $user = $this->getCurrentUser();
        $address = $this->findOwnedAddressOrThrow($id, $user);

        $wasDefault = $address->getIsDefault();
        $this->em->remove($address);
        $this->em->flush();

        if ($wasDefault) {
            $repo = $this->em->getRepository(UserDeliveryAddress::class);
            $firstAddress = $repo->findOneBy(['user' => $user], ['createdAt' => 'DESC']);
            if ($firstAddress && $this->syncSingleDefaultAddress($repo->findBy(['user' => $user]), $firstAddress)) {
                $this->em->flush();
            }
        }

        return $this->redirectToAddresses($request);
    }

    /**
     * @param list<UserDeliveryAddress> $existingAddresses
     */
    private function handleAddressCreate(Request $request, User $user, array $existingAddresses): ?string
    {
        if (!$this->isCsrfTokenValid('cabinet_addresses', $request->request->get('_csrf_token'))) {
            return 'frontend.cabinet.addresses.error.invalid_csrf';
        }

        $address = trim((string) $request->request->get('address', ''));
        if ('' === $address) {
            return 'frontend.cabinet.addresses.error.empty_address';
        }

        $title = trim((string) $request->request->get('title', ''));
        $isDefault = (bool) $request->request->get('is_default');

        if ($isDefault) {
            $this->syncSingleDefaultAddress($existingAddresses);
        }

        $deliveryAddress = new UserDeliveryAddress();
        $deliveryAddress->setUser($user);
        $deliveryAddress->setAddress($address);
        $deliveryAddress->setTitle($title ?: null);
        $deliveryAddress->setIsDefault($isDefault);

        $this->em->persist($deliveryAddress);
        $this->em->flush();

        return null;
    }

    private function findOwnedAddressOrThrow(int $id, User $user): UserDeliveryAddress
    {
        $address = $this->em->getRepository(UserDeliveryAddress::class)->find($id);

        if (!$address || $address->getUser()->getId() !== $user->getId()) {
            throw $this->createNotFoundException();
        }

        return $address;
    }

    /**
     * @return list<UserDeliveryAddress>
     */
    private function findAddressesForUser(User $user): array
    {
        return $this->em->getRepository(UserDeliveryAddress::class)->findBy(['user' => $user]);
    }

    private function orderBelongsToUser(Order $order, User $user): bool
    {
        if (null !== $order->getUser()) {
            return $order->getUser()->getId() === $user->getId();
        }

        return 0 === strcasecmp((string) $order->getEmail(), (string) $user->getEmail());
    }

    private function getCurrentUser(): User
    {
        /** @var User $user */
        $user = $this->getUser();

        return $user;
    }

    private function redirectToAddresses(Request $request): Response
    {
        $request->attributes->set('_sonata_page_skip', true);

        return $this->redirectToRoute('cabinet_addresses', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @param list<UserDeliveryAddress> $addresses
     */
    private function syncSingleDefaultAddress(array $addresses, ?UserDeliveryAddress $preferred = null): bool
    {
        if (!$addresses) {
            return false;
        }

        $selectedId = $preferred?->getId() ?? $this->findCurrentDefaultId($addresses) ?? $addresses[0]->getId();

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

    /**
     * @param list<UserDeliveryAddress> $addresses
     */
    private function findCurrentDefaultId(array $addresses): ?int
    {
        foreach ($addresses as $address) {
            if ($address->getIsDefault()) {
                return $address->getId();
            }
        }

        return null;
    }
}