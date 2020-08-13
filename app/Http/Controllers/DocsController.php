<?php

namespace App\Http\Controllers;

use App\Facades\Repositories;
use Illuminate\Http\Response;

class DocsController
{
    public function __invoke(string $package, string $version, string $doc)
    {
        Repositories::clear();
        $repository = Repositories::find($package);

        abort_unless(
            (bool) $repository && $repository->hasVersion($version) && $repository->docExists($version, $doc),
            Response::HTTP_NOT_FOUND
        );

        $docInfo = $repository->parseDocument($version, $doc);

        return view('pages.doc', [
            'content' => $docInfo['content'],
            'title' => $docInfo['title'],
            'slug' => $doc,
            'repository' => $repository,
            'nav' => $repository->nav($version),
            'nextDoc' => $repository->nextDoc($version),
            'previousDoc' => $repository->previousDoc($version),
            'version' => $version,
        ]);
    }
}
