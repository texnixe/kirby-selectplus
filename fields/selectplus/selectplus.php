<?php

class SelectplusField extends BaseField {

  static public $fieldname = 'selectplus';


  public $formfields;
  public $parent;
  public $template;

  static public $assets = array(
    'js' => array(
      'script.js'
    ),
    'css' => array(
      'add.css'
    )
  );



  public function __construct() {
    $this->type = 'selectplus';
    $this->options = array();
    $this->formfields = [];
    $this->icon    = 'chevron-down';

  }

  public function options() {
    return FieldOptions::build($this);
  }

  public function getFormfields() {
    return $this->formfields;
  }

  public function getTemplate() {
    if(!$this->template) {
      $this->template = 'default';
    }
    return $this->template;
  }

  public function option($value, $text, $selected = false) {
    return new Brick('option', $this->i18n($text), array(
      'value'    => $value,
      'selected' => $selected
    ));
  }
  public function label() {
    return null;
  }
  public function headline() {

    // make sure there's at least an empty label
    if(!$this->label) {
      $this->label = '&nbsp;';
    }

    $label = parent::label();

    $label->append($this->addButton());
    return $label;
  }

  public function input() {

    $outerWrapper = new Brick('div', [
      'class' => 'selectfield-wrapper'
    ]);
    $outerWrapper->append($this->headline());


    $select = new Brick('select', [
      'class'        => 'selectbox',
      'name'         => $this->name(),
      'id'           => $this->id(),
      'required'     => $this->required(),
      'autocomplete' => $this->autocomplete(),
      'autofocus'    => $this->autofocus(),
      'readonly'     => $this->readonly(),
      'disabled'     => $this->disabled(),
    ]);

    $default = $this->default();

    if(!$this->required()) {
      $select->append($this->option('', '', $this->value() == ''));
    }

    if($this->readonly()) {
      $select->attr('tabindex', '-1');
    }

    foreach($this->options() as $value => $text) {
      $select->append($this->option($value, $text, $this->value() == $value));
    }


    $inner = new Brick('div', [
      'class' => 'selectbox-wrapper'
    ]);

    $inner->append($select);

    $wrapper = new Brick('div');
    $wrapper->addClass('input input-with-selectbox');
    $wrapper->append($inner);

    if($this->readonly()) {
      $wrapper->addClass('input-is-readonly');
    } else {
      $wrapper->attr('data-focus', 'true');
    }

    $outer = new Brick('div');
    $outer->addClass('field-content selectplus-content');
    $outer->append($wrapper);
    $outer->append(parent::icon());
    $outerWrapper->append($outer);


    return $outerWrapper;

  }

  public function content () {


    $field = new Brick('div', [
      'class' => 'selectplus-field',
      'data-base' => u(),
      'data-fieldname' => $this->name()
    ]);

    $field->append($this->input());
    $field->append(new Brick('div', ['class'=> 'selectplus-message']));

    // create a wrapper for the page creation fields
    $wrapper = new Brick('div', [
      'class' => 'selectplus-formfield-wrapper',
      'data-field' => 'selectplus'
    ]);

    // loop through the form fields
    foreach($this->getFormfields() as $k => $v) {
      $wrapper->append($this->inputFormField($k, $v));
    }
    //$wrapper->append();

    $wrapper->append($this->saveButton());


    $field->append($wrapper);
    return $field;
  }

  private function addButton () {

    $addButtonContainer = new Brick('a');
    $addButtonContainer->append(new Brick('i', ['class' => 'icon icon-left fa fa-plus-circle']));
    $addButtonContainer->addClass('add-page-button label-option');
    $addButtonContainer->append(l('fields.structure.add'));

    return $addButtonContainer;
  }

  # Save Button
  private function saveButton () {
    # Wrapper
    $saveButtonContainer = new Brick('div');
    $saveButtonContainer->addClass('field-content input-save input-button');

    # Button
    $saveButton = new Brick('input');
    $saveButton->attr('type', 'button');
    $saveButton->val(l::get('fields.add.locate', $this->getMessage('selectplus.save')));
    $saveButton->addClass('btn btn-rounded save-button');

    # Combine & Ship It
    $saveButtonContainer->append($saveButton);

    return $saveButtonContainer;
  }

  private function inputFormField($name, $fieldvalues = []) {
    $content = null;
    if(is_array($fieldvalues)) {
      isset($fieldvalues['required'])? $required = $fieldvalues['required']: $required = false;
      isset($fieldvalues['label'])? $label = i18n($fieldvalues['label']): $label = '&nbsp;';
      isset($fieldvalues['placeholder'])? $placeholder = i18n($fieldvalues['placeholder']): $placeholder = '';
      # Wrapper
      $content = new Brick('div');
      $content->addClass('field-'.$name);
      $htmlLabel = new Brick('label', $label, ['class' => 'label', 'for' => $name]);
      if($required) {
        $htmlLabel->append(new Brick('abbr', '*',['title' => 'Required']));
      }
      $input = new Brick('input');
      $input->attr('name', $name);
      $input->attr('required', $required);
      $input->attr('data-message', $this->getMessage('field.required', [$label]));
      $input->addClass('input '.$name);
      $input->attr('placeholder', $placeholder);


      # Combine & Ship It
      $content->append($htmlLabel);
      $content->append($input);
    }
    return $content;
  }

  public function result() {
    // don't save content to file
    $input = get($this->name());

    # Store as Yaml.
    return $input;

  }


  public function createPage($data, $parent) {

    $site = kirby()->site();
    $page = page($parent);
    $data = array_slice($data, 0, count($data)-2);

    // get page data
    $title = esc(array_values($data)[0]);



    try {

      $newPage = $page->children()->create(str::slug($title), $this->getTemplate(), $data);


      // trigger panel.page.create event
      kirby()->trigger('panel.page.create', $newPage);

      $response = array(
        'message' => $this->getMessage('success.message'),
        'class' => 'success',
        'title' => $title,
        'uid' => $newPage->uid()
      );

    } catch(Exception $e) {

      $response = array(
        'message' => $e->getMessage(),
        'class' => 'error'
      );

    }


    return $response;
  }
  function translate($string) {

    $translation = c::get('selectplus.translation', false);
    $language = site()->user()->data()['language'];

    if(!$translation) {
      $translations = require __DIR__ . DS . 'translations.php';

      if (! array_key_exists($language, $translations)) {
        $language = 'en';
      }

      $translation = $translations[$language];
    }

    if(array_key_exists($string, $translation)) {
      $string = $translation[$string];
    }

    return $string;
  }

  public function getMessage($key, $params = []) {
    array_unshift($params, $this->translate($key));
    return sprintf(...$params);
  }

  public function validate() {
    return array_key_exists($this->value(), $this->options());
  }

  public function routes() {
    return [
      [
        'pattern' => 'selectplus',
        'method' => 'POST',
        'action'  => function() {
          $data = kirby()->request()->data();
          $response = $this->createPage($data, $this->parent);

          return json_encode($response);

        }
      ]
    ];
  }

}
