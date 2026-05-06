<?php

namespace Drupal\dungeoncrawler_content\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\dungeoncrawler_content\Service\LanguageService;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Language catalog controller.
 */
class LanguagesController extends ControllerBase {

  /**
   * Get the language catalog.
   */
  public function getLanguageCatalog(): JsonResponse {
    return new JsonResponse(static::getLanguages());
  }

  /**
   * Get the list of languages.
   */
  public static function getLanguages(): array {
    return LanguageService::getLanguageCatalog();
  }

  /**
   * Validate a language ID against the catalog.
   */
  public static function isValidLanguageId(string $language_id): bool {
    return LanguageService::isValidLanguageId($language_id);
  }

}
