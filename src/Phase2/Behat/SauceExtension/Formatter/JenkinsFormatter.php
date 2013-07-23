<?php

namespace Phase2\Behat\SauceExtension\Formatter;

use Behat\Behat\Event\ScenarioEvent;
use Behat\Behat\Event\SuiteEvent;
use Behat\Behat\Formatter\ConsoleFormatter;

/**
 * Formatter for printing the Sauce job ID in a format readable by the Sauce
 * OnDemand Jenkins plugin.
 *
 * @see https://wiki.jenkins-ci.org/display/JENKINS/Sauce+OnDemand+Plugin#SauceOnDemandPlugin-EmbeddedTestReports
 *
 * @author Roger López <rlopez@phase2technology.com>
 */
class JenkinsFormatter extends ConsoleFormatter
{
    /**
     * The detected job id.
     */
    private $jobId = 0;

    /**
     * {@inheritdoc}
     */
    protected function getDefaultParameters()
    {
        return array();
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            'afterScenario'  => array('findId', -10),
            'afterOutline'   => array('findId', -10),
            'afterSuite'     => 'afterSuite',
        );
    }

    /**
     * Find the job ID from the context.
     *
     * @param ScenarioEvent|OutlineEvent $event
     */
    public function findId($event)
    {
        if (empty($this->jobId)) {
            $scenario = $event instanceof ScenarioEvent ? $event->getScenario() : $event->getOutline();
            $context = $event->getContext();
            $url = $context->getSession()->getDriver()->getWebDriverSession()->getUrl();
            $parts = explode('/', $url);
            $this->jobId = array_pop($parts);
        }
    }

    /**
     * Listens to "suite.after" event.
     *
     * @param SuiteEvent $event
     */
    public function afterSuite(SuiteEvent $event)
    {
        $this->writeln(
            sprintf(
                "{+comment}  SauceOnDemandSessionID=%s job-name=%s{-comment}",
                $this->jobId,
                (($name = getenv('BUILD_TAG')) ? $name : 'Behat')
            )
        );
    }
}
