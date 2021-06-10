<?php


namespace Tests\Traits;


use Illuminate\Testing\TestResponse;

trait TestSaves
{
    protected abstract function model();
    protected abstract function routeStore();
    protected abstract function routeUpdate();

    protected function assertStore(array $data, array $verify, array $json = null): TestResponse
    {
        $response = $this->json('POST', $this->routeStore(), $data);

        if ($response->status() !== 201) {
            # throw new \Exception("Response status must be 201, given {$response->status()}:\n {$response->content()}");
        }

        $this->assertDatabase($response, $verify);

        $this->assertJsonResponseContent($response, $verify, $json);

        return $response;

    }

    protected function assertUpdate(array $data, array $verify, array $json = null): TestResponse
    {
        $response = $this->json('PUT', $this->routeUpdate(), $data);

        if ($response->status() !== 200) {
            # throw new \Exception("Response status must be 200, given {$response->status()}:\n {$response->content()}");
        }

        $this->assertDatabase($response, $verify);

        $this->assertJsonResponseContent($response, $verify, $json);

        return $response;
    }

    protected function assertDatabase(TestResponse $response, array $verify)
    {
        $model = $this->model();
        $table = (new $model)->getTable();
        $this->assertDatabaseHas($table, $verify + ['id' => $response->json('id')]);
    }

    private function assertJsonResponseContent(TestResponse $response, array $verify, array $json = null)
    {
        $test = $json ?? $verify;
        $response->assertJsonFragment($test + ['id' => $response->json('id')]);
    }

}
