<?php echo get_header(); ?>
<div class="row content">
	<div class="col-md-12">
		<div class="row wrap-vertical">
			<ul id="nav-tabs" class="nav nav-tabs">
				<li class="active"><a href="#general" data-toggle="tab"><?php echo lang('text_tab_general'); ?></a></li>
				<li><a href="#status" data-toggle="tab"><?php echo lang('text_tab_status'); ?></a></li>
				<li><a href="#restaurant" data-toggle="tab"><?php echo lang('text_tab_restaurant'); ?></a></li>
				<?php if ($check_order_type === '1') { ?>
					<li><a href="#delivery-address" data-toggle="tab"><?php echo lang('text_tab_delivery_address'); ?></a></li>
				<?php } ?>
				<li><a href="#payment" data-toggle="tab"><?php echo lang('text_tab_payment'); ?></a></li>
				<li><a href="#menus" data-toggle="tab"><?php echo sprintf(lang('text_tab_menu'), $total_items); ?></span></a></li>
			</ul>
		</div>

		<form role="form" id="edit-form" class="form-horizontal" accept-charset="utf-8" method="POST" action="<?php echo $_action; ?>">
			<div class="tab-content">
				<div id="general" class="tab-pane row wrap-all active">
					<div class="form-group">
						<label for="" class="col-sm-3 control-label"><?php echo lang('label_order_id'); ?></label>
						<div class="col-sm-5">
							#<?php echo $order_id; ?>
						</div>
					</div>
					<div class="form-group">
						<label for="input-name" class="col-sm-3 control-label"><?php echo lang('label_customer_name'); ?></label>
						<div class="col-sm-5">
							<?php if (!empty($customer_id)) { ?>
								<a href="<?php echo $customer_edit; ?>"><?php echo $first_name; ?> <?php echo $last_name; ?></a>
							<?php } else { ?>
								<?php echo $first_name; ?> <?php echo $last_name; ?> <span class="badge">Guest Order</span>
							<?php } ?>
						</div>
					</div>
					<div class="form-group">
						<label for="input-name" class="col-sm-3 control-label"><?php echo lang('label_email'); ?></label>
						<div class="col-sm-5">
							<?php echo $email; ?>
						</div>
					</div>
					<div class="form-group">
						<label for="input-name" class="col-sm-3 control-label"><?php echo lang('label_telephone'); ?></label>
						<div class="col-sm-5">
							<?php echo $telephone; ?>
						</div>
					</div>
					<div class="form-group">
						<label for="input-name" class="col-sm-3 control-label"><?php echo lang('label_order_type'); ?></label>
						<div class="col-sm-5">
							<?php echo $order_type; ?>
						</div>
					</div>
					<div class="form-group">
						<label for="input-name" class="col-sm-3 control-label"><?php echo lang('label_order_time'); ?></label>
						<div class="col-sm-5">
							<?php echo $order_time; ?>
						</div>
					</div>
					<div class="form-group">
						<label for="input-name" class="col-sm-3 control-label"><?php echo lang('label_order_date'); ?></label>
						<div class="col-sm-5">
							<?php echo $date_added; ?>
						</div>
					</div>
					<div class="form-group">
						<label for="input-name" class="col-sm-3 control-label"><?php echo lang('label_order_total'); ?></label>
						<div class="col-sm-5">
							<?php echo $order_total; ?>
						</div>
					</div>
					<div class="form-group">
						<label for="input-name" class="col-sm-3 control-label"><?php echo lang('label_comment'); ?></label>
						<div class="col-sm-5">
							<?php echo $comment; ?>
						</div>
					</div>
					<div class="form-group">
						<label for="input-name" class="col-sm-3 control-label"><?php echo lang('label_date_modified'); ?></label>
						<div class="col-sm-5">
							<?php echo $date_modified; ?>
						</div>
					</div>
					<div class="form-group">
						<label for="input-name" class="col-sm-3 control-label"><?php echo lang('label_notify'); ?></label>
						<div class="col-sm-5">
							<?php if ($notify === '1') { ?>
								<?php echo lang('text_email_sent'); ?>
							<?php } else { ?>
								<?php echo lang('text_email_not_sent'); ?>
							<?php } ?>
						</div>
					</div>
					<div class="form-group">
						<label for="input-name" class="col-sm-3 control-label"><?php echo lang('label_ip_address'); ?></label>
						<div class="col-sm-5">
							<?php echo $ip_address; ?>
						</div>
					</div>
					<div class="form-group">
						<label for="input-name" class="col-sm-3 control-label"><?php echo lang('label_user_agent'); ?></label>
						<div class="col-sm-5">
							<?php echo $user_agent; ?>
						</div>
					</div>
				</div>

				<div id="status" class="tab-pane row wrap-all">
					<div class="form-group">
						<label for="input-assign-staff" class="col-sm-3 control-label"><?php echo lang('label_assign_staff'); ?></label>
						<div class="col-sm-5">
                            <input type="hidden" name="old_assignee_id" value="<?php echo $assignee_id; ?>" />
                            <input type="hidden" name="old_status_id" value="<?php echo $status_id; ?>" />
							<select name="assignee_id" class="form-control">
								<option value=""><?php echo lang('text_please_select'); ?></option>
								<?php foreach ($staffs as $staff) { ?>
									<?php if ($staff['staff_id'] === $assignee_id) { ?>
										<option value="<?php echo $staff['staff_id']; ?>" <?php echo set_select('assignee_id', $staff['staff_id'], TRUE); ?> ><?php echo $staff['staff_name']; ?></option>
									<?php } else { ?>
										<option value="<?php echo $staff['staff_id']; ?>" <?php echo set_select('assignee_id', $staff['staff_id']); ?> ><?php echo $staff['staff_name']; ?></option>
									<?php } ?>
								<?php } ?>
							</select>
							<?php echo form_error('assignee_id', '<span class="text-danger">', '</span>'); ?>
						</div>
					</div>
					<div class="form-group">
						<label for="input-name" class="col-sm-3 control-label"><?php echo lang('label_status'); ?></label>
						<div class="col-sm-5">
							<select name="order_status" id="" class="form-control" onChange="getStatusComment();">
							<?php foreach ($statuses as $status) { ?>
							<?php if ($status['status_id'] === $status_id) { ?>
								<option value="<?php echo $status['status_id']; ?>" <?php echo set_select('order_status', $status['status_id'], TRUE); ?> ><?php echo $status['status_name']; ?></option>
							<?php } else { ?>
								<option value="<?php echo $status['status_id']; ?>" <?php echo set_select('order_status', $status['status_id']); ?> ><?php echo $status['status_name']; ?></option>
							<?php } ?>
							<?php } ?>
							</select>
							<?php echo form_error('order_status', '<span class="text-danger">', '</span>'); ?>
						</div>
					</div>
					<div class="form-group">
						<label for="input-name" class="col-sm-3 control-label"><?php echo lang('label_comment'); ?></label>
						<div class="col-sm-5">
							<textarea name="status_comment" id="" class="form-control" rows="3"><?php echo set_value('status_comment'); ?></textarea>
							<?php echo form_error('status_comment', '<span class="text-danger">', '</span>'); ?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo lang('label_notify'); ?></label>
						<div class="col-sm-5">
							<div id="input-notify" class="btn-group btn-group-toggle" data-toggle="buttons">
								<?php if ($notify == '1') { ?>
									<label class="btn btn-default" data-btn="btn-danger"><input type="radio" name="notify" value="0" <?php echo set_radio('notify', '0'); ?>><?php echo lang('text_no'); ?></label>
									<label class="btn btn-default active" data-btn="btn-success"><input type="radio" name="notify" value="1" <?php echo set_radio('notify', '1', TRUE); ?>><?php echo lang('text_yes'); ?></label>
								<?php } else { ?>
									<label class="btn btn-default active" data-btn="btn-danger"><input type="radio" name="notify" value="0" <?php echo set_radio('notify', '0', TRUE); ?>><?php echo lang('text_no'); ?></label>
									<label class="btn btn-default" data-btn="btn-success"><input type="radio" name="notify" value="1" <?php echo set_radio('notify', '1'); ?>><?php echo lang('text_yes'); ?></label>
								<?php } ?>
							</div>
							<?php echo form_error('notify', '<span class="text-danger">', '</span>'); ?>
						</div>
					</div>
					<br />

					<h4 class="block-heading">History</h4>
					<div class="panel panel-default panel-table">
						<div class="table-responsive">
							<table height="auto" class="table table-striped table-border" id="history">
								<thead>
									<tr>
										<th><?php echo lang('column_time_date'); ?></th>
										<th><?php echo lang('column_status'); ?></th>
										<th><?php echo lang('column_staff'); ?></th>
										<th><?php echo lang('column_assignee'); ?></th>
                                        <th class="left" width="35%"><?php echo lang('column_comment'); ?></th>
                                        <th class="text-center"><?php echo lang('column_notify'); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php if ($status_history) { ?>
									<?php foreach ($status_history as $history) { ?>
									<tr>
										<td><?php echo $history['date_time']; ?></td>
										<td><?php echo $history['status_name']; ?></td>
										<td><?php echo $history['staff_name']; ?></td>
										<td>
											<?php foreach ($staffs as $staff) { ?>
												<?php if ($staff['staff_id'] === $history['assignee_id']) { ?>
													<?php echo $staff['staff_name']; ?>
												<?php } ?>
											<?php } ?>
										</td>
                                        <td class="text-left"><?php echo $history['comment']; ?></td>
                                        <td class="text-center"><?php echo ($history['notify'] === '1') ? $this->lang->line('text_yes') : $this->lang->line('text_no'); ?></td>
									</tr>
									<?php } ?>
									<?php } else { ?>
									<tr>
										<td colspan="5"><?php echo lang('text_no_status_history'); ?></td>
									</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>

				<div id="restaurant" class="tab-pane row wrap-all">
					<div class="form-group">
						<label for="input-name" class="col-sm-3 control-label"><?php echo lang('label_restaurant_name'); ?></label>
						<div class="col-sm-5">
							<?php echo $location_name; ?>
						</div>
					</div>
					<div class="form-group">
						<label for="input-name" class="col-sm-3 control-label"><?php echo lang('label_restaurant_address'); ?></label>
						<div class="col-sm-5">
							<address><?php echo $location_address; ?></address>
						</div>
					</div>
				</div>

				<?php if ($check_order_type === '1') { ?>
				<div id="delivery-address" class="tab-pane row wrap-all">
					<div class="form-group">
						<label for="input-name" class="col-sm-3 control-label"><?php echo lang('label_restaurant_address'); ?></label>
						<div class="col-sm-5">
							<address><?php echo $customer_address; ?></address>
						</div>
					</div>
				</div>
				<?php } ?>

				<div id="payment" class="tab-pane row wrap-all">
					<div class="form-group">
						<label for="input-name" class="col-sm-3 control-label"><?php echo lang('label_payment_method'); ?></label>
						<div class="col-sm-5">
							<?php echo $payment; ?>
							<?php if ($paypal_details) { ?>
								<a class="view_details"><?php echo lang('text_transaction_detail'); ?></a><br />
							<?php } ?>
						</div>
					</div>
					<div class="paypal_details" style="display:none">
						<ul>
						<?php foreach ($paypal_details as $key => $value) { ?>
							<li>
								<span><?php echo $key; ?></span> <?php echo $value; ?>
							</li>
						<?php } ?>
						</ul>
					</div>
				</div>

				<div id="menus" class="tab-pane row wrap-all">
					<div class="panel panel-default panel-table">
						<div class="table-responsive">
							<table height="auto" class="table table-striped table-border">
								<thead>
									<tr>
										<th></th>
										<th width="25%"><?php echo lang('column_name_option'); ?></th>
										<th class="text-center"><?php echo lang('column_price'); ?></th>
										<th><?php echo lang('column_total'); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($cart_items as $cart_item) { ?>
									<tr id="<?php echo $cart_item['id']; ?>">
										<td><?php echo $cart_item['qty']; ?>x</td>
										<td><?php echo $cart_item['name']; ?><br />
										<?php if (!empty($cart_item['options'])) { ?>
											<div><font size="1">+ <?php echo $cart_item['options']; ?></font></div>
										<?php } ?>
										</td>
										<td class="text-center"><?php echo $cart_item['price']; ?></td>
										<td><?php echo $cart_item['subtotal']; ?></td>
									</tr>
									<?php } ?>
									<?php foreach ($totals as $total) { ?>
									<tr>
										<td width="1"></td>
										<td></td>
										<td class="text-center"><b><?php echo $total['title']; ?></b></td>
										<td><b><?php echo $total['value']; ?></b></td>
									</tr>
									<?php } ?>
									<tr>
										<td width="1"></td>
										<td></td>
										<td class="text-center"><b><?php echo lang('text_total'); ?></b></td>
										<td><b><?php echo $order_total; ?></b></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
  	$('.view_details').on('click', function(){
  		if($('.paypal_details').is(':visible')){
     		$('.paypal_details').fadeOut();
   			$('.view_details').attr('class', '');
		} else {
   			$('.paypal_details').fadeIn();
   			$('.view_details').attr('class', 'active');
		}
	});
});
</script>
<script type="text/javascript"><!--
function getStatusComment() {
	if ($('select[name="order_status"]').val()) {
		$.ajax({
			url: js_site_url('statuses/comment?status_id=') + encodeURIComponent($('select[name="order_status"]').val()),
			dataType: 'json',
			success: function(json) {
				$('textarea[name="status_comment"]').html(json);
			}
		});
	}
};

$('select[name="order_status"]').trigger('change');
//--></script>
<?php echo get_footer(); ?>