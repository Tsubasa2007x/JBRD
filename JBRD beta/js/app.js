const admins = [
{ user: "Johann", pass: "Johan123" },
{ user: "Biggy", pass: "Biggy123" },
{ user: "Mono", pass: "Mono123" },
{ user: "Ricardo", pass: "Ricardo123" }
];

const usuariosBase = [
{ user: "Juan22", pass: "Juan123" },
{ user: "Maria33", pass: "Maria123" },
{ user: "Pedro44", pass: "Pedro123" }
];

let usuariosRegistrados = JSON.parse(localStorage.getItem("usuarios")) || [];
let usuarios = [...usuariosBase, ...usuariosRegistrados];

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

document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll("input, select").forEach(input => {

        const validarCampo = () => {
            const valor = input.value.trim();

            if (valor !== "") {
                input.classList.remove("input-error");
                input.classList.add("input-ok");
            } else {
                input.classList.remove("input-ok");
            }
        };

        input.addEventListener("input", validarCampo);
        input.addEventListener("change", validarCampo);
        input.addEventListener("blur", validarCampo);

        input.addEventListener("input", () => {
            input.classList.remove("input-error");
        });
    });

    // 🔥 VALIDACIÓN EN TIEMPO REAL DE FECHAS
    const fechaNace = document.getElementById("fechanac");
    const fechaExpe = document.getElementById("fechaexp");
    const errorFecha = document.getElementById("errorFecha");

    if (fechaNace && fechaExpe) {

        function validarFechas() {
            const fechaNac = fechaNace.value;
            const fechaExp = fechaExpe.value;

            if (!fechaNac || !fechaExp) return;

            let nacimiento = new Date(fechaNac);
            let expedicion = new Date(fechaExp);

            let fechaMinExp = new Date(nacimiento);
            fechaMinExp.setFullYear(fechaMinExp.getFullYear() + 18);
            fechaMinExp.setDate(fechaMinExp.getDate() + 1);

            if (expedicion < fechaMinExp) {
                fechaExpInput.classList.add("input-error");
                fechaExpInput.classList.remove("input-ok");
                errorFecha.style.display = "block";
            } else {
                fechaExpInput.classList.remove("input-error");
                fechaExpInput.classList.add("input-ok");
                errorFecha.style.display = "none";
            }
        }

        fechaNacInput.addEventListener("change", () => {
            let nacimiento = new Date(fechaNacInput.value);

            let fechaMinExp = new Date(nacimiento);
            fechaMinExp.setFullYear(fechaMinExp.getFullYear() + 18);
            fechaMinExp.setDate(fechaMinExp.getDate() + 1);

            let min = fechaMinExp.toISOString().split("T")[0];
            fechaExpInput.min = min;

            validarFechas();
        });

        fechaExpInput.addEventListener("input", validarFechas);
        fechaExpInput.addEventListener("change", validarFechas);
    }

});

// 🔹 REGISTRO
function registrar1() {

    const campos = [
        { id: "regnom", nombre: "Nombres" },
        { id: "regap", nombre: "Apellidos" },
        { id: "tipoid", nombre: "Tipo de documento" },
        { id: "doc", nombre: "Documento" },
        { id: "fechanac", nombre: "Fecha de nacimiento" },
        { id: "fechaexp", nombre: "Fecha de expedición" },
        { id: "correo", nombre: "Correo" },
        { id: "contacto", nombre: "Contacto" }
    ];

    for (let campo of campos) {
        const input = document.getElementById(campo.id);

        if (!input.value.trim()) {
            input.classList.add("input-error");
            input.focus();
            return;
        }
    }

    // 🔥 VALIDACIÓN FINAL DE FECHA
    const fechaNac = document.getElementById("fechanac").value;
    const fechaExp = document.getElementById("fechaexp").value;

    let nacimiento = new Date(fechaNac);
    let expedicion = new Date(fechaExp);

    let fechaMinExp = new Date(nacimiento);
    fechaMinExp.setFullYear(fechaMinExp.getFullYear() + 18);
    fechaMinExp.setDate(fechaMinExp.getDate() + 1);

    if (expedicion < fechaMinExp) {
        document.getElementById("fechaexp").classList.add("input-error");
        return;
    }

    const correo = document.getElementById("correo");
    if (!correo.value.includes("@") || !correo.value.includes(".")) {
        correo.classList.add("input-error");
        return;
    }
    const contacto = document.getElementById("contacto");
    if (isNaN(contacto.value)) {
        contacto.classList.add("input-error");
        return;
    }
    const datosUsuario = {
        nombre: regnom.value,
        apellido: regap.value,
        tipoid: tipoid.value,
        documento: doc.value,
        fechanac,
        fechaexp: fechaExp,
        correo: correo.value,
        contacto: contacto.value
    };

    localStorage.setItem("datosUsuario", JSON.stringify(datosUsuario));

    window.location.href = "registro.html";
}