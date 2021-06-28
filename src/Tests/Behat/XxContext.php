<?php

namespace AcMarche\Mercredi\Tests\Behat;

use Behat\Mink\Element\DocumentElement;
use Doctrine\ORM\EntityManager;
use Behat\Mink\Element\NodeElement;
use Behat\Gherkin\Node\TableNode;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;

/**
 * Defines application features from the specific context.
 */
class XxContext
{
    private $currentUser;
    private EntityManagerInterface $entityManager;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @BeforeScenario
     */
    public function clearData(): void
    {
        $purger = new ORMPurger($this->entityManager);
        $purger->purge();
    }

    /**
     * @BeforeScenario @fixtures
     */
    public function loadFixtures(): void
    {
        $loader = new ContainerAwareLoader($this->getContainer());
        $loader->loadFromDirectory(__DIR__.'/../../src/AppBundle/DataFixtures');
        $executor = new ORMExecutor($this->getEntityManager());
        $executor->execute($loader->getFixtures(), true);
    }

    /**
     * @Given there is an admin user :username with password :password
     */
    public function thereIsAnAdminUserWithPassword($username, $password): \App\Entity\Security\User
    {
        $user = new \App\Entity\Security\User();
        $user->setUsername($username);
        $user->setPlainPassword($password);
        $user->setRoles(['ROLE_ADMIN']);

        //  $em->persist($user);
        //  $em->flush();

        return $user;
    }

    /**
     * @When I fill in the search box with :term
     */
    public function iFillInTheSearchBoxWith($term): void
    {
        $searchBox = $this->assertSession()
            ->elementExists('css', 'input[name="searchTerm"]');

        $searchBox->setValue($term);
    }

    /**
     * @When I press the search button
     */
    public function iPressTheSearchButton(): void
    {
        $button = $this->assertSession()
            ->elementExists('css', '#search_submit');

        $button->press();
    }

    /**
     * @Given there is/are :count product(s)
     */
    public function thereAreProducts($count): void
    {
        $this->createProducts($count);
    }

    /**
     * @Given I author :count products
     */
    public function iAuthorProducts($count): void
    {
        $this->createProducts($count, $this->currentUser);
    }

    /**
     * @Given the following product(s) exist(s):
     */
    public function theFollowingProductsExist(TableNode $table): void
    {
        foreach ($table as $row) {
            $product = new Product();
            $product->setName($row['name']);
            $product->setPrice(rand(10, 1000));
            $product->setDescription('lorem');

            if (isset($row['is published']) && 'yes' === $row['is published']) {
                $product->setIsPublished(true);
            }

            $this->getEntityManager()->persist($product);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @Then the :rowText row should have a check mark
     */
    public function theProductRowShouldShowAsPublished($rowText): void
    {
        $row = $this->findRowByText($rowText);

        assertContains('fa-check', $row->getHtml(), 'Could not find the fa-check element in the row!');
    }

    /**
     * @When I press :linkText in the :rowText row
     */
    public function iClickInTheRow($linkText, $rowText): void
    {
        $this->findRowByText($rowText)->pressButton($linkText);
    }

    /**
     * @When I click :linkName
     */
    public function iClick($linkName): void
    {
        $this->getPage()->clickLink($linkName);
    }

    /**
     * @Then I should see :count products
     */
    public function iShouldSeeProducts($count): void
    {
        $table = $this->getPage()->find('css', 'table.table');
        assertNotNull($table, 'Cannot find a table!');

        assertCount((int) $count, $table->findAll('css', 'tbody tr'));
    }

    /**
     * @Given I am logged in as an admin
     */
    public function iAmLoggedInAsAnAdmin(): void
    {
        $this->currentUser = $this->thereIsAnAdminUserWithPassword('admin', 'admin');

        $this->visitPath('/login');
        $this->getPage()->fillField('Username', 'admin');
        $this->getPage()->fillField('Password', 'admin');
        $this->getPage()->pressButton('Login');
    }

    /**
     * @When I wait for the modal to load
     */
    public function iWaitForTheModalToLoad(): void
    {
        $this->getSession()->wait(
            5000,
            "$('.modal:visible').length > 0"
        );
    }

    /**
     * Pauses the scenario until the user presses a key. Useful when debugging a scenario.
     *
     * @Then (I )break
     */
    public function iPutABreakpoint(): void
    {
        fwrite(STDOUT, "\033[s    \033[93m[Breakpoint] Press \033[1;93m[RETURN]\033[0;93m to continue...\033[0m");
        while ('' === fgets(STDIN, 1024)) {
        }
        fwrite(STDOUT, "\033[u");

        return;
    }

    /**
     * Saving a screenshot.
     *
     * @When I save a screenshot to :filename
     */
    public function iSaveAScreenshotIn($filename): void
    {
        sleep(1);
        $this->saveScreenshot($filename, __DIR__.'/../sallessf');
    }

    private function getPage(): DocumentElement
    {
        return $this->getSession()->getPage();
    }

    private function getEntityManager(): EntityManager
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }

    private function createProducts($count, ?User $author = null): void
    {
        for ($i = 0; $i < $count; ++$i) {
            $product = new Product();
            $product->setName('Product '.$i);
            $product->setPrice(rand(10, 1000));
            $product->setDescription('lorem');

            if ($author !== null) {
                $product->setAuthor($author);
            }

            $this->getEntityManager()->persist($product);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @param $rowText
     */
    private function findRowByText($rowText): ?NodeElement
    {
        $row = $this->getPage()->find('css', sprintf('table tr:contains("%s")', $rowText));
        assertNotNull($row, 'Cannot find a table row with this text!');

        return $row;
    }
}
