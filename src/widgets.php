<?php
namespace SinticBolivia\MonoInvoicesApi;

use SinticBolivia\MonoInvoicesApi\Classes\MonoInvoicesApi;

if( !function_exists('siat_widget_unidades_medida')):
function siat_widget_unidades_medida(MonoInvoicesApi $api, $selected = '', $name = 'unidad_medida_siat')
{
	$res = $api->unidadesMedida();
	?>
	<div class="mb-3">
		<label>Unindad de Medida SIAT</label>
		<select name="<?php print $name ?>" class="form-control form-select">
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
function siat_widget_actividades(MonoInvoicesApi $api, $selected = '', $name = 'actividad_economica_siat')
{
	$res = $api->actividades();
	?>
	<div class="mb-3">
		<label>Actividad Economica SIAT</label>
		<select id="<?php print $name ?>" name="<?php print $name ?>" class="form-control form-select" data-selected="<?php print $selected ?>">
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
function siat_widget_productos(MonoInvoicesApi $api, $selected = '', $name = 'codigo_producto_siat')
{
	$res = $api->productosServicios();
	?>
	<div class="mb-3">
		<label>Codigo Producto SIAT</label>
		<select id="<?php print $name ?>" name="<?php print $name ?>" class="form-control form-select" data-selected="<?php print $selected ?>">
			<option value="">-- producto sin --</option>
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