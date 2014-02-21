MediaUpload
===========

media_upload provides the following functionalities:

- upload a single media (video or audio)
- add media metadata (title, caption, description, author and copywrite)
- enable/disable the media
- remove the media
- easy and transparent integration with doctrine orm

### Usage :

In your form do the following:

``` php
<?php
// ...
public function buildForm(FormBuilderInterface $builder, array $options)
{
    $builder
        // .....
        ->add('video', 'thrace_media_upload', array(
            'label' => 'form.video',
            'data_class' => 'AppBundle\Entity\Video',
            'configs' => array(
                'extensions' => 'video/x-flv',
                'max_upload_size' => '4M',
                'type' => 'flv',
                'div_class' => 'col-lg-9' //fix mopa-bootsrap-bundle
            ),
        )) 
		// .....
    ;
}
```

### Doctrine ORM integration

##### Create *Media* entity by extending  *AbstractMedia* class.

``` php
<?php
namespace AppBundle\Entity;

use Thrace\MediaBundle\Entity\AbstractMedia;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Video extends AbstractMedia
{
    /**
     * @var integer
     *
     * @ORM\Id @ORM\Column(name="id", type="integer")
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function getUploadDir()
    {
        return '/media/videos/product';
    }
    
    public function getType()
    {
        return 'flv';
    }
}
```

*Note:* Method *MediaInterface::getUploadDir()* must return the relative path where file will be moved after entity is saved to DB (executed in postFlush event).
 *MediaInterface::getType()* is needed to determine media type.

##### Create *Product* entity:

``` php
<?php
namespace AppBundle\Entity;

use Thrace\MediaBundle\Model\FileInterface;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Product
{
    /**
     * @var integer 
     *
     * @ORM\Id @ORM\Column(name="id", type="integer")
     * 
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var string 
     *
     * @ORM\Column(type="string", name="name", length=255, nullable=true, unique=false)
     */
    protected $name;
    
    /**
     * @var decimal
     *
     * @ORM\Column(type="decimal", name="price", scale=2, precision=10)
     */
    protected $price;
    
    /**
     * @ORM\OneToOne(targetEntity="Video", cascade={"all"}, orphanRemoval=true)
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    protected $video;
    
    public function getId()
    {
        return $this->id;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setPrice($price)
    {
        $this->price = $price;
    }
    
    public function getPrice()
    {
        return $this->price;
    }
    
    
    public function setVideo(MediaInterface $video = null)
    {
        $this->video = $video;
    }
    
    public function getVideo()
    {
        return $this->video;
    }
}
```

**Note:** Update your database schema running the following command:

``` bash
$ php app/console doctrine:schema:update --force
```

As you see we have a product that has one-to-one unidirectional association with the media.
When you do flush the media will be moved to permenent directory.

The form is a standard symfony class.

**Note:** If you want to validate the file data use the standard symfony validators (File mimetype is validated on upload and it is done by the bundle).

##### File version uses optimistic lock  [more info](http://docs.doctrine-project.org/en/2.0.x/reference/transactions-and-concurrency.html#optimistic-locking):
By default versioning is disabled. You can enable it

``` yml
#app/config.config.yml

thrace_media:    
	enable_version: true
```
When you flush the entity you have to use try/catch statement. 

``` php
try{
	$em->flush();
} catch(OptimisticLockException $e){
	return 'some message';
}
```

#####  In the twig template include the following assets.

``` jinja
	{% block stylesheets %}
                
		{% stylesheets
			'jquery/css/smoothness/jquery-ui.css' 
            'bundles/thracemedia/css/base.css'
            filter='cssrewrite'
        %}
			<link rel="stylesheet" href="{{ asset_url }}" />
        {% endstylesheets %}

	{% endblock %}
    
{% block javascripts %}

	{% javascripts
		'jquery/js/jquery.js'
        'jquery/js/jquery-ui.js'
        'plugins/plupload/js/plupload.js'                    
        'plugins/plupload/js/plupload.html5.js'                    
        'plugins/plupload/js/plupload.flash.js'
        'plugins/jwplayer/jwplayer.js'
        'bundles/thracemedia/js/media-upload.js'                                                                                                                               
	%}
		<script src="{{ asset_url }}"></script>
	{% endjavascripts %}
   
{% endblock %}

{% form_theme form with ['ThraceMediaBundle:Form:fields.html.twig'] %}
           
<form action="" method="post" {{ form_enctype(form) }} novalidate="novalidate">
	{{ form_widget(form) }}
    <input type="submit" />
</form>
```
**Note:** Update your assets running the following command:

``` bash
$ php app/console assetic:dump
```

<a name="media_render"></a>
##### Render media. 

Add javascript asset:

``` jinja    
{% block javascripts %}

	{% javascripts
		'jquery/js/jquery.js'
        'plugins/jwplayer/jwplayer.js'
        'bundles/thracemedia/js/media-render.js'                                                                                                                               
	%}
		<script src="{{ asset_url }}"></script>
	{% endjavascripts %}
   
{% endblock %}

```

In the twig template use the following twig function:

``` jinja
{{ thrace_media(object.video, {}) }}
```
This will display jwplayer

That's it.

[back to index](index.md)