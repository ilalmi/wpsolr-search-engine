jQuery(document).ready(function(){
	jQuery(".radio_type").change(function () {
	
		if (jQuery("#self_host").attr("checked")) {
			jQuery('#div_self_hosted').slideDown("slow");
			jQuery('#hosted_on_other').css('display','none');
		}
		else if(jQuery("#other_host").attr("checked"))
		{
			jQuery('#hosted_on_other').slideDown("slow");
			jQuery('#div_self_hosted').css('display','none');
			
			
		}
	});
	
	jQuery('#solr_index_data').click(function(){
		
		jQuery('.status_index_message').addClass('loading');
	});
	
	jQuery('#solr_delete_index').click(function(){
		jQuery('.status_del_message').addClass('loading');
		
	}
	);
	jQuery('#save_selected_index_options_form').click(function(){
		ps_types='';
		tax='';
		fields='';
		jQuery("input:checkbox[name=post_tys]:checked").each(function()
		{
			ps_types+=jQuery(this).val()+',';
		});
		pt_tp=ps_types.substring(0,ps_types.length-1);
		jQuery('#p_types').val(pt_tp);
		jQuery("input:checkbox[name=taxon]:checked").each(function()
		{
			tax+=jQuery(this).val()+',';
		});
		tx=tax.substring(0,tax.length-1);
		jQuery('#tax_types').val(tx);
		jQuery("input:checkbox[name=cust_fields]:checked").each(function()
		{
			fields+=jQuery(this).val()+',';
		});
		fl=fields.substring(0,fields.length-1);
		jQuery('#field_types').val(fl);
		
		
		
	
	});
	jQuery('#save_selected_options_form').click(function (){
		
		
		result='';
		jQuery(".facet_selected").each(function(){
				result += jQuery(this).attr('id') + ",";
			});
		result=result.substring(0,result.length-1);
											
                jQuery("#select_fac").val(result);
	})
	jQuery('#save_selected_res_options_form').click(function(){
		num_of_res=jQuery('#number_of_res').val();
		num_of_fac=jQuery('#number_of_fac').val();
		err=1;
		if(isNaN(num_of_res))
		{
			jQuery('.res_err').text("Please enter valid number of results");
			err=0;
		}
		else if(num_of_res<1 || num_of_res>100)
		{
			jQuery('.res_err').text("Number of results must be between 1 and 100");
			err=0;
		}
		else 
		{
			jQuery('.res_err').text();
		}
		
		if(isNaN(num_of_fac))
		{
			jQuery('.fac_err').text("Please enter valid number of facets");
			err=0;
		}
		else if(num_of_fac<1 || num_of_fac>100)
		{
			jQuery('.fac_err').text("Number of facets must be between 1 and 100");
			err=0;
		}
		else 
		{
			jQuery('.fac_err').text();
			
		}
		if(err==0)
			return false;
	})
	jQuery('#check_solr_status').click(function(){
		path=jQuery('#adm_path').val();
		
		host=jQuery('#solr_host').val();
		port=jQuery('#solr_port').val();
		spath=jQuery('#solr_path').val();
		
		if(spath.substr(spath.length-1,1)=='/')
			spath=spath.substr(0,spath.length-1);
			
			jQuery('#solr_path').val(spath);
			
		error=0;
		if(host==''){
			jQuery('.host_err').text('Please enter solr host');	
		error=1;
		}
		else{
			jQuery('.host_err').text('');	
		}
		
		if(isNaN(port) || port.length<2)
		{
			jQuery('.port_err').text('Please enter valid port');
			error=1;
		}
		else
			jQuery('.port_err').text('');
		
		if(spath==''){
			jQuery('.path_err').text('Please enter solr path');	
		error=1;
		}
		else
			jQuery('.path_err').text('');	
		if(error==1)
			{
				return false;
			
			}
		else
			{
				jQuery('.img-succ').css('display','none');
				jQuery('.img-err').css('display','none');
				jQuery('.img-load').css('display','inline');
		
				jQuery.ajax({
								url: path+'admin-ajax.php',
								type: "post",
								timeout: 3000,
								data: { action:'return_solr_instance','shost':host,'sport':port,'spath':spath},
								success: function(data1){
									
									jQuery('.img-load').css('display','none');
									if(data1==0)
									{
										jQuery('.solr_error').html('');
										jQuery('.img-succ').css('display','inline');
										jQuery('#settings_conf_form').submit();
		 						        }
								        else
									{
										jQuery('.solr_error').html(data1);
									}
															},
								error:function(){
									jQuery('.img-load').css('display','none');
								
									jQuery('.solr_error').text('We could not contact your Solr server. It\'s probably because port ' + port + ' is blocked. Please try another port, for instance 443, or contact your hosting provider to unblock port ' + port + '.');
								
								}   
							      });
		
			}
			
	})
	jQuery('#check_solr_status_third').click(function(){
		path=jQuery('#adm_path').val();
		host=jQuery('#gtsolr_host').val();
		port=jQuery('#gtsolr_port').val();
		spath=jQuery('#gtsolr_path').val();
		pwd=jQuery('#gtsolr_secret').val();
		user=jQuery('#gtsolr_key').val();
		protocol=jQuery('#gtsolr_protocol').val();
		
		
		if(spath.substr(spath.length-1,1)=='/')
			spath=spath.substr(0,spath.length-1);
			jQuery('#gtsolr_path').val(spath);
		error=0;
		if(host=='')
		{
			jQuery('.ghost_err').text('Please enter solr host');	
			error=1;
		}
		else{
			jQuery('.ghost_err').text('');	
		}
		
		if(isNaN(port) || port.length<2)
		{
			jQuery('.gport_err').text('Please enter valid port');
			error=1;
		}
		else
			jQuery('.gport_err').text('');
		
		if(spath==''){
			jQuery('.gpath_err').text('Please enter solr path');	
			error=1;
		}
		else
			jQuery('.gpath_err').text('');
		if(pwd==''){
			jQuery('.gsec_err').text('Please enter solr secret');	
			error=1;
		}
		else
			jQuery('.gsec_err').text('');
		if(user==''){
			jQuery('.gkey_err').text('Please enter solr key');	
			error=1;
		}
		else
			jQuery('.gkey_err').text('');
		if(error==1)
			return false;
		else
			{
				jQuery('.img-succ').css('display','none');
				jQuery('.img-err').css('display','none');
				jQuery('.img-load').css('display','inline');
				jQuery.ajax({
						url: path+'admin-ajax.php',
						type: "post",
						data: { action:'return_goto_solr_instance','proto':protocol,'shost':host,'sport':port,'spath':spath,'spwd':pwd,'skey':user},
						timeout: 3000,
						success: function(data1){
									jQuery('.img-load').css('display','none');
									if(data1==0)
								        {
										jQuery('.solr_error').html('');
										jQuery('.img-succ').css('display','inline');
										jQuery('#settings_conf_form').submit();
									}
									else if(data1==1)
										jQuery('.solr_error').text('Error in detecting solr instance');
									else
										jQuery('.solr_error').html(data1);
								
								},
								error:function(){
								 
									jQuery('.img-load').css('display','none');
								
									jQuery('.solr_error').text('We could not contact your Solr server. It\'s probably because port ' + port + ' is blocked. Please try another port, for instance 443, or contact your hosting provider to unblock port ' + port + '.');
								}   
							      });
		
			}
			
		 
	})
	
		
	jQuery('.plus_icon').click(function()
	{
		jQuery(this).parent().addClass('facet_selected');
		jQuery(this).hide();
		jQuery(this).siblings().css('display','inline');
	})
	
	jQuery('.minus_icon').click(function()
	{
		jQuery(this).parent().removeClass('facet_selected');
		jQuery(this).hide();
		jQuery(this).siblings().css('display','inline');
	})
	 jQuery( "#sortable1" ).sortable(
	{
                connectWith: ".connectedSortable",
                stop: function(event, ui) {
                        jQuery('.connectedSortable').each(function()
			{
				result = "";
                                jQuery(this).find(".facet_selected").each(function()
									  {
                                                                                result += jQuery(this).attr('id') + ",";
                                                                          });
										result=result.substring(0,result.length-1);
													
                                                                                jQuery("#select_fac").val(result);
                                                                                });
                                        }
        });
	
	
	
});
