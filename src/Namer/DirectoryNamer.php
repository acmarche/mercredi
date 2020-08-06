<?php

namespace AcMarche\Mercredi\Namer;

use AcMarche\Mercredi\Entity\Enfant;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\DirectoryNamerInterface;

final class DirectoryNamer implements DirectoryNamerInterface
{
    /**
     * Creates a directory name for the file being uploaded.
     *
     * @param Enfant          $object  The object the upload is attached to
     * @param PropertyMapping $propertyMapping The mapping to use to manipulate the given object
     *
     * @return string The directory name
     */
    public function directoryName($object, PropertyMapping $propertyMapping): string
    {
        return '';
    }

    protected function getExtension(UploadedFile $uploadedFile)
    {
        $clientOriginalName = $uploadedFile->getClientOriginalName();
        if (($extension = pathinfo($clientOriginalName, PATHINFO_EXTENSION)) !== '') {
            return $extension;
        }
        if ($extension = $uploadedFile->guessExtension()) {
            return $extension;
        }

        return null;
    }
}
