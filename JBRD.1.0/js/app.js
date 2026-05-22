// JBRD COIN - app.js

// ══════════════════════════════════════
// HEADER SCROLL
// ══════════════════════════════════════

window.addEventListener('scroll', function () {

  var header = document.getElementById('header');

  if (header) {
    header.classList.toggle('scrolled', window.scrollY > 40);
  }

});

// ══════════════════════════════════════
// USER DROPDOWN
// ══════════════════════════════════════

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

// ══════════════════════════════════════
// TOGGLE PASSWORD
// ══════════════════════════════════════

function togglePass(inputId, btn) {

  var input = document.getElementById(inputId);

  var isPassword = input.type === 'password';

  input.type = isPassword ? 'text' : 'password';

  btn.querySelector('svg').innerHTML = isPassword

    ? '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>'

    : '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';

}

// ══════════════════════════════════════
// VALIDACION CONTRASEÑAS
// ══════════════════════════════════════

var formRegistro = document.getElementById('formRegistro');

if (formRegistro) {

  formRegistro.addEventListener('submit', function (e) {

    var p1 = document.getElementById('pass1').value;
    var p2 = document.getElementById('pass2').value;

    if (p1 !== p2) {

      e.preventDefault();

      alert('Las contrasenas no coinciden.');

    }

  });

}

// ══════════════════════════════════════
// TRADINGVIEW - MERCADO EN VIVO
// ══════════════════════════════════════

window.addEventListener('DOMContentLoaded', function () {

  // ════════════════════════════════════
  // BITCOIN EN VIVO
  // ════════════════════════════════════

  new TradingView.widget({

    "autosize": true,

    "symbol": "BINANCE:BTCUSDT",

    "interval": "1",

    "timezone": "America/Bogota",

    "theme": "dark",

    "style": "1",

    "locale": "es",

    "toolbar_bg": "#0f172a",

    "enable_publishing": false,

    "hide_top_toolbar": false,

    "hide_legend": false,

    "save_image": false,

    "allow_symbol_change": false,

    "withdateranges": true,

    "container_id": "btc_chart",

    "overrides": {

      // Fondo
      "paneProperties.background": "#081120",

      // Grid
      "paneProperties.vertGridProperties.color": "rgba(255,255,255,0.03)",
      "paneProperties.horzGridProperties.color": "rgba(255,255,255,0.03)",

      // Texto
      "scalesProperties.textColor": "#9ca3af",

      // Línea de precio
      "mainSeriesProperties.priceLineColor": "#00c3ff",

      // Velas verdes
      "mainSeriesProperties.candleStyle.upColor": "#00c3ff",
      "mainSeriesProperties.candleStyle.borderUpColor": "#00c3ff",
      "mainSeriesProperties.candleStyle.wickUpColor": "#5ee7ff",

      // Velas rojas
      "mainSeriesProperties.candleStyle.downColor": "#ff4d6d",
      "mainSeriesProperties.candleStyle.borderDownColor": "#ff4d6d",
      "mainSeriesProperties.candleStyle.wickDownColor": "#ff8da1",

      // Volumen
      "volume.volume.color.0": "rgba(255,77,109,0.45)",
      "volume.volume.color.1": "rgba(0,195,255,0.45)"

    }

  });

  // ════════════════════════════════════
  // DOLAR / PESO COLOMBIANO
  // ════════════════════════════════════

  new TradingView.widget({

    "autosize": true,

    "symbol": "FX:USDCOP",

    "interval": "1",

    "timezone": "America/Bogota",

    "theme": "dark",

    "style": "1",

    "locale": "es",

    "toolbar_bg": "#0f172a",

    "enable_publishing": false,

    "hide_top_toolbar": false,

    "hide_legend": false,

    "save_image": false,

    "allow_symbol_change": false,

    "withdateranges": true,

    "container_id": "usd_chart",

    "overrides": {

      // Fondo
      "paneProperties.background": "#081120",

      // Grid
      "paneProperties.vertGridProperties.color": "rgba(255,255,255,0.03)",
      "paneProperties.horzGridProperties.color": "rgba(255,255,255,0.03)",

      // Texto
      "scalesProperties.textColor": "#9ca3af",

      // Línea precio
      "mainSeriesProperties.priceLineColor": "#00c3ff",

      // Velas alcistas
      "mainSeriesProperties.candleStyle.upColor": "#00c3ff",
      "mainSeriesProperties.candleStyle.borderUpColor": "#00c3ff",
      "mainSeriesProperties.candleStyle.wickUpColor": "#5ee7ff",

      // Velas bajistas
      "mainSeriesProperties.candleStyle.downColor": "#ff4d6d",
      "mainSeriesProperties.candleStyle.borderDownColor": "#ff4d6d",
      "mainSeriesProperties.candleStyle.wickDownColor": "#ff8da1",

      // Volumen
      "volume.volume.color.0": "rgba(255,77,109,0.45)",
      "volume.volume.color.1": "rgba(0,195,255,0.45)"

    }

  });

});