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

use Symfony\Component\Validator\Constraints\Image;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * This controller handles image manipulations
 *
 * @author Nikolay Georgiev <symfonist@gmail.com>
 * @since 1.0
 */
class ImageController extends ContainerAware
{

    /**
     * This actions is responsible for validating, uploading of images
     * 
     * @return JsonResponse
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

        $imageManager = $this->container->get('thrace_media.imagemanager');
        $options = $this->container->getParameter('thrace_media.plupload.options');
        $extension = $handle->guessExtension();
        $name = uniqid() . '.' . $extension;
        
        if(!$extension){
            return new JsonResponse(array(
                'success' => false,
                'err_msg' => 'Unknown Mime-Type'
            ));
        }

        $validate = $this->validateImage($handle);

        if ($validate !== true) {
            return new JsonResponse(array(
                'success' => false,
                'err_msg' => $validate
            ));
        }

        $content = $imageManager->normalizeImage(
            $name, file_get_contents($handle->getRealPath()), 
            $options['normalize_width'], $options['normalize_height']
        );
        
        $imageManager->saveToTemporaryDirectory($name, $content);
        $imageManager->makeImageCopyToOriginalDirectory($name);
        $hash = $imageManager->checksumTemporaryFileByName($name);

        return new JsonResponse(array(
            'success' => true,
            'name'    => $name,
            'hash'    => $hash
        ));
    }
    
    /**
     * Renders temporary image
     * 
     * @param string $name
     * @return Response
     */
    public function renderTemporaryAction()
    {   
        $name = $this->getRequest()->get('name');
        $imageManager = $this->container->get('thrace_media.imagemanager');
        $content = $imageManager->getTemporaryImageBlobByName($name);
        $response = new Response($content);
        $response->headers->set('Accept-Ranges', 'bytes');
        $response->headers->set('Content-Length', mb_strlen($content));
        $response->headers->set('Content-Type', 'image');
        $response->expire();
        
        return $response;
    }
    
    /**
     * Renders permanent image
     * 
     * @return Response
     */
    public function renderAction()
    {   
        $filepath = $this->getRequest()->get('filepath');
        $hash = $this->getRequest()->get('hash');
        $filter = $this->getRequest()->get('filter');
        $tag = md5($hash . $filter);
        
        $response = new Response();
        $response->setPublic();
        $response->setEtag($tag);
        
        if($response->isNotModified($this->getRequest())){
            return $response;
        }
        
        $imageManager = $this->container->get('thrace_media.imagemanager');
        $filterManager = $this->container->get('liip_imagine.filter.manager');
        
        $image = $imageManager->loadPermanentImageByName($filepath);        
        $filteredImage = $filterManager->applyFilter($image, $filter);
        
        $content = $filteredImage->get($imageManager->getExtension($filepath));
        $response->setContent($content);
        $response->headers->set('Accept-Ranges', 'bytes');
        $response->headers->set('Content-Length', mb_strlen($content));
        $response->headers->set('Content-Type', 'image');
        
        return $response;
    }
    
    /**
     * Renders permanent original image
     * 
     * @return Response
     */
    public function renderOriginalAction()
    {   
        $filepath = $this->getRequest()->get('filepath');
        $hash = $this->getRequest()->get('hash');

        $tag = md5($hash . $filepath);
        
        $response = new Response();
        $response->setPublic();
        $response->setEtag($tag);
        
        if($response->isNotModified($this->getRequest())){
            return $response;
        }
        
        $imageManager = $this->container->get('thrace_media.imagemanager');
        
        $content = $imageManager->loadPermanentImageByName($filepath);        
         
        $response->setContent($content);
        $response->headers->set('Accept-Ranges', 'bytes');
        $response->headers->set('Content-Length', mb_strlen($content));
        $response->headers->set('Content-Type', 'image');
        
        return $response;
    }

    /**
     * This action performs image cropping
     * 
     * @return JsonResponse
     */
    public function cropAction ()
    {
        $this->validateRequest();
        
        $imageManager = $this->container->get('thrace_media.imagemanager');
        $name = $this->getRequest()->get('name');
        $options = array();
        
        $options['x'] = $this->getRequest()->get('x', false);
        $options['y'] = $this->getRequest()->get('y', false);
        $options['w'] = $this->getRequest()->get('w', false);
        $options['h'] = $this->getRequest()->get('h', false);
        
        if (!$imageManager->crop($name, $options)){
            return new JsonResponse(array(
                'success' => false,
                'err_msg' => $this->container->get('translator')
                    ->trans('error.image_crop', array('name' => $name), 'ThraceMediaBundle')
            ));
        }
       
        $hash = $imageManager->checksumTemporaryFileByName($name);
        
        return new JsonResponse(array(
            'success' => true,
            'name'    => $name,
            'hash'    => $hash
        ));
    }

    /**
     * This action performs image rotation
     * 
     * @return JsonResponse
     */
    public function rotateAction ()
    {
        $this->validateRequest();

        $imageManager = $this->container->get('thrace_media.imagemanager');
        $name = $this->getRequest()->get('name');
        $imageManager->rotate($name);
        $hash = $imageManager->checksumTemporaryFileByName($name);

        return new JsonResponse(array(
            'success' => true,
            'name'    => $name,
            'hash'    => $hash
        ));
        
    }

    /**
     * This action revert the image to original one
     * 
     * @return JsonResponse
     */
    public function resetAction ()
    {
        $this->validateRequest();

        $imageManager = $this->container->get('thrace_media.imagemanager');
        $name = $this->getRequest()->get('name');
        $imageManager->reset($name);         
        $hash = $imageManager->checksumTemporaryFileByName($name);

        return new JsonResponse(array(
            'success' => true,
            'name'    => $name,
            'hash'    => $hash
        ));
        
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
     * Checks if ajax request
     * 
     * @throws \InvalidArgumentException
     */
    protected function validateRequest()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            throw new \LogicException('Request must be ajax');
        }
    }

    /**
     * Validates image and return true on success and
     * array of error messages on failure
     *
     * @param UploadedFile $handle
     * @return boolean | string
     */
    protected function validateImage (UploadedFile $handle)
    {
        $configs = $this->getConfigs();
        $maxSize = $configs['max_upload_size'];
        $extensions = $configs['extensions'];
        $imageConstraint = new Image();
        $imageConstraint->minWidth = $configs['minWidth'];
        $imageConstraint->minHeight = $configs['minHeight'];
        $imageConstraint->maxSize = $maxSize;
        $imageConstraint->mimeTypes =
            array_map(function($item){return 'image/' . $item;}, explode( ',',  $extensions));
        $errors = $this->container->get('validator')->validateValue($handle, $imageConstraint);

        if (count($errors) == 0) {
            return true;
        } else {
            return $this->container->get('translator')
                ->trans(/** @Ignore */$errors[0]->getMessageTemplate(), $errors[0]->getMessageParameters());
        }
    }

    /**
     * Gets Image configs
     *
     * @return array
     */
    protected function getConfigs()
    {
    	$session = $this->container->get('session');
    	if (!$configs = $session->get($this->getRequest()->get('thrace_media_id', false))){
    		throw new \InvalidArgumentException('Request parameter "thrace_media_id" is missing!');
    	}
    	
    	return $configs;
    }
}
