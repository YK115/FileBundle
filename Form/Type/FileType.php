<?php

namespace SmartSystems\FileBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\Common\Persistence\ObjectManager;

use SmartSystems\FileBundle\Form\DataTransformer\UploadedFileTransformer;
use SmartSystems\FileBundle\Storage\ConfigurationContainer;
use SmartSystems\FileBundle\Storage\StorageFactory;
use SmartSystems\FileBundle\Storage\AbstractStorage;
use SmartSystems\FileBundle\Entity\Image;
use SmartSystems\FileBundle\Entity\ImagePreviewRepository;

/**
 * Тип поля - "файл".
 *
 */
class FileType extends AbstractType
{
    /**
     * Подключение к БД.
     *
     * @var ObjectManager
     */
    private $om;

    /**
     * Хранилище файлов.
     *
     * @var AbstractStorage
     */
    private $storage;

    /**
     * Конструктор.
     *
     * @param ObjectManager $entityManager Подключение к БД
     * @param ConfigurationContainer $configuration Настройки
     *
     */
    public function __construct(ObjectManager $entityManager, ConfigurationContainer $configuration)
    {
        $this->om = $entityManager;

        $this->storage = StorageFactory::create($configuration);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->storage->setEntityClass($options['data_class']);

        $builder->addModelTransformer(
            new UploadedFileTransformer(
                $this->om,
                $this->storage,
                $options['repository'],
                $options['entity_class'],
                $builder->getName()
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $file = $form->getData();

        if ($file !== NULL) {

            $fileUrl = $this->storage->getUrl($file);
            $previewUrl = $fileUrl;

            if ($file instanceof Image) {
                /** @var ImagePreviewRepository $rep */
                $rep = $this->om->getRepository('SmartSystemsFileBundle:ImagePreview');
                if ($preview = $rep->getByName($file, 'admin')) {
                    $previewUrl = $this->storage->getUrl($preview);
                }
            }

        } else {
            $fileUrl = NULL;
            $previewUrl = NULL;
        }

        $view->vars['file_url'] = $fileUrl;
        $view->vars['preview_url'] = $previewUrl;
        $view->vars['file_object'] = $file;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'invalid_message' => 'Error storing file.',
            ))
        ;

        $resolver->setRequired(array(
            'repository',
            'entity_class',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'file';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sis_file_type';
    }
}
