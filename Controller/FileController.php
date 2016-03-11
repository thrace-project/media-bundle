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
use Thrace\MediaBundle\Utils\SlugFilter;

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
        $handle = $this->getRequest()->files->get('file');
        
        if ($handle && $handle->getError()){
            return new JsonResponse(array(
                'success' => false,
                'err_msg' => $this->container->get('translator')
                    ->trans('file_upload_http_error', array(), 'ThraceMediaBundle')
            ));
        }
        
        $fileManager = $this->container->get('thrace_media.filemanager');
        $extension = $handle->getClientOriginalExtension();
        $name = uniqid() . '.' . $extension;
        
        if(!$extension){
            return new JsonResponse(array(
                'success' => false,
                'err_msg' => sprintf('Unknown Mime Type: "%s"', $handle->getMimeType())
            ));
        }

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
        $filepath = $this->getRequest()->get('filepath');
        $filename = SlugFilter::filter($this->getRequest()->get('filename'));

        $content = $fileManager->getPermanentFileBlobByName($filepath);

        $response = new Response($content);
        $response->headers->set('Content-Length', mb_strlen($content));
        $response->headers->set('Content-Type', $this->getMimeType($content));
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
        $name = $this->getRequest()->get('name');
        $fileManager = $this->container->get('thrace_media.filemanager');
        $content = $fileManager->getTemporaryFileBlobByName($name);
        
        $response = new Response($content);        
        $response->headers->set('Accept-Ranges', 'bytes');
        $response->headers->set('Content-Length', mb_strlen($content));
        $response->headers->set('Content-Type', $this->getMimeType($content));
        $response->expire();
    
        return $response;
    }
    
    /**
     * Renders permanent file
     *
     * @return Response
     */
    public function renderAction()
    {
        $name = $this->getRequest()->get('name');
        $hash = $this->getRequest()->get('hash');

        $response = new Response();        
        $response->setPublic();
        $response->setEtag($hash);
 
        if($response->isNotModified($this->getRequest())){
            return $response;
        }
        
        $fileManager = $this->container->get('thrace_media.filemanager');
        $content = $fileManager->getPermanentFileBlobByName($name);
        
        $response->setContent($content);
        $response->headers->set('Accept-Ranges', 'bytes');
        $response->headers->set('Content-Length', mb_strlen($content));
        $response->headers->set('Content-Type', $this->getMimeType($content));

        return $response;
    }

    /**
     * Gets request object
     * 
     * @return \Symfony\Component\HttpFoundation\Request
     */
    protected function getRequest()
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
    protected function validateFile (UploadedFile $handle)
    {
        $configs = $this->getConfigs();
        $maxSize = $configs['max_upload_size'];
        $extensions = $configs['extensions'];
        $fileConstraint = new File();
        $fileConstraint->maxSize = 2000000000;
        $fileConstraint->mimeTypes = 'application/*';

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
    protected function getConfigs()
    {
    	$session = $this->container->get('session');
    	if (!$configs = $session->get($this->getRequest()->get('thrace_media_id', false))){
            $configs = [
                'max_upload_size' => '200000000',
                'extensions' => 'application/*'
            ];
    	}
    	
    	return $configs;
    }
    
    protected function getMimeType($content)
    {
        $finfo = new \finfo(\FILEINFO_MIME_TYPE);
        $mimetype = $finfo->buffer($content);
        
        if(!$mimetype){
            $mimetype = 'application/octet-stream';
        }
        
        return $mimetype;
    }
}
