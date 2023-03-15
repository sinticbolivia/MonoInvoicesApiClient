<?php
use SinticBolivia\MonoInvoicesApi\Classes\MonoInvoicesApi;

if( !function_exists('siat_widget_unidades_medida')):
function siat_widget_unidades_medida(MonoInvoicesApi $api, $selected = '')
{
	$res = $api->unidadesMedida();
	?>
	<div class="mb-3">
		<label>Unindad de Medida SIAT</label>
		<select name="unidad_medida_siat" class="form-control form-select">
			<option value="">-- unidad medida --</option>
			<?php foreach($res->data->RespuestaListaParametricas->listaCodigos as $um): ?>
			<option value="<?php print $um->codigoClasificador ?>" <?php print $selected == $um->codigoClasificador ? 'selected' : '' ?>>
				<?php print $um->descripcion ?>
			</option>
	    	<?php endforeach; ?>
		</select>
	</div>
	<?php
}
endif;
if( !function_exists('siat_widget_actividades')):
function siat_widget_actividades(MonoInvoicesApi $api, $selected = '')
{
	$res = $api->actividades();
	?>
	<div class="mb-3">
		<label>Actividad Economica SIAT</label>
		<select id="actividad_economica_siat" name="actividad_economica_siat" class="form-control form-select">
			<option value="">-- actividad economica --</option>
			<?php foreach($res->data->RespuestaListaActividades->listaActividades as $a): ?>
			<option value="<?php print $a->codigoCaeb ?>" <?php print $selected == $a->codigoCaeb ? 'selected' : '' ?>>
				<?php print $a->descripcion ?>
			</option>
			<?php endforeach; ?>
		</select>
	</div>
	<?php
}
endif;
if( !function_exists('siat_widget_productos')):
function siat_widget_productos(MonoInvoicesApi $api, $selected = '', $filter_actividad = null)
{
	$res = $api->productosServicios();
	?>
	<div class="mb-3">
		<label>Codigo Producto SIAT</label>
		<select id="codigo_producto_siat" name="codigo_producto_siat" class="form-control form-select">
			<option value="">-- producto sin --</option>
			<?php foreach($res->data->RespuestaListaProductos->listaCodigos as $p): if( $filter_actividad && $filter_actividad != $p->codigoActividad ) continue; ?>
			<option value="<?php print $p->codigoProducto ?>" <?php print $selected == $p->codigoProducto ? 'selected' : '' ?>>
				<?php print $p->descripcionProducto ?>
			</option>
			<?php endforeach; ?>
		</select>
	</div>
	<script>
	(function()
	{
		
	})();
	</script>
	<?php
}
endif;