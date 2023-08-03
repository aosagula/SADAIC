<?php

namespace App\Http\Controllers\Member;

use \DateTime;
use App\Http\Controllers\Controller;
use App\Models\MemberProfile;
use App\Models\MemberProfileUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:members');
    }

    public function showList()
    {
        $member = Auth::user();

        $requests = MemberProfileUpdate::where('member_id', $member->member_id)
        ->where('heir', $member->heir)
        ->orderBy('id', 'desc')
        ->get();

        $disableNew = MemberProfileUpdate::where('member_id', $member->member_id)
        ->where('heir', $member->heir)
        ->where('updated_at', null)
        ->count() > 0;

        return view('member.profile.list', [
            'requests'   => $requests,
            'disableNew' => $disableNew
        ]);
    }

    public function showUpdate()
    {
        $member = Auth::user();
        $profile = MemberProfile::where(['member_id' => $member->id])->first();

        return view('member.profile.update', [
            'member'  => $member,
            'profile' => $profile
        ]);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'email'           => 'required|email',
            'name'            => 'required|string|between:2,255',
            'address_type'    => 'required|string|between:2,255',
            'address'         => 'required|string|between:2,500',
            'address_zip'     => 'required|string|between:2,50',
            'address_city'    => 'required|string|between:2,200',
            'address_state'   => 'required|string|between:2,150',
            'address_country' => 'required|string|between:2,250',
            'phone_country'   => 'integer|between:1,999',
            'phone_area'      => 'integer|min:0',
            'phone_number'    => 'integer|min:0',
            'cell_country'    => 'integer|between:1,999',
            'cell_area'       => 'integer|min:0',
            'cell_number'     => 'integer|min:0',
        ], [], [
            'email'               => 'correo electrónico',
            'name'                => 'nombre',
            'address'             => 'dirección',
            'address_zip'         => 'código postal',
            'address_city'        => 'localidad',
            'address_state'       => 'provincia',
            'address_country'     => 'país',
            'phone_country'       => 'código de país del teléfono',
            'phone_area'          => 'código de área del teléfono',
            'phone_number'        => 'número de teléfono',
            'cell_country'        => 'código de país del celular',
            'cell_area'           => 'código de área del celular',
            'cell_number'         => 'número de celular'
        ]);

        $params = $request->only([
            'email',
            'name',
            'address_type',
            'address',
            'address_zip',
            'address_city',
            'address_state',
            'address_country',
            'phone_country',
            'phone_area',
            'phone_number',
            'cell_country',
            'cell_area',
            'cell_number',
        ]);

        $member = Auth::user();
        $params['member_id'] = $member->member_id;
        $params['heir'] = $member->heir;
        $params['created_at'] = new DateTime();
        $params['status_id'] = 1;

        try {
            DB::table('profile_updates')->insert($params);

            $message_type = 'success';
            $message_data = 'Su solicitud de actualización de datos se registró correctamente';
        } catch (Throwable $t) {
            $message_type = 'danger';
            $message_data = 'Ocurrió un error al registrar su solicitud, por favor, intente nuevamente más tarde';

            Log::error("Error realizando actualización de datos",
                [
                    "error" => json_encode($t, JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_IGNORE ),
                    "data"  => json_encode($request->all(), JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_IGNORE )
                ]
            );
        }

        return redirect()->action('Member\ProfileController@showList')->with([
            'message.type' => $message_type,
            'message.data' => $message_data
        ]);
    }

    public function viewUpdate(MemberProfileUpdate $update)
    {
        return view('member.profile.view', [
            'update' => $update
        ]);
    }
}