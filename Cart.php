<?php
/**
 * Class Cart
 */
class Cart
{
	/**
	 * @var object $instance
	 */
	public static $instance;

	/**
	 * @var http_session $session
	 */
	protected $session;

	/**
	 * @var string $cart_name session name of the cart
	 * @var string $key key id of the cart
	 */
	protected
		$cart_name,
		$key;

	/**
	 * @var int $nb_items Total number of items in the cart
	 */
	private $nb_items = 0;

	/**
	 * @var array $items Items in the cart
	 * @var array $fees Optional fees to add
	 * @var array $total Total prices and taxes
	 */
	private
		$items = [],
		$fees = [],
		$total = [
			'exc' => 0,
			'inc' => 0,
			'vat' => []
		];

	/**
	 * Cart constructor.
	 */
	public function __construct() {
		$settingComp = new component_collections_setting();
		$settings = $settingComp->getSetting();
		$this->session = new http_session($settings['ssl']['value']);
		$this->newCart();
	}

	/**
	 * Return the key id of the current cart
	 * @return string
	 */
	public function getKey(): string {
		return $this->key;
	}

	/**
	 * @param object|CartItem $newitem
	 * @return int
	 */
	public function inCart(object $newitem): int {
		foreach ($this->items as $i => $item) {
			if($item['item'] == $newitem) return $i;
		}
		return -1;
	}

	/**
	 * @param int|string $id
	 * @param int $quantity
	 * @param float $price
	 * @param float $vat
	 * @param array $params
	 * @return bool
	 */
	public function addItem($id, int $quantity, $price = 0, $vat = 0, array $params = []): bool {
		if($quantity < 1) return false;

		$item = new CartItem($id, $price, $vat, $params);

		$key = $this->inCart($item);

		if($key > -1) {
			$this->items[$key]['q'] = $quantity + $this->items[$key]['q'];
		}
		else {
			$this->items[] = [
				'q' => $quantity,
				'item' => $item
			];
		}
		$this->nb_items += $quantity;
		$this->saveCart();
		return $key > -1;
	}

	/**
	 * @param int|string $id
	 * @param int|null $quantity
	 * @param float|null $price
	 * @param float|null $vat
	 * @param array $params
	 * @return array|null
	 */
	public function updItem($id, int $quantity = null, float $price = null, float $vat = null, array $params = []) {
		$item = new CartItem($id, $price, $vat, $params);
		$key = $this->inCart($item);
		if($key === -1) return null;

		if($price !== null) $this->items[$key]['item']->unit_price = $price;
		if($vat !== null) $this->items[$key]['item']->vat = $vat;
		if(!empty($params)) $this->items[$key]['item']->params = $params;

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
		return $this->items[$key] ?: null;
	}

	/**
	 * @param int $index
	 * @param int|string $quantity
	 * @return void|array
	 */
	public function removeItem(int $index, $quantity) {
		if(is_int($quantity) && $quantity < 0) return;
		if(is_string($quantity) && $quantity !== 'all') return;

		$this->nb_items -= $quantity;

		if($quantity === $this->items[$index]['q'] || $quantity === 'all') unset($this->items[$index]);
		else $this->items[$index]['q'] = ($this->items[$index]['q'] - $quantity);

		$this->saveCart();
		return $this->items[$index] ?? null;
	}

	/**
	 * @param int|string $id
	 * @return boolean
	 */
	public function inCartFee($id): bool {
		return key_exists($id, $this->fees);
	}

	/**
	 * @param int|string $fee
	 * @param float|null $price
	 * @param float|null $vat
	 */
	public function addFee($fee, float $price = null, float $vat = null) {
		if($this->inCartFee($fee)) $this->updFee($fee, $price, $vat);

		$this->fees[$fee] = [
			'price' => (float)$price,
			'vat' => (float)$vat
		];

		$this->saveCart();
	}

