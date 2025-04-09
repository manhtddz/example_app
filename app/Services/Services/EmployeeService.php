<?php

namespace App\Services\Services;

use App\Http\Requests\EmployeeCreateRequest;
use App\Http\Requests\EmployeeUpdateRequest;
use App\Jobs\SendEmployeeEmailJob;
use App\Models\Employee;
use App\Services\Interfaces\IEmployeeRepository;
use App\Services\Interfaces\ITaskRepository;
use App\Services\Repository\EmployeeRepository;
use App\Services\Repository\TaskRepository;
use Exception;
use Illuminate\Support\Facades\Storage as Storage;
use Response;

class EmployeeService
{
    private EmployeeRepository $employeeRepository;
    private TaskRepository $taskRepository;
    private FileService $fileService;

    public function __construct(IEmployeeRepository $employeeRepository, ITaskRepository $taskRepository)
    {
        $this->employeeRepository = $employeeRepository;
        $this->taskRepository = $taskRepository;
        $this->fileService = FileService::getInstance();
    }
    public function findAll()
    {
        return $this->employeeRepository->findAll();
    }

    public function findAllPaging()
    {
        return $this->employeeRepository->findAllPaging(ITEM_PER_PAGE);
    }
    public function findAllEmployeeId()
    {
        return $this->employeeRepository->findAllEmployeeId();
    }

    public function findById($id)
    {
        if (!is_numeric($id)) {
            throw new Exception(WRONG_FORMAT_ID);
        }
        $employee = $this->employeeRepository->findById($id);
        if (!$employee) {
            throw new Exception(NOT_EXIST_ERROR);
        }
        return $employee;
    }
    public function findNotActiveEmployeeByEmail($email)
    {
        return $this->employeeRepository->findNotActiveEmployeeByEmail($email);
    }
    public function findActiveEmployeeByEmail($email)
    {
        return $this->employeeRepository->findActiveEmployeeByEmail($email);
    }
    public function search(array $request, $sort = null, $direction = "asc")
    {
        $filtered = array_filter(
            $request,
            fn($value) => $value !== "" && $value !== null && $value != 0
        );

        $employees = $this->findAllPaging();

        if (!empty($filtered)) { // Call service when search data is not empty
            $employees = $this->employeeRepository
                ->searchPaging(ITEM_PER_PAGE, $filtered, $sort, $direction);
        }

        return $employees;
    }
    public function findAllSearchedId(array $request, $sort, $direction)
    {
        $filtered = array_filter(
            $request,
            fn($value) => $value !== "" && $value !== null && $value != 0
        );

        $employeeIds = $this->findAllEmployeeId();

        if (!empty($request)) { // Call service when search data is not empty
            $employeeIds = $this->employeeRepository
                ->findAllSearchedId($filtered, $sort, $direction);
        }

        return $employeeIds;
    }
    public function searchDetailsWithEmployee($employeeId, $request, $sort, $direction)
    {
        $filtered = array_filter(
            $request,
            fn($value) => $value !== "" && $value !== null && $value != 0
        );
        $data = $this->taskRepository->findAllWithEmployeePaging(ITEM_PER_PAGE, $employeeId);
        if (!empty($filtered)) { // Call service when search data is not empty
            $data = $this->taskRepository->searchWithEmployee(
                ITEM_PER_PAGE,
                $employeeId,
                $filtered,
                $sort,
                $direction
            );
        }

        return $data;
    }

    public function findAllWithProject($projectId)
    {
        return $this->employeeRepository->findAllIdWithProject($projectId);
    }
    public function searchWithTeamPaging($teamId, $request, $sort, $direction)
    {
        $filtered = array_filter(
            $request,
            fn($value) => $value !== "" && $value !== null && $value != 0
        );
        $employees = $this->employeeRepository->findAllWithTeamPaging(ITEM_PER_PAGE, $teamId);
        if (!empty($filtered)) { // Call service when search employees is not empty
            $employees = $this->employeeRepository->searchWithTeam(
                ITEM_PER_PAGE,
                $teamId,
                $filtered,
                $sort,
                $direction
            );
        }
        return $employees;
    }
    public function findAllWithTask($taskId)
    {
        return $this->employeeRepository->findAllIdWithTask($taskId);
    }
    public function searchWithProjectPaging($projectId, $request, $sort, $direction)
    {
        $filtered = array_filter(
            $request,
            fn($value) => $value !== "" && $value !== null && $value != 0
        );
        $employees = $this->employeeRepository->findAllWithProjectPaging(ITEM_PER_PAGE, $projectId);
        if (!empty($filtered)) { // Call service when search employees is not empty
            $employees = $this->employeeRepository->searchWithProject(
                ITEM_PER_PAGE,
                $projectId,
                $filtered,
                $sort,
                $direction
            );
        }
        return $employees;
    }

