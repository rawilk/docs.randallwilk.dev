<?php

namespace App\Models;

use App\Support\ContentTransformer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use TitasGailius\Terminal\Terminal;

/**
 * @property-read string $name
 * @property-read string $description
 * @property-read string $repository
 * @property-read array $branches
 * @property-read string $category
 */
class Repository extends Model
{
    protected $guarded = [];

    /** @var string */
    protected const CONTENT_META_PATTERN = '/---(.|\n)*---/';

    public function hasVersion(string $version): bool
    {
        if ($version === 'master') {
            $version = $this->branches['master'];
        }

        return in_array(strtolower($version), $this->branches, true);
    }

    public function isCurrentVersion(string $alias): bool
    {
        return $this->branches['master'] === $alias;
    }

    public function homeUrl(string $version = null): string
    {
        return route('doc', [
            'package' => $this->name,
            'version' => $version ?: $this->branches['master'],
            'doc' => 'introduction',
        ]);
    }

    public function docExists(string $version, $doc): bool
    {
        return file_exists($this->docPath($version, $doc));
    }

    public function docPath(string $version, string $doc): string
    {
        return "{$this->basePath()}/{$version}/{$doc}.blade.php";
    }

    protected function basePath(): string
    {
        return resource_path("views/docs/{$this->name}");
    }

    public function updateDocs(): void
    {
        foreach ($this->branches as $branch => $alias) {
            $this->createBranchDirectory($alias);
            $this->cloneBranchDocs($branch, $alias);
        }
    }

    protected function createBranchDirectory(string $alias): void
    {
        $directory = resource_path("views/docs/{$this->name}/{$alias}");

        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
    }

    protected function cloneBranchDocs(string $branch, string $alias): void
    {
        $baseDir = __DIR__ . '/temp';
        $dir = $baseDir . '/' . $this->name . '/' . $alias;
        $resourceDirectory = resource_path("views/docs/{$this->name}/{$alias}");
        $accessToken = config('site.git.github_access_token');

        Terminal::run(
            "mkdir -p {$dir} \
             && cd {$dir} \
             && git init \
             && git config core.sparseCheckout true \
             && echo \"/docs\" >> .git/info/sparse-checkout \
             && git remote add -f origin https://{$accessToken}@github.com/rawilk/{$this->name}.git \
             && git pull origin {$branch} \
             && cp -r docs/* {$resourceDirectory} \
             && echo \"---\ntitle: {$this->name}\ncategory: {$this->category}\n---\" > {$resourceDirectory}/_index.md
             "
        );

        File::deleteDirectory($baseDir);

        $this->convertMarkdownFilesToBlade($resourceDirectory);
    }

    protected function convertMarkdownFilesToBlade(string $directory): void
    {
        $files = Finder::create()
            ->in($directory);

        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach ($files as $file) {
            if ($file->isDir() || Str::startsWith($file->getFilename(), '_index')) {
                continue;
            }

            if ($file->getExtension() === 'md') {
                $path = $file->getPath();
                $fileName = $file->getFilenameWithoutExtension();
                $newPath = "{$path}/{$fileName}.blade.php";

                File::move(
                    $file->getRealPath(),
                    $newPath
                );
            }
        }
    }

    public function parseDocument(string $version, string $doc): array
    {
        $path = $this->docPath($version, $doc);

        $content = View::file($path)->render();

        $meta = [];

        if (Str::startsWith($content, '---')) {
            $meta = $this->extractMeta($content);

            $content = Str::after($content, "\n\n");
        }

        return [
            'content' => ContentTransformer::transform($content),
            'title' => $meta['title'] ?? $this->name,
        ];
    }

    protected function extractMeta(string $content): array
    {
        $content = Str::before($content, "\n\n");

        preg_match(static::CONTENT_META_PATTERN, $content, $meta);

        return collect(explode(PHP_EOL, $meta[0]))
            ->filter(fn ($line) => ! empty($line) && $line !== '---')
            ->mapWithKeys(static function ($line) {
                $parts = explode(':', $line);

                return [$parts[0] => trim($parts[1] ?? '')];
            })
            ->toArray();
    }

    public function nav(string $version, string $directory = null): array
    {
        return Cache::remember(
            "repository_nav.{$this->name}.{$version}",
            now()->addHour(),
            fn () => $this->generateNav($version, $directory)
        );
    }

    public function nextDoc(string $version)
    {
        $flattenedArrayOfPages = $this->getFlattenedArrayOfPages($version);

        $pathsByIndex = $flattenedArrayOfPages->pluck('url');

        $currentIndex = $pathsByIndex->search(request()->url());

        $nextIndex = $currentIndex + 1;

        return $flattenedArrayOfPages[$nextIndex] ?? null;
    }

    public function previousDoc(string $version)
    {
        $flattenedArrayOfPages = $this->getFlattenedArrayOfPages($version);

        $pathsByIndex = $flattenedArrayOfPages->pluck('url');

        $currentIndex = $pathsByIndex->search(request()->url());

        $previousIndex = $currentIndex - 1;

        return $flattenedArrayOfPages[$previousIndex] ?? null;
    }

    protected function getDirectoryMeta(string $directory): array
    {
        if (file_exists("{$directory}/_index.md")) {
            return $this->extractMeta(file_get_contents("{$directory}/_index.md"));
        }

        return [];
    }

    protected function getFileSlug(SplFileInfo $file): string
    {
        $fileNameWithoutExtension = explode('.', $file->getFilenameWithoutExtension());

        return $fileNameWithoutExtension[0];
    }

    protected function generateNav(string $version, string $directory = null): array
    {
        $directoryToSearch = $directory ?: "{$this->basePath()}/{$version}";

        $files = Finder::create()
            ->in($directoryToSearch)
            ->depth(0);

        $nav = $directory
            ? ['items' => []]
            : ['items' => [], 'directories' => []];

        $itemUrlPrefix = $directory ? basename($directory) . '/' : null;

        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach ($files as $file) {
            if (Str::startsWith($file->getFilename(), '_index')) {
                continue;
            }

            // Right now, we are only ever going one level deep, so this should be fine...
            if ($file->isDir()) {
                $subNav = $this->generateNav($version, $file->getPathname());
                $meta = $this->getDirectoryMeta($file->getPathname());

                $nav['directories'][] = [
                    'title' => $meta['title'] ?? $file->getFilename(),
                    'sort' => (int) ($meta['sort'] ?? 0),
                    'items' => $subNav['items'],
                ];

                continue;
            }

            $content = file_get_contents($file->getPathname());

            $meta = $this->extractMeta($content);

            $nav['items'][] = [
                'title' => $meta['title'] ?? '',
                'url' => route('doc', [
                    'package' => $this->name,
                    'version' => $version,
                    'doc' => $itemUrlPrefix . $this->getFileSlug($file),
                ]),
                'sort' => (int) ($meta['sort'] ?? 0),
            ];
        }

        $nav['items'] = collect($nav['items'])
            ->sortBy('sort')
            ->values()
            ->toArray();

        if (! $directory) {
            // sort each of the directories
            $nav['directories'] = collect($nav['directories'])
                ->sortBy('sort')
                ->values()
                ->toArray();
        }

        return $nav;
    }

    protected function getFlattenedArrayOfPages(string $version)
    {
        $nav = $this->nav($version);

        $directoryPages = collect($nav['directories'])
            ->map(fn ($directory) => $directory['items'])
            ->flatten(1);

        return collect($nav['items'])
            ->merge($directoryPages);
    }
}
