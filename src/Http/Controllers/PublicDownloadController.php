<?php

namespace AdamMarshall\FilamentFileLibrary\Http\Controllers;

use AdamMarshall\FilamentFileLibrary\Models\FileShareLink;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class PublicDownloadController extends Controller
{
    public function __invoke(string $token): Response
    {
        $link = FileShareLink::query()
            ->with('file')
            ->where('token', $token)
            ->firstOrFail();

        abort_unless($link->isUsable() && $link->file !== null, 410, 'This link is no longer available.');

        DB::transaction(function () use ($link) {
            $link->incrementQuietly('download_count');
        });

        return $link->file->download();
    }
}
