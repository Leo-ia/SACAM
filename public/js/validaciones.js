(function () {
  'use strict';

  var TAMANO_MAXIMO_MB = 5;
  var TIPOS_IMAGEN_VALIDOS = ['image/jpeg', 'image/png'];

  document.addEventListener('DOMContentLoaded', function () {
    configurarEtiquetaIdentificador();
    configurarEtiquetaPlaca();
    configurarFormulariosValidados();
  });

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

  function configurarFormulariosValidados() {
    var formularios = document.querySelectorAll('form.js-validar');
    formularios.forEach(function (formulario) {
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
