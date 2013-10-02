ImageUpload
===========

image_upload provides the following functionalities:
- upload a single image
- preview the image with colorbox plugin
- crop the image with Jcrop
- rotate the image
- reset the image to its original state
- add file metadata (title, caption, description, author and copywrite)
- enable/disable the image
- remove the image
- easy and transparent integration with doctrine orm

### Usage:

##### In your form do the following:

``` php
<?php
// ...
public function buildForm(FormBuilderInterface $builder, array $options)
{
    $builder
        // .....
        ->add('image', 'thrace_image_upload', array(
            'label' => 'form.image',
            'data_class' => 'AppBundle\Entity\Image',
            'configs' => array(
                'minWidth' => 300,
                'minHeight' => 100,
                'extensions' => 'jpeg,jpg',
                'maxSize' => '4M',
            ),
        ))  
		// .....
    ;
}
```

**Important:** Configs *'minWidth', 'minHeight', 'maxSize', 'extensions'* are required.


### Doctrine integration.

First create *Image* entity by extending  *AbstractImage* class.

``` php
<?php
namespace AppBundle\Entity;

use Thrace\MediaBundle\Entity\AbstractImage;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Image extends AbstractImage
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
        return '/media/images/image';
    }
}
```

**Note:** Method *ImageInterface::getUploadDir()* must return the relative path where image will be moved after entity is saved to DB (executed in postFlush event).

Create *Product* entity:

``` php
<?php
namespace AppBundle\Entity;

use Thrace\MediaBundle\Model\ImageInterface;

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
     * @ORM\OneToOne(targetEntity="Image", cascade={"all"}, orphanRemoval=true, fetch="EAGER")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    protected $image;

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
    
    public function setImage(ImageInterface $image = null)
    {
        $this->image = $image;
    }
    
    public function getImage()
    {
        return $this->image;
    }
}
```

**Note:** Update your database schema running the following command:

``` bash
$ php app/console doctrine:schema:update --force
```

As you see we have a product that has an one-to-one unidirectional association with an image and image could be optional.
When you do flush the image will be moved to permenent directory.

The form is a standard symfony class.

**Note:** If you want to validate the image data use the standard symfony validators (Image mimetype is validated on upload and it is done by the bundle).

##### Image version uses optimistic lock  [more info](http://docs.doctrine-project.org/en/2.0.x/reference/transactions-and-concurrency.html#optimistic-locking):
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
} catch (\Thrace\MediaBundle\Exception\FileHashChangedException $e) {
	// this exception is thrown when temporary image hash is different than the image you try to save.
	// this can happen if someone else has changed the image in the temp directory.
	return 'some message';
}
```

#####  In the twig template include the following assets.

``` jinja
	{% block stylesheets %}
                
		{% stylesheets
			'jquery/css/smoothness/jquery-ui.css' 
            'plugins/colorbox/example1/colorbox.css'
    	    'plugins/Jcrop/css/jquery.Jcrop.css'
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
        'plugins/colorbox/jquery.colorbox.js' 
        'plugins/Jcrop/js/jquery.Jcrop.js' 
        'bundles/thracemedia/js/image-upload.js'                                                                                                                                
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

<a name="image_render"></a>
##### Rendering the image. 
To render the image we are going to use LiipImagineBundle bundle.

``` yml
#app/config.config.yml

liip_imagine:
    filter_sets:
        product:
            quality: 70
            filters:
                relative_resize: { widen: 500 }
```

More information about [LiipImagineBundle](https://github.com/liip/LiipImagineBundle)

In the twig template use the following twig function:

``` jinja
{{ thrace_image(entity.image, 'product', {'alt': 'image thumb'}) }} 
```

That's it.
 
[back to index](index.md)