<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
include('../conexion.php');

/* =========================================================
   AUTO-MIGRACIÓN: crea o actualiza la tabla insumos
   usando el nuevo schema por lote
   ========================================================= */
mysqli_query($conexion,"CREATE TABLE IF NOT EXISTS `insumos` (
  `id`                   INT(11) NOT NULL AUTO_INCREMENT,
  `nombre`               VARCHAR(200) NOT NULL,
  `tipo`                 ENUM('unidad','volumen','peso') NOT NULL DEFAULT 'unidad',
  `precio_total_compra`  DECIMAL(10,2) NOT NULL DEFAULT 0,
  `cantidad_lote`        DECIMAL(10,2) NOT NULL DEFAULT 1 COMMENT 'Cuántas unidades hay en el pack',
  `contenido_por_unidad` DECIMAL(10,4) NOT NULL DEFAULT 1 COMMENT 'Contenido de cada unidad (ml, g o 1 para unidad)',
  `proveedor`            VARCHAR(200) DEFAULT NULL,
  `fecha_compra`         DATE DEFAULT NULL,
  `created_at`           TIMESTAMP NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

/* Si existe la tabla vieja 'suministros' y el usuario aún la tiene, migramos datos */
$chk = mysqli_query($conexion,"SHOW TABLES LIKE 'suministros'");
if (mysqli_num_rows($chk) > 0) {
    $old = mysqli_query($conexion,"SELECT * FROM suministros");
    while ($row = mysqli_fetch_assoc($old)) {
        $n  = mysqli_real_escape_string($conexion, $row['nombre']);
        $tm = $row['tipo_medida'];
        $tipo_nuevo = ($tm === 'ml') ? 'volumen' : (($tm === 'gr') ? 'peso' : 'unidad');
        $qt = (float)($row['cantidad_total'] ?? 1);
        $pt = (float)($row['precio_total']   ?? 0);
        $pv = mysqli_real_escape_string($conexion, $row['proveedor'] ?? '');
        $fc = !empty($row['fecha_compra']) ?"'" . $row['fecha_compra'] ."'" : 'NULL';
        // Verificamos que no exista ya migrado
        $ex = mysqli_query($conexion,"SELECT id FROM insumos WHERE nombre='$n' LIMIT 1");
        if (mysqli_num_rows($ex) === 0) {
            mysqli_query($conexion,"INSERT INTO insumos
              (nombre, tipo, precio_total_compra, cantidad_lote, contenido_por_unidad, proveedor, fecha_compra)
              VALUES ('$n','$tipo_nuevo',$pt,$qt,1,'$pv',$fc)");
        }
    }
    // Renombrar tabla vieja para no volver a migrar
    mysqli_query($conexion,"RENAME TABLE suministros TO suministros_old");
}

/* =========================================================
   ACCIONES PHP
   ========================================================= */
$msj = '';

// ALTA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'alta') {
    $nombre  = mysqli_real_escape_string($conexion, trim($_POST['nombre']));
    $tipo    = in_array($_POST['tipo'] ?? '', ['unidad','volumen','peso']) ? $_POST['tipo'] : 'unidad';
    $precio  = (float)($_POST['precio_total_compra'] ?? 0);
    $lote    = (float)($_POST['cantidad_lote']       ?? 1);
    $cont    = (float)($_POST['contenido_por_unidad'] ?? 1);
    $prov    = mysqli_real_escape_string($conexion, trim($_POST['proveedor']    ?? ''));
    $fecha   = mysqli_real_escape_string($conexion, trim($_POST['fecha_compra'] ?? ''));
    $fecha_sql = !empty($fecha) ?"'$fecha'" : 'NULL';
    if (!empty($nombre) && $precio > 0 && $lote > 0 && $cont > 0) {
        mysqli_query($conexion,"INSERT INTO insumos
          (nombre, tipo, precio_total_compra, cantidad_lote, contenido_por_unidad, proveedor, fecha_compra)
          VALUES ('$nombre','$tipo',$precio,$lote,$cont,'$prov',$fecha_sql)");
        $msj = 'ok';
    } else {
        $msj = 'error';
    }
}

// EDITAR
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'editar') {
    $id     = (int)($_POST['id'] ?? 0);
    $nombre = mysqli_real_escape_string($conexion, trim($_POST['nombre']));
    $tipo   = in_array($_POST['tipo'] ?? '', ['unidad','volumen','peso']) ? $_POST['tipo'] : 'unidad';
    $precio = (float)($_POST['precio_total_compra'] ?? 0);
    $lote   = (float)($_POST['cantidad_lote']       ?? 1);
    $cont   = (float)($_POST['contenido_por_unidad'] ?? 1);
    $prov   = mysqli_real_escape_string($conexion, trim($_POST['proveedor']    ?? ''));
    $fecha  = mysqli_real_escape_string($conexion, trim($_POST['fecha_compra'] ?? ''));
    $fecha_sql = !empty($fecha) ?"'$fecha'" : 'NULL';
    if ($id > 0 && !empty($nombre) && $precio > 0 && $lote > 0 && $cont > 0) {
        mysqli_query($conexion,"UPDATE insumos SET
          nombre='$nombre', tipo='$tipo',
          precio_total_compra=$precio, cantidad_lote=$lote, contenido_por_unidad=$cont,
          proveedor='$prov', fecha_compra=$fecha_sql
          WHERE id=$id");
        $msj = 'edit_ok';
    } else {
        $msj = 'error';
    }
}

// ELIMINAR
if (isset($_GET['del']) && is_numeric($_GET['del'])) {
    $del_id = (int)$_GET['del'];
    mysqli_query($conexion,"DELETE FROM insumos WHERE id=$del_id");
    header('Location: suministros.php');
    exit();
}

$insumos_q = mysqli_query($conexion, 'SELECT * FROM insumos ORDER BY created_at DESC');
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Suministros - AIMA Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../public/styles.css">

</head>
<body>

<!-- HEADER -->
<div class="dash-header">
  <a href="../mostrar_contenido.php" ><i class="fa fa-chevron-left me-1"></i>Dashboard</a>
  <h1><i class="fa fa-boxes-stacked me-2"></i>Gestión de Suministros</h1>
  <a href="calculadora.php" ><i class="fa fa-calculator me-1"></i>Calculadora</a>
</div>

<div class="page-body">

<?php if ($msj === 'ok' || $msj === 'edit_ok'): ?>
<div class="alrt-ok"><i class="fa fa-check-circle me-2"></i><?php echo $msj === 'ok' ? 'Suministro registrado con éxito.' : 'Suministro actualizado con éxito.'; ?></div>
<?php elseif ($msj === 'error'): ?>
<div class="alrt-er"><i class="fa fa-triangle-exclamation me-2"></i>Completá todos los campos obligatorios con valores válidos.</div>
<?php endif; ?>

<div class="row g-3">

  <!-- ===== FORMULARIO ALTA ===== -->
  <div class="col-lg-4">
    <div class="panel">
      <div class="panel-title"><i class="fa fa-plus"></i>Nuevo Suministro</div>
      <form method="POST" id="frmAlta">
        <input type="hidden" name="accion" value="alta">

        <div class="mb-2">
          <label class="fl">Nombre del insumo *</label>
          <input type="text" name="nombre" id="aNombre" class="fc form-control" placeholder="Ej: Cera de soja, Fragancia vainilla..." required>
        </div>

        <div class="mb-2">
          <label class="fl">Tipo *</label>
          <select name="tipo" id="aTipo" class="fc form-select" required>
            <option value="unidad">📦 Unidad (envases, etiquetas, cajas…)</option>
            <option value="volumen">🧪 Volumen (fragancias, aceites en ml)</option>
            <option value="peso">⚖️ Peso (cera, aditivos en g)</option>
          </select>
        </div>

        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="fl">Precio total del lote $ *</label>
            <input type="number" name="precio_total_compra" id="aPrecio" class="fc form-control" step="0.01" min="0.01" placeholder="12000" required>
          </div>
          <div class="col-6">
            <label class="fl">Cant. en el pack *</label>
            <input type="number" name="cantidad_lote" id="aLote" class="fc form-control" step="0.01" min="0.01" placeholder="24" required>
          </div>
        </div>

        <div class="mb-2">
          <label class="fl" id="aContLabel">Contenido por unidad *</label>
          <input type="number" name="contenido_por_unidad" id="aCont" class="fc form-control" step="0.0001" min="0.0001" placeholder="10" required>
          <small id="aContHint" >Ej: 10 ml por frasco</small>
        </div>

        <!-- PREVIEW EN TIEMPO REAL -->
        <div class="preview-box" id="aPreview">
          <div class="preview-row">
            <span class="preview-label">Capacidad total del lote</span>
            <span class="preview-val" id="aPrevTotal">—</span>
          </div>
          <div class="preview-row">
            <span class="preview-label">Costo por unidad base</span>
            <span class="preview-val" id="aPrevUnit">—</span>
          </div>
        </div>

        <div class="row g-2 mb-3 mt-2">
          <div class="col-6">
            <label class="fl">Proveedor</label>
            <input type="text" name="proveedor" class="fc form-control" placeholder="Nombre...">
          </div>
          <div class="col-6">
            <label class="fl">Fecha de compra</label>
            <input type="date" name="fecha_compra" class="fc form-control">
          </div>
        </div>

        <button type="submit" class="btn-main w-100 justify-content-center">
          <i class="fa fa-plus"></i>Registrar Suministro
        </button>
      </form>
    </div>
  </div>

  <!-- ===== TABLA DE INSUMOS ===== -->
  <div class="col-lg-8">
    <div class="panel">
      <div class="panel-title"><i class="fa fa-list"></i>Stock de Insumos
        <span >
          <?php echo mysqli_num_rows($insumos_q); ?> registrados
        </span>
      </div>

      <?php if (mysqli_num_rows($insumos_q) === 0): ?>
        <p class="text-center py-4"><i class="fa fa-box-open fa-2x d-block mb-2"></i>Sin suministros registrados aún.</p>
      <?php else: ?>
      <div class="tbl-wrap">
        <table class="tbl">
          <thead>
            <tr>
              <th>Insumo</th>
              <th>Tipo</th>
              <th>Pack</th>
              <th>Cont/u</th>
              <th>Precio total</th>
              <th>Costo/base</th>
              <th>Proveedor</th>
              <th>Fecha</th>
              <th ></th>
            </tr>
          </thead>
          <tbody>
          <?php
          mysqli_data_seek($insumos_q, 0);
          while ($s = mysqli_fetch_assoc($insumos_q)):
            $total_medida = $s['cantidad_lote'] * $s['contenido_por_unidad'];
            $costo_base   = $total_medida > 0 ? $s['precio_total_compra'] / $total_medida : 0;
            $unidad_label = $s['tipo'] === 'volumen' ? 'ml' : ($s['tipo'] === 'peso' ? 'g' : 'u');
            $badge_class  = 'badge-' . $s['tipo'];
          ?>
            <tr>
              <td><strong ><?php echo htmlspecialchars($s['nombre']); ?></strong></td>
              <td><span class="badge-tipo <?php echo $badge_class; ?>"><?php echo $s['tipo']; ?></span></td>
              <td ><?php echo number_format($s['cantidad_lote'], 0, ',', '.'); ?> u</td>
              <td ><?php echo number_format($s['contenido_por_unidad'], 2, ',', '.'); ?> <?php echo $unidad_label; ?></td>
              <td >$<?php echo number_format($s['precio_total_compra'], 0, ',', '.'); ?></td>
              <td class="costo-cell">$<?php echo number_format($costo_base, 4, ',', '.'); ?>/<?php echo $unidad_label; ?></td>
              <td ><?php echo htmlspecialchars($s['proveedor'] ?: '—'); ?></td>
              <td ><?php echo $s['fecha_compra'] ? date('d/m/y', strtotime($s['fecha_compra'])) : '—'; ?></td>
              <td>
                <button class="btn-edit me-1"
                  onclick="abrirEditar(<?php echo (int)$s['id']; ?>,'<?php echo addslashes(htmlspecialchars($s['nombre'])); ?>','<?php echo $s['tipo']; ?>',<?php echo $s['precio_total_compra']; ?>,<?php echo $s['cantidad_lote']; ?>,<?php echo $s['contenido_por_unidad']; ?>,'<?php echo $s['proveedor'] ?? ''; ?>','<?php echo $s['fecha_compra'] ?? ''; ?>')">
                  <i class="fa fa-pen"></i>
                </button>
                <a href="suministros.php?del=<?php echo $s['id']; ?>" class="btn-del"
                   onclick="return confirm('¿Eliminar <?php echo addslashes(htmlspecialchars($s['nombre'])); ?>?')">
                  <i class="fa fa-trash"></i>
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </div>

</div><!-- /row -->
</div><!-- /page-body -->

<!-- ===== MODAL EDITAR ===== -->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-pen me-2"></i>Editar Suministro</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" id="frmEditar">
        <input type="hidden" name="accion" value="editar">
        <input type="hidden" name="id" id="eId">
        <div class="modal-body">
          <div class="mb-2">
            <label class="fl">Nombre del insumo *</label>
            <input type="text" name="nombre" id="eNombre" class="fc form-control" required>
          </div>
          <div class="mb-2">
            <label class="fl">Tipo *</label>
            <select name="tipo" id="eTipo" class="fc form-select" required>
              <option value="unidad">📦 Unidad</option>
              <option value="volumen">🧪 Volumen (ml)</option>
              <option value="peso">⚖️ Peso (g)</option>
            </select>
          </div>
          <div class="row g-2 mb-2">
            <div class="col-6">
              <label class="fl">Precio total $ *</label>
              <input type="number" name="precio_total_compra" id="ePrecio" class="fc form-control" step="0.01" min="0.01" required>
            </div>
            <div class="col-6">
              <label class="fl">Cant. en el pack *</label>
              <input type="number" name="cantidad_lote" id="eLote" class="fc form-control" step="0.01" min="0.01" required>
            </div>
          </div>
          <div class="mb-2">
            <label class="fl" id="eContLabel">Contenido por unidad *</label>
            <input type="number" name="contenido_por_unidad" id="eCont" class="fc form-control" step="0.0001" min="0.0001" required>
          </div>
          <!-- PREVIEW MODAL -->
          <div class="preview-box visible" id="ePreview">
            <div class="preview-row">
              <span class="preview-label">Capacidad total del lote</span>
              <span class="preview-val" id="ePrevTotal">—</span>
            </div>
            <div class="preview-row">
              <span class="preview-label">Costo por unidad base</span>
              <span class="preview-val" id="ePrevUnit">—</span>
            </div>
          </div>
          <div class="row g-2 mt-2">
            <div class="col-6">
              <label class="fl">Proveedor</label>
              <input type="text" name="proveedor" id="eProv" class="fc form-control">
            </div>
            <div class="col-6">
              <label class="fl">Fecha de compra</label>
              <input type="date" name="fecha_compra" id="eFecha" class="fc form-control">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn-main"><i class="fa fa-floppy-disk"></i>Guardar Cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* =============================================
   HELPERS
   ============================================= */
function fmtPeso(n){ return n%1===0 ? n.toLocaleString('es-AR') : n.toFixed(4).replace('.',','); }
function fmtARS(n){ return '$'+Math.round(n).toLocaleString('es-AR'); }
function unitLabel(tipo){ return tipo==='volumen'?'ml': tipo==='peso'?'g':'u'; }

/* ============================================
   PREVIEW EN TIEMPO REAL — FORMULARIO ALTA
   ============================================= */
const calcPreview = (precio, lote, cont, tipo, totalEl, unitEl, boxEl) => {
  const p = parseFloat(precio)||0, l = parseFloat(lote)||0, c = parseFloat(cont)||0;
  const ul = unitLabel(tipo);
  if (p > 0 && l > 0 && c > 0) {
    const totalMedida = l * c;
    const costBase    = p / totalMedida;
    if (tipo === 'unidad') {
      totalEl.textContent = l.toLocaleString('es-AR') + ' unidades';
    } else {
      totalEl.textContent = fmtPeso(totalMedida) + ' ' + ul + ' totales';
    }
    unitEl.textContent = '$' + costBase.toFixed(4).replace('.',',') + '/' + ul;
    boxEl.classList.add('visible');
  } else {
    boxEl.classList.remove('visible');
  }
};

/* ALTA */
const aInputs = ['aPrecio','aLote','aCont','aTipo'];
aInputs.forEach(id => {
  document.getElementById(id).addEventListener('input', refreshAlta);
});
function refreshAlta(){
  const tipo = document.getElementById('aTipo').value;
  const ul = unitLabel(tipo);
  // Ajustar label y placeholder de contenido
  const contLabel = document.getElementById('aContLabel');
  const contHint  = document.getElementById('aContHint');
  const contInp   = document.getElementById('aCont');
  if (tipo === 'unidad') {
    contLabel.textContent = 'Contenido por unidad * (dejar 1 para unidades)';
    contHint.textContent  = 'Para envases, cajas: siempre 1';
    contInp.value = contInp.value || '1';
  } else if (tipo === 'volumen') {
    contLabel.textContent = 'Contenido por envase (ml) *';
    contHint.textContent  = 'Ej: 10 para frascos de 10ml';
    contInp.placeholder   = '10';
  } else {
    contLabel.textContent = 'Contenido por envase (g) *';
    contHint.textContent  = 'Ej: 1000 para bolsas de 1kg';
    contInp.placeholder   = '1000';
  }
  calcPreview(
    document.getElementById('aPrecio').value,
    document.getElementById('aLote').value,
    document.getElementById('aCont').value,
    tipo,
    document.getElementById('aPrevTotal'),
    document.getElementById('aPrevUnit'),
    document.getElementById('aPreview')
  );
}
// Inicializar
refreshAlta();

/* ============================================
   MODAL EDITAR
   ============================================= */
const eInputs = ['ePrecio','eLote','eCont','eTipo'];
eInputs.forEach(id => {
  document.getElementById(id).addEventListener('input', refreshEditar);
});

function refreshEditar(){
  calcPreview(
    document.getElementById('ePrecio').value,
    document.getElementById('eLote').value,
    document.getElementById('eCont').value,
    document.getElementById('eTipo').value,
    document.getElementById('ePrevTotal'),
    document.getElementById('ePrevUnit'),
    document.getElementById('ePreview')
  );
  // Label contenido
  const tipo = document.getElementById('eTipo').value;
  const lbl = document.getElementById('eContLabel');
  lbl.textContent = tipo==='volumen'? 'Contenido por envase (ml) *'
    : tipo==='peso' ? 'Contenido por envase (g) *'
    : 'Contenido por unidad * (1 para unidades)';
}

function abrirEditar(id, nombre, tipo, precio, lote, cont, prov, fecha) {
  document.getElementById('eId').value     = id;
  document.getElementById('eNombre').value = nombre;
  document.getElementById('eTipo').value   = tipo;
  document.getElementById('ePrecio').value = precio;
  document.getElementById('eLote').value   = lote;
  document.getElementById('eCont').value   = cont;
  document.getElementById('eProv').value   = prov;
  document.getElementById('eFecha').value  = fecha;
  refreshEditar();
  new bootstrap.Modal(document.getElementById('modalEditar')).show();
}
</script>
</body>
</html>