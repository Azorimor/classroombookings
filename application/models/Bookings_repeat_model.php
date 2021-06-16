<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Bookings_repeat_model extends CI_Model
{


	// Table for this model
	protected $table = 'bookings_repeat';

	// Other objects to get/include with returned value
	private $include = [];


	public function __construct()
	{
		$this->load->model('bookings_model');
		$this->load->helper('array');
	}


	/**
	 * Get Repeat by ID
	 *
	 */
	public function get($repeat_id)
	{
		$where = [ 'repeat_id' => $repeat_id ];

		$query = $this->db->get_where($this->table, $where, 1);

		if ($query->num_rows() === 1) {
			return $this->wake_value($query->row());
		}

		return FALSE;
	}



	public function create($data)
	{
		$dates = isset($data['dates']) ? $data['dates'] : [];
		if (empty($dates)) return FALSE;

		unset($data['dates']);

		$data = $this->sleep_values($data);

		$data['created_at'] = date('Y-m-d H:i:s');
		$data['created_by'] = $this->userauth->user->user_id;

		$ins = $this->db->insert($this->table, $data);
		if ( ! $ins) return FALSE;

		$repeat_id = $this->db->insert_id();

		$booking_data = [
			'repeat_id' => $repeat_id,
			'session_id' => element('session_id', $data, NULL),
			'period_id' => element('period_id', $data, NULL),
			'room_id' => element('room_id', $data, NULL),
			'user_id' => element('user_id', $data, NULL),
			'department_id' => element('department_id', $data, NULL),
			'date' => NULL,
			'status' => Bookings_model::STATUS_BOOKED,
			'notes' => element('notes', $data, NULL),
		];

		foreach ($dates as $date => $info) {

			$action = $info['action'];

			$replace_booking_id = isset($info['replace_booking_id']) ? $info['replace_booking_id'] : NULL;

			if ($action == 'do_not_book') continue;

			if ($action == 'replace') {
				// Cancel existing booking
				$this->bookings_model->cancel_single($replace_booking_id);
			}

			// Create booking
			$insert_data = array_merge($booking_data, ['date' => $date]);
			$this->bookings_model->create($insert_data);
		}

		return $repeat_id;
	}


	public function create_instances($repeat_id, $dates)
	{
		foreach ($dates as $date => $data) {

		}
	}


	public function wake_value($row)
	{
		foreach ($this->include as $include) {

			switch ($include) {

				case 'user':
					$this->load->model('users_model');
					$user = $this->users_model->get_by_id($row->user_id);
					unset($user->password);
					$row->user = $user;
					break;

				case 'department':
					$this->load->model('departments_model');
					$row->department = $this->departments_model->Get($row->department_id);
					break;

				case 'room':
					$this->load->model('rooms_model');
					$room = $this->rooms_model->Get($row->room_id);
					$row->room = $room;
					$row->room->info = $this->rooms_model->room_info($room->room_id);
					$row->room->fields = $this->rooms_model->GetFields();
					$row->room->fieldvalues = $this->rooms_model->GetFieldValues($room->room_id);
					break;

				case 'week':
					$this->load->model('weeks_model');
					$row->week = isset($row->week_id)
						? $this->weeks_model->get($row->week_id)
						: false;
					break;

				case 'period':
					$this->load->model('periods_model');
					$row->period = $this->periods_model->Get($row->period_id);
					break;

				case 'session':
					$this->load->model('sessions_model');
					$row->session = $this->sessions_model->get($row->session_id);
					break;

				case 'bookings':
					$this->load->model('bookings_model');
					$row->bookings = $this->bookings_model->find_by_repeat($row->repeat_id);
					break;

			}
		}

		return $row;
	}


	public function sleep_values($data)
	{
		return $data;
	}



}
