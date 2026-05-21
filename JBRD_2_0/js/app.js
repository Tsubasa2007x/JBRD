// ================== USUARIOS ==================
const admins = [
  { user: "Biggy", pass: "Biggy123" },
  { user: "Mono", pass: "Mono123" },
  { user: "Ricardo", pass: "Ricardo123" },
];

// ================== UTILIDADES ==================
function ocultar(id) {
  const input = document.getElementById(id);
  if (input) {
    input.type = input.type === "password" ? "text" : "password";
  }
}

function validarPassword(pass) {
  const regex = /^(?=.*[A-Z])[A-Za-z0-9]{6,}$/;
  return regex.test(pass);
}

// ================== REGISTRO ==================
function registrar() {
  const user = document.getElementById("regUser")?.value.trim();
  const pass = document.getElementById("regPass")?.value.trim();

  if (!user || !pass) {
    Swal.fire({ icon: "error", title: "Completa todos los campos" });
    return;
  }

  if (!validarPassword(pass)) {
    Swal.fire({ icon: "error", title: "Contraseña inválida" });
    return;
  }

  if (usuarios.some((u) => u.user === user)) {
    Swal.fire({ icon: "error", title: "Usuario ya existe" });
    return;
  }

  Swal.fire({ icon: "success", title: "Registro exitoso" });

  setTimeout(() => {
    window.location.href = "login.html";
  }, 1200);
}

// ================== LOGIN ==================
function login() {
  const rol = document.getElementById("rol")?.value;
  const user = document.getElementById("usuario")?.value.trim();
  const pass = document.getElementById("contraseña")?.value.trim();

  if (!rol || !user || !pass) {
    Swal.fire({ icon: "error", title: "Completa todos los campos" });
    return;
  }

  let valido = false;

  if (rol === "admin") {
    valido = admins.some((a) => a.user === user && a.pass === pass);
  }

  if (valido) {
    localStorage.setItem("usuarioActivo", user);
    localStorage.setItem("rolActivo", rol);

    Swal.fire({
      icon: "success",
      title: "Bienvenido",
      text: `Hola ${user}`,
      confirmButtonColor: "#00ffe7",
    }).then(() => {
      window.location.href = "inicio.html";
    });
  } else {
    Swal.fire({
      icon: "error",
      title: "Error",
      text: "Usuario o contraseña incorrectos",
    });
  }
}

// ================== NAVEGACIÓN ==================
function cerrarSesion() {
  localStorage.removeItem("usuarioActivo");
  localStorage.removeItem("rolActivo");
  window.location.href = "inicio.html";
}
function irSimulacion() {
  window.location.href = "simulacion.html";
}
function irAnalisis() {
  window.location.href = "analisis.html";
}

function irFormacion() {
  window.location.href = "formacion.html";
}

function irEntorno() {
  window.location.href = "entorno.html";
}

function irPerfilVista() {
  window.location.href = "seguimiento.html";
}
function volverInicio() {
  window.location.href = "inicio.html";
}

function volverAdmin() {
  window.location.href = "admin.html";
}

function irRegistro() {
  window.location.href = "registro1.php";
}

function irAdmin() {
  window.location.href = "admin.html";
}

function adminUsuarios() {
  window.location.href = "admin.html";
}

function irLogin() {
  window.location.href = "login.php";
}

function irreg() {
  window.location.href = "registro.html";
}

function mostrarPerfil() {
  window.location.href = "perfil.html";
}

