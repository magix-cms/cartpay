<?php
/**
 * Class Cart
 */
class Cart
{
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
		}
	}

	/**
	 * @return string
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * @param $cart
	 * @param $id
	 * @return boolean
	 */
	public function inCart($id) {
		return key_exists($id, $this->items);
	}

	/**
	 * @param int|string $item
	 * @param int $quantity
	 * @param float|null $price
	 * @param float|null $vat
	 * @return void|array
	 */
	public function addItem($item, int $quantity, $price = null, $vat = null) {
		if($quantity < 0) return;

		if($this->inCart($item)) {
			$nb = $quantity + $this->items[$item]['q'];
			$this->updItem($item, $nb, $price, $vat);
		}
		else {
			$this->nb_items += $quantity;
		}

		$this->items[$item] = [
			'q' => $quantity,
			'unit_price' => (float)$price,
			'vat' => (float)$vat
		];

		$this->saveCart();
		return $this->items[$item];
	}

	/**
	 * @param int $item
	 * @param int|null $quantity
	 * @param float|null $price
	 * @param float|null $vat
	 * @return array
	 */
	public function updItem($item, $quantity = null, $price = null, $vat = null) {
		if(!$this->inCart($item)) $this->addItem($item, $quantity, $price, $vat);

		$this->items[$item]['unit_price'] = $price === null ? $this->items[$item]['unit_price'] : $price;
		$this->items[$item]['vat'] = $vat === null ? $this->items[$item]['vat'] : $vat;

		if($quantity !== null) {
			if($quantity <= $this->items[$item]['q']) {
				$this->removeItem($item,$this->items[$item]['q'] - $quantity);
			}
			else {
				$this->nb_items -= ($this->items[$item]['q'] - $quantity);
				$this->items[$item]['q'] = $quantity;
			}
		}

		$this->saveCart();
		return isset($this->items[$item]) ? $this->items[$item] : null;
	}

	/**
	 * @param int $item
	 * @param int|string $quantity
	 * @return void|array
	 */
	public function removeItem($item, $quantity) {
		if(!$this->inCart($item)) return;
		if(is_int($quantity) && $quantity < 0) return;
		if(is_string($quantity) && $quantity !== 'all') return;

		$this->nb_items -= $quantity;

		if($quantity === $this->items[$item]['q'] || $quantity === 'all') unset($this->items[$item]);
		else $this->items[$item]['q'] = ($this->items[$item]['q'] - $quantity);

		$this->saveCart();
		return isset($this->items[$item]) ? $this->items[$item] : null;
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
			'nb_items' => $this->nb_items
		]]);
		if($current_sess !== $this->cart_name) $this->session->start($current_sess);
	}

	/**
	 * Calculate the total price, tax excluded, tax included and tax amount
	 * @return array
	 */
	public function getTotal() {
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
	 * Return all cart data
	 * @return array
	 */
	public function getCartData() {
		return [
			'items' => $this->items,
			'nb_items' => $this->nb_items,
			'total' => $this->getTotal()
		];
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