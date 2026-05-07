<?php

namespace app\core;

use app\core\services\Request;
use app\core\services\Response;
use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Permission
{

    public function __construct(public array $permissions)
    {
    }

    public function check(Request $request): bool
    {
        $specialPermissions = [];

        if (in_array('*', $this->permissions)) {
            $specialPermissions[] = '*';
        }
        if (in_array('@', $this->permissions)) {
            $specialPermissions[] = '@';
        }
        if (in_array('?', $this->permissions)) {
            $specialPermissions[] = '?';
        }

        foreach ($specialPermissions as $permission) {
            switch ($permission) {
                case '*':
                    return true;

                case '?':
                    if (App::$session->isGuest()) {
                        return true;
                    }
                    break;

                case '@':
                    if (App::$session->isAuthenticated()) {
                        return true;
                    }

                    if ($request->getPath() === '/site/index') {
                        Response::redirect('auth/login');
                    }

                    Response::redirect('auth/login', ['redirect' => urlencode($request->getPath())]);
            }
        }

        return false;
    }

}