<?php


namespace Tests\Traits;


use Illuminate\Support\Facades\Lang;
use Illuminate\Testing\TestResponse;

trait TestValidations
{
    protected abstract function routeStore();
    protected abstract function routeUpdate();

    protected function assertInvalidationStoreAction(array $data, string $rule, $ruleParam = [])
    {
        $fields = array_keys($data);
        $response = $this->json('POST', $this->routeStore(), $data);
        $this->assertInvalidationFields($response, $fields, $rule, $ruleParam);
    }

    protected function assertInvalidationUpdateAction(array $data, string $rule, $ruleParam = [])
    {
        $fields = array_keys($data);
        $response = $this->json('PUT', $this->routeUpdate(), $data);
        $this->assertInvalidationFields($response, $fields, $rule, $ruleParam);
    }

    protected function assertInvalidationFields(TestResponse $response, array $fields, string $rule, array $ruleParams = [])
    {
        $response->assertStatus(422)->assertJsonValidationErrors($fields);

        foreach ($fields as $field) {

            $fieldName = str_replace('_', ' ', $field);
            $response->assertJsonFragment([Lang::get("validation.{$rule}", ['attribute' => $fieldName] + $ruleParams)]);

        }

    }
}
