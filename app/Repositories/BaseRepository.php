<?php

namespace App\Repositories;

use App\Traits\FilterDataTrait;

abstract class BaseRepository implements RepositoryInterface {

	use FilterDataTrait;

	protected $model;

	public function __construct()
	{
		$this->model = $this->setModel();
	}

	public function setModel() {
		return app()->make($this->getModel());
	}

	abstract public function getModel();

	public function all()
	{
		return $this->model->all();
	}

	public function find($id)
	{
		return $this->model->find($id);
	}

	public function create(array $data)
	{
		return $this->model->create($data);
	}

	public function update($id, array $data)
	{
		$record = $this->model->find($id);
		return $record->update($data);
	}

	public function delete($id) {
		$record = $this->model->find($id);

		if (!$record) {
			return false;
		}

		return $record->delete();
	}
}