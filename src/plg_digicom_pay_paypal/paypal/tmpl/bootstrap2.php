<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;
//data-digicom-task="formSubmit"
$buy_image = $this->params->get('buy_image', 'https://www.paypalobjects.com/webstatic/en_US/i/buttons/checkout-logo-medium.png');
// $this->params->get('preview_image');
?>
<div class="digicom-payment-form">

	<div class="form-actions text-center">
		<input
		type="image"
		name="submit"
		src="<?php echo $buy_image; ?>"
		value="<?php echo JText::_('SUBMIT'); ?>"
		alt="Check out with PayPal - it's fast, free and secure!"
		onclick="Digicom.submitForm('#showFormSubmitModal', event);"
		/>
	</div>
	<div data-digicom-task="hide">
		<form data-digicom-id="paymentForm" action="<?php echo $vars->action_url ?>" class="form-horizontal" method="post">

			<input type="hidden" name="business" value="<?php echo $vars->business ?>" />
			<input type="hidden" name="custom" value="<?php echo $vars->order_id ?>" />

			<input type="hidden" name="item_name" value="<?php echo $vars->item_name ?>" />
			<input type="hidden" name="amount" value="<?php echo number_format( $vars->amount, 2); ?>" />

			<input type="hidden" name="return" value="<?php echo $vars->return ?>" />
			<input type="hidden" name="cancel_return" value="<?php echo $vars->cancel_return ?>" />
			<input type="hidden" name="notify_url" value="<?php echo $vars->notify_url ?>" />
			<input type="hidden" name="currency_code" value="<?php echo $vars->currency_code ?>" />
			<input type="hidden" name="no_note" value="1" />

			<!--//_cart when manual calc and multiple items-->
			<input type="hidden" name="cmd" value="_xclick" />

			<div class="hide">
				<input type="submit"
				value="<?php echo JText::_('SUBMIT'); ?>" 
				alt="Make payments with PayPal - it's fast, free and secure!" />
			</div>

		</form>
	</div>
	<?php
	$layoutData = array(
		'selector' => 'showFormSubmitModal',
		'params'   => array(
			'title'		=> JText::_('COM_DIGICOM_LOADING_TEXT'),
			'height' 	=> 'auto',
			'width'	 	=> 'auto',
			'closeButton'	=> false
			),
		'body'     => '<div class="container-fluid center">
			<h3>'.JText::_('PLG_DIGICOM_PAY_PAYPAL_WAIT') . '</h3>
				<div class="progress progress-striped active">
						<div class="bar" style="width: 100%;"></div>
				</div>
		</div>'
	);
	echo JLayoutHelper::render('bt3.modal.main', $layoutData);
	?>
</div>
