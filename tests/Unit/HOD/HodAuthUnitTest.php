<?php

namespace Tests\Unit\HOD;

use Tests\TestCase;
use App\Models\Hod;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;
use Mockery;

class HodAuthUnitTest extends TestCase
{
    public function test_hod_model_has_correct_fillable_attributes()
    {
        $hod = new Hod();
        
        $expectedFillable = [
            'user_id',
            'department_id',
            'staff_id',
            'title',
            'phone',
            'office_location',
            'is_active',
            'appointed_at',
            'last_login_at',
            'permissions',
        ];
        
        $this->assertEquals($expectedFillable, $hod->getFillable());
    }

    public function test_hod_model_has_correct_casts()
    {
        $hod = new Hod();
        
        $expectedCasts = [
            'id' => 'int',
            'is_active' => 'boolean',
            'appointed_at' => 'datetime',
            'last_login_at' => 'datetime',
            'permissions' => 'array',
        ];
        
        $this->assertEquals($expectedCasts, $hod->getCasts());
    }

    public function test_hod_model_has_correct_hidden_attributes()
    {
        $hod = new Hod();
        
        $expectedHidden = ['permissions'];
        
        $this->assertEquals($expectedHidden, $hod->getHidden());
    }

    public function test_hod_has_permission_method_works_correctly()
    {
        $hod = new Hod();
        $hod->permissions = ['view_reports', 'manage_eligibility'];
        
        $this->assertTrue($hod->hasPermission('view_reports'));
        $this->assertTrue($hod->hasPermission('manage_eligibility'));
        $this->assertFalse($hod->hasPermission('delete_records'));
    }

    public function test_hod_has_permission_returns_false_for_null_permissions()
    {
        $hod = new Hod();
        $hod->permissions = null;
        
        $this->assertFalse($hod->hasPermission('view_reports'));
    }

    public function test_hod_get_auth_password_returns_user_password()
    {
        $user = new User();
        $user->password = 'hashed_password';
        
        $hod = new Hod();
        $hod->setRelation('user', $user);
        
        $this->assertNotNull($hod->getAuthPassword());
        $this->assertIsString($hod->getAuthPassword());
    }

    public function test_hod_get_auth_password_returns_null_without_user()
    {
        $hod = new Hod();
        
        $this->assertNull($hod->getAuthPassword());
    }

    public function test_hod_get_email_attribute_returns_user_email()
    {
        $user = new User();
        $user->email = 'hod@test.com';
        
        $hod = new Hod();
        $hod->setRelation('user', $user);
        
        $this->assertEquals('hod@test.com', $hod->email);
    }

    public function test_hod_get_name_attribute_returns_user_full_name()
    {
        $user = new User();
        $user->full_name = 'Test HOD';
        
        $hod = new Hod();
        $hod->setRelation('user', $user);
        
        $this->assertEquals('Test HOD', $hod->name);
    }

    public function test_hod_get_full_name_attribute_returns_user_full_name()
    {
        $user = new User();
        $user->full_name = 'Test HOD';
        
        $hod = new Hod();
        $hod->setRelation('user', $user);
        
        $this->assertEquals('Test HOD', $hod->full_name);
    }

    public function test_hod_get_display_name_attribute_includes_title()
    {
        $user = new User();
        $user->full_name = 'John Doe';
        
        $hod = new Hod();
        $hod->title = 'Dr.';
        $hod->setRelation('user', $user);
        
        $this->assertEquals('Dr. John Doe', $hod->display_name);
    }

    public function test_hod_get_display_name_attribute_without_title()
    {
        $user = new User();
        $user->full_name = 'John Doe';
        
        $hod = new Hod();
        $hod->title = null;
        $hod->setRelation('user', $user);
        
        $this->assertEquals('John Doe', $hod->display_name);
    }
}