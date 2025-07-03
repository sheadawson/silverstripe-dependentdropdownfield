<?php

namespace Sheadawson\DependentDropdown\Traits;

use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Dev\Debug;
use SilverStripe\Forms\FormField;

trait DependentFieldTrait {
    /**
     * @config
     */
    private static array $allowed_actions = [
        'load',
    ];

    protected ?FormField $depends;

    protected string $unselected = '';

    protected ?\Closure $sourceCallback;

    /**
     * @throws \JsonException
     */
    public function load(HTTPRequest $request): HTTPResponse
    {
        $response = new HTTPResponse();
        $response->addHeader('Content-Type', 'application/json');
        $newValue = $request->getVar('val');
        $selectedValues = $request->getVar('selectedValues') ?? [];

        $items = call_user_func($this->sourceCallback, $newValue);
        $results = [];
        if ($items) {
            foreach ($items as $k => $v) {
                $results[] = ['k' => $k, 'v' => $v, 's' => in_array($k, $selectedValues)];
            }
        }

        $response->setBody(json_encode($results, JSON_THROW_ON_ERROR));

        return $response;
    }

    public function getDepends(): FormField
    {
        return $this->depends;
    }

    public function setDepends(FormField $field): self
    {
        $this->depends = $field;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUnselectedString(): string
    {
        return $this->unselected;
    }

    public function setUnselectedString(?string $string): self
    {
        $this->unselected = $string;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setSource($source)
    {
        $this->sourceCallback = $source;

        return $this;
    }
}
