<?php

/**
 * Home Controller
 * 
 * Handles home page and public pages
 */

class HomeController
{
    /**
     * Show home page
     * 
     * @return void
     */
    public function index(): void
    {
        // If user is authenticated, redirect to dashboard
        if (auth()) {
            redirect(url('/dashboard'));
            return;
        }
        
        // Otherwise, redirect to login
        redirect(url('/auth/login'));
    }
}
