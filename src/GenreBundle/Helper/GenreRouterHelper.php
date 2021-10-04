<?php

namespace GenreBundle\Helper;

use GenreBundle\Entity\Genre;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class GenreRouterHelper
 */
class GenreRouterHelper
{
    const GENRE_ROUTE = 'genre_books';
    const SUB_GENRE_ROUTE = 'sub_genre_books';

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * ArticleExtension constructor.
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param Genre $genre
     * @param bool  $needAbsolute
     *
     * @return string
     */
    public function getGenrePath(Genre $genre, $needAbsolute = false) : string
    {
        if ($genre->getParent()) {
            $path = $this->router->generate(
                self::SUB_GENRE_ROUTE,
                [
                    'genre' => $genre->getParent()->getSlug(),
                    'sub_genre' => $genre->getSlug(),
                ],
                $needAbsolute ? UrlGeneratorInterface::ABSOLUTE_URL : UrlGeneratorInterface::ABSOLUTE_PATH
            );
        } else {
            $path = $this->router->generate(
                self::GENRE_ROUTE,
                [
                    'genre' => $genre->getSlug(),
                ],
                $needAbsolute ? UrlGeneratorInterface::ABSOLUTE_URL : UrlGeneratorInterface::ABSOLUTE_PATH
            );
        }

        return $path;
    }
}
