window.onload = function() {
  
  var $someshit = ["hola","marimar","rubi","teresa","valorant","valorant"];
  
  var $someshit2 = ["hola","marimar","rubi","teresa","valorant","valorant","valorant","valorant"];
  
  // Containers juegos online
  var $diag = document.getElementById("container-juegos-online");

  $someshit.forEach(element => {

    let $container_juego = document.createElement("div");
    $container_juego.setAttribute("class", "container-juego-online");

    let $cont_diag = document.createElement("div");
    $cont_diag.setAttribute("class", "container-diagonal");

    let $img_int = document.createElement("img");
    $img_int.setAttribute("src", "rsc/"+ element +".jpg");
    $img_int.setAttribute("class", "img-juego-online");

    let $nombre_juego = document.createElement("div");
    $nombre_juego.setAttribute("class", "container-nombre-juego")
    $nombre_juego.innerHTML = element;

    $cont_diag.appendChild($img_int);
    
    $container_juego.appendChild($cont_diag);
    $container_juego.appendChild($nombre_juego);
    $diag.appendChild($container_juego);

  });

  // Container filtros

  var $diag2 = document.getElementById("container-filtros");

  $someshit2.forEach(element => {

    let $new_div_circle = document.createElement("div");
    $new_div_circle.setAttribute("class", "container-circular");
    let $img_icono = document.createElement("img");
    $img_icono.setAttribute("src", "rsc/manzana.png");
    $img_icono.setAttribute("width", "100px");
    $new_div_circle.appendChild($img_icono);
    $diag2.appendChild($new_div_circle);
  });

  // Container juegos
  var $diag3 = document.getElementById("container-juegos");

  let $modal = document.createElement("div");
  $modal.setAttribute("id", "modal");
  let $t = document.createElement("div");
  $t.setAttribute("class", "modal-content");
  let $p = document.createElement("span");
  
  $someshit2.forEach(element => {

    let $new_div_square = document.createElement("div");
    $new_div_square.setAttribute("class", "container-rectangular");
    
    let $img_juego = document.createElement("img");
    $img_juego.setAttribute("src", "rsc/manzana.png");
    $img_juego.setAttribute("width", "100px");
    
    let $overlay = document.createElement("div");
    $overlay.setAttribute("class", "container-rectangular-overlay");
    $overlay.innerHTML = '<p class="texto-overlay">'+{plataforma}+'</p><p class="texto-overlay">' + element +'</p>';  

    $overlay.onclick = function () {

      $p.innerHTML = element;
      $t.appendChild($p);
      $modal.appendChild($t);
      $modal.style.width = "block";
    $diag3.appendChild($modal);
    }

    $modal.onclick = function (){
        $modal.style.display = "none";
      
    }
    
    $new_div_square.appendChild($img_juego);
    $new_div_square.appendChild($overlay);
    $diag3.appendChild($new_div_square);
  });

}