// ================== MENÚ USUARIO ==================
window.onload = function () {
  // PERFILsimulacion.html
  if (document.getElementById("nombre")) {
    cargarPerfil();
  }
  if (document.getElementById("tablaUsuarios")) {
    cargarUsuarios();
  }
  //datos usuario vista del admin

  if (document.getElementById("nombre")) {
    cargarPerfil();
  }

  if (document.getElementById("tablaUsuarios")) {
    cargarUsuarios();
  }

  // AUTO GUARDADO
  document
    .getElementById("nombre")
    ?.addEventListener("input", autoGuardarPerfil);
  document
    .getElementById("correo")
    ?.addEventListener("input", autoGuardarPerfil);
  document
    .getElementById("telefono")
    ?.addEventListener("input", autoGuardarPerfil);
  document.getElementById("edad")?.addEventListener("input", autoGuardarPerfil);
  document
    .getElementById("ciudad")
    ?.addEventListener("input", autoGuardarPerfil);
  document
    .getElementById("estudio")
    ?.addEventListener("change", autoGuardarPerfil);

  const user = localStorage.getItem("usuarioActivo");
  const rol = localStorage.getItem("rolActivo");
  const contenedorAuth = document.querySelector(".auth-buttons");

  if (contenedorAuth) {

    contenedorAuth.innerHTML = "";

    if (user) {
      
      contenedorAuth.innerHTML = `
      <div class="user-menu">
        <button class="btn-outline user-btn" onclick="toggleMenu()">
          ${user}${rol === "admin" ? " (Admin)" : ""}
        </button>
        <div id="dropdown" class="dropdown hidden">
          ${
            rol === "admin"
              ? `<p onclick="adminUsuarios()">Administrar usuarios</p>`
              : `<p onclick="mostrarPerfil()">Perfil</p>`
          }
          <p onclick="cerrarSesion()">Cerrar sesión</p>
        </div>
      </div>
    `;
    } else {
      
      contenedorAuth.innerHTML = `
      <button class="btn-outline" onclick="irRegistro()">REGISTRO</button>
      <button class="btn-outline" onclick="irLogin()">INICIAR</button>
    `;
    }
  }
};
// ================== VALIDACIÓN EN TIEMPO REAL ==================
window.addEventListener("load", () => {
  document.querySelectorAll("input").forEach((input) => {
    const excluir = ["fechanac", "fechaexp", "tipoid"];

    if (excluir.includes(input.id)) return;

    const validarCampo = () => {
      const valor = input.value.trim();

      if (valor === "") {
        input.classList.remove("input-ok", "input-warning");
      } else if (valor.length < 3) {
        input.classList.add("input-warning");
        input.classList.remove("input-ok");
      } else {
        input.classList.remove("input-warning");
        input.classList.add("input-ok");
      }
    };

    input.addEventListener("input", validarCampo);
    input.addEventListener("blur", validarCampo);

    input.addEventListener("input", () => {
      input.classList.remove("input-error");
    });
  });
});
function toggleMenu() {
  const menu = document.getElementById("dropdown");
  if (menu) {
    menu.classList.toggle("hidden");
  }
}

// ================== PERFIL ==================
function cargarPerfil() {
  const user = localStorage.getItem("usuarioActivo");

  const datos = JSON.parse(localStorage.getItem("perfil_" + user)) || {};
  let datosRegistro =
    JSON.parse(localStorage.getItem("datosRegistroTemp")) || {};

  document.getElementById("nombreUsuario").textContent = user;

  document.getElementById("experienciaUsuario").textContent =
    "Experiencia: " + (datos.estudio || "No definida");

  document.getElementById("nombre").value =
    datos.nombre || datosRegistro.nombre || "";
  document.getElementById("correo").value =
    datos.correo || datosRegistro.correo || "";
  document.getElementById("edad").value = datos.edad || "";
  document.getElementById("ciudad").value = datos.ciudad || "";

  const telefonoCompleto = datos.telefono || datosRegistro.telefono || "";

  if (telefonoCompleto.startsWith("+57")) {
    document.getElementById("codigoPais").value = "+57";
    document.getElementById("telefono").value = telefonoCompleto.replace(
      "+57",
      "",
    );
  } else {
    document.getElementById("telefono").value = telefonoCompleto;
  }

  const fotoGuardada = localStorage.getItem("foto_" + user);

  if (fotoGuardada) {
    const img = document.getElementById("previewImg");
    const texto = document.getElementById("textoAvatar");

    img.src = fotoGuardada;
    img.classList.remove("hidden");
    texto.style.display = "none";
  }

  const rol = localStorage.getItem("rolActivo");
  document.getElementById("rolUsuario").textContent = "Rol: " + rol;

  let nivel = datos.nivel || 1;
  let porcentaje = nivel * 15;

if(porcentaje > 100){
porcentaje =100;
}

  const barra = document.getElementById("barraProgreso");
  if (barra) barra.style.width = porcentaje + "%";

  document.getElementById("nivelTexto").textContent = "Nivel " + nivel;

  document.getElementById("areaEstudio").textContent = datos.estudio
    ? "Ruta: " + datos.estudio
    : "Sin ruta";

  autoGuardarPerfil();
}

