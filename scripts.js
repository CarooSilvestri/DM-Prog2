window.onload = function() {
  
  var $someshit = ["hola","marimar","rubi","teresa","valorant","valorant"];
  
  // Containers juegos online
  var $diag = document.getElementById("container-juegos-online");
  console.log($diag);

  $someshit.forEach(element => {

    let $cont_diag = document.createElement("div");
    $cont_diag.setAttribute("class", "container-diagonal");
    let $img_int = document.createElement("img");
    $img_int.setAttribute("src", "rsc/"+ element +".jpg");
    $cont_diag.appendChild($img_int);
    $diag.appendChild($cont_diag);
  });

  // compañias

  var $diag2 = document.getElementById("container-companias");
  console.log($diag2);

  $someshit.forEach(element => {

    let $i12 = document.createElement("div");
    $i12.setAttribute("class", "container-circular");
    let $i22 = document.createElement("img");
    $i22.setAttribute("src", "rsc/manzana.png");
    $i12.appendChild($i22);
    $diag2.appendChild($i12);
  });

 /* var $mendrp = document.getElementById("dropdown-content-comp");
  $mendrp.onmouseover = function (){

    $someshit.forEach(element => {

      let $link = document.createElement("li");
      let $ala = document.createElement("a");
      $ala.setAttribute("href", "#");
      $ala.innerHTML = element;
      $link.appendChild($ala);
      $link.style.listStyle = "none";
      $mendrp.appendChild($link);    
    });
  }*/


}