    public function create(array $request)
    {
        $this->fileService->moveTempFileToApp($request['avatar'] ?? "");

        $result = $this->employeeRepository->create($request);

        if (empty($result)) {
            throw new Exception(CREATE_FAILED);
        }

        $emailGetter['email'] = $request['email'];
        $emailGetter['first_name'] = $request['first_name'];
        $emailGetter['last_name'] = $request['last_name'];
        SendEmployeeEmailJob::dispatch($emailGetter)->delay(now()->addSeconds(5));

        return $result;
    }
    public function update(int $id, array $request)
    {
        $employee = $this->employeeRepository->findById($id);
        if (!$employee) {
            throw new Exception(NOT_EXIST_ERROR);
        }
        $result = $this->employeeRepository->update($id, $request);
        if (!$result) {
            throw new Exception(UPDATE_FAILED);
        }
        if ($request['avatar'] !== $request['old_avatar']) {
            $this->fileService->removeFile('app/' . $request['old_avatar']);
            $this->fileService->moveTempFileToApp($request['avatar']);
        }

        $emailGetter['email'] = $request['email'];
        $emailGetter['first_name'] = $request['first_name'];
        $emailGetter['last_name'] = $request['last_name'];
        SendEmployeeEmailJob::dispatch($emailGetter)->delay(now()->addSeconds(5));

        return $result;
    }
    public function delete(int $id)
    {
        $employee = $this->employeeRepository->findById($id);
        if (!$employee) {
            throw new Exception(NOT_EXIST_ERROR);
        }
        $result = $this->employeeRepository->delete($id);
        if (!$result) {
            throw new Exception(DELETE_FAILED);
        }

        return $result;
    }
    public function prepareConfirmForUpdate($id, EmployeeUpdateRequest $request)
    {
        $validatedData = $request->validated();
        if ($request->hasFile('avatar')) {
            session()->forget('temp_file');
            $filePath = $this->fileService->uploadTempFileAndDeleteTempFile(
                $request->file('avatar'),
                $request->uploaded_avatar
            );
            $validatedData['avatar'] = $filePath;
        } else {
            $validatedData['avatar'] = $request->uploaded_avatar;
        }
        $validatedData['old_avatar'] = $request->old_avatar;
        $validatedData['id'] = $id;

        session()->flash('employee_data', $validatedData);
    }

    public function prepareConfirmForCreate(EmployeeCreateRequest $request)
    {
        $validatedData = $request->validated();
        if ($request->hasFile('avatar')) {
            session()->forget('temp_file');
            $filePath = $this->fileService
                ->uploadTempFileAndDeleteTempFile(
                    $request->file('avatar'),
                    $request->old_avatar
                );
            $validatedData['avatar'] = $filePath;
        } else {
            $validatedData['avatar'] = $request->old_avatar;
        }

        session()->flash('employee_data', $validatedData);
    }

    public function exportToCSV(array $ids)
    {
        ob_start();

        $headers = [
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="data.csv"',
            'Expires' => '0',
            'Pragma' => 'public',
        ];

        $fileName = 'employees_' . time() . '.csv';
        $filePath = 'temp/' . $fileName; // Store in `storage/app/public/temp/`

        // Create a white file in public disk
        Storage::disk('public')->put($filePath, '');

        // Take absolute path of white file
        $absolutePath = Storage::disk('public')->path($filePath);

        $handle = fopen($absolutePath, "w");
        fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($handle, ['ID', 'Team', 'Name', 'Email']);

        $emps = Employee::whereIn('id', $ids)->get(); // Filter by ID

        foreach ($emps as $emp) {
            fputcsv($handle, array_map(function ($value) {
                return mb_convert_encoding(strip_tags($value), 'UTF-8', 'auto');
            }, [
                $emp->id,
                $emp->team->name,
                $emp->name,
                $emp->email
            ]));
        }

        fclose($handle);
        ob_end_clean();

        return Response::download($absolutePath, $fileName, $headers)
            ->deleteFileAfterSend(true)
            ->send();
    }


}