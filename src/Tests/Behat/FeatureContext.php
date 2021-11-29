<?php

namespace AcMarche\Mercredi\Tests\Behat;

use AcMarche\Mercredi\Utils\DateUtils;
use Behat\MinkExtension\Context\RawMinkContext;
use Carbon\Carbon;
use Exception;

class FeatureContext extends RawMinkContext
{
    /**
     * @Given I am logged in as an admin
     */
    public function iAmLoggedInAsAnAdmin(): void
    {
        $this->visitPath('/login');
        //var_dump($this->getSession()->getPage()->getContent());
        $this->fillField('username', 'jf@marche.be');
        $this->fillField('password', 'homer');
        $this->pressButton('Me connecter');
    }

    /**
     * Given I am logged in as user :username.
     *
     * @Given /^I am logged in as user "([^"]*)"$/
     */
    public function iAmLoggedInAsUser(string $username): void
    {
        $this->visitPath('/login');
        $this->fillField('username', $username);
        $this->fillField('password', 'homer');
        $this->pressButton('Me connecter');
    }

    /**
     * @When /^I am login with user "([^"]*)" and password "([^"]*)"$/
     */
    public function iAmLoginWithUserAndPassword(string $email, string $password): void
    {
        $this->visitPath('/login');
        $this->fillField('username', $email);
        $this->fillField('password', $password);
        $this->pressButton('Me connecter');
    }

    /**
     * @When /^I select day plus "(\d+)" from "(?P<select>(?:[^"]|\\")*)"$/
     */
    public function iSelectDayPlusFrom($nbDays, $select): void
    {
        $today = Carbon::today();
        $today->addDays($nbDays);
        $today = ucfirst(DateUtils::formatFr($today));
        $select = $this->fixStepArgument($select);
        $option = $this->fixStepArgument($today);
        $this->getSession()->getPage()->selectFieldOption($select, $option);
    }

    /**
     * @When /^I additionally select day plus "(\d+)" from "(?P<select>(?:[^"]|\\")*)"$/
     */
    public function iAdditionallySelectDayPlusFrom($nbDays, $select): void
    {
        $today = Carbon::today();
        $today->addDays($nbDays);
        $today = ucfirst(DateUtils::formatFr($today));
        $select = $this->fixStepArgument($select);
        $option = $this->fixStepArgument($today);
        $this->getSession()->getPage()->selectFieldOption($select, $option, true);
    }

    /**
     * Selects additional option in select field with specified id|name|label|value
     * Example: When I additionally select "Deceased" from "parents_alive_status"
     * Example: And I additionally select "Deceased" from "parents_alive_status".
     *
     * @When /^(?:|I )ad222ditionally select "(?P<option>(?:[^"]|\\")*)" from "(?P<select>(?:[^"]|\\")*)"$/
     */
    public function additionallySelectOption($select, $option): void
    {
    }

    /**
     * Selects option in select field with specified id|name|label|value
     * Example: When I select "Bats" from "user_fears"
     * Example: And I select "Bats" from "user_fears".
     *
     * @When /^(?:|I )sel222ect date "(?P<option>(?:[^"]|\\")*)" from "(?P<select>(?:[^"]|\\")*)"$/
     */
    public function selectOption($select, $nbDays): void
    {
    }

    /**
     * @Given I fill the periodicity endTime with the date :day/:month/:year
     */
    public function iFillEndTimePeridocity(int $day, int $month, int $year): void
    {
        $this->fillField('entry_with_periodicity[periodicity][endTime][day]', $day);
        $this->fillField('entry_with_periodicity[periodicity][endTime][month]', $month);
        $this->fillField('entry_with_periodicity[periodicity][endTime][year]', $year);
    }

    /**
     * @Given /^I fill the periodicity endTime with this month and day (\d+) and year (\d+)$/
     */
    public function iFillEndTimePeridocityThisMonth(int $day, int $year): void
    {
        $today = Carbon::today();

        $this->fillField('entry_with_periodicity[periodicity][endTime][day]', $day);
        $this->fillField('entry_with_periodicity[periodicity][endTime][month]', $today->month);
        $this->fillField('entry_with_periodicity[periodicity][endTime][year]', $year);
    }

