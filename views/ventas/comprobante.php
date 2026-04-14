<?php
$titulo = 'Comprobante FP-' . htmlspecialchars(substr($venta['numero_comprobante'], 3));
function dCOP($n) {
  return '$' . number_format((float)$n, 0, ',', '.');
}

$subtotal = array_reduce($detalle, function ($c, $i) {
  return $c + (float)$i['subtotal'];
}, 0);

$descuento = $subtotal - (float)$venta['total'];
$totalQty = array_reduce($detalle, function ($c, $i) {
  return $c + (int)$i['cantidad'];
}, 0);

$fechaDate = date('d M. Y', strtotime($venta['created_at']));
$horaCorta = date('H:i \h', strtotime($venta['created_at']));
$basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
?>
<!DOCTYPE html>
<html lang="es" class="!overflow-y-auto !h-auto">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FarmaPlus — Comprobante <?= htmlspecialchars($venta['numero_comprobante']) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest"></script>
  <link rel="stylesheet" href="<?= $basePath ?>/assets/css/app.min.css?v=<?= time() ?>">
  <style>
    @media print {
      @page {
        margin: 10mm;
        size: auto;
      }
    }
  </style>
</head>

<body class="!overflow-y-auto !h-auto font-['Inter',sans-serif] bg-[#F4F9FC] text-[#2C3E50] print:!bg-white">

  <!-- Modal correo -->
  <div id="modal-overlay" class="fixed inset-0 bg-black/50 z-[200] hidden items-center justify-center print:!hidden">
    <div class="bg-white rounded-xl w-full max-w-[440px] shadow-[0_20px_60px_rgba(0,0,0,0.18)] overflow-hidden transform transition-all translate-y-4 opacity-0" id="modal-box">
      <div class="px-5 py-4 border-b border-[#BDC3C7] flex items-center justify-between">
        <h2 class="text-[17px] font-semibold text-[#2C3E50] flex items-center gap-2"><i data-lucide="mail" class="w-[18px] h-[18px] text-[#1A6B8A]"></i>Enviar comprobante</h2>
        <button onclick="closeEmailModal()" class="w-7 h-7 rounded-md flex items-center justify-center text-[#7F8C8D] hover:bg-[#FDEDEC] hover:text-[#E74C3C] transition-colors"><i data-lucide="x" class="w-4 h-4"></i></button>
      </div>
      <div class="p-5">
        <label class="block text-[13px] font-medium text-[#2C3E50] mb-2">Correo electrónico del cliente</label>
        <input type="email" id="emailInput" placeholder="correo@ejemplo.com" class="w-full h-11 px-3 border-2 border-[#BDC3C7]/50 rounded-lg bg-[#F4F9FC] text-[14px] outline-none focus:border-[#1A6B8A] transition-colors">
        <p class="text-[12px] text-[#7F8C8D] mt-2">Se enviará el comprobante <strong class="font-mono text-[#1A6B8A]"><?= htmlspecialchars($venta['numero_comprobante']) ?></strong> en formato PDF al correo indicado.</p>
      </div>
      <div class="px-5 py-3 border-t border-[#BDC3C7]/50 flex justify-end gap-2 bg-[#F4F9FC]/30">
        <button onclick="closeEmailModal()" class="h-10 px-4 rounded-lg bg-transparent border-2 border-[#BDC3C7]/50 text-[14px] font-medium text-[#2C3E50] hover:border-[#1A6B8A] hover:text-[#1A6B8A] transition-colors">Cancelar</button>
        <button onclick="sendEmail()" class="h-10 px-4 rounded-lg bg-[#1A6B8A] text-white text-[14px] font-semibold flex items-center gap-2 hover:bg-[#1A3A4A] transition-colors"><i data-lucide="send" class="w-[15px] h-[15px]"></i>Enviar comprobante</button>
      </div>
    </div>
  </div>

  <div id="toast-container" class="fixed top-20 right-6 z-[999] flex flex-col gap-2 print:!hidden"></div>

  <!-- ACTION BAR -->
  <div id="action-bar" class="bg-[#1A3A4A] px-4 md:px-8 py-3 flex flex-wrap md:flex-nowrap items-center justify-between gap-3 sticky top-0 z-[100] shadow-[0_2px_8px_rgba(0,0,0,0.2)] print:!hidden">
    <div class="flex items-center gap-2 text-white/70 text-[14px] font-medium w-full md:w-auto">
      <a href="<?= $basePath ?>/ventas/pos" class="flex items-center gap-2 text-white no-underline pr-2">
        <div class="w-7 h-7 bg-[#2A9D8F] rounded flex items-center justify-center shrink-0"><i data-lucide="pill" class="w-4 h-4 text-white"></i></div>
        <span class="text-[15px] font-bold">Farma<span class="text-[#2A9D8F]">Plus</span></span>
      </a>
      <div class="w-[1px] h-6 bg-white/20 hidden md:block"></div>
      <div class="flex items-center gap-2 flex-1 md:flex-none justify-end md:justify-start">
        <i data-lucide="file-text" class="w-4 h-4 text-[#2A9D8F]"></i> Comprobante <span class="font-mono font-medium text-white/90"><?= htmlspecialchars($venta['numero_comprobante']) ?></span>
      </div>
    </div>

    <div class="w-full md:w-auto md:ml-auto flex flex-wrap justify-center md:justify-end gap-2 mt-1 md:mt-0">
      <a href="<?= $basePath ?>/ventas/pos" class="h-10 px-3 md:px-4 rounded border border-white/20 text-white/70 text-[13px] md:text-[14px] font-semibold flex items-center gap-1.5 md:gap-2 hover:border-white/45 hover:text-white transition-colors"><i data-lucide="arrow-left" class="w-4 h-4"></i><span class="hidden sm:inline">Volver</span> POS</a>
      <button onclick="window.print()" class="h-10 px-3 md:px-4 rounded bg-white/10 border border-white/25 text-white text-[13px] md:text-[14px] font-semibold flex items-center gap-1.5 md:gap-2 hover:bg-white/20 transition-colors"><i data-lucide="printer" class="w-4 h-4"></i>Imprimir</button>
      <button onclick="openEmailModal()" class="h-10 px-3 md:px-4 rounded bg-white/10 border border-white/25 text-white text-[13px] md:text-[14px] font-semibold flex items-center gap-1.5 md:gap-2 hover:bg-white/20 transition-colors"><i data-lucide="mail" class="w-4 h-4"></i>Correo</button>
      <a href="<?= $basePath ?>/ventas/pos" class="h-10 px-3 md:px-4 rounded bg-[#2A9D8F] text-white text-[13px] md:text-[14px] font-semibold flex items-center gap-1.5 md:gap-2 hover:bg-[#239B8C] shadow-[0_2px_8px_rgba(42,157,143,0.4)] transition-colors"><i data-lucide="plus-circle" class="w-4 h-4"></i>Nueva venta</a>
    </div>
  </div>

  <!-- PAGE WRAPPER -->
  <div id="page-wrapper" class="flex justify-center items-start pt-6 md:pt-10 pb-16 px-4 md:px-6 min-h-[calc(100vh-64px)] print:!block print:!p-0 print:!min-h-0">

    <article id="receipt" class="bg-white w-full max-w-[680px] rounded-xl shadow-[0_4px_32px_rgba(0,0,0,0.1)] overflow-hidden relative print:!shadow-none print:!max-w-full print:!rounded-none">

      <!-- Header -->
      <header class="bg-[#1A3A4A] py-6 md:py-8 px-6 md:px-10 relative overflow-hidden">
        <!-- Decos -->
        <div class="absolute w-[300px] h-[300px] rounded-full border-[1.5px] border-[#2A9D8F]/20 top-[-80px] right-[-60px] pointer-events-none"></div>
        <div class="absolute w-[180px] h-[180px] rounded-full border border-white/5 bottom-[-40px] left-[200px] pointer-events-none"></div>

        <div class="flex flex-col sm:flex-row items-start sm:justify-between relative z-10 mb-6 gap-4 border-b border-white/10 pb-4 sm:border-0 sm:pb-0">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 bg-[#2A9D8F] rounded-xl flex items-center justify-center shadow-[0_4px_12px_rgba(42,157,143,0.4)] shrink-0"><i data-lucide="pill" class="w-6 h-6 text-white"></i></div>
            <div>
              <div class="text-[22px] font-bold text-white tracking-[-0.3px] leading-none mb-1">Farma<span class="text-[#2A9D8F]">Plus</span></div>
              <div class="text-[11px] font-medium text-white/45 uppercase tracking-[1.5px]">Droguería · Sede Principal</div>
            </div>
          </div>
          <div class="flex flex-col items-start sm:items-end gap-1.5">
            <div class="inline-flex items-center gap-1.5 bg-[#27AE60] text-white rounded-full px-3.5 py-1.5 text-[13px] font-bold shadow-[0_2px_8px_rgba(39,174,96,0.4)]"><i data-lucide="circle-check" class="w-3.5 h-3.5"></i>Pago confirmado</div>
            <div class="text-[12px] text-white/50"><?= $fechaDate ?> · <?= $horaCorta ?></div>
          </div>
        </div>

        <div class="relative z-10 flex flex-col sm:flex-row items-start justify-between gap-4 sm:gap-6 pt-4 sm:border-t border-white/10">
          <div class="flex items-start gap-2">
            <i data-lucide="map-pin" class="w-[14px] h-[14px] text-[#2A9D8F] shrink-0 mt-[3px]"></i>
            <div class="text-[12px] text-white/60 leading-relaxed"><strong class="text-white/90 font-semibold">FarmaPlus Droguería</strong><br>Cra. 5 #12-80, Centro · Neiva, Huila</div>
          </div>
          <div class="flex items-start gap-2">
            <i data-lucide="phone" class="w-[14px] h-[14px] text-[#2A9D8F] shrink-0 mt-[3px]"></i>
            <div class="text-[12px] text-white/60 leading-relaxed"><strong class="text-white/90 font-semibold">(8) 871 2345</strong><br>Lun–Sáb 7am–9pm · Dom 8am–6pm</div>
          </div>
          <div class="flex items-start gap-2">
            <i data-lucide="globe" class="w-[14px] h-[14px] text-[#2A9D8F] shrink-0 mt-[3px]"></i>
            <div class="text-[12px] text-white/60 leading-relaxed"><strong class="text-white/90 font-semibold">farmaplus.co</strong><br>soporte@farmaplus.co</div>
          </div>
        </div>
      </header>

      <!-- Band -->
      <div class="bg-[#F4F9FC] border-b-2 border-dashed border-[#BDC3C7] px-6 md:px-10 py-5 flex flex-col md:flex-row md:items-center justify-between gap-4 md:gap-6">
        <div>
          <div class="text-[11px] font-semibold uppercase tracking-[1px] text-[#7F8C8D] mb-1">Número de comprobante</div>
          <div class="font-mono text-[26px] font-medium text-[#1A6B8A] tracking-[1px] leading-none"><?= htmlspecialchars($venta['numero_comprobante']) ?></div>
        </div>
        <div class="flex flex-wrap gap-4 md:gap-8 shrink-0">
          <div>
            <div class="text-[10px] font-semibold uppercase tracking-[0.8px] text-[#7F8C8D] mb-1">Fecha</div>
            <div class="text-[13px] font-medium text-[#2C3E50]"><?= $fechaDate ?></div>
          </div>
          <div>
            <div class="text-[10px] font-semibold uppercase tracking-[0.8px] text-[#7F8C8D] mb-1">Hora</div>
            <div class="text-[13px] font-medium text-[#2C3E50]"><?= $horaCorta ?></div>
          </div>
          <div>
            <div class="text-[10px] font-semibold uppercase tracking-[0.8px] text-[#7F8C8D] mb-1">Tipo de venta</div>
            <div class="text-[13px] font-medium text-[#2C3E50]">Presencial</div>
          </div>
          <div>
            <div class="text-[10px] font-semibold uppercase tracking-[0.8px] text-[#7F8C8D] mb-1">Terminal</div>
            <div class="text-[12px] font-medium text-[#2C3E50] font-mono">POS-01</div>
          </div>
        </div>
      </div>

      <!-- Body -->
      <div class="p-6 md:p-10">

        <!-- Parties -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-5 mb-7">
          <div class="bg-[#F4F9FC] rounded-lg px-4 py-3.5">
            <div class="text-[10px] font-bold uppercase tracking-[1px] text-[#7F8C8D] mb-2 flex items-center gap-1.5"><i data-lucide="user-check" class="w-3 h-3 text-[#1A6B8A]"></i>Atendido por</div>
            <div class="text-[14px] font-semibold text-[#2C3E50]"><?= htmlspecialchars($venta['vendedor_nombre']) ?></div>
            <div class="text-[12px] text-[#7F8C8D] mt-0.5">Vendedor · FarmaPlus</div>
          </div>
          <div class="bg-[#F4F9FC] rounded-lg px-4 py-3.5">
            <div class="text-[10px] font-bold uppercase tracking-[1px] text-[#7F8C8D] mb-2 flex items-center gap-1.5"><i data-lucide="user" class="w-3 h-3 text-[#1A6B8A]"></i>Cliente</div>
            <div class="text-[14px] font-semibold text-[#2C3E50]">Cliente Mostrador</div>
            <div class="text-[12px] text-[#7F8C8D] mt-0.5">Venta presencial rápida</div>
          </div>
        </div>

        <!-- Table -->
        <div class="text-[11px] font-bold uppercase tracking-[1px] text-[#7F8C8D] mb-2.5 flex items-center gap-1.5"><i data-lucide="pill" class="w-[13px] h-[13px] text-[#1A6B8A]"></i>Detalle de productos</div>

        <div class="border border-[#BDC3C7] rounded-lg overflow-x-auto mb-6">
          <table class="w-full border-collapse min-w-[500px]">
            <thead>
              <tr class="bg-[#1A3A4A] text-white/80 text-[11px] font-semibold uppercase tracking-[0.7px]">
                <th class="py-2.5 px-3 text-left">Producto</th>
                <th class="py-2.5 px-3 text-center w-12">Cant.</th>
                <th class="py-2.5 px-3 text-center">Precio unit.</th>
                <th class="py-2.5 px-3 text-right">Subtotal</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($detalle as $item):
                $invima = htmlspecialchars($item['codigo_invima'] ?? 'S/N');
                $lote = htmlspecialchars($item['numero_lote'] ?? $item['lote_id']);
                $isCtrl = (int)($item['control_especial'] ?? 0) === 1;
              ?>
                <tr class="border-b border-[#F4F9FC] hover:bg-[#F4F9FC] transition-colors last:border-b-0 group">
                  <td class="py-3 px-3">
                    <div class="flex items-center gap-2.5 overflow-hidden">
                      <div class="w-8 h-8 rounded bg-[#F4F9FC] border border-[#BDC3C7]/50 flex items-center justify-center shrink-0">
                        <i data-lucide="pill" class="w-4 h-4 text-[#1A6B8A]"></i>
                      </div>
                      <div class="min-w-0">
                        <div class="text-[13px] font-semibold text-[#2C3E50] truncate"><?= htmlspecialchars($item['producto_nombre']) ?></div>
                        <div class="font-mono text-[10px] text-[#7F8C8D] mt-0.5 truncate">INVIMA <?= $invima ?> · Lote <?= $lote ?></div>
                      </div>
                    </div>
                  </td>
                  <td class="py-3 px-3 text-center"><span class="text-[14px] font-bold text-[#2C3E50]"><?= $item['cantidad'] ?></span></td>
                  <td class="py-3 px-3 text-center text-[13px] text-[#7F8C8D]"><?= dCOP($item['precio_unitario']) ?></td>
                  <td class="py-3 px-3 text-right"><span class="text-[14px] font-semibold text-[#2C3E50]"><?= dCOP($item['subtotal']) ?></span></td>
                </tr>
                <?php if ($isCtrl && !empty($venta['formula_medica'])): ?>
                  <tr class="bg-[#FFF9F9]">
                    <td colspan="4" class="py-2 px-3 border-b border-[#F4F9FC] last:border-b-0">
                      <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 bg-[#FDEDEC] text-[#E74C3C] border border-[#E74C3C]/20 rounded text-[11px] font-semibold px-2 py-0.5"><i data-lucide="flask-conical" class="w-[11px] h-[11px]"></i>Control especial</span>
                        <span class="text-[12px] text-[#7F8C8D]">Fórmula médica N.º</span>
                        <span class="font-mono text-[12px] text-[#E74C3C] font-semibold"><?= htmlspecialchars($venta['formula_medica']) ?></span>
                      </div>
                    </td>
                  </tr>
              <?php endif;
              endforeach; ?>
            </tbody>
          </table>
        </div>

        <!-- Totals -->
        <div class="flex justify-end">
          <div class="bg-[#F4F9FC] rounded-lg p-5 min-w-[280px] border border-[#BDC3C7]">
            <div class="flex justify-between items-center py-1.5 text-[13px]">
              <span class="text-[#7F8C8D]">Subtotal (<?= $totalQty ?> ítems)</span>
              <span class="font-medium text-[#2C3E50]"><?= dCOP($subtotal) ?></span>
            </div>
            <div class="flex justify-between items-center py-1.5 text-[13px]">
              <span class="text-[#7F8C8D]">Descuento aplicado</span>
              <span class="font-medium text-[#27AE60]"><?= $descuento > 0 ? "− " . dCOP($descuento) : "$0" ?></span>
            </div>
            <div class="flex justify-between items-center py-2 mt-1.5 text-[13px] border-t border-dashed border-[#BDC3C7]">
              <span class="text-[#7F8C8D]">IVA incluido</span>
              <span class="font-medium text-[#2C3E50]">$0 (exento)</span>
            </div>
            <div class="flex justify-between items-center py-2.5 mt-1.5 border-t-2 border-[#1A3A4A]">
              <span class="text-[15px] font-bold text-[#2C3E50]">Total pagado</span>
              <span class="text-[22px] font-bold text-[#27AE60]"><?= dCOP($venta['total']) ?></span>
            </div>
            <div class="flex items-center gap-1.5 pt-2 mt-1.5 border-t border-[#BDC3C7]">
              <i data-lucide="banknote" class="w-3.5 h-3.5 text-[#1A6B8A]"></i>
              <span class="text-[13px] text-[#7F8C8D]">Método de pago:</span>
              <strong class="text-[13px] font-semibold text-[#2C3E50] capitalize"><?= htmlspecialchars($venta['metodo_pago']) ?></strong>
            </div>
          </div>
        </div>

      </div>

      <!-- Dots separator -->
      <div class="flex items-center mx-10 mb-6 overflow-hidden relative">
        <div class="w-3.5 h-3.5 rounded-full bg-[#F4F9FC] absolute left-[-24px] shadow-[inset_0_0_0_1px_#BDC3C7]"></div>
        <div class="flex-1 border-t-2 border-dashed border-[#BDC3C7]"></div>
        <div class="w-3.5 h-3.5 rounded-full bg-[#F4F9FC] absolute right-[-24px] shadow-[inset_0_0_0_1px_#BDC3C7]"></div>
      </div>

      <!-- Footer -->
      <footer class="bg-[#F4F9FC] border-t border-[#BDC3C7] pt-6 pb-6 px-6 md:px-10 text-center relative z-10 before:absolute before:inset-0 before:bg-white/50 before:z-[-1]">
        <div class="text-[16px] font-bold text-[#1A3A4A] mb-1.5">¡Gracias por su compra en FarmaPlus!</div>
        <div class="text-[13px] text-[#7F8C8D] leading-relaxed mb-5">
          Conserve este comprobante para cualquier reclamación o devolución.<br>
          Tiene <strong class="text-[#2C3E50]">5 días hábiles</strong> para solicitar cambios en productos de venta libre.<br>
          Los medicamentos de control especial <strong class="text-[#2C3E50]">no son devueltos</strong>.
        </div>

        <div class="flex items-center justify-center flex-wrap gap-x-4 md:gap-x-6 gap-y-2 py-4 border-y border-[#BDC3C7] mb-4">
          <div class="flex items-center gap-1.5 text-[12px] text-[#7F8C8D]"><i data-lucide="map-pin" class="w-3 h-3 text-[#1A6B8A]"></i> Cra. 5 #12-80, Centro · <strong class="text-[#2C3E50] font-medium">Neiva, Huila</strong></div>
          <div class="flex items-center gap-1.5 text-[12px] text-[#7F8C8D]"><i data-lucide="phone" class="w-3 h-3 text-[#1A6B8A]"></i> <strong class="text-[#2C3E50] font-medium">(8) 871 2345</strong></div>
          <div class="flex items-center gap-1.5 text-[12px] text-[#7F8C8D]"><i data-lucide="mail" class="w-3 h-3 text-[#1A6B8A]"></i> <strong class="text-[#2C3E50] font-medium">soporte@farmaplus.co</strong></div>
          <div class="flex items-center gap-1.5 text-[12px] text-[#7F8C8D]"><i data-lucide="clock" class="w-3 h-3 text-[#1A6B8A]"></i> Lun–Sáb 7am–9pm · Dom 8am–6pm</div>
        </div>

        <div class="flex flex-col-reverse md:flex-row items-center md:items-start justify-between gap-6 text-center md:text-left">
          <div class="text-[11px] text-[#B0B8C1] leading-relaxed flex-1 pt-1">
            Documento de soporte generado por FarmaPlus CRM v1.0.0 · NIT 900.123.456-7<br>
            Registro Sanitario INVIMA vigente · Res. 1403/2007 · Ley 2300/2023<br>
            Los precios incluyen todos los impuestos aplicables. Medicamentos de venta libre dispensados sin restricción.
            <?php if (!empty($venta['formula_medica'])): ?>
              Medicamentos de control especial dispensados con fórmula médica registrada bajo número <?= htmlspecialchars($venta['formula_medica']) ?>.
            <?php endif; ?>
          </div>
          <div class="flex flex-col items-center gap-1.5 shrink-0">
            <div class="w-[72px] h-[72px] bg-[#1A3A4A] rounded-lg flex items-center justify-center shadow-[0_2px_8px_rgba(0,0,0,0.15)]"><i data-lucide="qr-code" class="w-9 h-9 text-white/80"></i></div>
            <div class="text-[10px] text-[#7F8C8D] font-medium">Verificar en línea</div>
            <div class="font-mono text-[10px] text-[#B0B8C1]"><?= htmlspecialchars($venta['numero_comprobante']) ?></div>
          </div>
        </div>
      </footer>

    </article>
  </div>

  <script>
    lucide.createIcons();

    function openEmailModal() {
      const m = document.getElementById('modal-overlay');
      const b = document.getElementById('modal-box');
      m.classList.remove('hidden');
      m.classList.add('flex');
      setTimeout(() => {
        b.classList.remove('translate-y-4', 'opacity-0');
      }, 10);
    }

    function closeEmailModal() {
      const m = document.getElementById('modal-overlay');
      const b = document.getElementById('modal-box');
      b.classList.add('translate-y-4', 'opacity-0');
      setTimeout(() => {
        m.classList.add('hidden');
        m.classList.remove('flex');
      }, 200);
    }

    async function sendEmail() {
      const email = document.getElementById('emailInput').value.trim();
      if (!email) return;
      
      const btn = document.querySelector('button[onclick="sendEmail()"]');
      const originalText = btn.innerHTML;
      btn.innerHTML = `<i data-lucide="loader-2" class="w-[15px] h-[15px] animate-spin"></i> Enviando...`;
      btn.disabled = true;

      try {
        const res = await fetch('<?= $basePath ?>/ventas/comprobante/<?= $venta["venta_id"] ?>/enviar-correo', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ email })
        });
        const data = await res.json();
        
        closeEmailModal();
        if (data.success) {
          showToast(`Comprobante enviado a ${email}`, 'success');
        } else {
          showToast(data.error || 'Error al enviar el comprobante.', 'info');
        }
      } catch (err) {
        closeEmailModal();
        showToast('Fallo de conexión al enviar el correo.', 'info');
      } finally {
        btn.innerHTML = originalText;
        btn.disabled = false;
        lucide.createIcons();
      }
    }

    function showToast(msg, type = 'success') {
      const icons = {
        success: 'circle-check',
        info: 'info'
      };
      const bg = type === 'success' ? 'bg-[#EAFAF1] border-[#27AE60]' : 'bg-[#EBF5FB] border-[#3498DB]';
      const text = type === 'success' ? 'text-[#27AE60]' : 'text-[#3498DB]';

      const wrapper = document.createElement('div');
      wrapper.className = `flex items-start gap-2.5 p-3 rounded-lg shadow-[0_4px_20px_rgba(0,0,0,0.12)] min-w-[270px] bg-white border-l-4 border transform translate-x-[120%] opacity-0 transition-all duration-300 ${type === 'success' ? 'border-[#27AE60]' : 'border-[#3498DB]'}`;
      wrapper.innerHTML = `
      <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 ${bg}"><i data-lucide="${icons[type]}" class="w-4 h-4 ${text}"></i></div>
      <div class="text-[13px] text-[#2C3E50] mt-1"><strong class="block font-semibold">Listo</strong>${msg}</div>
    `;
      document.getElementById('toast-container').appendChild(wrapper);
      lucide.createIcons();

      requestAnimationFrame(() => {
        wrapper.classList.remove('translate-x-[120%]', 'opacity-0');
      });

      setTimeout(() => {
        wrapper.classList.add('translate-x-[120%]', 'opacity-0');
        setTimeout(() => wrapper.remove(), 300);
      }, 3500);
    }
  </script>
</body>
</html>