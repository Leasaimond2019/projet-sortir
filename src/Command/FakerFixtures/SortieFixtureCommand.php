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

use App\Entity\Sortie;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\User;
use App\Entity\Site;

class SortieFixtureCommand extends Command
{
    protected static $defaultName = 'app:fixtures:sortie';

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
        ->setDescription('Load fresh dummy data in sortie table')
        ->addArgument('num', InputArgument::OPTIONAL, 'Load how many?', 10)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $num = $input->getArgument('num');

        $io = new SymfonyStyle($input, $output);

        $this->truncateTable();

        $allEtatEntities = $this->doctrine->getRepository(Etat::class)->findAll();
        $allLieuEntities = $this->doctrine->getRepository(Lieu::class)->findAll();
        $allUserEntities = $this->doctrine->getRepository(User::class)->findAll();
        $allSiteEntities = $this->doctrine->getRepository(Site::class)->findAll();

        for($i=0; $i<$num; $i++){
            $sortie = new Sortie();
            $dateDeb = $this->faker->dateTimeBetween($startDate = "- 3 months", $endDate = "+ 3 months");
            $sortie->setNom(
                $this->faker->text(30)
            );
            $sortie->setDateDebut(
                $dateDeb
            );
            $sortie->setDuree(
                $this->faker->numberBetween($min = 1000, $max = 9000)
            );
            $datefin = clone $dateDeb;
            $datefin->add(new \DateInterval('PT' . $sortie->getDuree() . 'M'));

            $sortie->setDateFin(
                $datefin
            );
            $sortie->setDateCloture(
                $this->faker->dateTimeBetween("-3 months", $dateDeb)
            );
            $sortie->setNbInscriptionMax(
                $this->faker->numberBetween($min = 2, $max = 20)
            );
            $sortie->setDescription(
                $this->faker->text(300)
            );
            $sortie->setUrlPhoto(
                "http://www.stleos.uq.edu.au/wp-content/uploads/2016/08/image-placeholder-350x350.png"
            );
            $sortie->setMotif(
                $this->faker->text(255)
            );
            $sortie->setNoEtat(
                $this->faker->randomElement($allEtatEntities)
            );
            $sortie->setNoLieu(
                $this->faker->randomElement($allLieuEntities)
            );
            $sortie->setNoOrganisateur(
                $this->faker->randomElement($allUserEntities)
            );
            $sortie->setNoSite(
                $this->faker->randomElement($allSiteEntities)
            );

            $this->manager->persist($sortie);
        }

        $this->manager->flush();
        $io->writeln($num . ' "Sortie" loaded!');
        return 0;
    }

    protected function truncateTable()
    {
        $connection = $this->doctrine->getConnection();
        $connection->query("SET FOREIGN_KEY_CHECKS = 0");
        $connection->query("TRUNCATE TABLE sortie");
        $connection->query("SET FOREIGN_KEY_CHECKS = 1");
    }
}