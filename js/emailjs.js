emailjs.init('BfcAhjkvxkc-UUdVkjmsQ');  // Sustituye 'tu_user_id' con tu User ID

        // Agregar el evento de envío del formulario
        document.getElementById('contact-form').addEventListener('submit', function(event) {
            event.preventDefault();  // Prevenir el comportamiento predeterminado del formulario

            // Usar el método sendForm de EmailJS para enviar el formulario
            emailjs.sendForm('service_x2h9xpl', 'template_b9y1w3r', this)
                .then(function(response) {
                    console.log('Correo enviado exitosamente!', response);
                    alert('¡Mensaje enviado con éxito!');  // Personaliza este mensaje si lo deseas
                    document.getElementById('contact-form').reset();  // Limpiar el formulario después de enviar
                }, function(error) {
                    console.error('Error al enviar el correo:', error);
                    alert('Error al enviar el mensaje. Intenta nuevamente.');
                });
        });