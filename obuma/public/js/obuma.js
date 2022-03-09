jQuery(function() {

	/*
	if (document.getElementById("obuma_tipo_documento")) {
		document.getElementById("obuma_tipo_documento").addEventListener("change",function(){
			let tipo_documento = this.value;
			if (tipo_documento == "39") {
				document.getElementById("obuma_giro_comercial_field").style.display = "none";
				document.getElementById("obuma_giro_comercial").setAttribute("name","obuma_giro_comercial_hidden");
			}else{
				document.getElementById("obuma_giro_comercial_field").style.display = "block";
				document.getElementById("obuma_giro_comercial").setAttribute("name","obuma_giro_comercial");
			}
		
		});
	}
	*/

	if (document.getElementById("billing_tipo_documento") && document.getElementById("billing_company")) {
		let span = document.createElement("span")
		span.classList.add("nasa-error");
		span.innerHTML = "<strong>Facturación Nombre</strong> es un campo requerido."

		let id_tipo_documento = document.getElementById("billing_tipo_documento").value

		document.getElementById("billing_tipo_documento").addEventListener("change", function(event) {

			if (this.value == "33") {
				document.getElementById("billing_company").parentElement.parentElement.classList.add("validate-required")
				document.getElementById("billing_company").parentElement.previousElementSibling.innerHTML = 'Nombre de la empresa <abbr class="required" title="required">*</abbr>'
				document.getElementById("billing_company").insertAdjacentElement("afterend", span);

			} else {
				document.getElementById("billing_company").parentElement.parentElement.classList.remove("validate-required")
				document.getElementById("billing_company").parentElement.previousElementSibling.innerHTML = 'Nombre de la empresa (Opcional)'
				document.getElementById("billing_company").nextElementSibling.remove();
			}

		})

		if (id_tipo_documento == "33") {
			document.getElementById("billing_company").parentElement.parentElement.classList.add("validate-required")
			document.getElementById("billing_company").parentElement.previousElementSibling.innerHTML = 'Nombre de la empresa <abbr class="required" title="required">*</abbr>'
			document.getElementById("billing_company").insertAdjacentElement("afterend", span);
		} else {
			document.getElementById("billing_company").parentElement.parentElement.classList.remove("validate-required")
			document.getElementById("billing_company").parentElement.previousElementSibling.innerHTML = 'Nombre de la empresa (Opcional)'
			if (document.getElementById("billing_company").nextElementSibling) {
				document.getElementById("billing_company").nextElementSibling.remove();
			}
			
		}

	}

});