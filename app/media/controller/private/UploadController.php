<?php

namespace app\media\private;

use app\core\RestController;
use app\core\router\Response;
use app\media\File;
use app\media\UploadObject;
use app\user\User;

class UploadController extends RestController
{
    public function post(): Response
    {
        $uploadObject = new UploadObject(
            $_FILES['file']['error'],
            $_FILES['file']['name'],
            $_FILES['file']['size'],
            $_FILES['file']['type'],
            $_FILES['file']['tmp_name'],
        );
        $path = File::upload($uploadObject);
        return $this->response
            ->setMessage('Successfully uploaded')
            ->setContent(['path' => $path]);
    }

    public function hasAccess(): bool
    {
        return User::getCurrentUser()?->isVerified();
    }
}