// ================== IMAGEN ==================
function cargarImagen(event) {
  const user = localStorage.getItem("usuarioActivo");
  const archivo = event.target.files[0];

  if (!archivo) return;

  const reader = new FileReader();

  reader.onload = function (e) {
    const base64 = e.target.result;

    localStorage.setItem("foto_" + user, base64);

    const img = document.getElementById("previewImg");
    const texto = document.getElementById("textoAvatar");

    img.src = base64;
    img.classList.remove("hidden");

    texto.style.display = "none";
  };

  reader.readAsDataURL(archivo);
}

// ================== GUARDAR PERFIL ==================
function guardarPerfil() {
  const user = localStorage.getItem("usuarioActivo");

  let datos = JSON.parse(localStorage.getItem("perfil_" + user)) || {};

  datos.nombre = document.getElementById("nombre").value;
  datos.correo = document.getElementById("correo").value;
  datos.edad = document.getElementById("edad").value;
  datos.ciudad = document.getElementById("ciudad").value;

  const codigo = document.getElementById("codigoPais").value;
  const numero = document.getElementById("telefono").value;
  datos.telefono = codigo + numero;

  datos.estudio = document.getElementById("estudio").value;

  localStorage.setItem("perfil_" + user, JSON.stringify(datos));

  Swal.fire({
    icon: "success",
    title: "Perfil guardado",
    showConfirmButton: false,
    timer: 1500,
  });

  cargarPerfil();
}
// ================== AUTO GUARDADO ==================
function autoGuardarPerfil() {
  const user = localStorage.getItem("usuarioActivo");

  let datos = JSON.parse(localStorage.getItem("perfil_" + user)) || {};

  datos.nombre = document.getElementById("nombre").value;
  datos.correo = document.getElementById("correo").value;
  datos.edad = document.getElementById("edad").value;
  datos.ciudad = document.getElementById("ciudad").value;

  const codigo = document.getElementById("codigoPais").value;
  const numero = document.getElementById("telefono").value;
  datos.telefono = codigo + numero;

  datos.estudio = document.getElementById("estudio").value;

  localStorage.setItem("perfil_" + user, JSON.stringify(datos));
}

// ================== NIVEL ==================
function subirNivel() {
  const user = localStorage.getItem("usuarioActivo");
  let datos = JSON.parse(localStorage.getItem("perfil_" + user)) || {};

  let nivel = datos.nivel || 1;

  if (nivel < 10) nivel++;

  datos.nivel = nivel;

  localStorage.setItem("perfil_" + user, JSON.stringify(datos));
}

function completarLeccion() {
  Swal.fire("Lección completada 🎉");
  subirNivel();
}

