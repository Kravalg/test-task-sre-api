<?php

declare(strict_types=1);

namespace App\Application\Controller\Action;

use App\Domain\Entity\File;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[AsController]
final class CreateFileAction extends AbstractController
{
    public function __invoke(Request $request): File
    {
        $uploadedFile = $request->files->get('file');

        if (!$uploadedFile) {
            throw new BadRequestHttpException('"file" is required');
        }

        $mediaObject = new File();
        $mediaObject->setFile($uploadedFile);

        return $mediaObject;
    }
}
