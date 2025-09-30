<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::firstOrCreate(
            ['name' => 'admin'],
            ['display_name' => 'Administrator', 'description' => 'Full system access', 'is_active' => true]
        );
        $jobSeeker = Role::firstOrCreate(
            ['name' => 'user'], 
            ['display_name' => 'Job Seeker', 'description' => 'Job seeker with limited access', 'is_active' => true]
        );
        $company = Role::firstOrCreate(
            ['name' => 'company'],
            ['display_name' => 'Company', 'description' => 'Company with job posting access', 'is_active' => true]
        );

        $adminPermissions = Permission::all();
        $admin->permissions()->sync($adminPermissions->pluck('id'));

        $jobSeekerPermissions = Permission::whereIn('name', [
            'job_seeker.create',
            'job_seeker.read.own',
            'job_seeker.update.own',
            'job_seeker.delete.own',
            'resume.crud',
            'company.read',
            'job_posting.read',
            'application.create',
            'application.read.own',
            'application.update.own',
            'interview.read.own',
            'job_seeker_skill.create',
            'job_seeker_skill.delete',
            'notification.read.own',
            'notification.update.own',
            'notification.delete.own',
        ])->get();
        $jobSeeker->permissions()->sync($jobSeekerPermissions->pluck('id'));

        $companyPermissions = Permission::whereIn('name', [
            'job_seeker.read',
            'company.create',
            'company.read.own',
            'company.update.own',
            'company.delete.own',
            'job_posting.crud.own',
            'application.read.own',
            'application.update.own',
            'interview.crud.own',
            'job_posting_skill.create',
            'job_posting_skill.delete',
            'subscription_plan.read',
            'company_subscription.create',
            'company_subscription.read.own',
            'payment_transaction.read.own',
            'notification.read.own',
            'notification.update.own',
            'notification.delete.own',
        ])->get();
        
        $company->permissions()->sync($companyPermissions->pluck('id'));
    }
}