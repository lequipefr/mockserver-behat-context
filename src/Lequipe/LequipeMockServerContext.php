<?php

declare(strict_types=1);

namespace Lequipe\MockServer\Lequipe;

use Behat\Gherkin\Node\PyStringNode;
use Lequipe\MockServer\MockServerContext;

/**
 * Extension de MockServerContext
 * avec la logique liée à l'environnement L'Équipe.
 */
class LequipeMockServerContext extends MockServerContext
{
    /**
     * @Given the MS :ms :method :path will return the json:
     *
     * Example:
     *
     * Given the MS "ms-user" "GET" "/users" will return the json:
     * """
     * [
     *   {
     *      "id": 1,
     *      "name": "Zidane"
     *   },
     *   {
     *      "id": 2,
     *      "name": "Barthez"
     *   }
     * ]
     * """
     */
    public function theMsWillReturn(string $ms, string $method, string $path, PyStringNode $node): void
    {
        $this->theRequestOnApiWillReturnBody($method, '/' . $ms . '/' . ltrim($path, '/'), json_decode($node->getRaw(), true));
    }

    /**
     * @Given the MS :ms :method :path will return the json from file :filename
     *
     * Example:
     *
     * Given MS "ms-user" "GET" "/users" will return the json from file "users/get-users.json"
     */
    public function theMsWillReturnFromFile(string $ms, string $method, string $path, string $filename): void
    {
        $content = file_get_contents($this->featurePath . DIRECTORY_SEPARATOR . $filename);

        $this->theRequestOnApiWillReturnBody($method, '/' . $ms . '/' . ltrim($path, '/'), json_decode($content, true));
    }
}
