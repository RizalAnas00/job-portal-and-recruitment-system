<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // ===============================
            // Admin Permissions
            // ===============================

            // Role Management
            ['name' => 'role.create', 'display_name' => 'Create Role', 'group' => 'Role Management'],
            ['name' => 'role.read', 'display_name' => 'View Roles', 'group' => 'Role Management'],
            ['name' => 'role.update', 'display_name' => 'Update Roles', 'group' => 'Role Management'],
            ['name' => 'role.delete', 'display_name' => 'Delete Roles', 'group' => 'Role Management'],

            // User Management
            ['name' => 'user.read', 'display_name' => 'View Users', 'group' => 'User Management'],
            ['name' => 'user.update', 'display_name' => 'Update Users', 'group' => 'User Management'],
            ['name' => 'user.delete', 'display_name' => 'Delete Users', 'group' => 'User Management'],

            // Job Seeker Management
            ['name' => 'job_seeker.read', 'display_name' => 'View Job Seekers', 'group' => 'Job Seeker Management'],
            ['name' => 'job_seeker.update', 'display_name' => 'Update Job Seekers', 'group' => 'Job Seeker Management'],
            ['name' => 'job_seeker.delete', 'display_name' => 'Delete Job Seekers', 'group' => 'Job Seeker Management'],

            // Company Management
            ['name' => 'company.read', 'display_name' => 'View Companies', 'group' => 'Company Management'],
            ['name' => 'company.update', 'display_name' => 'Update Companies', 'group' => 'Company Management'],
            ['name' => 'company.delete', 'display_name' => 'Delete Companies', 'group' => 'Company Management'],

            // Job Posting Management
            ['name' => 'job_posting.read', 'display_name' => 'View Job Postings', 'group' => 'Job Posting Management'],
            ['name' => 'job_posting.update', 'display_name' => 'Update Job Postings', 'group' => 'Job Posting Management'],
            ['name' => 'job_posting.delete', 'display_name' => 'Delete Job Postings', 'group' => 'Job Posting Management'],
            ['name' => 'job_posting.update_status', 'display_name' => 'Update Job Posting Status', 'group' => 'Job Posting Management'],

            // Application & Interview
            ['name' => 'application.read', 'display_name' => 'View Applications', 'group' => 'Application Management'],
            ['name' => 'application.filter', 'display_name' => 'Filter Applications', 'group' => 'Application Management'],
            ['name' => 'application.delete', 'display_name' => 'Delete Applications', 'group' => 'Application Management'],
            ['name' => 'interview.read', 'display_name' => 'View Interviews', 'group' => 'Interview Management'],
            ['name' => 'interview.delete', 'display_name' => 'Delete Interviews', 'group' => 'Interview Management'],

            // Master Data
            ['name' => 'skill.create', 'display_name' => 'Create Skills', 'group' => 'Skill Management'],
            ['name' => 'skill.read', 'display_name' => 'View Skills', 'group' => 'Skill Management'],
            ['name' => 'skill.update', 'display_name' => 'Update Skills', 'group' => 'Skill Management'],
            ['name' => 'skill.delete', 'display_name' => 'Delete Skills', 'group' => 'Skill Management'],

            ['name' => 'subscription_plan.create', 'display_name' => 'Create Subscription Plans', 'group' => 'Subscription Plan Management'],
            ['name' => 'subscription_plan.read', 'display_name' => 'View Subscription Plans', 'group' => 'Subscription Plan Management'],
            ['name' => 'subscription_plan.update', 'display_name' => 'Update Subscription Plans', 'group' => 'Subscription Plan Management'],
            ['name' => 'subscription_plan.delete', 'display_name' => 'Delete Subscription Plans', 'group' => 'Subscription Plan Management'],

            // Finance
            ['name' => 'company_subscription.crud', 'display_name' => 'Manage Company Subscriptions', 'group' => 'Finance Management'],
            ['name' => 'payment_transaction.crud', 'display_name' => 'Manage Payment Transactions', 'group' => 'Finance Management'],

            // Notifications
            ['name' => 'notification.create', 'display_name' => 'Send Notifications', 'group' => 'Notification Management'],
            ['name' => 'notification.read', 'display_name' => 'View Notifications', 'group' => 'Notification Management'],
            ['name' => 'notification.delete', 'display_name' => 'Delete Notifications', 'group' => 'Notification Management'],

            // ===============================
            // Job Seeker Permissions
            // ===============================

            ['name' => 'job_seeker.create', 'display_name' => 'Register Account', 'group' => 'Job Seeker Profile'],
            ['name' => 'job_seeker.read.own', 'display_name' => 'View Own Profile', 'group' => 'Job Seeker Profile'],
            ['name' => 'job_seeker.update.own', 'display_name' => 'Update Own Profile', 'group' => 'Job Seeker Profile'],
            ['name' => 'job_seeker.delete.own', 'display_name' => 'Delete Own Account', 'group' => 'Job Seeker Profile'],

            ['name' => 'resume.crud', 'display_name' => 'Manage Own Resume', 'group' => 'Resume Management'],
            ['name' => 'company.read', 'display_name' => 'View Companies', 'group' => 'Company Browsing'],
            ['name' => 'job_posting.read', 'display_name' => 'View Job Postings', 'group' => 'Job Browsing'],

            ['name' => 'application.create', 'display_name' => 'Apply for Jobs', 'group' => 'Application Management'],
            ['name' => 'application.read.own', 'display_name' => 'View Own Applications', 'group' => 'Application Management'],
            ['name' => 'application.update.own', 'display_name' => 'Cancel Own Application', 'group' => 'Application Management'],

            ['name' => 'interview.read.own', 'display_name' => 'View Own Interviews', 'group' => 'Interview Management'],

            ['name' => 'job_seeker_skill.create', 'display_name' => 'Add Own Skills', 'group' => 'Skill Management'],
            ['name' => 'job_seeker_skill.delete', 'display_name' => 'Delete Own Skills', 'group' => 'Skill Management'],

            ['name' => 'notification.read.own', 'display_name' => 'View Own Notifications', 'group' => 'Notification Management'],
            ['name' => 'notification.update.own', 'display_name' => 'Mark Notifications Read', 'group' => 'Notification Management'],
            ['name' => 'notification.delete.own', 'display_name' => 'Delete Own Notifications', 'group' => 'Notification Management'],

            // ===============================
            // Company Permissions
            // ===============================

            ['name' => 'job_seeker.read', 'display_name' => 'View Job Seekers', 'group' => 'Job Seeker Profile'],

            ['name' => 'company.create', 'display_name' => 'Register Company', 'group' => 'Company Profile'],
            ['name' => 'company.read.own', 'display_name' => 'View Own Profile', 'group' => 'Company Profile'],
            ['name' => 'company.update.own', 'display_name' => 'Update Own Profile', 'group' => 'Company Profile'],
            ['name' => 'company.delete.own', 'display_name' => 'Delete Own Account', 'group' => 'Company Profile'],

            ['name' => 'job_posting.crud.own', 'display_name' => 'Manage Own Job Postings', 'group' => 'Job Posting Management'],
            ['name' => 'application.read.own', 'display_name' => 'View Applications for Own Jobs', 'group' => 'Application Management'],
            ['name' => 'application.update.own', 'display_name' => 'Update Applications for Own Jobs', 'group' => 'Application Management'],

            ['name' => 'interview.crud.own', 'display_name' => 'Manage Own Interviews', 'group' => 'Interview Management'],

            ['name' => 'job_posting_skill.create', 'display_name' => 'Add Job Posting Skills', 'group' => 'Skill Management'],
            ['name' => 'job_posting_skill.delete', 'display_name' => 'Delete Job Posting Skills', 'group' => 'Skill Management'],

            ['name' => 'subscription_plan.read', 'display_name' => 'View Subscription Plans', 'group' => 'Subscription Plan'],
            ['name' => 'company_subscription.create', 'display_name' => 'Start Subscription', 'group' => 'Company Subscription'],
            ['name' => 'company_subscription.read.own', 'display_name' => 'View Own Subscriptions', 'group' => 'Company Subscription'],
            ['name' => 'payment_transaction.read.own', 'display_name' => 'View Own Payments', 'group' => 'Finance'],

            ['name' => 'notification.read.own', 'display_name' => 'View Own Notifications', 'group' => 'Notification Management'],
            ['name' => 'notification.update.own', 'display_name' => 'Mark Notifications Read', 'group' => 'Notification Management'],
            ['name' => 'notification.delete.own', 'display_name' => 'Delete Own Notifications', 'group' => 'Notification Management'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                ['display_name' => $permission['display_name'], 'group' => $permission['group']]
            );
        }
    }
}
