<?php

namespace Phase2\Behat\SauceExtension\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Loads Selenium2 parameters from environment variables provided by the Sauce
 * OnDemand Jenkins plugin. https://wiki.jenkins-ci.org/display/JENKINS/Sauce+OnDemand+Plugin
 */
class JenkinsPluginPass implements CompilerPassInterface
{
    /**
     * Loads Selenium2 parameters from environment variables.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (getenv('SAUCE_USER_NAME')) {
          // Construct and set wd_host.
          // TODO: SELENIUM_HOST AND SELENIUM_PORT get set to the values for the
          // Sauce-Connect proxy, which doesn't work when running the job on a
          // slave, and the slave cannot access Sauce-Connect on the Jenkins
          // Master.  Ultimately, there should be an option to send commands
          // through Sauce-Connect or directly to Sauce Selenium server.
          $container->setParameter('behat.mink.selenium2.wd_host', sprintf("http://%s:%s@%s:%d/wd/hub",
            getenv('SAUCE_USER_NAME'),
            getenv('SAUCE_API_KEY'),
            'ondemand.saucelabs.com', 80));

          // Set the browser name.
          $container->setParameter('behat.mink.selenium2.browser', getenv('SELENIUM_BROWSER'));

          // Fill in the right capabilities.
          $container->setParameter('behat.mink.selenium2.capabilities', array(
            'version' => getenv('SELENIUM_VERSION'),
            'platform' => getenv('SELENIUM_PLATFORM'),
          ));
        }
    }
}