// ================== REGISTRO EXTRA ==================
function guardarDatosRegistro() {
  const nombre = document.getElementById("regnom");
  const apellido = document.getElementById("regap");
  const tipoid = document.getElementById("tipoid");
  const doc = document.getElementById("doc");
  const correo = document.getElementById("correo");
  const contacto = document.getElementById("contacto");
  const fechaNac = document.getElementById("fechanac");
  const fechaExp = document.getElementById("fechaexp");

  const campos = [
    nombre,
    apellido,
    tipoid,
    doc,
    correo,
    contacto,
    fechaNac,
    fechaExp,
  ];

  let hayError = false;

  
  campos.forEach((campo) => {
    if (
      !campo.value ||
      campo.value.trim() === "" ||
      campo.value === "Tipo de documento"
    ) {
      campo.classList.add("input-error");
      hayError = true;
    }
  });

  if (hayError) {
    Swal.fire({
      icon: "error",
      title: "Hay campos vacíos",
      text: "Por favor completa todos los campos",
    });
    return;
  }

  // VALIDAR CORREO
  const regex =
    /^[a-zA-Z0-9._%+-]+@(gmail|outlook|hotmail|yahoo)\.(com|es|co)$/;

  if (!regex.test(correo.value)) {
    correo.classList.add("input-error");
    Swal.fire("Correo inválido");
    return;
  }

  //  VALIDAR TELÉFONO
  if (isNaN(contacto.value) || contacto.value.length !== 10) {
    contacto.classList.add("input-error");
    Swal.fire("Número inválido (10 dígitos)");
    return;
  }

  if (doc.value.length < 6) {
    doc.classList.add("input-error");
    Swal.fire("Documento inválido");
    return;
  }
  //  VALIDAR FECHAS (18 años + 1 día)
  const fechaNacVal = fechaNac.value;
  const fechaExpVal = fechaExp.value;

  let nacimiento = new Date(fechaNacVal);
  let expedicion = new Date(fechaExpVal);

  let fechaMinExp = new Date(nacimiento);
  fechaMinExp.setFullYear(fechaMinExp.getFullYear() + 18);
  fechaMinExp.setDate(fechaMinExp.getDate() + 1);

  if (expedicion < fechaMinExp) {
    fechaExp.classList.add("input-error");
    Swal.fire(
      "La fecha de expedición no cumple (debe ser después de los 18 años)",
    );
    return;
  }

  //  GUARDAR
  const datos = {
    nombre: nombre.value,
    apellido: apellido.value,
    tipoDocumento: tipoid.value,
    documento: doc.value,
    correo: correo.value,
    telefono: contacto.value,
  };

  localStorage.setItem("datosRegistroTemp", JSON.stringify(datos));

  //  ALERTA + REDIRECCIÓN
  Swal.fire({
    icon: "success",
    title: "Datos guardados correctamente",
  });

  window.location.href = "../html/registro.html";
}

function cargarUsuarios() {
  const tabla = document.querySelector("#tablaUsuarios tbody");
  if (!tabla) return;

  tabla.innerHTML = "";

  todos.forEach((u) => {
    const perfil = JSON.parse(localStorage.getItem("perfil_" + u.user)) || {};

    const nivel = perfil.nivel || 1;

    const fila = `
      <tr onclick="verUsuario('${u.user}')">
        <td class="clickable">${u.user}</td>
        <td>Nivel ${nivel}</td>
      </tr>
    `;

    tabla.innerHTML += fila;
  });
}
// ================== VER USUARIO (NUEVA VISTA) ==================
function verUsuario(user) {
  localStorage.setItem("usuarioSeleccionado", user);
  window.location.href = "usuario.html";
}

// ================== CARGAR DETALLE USUARIO ==================
function cargarDetalleUsuario() {
  const user = localStorage.getItem("usuarioSeleccionado");
  if (!user) return;

  const perfil = JSON.parse(localStorage.getItem("perfil_" + user)) || {};
  const registro = JSON.parse(localStorage.getItem("datosRegistroTemp")) || {};

  document.getElementById("nombreUsuario").textContent = user;

  document.getElementById("correo").textContent =
    perfil.correo || registro.correo || "No definido";

  document.getElementById("telefono").textContent =
    perfil.telefono || registro.telefono || "No definido";

  document.getElementById("ciudad").textContent =
    perfil.ciudad || "No definida";

  document.getElementById("genero").textContent =
    perfil.genero || "No definido";

  document.getElementById("tipoDoc").textContent =
    registro.tipoDocumento || "No definido";

  document.getElementById("numeroDoc").textContent =
    registro.documento || "No definido";

  document.getElementById("estudio").textContent =
    perfil.estudio || "No definido";

  const nivel = perfil.nivel || 1;

  document.getElementById("nivelTexto").textContent = "Nivel " + nivel;

  const barra = document.getElementById("barraNivel");
  if (barra) {
    barra.style.width = (nivel / 10) * 100 + "%";
  }
}
// ===== INICIO SIMULADOR =====

