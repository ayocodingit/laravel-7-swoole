<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Enums\StatusAntigenEnum;
use App\Exports\SwabAntigenExport;
use App\Http\Requests\BulkValidasiSwabAntigenRequest;
use App\Http\Requests\ImportExcelRequest;
use App\Http\Requests\StoreSwabAntigenRequest;
use App\Http\Resources\SwabAntigenDetailResource;
use App\Http\Resources\SwabAntigenResource;
use App\Imports\SwabAntigenImport;
use App\Models\SwabAntigen;
use App\Traits\ExportMappingTrait;
use App\Traits\PaginateTrait;
use App\Traits\SwabAntigenTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class SwabAntigenController extends Controller
{
    use SwabAntigenTrait;
    use ExportMappingTrait;
    use PaginateTrait;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('can:isAdminSwabAntigen');
    }

    public function index(Request $request)
    {
        $params          = $this->getValidParams($request);
        $order_direction = $this->getValidOrderDirection($request->input('order_direction'));
        $perpage         = $this->getValidPerpage($request->input('perpage'));
        $order           = $request->input('order', 'tanggal_periksa');

        $record = SwabAntigen::query();
        if ($params) {
            $record->filter($params);
        }
        $record->order($order, $order_direction);
        return SwabAntigenResource::collection($record->paginate($perpage));
    }

    public function show(SwabAntigen $swab_antigen)
    {
        return new SwabAntigenDetailResource($swab_antigen);
    }

    public function update(StoreSwabAntigenRequest $request, SwabAntigen $swab_antigen)
    {
        DB::beginTransaction();
        try {
            $origin = clone $swab_antigen;
            $swab_antigen->fill($request->validated())->save();
            $changes = $swab_antigen->getChanges();
            if ($changes) {
                $swab_antigen->swabAntigenLogs()->create([
                    "user_id" => auth()->user()->id,
                    "info" => json_encode($this->swabAntigenLogs($origin, $changes))
                ]);
            }
            DB::commit();
            return response()->json(['message' => 'Ubah data berhasil']);
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }

    public function store(StoreSwabAntigenRequest $request)
    {
        $swab_antigen = SwabAntigen::create($request->validated() + [
            'nomor_registrasi' => $this->generateNomorRegister()
        ]);
        return new SwabAntigenResource($swab_antigen);
    }

    public function destroy(SwabAntigen $swab_antigen)
    {
        abort_if(
            $swab_antigen->is_allow_delete,
            Response::HTTP_UNPROCESSABLE_ENTITY,
            __('validation.swab_antigen_action')
        );
        $swab_antigen->delete();
        return response()->json(['message' => 'DELETED']);
    }

    public function getNomorRegistrasi()
    {
        return response()->json([
            'nomor_registrasi' => $this->generateNomorRegister()
        ]);
    }

    public function bulkValidasi(BulkValidasiSwabAntigenRequest $request)
    {
        SwabAntigen::whereIn('id', $request->input('id'))->update([
            'validator_id' => $request->input('validator_id'),
            'status' => StatusAntigenEnum::tervalidasi()->getValue(),
            'tanggal_validasi' => Carbon::now(),
        ]);
        return response()->json([
            'message' => 'success'
        ]);
    }

    public function import(ImportExcelRequest $request)
    {
        $import = new SwabAntigenImport();
        DB::beginTransaction();
        try {
            $import->import($request->file('register_file'));
            DB::commit();
            return response()->json(['message' => 'Sukses import data.']);
        } catch (ValidationException $e) {
            DB::rollback();
            $errors = [];
            foreach ($e->failures() as $key => $failure) {
                $errors[$key]['row'] = $failure->row();
                $errors[$key]['attribute'] = $failure->attribute();
                $errors[$key]['errors'] = $failure->errors();
            }
            return response()->json(['errors' => $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function export(Request $request)
    {
        $models = $this->index($request);
        $nameFile = 'Swab-Antigen-' . time() . '.xlsx';
        return Excel::download(new SwabAntigenExport($models, $this->getNomorUrut($request)), $nameFile);
    }
}
