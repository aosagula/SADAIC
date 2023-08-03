<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Traits\WorkDraftTrait;
use App\Http\Traits\FileUploaderTrait;
use App\Models\Work\Distribution;
use App\Models\Work\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    use WorkDraftTrait, FileUploaderTrait;

    public function __construct()
    {
        $this->middleware('auth:web,members');
    }

    public function downloadFile(File $file)
    {
        if(!$this->checkPermissions(Auth::user(), $file->registration)) {
            abort(403);
        }

        if (!Storage::exists($file->path)) {
            abort(404);
        }

        return Storage::download($file->path);
    }

    public function uploadFile(Request $request)
    {
        try {
            DB::beginTransaction();

            // Intentar determinar el mime type por contenido
            $mimeType = $request->file('file')->getMimeType();

            // No se pudo identificar el mime type por contenido
            if ($mimeType == 'application/octet-stream') {
                // Utilizamos el mime type informado por el navegador
                $mimeType = $request->file('file')->getClientMimeType();
            }

            if (!in_array($mimeType, ['image/png', 'image/jpeg', 'application/pdf', 'audio/mpeg'])) {
                return [
                    'status' => 'failed',
                    'errors' => [
                        'file_mime' => 'Formato de archivo no soportado (' . $mimeType . ')'
                    ]
                ];
            }

            // Tamaño máximo
            if($request->file('file')->getSize() > $this->getUploadMaxSize()) {
                return [
                    'status'  => 'failed',
                    'errors' => [
                        'file_size' => 'El tamaño del archivo es mayor a ' . $this->getFormattedMaxSize()
                    ]
                ];
            }

            $registration = $this->saveDraft($request->input());

            // Si la solicitud ya está en trámite no se pueden editar los adjuntos
            if($registration->status_id != null) {
                abort(403);
            }

            // Recuperamos la distribución
            $distribution = null;
            if ($request->has('distribution_id')) {
                $distribution = Distribution::find($request->has('distribution_id'));
            } elseif ($request->has('member_id') || $request->has('doc_number')) {
                $distribution = Distribution::where([
                    'registration_id' => $registration->id,
                    'member_id'       => $request->input('member_id', null) ?? '',
                    'doc_number'      => $request->input('doc_number', null) ?? '',
                ])->first();
            }

            $fileParams = [
                'registration_id' => $registration->id,
                'name'            => $request->input('name'),
                'distribution_id' => optional($distribution)->id
            ];

            // Guardamos el archivo
            $fileName = $request->input('name');
            if (isset($fileParams['distribution_id'])) {
                $fileName .= '_' . $fileParams['distribution_id'];
            }

            $fileName .= '.';
            if ($request->file('file')->extension() == 'bin') {
                $fileName .= $request->file('file')->getClientOriginalExtension();
            } else {
                $fileName .= $request->file('file')->extension();
            }

            $filePath = 'files/' . Auth::user()->type . 's/' . Auth::user()->id . '/work-registration/' . $registration->id;

            if (!Storage::exists($filePath)) {
                Storage::makeDirectory($filePath);
            }

            $path = Storage::putFileAs(
                $filePath,
                $request->file('file'),
                $fileName
            );

            // Creamos el registro del archivo
            $file = File::updateOrCreate($fileParams, [
                'name' => $request->input('name'),
                'path' => $path
            ]);

            DB::commit();

            return array_merge([
                'id'     => $file->id,
                'status' => 'success',
                'path'   => $path,
            ], $fileParams);
        } catch (Throwable $t) {
            Log::error("Error subiendo archivo de registro de obra",
                [
                    "error" => $t,
                    "data"  => json_encode($request->all(), JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_IGNORE )
                ]
            );

            DB::rollBack();

            return [
                'status'  => 'failed',
                'errors' => [
                    'Se produjo un error desconocido al momento de subir el archivo'
                ]
            ];
        }
    }

    public function deleteFile(File $file)
    {
        try {
            if(!$this->checkPermissions(Auth::user(), $file->registration)) {
                abort(403);
            }

            // Si la solicitud ya está en trámite no se pueden editar los adjuntos
            if($file->registration->status_id != null) {
                abort(403);
            }

            if (Storage::exists($file->path)) {
                // Borramos el archivo del disco
                Storage::delete($file->path);
            }

            // Borramos la entrada de la tabla de archivos
            $file->delete();

            return [ 'status' => 'success' ];
        } catch (Throwable $t) {
            Log::error("Error eliminando archivo de registro de obra",
                [
                    "error" => $t,
                    "data"  => json_encode($request->all(), JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_IGNORE )
                ]
            );

            return [
                'status'  => 'failed',
                'errors' => [
                    'Se produjo un error desconocido al momento de eliminar el archivo'
                ]
            ];
        }
    }

    /**
     * Verifica si $user tiene permitido acceder a $registration
     */
    private function checkPermissions($user, $registration)
    {
        // Si el usuario actual es uno de los usuarios del nuevo SP
        if ($user instanceof \App\Models\User) {
            // Solicitudes donde el email del usuario coincide con alguna de las partes o inició el trámite
            $mailRegistrations = DB::table('works_registration')
            ->select('id')
            ->whereIn('id', function($query) {
                $query->select('registration_id') // Solicitudes donde el email del usuario coincide con alguna de las partes
                ->from('works_distribution')
                ->whereIn('id', function($query) {
                    $query->select('distribution_id')
                    ->from('works_meta')
                    ->where('email', Auth::user()->email);
                });
            })
            ->orWhere('user_id', Auth::user()->id) // Solicitudes iniciadas por el usuario
            ->groupBy('id')
            ->get();

            if($mailRegistrations->contains('id', $registration->id)) {
                return true;
            }

        // Si el usuario actual es un socio
        } elseif ($user instanceof \App\Models\Member) {
            // Puede descargar archivos del los trámites que inició
            if ($user->id == $registration->member_id) {
                return true;
            }

            // Puede descargar archivos de los trámites en los que es parte
            if ($registration->distribution->contains('member_id', $user->member_id)) {
                return true;
            }
        }

        return false;
    }
}