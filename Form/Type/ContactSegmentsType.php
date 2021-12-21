<?php

namespace MauticPlugin\SubPreferenceCenterBundle\Form\Type;

use Mautic\CoreBundle\Form\Type\FormButtonsType;
use Mautic\LeadBundle\Form\Type\LeadListType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactSegmentsType extends AbstractType {

  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder->add(
      'lead_lists',
      LeadListType::class,
      [
        'label' => 'mautic.lead.form.list',
        'label_attr' => ['class' => 'control-label'],
        'multiple' => TRUE,
        'choices' => $options['filtered_choices'],
        'expanded' => TRUE,
        'required' => FALSE,
      ]
    );

    $builder->add(
      'buttons',
      FormButtonsType::class,
      [
        'apply_text' => FALSE,
        'save_text' => 'mautic.page.form.saveprefs',
      ]
    );

    if (!empty($options['action'])) {
      $builder->setAction($options['action']);
    }
  }

  public function configureOptions(OptionsResolver $resolver) {
    $resolver->setDefaults([
      'filtered_choices' => [],
    ]);
  }

  /**
   * @return string
   */
  public function getBlockPrefix() {
    return 'contact_segments';
  }

}
