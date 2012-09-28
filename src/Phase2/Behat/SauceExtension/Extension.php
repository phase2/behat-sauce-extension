<?php

namespace Phase2\Behat\SauceExtension;

use Behat\Behat\Extension\Extension as BaseExtension;

use Symfony\Component\Config\FileLocator,
    Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * Sauce Labs extension for Behat class.
 */
class Extension extends BaseExtension {

  /**
   * Loads a specific configuration.
   *
   * @param array $config Extension configuration hash (from behat.yml)
   * @param ContainerBuilder $container ContainerBuilder instance
   */
  public function load(array $config, ContainerBuilder $container) {
    $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/services'));
    $loader->load('services.xml');
  }

  /**
   * Returns compiler passes used by this extension.
   *
   * @return array
   */
  public function getCompilerPasses()
  {
      return array(
        new Compiler\EnvironmentLoaderPass(),
      );
  }
}