function iniciarSimulacion() {
  const mensaje = document.getElementById("mensajeSimulacion");

  mensaje.textContent = "🚀 Preparando entorno de simulación...";
  
  setTimeout(() => {
    mensaje.textContent = "📊 Cargando datos del mercado...";
  }, 1500);

  setTimeout(() => {
    mensaje.textContent = "✅ Todo listo. Próximamente podrás invertir.";
  }, 3000);
}

// ===== MOSTRAR CONSEJOS =====
function mostrarConsejo() {
  const consejos = [
    "No inviertas todo en un solo activo.",
    "Diversificar reduce el riesgo.",
    "El mercado cambia constantemente.",
    "Invierte con estrategia, no por emoción.",
    "Aprende antes de arriesgar dinero."
  ];

  const random = Math.floor(Math.random() * consejos.length);

  document.getElementById("mensajeSimulacion").textContent =
    "💡 Consejo: " + consejos[random];
}
function verAnalisis() {
  const datos = [
    "📈 Mercado en tendencia alcista",
    "📉 Mercado bajista detectado",
    "⚠️ Alta volatilidad",
    "💰 Oportunidad de inversión detectada"
  ];

  const r = Math.floor(Math.random() * datos.length);

  document.getElementById("mensajeAnalisis").textContent = datos[r];
}
let paso = 0;

function siguienteLeccion() {
  const lecciones = [
    "Invertir es hacer crecer tu dinero.",
    "El riesgo siempre está presente.",
    "Diversificar es clave.",
    "Nunca inviertas sin conocimiento."
  ];

  if (paso < lecciones.length) {
    document.getElementById("mensajeFormacion").textContent = lecciones[paso];
    paso++;
  }
}
function mensajeSeguro() {
  document.getElementById("mensajeEntorno").textContent =
    "✅ Estás en un entorno completamente seguro para aprender.";
}
function verProgreso() {
  document.getElementById("mensajeSeguimiento").textContent =
    "Nivel: 2 | Simulaciones completadas: 3";
}
function cargarProgresoCurso(){

const progreso =
JSON.parse(
localStorage.getItem("curso")
) || {
leccion1:false,
leccion2:false,
leccion3:false,
leccion4:false
};


if(document.getElementById("mod2") && progreso.leccion1){

let mod2 = document.getElementById("mod2");

mod2.classList.remove("locked");
mod2.classList.add("unlocked");

mod2.innerHTML=`
<h3>Riesgo vs Ganancia</h3>
<p>Módulo desbloqueado</p>
`;

mod2.onclick=function(){
window.location.href="leccion2.html";
};

}


if(document.getElementById("mod3") && progreso.leccion2){

let mod3 = document.getElementById("mod3");

mod3.classList.remove("locked");
mod3.classList.add("unlocked");

mod3.innerHTML=`
<h3>Tipos de Activos</h3>
<p>Módulo desbloqueado</p>
`;

mod3.onclick=function(){
window.location.href="leccion3.html";
};

}


if(document.getElementById("mod4") && progreso.leccion3){

let mod4 = document.getElementById("mod4");

mod4.classList.remove("locked");
mod4.classList.add("unlocked");

mod4.innerHTML=`
<h3>Estrategias</h3>
<p>Módulo desbloqueado</p>
`;

mod4.onclick=function(){
window.location.href="leccion4.html";
};

}

actualizarBarraCurso(progreso);

}



