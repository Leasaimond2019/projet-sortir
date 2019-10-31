<?php
namespace App\Command\FakerFixtures;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use App\Entity\Inscription;
use App\Entity\Sortie;
use App\Entity\User;

class InscriptionFixtureCommand extends Command
{
    protected static $defaultName = 'app:fixtures:inscription';

    protected $manager = null;
    protected $doctrine = null;
    protected $faker = null;
    protected $passwordEncoder = null;

    public function __construct(RegistryInterface $doctrine, UserPasswordEncoderInterface $passwordEncoder, $name = null)
    {
        parent::__construct($name);
        $this->manager = $doctrine->getManager();
        $this->doctrine = $doctrine;
        $this->faker = \Faker\Factory::create($locale = 'fr_FR');
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function configure()
    {
        $this
        ->setDescription('Load fresh dummy data in inscription table')
        ->addArgument('num', InputArgument::OPTIONAL, 'Load how many?', 10)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $num = $input->getArgument('num');

        $io = new SymfonyStyle($input, $output);

        $this->truncateTable();

        $allSortieEntities = $this->doctrine->getRepository(Sortie::class)->findAll();
        $allUserEntities = $this->doctrine->getRepository(User::class)->findAll();

        for($i=0; $i<$num; $i++){
            $inscription = new Inscription();

            $inscription->setDateInscription(
                $this->faker->dateTimeBetween($startDate = "- 3 months", $endDate = "now")
            );
            $inscription->setNoSortie(
                $this->faker->randomElement($allSortieEntities)
            );
            $inscription->setNoUser(
                $this->faker->randomElement($allUserEntities)
            );

            $this->manager->persist($inscription);
        }

        $this->manager->flush();
        $io->writeln($num . ' "Inscription" loaded!');
        return 0;
    }

    protected function truncateTable()
    {
        $connection = $this->doctrine->getConnection();
        $connection->query("SET FOREIGN_KEY_CHECKS = 0");
        $connection->query("TRUNCATE TABLE inscription");
        $connection->query("SET FOREIGN_KEY_CHECKS = 1");
    }
}