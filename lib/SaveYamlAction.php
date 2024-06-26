<?php

namespace Uniform\Actions;

use Error;
use Kirby\Toolkit\I18n;

class SaveYamlAction extends Action {
  public function perform() {
    try {
      $data = $this->form->data();
      $page = $this->option('page');

      // We don't need `form_name` (used for email subject) and `form_id` (used
      // to differentiate multiple forms on a single page) anymore.
      unset($data['form_name']);
      unset($data['form_id']);

      $entries = $page->form_entries()->toData('yaml');
      $entries[] = $data;

      kirby()->impersonate(
        'kirby',
        fn() => $page->update([
          'form_entries' => \Kirby\Data\Yaml::encode($entries),
        ]),
      );
    } catch (\Exception $e) {
      $this->handleException($e);
    }
  }

  /**
   * @param Exception|Error $e
   */
  protected function handleException($e) {
    $error = $e->getMessage();
    $message = I18n::translate('arnoson.kirby-forms.save-error');
    $message = option('debug') ? "$message: $error" : "$message.";
    $this->fail($message);
  }
}