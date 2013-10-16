MultiImageUpload
================

multi_image_upload is almost the same as [image_upload](image_upload.md) the only difference is that you can upload and manage more then one image and items are sortable.

### Usage :

##### In your form do the following:

``` php
<?php
// ...
public function buildForm(FormBuilderInterface $builder, array $options)
{
    $builder
        // .....
        ->add('images', 'thrace_multi_image_upload_collection', array(
            'options' => array(
                'data_class' => 'AppBundle\Entity\MultiImage',
                'label_render' => false,  #fix mopa bootstrap bundle. 
                'widget_form_group' => false,  #fix mopa bootstrap bundle. 
            ),
            'configs' => array(
                'minWidth' => 300,
                'minHeight' => 100,
                'extensions' => 'jpeg,jpg',
                'max_upload_size' => '4M',
                'div_class' => 'col-lg-9' //fix mopa-bootsrap-bundle
            )
        ))
		// .....
    ;
}
```

### Doctrine ORM integration

Create *MultiImage* entity by extending  *AbstractMultiImage* class.

``` php
<?php
namespace AppBundle\Entity;

use Thrace\MediaBundle\Entity\AbstractMultiImage;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class MultiImage extends AbstractMultiImage
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
        return '/media/images/product';
    }
}
```

**Note:** Method *ImageInterface::getUploadDir()* must return the relative path where image will be moved after entity is saved to DB (executed in postFlush event).

##### Create *Product* entity:

``` php
<?php
namespace AppBundle\Entity;

use Thrace\MediaBundle\Model\MultiImageInterface;

use Doctrine\Common\Collections\ArrayCollection;

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
     * @ORM\ManyToMany(targetEntity="MultiImage", cascade={"all"})
     * @ORM\OrderBy({"position" = "ASC"})
     * @ORM\JoinTable(
     *   inverseJoinColumns={@ORM\JoinColumn(unique=true,  onDelete="CASCADE")}
     * )
     */
    protected $images;

    
    public function __construct()
    {
        $this->images = new ArrayCollection();
    }
    
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
    
    public function addImage(MultiImageInterface $image)
    {
        if (!$this->images->contains($image)){
            $this->images->add($image);
        }
    }
    
    public function getImages()
    {
        return $this->images;
    }
    
    public function removeImage(MultiImageInterface $image)
    {
        if ($this->images->contains($image)){
            $this->images->removeElement($image);
        }
    }
}
```

**Note:** Update your database schema running the following command:

``` bash
$ php app/console doctrine:schema:update --force
```

As you see we have a product that has one-to-many unidirectional association with images.
When you do flush all images will be moved to permenent directory.

The form is a standard symfony class.

**Note:** If you want to validate the images data use the standard symfony validators (Image mimetype is validated on upload and it is done by the bundle).


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
        'bundles/thracemedia/js/multi-image-upload.js'                                                                                                                                
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


**Note** - [See how to render images](image_upload.md#image_render) 

That's it.

[back to index](index.md)