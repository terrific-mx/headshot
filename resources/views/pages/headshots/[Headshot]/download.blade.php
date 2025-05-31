<?php

use App\Models\Headshot;
use Illuminate\Http\Request;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

name('headshots.download');

middleware('signed');

render(function (Request $request, Headshot $headshot) {
    return $headshot->downloadPhotos();
});
