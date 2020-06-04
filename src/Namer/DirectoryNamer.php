<?php

namespace AcMarche\Mercredi\Namer;

use AcMarche\Mercredi\Entity\Enfant;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\DirectoryNamerInterface;

class DirectoryNamer implements DirectoryNamerInterface
{
    protected function getExtension(UploadedFile $file)
    {
        $originalName = $file->getClientOriginalName();
        if ($extension = pathinfo($originalName, PATHINFO_EXTENSION)) {
            return $extension;
        }
        if ($extension = $file->guessExtension()) {
            return $extension;
        }

        return null;
    }

    /**
     * Creates a directory name for the file being uploaded.
     *
     * @param Enfant          $object  The object the upload is attached to
     * @param PropertyMapping $mapping The mapping to use to manipulate the given object
     *
     * @return string The directory name
     */
    public function directoryName($object, PropertyMapping $mapping): string
    {
        return '';
        //todo bug getId() empty
        return (string) $object->getId();
    }
}
