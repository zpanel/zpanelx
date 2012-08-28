<table class="wm_contacts_view">
	<tr>
		<td class="wm_field_title" colspan="2">
			<input type="checkbox" class="wm_checkbox override" name="chEnableAddressBook" id="chEnableAddressBook"
				   value="1" <?php $this->Data->PrintCheckedValue('chEnableAddressBook'); ?> />
			<label id="chEnableAddressBook_label" for="chEnableAddressBook">Enable address book</label>
		</td>
	</tr>
	<tr>
		<td class="wm_field_title">
			<span id="selContactsPerPage_label">
				Contacts per page
			</span>
		</td>
		<td class="wm_field_value">
			<select name="selContactsPerPage" class="wm_select override" id="selContactsPerPage">
				<?php $this->Data->PrintValue('selContactsPerPageOptions'); ?>
			</select>
		</td>
	</tr>
</table>