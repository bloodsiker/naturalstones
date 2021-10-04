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
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Oneup\UploaderBundle\OneupUploaderBundle(),
            new Knp\Bundle\GaufretteBundle\KnpGaufretteBundle(),
            new Vich\UploaderBundle\VichUploaderBundle(),

            // Doctrine Migrations bundle
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),

            // Sonata AdminBundle and it dependencies
            new Sonata\CoreBundle\SonataCoreBundle(),
            new Sonata\BlockBundle\SonataBlockBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
            new Sonata\AdminBundle\SonataAdminBundle(),

            // Sonata Intl bundle
            new Sonata\IntlBundle\SonataIntlBundle(),

            // Sonata PageBundle ant it dependencies
            new Sonata\PageBundle\SonataPageBundle(),
            new Sonata\CacheBundle\SonataCacheBundle(),
            new Sonata\NotificationBundle\SonataNotificationBundle(),
            new Cocur\Slugify\Bridge\Symfony\CocurSlugifyBundle(),

            // Sonata Seo bundle
            new Sonata\SeoBundle\SonataSeoBundle(),

            // Sonata UserBundle and it dependencies
            new FOS\UserBundle\FOSUserBundle(),
            new Sonata\UserBundle\SonataUserBundle(),

            // Sonata EasyExtendsBundle
            new Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),

//            // Doctrine2 Behaviors
//            new Knp\DoctrineBehaviors\Bundle\DoctrineBehaviorsBundle(),

            // Image manipulations
            new Liip\ImagineBundle\LiipImagineBundle(),

            // CKEditor integration
            new FOS\CKEditorBundle\FOSCKEditorBundle(),

            // User bundle
            new UserBundle\UserBundle(),

            // Custom admin controllers, templates, etc.
            new AdminBundle\AdminBundle(),

            // Pagerfanta Bundle
            new WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle(),

            // Application bundles
            new AppBundle\AppBundle(),
            new PageBundle\PageBundle(),
            new MediaBundle\MediaBundle(),
            new GenreBundle\GenreBundle(),
            new ProductBundle\ProductBundle(),
            new BookBundle\BookBundle(),
            new CommentBundle\CommentBundle(),
            new ShareBundle\ShareBundle(),
            new OrderBundle\OrderBundle(),
            new QuizBundle\QuizBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();

            if ('dev' === $this->getEnvironment()) {
                $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
                $bundles[] = new Symfony\Bundle\WebServerBundle\WebServerBundle();
            }
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
        $loader->load(function (ContainerBuilder $container) {
            $container->setParameter('container.autowiring.strict_mode', true);
            $container->setParameter('container.dumper.inline_class_loader', true);

            $container->addObjectResource($this);
        });
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
