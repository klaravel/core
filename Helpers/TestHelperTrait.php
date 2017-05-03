<?php 

namespace Modules\Core\Helpers;

use App\Exceptions\Handler;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Modules\Admin\Http\Admin;
use Modules\Admin\Http\AdminRole;

trait TestHelperTrait
{
    protected function disableExceptionHandling()
    {
        $this->app->instance(ExceptionHandler::class, new class extends Handler {
            public function __construct() {}
            public function report(\Exception $e) {}
            public function render($request, \Exception $e) {
                throw $e;
            }
        });
    }

    /**
     * Ajax post data
     * 
     * @param  string $url
     * @param  array  $id
     * @param  array  $params
     * @return self
     */
    protected function ajaxPost($url, $params = [], $id = []) 
    {
        return $this->post(route($url, $id),
            $params,
            ['X-Requested-With' => 'XMLHttpRequest']);
    }

    protected function getRoute($route, $params = []) 
    {
        return $this->get(route($route, $params));
    }

    /**
     * Ajax post data
     * 
     * @param  string $url
     * @param  array  $id
     * @param  array  $params
     * @return self
     */
    protected function ajaxPatch($url, $params = [], $id = []) 
    {
        return $this->patch(route($url, $id),
            $params, 
            ['X-Requested-With' => 'XMLHttpRequest']);
    }

    /**
     * Assertation for validation error
     * 
     * @param  array  $fields
     * @return self
     */
    protected function assertValidationErrors($response, $fields = [])
    {
        $response->assertStatus(422);

        foreach ($fields as $key) {
            $this->assertArrayHasKey($key, $response->decodeResponseJson());    
        }

        return $this;
    }

    /**
     * Assertation for validation error not in
     * 
     * @param  array  $fields
     * @return self
     */
    protected function assertValidationNotHasErrors($response, $fields = [])
    {
        foreach ($fields as $key) {
            $this->assertArrayNotHasKey($key, $response->decodeResponseJson());    
        }

        return $this;
    }

    /**
     * Generate new admin
     * 
     * @return Modules/Admin/Http/Admin
     */
    protected function getNewAdmin($params = []) 
    {
        \Notification::fake();

        return cfactory('admin', Admin::class)->create($params);
    }

    /**
     * Get loged in admin user
     * 
     * @return Modules/Admin/Http/Admin
     */
    protected function getLogedInAdmin($params = []) 
    {
        $admin = $this->getNewAdmin($params);
        $this->Be($admin, 'admin');

        return $admin;
    }

    protected function getLogedInSuperAdmin($params = []) 
    {
        $admin = $this->getLogedInAdmin($params);

        $role = AdminRole::create(['name' => 'superadmin', 'display_name' => 'Super Admin']);

        $admin->attachRole($role);

        return $admin;
    }

    protected function assertSearchResult($url, $params, $resultParams) 
    {
        $response = $this->ajaxPost($url, $params);
        $response->assertStatus(200);
        $response->assertJsonFragment($resultParams);
    }

    public function assertActionVerify($route, $action, $model) 
    {
        $whereAction = $action;
        switch ($action) {
            case 'active': $whereAction = 1; break;
            case 'inactive': $whereAction = 0; break;
        }

        $this->getLogedInSuperAdmin();

        // try to publish records which is not exits in database
        $response = $this->ajaxPost($route, 
            [
                'action' => $action,
                'ids' => '500', // not exits in database
            ]);

        $response->assertStatus(200);
        $this->assertArrayHasKey('message', $response->decodeResponseJson());

        // publish single records
        $response = $this->ajaxPost($route,
            [
                'action' => $action,
                'ids' => '23',
            ]);
        $response->assertStatus(200);
        $this->assertArrayHasKey('message', $response->decodeResponseJson());

        $totalRecords = $model::where('status', $whereAction)->where('id', 23)->count();
        $this->assertEquals(1, $totalRecords);

        // publish multiple records
        $response = $this->ajaxPost($route,
            [
                'action' => $action,
                'ids' => '24,25',
            ]);
        $response->assertStatus(200);
        $this->assertArrayHasKey('message', $response->decodeResponseJson());

        $totalRecords = $model::where('status', $whereAction)->whereIn('id', [24, 25])->count();
        $this->assertEquals(2, $totalRecords);
    }

    public function assertActionDeleteVerify($route, $model) 
    {
        $this->getLogedInSuperAdmin();

        // try to delete records which is not exits in database
        $response = $this->ajaxPost($route, [], 
            [
                'action' => 'delete',
                'ids' => '500', // not exits in database
            ]);
        $response->assertStatus(200);
        $this->assertArrayHasKey('message', $response->decodeResponseJson());

        // delete single records
        $response = $this->ajaxPost($route, [], 
            [
                'action' => 'delete',
                'ids' => '23',
            ]);
        $response->assertStatus(200);
        $this->assertArrayHasKey('message', $response->decodeResponseJson());

        $totalRecords = $model::count();
        $this->assertEquals(24, $totalRecords);

        // delete multiple records
        $response = $this->ajaxPost($route, [], 
            [
                'action' => 'delete',
                'ids' => '24,25',
            ]);
        $response->assertStatus(200);
        $this->assertArrayHasKey('message', $response->decodeResponseJson());

        $totalRecords = $model::count();
        $this->assertEquals(22, $totalRecords);
    }
}