// barra progreso visual
function actualizarBarraCurso(progreso){

let completadas=0;

if(progreso.leccion1) completadas++;
if(progreso.leccion2) completadas++;
if(progreso.leccion3) completadas++;
if(progreso.leccion4) completadas++;

let porcentaje=completadas*25;

let barra=
document.getElementById("barraCurso");

let texto=
document.getElementById("progresoTexto");

if(barra){
barra.style.width=
porcentaje+"%";
}

if(texto){
texto.innerText=
"Progreso del curso "+porcentaje+"%";
}

}



// aprobar lecciones
function aprobarLeccion(numero){

let progreso=
JSON.parse(
localStorage.getItem("curso")
)||{
leccion1:false,
leccion2:false,
leccion3:false,
leccion4:false
};

progreso["leccion"+numero]=true;

localStorage.setItem(
"curso",
JSON.stringify(progreso)
);


// sube nivel usando tu sistema actual
subirNivel();


Swal.fire({
icon:"success",
title:"Lección aprobada",
text:"Nuevo módulo desbloqueado"
}).then(()=>{
window.location.href="inicio.html";
});

}
function verVideo(){

window.open(
"https://www.youtube.com/watch?v=_UKYD6ybUEU",
"_blank"
);

}


function irQuiz(){

window.location.href=
"quiz1.html";

}
window.addEventListener("load",()=>{

const user=
localStorage.getItem("usuarioActivo");

if(user && document.getElementById("usuarioSesion")){
document.getElementById("usuarioSesion").innerText=user;
}

});

// ================== HEADER SCROLL ==================
window.addEventListener("scroll", () => {
  const header = document.querySelector("header");
  if (!header) return;

  if (window.scrollY > 50) {
    header.style.background = "rgba(10,10,10,0.95)";
  } else {
    header.style.background = "rgba(10,10,10,0.8)";
  }
});

// ================== GRAFICAS EN TIEMPO REAL ==================
document.addEventListener("DOMContentLoaded", () => {

  function crearGraficaEnVivo(
    canvasId,
    titulo,
    colorLinea,
    colorFondo,
    valorInicial,
    variacionMax
  ) {

    const canvas = document.getElementById(canvasId);

    if (!canvas) return;

    const etiquetas = [];
    const datos = [];

    let valorActual = valorInicial;

    const grafica = new Chart(canvas, {
      type: "line",

      data: {
        labels: etiquetas,

        datasets: [{
          label: titulo,
          data: datos,

          borderColor: colorLinea,
          backgroundColor: colorFondo,

          tension: 0.4,
          fill: true
        }]
      },

      options: {
        responsive: true,
        maintainAspectRatio: false,

        plugins: {
          legend: {
            labels: {
              color: "white"
            }
          }
        },

        scales: {
          x: {
            ticks: {
              color: "white"
            },

            grid: {
              color: "rgba(255,255,255,0.05)"
            }
          },

          y: {
            ticks: {
              color: "white"
            },

            grid: {
              color: "rgba(255,255,255,0.05)"
            }
          }
        }
      }
    });

    function actualizar() {

      const hora = new Date().toLocaleTimeString([], {
        hour: "2-digit",
        minute: "2-digit"
      });

      const cambio =
        Math.floor(Math.random() * variacionMax * 2)
        - variacionMax;

      valorActual = Math.max(1, valorActual + cambio);

      etiquetas.push(hora);
      datos.push(valorActual);

      if (etiquetas.length > 8) {
        etiquetas.shift();
        datos.shift();
      }

      grafica.update();
    }

    actualizar();

    setInterval(actualizar, 3000);
  }

  // BTC
  crearGraficaEnVivo(
    "btcChart",
    "BTC/USD",
    "#00c3ff",
    "rgba(0,195,255,0.15)",
    62000,
    800
  );

  // USD
  crearGraficaEnVivo(
    "usdChart",
    "USD/COP",
    "#00ffe7",
    "rgba(0,255,231,0.12)",
    3900,
    15
  );

});