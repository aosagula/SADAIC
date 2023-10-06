<?php

namespace App\Http\Controllers;

use App\Models\Work\Distribution;
use App\Models\Work\Registration as WorkRegistration;
use App\Models\Jingles\Registration as JingleRegistration;
use App\Models\Members\Registration as MemberRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;

use App\Traits\FileTrait;
use DateTime;
use ZipArchive;

class IntegrationController extends Controller
{
    use FileTrait;
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('integration.index');
    }

    public function exportZipFile($works){
        

        $works_data = $works->map(function(WorkRegistration $work) {
            $interestedParties = $work->distribution->map(function(Distribution $dist) {
                $porcentPer = str_pad($dist->public * 100, 5, '0', STR_PAD_LEFT);
                $porcentMec = str_pad(strval($dist->mechanic * 100), 5, '0', STR_PAD_LEFT);
                $porcentSyn = str_pad(strval($dist->sync * 100), 5, '0', STR_PAD_LEFT);

                return [
                    'nameNumber' => $dist->type == 'member' ? $dist->member->ipname : 99999999999,
                    'name'       => $dist->type == 'member' ? ucwords(strtolower(optional($dist->member)->nombre)) : $dist->meta->name,
                    'role'       => $dist->fn,
                    'porcentPer' => (string) $porcentPer,
                    'porcentMec' => (string) $porcentMec,
                    'porcentSyn' => (string) $porcentSyn
                ];
            });

            $sheetMusicFile = new \stdClass();
            $audioFile = new \stdClass();

            $work->files->map(function($file) use ($sheetMusicFile) {
                if ($file->name == 'lyric_file') {
                    $fileName = explode('/', $file->path);
                    $sheetMusicFile->fileName = $fileName[count($fileName) - 1];
                    $sheetMusicFile->filePath = $this->addUrlFile($file->path);
                }
            });

            $work->files->map(function($file) use ($audioFile) {
                if ($file->name == 'audio_file') {
                    $fileName = explode('/', $file->path);
                    $audioFile->fileName = $fileName[count($fileName) - 1];
                    $audioFile->filePath = $this->addUrlFile($file->path);
                }
            });

            /**
             *  Add this lines
             */
            $unpublishedDate = ($work->dnda_in_date) ? (new DateTime($work->dnda_in_date))->format('Y-m-d') : null;
            $editedDate = ($work->dnda_ed_date) ? (new DateTime($work->dnda_ed_date))->format('Y-m-d') : null;
            
        

            $data = [
                'submissionId'      => $work->id,
                'agency'            => '128',
                'originalTitle'     => $work->title,
                'otherTitles'       =>  $work->titles->map(function($t){
                    return $t->title;
                }),
                'albumTitle'        => $work->dnda_title,
                'genre'             => $work->genre_id,
                'duration'          => $work->duration,
                'jingle'            => $work->is_jingle == 1 ? 'S' : 'N',
                'musicMovies'       => $work->is_movie == 1 ? 'S' : 'N',
                'unpublishedDndaNumberLetter' => intval($work->lyric_dnda_in_file),
                'unpublishedDndaNumberMusic' => intval($work->audio_dnda_in_file),
                'unpublishedDate' => $unpublishedDate , #$work->dnda_in_date,
                'editedDndaNumberLetter' => intval($work->lyric_dnda_ed_file),
                'editedDndaNumberMusic' => intval($work->audio_dnda_ed_file),
                'editedDate' =>  $editedDate, # $work->dnda_ed_date,
                'interestedParties' => $interestedParties,
                'sheetMusicFile' => $sheetMusicFile,
                'audioFile' => $audioFile
            ];

            return $data;
        });

        // Directorio temporal para almacenar los archivos
        $tempDir = storage_path('temp');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        $date = new DateTime('now');
        $zipFile = $tempDir.'/works'.$date->format('Y-m-d\TH:i:s').'.zip';
        $zip = new ZipArchive();

        foreach ( $works_data as $work){
            $date = new DateTime('now');
            $dt = $date->format('Y-m-d\TH:i:s.uT');
            $dtf = $date->format('Y-m-d\TH:i:s');
            $fileContents = [
                '$schema'    => './work_schema.json',
                'fileHeader' => [
                    'submittingAgency'     => '128',
                    'fileCreationDateTime' => $dt,
                    'receivingAgency'      => '061'
                ],
                'addWorks' => $work
            ];

            $fileName = 'work-'.$work['submissionId'].'-';
            $fileName .= $dtf;
            $fileName .= '-128-061-registros.json';


            $headers = [
                'X-Accel-Buffering' => 'no',
                'Content-Encoding' => 'utf-8',
                'Content-Type'     => 'application/json',
                'Content-Disposition' => 'attachment; filename=' . $fileName,
            ];
            $output = json_encode($fileContents, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
            $patterns = [
                '/("nameNumber"): "(\d*)"/m',
                '/("porcentPer"): "(\d*)"/m',
                '/("porcentMec"): "(\d*)"/m',
                '/("porcentSyn"): "(\d*)"/m',
            ];

            $output = preg_replace($patterns, '${1}: ${2}', $output);

            file_put_contents($tempDir . '/' . $fileName, $output);
            
            // 
         
        }
        if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {

            $archivos = scandir($tempDir);
            foreach ($archivos as $archivo) {
                if ($archivo !== '.' && $archivo !== '..') {
                    $zip->addFile($tempDir . '/' . $archivo, $archivo);
                }
            }
            $zip->close();
        }

        // Elimina los archivos temporales
        foreach ($archivos as $archivo) {
            if ($archivo !== '.' && $archivo !== '..') {
                if ( File::extension($archivo) == 'json'){
                    $work_id = explode ( '-', $archivo)[1];
                    $this->saveExportFilename( $work_id, $archivo, $dt);
                }
                
                unlink($tempDir . '/' . $archivo);
            }
        }

        // Descarga el archivo ZIP
        return response()->download($zipFile)->deleteFileAfterSend(true);

        
    }
    /**
     *  2023.08.28 - Alejandro Sagula
     *  Add new attribute called otherTitles, 
     *  an array with subtitles when exists
     * 
     *  2023.09.28 - Alejandro Sagula
     *  export each work as a single json file, if there is more than one work 
     *  the download is ziped with all works, 
     *  if there is 1 the json file is generated without zip
     * 
     *  each file name is store in each work in the data base. 
     *  The export will download a file only if the work dosent have a filename linked.
     * 
     */
    public function exportWorks()
    {
        $works = WorkRegistration::where('status_id', 6)
                                    ->where( function($query) {
                                        $query->whereNull('export_filename')
                                              ->orWhere('export_filename', '');
                                    })
                                    
                                    ->with('titles')
                                    ->get();
        
        Session::forget('integration_error');
        // if ($works->count() > 1){
        //     return $this->exportZipFile($works);
        // }
        // elseif($works->count() == 1) {
        //     return $this->exportSingleFile($works);
        // }
        if ($works->count()> 0)
            return $this->exportSingleFile($works);
        else{
            return back()->with(['integration_error' => 'No hay obras en Estado Para enviar a Procesamiento Interno']);
        }
    }

    public function exportSingleFile($works){
        $works_data = $works->map(function(WorkRegistration $work) {
            $interestedParties = $work->distribution->map(function(Distribution $dist) {
                $porcentPer = str_pad($dist->public * 100, 5, '0', STR_PAD_LEFT);
                $porcentMec = str_pad(strval($dist->mechanic * 100), 5, '0', STR_PAD_LEFT);
                $porcentSyn = str_pad(strval($dist->sync * 100), 5, '0', STR_PAD_LEFT);

                return [
                    'nameNumber' => $dist->type == 'member' ? $dist->member->ipname : 99999999999,
                    'name'       => $dist->type == 'member' ? ucwords(strtolower(optional($dist->member)->nombre)) : $dist->meta->name,
                    'role'       => $dist->fn,
                    'porcentPer' => (string) $porcentPer,
                    'porcentMec' => (string) $porcentMec,
                    'porcentSyn' => (string) $porcentSyn
                ];
            });

            $sheetMusicFile = new \stdClass();
            $audioFile = new \stdClass();

            $work->files->map(function($file) use ($sheetMusicFile) {
                if ($file->name == 'lyric_file') {
                    $fileName = explode('/', $file->path);
                    $sheetMusicFile->fileName = $fileName[count($fileName) - 1];
                    $sheetMusicFile->filePath = $this->addUrlFile($file->path);
                }
            });

            $work->files->map(function($file) use ($audioFile) {
                if ($file->name == 'audio_file') {
                    $fileName = explode('/', $file->path);
                    $audioFile->fileName = $fileName[count($fileName) - 1];
                    $audioFile->filePath = $this->addUrlFile($file->path);
                }
            });

            /**
             *  Add this lines
             */
            $unpublishedDate = ($work->dnda_in_date) ? (new DateTime($work->dnda_in_date))->format('Y-m-d') : null;
            $editedDate = ($work->dnda_ed_date) ? (new DateTime($work->dnda_ed_date))->format('Y-m-d') : null;
            
        

            $data = [
                'submissionId'      => $work->id,
                'agency'            => '128',
                'originalTitle'     => $work->title,
                'otherTitles'       =>  $work->titles->map(function($t){
                    return $t->title;
                }),
                'albumTitle'        => $work->dnda_title,
                'genre'             => $work->genre_id,
                'duration'          => $work->duration,
                'jingle'            => $work->is_jingle == 1 ? 'S' : 'N',
                'musicMovies'       => $work->is_movie == 1 ? 'S' : 'N',
                'unpublishedDndaNumberLetter' => intval($work->lyric_dnda_in_file),
                'unpublishedDndaNumberMusic' => intval($work->audio_dnda_in_file),
                'unpublishedDate' => $unpublishedDate , #$work->dnda_in_date,
                'editedDndaNumberLetter' => intval($work->lyric_dnda_ed_file),
                'editedDndaNumberMusic' => intval($work->audio_dnda_ed_file),
                'editedDate' =>  $editedDate, # $work->dnda_ed_date,
                'interestedParties' => $interestedParties,
                'sheetMusicFile' => $sheetMusicFile,
                'audioFile' => $audioFile
            ];

            return $data;
        });

        $date = new DateTime('now');
        $dt = $date->format('Y-m-d\TH:i:s.uT');
        $dtf = $date->format('Y-m-d\TH:i:s');
        $fileContents = [
            '$schema'    => './work_schema.json',
            'fileHeader' => [
                'submittingAgency'     => '128',
                'fileCreationDateTime' => $dt,
                'receivingAgency'      => '061'
            ],
            'addWorks' => $works_data
        ];

        $fileName = 'work-';
        $fileName .= $dtf;
        $fileName .= '-128-061-registros.json';


        foreach($works_data as $work){
            $this->saveExportFilename( $work['submissionId'], $fileName, $dt);
        }
        //

        return response()->streamDownload(function() use ($fileContents) {
            $output = json_encode($fileContents, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

            // Transformar los strings que representan números con leading zeros a
            // "números" con leading zeros. Se hace de forma manual porque el estandar
            // de JSON no soporta leading zeros para los números
            $patterns = [
                '/("nameNumber"): "(\d*)"/m',
                '/("porcentPer"): "(\d*)"/m',
                '/("porcentMec"): "(\d*)"/m',
                '/("porcentSyn"): "(\d*)"/m',
            ];

            $output = preg_replace($patterns, '${1}: ${2}', $output);

            echo $output;
        }, $fileName, [
            'Content-Encoding' => 'utf-8',
            'Content-Type'     => 'application/json'
        ]);
    }


    /**
     *  2023.10.01: Alejandro Sagula
     *  Save export filename for each work exported.
     */
    private function saveExportFilename($work_id, $export_filename, $dt){
        $work = WorkRegistration::find($work_id);
        $work->export_filename = $export_filename;
        $work->export_datetime = $dt;
        $work->save();
    }
   

    /**
     * 2023.10.02: Alejandro Sagula
     * Reexport file
     * 
     */
    public function re_export( Request $request){
        
        $works = WorkRegistration::where('id',$request->id)->with('titles')->get();
        $works_data = $works->map(function(WorkRegistration $work) {
            $interestedParties = $work->distribution->map(function(Distribution $dist) {
                $porcentPer = str_pad($dist->public * 100, 5, '0', STR_PAD_LEFT);
                $porcentMec = str_pad(strval($dist->mechanic * 100), 5, '0', STR_PAD_LEFT);
                $porcentSyn = str_pad(strval($dist->sync * 100), 5, '0', STR_PAD_LEFT);

                return [
                    'nameNumber' => $dist->type == 'member' ? $dist->member->ipname : 99999999999,
                    'name'       => $dist->type == 'member' ? ucwords(strtolower(optional($dist->member)->nombre)) : $dist->meta->name,
                    'role'       => $dist->fn,
                    'porcentPer' => (string) $porcentPer,
                    'porcentMec' => (string) $porcentMec,
                    'porcentSyn' => (string) $porcentSyn
                ];
            });

            $sheetMusicFile = new \stdClass();
            $audioFile = new \stdClass();

            $work->files->map(function($file) use ($sheetMusicFile) {
                if ($file->name == 'lyric_file') {
                    $fileName = explode('/', $file->path);
                    $sheetMusicFile->fileName = $fileName[count($fileName) - 1];
                    $sheetMusicFile->filePath = $this->addUrlFile($file->path);
                }
            });

            $work->files->map(function($file) use ($audioFile) {
                if ($file->name == 'audio_file') {
                    $fileName = explode('/', $file->path);
                    $audioFile->fileName = $fileName[count($fileName) - 1];
                    $audioFile->filePath = $this->addUrlFile($file->path);
                }
            });

            /**
             *  Add this lines
             */
            $unpublishedDate = ($work->dnda_in_date) ? (new DateTime($work->dnda_in_date))->format('Y-m-d') : null;
            $editedDate = ($work->dnda_ed_date) ? (new DateTime($work->dnda_ed_date))->format('Y-m-d') : null;
            
        

            $data = [
                'submissionId'      => $work->id,
                'agency'            => '128',
                'originalTitle'     => $work->title,
                'otherTitles'       =>  $work->titles->map(function($t){
                    return $t->title;
                }),
                'albumTitle'        => $work->dnda_title,
                'genre'             => $work->genre_id,
                'duration'          => $work->duration,
                'jingle'            => $work->is_jingle == 1 ? 'S' : 'N',
                'musicMovies'       => $work->is_movie == 1 ? 'S' : 'N',
                'unpublishedDndaNumberLetter' => intval($work->lyric_dnda_in_file),
                'unpublishedDndaNumberMusic' => intval($work->audio_dnda_in_file),
                'unpublishedDate' => $unpublishedDate , #$work->dnda_in_date,
                'editedDndaNumberLetter' => intval($work->lyric_dnda_ed_file),
                'editedDndaNumberMusic' => intval($work->audio_dnda_ed_file),
                'editedDate' =>  $editedDate, # $work->dnda_ed_date,
                'interestedParties' => $interestedParties,
                'sheetMusicFile' => $sheetMusicFile,
                'audioFile' => $audioFile
            ];

            return $data;
        });

        $dt = $works->first()['export_datetime'];
        

        $fileContents = [
            '$schema'    => './work_schema.json',
            'fileHeader' => [
                'submittingAgency'     => '128',
                'fileCreationDateTime' => $dt,
                'receivingAgency'      => '061'
            ],
            'addWorks' => $works_data
        ];

        $fileName = $works->first()['export_filename'];

        
        return response()->streamDownload(function() use ($fileContents) {
            $output = json_encode($fileContents, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

            // Transformar los strings que representan números con leading zeros a
            // "números" con leading zeros. Se hace de forma manual porque el estandar
            // de JSON no soporta leading zeros para los números
            $patterns = [
                '/("nameNumber"): "(\d*)"/m',
                '/("porcentPer"): "(\d*)"/m',
                '/("porcentMec"): "(\d*)"/m',
                '/("porcentSyn"): "(\d*)"/m',
            ];

            $output = preg_replace($patterns, '${1}: ${2}', $output);

            echo $output;
        }, $fileName, [
            'Content-Encoding' => 'utf-8',
            'Content-Type'     => 'application/json'
        ]);
    }


    public function importWorks(Request $request)
    {
        if (!$request->hasFile('file')) {
            abort(400);
        }

        $contents = $request->file('file')->get();
        $contents = json_decode($contents);

       

        /***
         *  Author: Alejandro Sagula
         *  Date: 15/08/2023
         *  Desc: Someone ask to change the value 061 instead of 128, I don't know why.
         */

        //  if ($contents->fileHeader->receivingAgency != '128') {
        //     abort(400);
        //     }

        if ($contents->fileHeader->receivingAgency != '061') {
            abort(400);
        }

        $events = [];
        $stats = [
            'success' => 0,
            'failure' => 0
        ];

        foreach($contents->acknowledgements as $ack) {
            // Si no es alta, omitimos el registro
            if ($ack->originalTransactionType != 'addWork') {
                $events[] = "Respuesta $ack->submissionId omitida porque no es un alta";
                $stats['failure']++;
                continue;
            }

            $work = WorkRegistration::find($ack->originalSubmissionId);

            // Si no encontramos la solicitud en la BBDD, omitimos el registro
            if (!$work) {
                $events[] = "Respuesta $ack->submissionId omitida porque no se encontro solicitud(id $ack->originalSubmissionId) en la BBDD";
                $stats['failure']++;
                continue;
            }

            // Si la solicitud no está a la espera de respuesta, omitimos el registro
            if ($work->status_id != 6) { // Para pasar a PI
                $events[] = "Respuesta $ack->submissionId omitida porque la solicitud(id $ack->originalSubmissionId) no está a la espera de respuesta";
                $stats['failure']++;
                continue;
            }

            if ($ack->transactionsStatus == 'FullyAccepted') {
                $work->status_id = 8; // Aprobado
                $work->approved = true;
                $work->codwork = $ack->codworkSq;
                $stats['success']++;
            } else if ($ack->transactionStatus == 'Rejected') {
                $work->status_id = 9; // Rechazado
                $work->approved = true;
                $stats['success']++;
            } else {
                $events[] = "Respuesta $ack->submissionId omitida porque no está soportado el tipo";
                $stats['failure']++;
                continue;
            }

            $work->save();
        }

        return [
            'status' => 'success',
            'events' => $events,
            'stats'  => $stats
        ];
    }

    public function exportJingles()
    {
        $jingles = JingleRegistration::where('status_id', 6)->with('agreements')->get();

        // Preparamos los datos y actualizamos el estado de la solicitud
        $jingles_data = $jingles->map(function(JingleRegistration $jingle) {
            $data = [
                'id'             => $jingle->id,
                'tipo_solicitud' => $jingle->is_especial ? 'especial' : 'regular',
                'tipo_accion'    => $jingle->request_action,
                'vigencia'       => $jingle->validity,
                'fecha_salida'   => $jingle->air_date->format('Y-m-d'),
                'territorio'     => $jingle->broadcast_territory,
            ];

            $data['solicitante'] = [
                'cuit'         => $jingle->applicant->cuit,
                'razon_social' => $jingle->applicant->name,
                'direccion'    => trim($jingle->applicant->address ?? ''),
                'telefono'     => trim($jingle->applicant->phone ?? ''),
                'correo'       => trim($jingle->applicant->cuit ?? ''),
            ];

            $data['anunciante'] = [
                'cuit'         => $jingle->advertiser->cuit,
                'razon_social' => $jingle->advertiser->name,
                'direccion'    => trim($jingle->advertiser->address ?? ''),
                'telefono'     => trim($jingle->advertiser->phone ?? ''),
                'correo'       => trim($jingle->advertiser->cuit ?? ''),
            ];

            if ($jingle->is_especial) {
                $data['cantidad_avisos'] = count($jingle->ads_duration);
                $data['duracion_avisos'] = $jingle->ads_duration;
            } else {
                $data['duracion_aviso'] = $jingle->ads_duration[0];
            }

            if ($jingle->broadcast_territory_id == 2) { // Provincial
                $data['provincias'] = $jingle->territories->map(function($t) {
                    return $t->state;
                });
            } else if ($jingle->broadcast_territory_id == 3) { // Internacional
                $data['paises'] = $jingle->territories->map(function($t) {
                    return $t->tis_n;
                });

                $data['difusion_nacional'] = $jingle->also_national;
            }

            $data['medios_de_comunicacion'] = [
                'tipo_1' => $jingle->media->name,
                'tipo_2' => $jingle->media->description,
            ];

            $data['agencia'] = [
                'tipo'         => $jingle->agency_type,
                'cuit'         => $jingle->agency->cuit,
                'razon_social' => $jingle->agency->name,
                'direccion'    => trim($jingle->agency->address ?? ''),
                'telefono'     => trim($jingle->agency->phone ?? ''),
                'correo'       => trim($jingle->agency->cuit ?? ''),
            ];

            $data['producto'] = [
                'marca'  => $jingle->product_brand,
                'tipo'   => $jingle->product_type,
                'nombre' => $jingle->product_name
            ];

            $data['obra'] = [
                'titulo'            => $jingle->work_title,
                'original'          => $jingle->work_original ? 'Si' : 'No',
                'dnda'              => $jingle->work_dnda ?? '',
                'autores'           => $jingle->work_authors ?? '',
                'compositores'      => $jingle->work_composers ?? '',
                'editores'          => $jingle->work_editors ?? '',
                'letra_modificada'  => $jingle->work_script_mod ? 'Si' : 'No',
                'musica_modificada' => $jingle->work_music_mod ? 'Si' : 'No'
            ];

            $data['conformidad_autores'] = $jingle->authors_agreement ? 'Si' : 'No';

            if ($jingle->authors_agreement) {
                $data['autores'] = [];

                foreach($jingle->agreements as $person) {
                    $autor = [];

                    if ($person->type_id == 1) { // Socios
                        $autor['nombre'] = optional($person->member)->nombre;
                        $autor['nro_socio'] = optional($person->member)->codanita;
                        $autor['nro_doc'] = $person->doc_number;
                        $autor['correo'] = optional($person->member)->email;
                    } else { // No socios
                        $autor['nombre'] = $person->meta->name;
                        $autor['nro_doc'] = $person->doc_number;
                        $autor['correo'] = $person->meta->email;
                        $autor['pais'] = $person->meta->country;
                        $autor['nacionalidad'] = $person->meta->birth_country->name_ter;
                        $autor['provincia'] = $person->meta->state;
                        $autor['localidad'] = $person->meta->city;
                        $autor['codigo_postal'] = $person->meta->address_zip ?? '';
                        $autor['calle'] = $person->meta->street_name;
                        $autor['numero'] = $person->meta->street_number;
                        $autor['piso'] = $person->meta->floor ?? '';
                        $autor['departamento'] = $person->meta->apartment ?? '';
                        $autor['fecha_nacimiento'] = $person->meta->birth_date->format('Y-m-d');
                        $autor['tel_pais'] = $person->meta->phone_country;
                        $autor['tel_area'] = $person->meta->phone_area;
                        $autor['tel_num'] = $person->meta->phone_number;
                    }

                    array_push($data['autores'], $autor);
                }
            }

            $data['arancel_monto'] = $jingle->authors_tariff;
            $data['arancel_responsable'] = $jingle->tariff_payer;

            if ($jingle->tariff_payer_id == 3) {
                $data['arancel_a_cuenta'] = $jingle->tariff_representation;
            }

            return $data;
        });

        $date = new DateTime('now');

        // Preparamos el resto del contenido del archivo
        $fileContents = $jingles_data;

        // Calculamos el nombre del archivo
        $fileName = 'work-';
        $fileName .= $date->format('Y\-m\-d\TH\-i\-s');
        $fileName .= '-inclusiones.json';

        return response()->streamDownload(function() use ($fileContents) {
            echo json_encode($fileContents, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        }, $fileName, [
            'Content-Encoding' => 'utf-8',
            'Content-Type'     => 'application/json'
        ]);
    }

    public function exportMembers()
    {
        $members = MemberRegistration::where('status_id', 3)->get();

        // Preparamos los datos y actualizamos el estado de la solicitud
        $members_data = $members->map(function(MemberRegistration $member) {
            $data['id'] = $member->id;
            $data['nombre'] = $member->name;
            $data['fecha_nacimiento'] = $member->birth_date->format('Y-m-d');
            $data['pais_nacimiento'] = $member->birth_country;
            $data['provincia_nacimiento'] = $member->birth_state;
            $data['localidad_nacimiento'] = $member->birth_city;
            $data['nro_doc'] = $member->doc_number;
            $data['cuit'] = $member->work_code;

            $data['calle'] = $member->address_street;
            $data['numero'] = $member->address_number;
            $data['piso'] = $member->address_floor ?? '';
            $data['departamento'] = $member->address_apt ?? '';
            $data['pais'] = $member->address_country;
            $data['provincia'] = $member->address_state;
            $data['localidad'] = $member->address_city;
            $data['codigo_postal'] = $member->address_zip ?? '';
            $data['telefono'] = $member->landline;
            $data['celular'] = $member->mobile;
            $data['correo'] = $member->email;

            $data['pseudonimo'] = $member->pseudonym;
            $data['banda'] = $member->band;
            $data['obra'] = $member->entrance_work;
            $data['genero'] = $member->genre_id	;

            return $data;
        });

        $date = new DateTime('now');

        // Parseamos el contenido del archivo
        $fileContents = $members_data;

        // Calculamos el nombre del archivo
        $fileName = 'socios-';
        $fileName .= $date->format('Y\-m\-d\TH\-i\-s');
        $fileName .= '-registros.json';

        return response()->streamDownload(function() use ($fileContents) {
            echo json_encode($fileContents, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        }, $fileName, [
            'Content-Encoding' => 'utf-8',
            'Content-Type'     => 'application/json'
        ]);
    }
}
