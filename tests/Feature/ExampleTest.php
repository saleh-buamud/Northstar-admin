<?php

it('redirects guests to sign in', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('login'));
});

it('retains the standard authenticated API user endpoint', function () {
    $this->getJson('/api/user')->assertUnauthorized();
});