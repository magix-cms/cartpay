<?php
/**
 * Class Cart
 */
class Cart
{
	/**
	 * @var $instance
	 */
	public static $instance;

	protected
		$settingComp,
		$settings,
		$session,
		$cart_name,
		$data,
		$key;

	private
		$items = [],
		$nb_items = 0,
		$fees = [],
		$total = [
			'exc' => 0,
			'inc' => 0,
			'vat' => []
		];

	/**
	 * Cart constructor.
	 * @param string $session_name
	 * @param null|string $key
	 */
	public function __construct($session_name, $key = null) {
		$this->settingComp = new component_collections_setting();
		$this->settings = $this->settingComp->getSetting();
		$this->session = new http_session($this->settings['ssl']['value']);
        session_write_close();
        $params = [];
        if($key !== null) $params['ssid'] = $key;
		$this->session->start($session_name,$params);
		$this->cart_name = $session_name;

		// --- Session
		$this->key = $key === null ? (http_request::isSession('session_key_cart') ? form_inputEscape::simpleClean($_SESSION['session_key_cart']) : session_id()) : $key;
		$this->session->run(['session_key_cart' => $this->key]);
		if(http_request::isSession('cart')) {
			$cart = $_SESSION['cart'];
			$this->items = $cart['items'];
			$this->nb_items = $cart['nb_items'];
			$this->fees = $cart['fees'];
		}
		self::$instance = $this;
	}

	/**
	 *
	 */
	public function renew()
	{
		$this->session->regenerate();
		$this->key = session_id();
		$this->session->run(['session_key_cart' => $this->key]);
	}

	/**
	 * @return string
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * @param $id
	 * @param $param
	 * @return boolean
	 */
	public function inCart($id, $param = []) {
		foreach ($this->items as $i => $item) {
			if($item['id'] === $id) {
				if(!empty($param) && !empty($item['param'])) {
					$same = true;
					foreach ($param as $k => $v) {
						if(!key_exists($k,$item['param']) || $v !== $item['param'][$k]) $same = false;
					}
					if($same) return $i;
				}
				else {
					return $i;
				}
			}
		}
		return -1;
	}

	/**
	 * @param int|string $item
	 * @param int $quantity
	 * @param float|null $price
	 * @param float|null $vat
	 * @param array $param
	 * @return void|array
	 */
	public function addItem($item, int $quantity, $price = null, $vat = null, $param = []) {
		if($quantity < 0) return;

		$key = $this->inCart($item, $param);

		if($key > -1) {
			$nb = $quantity + $this->items[$key]['q'];
			return $this->updItem($item, $nb, $price, $vat, $param);
		}
		else {
			$this->nb_items += $quantity;
			$newItem = [
				'id' => $item,
				'q' => $quantity,
				'unit_price' => (float)$price,
				'vat' => (float)$vat,
				'param' => $param
			];
			$this->items[] = $newItem;

			$this->saveCart();
			return $newItem;
		}
	}

	/**
	 * @param int $item
	 * @param int|null $quantity
	 * @param float|null $price
	 * @param float|null $vat
	 * @param array $param
	 * @return array
	 */
	public function updItem($item, $quantity = null, $price = null, $vat = null, $param = []) {
		$key = $this->inCart($item, $param);
		if($key === -1) $this->addItem($item, $quantity, $price, $vat);

		$this->items[$key]['unit_price'] = $price === null ? $this->items[$key]['unit_price'] : $price;
		$this->items[$key]['vat'] = $vat === null ? $this->items[$key]['vat'] : $vat;

		if(!empty($param)) {
			foreach ($param as $k => $v) {
				$this->items[$key]['param'][$k] = $v;
			}
		}

		if($quantity !== null) {
			if($quantity <= $this->items[$key]['q']) {
				$this->removeItem($key,$this->items[$key]['q'] - $quantity);
			}
			else {
				$this->nb_items -= ($this->items[$key]['q'] - $quantity);
				$this->items[$key]['q'] = $quantity;
			}
		}

		$this->saveCart();
		return $this->items[$key] ?? null;
	}

