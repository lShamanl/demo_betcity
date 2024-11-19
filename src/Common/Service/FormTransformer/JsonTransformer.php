<?php

declare(strict_types=1);

namespace App\Common\Service\FormTransformer;

use JMS\Serializer\SerializerInterface;
use JsonException;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

readonly class JsonTransformer implements DataTransformerInterface
{
    public function __construct(
        private SerializerInterface $serializer,
    ) {
    }

    /**
     * @param array|null $value
     */
    public function transform($value): string
    {
        if (null === $value) {
            return '';
        }

        return $this->serializer->serialize($value, 'json');
    }

    /**
     * @param string|null $value
     */
    public function reverseTransform($value): ?array
    {
        // no issue number? It's optional, so that's ok
        if (!$value) {
            return null;
        }

        try {
            return json_decode($value, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $jsonException) {
            $failure = new TransformationFailedException(message: $jsonException->getMessage(), previous: $jsonException);
            $failure->setInvalidMessage('JSON parse Error', [
                '{{ value }}' => $value,
            ]);
            throw $failure;
        }
    }
}
