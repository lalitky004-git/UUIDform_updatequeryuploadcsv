<?php
/**
 * @file
 * Contains \Drupal\drupalform\Form\SimpleForm.
 */
namespace Drupal\drupalform\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class SimpleForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'simple_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // $form['id'] = array(
    //   '#type' => 'textfield',
    //   '#title' => t('ID: '),
    //   '#required' => TRUE,
    // );


    // $form['connection_count'] = array (
    //     '#type' => 'textfield',
    //     '#title' => t('connection_count: '),
    //     '#required' => TRUE,
    // );

    $form['uuid'] = array(
      '#type' => 'textfield',
      '#title' => t('UUID: '),
      '#required' => FALSE,
    );

    // $form['updated'] = array(
    //   '#type' => 'date',
    //   '#title' => t('Start date'),
    //   '#required' => TRUE,
    // );
    // $form['browser'] = array(
    //   '#type' => 'fieldset', 
    //   '#title' => t('UUID upload'), 
    //   '#collapsible' => TRUE, 
    //   '#description' => t("Upload a CSV file."), 
    // ); 
    // $form['browser']['file_upload'] = array( 
    //   '#type' => 'file', 
    //   '#title' => t('CSV File'), 
    //   '#size' => 40, 
    //   '#description' => t('Select the CSV file to be imported. ')
    // );
    $form['import_csv'] = array(
      '#type' => 'managed_file',
      '#title' => t('Upload file here'),
      '#upload_location' => 'public://importcsv/',
      '#default_value' => '',
      "#upload_validators"  => array("file_validate_extensions" => array("csv")),
      '#states' => array(
        'visible' => array(
          ':input[name="File_type"]' => array('value' => t('Upload Your File')),
        ),
      ),
    );

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
      
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
    public function validateForm(array &$form, FormStateInterface $form_state) {
      // $connection=$form_state->getValue('connection_count');
      //   if(!is_numeric($connection)){
      //     $form_state->setErrorByName('connection_count', $this->t('count should be numeric'));
      //   }
      // $uuidval=$form_state->getValue('uuid');
      // $csvval=$form_state->getValue('import_csv');
      // if (!empty($uuidval) && !empty($csvval)) {
      //   form_set_error('uuid', t("you need to fill only one field at a time"));
      // }
    }

  /**
   * {@inheritdoc}
   */
    public function submitForm(array &$form, FormStateInterface $form_state) {
      $connection = \Drupal::service('database');
      $file = \Drupal::entityTypeManager()->getStorage('file')
      ->load($form_state->getValue('import_csv')[0]); 
      $full_path = $file->get('uri')->value;
      $file_name = basename($full_path);
      // $inputFileName = \Drupal::service('file_system')->realpath('public://import_csv/'.$file_name);
      $file = fopen($full_path, "r");
      while (!feof($file)) {
        $customer = fgetcsv($file);
        foreach($customer as $data){
          $query = $connection->update('connection_user_data')
          ->fields(['connection_count' => 2])
          ->condition('uuid', $data,'=');
          $query->execute();
        }
    }    
    $connection = \Drupal::service('database');
      $uuid = $form_state->getValue('uuid');
      $conncount= $form_state->getValue('connection_count');
      $query = $connection->update('connection_user_data')
          ->fields(['connection_count' => 8])
          ->condition('uuid', $uuid,'=');
        $query->execute();
        \Drupal::messenger()->addMessage('Data updated Successfully');
        die();
        }
    }


