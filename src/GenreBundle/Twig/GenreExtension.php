<?php

namespace GenreBundle\Twig;

use GenreBundle\Entity\Genre;
use GenreBundle\Helper\GenreRouterHelper;

/**
 * Class GenreExtension
 */
class GenreExtension extends \Twig_Extension
{
    /**
     * @var GenreRouterHelper
     */
    private $genreRouterHelper;

    /**
     * ArticleExtension constructor.
     *
     * @param GenreRouterHelper $genreRouterHelper
     */
    public function __construct(GenreRouterHelper $genreRouterHelper)
    {
        $this->genreRouterHelper = $genreRouterHelper;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'genre_extension';
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('genre_path', array($this, 'getGenrePath')),
        ];
    }

    /**
     * @param Genre $genre
     * @param bool  $needAbsolute
     *
     * @return string
     */
    public function getGenrePath(Genre $genre, $needAbsolute = false) : string
    {
        return $this->genreRouterHelper->getGenrePath($genre, $needAbsolute);
    }
}
