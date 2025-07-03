<?php

namespace Sheadawson\DependentDropdown\Traits;

use ArrayAccess;
use Closure;
use JsonException;
use Psr\Log\LoggerInterface;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Forms\FormField;
use SilverStripe\Model\List\Map;

trait DependentFieldTrait
{
    protected ?FormField $depends;

    protected string $unselected = '';

    protected ?Closure $sourceCallback;

    /**
     * @config
     */
    private static array $allowed_actions = [
        'load',
    ];

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
                $results[] = ['k' => $k, 'v' => $v, 's' => in_array($k, $selectedValues, true)];
            }
        }

        try {
            $payload = json_encode($results, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            Injector::inst()->get(LoggerInterface::class)->error(
                sprintf('%s: %s', __CLASS__, $e->getMessage())
            );
            $payload = sprintf('{"error": "%s"}', _t(__CLASS__.'.FETCH_ERROR', 'Failed to fetch dependent data.'));
        }

        return $response->setBody($payload);
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
     * {@inheritDoc}
     */
    public function setSourceCallback(?Closure $source): self
    {
        $this->sourceCallback = $source;

        return $this;
    }

    /**
     * @return array|ArrayAccess
     */
    public function getSource()
    {
        $val = $this->depends->getValue();

        if (
            !$val
            && method_exists($this->depends, 'getHasEmptyDefault')
            && !$this->depends->getHasEmptyDefault()
        ) {
            $dependsSource = array_keys($this->depends->getSource());
            $val = isset($dependsSource[0]) ? $dependsSource[0] : null;
        }

        if (!$val) {
            $source = [];
        } else {
            $source = call_user_func($this->sourceCallback, $val, $this->getValue());
            if ($source instanceof Map) {
                $source = $source->toArray();
            }
        }

        return $source;
    }
}
