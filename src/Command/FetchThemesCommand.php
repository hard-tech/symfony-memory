<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\PixabayService;

#[AsCommand(
    name: 'app:fetch-themes',
    description: 'Fetch themes from Pixabay',
)]
class FetchThemesCommand extends Command
{
    private $pixabayService;

    public function __construct(PixabayService $pixabayService)
    {
        parent::__construct();
        $this->pixabayService = $pixabayService;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('theme', InputArgument::REQUIRED, 'The theme to search for');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $theme = $input->getArgument('theme');
        $output->writeln("Fetching themes from Pixabay for: $theme");

        $results = $this->pixabayService->searchImages([
            'q' => $theme,
            'per_page' => 10  // Example: limit to 10 results
        ]);

        if (empty($results)) {
            $output->writeln("No images found for the theme: $theme");
            return Command::SUCCESS;
        }

        foreach ($results as $image) {
            $output->writeln("Found image: " . $image['webformatURL']);
            // Process each image as needed
        }

        $output->writeln('Finished fetching themes.');

        return Command::SUCCESS;
    }
}