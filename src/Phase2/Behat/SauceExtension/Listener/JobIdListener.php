<?php

namespace Phase2\Behat\SauceExtension\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Behat\Behat\Event\ScenarioEvent,
    Behat\Behat\Event\OutlineEvent;

/**
 * Find the Sauce Job ID and print it.
 */
class JobIdListener implements EventSubscriberInterface
{
    private $job_id;

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
            'afterOutline'   => array('findId', -10)
        );
    }

    /**
     * Find the job ID from the context.
     *
     * @param ScenarioEvent|OutlineEvent $event
     */
    public function findId($event)
    {
        $scenario = $event instanceof ScenarioEvent ? $event->getScenario() : $event->getOutline();
        $context = $event->getContext();

        if (empty($this->job_id)) {
          $url = $context->getSession()->getDriver()->wdSession->getUrl();
          $parts = explode('/', $url);
          $this->job_id = array_pop($parts);

          printf('SauceOnDemandSessionID=%s job-name=%s', $this->job_id, getenv('BUILD_TAG'));
        }
    }

}
