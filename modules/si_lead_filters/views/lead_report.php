<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();
$report_heading = '';
?>
<link href="<?php echo module_dir_url('si_lead_filters','assets/css/si_lead_filters_style.css'); ?>" rel="stylesheet" />
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<?php echo form_open($this->uri->uri_string() . ($this->input->get('filter_id') ? '?filter_id='.$this->input->get('filter_id') : ''),"id=si_form_lead_filter"); ?>
						<h4 class="pull-left"><?php echo _l('si_lf_submenu_lead_filters'); ?> <small class="text-success"><?php echo htmlspecialchars($saved_filter_name);?></small></h4>
						<div class="btn-group pull-right mleft4 btn-with-tooltip-group" data-toggle="tooltip" data-title="<?php echo _l('si_lf_filter_templates'); ?>" data-original-title="" title="">
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-list"></i>
							</button>
							<ul class="row dropdown-menu width400">
							<?php
							if(!empty($filter_templates))
							{
								foreach($filter_templates as $row)
								{
									echo "<li><a href='leads_filter?filter_id=$row[id]'>$row[filter_name]</a></li>";
								}
							}
							else
								echo '<li><a >'._l('si_lf_no_filter_template').'</a></li>';
							?>
							</ul>
						</div>
						<button type="submit" data-toggle="tooltip" data-title="<?php echo _l('si_lf_apply_filter'); ?>" class=" pull-right btn btn-info mleft4"><?php echo _l('filter'); ?></button>
						<a href="leads_filter" class=" pull-right btn btn-info mleft4"><?php echo _l('new'); ?></a>
						<div class="clearfix"></div>
						<hr />
						<div class="row">
							<?php if(has_permission('leads','','view')){ ?>
							<div class="col-md-2 border-right">
								<label for="rel_type" class="control-label"><?php echo _l('staff_members'); ?></label>
								<?php echo render_select('member',$members,array('staffid',array('firstname','lastname')),'',$staff_id,array('data-none-selected-text'=>_l('all_staff_members')),array(),'no-margin'); ?>
							</div>
							<?php } ?>
							<div class="col-md-2 text-center1 border-right">
								<label for="status" class="control-label"><?php echo _l('lead_status'); ?></label>		
								<?php 
								echo render_select('status[]',$lead_statuses,array('id','name'),'',$statuses,array('data-width'=>'100%','data-none-selected-text'=>_l('leads_all'),'multiple'=>true,'data-actions-box'=>true),array(),'no-mbot','',false);?>
								
							</div>
							<!--start sources select -->
							<div class="col-md-2  border-right">
								<label for="rel_type" class="control-label"><?php echo _l('lead_source'); ?></label>		
								<?php 
								echo render_select('source[]',$lead_sources,array('id','name'),'',$sources,array('data-width'=>'100%','data-none-selected-text'=>_l('leads_all'),'multiple'=>true,'data-actions-box'=>true),array(),'no-mbot','',false);?>
							</div>
							<!--end sources select-->
							<!--start country select -->
							<div class="col-md-2  border-right">
								<label for="rel_type" class="control-label"><?php echo _l('lead_country'); ?></label>		
								<?php 
								$lead_countries[]=array('id'=>-1,'name'=>_l('si_lf_unknown'));
								echo render_select('countries[]',$lead_countries,array('id','name'),'',$countries,array('data-width'=>'100%','data-none-selected-text'=>_l('leads_all'),'multiple'=>true,'data-actions-box'=>true),array(),'no-mbot','',false);?>
							</div>
							<!--end counry select-->
							<!--start tags -->
							<div class="col-md-2 text-center1 border-right">
								<label for="rel_type" class="control-label"><?php echo _l('tags'); ?></label>		
								<?php 
								echo render_select('tags[]',get_tags(),array('id','name'),'',$tags,array('data-width'=>'100%','data-none-selected-text'=>_l('leads_all'),'multiple'=>true,'data-actions-box'=>true),array(),'no-mbot','',false);?>
							</div>
							<!--end tags-->
							<!--start other_type select -->
							<div class="col-md-2 border-right form-group">
								<label for="date_by" class="control-label"><span class="control-label"><?php echo _l('si_lf_filter_by_type'); ?></span></label>
								<select name="type" id="type" class="selectpicker no-margin" data-width="100%" >
									<option value=""><?php echo _l('dropdown_non_selected_tex'); ?></option>
									<option value="lost" <?php echo ($type=='lost'?'selected':'')?>><?php echo _l('lead_lost'); ?></option>
									<option value="junk" <?php echo ($type=='junk'?'selected':'')?>><?php echo _l('lead_junk'); ?></option>
									<option value="public" <?php echo ($type=='public'?'selected':'')?>><?php echo _l('lead_public'); ?></option>
									<option value="not_assigned" <?php echo ($type=='not_assigned'?'selected':'')?>><?php echo _l('leads_not_assigned'); ?></option>
								</select>
							</div>
							<!--end other_type select-->
						</div>
						<div class="row">
							<!--start hide_export_columns select -->
							<div class="col-md-2 border-right form-group">
								<label for="hide_columns" class="control-label"><span class="control-label"><?php echo _l('si_lf_hide_export_columns'); ?></span></label>
								<select name="hide_columns[]" id="hide_columns" class="selectpicker no-margin" data-width="100%" multiple>
									<option value=""><?php echo _l('dropdown_non_selected_tex'); ?></option>
									<option value="name" <?php echo (in_array('name',$hide_columns)?'selected':'')?>><?php echo _l('leads_dt_name'); ?></option>
									<option value="company" <?php echo (in_array('company',$hide_columns)?'selected':'')?>><?php echo _l('lead_company'); ?></option>
									<option value="email" <?php echo (in_array('email',$hide_columns)?'selected':'')?>><?php echo _l('leads_dt_email'); ?></option>
									<option value="phonenumber" <?php echo (in_array('phonenumber',$hide_columns)?'selected':'')?>><?php echo _l('leads_dt_phonenumber'); ?></option>
									<option value="country" <?php echo (in_array('country',$hide_columns)?'selected':'')?>><?php echo _l('lead_country'); ?></option>
									
									<?php
									$custom_fields = get_custom_fields('leads', ['show_on_table' => 1,]);
									foreach($custom_fields as $field)
										echo "<option value='$field[slug]' ".(in_array($field['slug'],$hide_columns)?'selected':'').">$field[name]</option>";
									?>
									<option value="status" <?php echo (in_array('status',$hide_columns)?'selected':'')?>><?php echo _l('leads_dt_status'); ?></option>
									<option value="source" <?php echo (in_array('source',$hide_columns)?'selected':'')?>><?php echo _l('lead_add_edit_source'); ?></option>
									<option value="dateadded" <?php echo (in_array('dateadded',$hide_columns)?'selected':'')?>><?php echo _l('si_lf_created_date'); ?></option>
									<option value="lastcontact" <?php echo (in_array('lastcontact',$hide_columns)?'selected':'')?>><?php echo _l('si_lf_last_contacted_date'); ?></option>
									<option value="is_public" <?php echo (in_array('is_public',$hide_columns)?'selected':'')?>><?php echo _l('lead_public'); ?></option>
									<option value="assigned" <?php echo (in_array('assigned',$hide_columns)?'selected':'')?>><?php echo _l('leads_dt_assigned'); ?></option>
									<option value="tags" <?php echo (in_array('tags',$hide_columns)?'selected':'')?>><?php echo _l('tags'); ?></option>
								</select>
							</div>
							<!--end hide_export_columns select-->
							<!--start filter_by select -->
							<div class="col-md-2 border-right form-group">
								<label for="date_by" class="control-label"><span class="control-label"><?php echo _l('si_lf_lead_filter_by_date'); ?></span></label>
								<select name="date_by" id="date_by" class="selectpicker no-margin" data-width="100%" >
									<option value="dateadded"><?php echo _l('si_lf_created_date'); ?></option>
									<option value="lastcontact" <?php echo ($date_by!='' && $date_by=='lastcontact'?'selected':'')?>><?php echo _l('si_lf_last_contacted_date'); ?></option>
								</select>
							</div>
							<!--end filter_by select-->
							<div class="col-md-2 form-group border-right" id="report-time">
								<label for="months-report"><?php echo _l('period_datepicker'); ?></label><br />
								<select class="selectpicker" name="report_months" id="report_months" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
									<option value=""><?php echo _l('report_sales_months_all_time'); ?></option>
									<option value="this_month"><?php echo _l('this_month'); ?></option>
									<option value="1"><?php echo _l('last_month'); ?></option>
									<option value="this_year"><?php echo _l('this_year'); ?></option>
									<option value="last_year"><?php echo _l('last_year'); ?></option>
									<option value="3" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-2 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_three_months'); ?></option>
									<option value="6" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-5 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_six_months'); ?></option>
									<option value="12" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-11 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_twelve_months'); ?></option>
									<option value="custom"><?php echo _l('period_datepicker'); ?></option>
								</select>
								<?php
									if($report_months !== '')
									{
										$report_heading.=' for '._l('period_datepicker')." ";
										switch($report_months)
										{
											case 'this_month':$report_heading.=date('01-m-Y')." To ".date('t-m-Y');break;
											case '1'         :$report_heading.=date('01-m-Y',strtotime('-1 month'))." To ".date('t-m-Y',strtotime('-1 month'));break;
											case 'this_year' :$report_heading.=date('01-01-Y')." To ".date('31-12-Y');break;
											case 'last_year' :$report_heading.=date('01-01-Y',strtotime('-1 year'))." To ".date('31-12-Y',strtotime('-1 year'));break;
											case '3'         :$report_heading.=date('01-m-Y',strtotime('-2 month'))." To ".date('t-m-Y');break;
											case '6'         :$report_heading.=date('01-m-Y',strtotime('-5 month'))." To ".date('t-m-Y');break;
											case '12'        :$report_heading.=date('01-m-Y',strtotime('-11 month'))." To ".date('t-m-Y');break;
											case 'custom'    :$report_heading.=$report_from." To ".$report_to;break;
											default          :$report_heading.='All Time';
										}
									}
								?>
							</div>
							<div id="date-range" class="col-md-4 hide mbot15" id="date_by_wrapper">
								<div class="row">
									<div class="col-md-6">
										<label for="report_from" class="control-label"><?php echo _l('report_sales_from_date'); ?></label>
										<div class="input-group date">
											<input type="text" class="form-control datepicker" id="report_from" name="report_from" value="<?php echo htmlspecialchars($report_from);?>" autocomplete="off">
											<div class="input-group-addon">
												<i class="fa fa-calendar calendar-icon"></i>
											</div>
										</div>
									</div>
									<div class="col-md-6 border-right">
										<label for="report_to" class="control-label"><?php echo _l('report_sales_to_date'); ?></label>
										<div class="input-group date">
											<input type="text" class="form-control datepicker" id="report_to" name="report_to" autocomplete="off">
											<div class="input-group-addon">
												<i class="fa fa-calendar calendar-icon"></i>
											</div>
										</div>
									</div>
								</div>
							</div>
							<!--end date time div-->
							<div class="col-md-12">
								<div class="checklist relative">
									<div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip" title="" data-original-title="<?php echo _l('si_lf_save_filter_template'); ?>">
										<input type="checkbox" id="si_lf_save_filter" name="save_filter" value="1" title="<?php echo _l('si_lf_save_filter_template'); ?>" <?php echo ($this->input->get('filter_id')?'checked':'')?>>
										<label for=""><span class="hide"><?php echo _l('si_lf_save_filter_template'); ?></span></label>
										<textarea id="si_lf_filter_name" name="filter_name" rows="1" placeholder="<?php echo _l('si_lf_filter_template_name'); ?>" <?php echo ($this->input->get('filter_id')?'':'disabled="disabled"')?> maxlength='100'><?php echo ($this->input->get('filter_id')?$saved_filter_name:'');?></textarea>
									</div>
								</div>
							</div>
						</div>
						<?php echo form_close(); ?>
					</div>
				</div>
				<div class="panel_s">
					<div class="panel-body">
					<?php
					foreach($overview as $month =>$data){ if(count($data) == 0){continue;} ?>
						<h4 class="bold text-success"><?php echo htmlspecialchars($month); ?>
						</h4>
						<table class="table tasks-overview dt-table scroll-responsive">
							<caption class="si_lf_caption"><?php echo htmlspecialchars($month.$report_heading);?></caption>
							<thead>
								<tr>
								
									<th>#</th>
									<th class="<?php echo (in_array('name',$hide_columns)?'not-export':'')?>"><?php echo _l('leads_dt_name'); ?></th>
									<th class="<?php echo (in_array('company',$hide_columns)?'not-export':'')?>"><?php echo _l('lead_company'); ?></th>
									<th class="<?php echo (in_array('email',$hide_columns)?'not-export':'')?>"><?php echo _l('leads_dt_email'); ?></th>
									<th class="<?php echo (in_array('phonenumber',$hide_columns)?'not-export':'')?>"><?php echo _l('leads_dt_phonenumber'); ?></th>
									<th class="<?php echo (in_array('country',$hide_columns)?'not-export':'')?>"><?php echo _l('lead_country'); ?></th>
								<?php
									$custom_fields = get_custom_fields('leads', ['show_on_table' => 1,]);
									foreach($custom_fields as $field)
									{
										echo '<th class="'.(in_array($field['slug'],$hide_columns)?'not-export':'').'">'.$field['name'].'</th>';	
									}
								?>
									<th class="<?php echo (in_array('status',$hide_columns)?'not-export':'')?>"><?php echo _l('leads_dt_status'); ?></th>
									<th class="<?php echo (in_array('source',$hide_columns)?'not-export':'')?>"><?php echo _l('lead_add_edit_source'); ?></th>
									<th class="<?php echo (in_array('dateadded',$hide_columns)?'not-export':'')?>"><?php echo _l('si_lf_created_date'); ?></th>
									<th class="<?php echo (in_array('lastcontact',$hide_columns)?'not-export':'')?>"><?php echo _l('si_lf_last_contacted_date'); ?></th>
									<th class="<?php echo (in_array('is_public',$hide_columns)?'not-export':'')?>"><?php echo _l('lead_public'); ?></th>
									<th class="<?php echo (in_array('assigned',$hide_columns)?'not-export':'')?>"><?php echo _l('leads_dt_assigned'); ?></th>
									<th class="<?php echo (in_array('tags',$hide_columns)?'not-export':'')?>"><?php echo _l('tags'); ?></th>
								</tr>
							</thead>
						<tbody>
							<?php
								$no=1;
								foreach($data as $lead){ ?>
								<tr>
								
									<td><?php echo htmlspecialchars($no++);?></td>
									<td data-order="<?php echo htmlspecialchars($lead['name']); ?>"><a href="<?php echo admin_url('leads/index/'.$lead['id']); ?>" onclick="init_lead(<?php echo htmlspecialchars($lead['id']); ?>); return false;"><?php echo htmlspecialchars($lead['name']); ?></a>
									</td>
									<td><?php echo htmlspecialchars($lead['company']); ?></td>
									<td><?php echo htmlspecialchars($lead['email']); ?></td>
									<td><?php echo htmlspecialchars($lead['phonenumber']); ?></td>
									<td><?php echo htmlspecialchars(get_country_name($lead['country'])); ?></td>
								<?php
									foreach($custom_fields as $field)
									{
										$current_value = get_custom_field_value($lead['id'], $field['id'], 'leads', false);
										echo '<td>'.(($field['type']=='date_picker' || $field['type']=='date_picker_time') && $current_value!='' ? date('d-m-Y H:i:s A',strtotime($current_value)):$current_value).'</td>';
									}
								?>
									<td><?php echo si_format_lead_status($lead['status']); ?></td>
									<td><?php echo htmlspecialchars($lead['source_name']); ?></td>
									<td data-order="<?php echo htmlspecialchars($lead['dateadded']); ?>"><?php echo _d($lead['dateadded']); ?></td>
									<td data-order="<?php echo htmlspecialchars($lead['lastcontact']); ?>"><?php echo _d($lead['lastcontact']); ?></td>
									<td data-order="<?php echo htmlspecialchars($lead['is_public']); ?>"><?php echo ($lead['is_public']?_l('lead_is_public_yes'):_l('lead_is_public_no')); ?></td>
									<td>
										<?php if($lead['assigned']>0){?>
										<a data-toggle="tooltip" data-title="<?php echo htmlspecialchars($lead['staff_name']) ?>" href="<?php echo admin_url('profile/' . $lead['assigned']) ?>"><?php echo staff_profile_image($lead['assigned'], 
										['staff-profile-image-small',]) ?></a>
										<?php } ?>
									</td>
									<td><?php echo  render_tags(prep_tags_input(get_tags_in($lead['id'],'lead'))); ?></td>	
								</tr>
								<?php } ?>
							</tbody>
						</table>
						<hr />
					<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>
</body>
</html>
<script src="<?php echo module_dir_url('si_lead_filters','assets/js/si_lead_filters_lead_report.js'); ?>"></script>
<script>
(function($) {
"use strict";
<?php  if($report_months !== ''){ ?>
	$('#report_months').val("<?php echo htmlspecialchars($report_months);?>");
	$('#report_months').change();		
<?php }
	if($report_from !== ''){ 
?>
	$('#report_from').val("<?php echo htmlspecialchars($report_from);?>");
<?php
	}
	if($report_to !== ''){ 
?>
	$('#report_to').val("<?php echo htmlspecialchars($report_to);?>");
<?php
	}
?>
})(jQuery);				  
</script>

