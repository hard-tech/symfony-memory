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
use Symfony\Component\Console\Exception\RuntimeException;

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
            'per_page' => 16,
        ];

        $images = $this->pixabayService->searchImages($params);

        // Vérification si le nombre d'images retournées est bien de 16
        if (count($images) < 16) {
            throw new RuntimeException('Erreur: Moins de 16 images ont été récupérées.');
        }

        foreach ($images as $image) {
            $theme = new Theme();
            $theme->setName($themeName);
            $theme->setUrl($image['webformatURL']);
            $theme->setType(ThemeType::IMAGE); // Tous les éléments sont considérés comme des images

            $this->entityManager->persist($theme);
        }

        $this->entityManager->flush();

        $output->writeln('Les thèmes ont été récupérés et stockés avec succès.');

        return Command::SUCCESS;
    }
}
