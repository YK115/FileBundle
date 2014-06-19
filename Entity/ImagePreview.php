<?php

namespace SmartSystems\FileBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use SmartSystems\FileBundle\Common\AbstractEntity;

/**
 * Превью изображения.
 *
 * @ORM\Entity(repositoryClass="SmartSystems\FileBundle\Entity\ImagePreviewRepository")
 * @ORM\Table(name="sis_image_preview", uniqueConstraints={@ORM\UniqueConstraint(name="ui_name", columns={"image_id", "name"})})
 * @ORM\HasLifecycleCallbacks()
 */
class ImagePreview extends AbstractEntity
{
    /**
     * Имя превью.
     *
     * @var string
     *
     * @ORM\Column(nullable=false)
     */
    protected $name;

    /**
     * Изображение.
     *
     * @var Image
     *
     * @ORM\ManyToOne(targetEntity="Image", inversedBy="previews", cascade="all")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id", nullable=false)
     */
    protected $image;

    /**
     * Файл.
     *
     * @var File
     *
     * @ORM\OneToOne(targetEntity="File", cascade="all")
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id", nullable=false)
     */
    protected $file;

    /**
     * Ширина картинки.
     *
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $width;

    /**
     * Высота картинки.
     *
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $height;

    /**
     * {@inheritdoc}
     */
    public function __construct($originalFile = NULL)
    {
        parent::__construct($originalFile);

        if ($this->getOriginalFile()) {
            if (!($info = getimagesize($this->getOriginalFile()->getRealPath()))) {
                throw new \Exception('Файл не является картинкой: ' . $this->getOriginalFile()->getRealPath());
            }

            $this->setFile(new File($originalFile));
            $this->setWidth($info[0]);
            $this->setHeight($info[1]);
        }
    }

    /**
     * Устанавливает имя.
     *
     * @param string $name Имя
     *
     * @return ImagePreview
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Возвращает имя.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Устанавливает ширину.
     *
     * @param integer $width Ширина
     *
     * @return ImagePreview
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Возвращает ширину
     *
     * @return integer
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Устанавливает высоту.
     *
     * @param integer $height Высота
     *
     * @return ImagePreview
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Возвращает высоту.
     *
     * @return integer
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Устанавливает изображение.
     *
     * @param Image $image Изображение
     *
     * @return ImagePreview
     */
    public function setImage(Image $image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Возвращает изображение.
     *
     * @return Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Устанавливает файл.
     *
     * @param File $file Файл
     *
     * @return ImagePreview
     */
    public function setFile(File $file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Возвращает файл.
     *
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Является ли файл картинкой.
     *
     * @return bool
     */
    public function isImage()
    {
        return TRUE;
    }

    /**
     * Выполняется перед сохранением в БД.
     *
     * @ORM\PrePersist
     */
    public function prePersistHandler()
    {
        parent::prePersistHandler();
    }
}