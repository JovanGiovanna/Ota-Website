<?php

namespace App\Traits;

use App\Helpers\SweetAlertHelper;
use Illuminate\Support\Facades\Validator;

trait CrudNotificationTrait
{
    /**
     * Handle create response with notification
     */
    public function handleCreateSuccess($resourceName = 'Data', $redirectUrl = null, $data = null)
    {
        $response = [
            'status' => 'success',
            'title' => 'Created!',
            'message' => $resourceName . ' has been created successfully.',
            'icon' => 'success'
        ];

        if ($redirectUrl) {
            $response['redirect'] = $redirectUrl;
            $response['delay'] = 1500;
        }

        if ($data) {
            $response['data'] = $data;
        }

        return response()->json($response);
    }

    /**
     * Handle update response with notification
     */
    public function handleUpdateSuccess($resourceName = 'Data', $redirectUrl = null, $data = null)
    {
        $response = [
            'status' => 'success',
            'title' => 'Updated!',
            'message' => $resourceName . ' has been updated successfully.',
            'icon' => 'success'
        ];

        if ($redirectUrl) {
            $response['redirect'] = $redirectUrl;
            $response['delay'] = 1500;
        }

        if ($data) {
            $response['data'] = $data;
        }

        return response()->json($response);
    }

    /**
     * Handle delete response with notification
     */
    public function handleDeleteSuccess($resourceName = 'Data', $redirectUrl = null)
    {
        $response = [
            'status' => 'success',
            'title' => 'Deleted!',
            'message' => $resourceName . ' has been deleted successfully.',
            'icon' => 'success'
        ];

        if ($redirectUrl) {
            $response['redirect'] = $redirectUrl;
            $response['delay'] = 1500;
        }

        return response()->json($response);
    }

    /**
     * Handle validation error response
     */
    public function handleValidationError($validator, $statusCode = 422)
    {
        return response()->json([
            'status' => 'error',
            'title' => 'Validation Error',
            'message' => $this->formatValidationErrors($validator),
            'icon' => 'error',
            'errors' => $validator->errors()
        ], $statusCode);
    }

    /**
     * Handle generic error response
     */
    public function handleError($title = 'Error', $message = 'An error occurred', $statusCode = 500)
    {
        return response()->json([
            'status' => 'error',
            'title' => $title,
            'message' => $message,
            'icon' => 'error'
        ], $statusCode);
    }

    /**
     * Handle not found error
     */
    public function handleNotFound($resourceName = 'Resource')
    {
        return response()->json([
            'status' => 'error',
            'title' => 'Not Found',
            'message' => $resourceName . ' not found.',
            'icon' => 'error'
        ], 404);
    }

    /**
     * Handle unauthorized error
     */
    public function handleUnauthorized($message = 'You are not authorized to perform this action')
    {
        return response()->json([
            'status' => 'error',
            'title' => 'Unauthorized',
            'message' => $message,
            'icon' => 'error'
        ], 403);
    }

    /**
     * Format validation errors for SweetAlert
     */
    protected function formatValidationErrors($validator)
    {
        $errors = $validator->errors()->all();
        return implode(' | ', $errors);
    }

    /**
     * Validate request with custom rules
     */
    protected function validateRequest($data, $rules, $messages = [])
    {
        return Validator::make($data, $rules, $messages);
    }
}
