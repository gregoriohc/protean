<?php

namespace Gregoriohc\Protean\Common\Validation;

use Gregoriohc\Protean\Common\Concerns\Parametrizable;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory as ValidatorFactory;

class Validator
{
    /**
     * @var \Illuminate\Validation\Validator
     */
    protected $validator;

    protected $validationMessages = [
        /*
        |--------------------------------------------------------------------------
        | Validation Language Lines
        |--------------------------------------------------------------------------
        |
        | The following language lines contain the default error messages used by
        | the validator class. Some of these rules have multiple versions such
        | as the size rules. Feel free to tweak each of these messages here.
        |
        */
        "accepted"         => "The :attribute must be accepted.",
        "active_url"       => "The :attribute is not a valid URL.",
        "after"            => "The :attribute must be a date after :date.",
        "alpha"            => "The :attribute may only contain letters.",
        "alpha_dash"       => "The :attribute may only contain letters, numbers, and dashes.",
        "alpha_num"        => "The :attribute may only contain letters and numbers.",
        "array"            => "The :attribute must be an array.",
        "before"           => "The :attribute must be a date before :date.",
        "between"          => [
            "numeric" => "The :attribute must be between :min and :max.",
            "file"    => "The :attribute must be between :min and :max kilobytes.",
            "string"  => "The :attribute must be between :min and :max characters.",
            "array"   => "The :attribute must have between :min and :max items.",
        ],
        "confirmed"        => "The :attribute confirmation does not match.",
        "date"             => "The :attribute is not a valid date.",
        "date_format"      => "The :attribute does not match the format :format.",
        "different"        => "The :attribute and :other must be different.",
        "digits"           => "The :attribute must be :digits digits.",
        "digits_between"   => "The :attribute must be between :min and :max digits.",
        "email"            => "The :attribute format is invalid.",
        "exists"           => "The selected :attribute is invalid.",
        "image"            => "The :attribute must be an image.",
        "in"               => "The selected :attribute is invalid.",
        "integer"          => "The :attribute must be an integer.",
        "ip"               => "The :attribute must be a valid IP address.",
        "max"              => [
            "numeric" => "The :attribute may not be greater than :max.",
            "file"    => "The :attribute may not be greater than :max kilobytes.",
            "string"  => "The :attribute may not be greater than :max characters.",
            "array"   => "The :attribute may not have more than :max items.",
        ],
        "mimes"            => "The :attribute must be a file of type: :values.",
        "min"              => [
            "numeric" => "The :attribute must be at least :min.",
            "file"    => "The :attribute must be at least :min kilobytes.",
            "string"  => "The :attribute must be at least :min characters.",
            "array"   => "The :attribute must have at least :min items.",
        ],
        "not_in"           => "The selected :attribute is invalid.",
        "numeric"          => "The :attribute must be a number.",
        "regex"            => "The :attribute format is invalid.",
        "required"         => "The :attribute field is required.",
        "required_if"      => "The :attribute field is required when :other is :value.",
        "required_with"    => "The :attribute field is required when :values is present.",
        "required_without" => "The :attribute field is required when :values is not present.",
        "required_without_all" => "The :attribute field is required when :values are not present.",
        "same"             => "The :attribute and :other must match.",
        "size"             => [
            "numeric" => "The :attribute must be :size.",
            "file"    => "The :attribute must be :size kilobytes.",
            "string"  => "The :attribute must be :size characters.",
            "array"   => "The :attribute must contain :size items.",
        ],
        "unique"           => "The :attribute has already been taken.",
        "url"              => "The :attribute format is invalid.",
        'custom' => [],
        'attributes' => [],
    ];

    protected $validationMessagesCustom = [
        "is_valid"         => "The :attribute must be a validable object.",
        "is_model"         => "The :attribute must be a model of type :model.",
    ];

    protected $context;

    /**
     * Validator constructor.
     *
     * @param array $parameters
     * @param array $rules
     * @param mixed $context
     */
    public function __construct($parameters, $rules, $context = null)
    {
        $this->context = $context;
        $this->validator = $this->getValidator($parameters, $rules);
    }

    /**
     * @param array $parameters
     * @param array $rules
     * @param mixed $context
     * @return static|\Illuminate\Validation\Validator
     */
    public static function make($parameters, $rules, $context = null)
    {
        return new static($parameters, $rules, $context);
    }

    /**
     * @param array $parameters
     * @param array $rules
     * @return \Illuminate\Validation\Validator
     */
    public function getValidator($parameters, $rules)
    {
        if (!class_exists('\Validator')) {
            $loader = new ArrayLoader();
            $loader->addMessages('en_US', 'validation', $this->validationMessages);
            $translator = new Translator($loader, 'en_US');
            $validatorFactory = new ValidatorFactory($translator);
            $validator = $validatorFactory->make($parameters, $rules, $this->validationMessagesCustom);
        } else {
            $validator = \Validator::make($parameters, $rules, $this->validationMessagesCustom);
        }
        $this->bootValidatorExtensions($validator, ['is_valid', 'is_model']);

        return $validator;
    }

    /**
     * @param \Illuminate\Validation\Validator $validator
     * @param array $names
     */
    public function bootValidatorExtensions(&$validator, $names)
    {
        $context = $this->context;

        foreach ($names as $name) {
            $validator->addExtension($name, function ($attribute, $value, $parameters, $validator) use ($name, $context) {
                $parameters[] = $context;
                $method = 'validate' . studly_case($name);
                return call_user_func_array([static::class, $method], [$attribute, $value, $parameters, $validator]);
            });

            $validator->addReplacer($name, function ($message, $attribute, $rule, $parameters) use ($name, $context) {
                $parameters[] = $context;
                $method = 'replace' . studly_case($name);
                if (!method_exists(static::class, $method)) {
                    return $message;
                }
                return call_user_func_array([static::class, $method], [$message, $attribute, $rule, $parameters]);
            });
        }
    }

    /**
     * @param string $attribute
     * @param mixed $value
     * @param array $parameters
     * @param \Illuminate\Validation\Validator $validator
     * @return bool
     */
    protected static function validateIsValid($attribute, $value, $parameters, $validator)
    {
        if (!is_object($value)) {
            return "Parameter '$attribute' must be an object";
        }

        if (!method_exists($value, 'validateParameters')) {
            return false;
        }

        /** @var Parametrizable $parameterValue */
        $value->validateParameters();

        return true;
    }

    /**
     * @param string $attribute
     * @param mixed $value
     * @param array $parameters
     * @param \Illuminate\Validation\Validator $validator
     * @return bool
     */
    protected static function validateIsModel($attribute, $value, $parameters, $validator)
    {
        $basePackageNamespace = implode('\\', array_slice(explode('\\', static::class), 0, 2));
        if (count($parameters) == 1) {
            array_unshift($parameters, $basePackageNamespace . '\\Common\\Models\\AbstractModel');
        }

        $contextClass = get_class($parameters[1]);
        $baseContextNamespace = implode('\\', array_slice(explode('\\', $contextClass), 0, 2));;
        if (!strstr($parameters[0], '\\')) {
            $parameters[0] = $baseContextNamespace . '\\Common\\Models\\' . $parameters[0];
        }

        if (!is_a($value, $parameters[0], true)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $message
     * @param string $attribute
     * @param string $rule
     * @param array $parameters
     * @return bool
     */
    protected static function replaceIsModel($message, $attribute, $rule, $parameters)
    {
        if (count($parameters) == 1) {
            array_unshift($parameters, 'AbstractModel');
        }

        return str_replace(':model', $parameters[0], $message);
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->validator, $name], $arguments);
    }


}
