<?php
/*
 * This file is part of ThraceMediaBundle
 *
 * (c) Nikolay Georgiev <symfonist@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Thrace\MediaBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\DependencyInjection\ContainerAware;

use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use Symfony\Component\Validator\Constraints\File;

/**
 * This controller handles file upload
 *
 * @author Nikolay Georgiev <symfonist@gmail.com>
 * @since 1.0
 */
class FileController extends ContainerAware
{

    /**
     * This actions is responsible for validation, uploading of files
     * 
     * @return Response
     */
    public function uploadAction ()
    { 
        if (null === $handle = $this->getRequest()->files->get('file')){
            throw new \RuntimeException('Invalid request');
        }

        $fileManager = $this->container->get('thrace_media.filemanager');
        
        $name = uniqid() . '.' . $handle->guessExtension();

        $validate = $this->validateFile($handle);

        if ($validate !== true) {
            return new JsonResponse(array(
                'success' => false,
                'err_msg' => $validate
            ));
        }
        
        $content = file_get_contents($handle->getRealPath());
        $fileManager->saveToTemporaryDirectory($name, $content);
        $hash = $fileManager->checksumTemporaryFileByName($name);

        return new JsonResponse(array(
            'success' => true,
            'name'    => $name,
            'hash'    => $hash,
        ));
        
    }
    
    /**
     * This action is responsible for downloading resourses
     * 
     * @return Response
     */
    public function downloadAction()
    {
        $fileManager = $this->container->get('thrace_media.filemanager');
        $filepath = $this->container->get('request')->get('filepath');
        $filename = $this->container->get('request')->get('filename');
        
        $content = $fileManager->getPermenentFileByKey($filepath); 
        $response = new Response($content);
        $response->headers->set('Content-Length', mb_strlen($content));
        $response->headers->set('Content-Disposition', 'attachment;filename=' . $filename);
        $response->expire();
        
        return $response;
    }
    
    /**
     * Renders temporary file
     *
     * @return Response
     */
    public function renderTemporaryAction()
    {
        $name = $this->container->get('request')->get('name');
        $fileManager = $this->container->get('thrace_media.filemanager');
        $content = $fileManager->getTemporaryFileBlobByKey($name);
        
        $response = new Response($content);        
        $response->headers->set('Accept-Ranges', 'bytes');
        $response->headers->set('Content-Length', mb_strlen($content));
        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->expire();
    
        return $response;
    }

    /**
     * Gets request object
     * 
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->container->get('request');
    }

    /**
     * Validates file and return true on success and
     * array of error messages on failure
     *
     * @param UploadFile $handle
     * @return boolean | string
     */
    private function validateFile (UploadedFile $handle)
    {
        $configs = $this->getConfigs();
        $maxSize = $configs['maxSize'];
        $extensions = $configs['extensions'];
        $fileConstraint = new File();
        $fileConstraint->maxSize = $maxSize;
        $fileConstraint->mimeTypes = explode( ',',  $extensions);

        $errors = $this->container->get('validator')->validateValue($handle, $fileConstraint);

        if (count($errors) == 0) {
            return true;
        } else {
            return $this->container->get('translator')
                ->trans(/** @Ignore */$errors[0]->getMessageTemplate(), $errors[0]->getMessageParameters());
        }
    }

    /**
     * Gets File configs
     *
     * @return array
     */
    private function getConfigs()
    {
    	$session = $this->container->get('session');
    	if (!$configs = $session->get($this->getRequest()->get('thrace_media_id', false))){
    		throw new \InvalidArgumentException('Request parameter "thrace_media_id" is missing!');
    	}
    	
    	return $configs;
    }
}