	/**
	 * @param int|string $fee
	 * @param float|null $price
	 * @param float|null $vat
	 */
	public function updFee($fee, float $price = null, float $vat = null) {
		if(!$this->inCartFee($fee)) $this->addFee($fee, $price, $vat);

		$this->fees[$fee]['price'] = $price === null ? $this->fees[$fee]['price'] : $price;
		$this->fees[$fee]['vat'] = $vat === null ? $this->fees[$fee]['vat'] : $vat;

		$this->saveCart();
	}

	/**
	 * Calculate the product total price, tax excluded, tax included and tax amount
	 * @return array
	 */
    public function getTotalProduct(): array {
        if(!empty($this->items)) {
            foreach ($this->items as $item) {
                $rate = 1 + (floatval($item['item']->vat) / 100);
                $exc = intval($item['q']) * floatval($item['item']->unit_price);
                $inc = $exc * $rate;
                $vat = $inc - $exc;
                $this->total['exc'] += $exc;
                $this->total['inc'] += $inc;
                if(isset($this->total['vat'][$item['item']->vat]))
                    $this->total['vat'][$item['item']->vat] += $vat;
                else
                    $this->total['vat'][$item['item']->vat] = $vat;

                if(!empty($item['item']->params)) {
                    foreach ($item['item']->params as $param) {
                        if(isset($param['price']) && !empty($param['price'])) {
                            $rate = 1 + (floatval($param['price']['vat']) / 100);
                            $exc = $param['price']['price'];
                            $inc = $exc * $rate;
                            $vat = $inc - $exc;
                            $this->total['exc'] += $exc;
                            $this->total['inc'] += $inc;
                            if(isset($this->total['vat'][$param['price']['vat']]))
                                $this->total['vat'][$param['price']['vat']] += $vat;
                            else
                                $this->total['vat'][$param['price']['vat']] = $vat;
                        }
                    }
                }
            }
        }
        return $this->total;
    }

	/**
	 * Calculate the total price, tax excluded, tax included and tax amount
	 * @return array
	 */
	public function getTotal(): array {
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
	public function getCartData(): array {
		return [
			'items' => $this->items,
			'nb_items' => $this->nb_items,
            'fees' => $this->fees,
			'total' => $this->getTotal()
		];
	}

	/**
	 * Create a new Cart, if there was an opened Cart, destroy it then create a new one
	 * If a key is given, the new Cart will use it as id
	 * @param string $key (optional) id of the cart
	 * @param string $session_name (optional) name of the session
	 */
	public function newCart(string $key = '', string $session_name = 'mc_cart') {
		$params = [];
		if($key !== '') $params['ssid'] = $key;

		if($this->key !== null) {
			$this->session->close($this->cart_name);
		}
		else {
			session_write_close();
		}
		$this->session->start($session_name,$params);
		$this->cart_name = $session_name;
		$this->key = $key === '' ? (http_request::isSession('session_key_cart') ? form_inputEscape::simpleClean($_SESSION['session_key_cart']) : session_id()) : $key;
		$this->session->run(['session_key_cart' => $this->key]);
	}

	/**
	 * Open the active cart
	 */
	public function openCart() {
		$this->session->start($this->cart_name);
		if(http_request::isSession('cart')) {
			$this->items = $_SESSION['cart']['items'];
			$this->nb_items = $_SESSION['cart']['nb_items'];
			$this->fees = $_SESSION['cart']['fees'];
		}
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
	 * Empty the current cart
	 */
	public function emptyCart()	{
		$this->items = [];
		$this->nb_items = 0;
		$this->fees = [];
		$this->saveCart();
	}

	/**
	 * @return object Cart
	 */
	public static function getInstance()
	{
		if(!self::$instance instanceof self) self::$instance = new self();
		else self::$instance->openCart();
		return self::$instance;
	}
}

Class CartItem {
	public
		$id,
		$unit_price,
		$vat,
		$params;

	/**
	 * CartItem constructor.
	 * @param int $id Id
	 * @param float $u Unit Price
	 * @param float $v Rate VAT
	 * @param array $p Additionnal Parameters
	 */
	public function __construct(int $id, $u = 0, $v = 0, array $p = []) {
		$this->id = $id;
		$this->unit_price = $u;
		$this->vat = $v;
		$this->params = $p;
	}
}