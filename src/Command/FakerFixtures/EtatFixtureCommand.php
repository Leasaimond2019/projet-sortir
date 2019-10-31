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

use App\Entity\Etat;

class EtatFixtureCommand extends Command
{
    protected static $defaultName = 'app:fixtures:etat';

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
        ->setDescription('Load fresh dummy data in etat table')
        ->addArgument('num', InputArgument::OPTIONAL, 'Load how many?', 10)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $num = $input->getArgument('num');

        $io = new SymfonyStyle($input, $output);

        $this->truncateTable();

        $etatTable[0]="Créée";
        $etatTable[1]="Ouverte";
        $etatTable[2]="Clôturée";
        $etatTable[3]="Activité en
cours";
        $etatTable[4]="Passée";
        $etatTable[5]="Annulée";
        $etatTable[6]="Archivée";

        for($i=0; $i<$num; $i++){
            $etat = new Etat();

            $etat->setLibelle(
                $etatTable[$i]
            );

            $this->manager->persist($etat);
        }

        $this->manager->flush();
        $io->writeln($num . ' "Etat" loaded!');
        return 0;
    }

    protected function truncateTable()
    {
        $connection = $this->doctrine->getConnection();
        $connection->query("SET FOREIGN_KEY_CHECKS = 0");
        $connection->query("TRUNCATE TABLE etat");
        $connection->query("SET FOREIGN_KEY_CHECKS = 1");
    }
}