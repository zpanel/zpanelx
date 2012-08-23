<table class="wm_contacts_view">
	<tr>
		<td class="wm_field_title" colspan="2">
			<h3>Regional Settings</h3>
		</td>
	</tr>
	<tr>
		<td class="wm_field_title">
			<span id="selLanguage_label">
				Language
			</span>
		</td>
		<td class="wm_field_value">
			<select name="selLanguage" id="selLanguage" class="wm_select override">
				 <?php $this->Data->PrintValue('selLanguageOptions'); ?>
			</select>
		</td>
	</tr>
	<tr>
		<td class="wm_field_title">
			<span id="selTimeZone_label">
				Timezone
			</span>
		</td>
		<td class="wm_field_value">
			<select name="selTimeZone" id="selTimeZone" class="wm_select override">
				<?php $this->Data->PrintValue('selTimeZone'); ?>
			</select>
		</td>
	</tr>
	<tr>
		<td class="wm_field_title">
			<span id="radioTimeFormat_label">
				Time format
			</span>
		</td>
		<td class="wm_field_value">
			<input type="radio" class="wm_checkbox override" name="radioTimeFormat" id="radioTimeFormat12"
				value="<?php echo EnumConvert::ToPost(ETimeFormat::F12, 'ETimeFormat'); ?>" <?php
				$this->Data->PrintCheckedValue('radioTimeFormat12'); ?> x-data-label="radioTimeFormat_label" />
			<label id="radioTimeFormat12_label" for="radioTimeFormat12">1PM</label>
			&nbsp;&nbsp;&nbsp;
			<input type="radio" class="wm_checkbox override" name="radioTimeFormat" id="radioTimeFormat24"
				value="<?php echo EnumConvert::ToPost(ETimeFormat::F24, 'ETimeFormat'); ?>" <?php
				$this->Data->PrintCheckedValue('radioTimeFormat24'); ?>  x-data-label="radioTimeFormat_label" />
			<label id="radioTimeFormat24_label" for="radioTimeFormat24">13:00</label>
		</td>
	</tr>
	
</table>