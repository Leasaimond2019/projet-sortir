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

use App\Entity\Lieu;
use App\Entity\Ville;

class LieuFixtureCommand extends Command
{
    protected static $defaultName = 'app:fixtures:lieu';

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
        ->setDescription('Load fresh dummy data in lieu table')
        ->addArgument('num', InputArgument::OPTIONAL, 'Load how many?', 10)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $num = $input->getArgument('num');

        $io = new SymfonyStyle($input, $output);

        $this->truncateTable();

        $allVilleEntities = $this->doctrine->getRepository(Ville::class)->findAll();

        for($i=0; $i<$num; $i++){
            $lieu = new Lieu();

            $lieu->setNomLieu(
                $this->faker->text(30)
            );
            $lieu->setRue(
                $this->faker->text(30)
            );
            $lieu->setLatitude(
                $this->faker->latitude(41.3166667,51.0716667)
            );
            $lieu->setLongitude(
                $this->faker->longitude(-5.151,9.56)
            );
            $lieu->setNoVille(
                $this->faker->randomElement($allVilleEntities)
            );

            $this->manager->persist($lieu);
        }

        $this->manager->flush();
        $io->writeln($num . ' "Lieu" loaded!');
        return 0;
    }

    protected function truncateTable()
    {
        $connection = $this->doctrine->getConnection();
        $connection->query("SET FOREIGN_KEY_CHECKS = 0");
        $connection->query("TRUNCATE TABLE lieu");
        $connection->query("SET FOREIGN_KEY_CHECKS = 1");
    }
}