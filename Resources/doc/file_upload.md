FileUpload
==========

file_upload provides the following functionalities:

- upload a single file
- add file metadata (title, caption, description, author and copywrite)
- enable/disable the file
- remove the file
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
        ->add('file', 'thrace_file_upload', array(
            'label' => 'form.file',
            'data_class' => 'AppBundle\Entity\File',
            'configs' => array(
                'extensions' => 'text/plain,application/pdf',
                'maxSize' => '4M',
                'div_class' => 'col-lg-9' //fix mopa-bootsrap-bundle
            ),
        ))  
		// .....
    ;
}
```

### Doctrine ORM integration

##### Create *File* entity by extending  *AbstractFile* class.

``` php
<?php
namespace AppBundle\Entity;

use Thrace\MediaBundle\Entity\AbstractFile;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class File extends AbstractFile
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

*Note:* Method *FileInterface::getUploadDir()* must return the relative path where file will be moved after entity is saved to DB (executed in postFlush event).

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
     * @ORM\OneToOne(targetEntity="File", cascade={"all"}, orphanRemoval=true)
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    protected $file;
    
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
    
    
    public function setFile(FileInterface $file = null)
    {
        $this->file = $file;
    }
    
    public function getFile()
    {
        return $this->file;
    }
}
```

**Note:** Update your database schema running the following command:

``` bash
$ php app/console doctrine:schema:update --force
```

As you see we have a product that has one-to-one unidirectional association with the file.
When you do flush the file will be moved to permenent directory.

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
        'bundles/thracemedia/js/file-upload.js'                                                                                                                               
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

<a name="file_download"></a>
##### Download file. 

In the twig template use the following twig function:

``` jinja
thrace_file_download_url(entity.file)
```
This will generate download url.

That's it.

[back to index](index.md)