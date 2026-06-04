<?php

return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    Symfony\Bundle\SecurityBundle\SecurityBundle::class => ['all' => true],
    Symfony\Bundle\TwigBundle\TwigBundle::class => ['all' => true],
    Symfony\Bundle\MonologBundle\MonologBundle::class => ['all' => true],
    Symfony\Bundle\WebProfilerBundle\WebProfilerBundle::class => ['dev' => true, 'test' => true],
    Symfony\Bundle\DebugBundle\DebugBundle::class => ['dev' => true, 'test' => true],

    Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class => ['all' => true],
    Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle::class => ['all' => true],

    Oneup\UploaderBundle\OneupUploaderBundle::class => ['all' => true],
    Knp\Bundle\GaufretteBundle\KnpGaufretteBundle::class => ['all' => true],
    Vich\UploaderBundle\VichUploaderBundle::class => ['all' => true],

    Symfony\Cmf\Bundle\RoutingBundle\CmfRoutingBundle::class => ['all' => true],

    Sonata\Doctrine\Bridge\Symfony\SonataDoctrineBundle::class => ['all' => true],
    Sonata\Twig\Bridge\Symfony\SonataTwigBundle::class => ['all' => true],
    Sonata\Form\Bridge\Symfony\SonataFormBundle::class => ['all' => true],
    Sonata\BlockBundle\SonataBlockBundle::class => ['all' => true],
    Knp\Bundle\MenuBundle\KnpMenuBundle::class => ['all' => true],
    Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle::class => ['all' => true],
    Sonata\AdminBundle\SonataAdminBundle::class => ['all' => true],
    Sonata\IntlBundle\SonataIntlBundle::class => ['all' => true],
    Sonata\PageBundle\SonataPageBundle::class => ['all' => true],
    Cocur\Slugify\Bridge\Symfony\CocurSlugifyBundle::class => ['all' => true],
    Sonata\SeoBundle\SonataSeoBundle::class => ['all' => true],
    Sonata\UserBundle\SonataUserBundle::class => ['all' => true],

    Lexik\Bundle\TranslationBundle\LexikTranslationBundle::class => ['all' => true],
    A2lix\AutoFormBundle\A2lixAutoFormBundle::class => ['all' => true],
    A2lix\TranslationFormBundle\A2lixTranslationFormBundle::class => ['all' => true],

    Symfony\UX\StimulusBundle\StimulusBundle::class => ['all' => true],

    Knp\DoctrineBehaviors\DoctrineBehaviorsBundle::class => ['all' => true],

    Liip\ImagineBundle\LiipImagineBundle::class => ['all' => true],
    FOS\CKEditorBundle\FOSCKEditorBundle::class => ['all' => true],

    BabDev\PagerfantaBundle\BabDevPagerfantaBundle::class => ['all' => true],

    // Sentry (disabled)
    // Sentry\SentryBundle\SentryBundle::class => ['all' => true],

    // Application bundles
    UserBundle\UserBundle::class => ['all' => true],
    AdminBundle\AdminBundle::class => ['all' => true],
    AppBundle\AppBundle::class => ['all' => true],
    PageBundle\PageBundle::class => ['all' => true],
    ShortcodeBundle\ShortcodeBundle::class => ['all' => true],
    MediaBundle\MediaBundle::class => ['all' => true],
    ProductBundle\ProductBundle::class => ['all' => true],
    ArticleBundle\ArticleBundle::class => ['all' => true],
    CommentBundle\CommentBundle::class => ['all' => true],
    ShareBundle\ShareBundle::class => ['all' => true],
    OrderBundle\OrderBundle::class => ['all' => true],
    MainImageBundle\MainImageBundle::class => ['all' => true],
    InformationBundle\InformationBundle::class => ['all' => true],
    WheelSpinBundle\WheelSpinBundle::class => ['all' => true],
];