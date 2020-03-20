<?php

namespace Alex\MultisiteBundle\Twig;

use Alex\MultisiteBundle\Branding\SiteContext;
use Twig\Error\LoaderError;
use Twig\Loader\FilesystemLoader;
use Twig\Loader\LoaderInterface;

/**
 * Wraps a Twig Loader and extends template syntax
 */
class MultisiteLoader extends FilesystemLoader
{
    /**
     * @var FilesystemLoader
     */
    protected $loader;

    /**
     * @var SiteContext
     */
    protected $siteContext;

    /**
     * Constructs the loader.
     *
     * @param LoaderInterface $loader a twig loader
     * @param SiteContext $siteContext
     */
    public function __construct(LoaderInterface $loader, SiteContext $siteContext)
    {
        parent::__construct();

        $this->loader      = $loader;
        $this->siteContext = $siteContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getSource($name)
    {
        $templates = $this->getTemplates($name);

        foreach ($templates as $template) {
            try {
                return $this->loader->getSource($template);
            } catch (LoaderError $e) {
            }
        }

        throw new LoaderError(sprintf("Template \"%s\" not found. Tried the following:\n%s", $name, implode("\n", $templates)));
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceContext($name)
    {
        $templates = $this->getTemplates($name);

        foreach ($templates as $template) {
            try {
                return $this->loader->getSourceContext($template);
            } catch (LoaderError $e) {
            }
        }

        throw new LoaderError(sprintf("Template \"%s\" not found. Tried the following:\n%s", $name, implode("\n", $templates)));
    }

    /**
     * {@inheritdoc}
     */
    protected function findTemplate($name, $throw = true)
    {
        return $this->loader->findTemplate($name, $throw);
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheKey($name)
    {
        $templates = $this->getTemplates($name);

        foreach ($templates as $template) {
            try {
                return $this->loader->getCacheKey($template);
            } catch (LoaderError $e) {
            }
        }

        throw new LoaderError(sprintf("Template \"%s\" not found. Tried the following:\n%s", $name, implode("\n", $templates)));
    }

    /**
     * {@inheritdoc}
     */
    public function isFresh($name, $time)
    {
        $templates = $this->getTemplates($name);

        foreach ($templates as $template) {
            try {
                return $this->loader->isFresh($template, $time);
            } catch (LoaderError $e) {
            }
        }

        throw new LoaderError(sprintf("Template \"%s\" not found. Tried the following:\n%s", $name, implode("\n", $templates)));
    }

    /**
     * {@inheritdoc}
     */
    protected function getTemplates($name)
    {
        $posA = strrpos($name, ':');
        $posB = strrpos($name, '/');
        $posC = strrpos($name, '/');

        $b = $this->siteContext->getCurrentBrandingName();
        $l = $this->siteContext->getCurrentLocale();

        if ($posA === false && $posB === false && $posC === false) {
            $prefix = '';
            $suffix = '/'.$name;
        } else {
            $pos = max($posA, $posB, $posC);
            $prefix = substr($name, 0, $pos + 1);
            $suffix = '/'.substr($name, $pos + 1);
        }

        return array(
            $prefix.'_'.$b.'_'.$l.''.$suffix,
            $prefix.'_'.$b.'_'.$suffix,
            $prefix.'__'.$l.''.$suffix,
            $name
        );
    }
}
