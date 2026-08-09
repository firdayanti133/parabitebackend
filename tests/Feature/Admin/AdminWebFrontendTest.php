<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;

class AdminWebFrontendTest extends TestCase
{
    public function test_admin_login_uses_the_bundled_frontend_runtime(): void
    {
        $this->withoutVite();

        $this->get('/admin/login')
            ->assertOk()
            ->assertSee('Parabite Admin')
            ->assertSee('parseApiResponse', false)
            ->assertDontSee('cdn.jsdelivr.net/npm/alpinejs', false);
    }

    public function test_admin_dashboard_renders_shared_authentication_and_refresh_handling(): void
    {
        $this->withoutVite();

        $this->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('Admin Panel')
            ->assertSee('refreshAdminToken', false)
            ->assertSee("apiCall('/admin/dashboard')", false)
            ->assertDontSee('cdn.jsdelivr.net/npm/alpinejs', false);
    }

    public function test_every_admin_management_page_renders_without_blade_errors(): void
    {
        $this->withoutVite();

        foreach ([
            '/admin/users',
            '/admin/users/create',
            '/admin/users/1',
            '/admin/users/1/edit',
            '/admin/locations',
            '/admin/locations/create',
            '/admin/locations/1/edit',
        ] as $path) {
            $this->get($path)->assertOk();
        }
    }

    public function test_inactive_user_deactivation_action_is_disabled(): void
    {
        $this->withoutVite();

        $this->get('/admin/users')
            ->assertOk()
            ->assertSee(':disabled="!user.is_active"', false)
            ->assertSee("user.is_active ? 'Hapus' : 'Nonaktif'", false);
    }

    public function test_admin_management_uses_in_app_notifications_instead_of_browser_alerts(): void
    {
        $this->withoutVite();

        foreach ([
            '/admin/users',
            '/admin/users/create',
            '/admin/locations',
            '/admin/locations/create',
        ] as $path) {
            $this->get($path)
                ->assertOk()
                ->assertSee('showAdminNotification', false)
                ->assertDontSee('alert(', false);
        }
    }
}
