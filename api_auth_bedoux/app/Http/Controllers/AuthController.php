<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $authService;

    // Injection du service (si tu as un service dédié)
    public function __construct(\App\Services\AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "Name" => "required|string|max:50",
            "Email" => "required|email|max:255",
            "Password" => "required|string|min:6",
            "LieuNaissance" => "required|string|max:95",
            "DateNaissance" => "required|date",
            "Nom_Emploi" => "required|string|max:95",
            "Secteurs_Activite" => "required|string|max:150",
            "Type_Contrat" => "required|string|max:150",
            "Duree_Contrat" => "nullable|string|max:150",
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Données invalides",
                    "errors" => $validator->errors(),
                ],
                422
            );
        }

        // Appel du service (ton code métier ici)
        $result = $this->authService->register($request->all());

        $statusCode = $result["success"] ? 201 : 400;
        return response()->json($result, $statusCode);
    }
}
