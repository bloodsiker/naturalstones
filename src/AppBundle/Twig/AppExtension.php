<?php

namespace AppBundle\Twig;

use OrderBundle\Entity\OrderBoard;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Sonata\BlockBundle\Model\Block;

/**
 * Class AppExtension
 */
class AppExtension extends \Twig_Extension
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * AppExtension constructor.
     *
     * @param TranslatorInterface $translator
     * @param RouterInterface     $router
     */
    public function __construct(TranslatorInterface $translator, RouterInterface $router)
    {
        $this->router       = $router;
        $this->translator   = $translator;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_extension';
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('block_ajax_url', array($this, 'blockAjaxUrl')),
            new \Twig_SimpleFunction('replace_highlight', array($this, 'replaceHighlight')),
            new \Twig_SimpleFunction('book_change_end', array($this, 'countBookChangeEnd')),
            new \Twig_SimpleFunction('icon_order_status', array($this, 'iconOrderStatus')),
            new \Twig_SimpleFunction('is_constrain', array($this, 'isConstrain')),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getTests()
    {
        return array(
            new \Twig_SimpleTest('instanceof', array($this, 'isInstanceOf')),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('file_size_humanize', array($this, 'fileSizeHumanize')),
            new \Twig_SimpleFilter('date_humanize', array($this, 'dateHumanize')),
            new \Twig_SimpleFilter('date_time_humanize', array($this, 'dateTimeHumanize')),
            new \Twig_SimpleFilter('url_decode', array($this, 'urlDecode')),
        );
    }

    /**
     * @param string $value
     * @param string $replace
     *
     * @return string
     */
    public function replaceHighlight($value, $replace) : string
    {
        return preg_replace("/$replace/iu", '<span class="highlight">'.$replace.'</span>', $value);
    }

    /**
     * @param int $count
     *
     * @return string
     */
    public function countBookChangeEnd($count) : string
    {
        $titles = ['книга', 'книги', 'книг'];

        $cases = [2, 0, 1, 1, 1, 2];
        $key = ($count % 100 > 4 && $count % 100 < 20) ? 2 : $cases[min($count % 10, 5)];

        return $titles[$key];
    }

    /**
     * @param int $status
     *
     * @return string
     */
    public function iconOrderStatus(int $status) : string
    {
        $statuses = [
            OrderBoard::STATUS_NEW => 'fa fa-exclamation',
            OrderBoard::STATUS_COMPLETED => 'fa fa-check',
            OrderBoard::STATUS_CANCEL => 'fa fa-times',
        ];

        return $statuses[$status];
    }


    /**
     * Generate ajax block route without page
     *
     * @param Block $block
     * @param array $parameters
     *
     * @return string
     */
    public function blockAjaxUrl(Block $block, $parameters = [])
    {
        $parameters = array_merge($parameters, [
            'blockId'   => $block->getId(),
            'blockType' => $block->getType(),
        ]);

        return $this->router->generate('block__ajax', $parameters);
    }

    /**
     * Checks if $var is instance of $instance
     *
     * @param mixed $var
     * @param mixed $instance
     *
     * @return bool
     */
    public function isInstanceOf($var, $instance)
    {
        return $var instanceof $instance;
    }

    /**
     * @param $id
     * @param $string
     *
     * @return string|null
     */
    public function isConstrain($id, $string)
    {
        $explode = explode(',', $string);

        return in_array($id, $explode) ? 'checked' : null;
    }

    /**
     * Returns a file size in human readable format.
     *
     * @param integer $size
     * @param string  $delimiter
     *
     * @return string
     */
    public function fileSizeHumanize($size, $delimiter = '')
    {
        $prefix = array('b', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb', 'Eb', 'Zb', 'Yb');

        $counter = 0;

        while (($size / 1024) > 1) {
            $size = $size / 1024;
            $counter += 1;
        }

        return sprintf('%.2f%s%s', $size, $delimiter, $prefix[$counter]);
    }

    /**
     * URL Decode a string
     *
     * @param string $url
     *
     * @return string The decoded URL
     */
    public function urlDecode($url)
    {
        return urldecode($url);
    }

    /**
     * Convert DateTime value for news format
     *
     * @param \DateTime $datetime
     * @param string    $formatDay
     * @param bool      $hideYearIfCurrent
     *
     * @return string
     *
     * @throws \Exception
     */
    public function dateHumanize(\DateTime $datetime, $formatDay = 'd MMMM', $hideYearIfCurrent = true)
    {
        $now = self::getNow();
        $date = $datetime->format('d.m.Y');
        if ($date === $now->format('d.m.Y')) {
            return $this->translator->trans('app.datetime.today', [], 'AppBundle');
        } elseif ($date === $now->sub(new \DateInterval('PT24H'))->format('d.m.Y')) {
            return $this->translator->trans('app.datetime.yesterday', [], 'AppBundle');
        }

//        if (true === $hideYearIfCurrent && $now->format('Y') !== $datetime->format('Y')) {
//            $formatDay .= ' y';
//        }

        return datefmt_format_object($datetime, $formatDay);
    }

    /**
     * Convert DateTime value for news format (with time)
     *
     * @param \DateTime $datetime
     * @param string    $formatDay
     * @param string    $formatTime
     * @param bool      $hideYearIfCurrent
     *
     * @return string
     *
     * @throws \Exception
     */
    public function dateTimeHumanize(\DateTime $datetime, $formatDay = 'd MMMM', $formatTime = ', HH:mm', $hideYearIfCurrent = true)
    {
        return $this->dateHumanize($datetime, $formatDay, $hideYearIfCurrent).datefmt_format_object($datetime, $formatTime);
    }

    /**
     * Returns current date and time, rounded to nearest minute
     *
     * @return \DateTime
     *
     * @throws \Exception
     */
    private static function getNow()
    {
        $now = new \DateTime('now');
        $second = $now->format('s');
        $now->add(new \DateInterval('PT'.(60-$second).'S'));

        return $now;
    }
}
