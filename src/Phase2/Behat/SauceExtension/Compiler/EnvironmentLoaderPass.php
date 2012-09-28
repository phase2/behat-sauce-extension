<?php

namespace Phase2\Behat\SauceExtension\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Loads Selenium2 parameters from environment variables provided by the Sauce
 * OnDemand Jenkins plugin. https://wiki.jenkins-ci.org/display/JENKINS/Sauce+OnDemand+Plugin
 */
class EnvironmentLoaderPass implements CompilerPassInterface
{
    /**
     * Loads Selenium2 parameters from environment variables.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        // Generate the wd_host from env variables if available.
        if (getenv('SAUCE_USER_NAME')) {
          // Construct and set wd_host.
          $container->setParameter('behat.mink.selenium2.wd_host', sprintf("http://%s:%s@%s:%d/wd/hub",
            getenv('SAUCE_USER_NAME'),
            getenv('SAUCE_API_KEY'),
            getenv('SELENIUM_HOST'),
            getenv('SELENIUM_PORT')));
        }

        // Generate the browser capabilities from SELENIUM_DRIVER env variable if available.
        if ($driver = getenv('SELENIUM_DRIVER')) {
          // sauce-ondemand:?os=Linux&browser=firefox&browser-version=10
          $uri = parse_url($driver);
          if ($uri['scheme'] == 'sauce-ondemand') {
            $params = array();
            parse_str($uri['query'], $params);

            // Fill in the right capabilities.
            $container->setParameter('behat.mink.selenium2.capabilities', array(
              'version' => $params['browser-version'],
              'platform' => $params['os'],
            ));

            // Set the browser name.
            $container->setParameter('behat.mink.selenium2.browser', $params['browser']);
          }
        }
        // Otherwise, generate the browser capabilities from other env variables if available.
        elseif ($browser = getenv('SELENIUM_BROWSER')) {
          // Fill in the right capabilities.
          $container->setParameter('behat.mink.selenium2.capabilities', array(
            'version' => getenv('SELENIUM_VERSION'),
            'platform' => getenv('SELENIUM_PLATFORM'),
          ));

          // Set the browser name.
          $container->setParameter('behat.mink.selenium2.browser', $browser);
        }
    }
}
