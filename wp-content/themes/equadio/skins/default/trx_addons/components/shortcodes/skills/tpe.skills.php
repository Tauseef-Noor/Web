<?php
/**
 * Template to represent shortcode as a widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and using to generate the live preview in the Elementor's Editor
 *
 * @package ThemeREX Addons
 * @since v1.6.41
 */

extract(get_query_var('trx_addons_args_sc_skills'));
?><#
var id = settings._element_id ? settings._element_id + '_sc' : 'sc_skills_'+(''+Math.random()).replace('.', ''),
    svg_bg_dir = '<?php echo equadio_get_svg_from_file(equadio_get_file_dir('images/brushstroke.svg')); ?>',
	column_class = "<?php echo esc_attr(trx_addons_get_column_class(1, '##')); ?>",
	columns = 0,
	legend = '',
	data = '',
	max = 0,
	compact = 0,
	cutout = 0,
	matches = '',
	reg = /([+\-\.0-9]+)(.*)/;

for (var i in settings.values) {
	matches = ('' + settings.values[i].value).match(reg);
	if (matches && matches.length) {
		settings.values[i].value = parseFloat(matches[1]);
		settings.values[i].units = matches[2];
	} else {
		if ( settings.values[i].value == '' ) {
			settings.values[i].value = '0';
		} else {
			settings.values[i].value = parseFloat(('' + settings.values[i].value).replace('%', ''));
		}
		settings.values[i].units = '';
	}
	if (max < settings.values[i].value) max = settings.values[i].value;
}

matches = ('' + max).match(reg);
if (matches && matches.length) {
	max = matches[1];
} else {
	max = ('' + max).replace('%', '');
}

columns = settings.compact == 0 
			? (settings.columns.size < 1 
				? settings.values.length
				: Math.min(settings.columns.size, settings.values.length)
				)
			: 1;
if (settings.columns_tablet.size > 0 && settings.compact == 0) settings.columns_tablet.size = Math.max(1, Math.min(settings.values.length, settings.columns_tablet.size));
if (settings.columns_mobile.size > 0 && settings.compact == 0) settings.columns_mobile.size = Math.max(1, Math.min(settings.values.length, settings.columns_mobile.size));

cutout = Math.min(100, Math.max(0, settings.cutout.size));
compact = settings.compact < 1 ? 0 : 1;

