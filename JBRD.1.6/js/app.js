(function () {
  if (localStorage.getItem('tema') === 'claro') {
    document.documentElement.classList.add('tema-claro');
  }
})();
 
 
window.addEventListener('scroll', function () {
  var header = document.getElementById('header');
  if (header) {
    header.classList.toggle('scrolled', window.scrollY > 40);
  }
});
 
 
/* ── USER MENU ── */
function toggleMenu() {
  var dd = document.getElementById('userDropdown');
  if (dd) {
    dd.classList.toggle('hidden');
  }
}
 
document.addEventListener('click', function (e) {
  var menu = document.querySelector('.user-menu');
  var dd   = document.getElementById('userDropdown');
  if (menu && dd && !menu.contains(e.target)) {
    dd.classList.add('hidden');
  }
});
 
 
/* ── TOGGLE PASSWORD ── */
function togglePass(inputId, btn) {
  var input = document.getElementById(inputId);
  if (!input) return;
 
  var isPassword = input.type === 'password';
  input.type = isPassword ? 'text' : 'password';
 
  var svgElement = btn.querySelector('svg');
  if (svgElement) {
    svgElement.innerHTML = isPassword
      ? '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>'
      : '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
  }
}
 
 
/* ── FORM REGISTRO ── */
document.addEventListener('DOMContentLoaded', function () {
 
  var formRegistro = document.getElementById('formRegistro');
  if (formRegistro) {
    formRegistro.addEventListener('submit', function (e) {
      var pass1Element = document.getElementById('pass1');
      var pass2Element = document.getElementById('pass2');
      if (pass1Element && pass2Element) {
        if (pass1Element.value !== pass2Element.value) {
          e.preventDefault();
          alert('Las contraseñas no coinciden.');
        }
      }
    });
  }
 
  loadBTCWidget();
  loadDXYWidget();
 
  updateClock();
  updateMarketStatus();
 
  /* ── Toggle tema en configuracion.php ── */
  var toggleTema = document.getElementById('toggle-tema');
  if (toggleTema) {
    var temaDesc = document.getElementById('tema-desc');
 
    // Sincronizar estado visual del checkbox con localStorage
    if (localStorage.getItem('tema') === 'claro') {
      toggleTema.checked = false;
      if (temaDesc) temaDesc.textContent = 'Actualmente usando el tema claro.';
    } else {
      toggleTema.checked = true;
      if (temaDesc) temaDesc.textContent = 'Actualmente usando el tema oscuro.';
    }
 
    toggleTema.addEventListener('change', function () {
      if (toggleTema.checked) {
        // Activar tema oscuro
        document.documentElement.classList.remove('tema-claro');
        localStorage.setItem('tema', 'oscuro');
        if (temaDesc) temaDesc.textContent = 'Actualmente usando el tema oscuro.';
      } else {
        // Activar tema claro
        document.documentElement.classList.add('tema-claro');
        localStorage.setItem('tema', 'claro');
        if (temaDesc) temaDesc.textContent = 'Actualmente usando el tema claro.';
      }
    });
  }
 
});
 
 
/* ── BTC WIDGET ── */
function loadBTCWidget() {
  var container = document.getElementById('btc_container');
  if (!container) return;
 
  var widgetDiv = container.querySelector('.tradingview-widget-container__widget');
  if (!widgetDiv) return;
 
  widgetDiv.innerHTML = '';
 
  var script = document.createElement('script');
  script.type = 'text/javascript';
  script.src  = 'https://s3.tradingview.com/external-embedding/embed-widget-advanced-chart.js';
  script.async = true;
 
  script.innerHTML = JSON.stringify({
    "autosize":          true,
    "symbol":            "BINANCE:BTCUSDT",
    "interval":          "5S",
    "timezone":          "America/Bogota",
    "theme":             "dark",
    "style":             "1",
    "locale":            "es",
    "backgroundColor":   "#111827",
    "gridColor":         "rgba(255,255,255,0.04)",
    "hide_top_toolbar":  false,
    "hide_legend":       false,
    "save_image":        false,
    "hide_volume":       false,
    "support_host":      "https://www.tradingview.com"
  });
 
  container.appendChild(script);
}
 
 
/* ── DXY WIDGET ── */
function loadDXYWidget() {
  var container = document.getElementById('dxy_container');
  if (!container) return;
 
  var widgetDiv = container.querySelector('.tradingview-widget-container__widget');
  if (!widgetDiv) return;
 
  widgetDiv.innerHTML = '';
 
  var script = document.createElement('script');
  script.type  = 'text/javascript';
  script.src   = 'https://s3.tradingview.com/external-embedding/embed-widget-advanced-chart.js';
  script.async = true;
 
  script.innerHTML = JSON.stringify({
    "autosize":          true,
    "symbol":            "CAPITALCOM:DXY",
    "interval":          "5S",
    "timezone":          "America/Bogota",
    "theme":             "dark",
    "style":             "1",
    "locale":            "es",
    "backgroundColor":   "#111827",
    "gridColor":         "rgba(255,255,255,0.04)",
    "hide_top_toolbar":  false,
    "hide_legend":       false,
    "save_image":        false,
    "hide_volume":       false,
    "support_host":      "https://www.tradingview.com"
  });
 
  container.appendChild(script);
}
 
 
/* ── RELOJ EN VIVO ── */
function updateClock() {
  var clock = document.getElementById('marketClock');
  if (!clock) return;
 
  clock.textContent = new Date().toLocaleTimeString('es-CO', {
    hour:   '2-digit',
    minute: '2-digit',
    second: '2-digit'
  });
}
 
