/**
 * Digicom Plugin for script
 * copyright	Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * license		GNU General Public License version 2 or later; see LICENSE.txt
 */

if (typeof jQuery === 'undefined') {
   throw new Error('Digicom\'s JavaScript requires jQuery')
}

+function ($) {
   'use strict';
   var version = $.fn.jquery.split(' ')[0].split('.')
   if ((version[0] < 2 && version[1] < 9) || (version[0] == 1 && version[1] == 9 && version[2] < 1) || (version[0] > 2)) {
     throw new Error('Digicom\'s JavaScript requires jQuery version 1.9.1 or higher, but lower than version 3')
   }
}(jQuery);

+(function( $, scope ) {
	"use strict";

	var Digicom = scope.Digicom = {

		/**
		 * Basic setup
		 *
		 * @return  void
		 */
		initialize: function()
		{
			Digicom.root(); // get site root url first
			Digicom.checkPersonStatus(); // check if need to toggle the field

			// on click show terms, show
			Digicom.dataSet('showterms').click(function(e) {
         e.preventDefault();
         $('#termsShowModal').digicomodal('show');
			});

			// on click show terms, show
			Digicom.taskSet('loading').click(function() {
					$(this).append('<div class="digicom-loader small"></div>');
					$(this).addClass('disabled');
			});

			// on click agree, make the agreeterms accepet
			Digicom.dataSet('action-agree').click(function() {
			    $('input[name="agreeterms"]').attr('checked', 'checked');
			});

			$(document).on("click", "input:radio[id^='jform_person']", function (event) {
				Digicom.checkPersonStatus();
			});

			// lets handle some data-digicom-task
			if( Digicom.taskSet('hide').length ){
				Digicom.taskSet('hide').hide();
			}

			if( Digicom.taskSet('formSubmit').length ){
				Digicom.taskSet('formSubmit').submit();
			}
      Digicom.dataSet('digicom_country').each(function()
      {
        var item = $(this);
        Digicom.changeStatelist(item);
      });
		},

		/**
		* get digicom change state
		*/
		changeStatelist: function(e)
		{
      var ajaxurl = this.root + 'administrator/index.php?option=com_digicom&task=action&format=json';
      var item = $(e);

      var data = {
        action      : 'get_store_states',
        class       : 'DigiComHelperCountry',
        country     : item.val(),
        field_name  : item.attr('name').replace('country', 'state')
      };
      // console.log(data);
      var stateval = item.parent().parent().next().find('input').val();
      $.getJSON(ajaxurl, data, function(response)
      {
        var total = Object.keys(response).length;

        if(total == '0')
        {
          var text_field = '<input type="text" name="' + data.field_name + '" value=""/>';
          item.parent().parent().next().find('select[name="'+data.field_name+'"]').chosen('destroy');
          item.parent().parent().next().find('select').replaceWith( text_field );
        }
        else
        {
          var items = [];

          // Build options
          $.each(response, function(key, val) {
            if(key == stateval){
              items.push('<option value="' + key + '" selected>' + val + '</option>');
            }else{
              items.push('<option value="' + key + '">' + val + '</option>');
            }

          });

          // Replace current select options. The trigger is needed for Chosen select box enhancer
          var select_field = '<select name="' + data.field_name + '"></select>';
          item.parent().parent().next().find('select').chosen('destroy');
          item.parent().parent().next().find('select, input').replaceWith( select_field );
          item.parent().parent().next().find('select').empty().append(items).chosen();
        }
      });
		},
		/**
		* get digicom dataset
		*/
		submitForm: function(id, e)
		{
			e.preventDefault();

			$(id).digicomodal({
				backdrop  : 'static',
				keyboard  : true
			});

			Digicom.dataSet("paymentForm").submit();
		},
		/**
		/**
		* get digicom dataset
		*/
		dataSet: function(name)
		{
			return $('[data-digicom-id="'+name+'"]');
		},
		/**
		* get digicom taskset
		*/
		taskSet: function(name)
		{
			return $('[data-digicom-task="'+name+'"]');
		},

		/**
		* root method to get root url
		*/
		root: function()
		{
			// Set the site root path
			this.root = site_url;
			return this.root;
		},

		/**
		* checkPersonStatus for registration info
		*/
		checkPersonStatus: function()
		{
			var persion = $("input:radio[name='jform[person]']:checked").val();
      $("#jform_person label[for^='jform_person']").addClass('btn btn-default');
			if(persion == '1'){
				$(".group-input-company, .group-input-tax").hide();
				$("label[for='jform_person0']").addClass('active btn-success');
				$("label[for='jform_person1']").removeClass('active btn-success');
			}else{
				$(".group-input-company, .group-input-tax").show();
				$("label[for='jform_person0']").removeClass('active btn-success');
				$("label[for='jform_person1']").addClass('active btn-success');
			}
		},

		/**
		* addtoCart
		*/
		addtoCart: function(pid, to_cart)
		{
			if( Digicom.dataSet("quantity_" + pid).length ){
				var qty = Digicom.dataSet("quantity_"+pid).val();
			}else{
				var qty = 1;
			}

			var url = this.root + 'index.php?option=com_digicom&view=cart&task=cart.add&from=ajax&pid='+pid+'&qty='+qty;

			$('#digicomCartPopup .modal-body').html('<div class="digicom-loader"></div>');

			$.ajax({
		      url: url,
					method: 'get',
		      success: function (data, textStatus, xhr) { // data= returnval, textStatus=success, xhr = responseobject
						$('#digicomCartPopup .modal-body').html(data);
					}
			});

			$('#digicomCartPopup').digicomodal('show');

			Digicom.refresCartModule();
		},
		/**
		* Update Cart Method
		*/
		updateCart: function (pid)
		{
			var url = this.root + "index.php?option=com_digicom&view=cart&task=cart.getCartItem&cid="+pid;

			if ( Digicom.dataSet('quantity'+pid).length ) {
        var quantity = Digicom.dataSet('quantity'+pid).val();
				if(quantity <= 0){
          quantity = 1;
          Digicom.dataSet('quantity'+pid).val('1');
        }
        url += '&quantity'+pid+'=' + quantity;
			}

			if ( Digicom.dataSet('promocode').length ) {
        var promocode = Digicom.dataSet('promocode').val();
				if(promocode != ''){
          url += '&promocode=' + promocode;
        }
			}


			$.ajax({
		      url: url,
					method: 'get',
		      success: function (data, textStatus, xhr) { // data= returnval, textStatus=success, xhr = responseobject
						var responce = $.parseJSON(data);
						console.log(responce);
						var cart_item_price = eval('responce.cart_item_price'+pid);
						var cart_item_total = eval('responce.cart_item_total'+pid);
						var cart_item_discount = eval('responce.cart_item_discount'+pid);

						Digicom.dataSet('price'+pid).html(cart_item_price);
						Digicom.dataSet('total'+pid).html(cart_item_total);

						Digicom.dataSet('discount'+pid).html(cart_item_discount);

						Digicom.dataSet('cart_subtotal').html(responce.cart_subtotal);
						Digicom.dataSet('cart_discount').html(responce.cart_discount);
						Digicom.dataSet('cart_tax').html(responce.cart_tax);
						Digicom.dataSet('cart_total').html(responce.cart_total);

						Digicom.refresCartModule();
		      }
		  });
		},

		/**
		* Update Cart Method
		*/
		refreshCart: function ()
		{
			Digicom.dataSet('task').val('cart.updateCart');
			Digicom.dataSet('cart_form').submit();
		},

		/**
		* Update Cart Method
		*/
		deleteFromCart: function (cartid, e)
		{
      e.preventDefault();
			location.href = this.root+'index.php?option=com_digicom&view=cart&task=cart.deleteFromCart&cartid='+cartid;
		},

		/**
		* ajax refresh digicom cart module
		*/
		refresCartModule: function()
		{
			//
			if(Digicom.dataSet('mod_digicom_cart_wrap').length){
				var url = this.root + 'index.php?option=com_digicom&view=cart&task=cart.get_cart_content';
				$.ajax({
			      url: url,
						method: 'get',
			      success: function (data, textStatus, xhr) { // data= returnval, textStatus=success, xhr = responseobject
							Digicom.dataSet('mod_digicom_cart_wrap').html(data);
						}
			  });
			}

		},

		/**
		* ajax refresh digicom cart module
		*/
		goCheckout: function()
		{
			// data-digicom-id
			var agreeterms = true;
			var processor = '';
			// check agree terms
			if ( Digicom.dataSet('agreeterms').length ) {
				agreeterms = Digicom.dataSet('agreeterms').prop( "checked" );
			}
			if(!agreeterms){
				$('#termsAlertModal').digicomodal('show');
				return false;
			}

			// check processor
			if ( Digicom.dataSet('processor').length ) {
				processor = Digicom.dataSet('processor').val();
			}
			if ( !processor.length ) {
				$('#paymentAlertModal').digicomodal('show');
				return false;
			}

			// all checked passed, submit the form
			Digicom.dataSet('cart_form').submit();

		},

		/**
		 * Generic function to validateInput by name
		 * @targetScript   string
		 * @varName   string  from event hook like: username, email
		 * @return  string
		 */
		validateInput: function(input)
		{
			var formname 	= 'jform_' + input;
			var value 		= $('#'+formname).val();
			var url				= this.root + 'index.php?option=com_digicom&task=cart.validate_input&input='+input+'&value='+value;

			if(value != ""){

				$.ajax({
			      url: url,
						method: 'get',
			      success: function (data, textStatus, xhr) { // data= returnval, textStatus=success, xhr = responseobject
							data = parseInt(data);

							if(data == "1")
							{
								if(input == "email"){
									var msg = Joomla.JText._('COM_DIGICOM_REGISTRATION_EMAIL_ALREADY_USED');
								}
								else{
									var msg = Joomla.JText._('COM_DIGICOM_REGISTER_USERNAME_TAKEN');
								}

								if ( !$('#'+formname+'-warning').length ) {
									var warning = '<span id="'+formname+'-warning" class="label label-warning">'+msg+'</span>';
									$('#'+formname).parent().append(warning);
								}

							}else{
								$('#'+formname+'-warning').remove();
							}
						}
			  });
			}
		}

	};

	$(function() {

		Digicom.initialize();

	});

}( jQuery, window ));
