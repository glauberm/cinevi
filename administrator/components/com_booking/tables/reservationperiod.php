<?php

class TableReservationPeriod extends JTable {

	public $id;
	public $reservation_item_id;
	public $from;
	public $to;
	
	public function __construct(& $db)
	{
		parent::__construct('#__booking_reservation_period', 'id', $db);
	}
}