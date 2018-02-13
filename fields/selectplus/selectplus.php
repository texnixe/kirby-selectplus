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
    $this->label = $this->label();
    $this->options = array();
    $this->formfields = [];
    $this->icon    = 'chevron-down';

  }
  public function getTemplate() {
    if(!$this->template) {
      $this->template = 'default';
    }
    return $this->template;
  }
  public function options() {
    return FieldOptions::build($this);
  }

  public function formfields() {
    return $this->formfields;
  }

  public function option($value, $text, $selected = false) {
    return new Brick('option', $this->i18n($text), array(
      'value'    => $value,
      'selected' => $selected
    ));
  }
  public function label() {

    // make sure there's at least an empty label
    if(!$this->label) {
      $this->label = '&nbsp;';
    }

    $label = parent::label();

    $label->append($this->addButton());
    return $label;
  }

  public function input() {

    $select = new Brick('select');
    $select->addClass('selectbox');
    $select->attr(array(
      'name'         => $this->name(),
      'id'           => $this->id(),
      'required'     => $this->required(),
      'autocomplete' => $this->autocomplete(),
      'autofocus'    => $this->autofocus(),
      'readonly'     => $this->readonly(),
      'disabled'     => $this->disabled(),
    ));

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


    $inner = new Brick('div');
    $inner->addClass('selectbox-wrapper');
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
    $outer->addClass('field-content');
    $outer->append($wrapper);
    $outer->append(parent::icon());
    return $outer;

  }

  public function content () {

    $field = new Brick('div');
    $field->append($this->input());
    $field->attr('data-fieldname', $this->name());
    $field->attr('data-base', url());

    $field->addClass('select-with-add');

    $wrapper = new Brick('div');
    $wrapper->addClass('field-selectplus');
    $wrapper->attr('data-field', 'selectplus');
    foreach($this->formfields() as $k => $v) {
      $wrapper->append($this->inputFormField($k, $v['placeholder']));
    }

    $wrapper->append($this->saveButton());

    # Add each

    $field->append($wrapper);
    # Concatenate & Return
    return $field;
  }

  private function addButton () {
    # Wrapper
    $addButtonContainer = new Brick('a');
    $addButtonContainer->append('<i class="icon icon-left fa fa-plus-circle"></i>');
    $addButtonContainer->addClass('add-page-button label-option');
    $addButtonContainer->append('Hinzufügen');

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
    $saveButton->val(l::get('fields.add.locate', 'OK'));
    $saveButton->addClass('btn btn-rounded save-button');

    # Combine & Ship It
    $saveButtonContainer->append($saveButton);

    return $saveButtonContainer;
  }

  private function inputFormField($name, $placeholder) {
    # Wrapper
    $content = new Brick('div');
    $content->addClass('field-'.$name);

    $input = new Brick('input');
    $input->attr('name', $name);
    $input->addClass('input '.$name);
    $input->attr('placeholder', $placeholder);


    # Combine & Ship It
    $content->append($input);

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
        'message' => 'The new option was created and can now be selected',
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

  public function validate() {
    return array_key_exists($this->value(), $this->options());
  }

  public function routes() {
    return array(
      array(
        'pattern' => 'selectplus',
        'method' => 'POST',
        'action'  => function() {
          $data = kirby()->request()->data();
          $response = $this->createPage($data, $this->parent);

          return json_encode($response);

        }
      )
    );
  }

}