_.each(settings.values, function(item) {
	var icon = trx_addons_get_settings_icon( item.icon ), img = '', svg = '';
	if (typeof item.icon_type == 'undefined') item.icon_type = '';
	if (icon != '') {
		if (icon.indexOf('//') >= 0) {
			if (icon.indexOf('.svg') >= 0) {
				svg = icon;
				item.icon_type = 'svg';
			} else {
				img = icon;
				item.icon_type = 'images';
			}
			icon = trx_addons_get_basename(icon);
		}
	}
	var ed = item.units,	//(''+item.value).substr(-1)=='%' ? '%' : '',
		value = item.value,	//(''+item.value).replace('%', ''),
		percent = Math.round(value / max * 100),
		start = 0,
		stop = value,
		steps = 100,
		step = Math.max(1, max / steps),
		speed = Math.round(10 + Math.random()* 30),
		animation = Math.round((stop - start) / step * speed),
		item_color = item.color != '' 
						? item.color 
						: (settings.color!='' 
							? settings.color 
							: (settings.type == 'pie' 
								? '<?php echo apply_filters('trx_addons_filter_get_theme_accent_color', '#efa758'); ?>' 
								: ''
								)
							),
		bg_color = settings.bg_color != '' 
						? settings.bg_color 
						: '#f7f7f7',
		border_color = settings.border_color != '' 
						? settings.border_color 
						: '';


	
	if (settings.type == 'pie') {

		if (compact == 1) {
			legend += '<div class="sc_skills_legend_item">'
							+ '<span class="sc_skills_legend_marker" style="background-color:' + item_color + '"></span>'
							+ '<span class="sc_skills_legend_title">' + item.title + '</span>'
							+ '<span class="sc_skills_legend_value">' + item.value + '</span>'
						+ '</div>';
			data += '<div class="pie"'
						+ ' data-start="' + start + '"'
						+ ' data-stop="' + stop + '"'
						+ ' data-step="' + step + '"'
						+ ' data-steps="' + steps + '"'
						+ ' data-max="' + max + '"'
						+ ' data-speed="' + speed + '"'
						+ ' data-duration="' + animation + '"'
						+ ' data-color="' + item_color + '"'
						+ ' data-bg_color="' + bg_color + '"'
						+ ' data-border_color="' + border_color + '"'
						+ ' data-cutout="' + cutout + '"'
						+ ' data-easing="easeOutCirc"'
						+ ' data-ed="' + ed + '"'
				+ '>'
					+ '<input type="hidden" class="text" value="' + item.title + '" />'
					+ '<input type="hidden" class="percent" value="' + percent + '" />'
					+ '<input type="hidden" class="color" value="' + item_color + '" />'
				+ '</div>';

		} else {
		
			var item_id = 'sc_skills_canvas_' + (''+Math.random()).replace('.','');
			data += (columns > 0
						? '<div class="sc_skills_column '
								+ column_class.replace('##', columns)
								+ (settings.columns_tablet.size > 0 ? ' ' + column_class.replace('##', settings.columns_tablet.size) + '-tablet' : '')
								+ (settings.columns_mobile.size > 0 ? ' ' + column_class.replace('##', settings.columns_mobile.size) + '-mobile' : '')
							+ '">'
						: ''
						)
					+ '<div class="sc_skills_item_wrap">'
						+ '<div class="sc_skills_item">'
							+ '<canvas id="' + item_id + '"></canvas>'
							+ '<div class="sc_skills_total"'
								+ ' data-start="' + start + '"'
								+ ' data-stop="' + stop + '"'
								+ ' data-step="' + step + '"'
								+ ' data-steps="' + steps + '"'
								+ ' data-max="' + max + '"'
								+ ' data-speed="' + speed + '"'
								+ ' data-duration="' + animation + '"'
								+ ' data-color="' + item_color + '"'
								+ ' data-bg_color="' + bg_color + '"'
								+ ' data-border_color="' + border_color + '"'
								+ ' data-cutout="' + cutout + '"'
								+ ' data-easing="easeOutCirc"'
								+ ' data-ed="' + ed + '">'
								+ start + ed
							+ '</div>'
						+ '</div>'
						+ (item.title != '' 
								? '<div class="sc_skills_item_title">'
										+ (icon != ''
											? '<span class="sc_skills_icon sc_icon_type_' + item.icon_type + ' ' + icon + '">'
												+ (svg != ''
													? '<object type="image/svg+xml" data="' + svg + '" border="0"></object>'
													: '')
												+ (img != ''
													? '<img class="sc_icon_as_image" src="' + img + '" alt="<?php esc_attr_e('Icon', 'equadio'); ?>">'
													: '')
												+ '</span>'
											: '') 
										+ item.title.replace(/\|/g, '\n').replace(/\n/g, '<br>')
									+ '</div>' 
								: '')
                        + (item.description != '' ? '<div class="sc_skills_item_description">' + item.description.replace(/\|/g, '\n').replace(/\n/g, '<br>') + '</div>' : '')
					+ '</div>'
				+ (columns > 0 ? '</div>' : '');
		}

	} else {

		data += (columns > 0
					? '<div class="sc_skills_column '
							+ column_class.replace('##', columns)
							+ (settings.columns_tablet.size > 0 ? ' ' + column_class.replace('##', settings.columns_tablet.size) + '-tablet' : '')
							+ (settings.columns_mobile.size > 0 ? ' ' + column_class.replace('##', settings.columns_mobile.size) + '-mobile' : '')
						+ '">'
					: '')
				+ '<div class="sc_skills_item_wrap">'
					+ '<div class="sc_skills_item">'
						+ (icon != ''
							? '<span class="sc_skills_icon sc_icon_type_' + item.icon_type + ' ' + icon + '">'
								+ (svg != ''
									? '<object type="image/svg+xml" data="' + svg + '" border="0"></object>'
									: '')
								+ (img != ''
									? '<img class="sc_icon_as_image" src="' + img + '" alt="<?php esc_attr_e('Icon', 'equadio'); ?>">'
									: '')
								+ '</span>'
							: '')
                        + '<div class="sc_skills_total_wrap">'
                            + '<div class="sc_skills_total"'
                                + ' data-start="' + start + '"'
                                + ' data-stop="' + stop + '"'
                                + ' data-step="' + step + '"'
                                + ' data-max="' + max + '"'
                                + ' data-speed="' + speed + '"'
                                + ' data-duration="' + animation + '"'
                                + ' data-ed="' + ed + '"'
                                + (item_color != '' ? ' style="color: ' + item_color + ';"' : '')
                                + '>'
                                + start + ed
                            + '</div>'
                            + '<div class="sc_skills_total_bg">'
                                + svg_bg_dir
                            + '</div>'
                        + '</div>'
					+ '</div>'
					+ (item.title != '' ? '<div class="sc_skills_item_title">' + item.title.replace(/\|/g, '\n').replace(/\n/g, '<br>') + '</div>' : '')
					+ (item.description != '' ? '<div class="sc_skills_item_description">' + item.description.replace(/\|/g, '\n').replace(/\n/g, '<br>') + '</div>' : '')
				+ '</div>'
			+ (columns > 0 ? '</div>' : '');
	}
});


if (settings.type == 'pie') {
	#><div id="{{ id }}"
		class="<# print( trx_addons_apply_filters('trx_addons_filter_sc_classes', 'sc_skills sc_skills_pie sc_skills_compact_' + (compact > 0 ? 'on' : 'off'), settings ) ); #>"
		data-type="pie"><#
} else {
	#><div id="{{ id }}"
		class="<# print( trx_addons_apply_filters('trx_addons_filter_sc_classes', 'sc_skills sc_skills_counter', settings ) ); #>"
		data-type="counter"><#
}

	#><?php $element->sc_show_titles('sc_skills'); ?><#

	if (columns > 1) {
		#><div class="sc_skills_columns sc_item_columns
			<?php echo esc_attr(trx_addons_get_columns_wrap_class()); ?>
			columns_padding_bottom<#
			if (columns >= settings.values.length ) {
				#> columns_in_single_row<#
			}
		#>"><#
	}
	if (settings.type == 'pie' && compact == 1) {
		#><div class="sc_item_content sc_skills_content">
			<div class="sc_skills_legend">{{{ legend }}}</div>
			<div id="{{ id }}_pie_item" class="sc_skills_item">
				<canvas id="{{ id }}_pie" class="sc_skills_pie_canvas"></canvas>
				<div class="sc_skills_data" style="display:none;">{{{ data }}}</div>
			</div>
		</div><#
	} else {
		print(data);
	}

	if (columns > 1) {
		#></div><#
	}

	#><?php $element->sc_show_links('sc_skills'); ?>

</div>