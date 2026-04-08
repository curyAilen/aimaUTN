<?php
session_start();
if(!isset($_SESSION["rol"])||$_SESSION["rol"]!=="admin"){header("Location: ../login.php");exit();}
include("../conexion.php");

/* Asegura tabla insumos si por algún motivo no existe aún */
mysqli_query($conexion,"CREATE TABLE IF NOT EXISTS `insumos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) NOT NULL,
  `tipo` enum('unidad','volumen','peso') NOT NULL DEFAULT 'unidad',
  `precio_total_compra` decimal(10,2) NOT NULL DEFAULT 0,
  `cantidad_lote` decimal(10,2) NOT NULL DEFAULT 1,
  `contenido_por_unidad` decimal(10,4) NOT NULL DEFAULT 1,
  `proveedor` varchar(200) DEFAULT NULL,
  `fecha_compra` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

/* Leer insumos con cálculo de costo_unit real */
$q_sum = mysqli_query($conexion,"SELECT * FROM insumos ORDER BY nombre ASC");
$suministros_list = [];
while($s = mysqli_fetch_assoc($q_sum)){
    $total_medida = (float)$s['cantidad_lote'] * (float)$s['contenido_por_unidad'];
    $s['costo_unit'] = $total_medida > 0 ? (float)$s['precio_total_compra'] / $total_medida : 0;
    /* Para que la lógica de la calculadora siga funcionando con tipo_medida como antes */
    $s['tipo_medida'] = $s['tipo'] === 'volumen' ? 'ml' : ($s['tipo'] === 'peso' ? 'gr' : 'unidad');
    $suministros_list[] = $s;
}
$suministros_json = json_encode($suministros_list, JSON_UNESCAPED_UNICODE);

/* Recetas predefinidas (pueden expandirse) */
$recetas = ["vela_320" => ["label"=>"Vela 320ml","tipos"=>[[" tipo"=>"ml","tipo"=>"ml","cantidad"=>25,"keyword"=>"fragancia"],[" tipo"=>"gr","tipo"=>"gr","cantidad"=>300,"keyword"=>"cera"]],"margen_sugerido"=>2.8],"vela_250" => ["label"=>"Vela 250ml","tipos"=>[["tipo"=>"ml","cantidad"=>20,"keyword"=>"fragancia"],["tipo"=>"gr","cantidad"=>230,"keyword"=>"cera"]],"margen_sugerido"=>2.8],"barras"   => ["label"=>"Barras Aromáticas","tipos"=>[["tipo"=>"ml","cantidad"=>10,"keyword"=>"fragancia"],["tipo"=>"gr","cantidad"=>120,"keyword"=>"cera"]],"margen_sugerido"=>3.0],"bombones" => ["label"=>"Bombones Aromáticos","tipos"=>[["tipo"=>"ml","cantidad"=>8,"keyword"=>"fragancia"],["tipo"=>"gr","cantidad"=>80,"keyword"=>"cera"]],"margen_sugerido"=>3.2],
];
$recetas_json = json_encode($recetas, JSON_UNESCAPED_UNICODE);
?>
<!DOCTYPE html><html lang="es"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Calculadora de Costos - AIMA</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../public/styles.css">
</head><body>

<div class="dash-header">
  <a href="../mostrar_contenido.php" ><i class="fa fa-chevron-left me-1"></i>Dashboard</a>
  <h1><i class="fa fa-calculator me-2"></i>Calculadora de Costos</h1>
  <a href="suministros.php" ><i class="fa fa-boxes-stacked me-1"></i>Suministros</a>
</div>

<div class="page-body">

<?php if(empty($suministros_list)): ?>
<div class="aviso mb-3"><i class="fa fa-triangle-exclamation me-2"></i><strong>Sin insumos cargados.</strong>
Primero <a href="suministros.php" >cargá tus suministros</a> para calcular costos reales.</div>
<?php endif; ?>

<div class="row g-3">

<!-- ===== CONFIGURACIÓN ===== -->
<div class="col-lg-5">
  <div class="panel">
    <div class="panel-title"><i class="fa fa-sliders"></i>Configuración</div>
    <label class="fl mb-2 d-block">Tipo de producto</label>
    <div class="prod-tabs" id="prodTabs">
      <div class="prod-tab active" data-prod="vela_320">🕯️ Vela 320ml</div>
      <div class="prod-tab" data-prod="vela_250">🕯️ Vela 250ml</div>
      <div class="prod-tab" data-prod="barras">🪵 Barras</div>
      <div class="prod-tab" data-prod="bombones">🍫 Bombones</div>
    </div>

    <div class="toggle-pack">
      <div class="form-check form-switch mb-0">
        <input class="form-check-input" type="checkbox" id="togglePack" checked>
      </div>
      <div>
        <div >Incluir packaging</div>
        <div >Envase, etiqueta, caja...&nbsp;<small >(tipo: Unidad)</small></div>
      </div>
    </div>

    <div class="mb-3">
      <label class="fl">Margen de ganancia (multiplicador)</label>
      <div class="d-flex align-items-center gap-2 mt-1">
        <input type="range" id="margenRange" min="1.5" max="5" step="0.1" value="2.8" class="form-range flex-fill">
        <span id="margenLabel" >x2.8</span>
      </div>
      <small >Costo × <span id="margenSmall">2.8</span> = Precio de venta sugerido</small>
    </div>

    <div class="mb-3">
      <label class="fl">Packaging extra $ (por unidad)</label>
      <input type="number" id="packManual" class="form-control fc-dark" value="0" min="0" step="100" placeholder="0">
      <small >Sumá aquí costos de packaging no cargados como insumo</small>
    </div>

    <button onclick="calcular()" class="btn-calc">
      <i class="fa fa-calculator"></i>Calcular Precio
    </button>
  </div>
</div>

<!-- ===== RESULTADO ===== -->
<div class="col-lg-7">
  <div class="panel">
    <div class="panel-title"><i class="fa fa-chart-bar"></i>Resultado</div>
    <div id="resEmpty" class="text-center py-4">
      <i class="fa fa-calculator fa-2x d-block mb-2"></i>
      <p >Configurá el producto y presioná <strong >Calcular Precio</strong></p>
    </div>
    <div id="resBlock" >
      <div class="result-card mb-3">
        <div id="prodLabel" ></div>
        <div class="row g-3">
          <div class="col-6">
            <div class="label-small">Costo de producción</div>
            <div class="costo-total" id="costoDisplay">$0</div>
          </div>
          <div class="col-6">
            <div class="label-small">Precio sugerido de venta</div>
            <div class="precio-sugerido" id="precioDisplay">$0</div>
          </div>
        </div>
      </div>
      <div>
        <h6 >Desglose de costos</h6>
        <div id="desglose"></div>
      </div>
      <div class="mt-3 p-3 rounded-3">
        <p ><i class="fa fa-circle-info me-1"></i>Cantidades estimadas base por producto. Ajustá el multiplicador según tu mercado.</p>
      </div>
    </div>
  </div>

  <!-- TABLA INSUMOS DISPONIBLES -->
  <?php if(!empty($suministros_list)): ?>
  <div class="panel">
    <div class="panel-title"><i class="fa fa-boxes-stacked"></i>Insumos disponibles
      <a href="suministros.php" ><i class="fa fa-pen me-1"></i>Gestionar</a>
    </div>
    <div ><table class="ins-tbl">
      <thead><tr>
        <th>Insumo</th><th>Tipo</th><th>Lote</th><th>Cont/u</th><th>Costo/base</th>
      </tr></thead>
      <tbody>
      <?php foreach($suministros_list as $sl):
        $ul = $sl['tipo']==='volumen'?'ml': ($sl['tipo']==='peso'?'g':'u');
        $bc = 'badge-'.$sl['tipo'];
      ?>
      <tr>
        <td ><?php echo htmlspecialchars($sl["nombre"]); ?></td>
        <td><span class="badge-tipo <?php echo $bc; ?>"><?php echo $sl["tipo"]; ?></span></td>
        <td ><?php echo number_format($sl['cantidad_lote'],0,',','.'); ?> u</td>
        <td ><?php echo number_format($sl['contenido_por_unidad'],2,',','.'); ?> <?php echo $ul; ?></td>
        <td >$<?php echo number_format($sl["costo_unit"],4,",","."); ?>/<?php echo $ul; ?></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
  </div>
  <?php endif; ?>
</div>

</div><!-- /row -->
</div><!-- /page-body -->

<script>
const SUMINISTROS=<?php echo $suministros_json; ?>;
const RECETAS=<?php echo $recetas_json; ?>;
let prodActual="vela_320";

document.querySelectorAll(".prod-tab").forEach(t=>{
  t.addEventListener("click",function(){
    document.querySelectorAll(".prod-tab").forEach(x=>x.classList.remove("active"));
    this.classList.add("active");
    prodActual=this.dataset.prod;
    const r=RECETAS[prodActual];
    if(r){
      document.getElementById("margenRange").value=r.margen_sugerido;
      document.getElementById("margenLabel").textContent="x"+r.margen_sugerido;
      document.getElementById("margenSmall").textContent=r.margen_sugerido;
    }
  });
});

document.getElementById("margenRange").addEventListener("input",function(){
  const v=parseFloat(this.value).toFixed(1);
  document.getElementById("margenLabel").textContent="x"+v;
  document.getElementById("margenSmall").textContent=v;
});

function fmt(n){return"$"+Math.round(n).toLocaleString("es-AR");}
function fmt4(n){return"$"+n.toFixed(4).replace(".",",");}

function calcular(){
  const receta=RECETAS[prodActual]; if(!receta)return;
  const inclPack=document.getElementById("togglePack").checked;
  const margen=parseFloat(document.getElementById("margenRange").value);
  const packExtra=parseFloat(document.getElementById("packManual").value)||0;
  let costo=0, desglose=[];

  receta.tipos.forEach(req=>{
    /* Buscar el insumo que coincida con tipo y keyword (nombre) */
    let match=null;
    SUMINISTROS.forEach(s=>{
      if(s.tipo_medida===req.tipo){
        const nl=s.nombre.toLowerCase();
        if(nl.includes(req.keyword)){if(!match||s.costo_unit<match.costo_unit)match=s;}
      }
    });
    /* fallback: cualquier insumo del mismo tipo */
    if(!match)SUMINISTROS.forEach(s=>{
      if(s.tipo_medida===req.tipo){if(!match||s.costo_unit<match.costo_unit)match=s;}
    });

    if(match){
      const sub=match.costo_unit*req.cantidad;
      costo+=sub;
      desglose.push({n:match.nombre+" ("+req.cantidad+""+req.tipo+")|"+fmt4(match.costo_unit)+"/"+req.tipo,v:sub,t:"insumo"});
    } else {
      desglose.push({n:"⚠ Sin insumo de tipo"+req.keyword,v:0,t:"warn"});
    }
  });

  /* Packaging */
  if(inclPack){
    let ps=0;
    ["packaging","envase","caja","etiqueta","bolsa","tapa","pote","frasco","vaso"].forEach(kw=>{
      SUMINISTROS.forEach(s=>{
        if(s.tipo_medida==="unidad"&&s.nombre.toLowerCase().includes(kw)) ps+=s.costo_unit;
      });
    });
    const tp=ps+packExtra;
    if(tp>0){costo+=tp;desglose.push({n:"Packaging",v:tp,t:"pack"});}
  }

  const precio=costo*margen;
  document.getElementById("resEmpty").style.display="none";
  document.getElementById("resBlock").style.display="block";
  document.getElementById("prodLabel").textContent=receta.label;
  document.getElementById("costoDisplay").textContent=fmt(costo);
  document.getElementById("precioDisplay").textContent=fmt(precio);

  const el=document.getElementById("desglose"); el.innerHTML="";
  desglose.forEach(d=>{
    const div=document.createElement("div");
    div.className="desglose-item";
    const c=d.t==="warn"?"#f59e0b": d.t==="pack"?"#a78bfa":"#E5E7EB";
    const [nombre, detalle]= d.n.split("|");
    div.innerHTML='<span class="dname">'+nombre+(detalle?'<small >('+detalle+')</small>':'')+'</span><span class="dval">'+fmt(d.v)+'</span>';
    el.appendChild(div);
  });

  const dt=document.createElement("div");
  dt.className="desglose-item";
  dt.style.cssText="border-top:2px solid #374151;margin-top:6px";
  dt.innerHTML='<span class="dname">COSTO TOTAL</span><span class="dval">'+fmt(costo)+'</span>';
  el.appendChild(dt);

  const dp=document.createElement("div");
  dp.className="desglose-item";
  dp.innerHTML='<span class="dname">PRECIO SUGERIDO (×'+margen.toFixed(1)+')</span><span class="dval">'+fmt(precio)+'</span>';
  el.appendChild(dp);
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body></html>