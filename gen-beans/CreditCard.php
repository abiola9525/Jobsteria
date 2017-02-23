<?PHP
class CreditCard {
	private $Uid;
	private $MerchantId;
	private $MerchantRefCode;
	private $CardAccountNumber;
	private $CardExpirationMonth;
	private $CardExpirationYear;
	private $CardLastFourDigits;
	private $CardName;
	private $CardSecurityId;


	public function __construct($Uid,$MerchantId,$MerchantRefCode,$CardAccountNumber,$CardExpirationMonth,$CardExpirationYear,$CardLastFourDigits,$CardName,$CardSecurityId) {
		$this->Uid = $Uid;
		$this->MerchantId = $MerchantId;
		$this->MerchantRefCode = $MerchantRefCode;
		$this->CardAccountNumber = $CardAccountNumber;
		$this->CardExpirationMonth = $CardExpirationMonth;
		$this->CardExpirationYear = $CardExpirationYear;
		$this->CardLastFourDigits = $CardLastFourDigits;
		$this->CardName = $CardName;
		$this->CardSecurityId = $CardSecurityId;
	}

	public function getUid() {
		 return $this->Uid;
	}

	public function getMerchantId() {
		 return $this->MerchantId;
	}

	public function getMerchantRefCode() {
		 return $this->MerchantRefCode;
	}

	public function getCardAccountNumber() {
		 return $this->CardAccountNumber;
	}

	public function getCardExpirationMonth() {
		 return $this->CardExpirationMonth;
	}

	public function getCardExpirationYear() {
		 return $this->CardExpirationYear;
	}

	public function getCardLastFourDigits() {
		 return $this->CardLastFourDigits;
	}

	public function getCardName() {
		 return $this->CardName;
	}

	public function getCardSecurityId() {
		 return $this->CardSecurityId;
	}
}
?>
