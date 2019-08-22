<?php 
class ModelPaymentTltBraintree3DS extends Model {
	public function getMethod($address, $total) {
		$this->language->load('payment/tltbraintree3DS');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('tltbraintree3DS_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if ($this->config->get('tltbraintree3DS_total') > 0 && $this->config->get('tltbraintree3DS_total') > $total) {
			$status = false;
		} elseif (!$this->config->get('tltbraintree3DS_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}

        $method_data = array();

        if ($status) {
            $terms = $payment_sources = '';

            if ($this->config->get('tltbraintree3DS_paypal')) {
                $payment_sources .= 'p';
            }

            if ($this->config->get('tltbraintree3DS_googlepay') && ($this->config->get('tltbraintree3DS_googlepay_mid') || ($this->config->get('tltbraintree3DS_mode') == 'sandbox'))) {
                $payment_sources .= 'g';
            }

            $payment_sources .= 'c';

            switch ($payment_sources) {
                case 'pgc':
                    $title = $this->language->get('text_googlepay') . ', ' . $this->language->get('text_paypal') . ', ' . $this->language->get('text_card');
                    break;
                case 'pc':
                    $title = $this->language->get('text_paypal') . ', ' . $this->language->get('text_card');
                    break;
                case 'gc':
                    $title = $this->language->get('text_googlepay') . ', ' . $this->language->get('text_card');
                    break;
                case 'c':
                    $title = $this->language->get('text_card');
                    $terms = $this->language->get('text_terms');
                    break;
            }

            if (is_file(DIR_IMAGE . 'payment/Braintree-' . $payment_sources . '-logo.png')) {
                $logo = $this->config->get('config_url') . 'image/payment/Braintree-' . $payment_sources . '-logo.png';
            } else {
                $logo = '';
            }

            $method_data = array(
                'code'       => 'tltbraintree3DS',
                'title'      => $title,
                'terms'      => $terms,
                'logo'       => $logo,
                'sort_order' => $this->config->get('tltbraintree3DS_sort_order')
            );
        }

		return $method_data;
	}

    /**
     * Get currencies array in ISO 4217 format
     * https://en.wikipedia.org/wiki/ISO_4217
     *
     * Comment unused currencies, or add currencies needed
     * ISO Alpha => ISO Numeric
     *
     * @return array
     */
	public function currencies() {
        return array(
            'AUD' => '036',
            'CAD' => '124',
            'CHF' => '756',
            'CNY' => '156',
            'EUR' => '978',
            'GBP' => '826',
            'HKD' => '344',
            'JPY' => '392',
            'RUB' => '643',
            'UAH' => '980',
            'USD' => '840'
        );
    }

    /**
     * Braintree supported locales
     * https://github.com/braintree/braintree-web-drop-in/tree/master/src/translations
     *
     * @return array
     */
    public function locales() {
        return array(
            'da' => 'da_DK',
            'de' => 'de_DE',
            //'en' => 'en_AU',
            //'en' => 'en_GB',
            'en' => 'en_US',
            'es' => 'es_ES',
            //'fr' => 'fr_CA',
            'fr' => 'fr_FR',
            'id' => 'id_ID',
            'it' => 'it_IT',
            'ja' => 'ja_JP',
            'ko' => 'ko_KR',
            'nl' => 'nl_NL',
            'no' => 'no_NO',
            'pl' => 'pl_PL',
            //'pt' => 'pt_BR',
            'pt' => 'pt_PT',
            'ru' => 'ru_RU',
            'sv' => 'sv_SE',
            'th' => 'th_TH',
            'zh' => 'zh_CN',
            //'zh' => 'zh_HK',
	        //'zh' => 'zh_TW'
        );
    }
}
