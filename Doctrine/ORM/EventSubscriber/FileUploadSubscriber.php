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

use Thrace\MediaBundle\Model\FileInterface;

use Thrace\MediaBundle\Manager\FileManagerInterface;

use Thrace\MediaBundle\Exception\FileHashChangedException;

use Doctrine\DBAL\LockMode;

use Doctrine\ORM\EntityManager;

use Doctrine\ORM\UnitOfWork;

use Doctrine\ORM\Events;

use Doctrine\ORM\Event\PostFlushEventArgs;

use Doctrine\ORM\Event\OnFlushEventArgs;

use Doctrine\Common\EventSubscriber;

/**
 * Doctrine ORM file upload subscriber
 *
 * @author Nikolay Georgiev <symfonist@gmail.com>
 * @since 1.0
 */
class FileUploadSubscriber implements EventSubscriber
{
    /**
     * @var FileManagerInterface
     */
    protected $fileManager;
    
    /**
     * @var array
     */
    protected $scheduledForCopyFiles = array();
    
    /**
     * @var array
     */
    protected $scheduledForDeleteFiles = array();
    
    /**
     * @var boolean
     */
    protected $versionEnabled;

    /**
     * Construct
     * 
     * @param FileManagerInterface $fileManager
     * @param boolean $versionEnabled
     */
    public function __construct(FileManagerInterface $fileManager, $versionEnabled)
    { 
        $this->fileManager = $fileManager;
        $this->versionEnabled = $versionEnabled;
    }

    /**
     * Handles onFlush event and moves file to permenant directory
     *
     * @param OnFlushEventArgs $eventArgs
     */
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof FileInterface){
                $this->populateMeta($em, $uow, $entity);
                $this->scheduledForCopyFiles[] = $entity;
            }
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {

            if ($entity instanceof FileInterface){

                if ($this->versionEnabled){
                    $em->lock($entity, LockMode::OPTIMISTIC, $entity->getCurrentVersion());
                }
                
                if (true === $entity->isScheduledForDeletion()){
                    $uow->scheduleForDelete($entity);
                    continue;
                }
                
                $changeSet = $uow->getEntityChangeSet($entity);

                if (isset($changeSet['name'])){
                    // remove old file
                    $clonedEntity = clone $entity;
                    $clonedEntity->setName($changeSet['name'][0]);
                    $this->scheduledForDeleteFiles[] = $clonedEntity; 
                } 
                
                if (isset($changeSet['hash'])){
                    $this->checksum($entity, $changeSet['hash'][1]);                    
                    $this->scheduledForCopyFiles[] = $entity;
                }
                
                $this->populateMeta($em, $uow, $entity);
            }
        }

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            if ($entity instanceof FileInterface){
                $this->scheduledForDeleteFiles[] = $entity;
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
        foreach ($this->getScheduledForCopyFiles() as $entity){
            $this->fileManager->copyFileToPermenentDirectory($entity);
            $this->fileManager->removeFileFromTemporaryDirectory($entity);
        }
     
        foreach ($this->getScheduledForDeleteFiles() as $entity){
            $this->fileManager->removeAllFiles($entity);
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
    
    protected function populateMeta(EntityManager $em, UnitOfWork $uow, FileInterface $entity)
    {
        $meta = $em->getClassMetadata(get_class($entity));
        $meta
            ->getReflectionProperty('size')
            ->setValue($entity, $this->fileManager->getFile($entity)->getSize())
        ;
        $uow->recomputeSingleEntityChangeSet($meta, $entity);
    }
    
    protected function getScheduledForCopyFiles()
    {
        $objects = $this->scheduledForCopyFiles;
        $this->scheduledForCopyFiles = array();
        
        return $objects;
    }
    
    protected function getScheduledForDeleteFiles()
    {
        $objects = $this->scheduledForDeleteFiles;
        $this->scheduledForDeleteFiles = array();
        
        return $objects;
    }
    
    /**
     * Checks if file is modified by some other user
     * 
     * @param FileInterface $entity
     * @param string $hash
     * @throws FileHashChangedException
     */
    protected function checksum(FileInterface $entity, $hash)
    {
        $currentHash = $this->fileManager->checksumTemporaryFileByName($entity->getName());
        
        if ($hash !== $currentHash){
            throw new FileHashChangedException($entity->getName());
        }
    }
}