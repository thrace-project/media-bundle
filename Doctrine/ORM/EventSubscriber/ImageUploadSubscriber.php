<?php
/*
 * This file is part of ThraceMediaBundle
*
* (c) Nikolay Georgiev <symfonist@gmail.com>
*
* This source file is subject to the MIT license that is bundled
* with this source code in the file LICENSE.
*/
namespace Thrace\MediaBundle\Doctrine\ORM\EventSubscriber;

use Thrace\MediaBundle\Model\ImageInterface;

use Thrace\MediaBundle\Exception\FileHashChangedException;

use Thrace\MediaBundle\Manager\ImageManagerInterface;

use Doctrine\DBAL\LockMode;

use Doctrine\ORM\EntityManager;

use Doctrine\ORM\UnitOfWork;

use Doctrine\ORM\Events;

use Doctrine\ORM\Event\PostFlushEventArgs;

use Doctrine\ORM\Event\OnFlushEventArgs;

use Doctrine\Common\EventSubscriber;

/**
 * Doctrine ORM image upload subscriber
 *
 * @author Nikolay Georgiev <symfonist@gmail.com>
 * @since 1.0
 */
class ImageUploadSubscriber implements EventSubscriber
{
    /**
     * @var \Thrace\MediaBundle\Manager\ImageManagerInterface
     */
    protected $imageManager;

    /**
     * @var array
     */
    protected $scheduledForCopyImages = array();

    /**
     * @var array
    */
    protected $scheduledForDeleteImages = array();

    /**
     * @var boolean
    */
    protected $versionEnabled;

    /**
     * Construct
     *
     * @param ImageManagerInterface $imageManager
     * @param boolean $versionEnabled
     */
    public function __construct(ImageManagerInterface $imageManager, $versionEnabled)
    {
        $this->imageManager = $imageManager;
        $this->versionEnabled = $versionEnabled;
    }

    /**
     * Handles onFlush event and moves images to permenant directory
     *
     * @param OnFlushEventArgs $eventArgs
     */
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof ImageInterface){
                $this->scheduledForCopyImages[] = $entity;
                $this->populateMeta($em, $uow, $entity);
            }
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {

            if ($entity instanceof ImageInterface){

                if ($this->versionEnabled){
                    $em->lock($entity, LockMode::OPTIMISTIC, $entity->getCurrentVersion());
                }

                $changeSet = $uow->getEntityChangeSet($entity);

                if (true === $entity->isScheduledForDeletion()){
                    $uow->scheduleForDelete($entity);
                    continue;
                }

                if (isset($changeSet['name'])){
                    // remove old image
                    $clonedEntity = clone $entity;
                    $clonedEntity->setName($changeSet['name'][0]);
                    $this->scheduledForDeleteImages[] = $clonedEntity;                  
                }

                if (isset($changeSet['hash'])){
                    $this->scheduledForCopyImages[] = $entity;
                }
                
                if(isset($changeSet['name']) || isset($changeSet['hash'])){
                    $this->checksum($entity);
                    $this->populateMeta($em, $uow, $entity);
                } 
            }
        }

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            if ($entity instanceof ImageInterface){
                $this->scheduledForDeleteImages[] = $entity;
            }
        }
    }

    /**
     * Handles postFlush event
     *
     * @param PostFlushEventArgs $eventArgs
     */
    public function postFlush(PostFlushEventArgs $eventArgs)
    {
        foreach ($this->getScheduledForCopyImages() as $entity){
            $this->imageManager->copyImagesToPermanentDirectory($entity);
            $this->imageManager->removeImagesFromTemporaryDirectory($entity);
        }

        foreach ($this->getScheduledForDeleteImages() as $entity){
            $this->imageManager->removeAllImages($entity);
        }
    }

    /**
     * (non-PHPdoc)
     * @see Doctrine\Common.EventSubscriber::getSubscribedEvents()
     */
    public function getSubscribedEvents()
    {
        return array(Events::onFlush, Events::postFlush);
    }

    protected function populateMeta(EntityManager $em, UnitOfWork $uow, ImageInterface $entity)
    {
        $meta = $em->getClassMetadata(get_class($entity));
        $meta
            ->getReflectionProperty('size')
            ->setValue($entity, $this->imageManager->getTemporaryImage($entity)->getSize())
        ;
        $uow->recomputeSingleEntityChangeSet($meta, $entity);
    }

    /**
     * Pop-up images scheduled for copying
     *
     * @return array:
     */
    protected function getScheduledForCopyImages()
    {
        $objects = $this->scheduledForCopyImages;
        $this->scheduledForCopyImages = array();

        return $objects;
    }

    /**
     * Pop-up images scheduled for deliting
     *
     * @return array:
     */
    protected function getScheduledForDeleteImages()
    {
        $objects = $this->scheduledForDeleteImages;
        $this->scheduledForDeleteImages = array();

        return $objects;
    }
     
    /**
     * Checks if images is modified by some other user
     *
     * @param ImageInterface $entity
     * @param string $hash
     * @throws FileHashChangedException
     */
    protected function checksum(ImageInterface $entity)
    {
        $currentHash = $this->imageManager->checksumTemporaryFileByName($entity->getName());

        if ($entity->getHash() !== $currentHash){
            throw new FileHashChangedException($entity->getName());
        }
    }
}