<?php

it('guest are redirected to login', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});
