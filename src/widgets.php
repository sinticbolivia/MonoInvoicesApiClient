<?php
namespace SinticBolivia\MonoInvoicesApi;

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
		<select id="actividad_economica_siat" name="actividad_economica_siat" class="form-control form-select" data-selected="<?php print $selected ?>">
			<option value="">-- actividad economica --</option>
			<?php foreach($res->data->RespuestaListaActividades->listaActividades as $a): ?>
			<option value="<?php print $a->codigoCaeb ?>" <?php print $selected == $a->codigoCaeb ? 'selected' : '' ?>>
				<?php printf("(%s) %s", $a->codigoCaeb, $a->descripcion) ?>
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
		<select id="codigo_producto_siat" name="codigo_producto_siat" class="form-control form-select" data-selected="<?php print $selected ?>">
			<option value="">-- producto sin --</option>
			<?php /*foreach($res->data->RespuestaListaProductos->listaCodigos as $p): if( $filter_actividad && $filter_actividad != $p->codigoActividad ) continue; ?>
			<option value="<?php print $p->codigoProducto ?>" <?php print $selected == $p->codigoProducto ? 'selected' : '' ?>>
				<?php print $p->descripcionProducto ?>
			</option>
			<?php endforeach;*/ ?>
		</select>
	</div>
	<script>
	(function()
	{
		const productos_sin = <?php print json_encode($res->data->RespuestaListaProductos->listaCodigos); ?>;
		const elActividad = document.querySelector('#actividad_economica_siat');
		const elProductos = document.querySelector('#codigo_producto_siat');
		
		function filterProducts(codigo_actividad)
		{
			let items = productos_sin.filter( prod => prod.codigoActividad == codigo_actividad );
			return items;
		}
		function setProductos(productos)
		{
			elProductos.innerHTML = '';
			productos.forEach( (prod) => 
			{
				const op = document.createElement('option');
				op.innerHTML = `${prod.descripcionProducto}`;
				op.value = prod.codigoProducto;
				op.dataset.actividad = prod.codigoActividad;
				elProductos.appendChild(op);
			});
		}
		elActividad.addEventListener('change', function(e)
		{
			const prods = filterProducts(this.value);
			setProductos(prods);
		});
		if( elActividad.dataset.selected )
		{
			setProductos( filterProducts(elActividad.dataset.selected) );
		}
		if( elProductos.dataset.selected )
			elProductos.value = elProductos.dataset.selected;
	})();
	</script>
	<?php
}
endif;