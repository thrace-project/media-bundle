MultiFileUpload
===============

multi_file_upload is almost the same as [file_upload](file_upload.md) the only difference is that you can upload and manage more then one file and items are sortable.

### Usage :

In your form do the following:

``` php
<?php
// ...
public function buildForm(FormBuilderInterface $builder, array $options)
{
    $builder
        // .....
        ->add('files', 'thrace_multi_file_upload_collection', array(
             'label' => 'form.multi_file',
             'options' => array(
                 'data_class' => 'AppBundle\Entity\MultiFile',
                 'label_render' => false, #fix mopa bootstrap bundle. 
                 'widget_form_group' => false, #fix mopa-bootstrap bundle.
                
             ),
             'configs' => array(
                 'extensions' => 'application/pdf,text/plain',
                 'maxSize' => '3M',
                 'div_class' => 'col-lg-9' //fix mopa-bootsrap-bundle
             )
         ))
		// .....
    ;
}
```
### Doctrine ORM integration

Create *MultiFile* entity by extending  *AbstractMultiFile* class.

``` php
<?php
namespace AppBundle\Entity;

use Thrace\MediaBundle\Entity\AbstractMultiFile;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class MultiFile extends AbstractMultiFile
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
        return '/media/files/product';
    }
}
```

**Note:** Method *FileInterface::getUploadDir()* must return the relative path where file will be moved after entity is saved to DB (executed in postFlush event).

Create *Product* entity:

``` php
<?php
namespace AppBundle\Entity;

use Thrace\MediaBundle\Model\MultiFileInterface;

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
     * @ORM\ManyToMany(targetEntity="MultiFile", cascade={"all"})
     * @ORM\OrderBy({"position" = "ASC"})
     * @ORM\JoinTable(
     *   inverseJoinColumns={@ORM\JoinColumn(unique=true,  onDelete="CASCADE")}
     * )
     */
    protected $files;
    
    public function __construct()
    {
        $this->files = new ArrayCollection();
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
    
    public function addFile(MultiFileInterface $file)
    {
        if (!$this->files->contains($file)){
            $this->files->add($file);
        }
    }
    
    public function getFiles()
    {
        return $this->files;
    }
    
    public function removeFile(MultiFileInterface $file)
    {
        if ($this->files->contains($file)){
            $this->files->removeElement($file);
        }
    }    
}
```

**Note:** Update your database schema running the following command:

``` bash
$ php app/console doctrine:schema:update --force
```

As you see we have a product that has one-to-many unidirectional association with the files.
When you do flush all files will be moved to permenent directory.

The form is a standard symfony class.

**Note:** If you want to validate the files data use the standard symfony validators (File mimetype is validated on upload and it is done by the bundle).


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
        'bundles/thracemedia/js/multi-file-upload.js'                                                                                                                               
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

##### Download file. 
[See here](file_upload.md#file_download)

That's it.

[back to index](index.md)