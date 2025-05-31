<?php

use App\Models\Headshot;
use App\Models\Style;
use Illuminate\Http\Request;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

name('headshots.styles.download');

middleware('signed');

render(function (Request $request, Headshot $headshot, Style $style) {
    return $style->downloadPhotos();
});
