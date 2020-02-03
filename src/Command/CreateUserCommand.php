<?php


namespace App\Command;


use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateUserCommand extends Command
{
	protected static $defaultName = 'app:create-user';
	private $em;
	private $userRepository;
	private $userPasswordEncoder;

	/**
	 * CreateUserCommand constructor.
	 * @param EntityManagerInterface $em
	 * @param UserPasswordEncoderInterface $userPasswordEncoder
	 * @param UserRepository $userRepository
	 */
	public function __construct(
		EntityManagerInterface $em,
		UserPasswordEncoderInterface $userPasswordEncoder,
		UserRepository $userRepository)
	{
		$this->em = $em;
		$this->userRepository = $userRepository;
		$this->userPasswordEncoder = $userPasswordEncoder;
		parent::__construct();
	}

	/**
	 *
	 */
	protected function configure()
	{
		$this->setDescription('Este comando permite la creación de usuarios');
		$this->setHelp('Este comando permite la creación de usuarios');
		$this->addArgument('username', InputArgument::REQUIRED, 'admins\'s username');
		$this->addArgument('nombre', InputArgument::REQUIRED, 'admins\'s nombre');
		$this->addArgument('password', InputArgument::REQUIRED, 'admins\'s password');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln('<fg=white;bg=cyan>Creación de Usuarios</>');
		$username = $input->getArgument('username');
		$nombre = $input->getArgument('nombre');
		$plainPassword = $input->getArgument('password');
		$user = $this->userRepository->findOneByUsername($username);
		if (!empty($user)) {
			$output->writeln('usuario ya registrado ');
			return;
		}

		$user = new User();
		$user->setUsername($username);
		$user->setNombre($nombre);
		$password = $this->userPasswordEncoder->encodePassword($user,$plainPassword);
		$user->setPassword($password);
		$this->em->persist($user);
		$this->em->flush();
		$output->writeln('<fg=white;bg=green>Usuario Creado!</>');
		return;

	}
}