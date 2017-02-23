<?PHP
class Transaction {
	private $Tid;
	private $Amount;
	private $Type;
	private $Status;
	private $Date;
	private $Pid;
	private $FromUid;
	private $ToUid;
	private $AddTs;
	private $UpdateTs;


	public function __construct($Tid,$Amount,$Type,$Status,$Date,$Pid,$FromUid,$ToUid,$AddTs,$UpdateTs) {
		$this->Tid = $Tid;
		$this->Amount = $Amount;
		$this->Type = $Type;
		$this->Status = $Status;
		$this->Date = $Date;
		$this->Pid = $Pid;
		$this->FromUid = $FromUid;
		$this->ToUid = $ToUid;
		$this->AddTs = $AddTs;
		$this->UpdateTs = $UpdateTs;
	}

	public function getTid() {
		 return $this->Tid;
	}

	public function getAmount() {
		 return $this->Amount;
	}

	public function getType() {
		 return $this->Type;
	}

	public function getStatus() {
		 return $this->Status;
	}

	public function getDate() {
		 return $this->Date;
	}

	public function getPid() {
		 return $this->Pid;
	}

	public function getFromUid() {
		 return $this->FromUid;
	}

	public function getToUid() {
		 return $this->ToUid;
	}

	public function getAddTs() {
		 return $this->AddTs;
	}

	public function getUpdateTs() {
		 return $this->UpdateTs;
	}
}
?>
