<?php

namespace WPVABR\Api;

use WP_REST_Controller;

class User_Route extends WP_REST_Controller
{

    protected $namespace;
    protected $rest_base;

    public function __construct()
    {
        $this->namespace = 'wpvbr/v1';
        $this->rest_base_login = 'login';
        $this->rest_base_register = 'register';
        $this->rest_base_logout = 'logout';
        $this->rest_base_cookie = 'checkLoginCookie';
    }

    /**
     * Register Routes
     */
    public function register_routes()
    {
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base_login,
            [
                [
                    'methods'             => \WP_REST_Server::CREATABLE,
                    'callback'            => [$this, 'func_user_login'],
                    'permission_callback' => [$this, 'get_items_permission_check']
                ],

            ]
        );

        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base_register,
            [
                [
                    'methods'             => \WP_REST_Server::CREATABLE,
                    'callback'            => [$this, 'func_user_register'],
                    'permission_callback' => [$this, 'get_items_permission_check']
                ],

            ]
        );
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base_logout,
            [
                [
                    'methods'             => \WP_REST_Server::CREATABLE,
                    'callback'            => [$this, 'func_user_logout'],
                    'permission_callback' => [$this, 'get_items_permission_check']
                ],

            ]
        );

        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base_cookie,
            [
                [
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => [$this, 'func_user_cookies'],
                    'permission_callback' => [$this, 'get_items_permission_check']
                ],

            ]
        );
    }




    function func_user_login($request)
    {

        $response_body = $request->get_json_params();
        $user_login = isset($response_body['user_login']) ? sanitize_text_field($response_body['user_login']) : '';
        $password = isset($response_body['password']) ? sanitize_text_field($response_body['password']) : '';


        if (empty($user_login)) {
            $response_data = array('status' => '403', 'error_code' => 'user_login_empty', 'error_message' => 'Email Or Username is empty');
            $data[] = $this->prepare_response_for_collection($response_data);
            return rest_ensure_response($data);
        } elseif (empty($password)) {
            $response_data = array('status' => '403', 'error_code' => 'password_empty', 'error_message' => 'Password field is empty');
            $data[] = $this->prepare_response_for_collection($response_data);
        } else {
            if (is_email($user_login)) {
                if (!email_exists($user_login)) {
                    $response_data = array('status' => '403', 'error_code' => 'email_not_exist', 'error_message' => 'Email doesnot exist in our system');
                    $data[] = $this->prepare_response_for_collection($response_data);
                    return rest_ensure_response($data);
                }

                $user_obj = get_user_by('email', $user_login);
                $user = wp_authenticate($user_obj->data->user_login, $password);
            } else {
                $user = wp_authenticate($user_login, $password);
            }

            if (is_wp_error($user)) {
                $error = 'Invalid username/email and/or password.';
                if (isset($user->errors['pending_approval'])) {
                    $error = $user->errors['pending_approval'];
                }
                if (isset($user->errors['denied_access'])) {
                    $error = $user->errors['denied_access'];
                }

                $error_code = $user->get_error_code();

                if ($error_code == 'invalid_username') {
                    $error = 'Invalid username.';
                } else if ($error_code == 'incorrect_password') {
                    $error = 'The password you entered is incorrect.';
                } else {
                    $error = $user->get_error_message($error_code);
                }

                $response_data = array('status' => '403', 'error_code' => 'invalid_auth_user', 'error_message' => $error);
                $data[] = $this->prepare_response_for_collection($response_data);
                return rest_ensure_response($data);
            }
        }
        $full_name = get_user_meta($user->data->ID, 'display_name', true);
        $first_name = get_user_meta($user->data->ID, 'first_name', true);
        $user = wp_signon(
            [
                'user_login' => $user_login,
                'user_password' => $password,
            ],
        );
        $response_data = array(
            'user_email' => $user->data->user_email,
            'user_login' => $user->data->user_login,
            'full_name' => $full_name,
            'first_name' => $first_name,
            'user_id' => (int) $user->data->ID,
            'url' => home_url()
        );

        $data[] = $this->prepare_response_for_collection($response_data);
        return rest_ensure_response($data);
    }



    public function func_user_register($request)
    {

        $response_body = $request->get_json_params();

        if (!empty($response_body)) {

            $user_name = isset($response_body['username']) ? sanitize_text_field($response_body['username']) : '';
            $user_email = isset($response_body['email']) ? sanitize_email($response_body['email']) : '';
            $password = isset($response_body['password']) ? sanitize_text_field($response_body['password']) : '';


            if (empty($user_name)) {
                $response_data = array('status' => '403', 'error_code' => 'empty_user_name', 'error_message' => 'User Name is empty');
                $data[] = $this->prepare_response_for_collection($response_data);
                return rest_ensure_response($data);
            }
            if (empty($user_email)) {
                $response_data = array('status' => '403', 'error_code' => 'empty_email', 'error_message' => 'Email is empty');
                $data[] = $this->prepare_response_for_collection($response_data);
                return rest_ensure_response($data);
            } elseif (!is_email($user_email)) {
                $response_data = array('status' => '403', 'error_code' => 'email_invalid', 'error_message' => 'Email is not valid');
                $data[] = $this->prepare_response_for_collection($response_data);
                return rest_ensure_response($data);
            } elseif (email_exists($user_email)) {
                $response_data = array('status' => '403', 'error_code' => 'email_used', 'error_message' => 'Email already registered');
                $data[] = $this->prepare_response_for_collection($response_data);
                return rest_ensure_response($data);
            }
            if (empty($password)) {
                $response_data = array('status' => '403', 'error_code' => 'empty_password', 'error_message' => 'Password is empty');
                $data[] = $this->prepare_response_for_collection($response_data);
                return rest_ensure_response($data);
            }


            //Not error
            global $wpdb;
            $user_login_sql = "select count(*) from $wpdb->prefix" . "users us
                                        where SUBSTR(us.user_email,1, INSTR(us.user_email,'@')-1)= %s";

            $user_login_sql_safe = $wpdb->prepare($user_login_sql, $user_name);
            $user_login_count = $wpdb->get_var($user_login_sql_safe);
            if ($user_login_count > 0) {
                $user_name = $user_name . ($user_login_count + 1);
            }
            $userid = wp_create_user($user_name, $password, $user_email);
            if (is_wp_error($userid)) {
                $response_data = array('status' => '403', 'error_code' => 'error_user_create', 'error_message' => 'Sorry, We are unable to register');
                $data[] = $this->prepare_response_for_collection($response_data);
                return rest_ensure_response($data);
            } else {
                update_user_meta($userid, 'display_name', $user_name);
                wp_new_user_notification($userid);
                $response['message'] = 'Successfully Register';
                $response['user_id'] = $userid;
                $data[] = $this->prepare_response_for_collection($response);
                return rest_ensure_response($data);
                // return rest_ensure_response($data);
            }
        } else {
            $response_data = array('status' => '403', 'error_code' => 'empty_post_request', 'error_message' => 'Post Request is empty');
            $data[] = $this->prepare_response_for_collection($response_data);
            return rest_ensure_response($data);
        }
    }

    public function func_user_logout($request)
    {
        wp_logout();
        $response_data = array('message' => 'Success');
        $data[] = $this->prepare_response_for_collection($response_data);
        return rest_ensure_response($data);
    }

    public function func_user_cookies($request)
    {
        wp_logout();
        $response_data = array('message' => 'Success');
        $data[] = $this->prepare_response_for_collection($response_data);
        return rest_ensure_response($data);
    }

    /**
     * Get items permission check
     */
    public function get_items_permission_check($request)
    {
        return true;
    }


    /**
     * Create item permission check
     */
    public function create_items_permission_check($request)
    {
        return true;
    }

    /**
     * Retrives the query parameters for the items collection
     */
    public function get_collection_params()
    {
        return [];
    }
}
