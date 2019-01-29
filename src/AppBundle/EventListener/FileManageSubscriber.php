<?php
namespace AppBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use AppBundle\Service\FileUploader;
use AppBundle\Entity\Book;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Filesystem;

class FileManageSubscriber implements EventSubscriber
{
    private $uploader;

    public function __construct(FileUploader $uploader)
    {
        $this->uploader = $uploader;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate,
            Events::postLoad,
            Events::preRemove
        ];
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        $this->uploadFile($entity);
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        $this->uploadFile($entity);
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof Book) {
            return;
        }

        if ($filename = $entity->getCover()) {
            $file = new File($this->uploader->getTargetDirectory().'/'.$filename);
            $entity->setCover($file);
        }
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof Book) {
            return;
        }

        if ($filename = $entity->getCover()) {
            $filesystem = new Filesystem();
            //$file = new File($filename);
            $filesystem->remove($filename);
        }
    }

    private function uploadFile($entity)
    {

        if (!$entity instanceof Book) {
            return;
        }

        $file = $entity->getCover();

        if ($file instanceof UploadedFile) {
            $filename = $this->uploader->upload($file);
            $entity->setCover($filename);
        } elseif ($file instanceof File) {
            $entity->setCover($file->getFilename());
        }
    }

}
