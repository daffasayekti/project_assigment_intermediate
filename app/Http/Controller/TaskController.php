<?php

namespace App\Http\Controller;

use App\ContohBootcamp\Services\TaskService;
use App\Helpers\MongoModel;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class TaskController extends Controller
{
	private TaskService $taskService;
	public function __construct()
	{
		$this->taskService = new TaskService();
	}

	public function showTasks()
	{
		$tasks = $this->taskService->getTasks();
		return response()->json($tasks);
	}

	public function createTask(Request $request)
	{
		$request->validate([
			'title' => 'required|string|min:3',
			'description' => 'required|string'
		]);

		$data = [
			'title' => $request->post('title'),
			'description' => $request->post('description')
		];

		$dataSaved = [
			'title' => $data['title'],
			'description' => $data['description'],
			'assigned' => null,
			'subtasks' => [],
			'created_at' => time()
		];

		try {
			$id = $this->taskService->addTask($dataSaved);
			$task = $this->taskService->getById($id);
		} catch (Exception $e) {
			return response()->json([
				'message' => $e->getMessage()
			], $e->getCode());
		}

		return response()->json($task);
	}


	public function updateTask(Request $request)
	{
		$request->validate([
			'task_id' => 'required|string',
			'title' => 'string',
			'description' => 'string',
			'assigned' => 'string',
			'subtasks' => 'array',
		]);

		$taskId = $request->post('task_id');
		$formData = $request->only('title', 'description', 'assigned', 'subtasks');

		try {
			$task = $this->taskService->getById($taskId);
			$this->taskService->updateTask($task, $formData);
			$task = $this->taskService->getById($taskId);
		} catch (Exception $e) {
			return response()->json([
				'message' => $e->getMessage()
			], $e->getCode());
		}

		return response()->json($task);
	}


	// TODO: deleteTask()
	public function deleteTask(Request $request)
	{
		$validated = $request->validate([
			'task_id' => 'required'
		]);

		try {
			$this->taskService->destroyTask($validated['task_id']);
		} catch (Exception $e) {
			return response()->json([
				'message' => $e->getMessage()
			], $e->getCode());
		}

		return response()->json([
			'message' => 'Success delete task ' . $validated['task_id']
		]);
	}

	// TODO: assignTask()
	public function assignTask(Request $request)
	{
		$validated = $request->validate([
			'task_id' => 'required',
			'assigned' => 'required'
		]);

		try {
			$task = $this->taskService->assignTask($validated['task_id'], $validated['assigned']);
		} catch (Exception $e) {
			return response()->json([
				"message" => $e->getMessage()
			], $e->getCode());
		}
		return response()->json($task);
	}

	// TODO: unassignTask()
	public function unassignTask(Request $request)
	{
		$validated = $request->validate([
			'task_id' => 'required'
		]);

		try {
			$task = $this->taskService->unassignTask($validated['task_id']);
		} catch (Exception $e) {
			return response()->json([
				"message" => $e->getMessage()
			], $e->getCode());
		}

		return response()->json($task);
	}

	// TODO: createSubtask()
	public function createSubtask(Request $request)
	{
		$request->validate([
			'task_id' => 'required',
			'title' => 'required|string',
			'description' => 'required|string'
		]);

		$taskId = $request->post('task_id');
		$subtask = $request->only(['title', 'description']);

		try {
			$task = $this->taskService->addSubtask($taskId, $subtask);
		} catch (Exception $e) {
			return response()->json([
				'message' => $e->getMessage()
			], $e->getCode());
		}

		return response()->json($task);
	}

	// TODO deleteSubTask()
	public function deleteSubtask(Request $request)
	{
		$request->validate([
			'task_id' => 'required',
			'subtask_id' => 'required'
		]);

		$taskId = $request->post('task_id');
		$subtaskId = $request->post('subtask_id');

		try {
			$task = $this->taskService->deleteSubtask($taskId, $subtaskId);
		} catch (Exception $e) {
			return response()->json([
				'message' => $e->getMessage()
			], $e->getCode());
		}

		return response()->json($task);
	}
}
