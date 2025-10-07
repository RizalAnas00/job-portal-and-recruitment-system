<?php

if (! function_exists('formatRoleName')) {
    /**
     * Convert display name to lowercase snake_case.
     *
     * Example: "Super Admin" → "super_admin"
     */
    function formatRoleName(string $roleName): string
    {
        return strtolower(str_replace(' ', '_', trim($roleName)));
    }
}