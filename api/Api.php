<?php

namespace WPVABR\Api;

use WP_REST_Controller;
use WPVABR\Api\User_Route;

/**
 * Rest API Handler
 */
class Api extends WP_REST_Controller
{

    /**
     * Construct Function
     */
    public function __construct()
    {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    /**
     * Register API routes
     */
    public function register_routes()
    {
        (new User_Route())->register_routes();
    }
}