setInterval(updateClock, 1000);
 
 
/* ── ESTADO DE MERCADOS (rotación cada 10s) ── */
var MERCADOS = [
  { nombre: 'NYSE',      zona: 'America/New_York',  abre:  9.5,  cierra: 16   },
  { nombre: 'NASDAQ',    zona: 'America/New_York',  abre:  9.5,  cierra: 16   },
  { nombre: 'Londres',   zona: 'Europe/London',     abre:  8,    cierra: 16.5 },
  { nombre: 'Frankfurt', zona: 'Europe/Berlin',     abre:  9,    cierra: 17.5 },
  { nombre: 'Tokio',     zona: 'Asia/Tokyo',        abre:  9,    cierra: 15.5 },
  { nombre: 'Hong Kong', zona: 'Asia/Hong_Kong',    abre:  9.5,  cierra: 16   },
  { nombre: 'Shanghái',  zona: 'Asia/Shanghai',     abre:  9.5,  cierra: 15   },
  { nombre: 'Crypto',    zona: null,                abre:  0,    cierra: 24   },
];
 
var _mercadoIndex = 0;
 
function getHoraDecimal(zona) {
  var now = new Date();
  if (!zona) return now.getHours() + now.getMinutes() / 60;
  var local = new Date(now.toLocaleString('en-US', { timeZone: zona }));
  return local.getHours() + local.getMinutes() / 60;
}
 
function esMercadoAbierto(mercado) {
  if (!mercado.zona) return true;
  var hora = getHoraDecimal(mercado.zona);
  var ahora = new Date(new Date().toLocaleString('en-US', { timeZone: mercado.zona }));
  var dia = ahora.getDay();
  if (dia === 0 || dia === 6) return false;
  return hora >= mercado.abre && hora < mercado.cierra;
}
 
function updateMarketStatus() {
  var status = document.getElementById('marketStatus');
  if (!status) return;
  var mercado = MERCADOS[_mercadoIndex];
  var abierto = esMercadoAbierto(mercado);
  status.innerHTML =
    '<span class="market-label">' + mercado.nombre + ':</span> ' +
    (abierto
      ? '<span class="market-open">● Abierto</span>'
      : '<span class="market-closed">● Cerrado</span>');
  _mercadoIndex = (_mercadoIndex + 1) % MERCADOS.length;
}
 
updateMarketStatus();
setInterval(updateMarketStatus, 10000);
 
 
/* ── Perfil: animación de la barra de XP ── */
(function () {
  var xpBar = document.getElementById('xpBar');
  if (!xpBar) return;
  requestAnimationFrame(function () {
    setTimeout(function () {
      xpBar.style.width = xpBar.dataset.pct + '%';
    }, 150);
  });
})();
 