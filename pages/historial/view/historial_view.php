<!DOCTYPE html>
<html lang="es">
<head>
  <?php require_once __DIR__ . '/../../../shared/head.php'; ?>
  <title>Historial - Parqueadero</title>
  <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
</head>
<body>
  <div class="app-layout">
    <?php require_once __DIR__ . '/../../../shared/nav.php'; ?>
    <main class="main-content">
      <div class="page-container">
        <div class="page-header">
          <h2>Historial</h2>
          <p class="page-subtitle">Consulte la trazabilidad de ingresos y salidas</p>
        </div>

        <div class="section-card">
          <div class="section-card-header"><h3>Filtros</h3></div>
          <form class="filter-form" id="form-filtros">
            <div class="filter-row">
              <div class="form-group">
                <label>Período rápido</label>
                <select name="periodo" class="form-control" id="select-periodo">
                  <option value="hoy">Hoy</option>
                  <option value="semana">Esta semana</option>
                  <option value="mes">Este mes</option>
                  <option value="anio">Este año</option>
                  <option value="rango">Rango personalizado</option>
                </select>
              </div>
              <div class="form-group" id="group-fecha-inicio" style="display:none">
                <label>Fecha inicio</label>
                <input type="date" name="fecha_inicio" class="form-control">
              </div>
              <div class="form-group" id="group-fecha-fin" style="display:none">
                <label>Fecha fin</label>
                <input type="date" name="fecha_fin" class="form-control">
              </div>
              <div class="form-group">
                <label>Tipo</label>
                <select name="tipo_vehiculo_id" class="form-control">
                  <option value="">Todos</option>
                  <?php foreach ($tipos as $t): ?>
                    <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['nombre']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label>Placa</label>
                <input type="text" name="placa" class="form-control" placeholder="Buscar placa">
              </div>
              <div class="form-group">
                <label>Estado</label>
                <select name="estado" class="form-control">
                  <option value="">Todos</option>
                  <option value="activo">Activos</option>
                  <option value="finalizado">Finalizados</option>
                </select>
              </div>
              <div class="form-group form-group-btn">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary">Filtrar</button>
              </div>
            </div>
          </form>
        </div>

        <div class="stats-grid stats-grid-sm" id="stats-historial">
          <div class="stat-card stat-blue">
            <div class="stat-info">
              <span class="stat-value" id="stat-activos">0</span>
              <span class="stat-label">Activos</span>
            </div>
          </div>
          <div class="stat-card stat-green">
            <div class="stat-info">
              <span class="stat-value" id="stat-finalizados">0</span>
              <span class="stat-label">Finalizados</span>
            </div>
          </div>
          <div class="stat-card stat-purple">
            <div class="stat-info">
              <span class="stat-value" id="stat-ingresos">$0</span>
              <span class="stat-label">Ingresos del período</span>
            </div>
          </div>
        </div>

        <div class="section-card">
          <div class="section-card-header d-flex justify-content-between align-items-center">
            <h3>Registros <span id="total-registros">(0)</span></h3>
            <button class="btn btn-sm btn-success" onclick="exportarExcel()"><i class="fas fa-file-excel"></i> Exportar Excel</button>
          </div>
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>Placa</th>
                  <th>Tipo</th>
                  <th>Ingreso</th>
                  <th>Salida</th>
                  <th>Duración</th>
                  <th>Total</th>
                  <th>Estado</th>
                </tr>
              </thead>
              <tbody id="tbody-historial"></tbody>
            </table>
          </div>
        </div>
      </div>
    </main>
  </div>
  <?php require_once __DIR__ . '/../../../shared/foot.php'; ?>
  <script>
    function cargarHistorial(filtros) {
      filtros = filtros || {};
      filtros.accion = 'listar';

      $.ajax({
        type: 'POST',
        url: 'index.php?url=historial',
        data: filtros,
        dataType: 'json',
        success: function (data) {
          var stats = data.stats || {};
          $('#stat-activos').text(stats.activos || 0);
          $('#stat-finalizados').text(stats.finalizados || 0);
          $('#stat-ingresos').text('$' + (stats.ingresos_hoy || 0).toLocaleString('es-CO'));

          var registros = data.registros || [];
          $('#total-registros').text('(' + registros.length + ')');
          var tbody = $('#tbody-historial').empty();

          if (registros.length === 0) {
            tbody.append('<tr><td colspan="7" class="empty-table">No hay registros en este período</td></tr>');
            return;
          }

          registros.forEach(function (r) {
            var total = r.total !== null ? '$' + Number(r.total).toLocaleString('es-CO') : '—';
            var badge = r.fecha_salida
              ? '<span class="badge badge-inactive">Finalizado</span>'
              : '<span class="badge badge-active">Activo</span>';
            var tr = '<tr>' +
              '<td class="td-placa">' + $('<span>').text(r.placa).html() + '</td>' +
              '<td>' + $('<span>').text(r.tipo_nombre || 'Desconocido').html() + '</td>' +
              '<td class="td-date">' + $('<span>').text(r.fecha_entrada_fmt || '').html() + '</td>' +
              '<td class="td-date">' + $('<span>').text(r.fecha_salida_fmt || '—').html() + '</td>' +
              '<td>' + $('<span>').text(r.duracion || '').html() + '</td>' +
              '<td class="td-total">' + total + '</td>' +
              '<td>' + badge + '</td>' +
              '</tr>';
            tbody.append(tr);
          });
        },
        error: function () {
          $('#tbody-historial').html('<tr><td colspan="7" class="empty-table">Error al cargar datos</td></tr>');
        }
      });
    }

    $(function () {
      cargarHistorial();

      $('#select-periodo').on('change', function () {
        var show = this.value === 'rango';
        $('#group-fecha-inicio').toggle(show);
        $('#group-fecha-fin').toggle(show);
      });

      $('#form-filtros').on('submit', function (e) {
        e.preventDefault();
        var data = {};
        $(this).serializeArray().forEach(function (field) {
          if (field.value) data[field.name] = field.value;
        });
        cargarHistorial(data);
      });
    });

    function exportarExcel() {
      var registros = [];
      $('#tbody-historial tr').each(function () {
        var celdas = $(this).find('td');
        if (celdas.length < 7) return;
        registros.push({
          'Placa': celdas.eq(0).text().trim(),
          'Tipo': celdas.eq(1).text().trim(),
          'Ingreso': celdas.eq(2).text().trim(),
          'Salida': celdas.eq(3).text().trim(),
          'Duración': celdas.eq(4).text().trim(),
          'Total': celdas.eq(5).text().trim(),
          'Estado': celdas.eq(6).text().trim(),
        });
      });

      if (registros.length === 0) {
        Swal.fire({ title: 'Sin datos', text: 'No hay registros para exportar', icon: 'info', confirmButtonText: 'Aceptar' });
        return;
      }

      var ws = XLSX.utils.json_to_sheet(registros);
      var wb = XLSX.utils.book_new();
      XLSX.utils.book_append_sheet(wb, ws, 'Historial');

      ws['!cols'] = [
        { wch: 10 }, { wch: 14 }, { wch: 18 }, { wch: 18 }, { wch: 10 }, { wch: 12 }, { wch: 12 }
      ];

      var total = registros.length;
      var fecha = new Date().toLocaleDateString('es-CO').replace(/\//g, '-');
      XLSX.writeFile(wb, 'historial_' + fecha + '_' + total + 'reg.xlsx');
    }
  </script>
</body>
</html>
