<?php

use Laravel\WorkOS\Http\Middleware\ValidateSessionWithWorkOS;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

middleware(['auth', ValidateSessionWithWorkOS::class]);

name('dashboard');

render(function () {
    return redirect('/headshots');
});
