<?php

namespace App\ContohBootcamp\Services;

use App\ContohBootcamp\Repositories\TaskRepository;
use Exception;

class TaskService
{
	private TaskRepository $taskRepository;

	public function __construct()
	{
		$this->taskRepository = new TaskRepository();
	}

	/**
	 * NOTE: untuk mengambil semua tasks di collection task
	 */
	public function getTasks()
	{
		$tasks = $this->taskRepository->getAll();
		return $tasks;
	}

	/**
	 * NOTE: menambahkan task
	 */
	public function addTask(array $data)
	{
		$taskId = $this->taskRepository->create($data);
		return $taskId;
	}

	/**
	 * NOTE: UNTUK mengambil data task
	 */
	public function getById(string $taskId)
	{
		$task = $this->taskRepository->getById($taskId);
		return $task;
	}

	/**
	 * NOTE: untuk update task
	 */
	public function updateTask(array $editTask, array $formData)
	{
		if (isset($formData['title'])) {
			$editTask['title'] = $formData['title'];
		}

		if (isset($formData['description'])) {
			$editTask['description'] = $formData['description'];
		}

		$id = $this->taskRepository->save($editTask);
		return $id;
	}

	public function destroyTask(string $id)
	{
		try {
			$existTask = $this->getById($id);
		} catch (Exception $e) {
			throw $e;
		}

		$this->taskRepository->delete($id);
	}

	public function assignTask(string $id, string $assigned)
	{
		try {
			$existTask = $this->getById($id);
		} catch (Exception $e) {
			throw $e;
		}

		$existTask['assigned'] = $assigned;

		$this->taskRepository->save($existTask);

		return $this->getById($id);
	}

	public function unassignTask(string $id)
	{
		try {
			$existTask = $this->getById($id);
		} catch (Exception $e) {
			throw $e;
		}

		$existTask['assigned'] = null;
		$this->taskRepository->save($existTask);

		return $this->getById($id);
	}

	public function addSubtask(string $taskId, array $subtask)
	{
		try {
			$task = $this->getById($taskId);
		} catch (Exception $e) {
			throw $e;
		}

		$subtask['_id'] = $subtask['_id'] ?? $this->taskRepository->generateOid();
		$task['subtasks'][] = $subtask;
		$this->taskRepository->save($task);

		return $this->getById($taskId);
	}

	public function deleteSubtask(string $taskId, string $subtaskId)
	{
		try {
			$task = $this->getById($taskId);
		} catch (Exception $e) {
			throw $e;
		}

		$subtasks = collect($task['subtasks'] ?? []);

		$filteredSubtasks = $subtasks->reject(fn ($value) => $value['_id'] === $subtaskId);

		$task['subtasks'] = [...$filteredSubtasks];

		$this->taskRepository->save($task);

		return $this->getById($taskId);
	}
}
