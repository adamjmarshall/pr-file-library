<?php

namespace AdamMarshall\FilamentFileLibrary\Http\Controllers;

use AdamMarshall\FilamentFileLibrary\Models\FileShareLink;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;

class RevokeShareLinkController extends Controller
{
    use AuthorizesRequests;

    public function __invoke(FileShareLink $link): RedirectResponse
    {
        $this->authorize('manageShareLinks', $link->file);

        $link->update(['revoked_at' => now()]);

        return back();
    }
}
