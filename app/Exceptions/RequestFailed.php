<?php

namespace App\Exceptions;

use App\Providers\Renderable;
use RuntimeException;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Output\ConsoleOutput;

class RequestFailed extends RuntimeException implements ExceptionInterface, Renderable
{
    private array $errors;

    public function __construct(array $errors)
    {
        $this->errors = $errors;
        parent::__construct("Request failed for Nightlands API.");
    }

    /**
     * @return array<Error>
     */
    public function getErrors(): array
    {
        return collect($this->errors)->mapInto(Error::class)->all();
    }

    public function render(ConsoleOutput $output): void
    {
        dd("Render request failed", $this->errors);
    }
}