	/**
	 * @param int $index
	 * @param int|string $quantity
	 * @return void|array
	 */
	public function removeItem($index, $quantity) {
		if(is_int($quantity) && $quantity < 0) return;
		if(is_string($quantity) && $quantity !== 'all') return;

		$this->nb_items -= $quantity;

		if($quantity === $this->items[$index]['q'] || $quantity === 'all') unset($this->items[$index]);
		else $this->items[$index]['q'] = ($this->items[$index]['q'] - $quantity);

		$this->saveCart();
		return $this->items[$index] ?? null;
	}

	/**
	 * @param $cart
	 * @param $id
	 * @return boolean
	 */
	public function inCartFee($id) {
		return key_exists($id, $this->fees);
	}

	/**
	 * @param int|string $fee
	 * @param float|null $price
	 * @param float|null $vat
	 * @return array
	 */
	public function addFee($fee, $price = null, $vat = null){
		if($this->inCartFee($fee)) $this->updFee($fee, $price, $vat);

		$this->fees[$fee] = [
			'price' => (float)$price,
			'vat' => (float)$vat
		];

		$this->saveCart();
		return $this->fees[$fee];
	}

	/**
	 * @param int $fee
	 * @param float|null $price
	 * @param float|null $vat
	 * @return array
	 */
	public function updFee($fee, $price = null, $vat = null) {
		if(!$this->inCartFee($fee)) $this->addFee($fee, $price, $vat);

		$this->fees[$fee]['price'] = $price === null ? $this->fees[$fee]['price'] : $price;
		$this->fees[$fee]['vat'] = $vat === null ? $this->fees[$fee]['vat'] : $vat;

		$this->saveCart();
		return $this->fees[$fee] ?? null;
	}

	/**
	 *
	 */
	public function emptyCart() {
		$this->session->close($this->cart_name);
	}

	/**
	 * Save cart in session var
	 */
	private function saveCart() {
		$current_sess = session_name();
		if($current_sess !== $this->cart_name) $this->session->start($this->cart_name);
		$this->session->run(['cart' => [
			'items' => $this->items,
			'nb_items' => $this->nb_items,
			'fees' => $this->fees
		]]);
		if($current_sess !== $this->cart_name) $this->session->start($current_sess);
	}

	/**
	 * Calculate the product total price, tax excluded, tax included and tax amount
	 * @return array
	 */
    public function getTotalProduct() {
        if(!empty($this->items)) {
            foreach ($this->items as $item) {
                $rate = 1 + (floatval($item['vat']) / 100);
                $exc = intval($item['q']) * floatval($item['unit_price']);
                $inc = $exc * $rate;
                $vat = $inc - $exc;
                $this->total['exc'] += $exc;
                $this->total['inc'] += $inc;
                if(isset($this->total['vat'][$item['vat']]))
                    $this->total['vat'][$item['vat']] += $vat;
                else
                    $this->total['vat'][$item['vat']] = $vat;
            }
        }
        return $this->total;
    }

	/**
	 * Calculate the total price, tax excluded, tax included and tax amount
	 * @return array
	 */
	public function getTotal() {
        $this->getTotalProduct();
        if(!empty($this->fees)) {
            foreach ($this->fees as $fee) {
                $rate = 1 + (floatval($fee['vat']) / 100);
                $exc = floatval($fee['price']);
                $inc = $exc * $rate;
                $vat = $inc - $exc;
                $this->total['exc'] += $exc;
                $this->total['inc'] += $inc;
                if(isset($this->total['vat'][$fee['vat']]))
                    $this->total['vat'][$fee['vat']] += $vat;
                else
                    $this->total['vat'][$fee['vat']] = $vat;
            }
        }
        return $this->total;

	}

	/**
	 * Return all cart data
	 * @return array
	 */
	public function getCartData() {
		return [
			'items' => $this->items,
			'nb_items' => $this->nb_items,
            'fees' => $this->fees,
			'total' => $this->getTotal()
		];
	}

	/**
	 * @return Cart
	 */
	public static function getInstance($session_name, $key = null)
	{
		if(!self::$instance instanceof self) new self($session_name, $key);
		return self::$instance;
	}
}

Class CartItem {
	public
		$q,
		$unit_price,
		$vat;

	/**
	 * CartItem constructor.
	 * @param int $q Quantity
	 * @param null|float $u Unit Price
	 * @param null|float $v Rate VAT
	 */
	public function __construct($q = 0, $u = null, $v = null)
	{
		$this->q = $q;
		$this->unit_price = $u;
		$this->vat = $v;
	}
}