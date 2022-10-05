<?php

use Behat\Behat\Context\Step\When;

use Behat\MinkExtension\Context\MinkContext;

/**
 * Features context.
 */
class FeatureContext extends MinkContext
{
    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        // Initialize your context here
    }

    /**
     * @Given /^I wait "([^"]*)" seconds$/
     */
    public function iWaitSeconds($seconds)
    {
        sleep($seconds);
    }

    /**
     * @Given /^I am on the main page$/
     */
    public function iAmOnTheMainPage()
    {
        return array(
            new Behat\Behat\Context\Step\Given(sprintf('I am on "%s"', 'mod.php?M=user_MediaM1')),
        );
    }

    /**
     * @Given /^I am logged in as "([^"]*)" with password "([^"]*)"$/
     */
    public function iAmLoggedInAsWithPassword($username, $password)
    {
        $this->loginUrl = 'http://media.fab/';
        return array(
            new Behat\Behat\Context\Step\Given(sprintf('I am on "%s"', $this->loginUrl)),
            new When(sprintf('I fill in "Password" with "%s"', $password)),
            new When('I press "Identification"'),
        );
    }
}
