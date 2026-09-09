/**
 * SACAM — Validación de cliente (Sprint 1, frontend).
 *
 * Reglas del backlog que cubre este archivo:
 *   - Si falta un campo obligatorio, el formulario no se envía y muestra el
 *     error junto al campo (no una alerta genérica).
 *   - Si el correo no tiene formato válido, se rechaza con mensaje claro.
 *   - Si la imagen no es JPG/PNG o excede el tamaño máximo, se rechaza antes
 *     de intentar guardarse.
 *
 * Importante: esta validación es solo de experiencia de usuario. La
 * validación real y obligatoria ocurre en el servidor (tareas 6 y 8), porque
 * cualquier persona puede desactivar JavaScript o saltarse el navegador.
 */

(function () {
  'use strict';

  var TAMANO_MAXIMO_MB = 5;
  var TIPOS_IMAGEN_VALIDOS = ['image/jpeg', 'image/png'];

  document.addEventListener('DOMContentLoaded', function () {
    configurarEtiquetaIdentificador();
    configurarEtiquetaPlaca();
    configurarFormulariosValidados();
  });

  /* -----------------------------------------------------------------------
     Registro de usuario: el identificador cambia de nombre según el tipo
     de persona (boleta para alumnado, número de empleado para el resto).
     ----------------------------------------------------------------------- */
  function configurarEtiquetaIdentificador() {
    var selectorTipo = document.getElementById('tipo_persona');
    var etiqueta = document.getElementById('etiqueta_identificador');
    var ayuda = document.getElementById('ayuda_identificador');
    var campoInput = document.getElementById('identificador_institucional');
    if (!selectorTipo || !etiqueta || !ayuda || !campoInput) {
      return;
    }

    function actualizar() {
      if (selectorTipo.value === 'alumno') {
        etiqueta.textContent = 'Boleta';
        ayuda.textContent = 'Tu número de boleta, tal como aparece en tu credencial.';
        campoInput.setAttribute('placeholder', 'Ej. 2023630123');
      } else if (selectorTipo.value === '') {
        etiqueta.textContent = 'Boleta o número de empleado';
        ayuda.textContent = 'Selecciona primero tu tipo de persona.';
        campoInput.removeAttribute('placeholder');
      } else {
        etiqueta.textContent = 'Número de empleado';
        ayuda.textContent = 'Tu número de empleado del IPN.';
        campoInput.setAttribute('placeholder', 'Ej. E12345');
      }
    }

    selectorTipo.addEventListener('change', actualizar);
    actualizar();
  }

  /* -----------------------------------------------------------------------
     Registro de motocicleta: la placa cambia de nombre/ayuda si la moto
     todavía no tiene placa definitiva (permiso provisional).
     ----------------------------------------------------------------------- */
  function configurarEtiquetaPlaca() {
    var opciones = document.querySelectorAll('input[name="tipo_placa"]');
    var etiqueta = document.getElementById('etiqueta_placa');
    var ayuda = document.getElementById('ayuda_placa');
    if (!opciones.length || !etiqueta || !ayuda) {
      return;
    }

    function actualizar() {
      var seleccionado = document.querySelector('input[name="tipo_placa"]:checked');
      var esProvisional = seleccionado && seleccionado.value === 'permiso_provisional';
      etiqueta.textContent = esProvisional ? 'Número de permiso provisional' : 'Placa';
      ayuda.textContent = esProvisional
        ? 'Este registro quedará marcado como pendiente de actualizar hasta que captures la placa definitiva.'
        : 'Captura la placa tal como aparece en la tarjeta de circulación.';
    }

    opciones.forEach(function (opcion) {
      opcion.addEventListener('change', actualizar);
    });
    actualizar();
  }

  /* -----------------------------------------------------------------------
     Validación de los formularios del trámite.
     ----------------------------------------------------------------------- */
  function configurarFormulariosValidados() {
    var formularios = document.querySelectorAll('form.js-validar');
    formularios.forEach(function (formulario) {
      // Revalida un campo apenas la persona lo corrige, para que el error
      // desaparezca sin tener que volver a enviar el formulario.
      formulario.querySelectorAll('input, select').forEach(function (campo) {
        var evento = campo.type === 'file' || campo.tagName === 'SELECT' ? 'change' : 'input';
        campo.addEventListener(evento, function () {
          validarCampo(campo);
        });
      });

      formulario.addEventListener('submit', function (evento) {
        var camposRequeridos = formulario.querySelectorAll('[required]');
        var primerInvalido = null;

        camposRequeridos.forEach(function (campo) {
          var valido = validarCampo(campo);
          if (!valido && !primerInvalido) {
            primerInvalido = campo;
          }
        });

        if (primerInvalido) {
          evento.preventDefault();
          primerInvalido.focus();
        }
      });
    });
  }

  function validarCampo(campo) {
    var contenedor = campo.closest('.campo');
    var mensajeError = obtenerMensajeError(campo);
    mostrarError(campo, contenedor, mensajeError);
    return mensajeError === '';
  }

  function obtenerMensajeError(campo) {
    if (campo.type === 'checkbox') {
      return campo.required && !campo.checked
        ? 'Debes aceptar el aviso de privacidad para continuar.'
        : '';
    }

    if (campo.type === 'radio') {
      return '';
    }

    if (campo.required && campo.value.trim() === '') {
      return 'Este campo es obligatorio.';
    }

    if (campo.type === 'email' && campo.value.trim() !== '') {
      var patronCorreo = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!patronCorreo.test(campo.value.trim())) {
        return 'Escribe un correo electrónico válido, por ejemplo nombre@correo.com.';
      }
    }

    if (campo.type === 'file' && campo.required) {
      return validarArchivoImagen(campo);
    }

    return '';
  }

  function validarArchivoImagen(campo) {
    if (!campo.files || campo.files.length === 0) {
      return 'Sube una fotografía en formato JPG o PNG.';
    }

    var archivo = campo.files[0];
    if (TIPOS_IMAGEN_VALIDOS.indexOf(archivo.type) === -1) {
      return 'El archivo debe ser una imagen JPG o PNG.';
    }

    var tamanoMaximoBytes = TAMANO_MAXIMO_MB * 1024 * 1024;
    if (archivo.size > tamanoMaximoBytes) {
      return 'La imagen pesa más de ' + TAMANO_MAXIMO_MB + ' MB. Elige un archivo más ligero.';
    }

    return '';
  }

  function mostrarError(campo, contenedor, mensaje) {
    if (!contenedor) {
      return;
    }
    var espacioError = contenedor.querySelector('[data-error-para="' + campo.id + '"]')
      || contenedor.querySelector('.campo__error');

    if (espacioError) {
      espacioError.textContent = mensaje;
    }
    contenedor.classList.toggle('campo--invalido', mensaje !== '');
  }
})();
