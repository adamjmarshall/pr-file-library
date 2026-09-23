<?php

namespace AdamMarshall\FilamentFileLibrary\Http\Controllers;

use AdamMarshall\FilamentFileLibrary\Models\LibraryFile;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;

class ViewFileController extends Controller
{
    use AuthorizesRequests;

    public function __invoke(LibraryFile $file)
    {
        $this->authorize('view', $file);

        abort_unless($file->isPreviewable(), 404);

        return $file->view();
    }
}
