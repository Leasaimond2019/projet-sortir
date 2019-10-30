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

use App\Entity\User;
use App\Entity\Site;

class UserFixtureCommand extends Command
{
    protected static $defaultName = 'app:fixtures:user';

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
        ->setDescription('Load fresh dummy data in user table')
        ->addArgument('num', InputArgument::OPTIONAL, 'Load how many?', 10)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $num = $input->getArgument('num');

        $io = new SymfonyStyle($input, $output);

        $this->truncateTable();

        $allSiteEntities = $this->doctrine->getRepository(Site::class)->findAll();

        for($i=0; $i<$num; $i++){
            $user = new User();

            $user->setPseudo(
                $this->faker->unique()->userName
            );
            //roles
            $user->setRoles(
                [$this->faker->randomElement(["ROLE_USER", "ROLE_ADMIN"])]
            );
            //password
            $plainPassword = "123";
            $hash = $this->passwordEncoder->encodePassword($user, $plainPassword);
            $user->setPassword($hash);
            $user->setNom(
                $this->faker->lastName()
            );
            $user->setPrenom(
                $this->faker->firstName()
            );
            $user->setTelephone(
                $this->faker->optional($chancesOfValue = 0.5, $default = null)->text(15)
            );
            $user->setMail(
                $this->faker->email
            );
            $user->setActif(
                $this->faker->boolean($chanceOfGettingTrue = 50)
            );
            $user->setNoSite(
                $this->faker->randomElement($allSiteEntities)
            );

            $this->manager->persist($user);
        }

        $this->manager->flush();
        $io->writeln($num . ' "User" loaded!');
        return 0;
    }

    protected function truncateTable()
    {
        $connection = $this->doctrine->getConnection();
        $connection->query("SET FOREIGN_KEY_CHECKS = 0");
        $connection->query("TRUNCATE TABLE user");
        $connection->query("SET FOREIGN_KEY_CHECKS = 1");
    }
}