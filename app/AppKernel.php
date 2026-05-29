<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Oneup\UploaderBundle\OneupUploaderBundle(),
            new Knp\Bundle\GaufretteBundle\KnpGaufretteBundle(),
            new Vich\UploaderBundle\VichUploaderBundle(),

            // Symfony CMF RoutingBundle
            new Symfony\Cmf\Bundle\RoutingBundle\CmfRoutingBundle(),
            new JMS\I18nRoutingBundle\JMSI18nRoutingBundle(),

            // Doctrine Migrations bundle
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),

            // Sonata doctrine extensions (provides DoctrineORMMapper for dynamic associations)
            new Sonata\Doctrine\Bridge\Symfony\SonataDoctrineBundle(),

            // Sonata Twig extensions (provides @SonataTwig templates, FlashMessage, etc.)
            new Sonata\Twig\Bridge\Symfony\SonataTwigBundle(),

            // Sonata Form extensions (provides @SonataForm templates, datepicker, etc.)
            new Sonata\Form\Bridge\Symfony\SonataFormBundle(),

            // Sonata AdminBundle and it dependencies
            new Sonata\BlockBundle\SonataBlockBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
            new Sonata\AdminBundle\SonataAdminBundle(),

            // Sonata Intl bundle
            new Sonata\IntlBundle\SonataIntlBundle(),

            // Sonata PageBundle
            new Sonata\PageBundle\SonataPageBundle(),
            new Cocur\Slugify\Bridge\Symfony\CocurSlugifyBundle(),

            // Sonata Seo bundle
            new Sonata\SeoBundle\SonataSeoBundle(),

            // Sonata UserBundle
            new Sonata\UserBundle\SonataUserBundle(),

            // Store translations in database
            new Lexik\Bundle\TranslationBundle\LexikTranslationBundle(),

            // A2lix multilingual forms
            new A2lix\AutoFormBundle\A2lixAutoFormBundle(),
            new A2lix\TranslationFormBundle\A2lixTranslationFormBundle(),

            // Doctrine2 Behaviors
            new Knp\DoctrineBehaviors\DoctrineBehaviorsBundle(),

            // Image manipulations
            new Liip\ImagineBundle\LiipImagineBundle(),

            // CKEditor integration
            new FOS\CKEditorBundle\FOSCKEditorBundle(),

            // User bundle
            new UserBundle\UserBundle(),

            // Custom admin controllers, templates, etc.
            new AdminBundle\AdminBundle(),

            // Pagerfanta Bundle
            new BabDev\PagerfantaBundle\BabDevPagerfantaBundle(),

//            new Sentry\SentryBundle\SentryBundle(),

            // Application bundles
            new AppBundle\AppBundle(),
            new PageBundle\PageBundle(),
            new ShortcodeBundle\ShortcodeBundle(),
            new MediaBundle\MediaBundle(),
            new ProductBundle\ProductBundle(),
            new ArticleBundle\ArticleBundle(),
//            new BookBundle\BookBundle(),
            new CommentBundle\CommentBundle(),
            new ShareBundle\ShareBundle(),
            new OrderBundle\OrderBundle(),
            new MainImageBundle\MainImageBundle(),
            new InformationBundle\InformationBundle(),
            new WheelSpinBundle\WheelSpinBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
        }

        return $bundles;
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
    }

    public function getLogDir()
    {
        return dirname(__DIR__).'/var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}