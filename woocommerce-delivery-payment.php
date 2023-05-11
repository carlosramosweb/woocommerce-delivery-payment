<?php
/**
 *
 * @link              https://criacaocriativa.com
 * @since             1.0.0
 * @package           WC_Delivery_Payment_Gateway 
 *
 * @wordpress-plugin
 * Plugin Name:       WC Delivery Payment Gateway for WooCommerce
 * Plugin URI:        https://plugins.criacaocriativa.com
 * Description:       O WC Delivery Payment Gateway for WooCommerce é um plugin que adiciona uma opção de pagamento para entregas no WooCommerce.
 * Version:           1.0.0
 * Author:            carlosramosweb
 * Author URI:        https://criacaocriativa.com
 * Donate link:       https://donate.criacaocriativa.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wc-delivery-payment-gateway
 * Domain Path:       /languages
 */

// Verifica se o WooCommerce está ativo
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

    // Hook para adicionar o método de pagamento
    add_filter('woocommerce_payment_gateways', 'add_delivery_payment_gateway');

    function add_delivery_payment_gateway($gateways)
    {
        $gateways[] = 'WC_Delivery_Payment_Gateway';
        return $gateways;
    }

    // Classe do gateway de pagamento
    class WC_Delivery_Payment_Gateway extends WC_Payment_Gateway
    {
        public function __construct()
        {
            $this->id = 'delivery_payment';
            $this->method_title = 'Delivery Payment';
            $this->method_description = 'Pagamento na entrega';
            $this->has_fields = false;

            $this->init_form_fields();
            $this->init_settings();

            $this->title = $this->get_option('title');
            $this->description = $this->get_option('description');

            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        }

        public function init_form_fields()
        {
            $this->form_fields = array(
                'enabled' => array(
                    'title' => 'Ativar/Desativar',
                    'type' => 'checkbox',
                    'label' => 'Ativar pagamento na entrega',
                    'default' => 'yes'
                ),
                'title' => array(
                    'title' => 'Título',
                    'type' => 'text',
                    'description' => 'Título exibido durante o checkout',
                    'default' => 'Pagamento na entrega'
                ),
                'description' => array(
                    'title' => 'Descrição',
                    'type' => 'textarea',
                    'description' => 'Descrição exibida durante o checkout',
                    'default' => 'Realize o pagamento no momento da entrega'
                )
            );
        }

        public function process_payment($order_id)
        {
            $order = wc_get_order($order_id);
            $order->payment_complete();
            $order->reduce_order_stock();

            // Redireciona o usuário para a página de sucesso do WooCommerce
            return array(
                'result' => 'success',
                'redirect' => $this->get_return_url($order)
            );
        }
    }

    // Adiciona o gateway de pagamento
    function add_delivery_payment_gateway_class($methods)
    {
        $methods[] = 'WC_Delivery_Payment_Gateway';
        return $methods;
    }

    add_filter('woocommerce_payment_gateways', 'add_delivery_payment_gateway_class');
}
