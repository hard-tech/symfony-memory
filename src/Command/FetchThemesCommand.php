<?php
namespace App\Command;

use App\Entity\Theme;
use App\Enum\ThemeType;
use App\Service\PixabayService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:fetch-themes',
    description: 'Fetch themes from Pixabay and store them in the database',
)]
class FetchThemesCommand extends Command
{
    private $pixabayService;
    private $entityManager;

    public function __construct(PixabayService $pixabayService, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->pixabayService = $pixabayService;
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('theme', InputArgument::REQUIRED, 'The theme to search for');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $themeName = $input->getArgument('theme');

        $params = [
            'q' => $themeName,
            'image_type' => 'photo',
            'per_page' => 10,
        ];

        $images = $this->pixabayService->searchImages($params);

        foreach ($images as $image) {
            $theme = new Theme();
            $theme->setName($themeName);
            $theme->setUrl($image['webformatURL']);
            $theme->setType(ThemeType::IMAGE); // Assuming all fetched items are of type 'image'

            $this->entityManager->persist($theme);
        }

        $this->entityManager->flush();

        $output->writeln('Themes have been successfully fetched and stored.');

        return Command::SUCCESS;
    }
}