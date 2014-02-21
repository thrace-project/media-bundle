ThraceMediaBundle
=================

### Installation.

##### Before you start you should install [LiipImagineBundle](https://github.com/liip/LiipImagineBundle/blob/master/Resources/doc/installation.md) and [KnpGaufretteBundle](https://github.com/KnpLabs/KnpGaufretteBundle)

### Step 1) Get the bundle

First, grab the ThraceMediaBundle using composer (symfony 2.1 pattern)

Add on composer.json (see http://getcomposer.org/)

    "require" :  {
        // ...
        "thrace-project/media-bundle":"dev-master",
    }


### Step 2) Register the bundle

To start using the bundle, register it in your Kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Thrace\MediaBundle\ThraceMediaBundle(),        
    );
    // ...
}
```

##### Step 3) Download javascript libraries
- jquery (http://jquery.com/)
- jqueryUI (http://jqueryui.com/)
- plupload (https://github.com/moxiecode/plupload)
- Jcrop (https://github.com/tapmodo/Jcrop)
- colorbox (https://github.com/jackmoore/colorbox)
- jwplayer (http://www.longtailvideo.com/jw-player/)

Put the sources somewhere in the *web* folder. EX: *web/plugins/*

##### Step 4) Configure KnpGaufretteBundle.

``` yml
knp_gaufrette:
    adapters:
        media:
            local:
                directory: %kernel.root_dir%/../web/media
        temp:
            local:
                directory: %kernel.root_dir%/../web/media/temp
    filesystems:
        media:
            adapter:    media
            alias:      media_filesystem
        temp:
            adapter:    temp
            alias:      temp_filesystem
	
```

**Note** - Media adapter is used to store already persisted files and temp adapter is used to store files which are still not persisted to DB or being manipulated by user.

##### Step 5) Configure media bundle.

```yml
thrace_media:
    temporary_filesystem_key: temp #gaufrette temporary filesystem key
    media_filesystem_key: media # #gaufrette media filesystem key
    jwplayer:
        key: "Kh16KTAXkhlYsWK6JnxLL/qK9hjIUn/f9TU1LA==" #get the key from jwplayer website
        html5player: "plugins/jwplayer/jwplayer.html5.js" #set the path to html5 js file
        flashplayer: "plugins/jwplayer/jwplayer.flash.swf" #set the path to flash file
```

##### Step 6) Register the bundle routes

``` yaml
# app/config/routing.yaml
thrace_media_backend:
    resource: "@ThraceMediaBundle/Resources/config/routing/backend.xml"
    prefix:   /admin
    
thrace_media_frontend:
    resource: "@ThraceMediaBundle/Resources/config/routing/frontend.xml"
    prefix:   /

```

##### Step 7) Securing Specific URL Patterns

ThraceMediaBundle uses ajax/post requests to upload, render, crop, rotate and reset images and files.

- thrace_media_image_upload (used to upload image: */_thrace-media/image-upload/*  
- thrace_media_image_crop (used to crop image): */_thrace-media/image-crop* 
- thrace_form_media_image_rotate (used to rotate image): */_thrace-media/image-rotate* 
- thrace_form_media_image_reset (used to reset image): */_thrace-media/image-reset* 
- thrace_media_image_render_temporary (used to render temporary image): */_thrace-media/image-render-temporary* 
- thrace_media_render_temporary (used to render temporary media video/audio): */_thrace-media/render-temporary* 
- thrace_media_file_upload (used to upload file): */_thrace_media/file_upload* 

``` yaml
# app/config/security.yml
security:
    # ...
    access_control:
        - { path: ^/_thrace_media/image-upload, roles: ROLE_ADMIN }
        - { path: ^/_thrace_media/image-crop, roles: ROLE_ADMIN }
        - { path: ^/_thrace_media/image-rotate, roles: ROLE_ADMIN }
        - { path: ^/_thrace_media/image-reset, roles: ROLE_ADMIN }
        - { path: ^/_thrace-media/image-render-temporary, roles: ROLE_ADMIN }
        - { path: ^/_thrace-media/render-temporary, roles: ROLE_ADMIN }
        - { path: ^/_thrace_media/file-upload, roles: ROLE_ADMIN }
        
```
Or you can secure *^/admin* section and then prefix the bundle routing.

``` yaml
# app/config/routing.yml

thrace_media_backend:
    resource: "@ThraceMediaBundle/Resources/config/routing/backend.xml"
    prefix:   /admin

```
[For more information go to symfony documentation](http://symfony.com/doc/current/book/security.html#securing-specific-url-patterns)

##### Step 8) Run the following command to delete files in temporary directory:

``` bash
$ php app/console thrace:media:cache-clear --maxAge=7200 
```

<a name="list"></a>
**List of all elements**

* [thrace_file_upload](file_upload.md)
* [thrace_multi_file_upload](multi_file_upload.md)
* [thrace_image_upload](image_upload.md)
* [thrace_multi_image_upload](multi_image_upload.md)
* [thrace_media_upload](media_upload.md)

