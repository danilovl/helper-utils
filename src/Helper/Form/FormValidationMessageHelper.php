<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Helper\Form;

use Symfony\Component\Form\{FormInterface};

final class FormValidationMessageHelper
{
    /**
     * @param FormInterface<mixed> $form
     * @return array<string, list<string>>
     */
    public static function getErrorMessages(FormInterface $form): array
    {
        $messages = [];
        foreach ($form->getErrors() as $error) {
            $messages[$form->getName()][] = $error->getMessage();
        }

        foreach ($form->all() as $child) {
            if ($child->isSubmitted() && !$child->isValid()) {
                $childErrors = self::getErrorMessages($child);
                foreach ($childErrors as $field => $list) {
                    $messages[$field] = array_merge($messages[$field] ?? [], $list);
                }
            }
        }

        return $messages;
    }

    /**
     * @param FormInterface<mixed> $form
     * @return list<string>
     */
    public static function getFlatErrorMessages(FormInterface $form): array
    {
        $flat = [];
        foreach ($form->getErrors(true) as $error) {
            $flat[] = $error->getMessage();
        }

        return $flat;
    }

    /**
     * @param FormInterface<mixed> $form
     * @return list<string>
     */
    public static function getGlobalErrors(FormInterface $form): array
    {
        $messages = [];
        foreach ($form->getErrors() as $error) {
            $messages[] = $error->getMessage();
        }

        return $messages;
    }

    /**
     * @param FormInterface<mixed> $form
     */
    public static function hasErrors(FormInterface $form): bool
    {
        return self::countErrors($form) > 0;
    }

    /**
     * @param FormInterface<mixed> $form
     */
    public static function countErrors(FormInterface $form): int
    {
        return count($form->getErrors(true));
    }
}
