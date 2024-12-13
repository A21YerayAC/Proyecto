
// document.getElementById('like-button').addEventListener('click', function() {
//   const reviewId = this.dataset.reviewId;

//   fetch(`/like/${reviewId}`, {
//       method: 'POST',
//       headers: {
//           'Content-Type': 'application/json',
//       },
//       body: JSON.stringify({}),
//   })
//   .then(response => response.json())
//   .then(data => {
//       if (data.success) {
//           // Actualizar la interfaz o mostrar notificaciones
//           alert('Like añadido');
//       } else {
//           alert('Error al añadir like');
//       }
//   })
//   .catch(error => {
//       console.error('Error:', error);
//   });
// });

document.getElementById('comment-form').addEventListener('submit', function (e) {
  e.preventDefault();

  const reviewId = this.dataset.reviewId;
  const content = this.querySelector('input[name="content"]').value;

  fetch(`/comment/${reviewId}`, {
      method: 'POST',
      headers: {
          'Content-Type': 'application/json',
      },
      body: JSON.stringify({ content }),
  })
      .then(response => response.json())
      .then(data => {
          if (data.success) {
            
              alert('Comentario añadido');
              window.location.reload();
              // Aquí podrías actualizar dinámicamente la lista de comentarios
          } else {
              alert('Error: ' + data.message);
          }
      })
      .catch(error => {
          console.error('Error:', error);
      });
});


document.addEventListener('DOMContentLoaded', () => {
    const likeButtons = document.querySelectorAll('.like-button');

    likeButtons.forEach(button => {
        button.addEventListener('click', async (event) => {
            event.preventDefault();
            const reviewId = button.getAttribute('data-review-id');

            const response = await fetch(`/review/${reviewId}/like`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
            });

            if (response.ok) {
                const data = await response.json();
                button.classList.toggle('liked', data.liked);
                const icon = button.querySelector('i');
                icon.classList.toggle('bi-hand-thumbs-up-fill', data.liked);
                icon.classList.toggle('bi-hand-thumbs-up', !data.liked);
            } else {
                console.error('Error al procesar el like.');
            }
        });
    });
});

document.querySelector('.borrar-comentario').addEventListener('submit', function (e) {
    e.preventDefault();

    const reviewId = this.dataset.reviewId;
    const content = this.querySelector('input[name="content"]').value;

    fetch(`/comment/delete/${reviewId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({}),
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Comentario eliminado');
                window.location.reload();  // Recargar la página
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
});
