<?php

namespace Phase2\Behat\SauceExtension;

use Behat\Behat\Extension\Extension as BaseExtension;

/**
 * Sauce Labs extension for Behat class.
 */
class Extension extends BaseExtension {

  /**
   * Returns compiler passes used by this extension.
   *
   * @return array
   */
  public function getCompilerPasses()
  {
      return array(
        new Compiler\JenkinsPluginPass(),
      );
  }
}