    /**
     * @Given I fill the periodicity endTime with later date
     */
    public function iFillEndTimePeridocityLater(): void
    {
        $today = Carbon::today();
        $today->addDays(3);

        $this->fillField('entry_with_periodicity[periodicity][endTime][day]', $today->day);
        $this->fillField('entry_with_periodicity[periodicity][endTime][month]', $today->month);
        $this->fillField('entry_with_periodicity[periodicity][endTime][year]', $today->year);
    }

    /**
     * @Given I fill the entry startTime with the date :day/:month/:year
     */
    public function iFillDateBeginEntry(int $day, int $month, int $year): void
    {
        $this->fillField('entry_with_periodicity_startTime_date_day', $day);
        $this->fillField('entry_with_periodicity_startTime_date_month', $month);
        $this->fillField('entry_with_periodicity_startTime_date_year', $year);
    }

    /**
     * @Given I fill the entry startTime with today :hour::minute
     */
    public function iFillDateBeginEntryWithToday(int $hour, int $minute): void
    {
        $today = Carbon::today();
        $this->fillField('entry_with_periodicity_startTime_date_day', $today->day);
        $this->fillField('entry_with_periodicity_startTime_date_month', $today->month);
        $this->fillField('entry_with_periodicity_startTime_date_year', $today->year);
        $this->fillField('entry_with_periodicity_startTime_time_hour', $hour);
        $this->fillField('entry_with_periodicity_startTime_time_minute', $minute);
    }

    /**
     * @Given /^I fill the entry startTime with this month and day (\d+) and year (\d+) at time (\d+):(\d+)$/
     */
    public function iFillDateBeginEntryWithThisMonth(int $day, int $year, int $hour, int $minute): void
    {
        $today = Carbon::today();
        $this->fillField('entry_with_periodicity_startTime_date_day', $day);
        $this->fillField('entry_with_periodicity_startTime_date_month', $today->month);
        $this->fillField('entry_with_periodicity_startTime_date_year', $year);
        $this->fillField('entry_with_periodicity_startTime_time_hour', $hour);
        $this->fillField('entry_with_periodicity_startTime_time_minute', $minute);
    }

    /**
     * Clicks link semaine
     * Example: When I follow this week.
     *
     * @Then /^I follow this week$/
     */
    public function clickLinkWeek(): void
    {
        $link = 's' . Carbon::today()->week;
        $link = $this->fixStepArgument($link);
        $this->getSession()->getPage()->clickLink($link);
    }

    /**
     * Clicks link day
     * Example: When I follow this day.
     *
     * @Then /^I follow this day$/
     */
    public function clickLinkDay(): void
    {
        $link = Carbon::today()->day;
        $link = $this->fixStepArgument($link);
        $this->getSession()->getPage()->clickLink($link);
    }

    /**
     * @Then /^I should see "([^"]*)" exactly "([^"]*)" times$/
     *
     * @throws Exception
     */
    public function iShouldSeeTextSoManyTimes($sText, $iExpected): void
    {
        $sContent = $this->getSession()->getPage()->getText();
        $iFound = substr_count($sContent, $sText);
        if ($iExpected !== $iFound) {
            throw new Exception('Found ' . $iFound . ' occurences of "' . $sText . '" when expecting ' . $iExpected);
        }
    }

    /**
     * @return mixed[]|string
     */
    protected function fixStepArgument($argument)
    {
        return str_replace('\\"', '"', $argument);
    }

    private function fillField(string $field, string $value): void
    {
        $this->getSession()->getPage()->fillField($field, $value);
    }

    private function pressButton($button): void
    {
        $button = $this->fixStepArgument($button);
        $this->getSession()->getPage()->pressButton($button);
    }
}
