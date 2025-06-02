<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class AddFluxController extends Controller
{
    public function addFlux(Request $request)
    {
        try {
            // Validation des données
            $validated = $request->validate([
                'Name' => 'required|string|max:95',
                'Montant' => 'required|numeric',
                'TypeFlux' => 'required|string|in:active,passive',
                'date_' => 'nullable|date',
                'ID_1' => 'required|string|max:50',
                'Id_2' => 'required|string|max:50',
            ]);

            // Conversion de "active"/"passive" vers boolean
            $typeFlux = $request->TypeFlux === 'active';

            // Génération d'un ID unique
            $id = Str::uuid()->toString();

            // Insertion dans la base
            DB::table('Flux')->insert([
                'ID' => $id,
                'Name' => $validated['Name'],
                'Montant' => $validated['Montant'],
                'TypeFlux' => $typeFlux,
                'date_' => $validated['date_'] ?? now(),
                'ID_1' => $validated['ID_1'],
                'Id_2' => $validated['Id_2'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Flux ajouté avec succès',
                'data' => [
                    'ID' => $id,
                    'Name' => $validated['Name'],
                    'Montant' => $validated['Montant'],
                    'TypeFlux' => $request->TypeFlux,
                    'date_' => $validated['date_'] ?? now(),
                    'ID_1' => $validated['ID_1'],
                    'Id_2' => $validated['Id_2']
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'ajout du flux: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'ajout du flux',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
