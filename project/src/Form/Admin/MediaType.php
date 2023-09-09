<?php

namespace App\Form\Admin;

use App\Entity\Media;
use Symfony\Component\Form\AbstractType;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class MediaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('imageFile', VichImageType::class, [
                'label' => false,
                'by_reference' => false,
                'allow_delete' => true,
                'constraints' => [
                    new Callback([
                        $this,
                        'validate',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Media::class,
        ]);
    }

    public function validate(?UploadedFile $file, ExecutionContextInterface $context): void
    {
        if (!$file) {
            return;
        }

        if (!in_array($file->getMimeType(), ['image/jpg', 'image/jpeg', 'image/png', 'video/mp4'])) {
            $context->buildViolation(sprintf('%s -- не допустимое расширение, допустимые: jpg, jpeg, png, mp4', $file->getMimeType()))
                ->atPath('imageFile')
                ->addViolation();
        }

        $invalidFileNameLength = strlen($file->getClientOriginalName());

        if ($invalidFileNameLength >= 80) {
            $context->buildViolation(sprintf('Длина имени файла %s символа, что превышает допустимое значение в 80 символов!', $invalidFileNameLength))
                ->atPath('imageFile')
                ->addViolation();
        }
    }

}
