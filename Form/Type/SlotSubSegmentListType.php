<?php

namespace MauticPlugin\SubPreferenceCenterBundle\Form\Type;

use Mautic\CoreBundle\Form\Type\SlotType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SlotSubSegmentListType extends SlotType {

  /**
   * @var \Symfony\Contracts\Translation\TranslatorInterface
   */
  protected $translator;

  /**
   * ConfigType constructor.
   */
  public function __construct(TranslatorInterface $translator) {
    $this->translator = $translator;
  }

  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder->add(
      'label-text',
      TextType::class,
      [
        'label' => 'mautic.lead.field.label',
        'label_attr' => ['class' => 'control-label'],
        'required' => FALSE,
        'attr' => [
          'class' => 'form-control',
          'data-slot-param' => 'label-text',
        ],
        'data' => $this->translator->trans('mautic.lead.form.list'),
      ]
    );

    parent::buildForm($builder, $options);
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix() {
    return 'slot_subsegmentlist';
  }

}
