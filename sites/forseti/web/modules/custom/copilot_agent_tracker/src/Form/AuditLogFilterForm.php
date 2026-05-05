<?php

namespace Drupal\copilot_agent_tracker\Form;

use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;

/**
 * Audit Log Filter Form — filter audit entries by operator, action, date range, and resource.
 */
final class AuditLogFilterForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'audit_log_filter_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $db = Database::getConnection();
    $request = \Drupal::request();
    $query_params = $request->query->all();

    // Get list of unique operators from audit table.
    $operators = $db->select('copilot_agent_tracker_audit', 'a')
      ->distinct()
      ->fields('a', ['operator_id'])
      ->orderBy('operator_id', 'ASC')
      ->execute()
      ->fetchAllKeyed(0, 0);

    $operator_options = ['' => $this->t('— Any operator —')];
    foreach (array_keys($operators) as $uid) {
      $user = User::load($uid);
      $operator_options[$uid] = $user ? $user->getDisplayName() . " (UID: $uid)" : "Unknown (UID: $uid)";
    }

    // Get list of unique actions from audit table.
    $actions = $db->select('copilot_agent_tracker_audit', 'a')
      ->distinct()
      ->fields('a', ['action'])
      ->orderBy('action', 'ASC')
      ->execute()
      ->fetchAllKeyed(0, 0);

    $action_options = ['' => $this->t('— Any action —')];
    foreach (array_keys($actions) as $action) {
      $action_options[$action] = $action;
    }

    $form['#method'] = 'GET';
    $form['#attributes']['class'][] = 'audit-log-filter-form';

    $form['filters'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Filters'),
      '#collapsible' => FALSE,
    ];

    $form['filters']['operator'] = [
      '#type' => 'select',
      '#title' => $this->t('Operator'),
      '#options' => $operator_options,
      '#default_value' => $query_params['operator'] ?? '',
      '#empty_option' => $this->t('— Any operator —'),
    ];

    $form['filters']['action'] = [
      '#type' => 'select',
      '#title' => $this->t('Action'),
      '#options' => $action_options,
      '#default_value' => $query_params['action'] ?? '',
      '#empty_option' => $this->t('— Any action —'),
    ];

    $form['filters']['from'] = [
      '#type' => 'datetime',
      '#title' => $this->t('From'),
      '#default_value' => $query_params['from'] ? new \DateTime($query_params['from']) : NULL,
    ];

    $form['filters']['to'] = [
      '#type' => 'datetime',
      '#title' => $this->t('To'),
      '#default_value' => $query_params['to'] ? new \DateTime($query_params['to']) : NULL,
    ];

    $form['filters']['resource'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Resource ID (partial match)'),
      '#default_value' => $query_params['resource'] ?? '',
      '#size' => 40,
    ];

    $form['filters']['actions'] = [
      '#type' => 'actions',
    ];

    $form['filters']['actions']['filter'] = [
      '#type' => 'submit',
      '#value' => $this->t('Filter'),
      '#button_type' => 'primary',
    ];

    $form['filters']['actions']['clear'] = [
      '#type' => 'submit',
      '#value' => $this->t('Clear All'),
      '#submit' => ['::submitFormClear'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $query_params = [];

    if ($form_state->getValue('operator')) {
      $query_params['operator'] = $form_state->getValue('operator');
    }
    if ($form_state->getValue('action')) {
      $query_params['action'] = $form_state->getValue('action');
    }
    if ($form_state->getValue('from')) {
      $from = $form_state->getValue('from');
      if ($from instanceof \DateTime) {
        $query_params['from'] = $from->format('Y-m-d H:i:s');
      }
    }
    if ($form_state->getValue('to')) {
      $to = $form_state->getValue('to');
      if ($to instanceof \DateTime) {
        $query_params['to'] = $to->format('Y-m-d H:i:s');
      }
    }
    if ($form_state->getValue('resource')) {
      $query_params['resource'] = $form_state->getValue('resource');
    }

    $form_state->setRedirect('copilot_agent_tracker.admin_audit_log', [], ['query' => $query_params]);
  }

  /**
   * Submit handler for "Clear All" button.
   */
  public function submitFormClear(array &$form, FormStateInterface $form_state): void {
    $form_state->setRedirect('copilot_agent_tracker.admin_audit_log');
  }
}
