// Utiles.php | Versión 1.01 | @author(Carlos F. Gorosito|carlos.gorosito@gmail.com) | Sea libre de utilizar como desee
    U = typeof(window) === "undefined" ? global : window ;
    App = U

    Object.defineProperty(
        Object.prototype, "tipo"
        , {
            value: function() {
                return Object.prototype.toString.call( this )
            }
            , enumerable: false
        }
    ) ;
    
    App.indefinido = "-"
    App.Nulo        = null ;
    App.Indefinido  = undefined ;
    App.NoEsUnNumero= NaN ;
    App.Infinito    = Infinity ;

    App.C           = {} ;
        C.ENTER         = "\n"
        C.TAB           = "\t"
        C.DOSPUNTOS     = ":"
        C.PUNTO         = "."
        C.PUNTOPUNTO    = ".."
        C.COMA          = ","
        
    App.SI              = {}
        SI.ACEPTO       = true
        SI.AGREGAR      = true
        SI.CANCELAR     = false
        SI.LOHIZO       = true
        SI.LOHICIERON   = true
        
    App.NO              = {}
        NO.ACEPTO       = false
        NO.AGREGAR      = false
        NO.CANCELAR     = true
        NO.LOHIZO       = false
        NO.LOHICIERON   = false
        
// ----------------------------------------------------[ Funciones útiles ]
    U.fnArray = function( dadoObjetoIterable ) {
        return Array.prototype.slice.call( dadoObjetoIterable )
    }
    
    U.fnTipo = function( dadoObjetoIterable ) {
        return Object.prototype.toString.call( dadoObjetoIterable ).slice( 8, -1 )
    }

// ----------------------------------------------------[ Hacks ]

    U.Numero = Number ;         U.Numero_ = Number.prototype ;
    U.Frase = String ;          U.Frase_ = String.prototype ;
    U.Verdad = Boolean ;        U.Verdad_ = Boolean.prototype ;
    U.Lista = Array ;           U.Lista_ = Array.prototype ;
    U.Ficha = Object ;          U.Ficha_ = Object.prototype ;
    
    Verdad_.o = function( x ) { return this || x ; }
    Verdad_.y = function( x ) { return this && x ; }
    Verdad_.negado = Verdad_.negada = Verdad_.no = function() { return !this }
    Verdad_.si = function( x, y ) {
        if( this ) return x ;
        else return y ;
    }
    
    Numero_.dividido = function( divisor ) {
        return {
            cociente: this / divisor
            , entero: (this/divisor).entero()
            , resto: this % divisor
        }
    }
    
    Numero_.generarLista = function( valor ) {
        p = [] ;
        for(var i = 0 ; i < this.absoluto() ; i++ )
            p.push( valor.tipo() == "[object Function]" ? valor(i) : valor === undefined ? i : valor ) ;
        return p ;
    }
    
    Frase_.extraerNumeros = function(i) {
        if( i ) {
            return this.replace( /(\,+\s)/g, "___COMA___" ).replace(/\,/g,".").extraerNumeros()
        }
        return this.match( /[+-]?\d+(?:\.\d+)?/g ).map(function(n){
            return n.aNumero()
        })
    }
    
    Numero_.entero = function() { return this < 0 ? Math.ceil( this ) : Math.floor( this ) ; }
    Numero_.aleatorio = function() {
        return Math.random() * this ;
    }
    Numero_.completar = function() {
        $ = [] ;
        for( i = 0 ; i < this ; i++ ) $.push(i) ;
        return $ ;
    }
    Numero_.entre = function( a, b, debil ) {
        a = a || 0 ;
        b = b || 0 ;
        return a < this && this < b ;
    }
    Numero_.rango = function( paso ) {
        n = Math.abs(this) ;
        l = [] ;
        for( var i = 0 ; i < n ; i+=paso ){
            l.push(i) ;
        }
        return l ;
    }
    Lista_.cualquiera = function() { return this[ this.length.aleatorio().entero() ] ; }

// ----------------------------------------------------[ Herramientas para procesar datos ]
    App.metodoDelimitadores = /{(.*?)}/g

    function Procesar( $Frase, $cnDatos, $delimitadores ) {
        $cnDatos = $cnDatos || {} ;
        $$ = $Frase.replace( 
              $delimitadores  || metodoDelimitadores
            , function(a,b) { 
                c = "" ;
                if( b.includes("[") ) {
                    c = ( 
                        b.replace(/\[(.*?)\]/g, function(a,b){ 
                            return "$cnDatos['"+b+"']" 
                        } )
                        .replace(/&lt;/g, "<")
                        .replace(/&gt;/g, ">")
                    )
                    if( c.trim() != "" ) c = eval(c)
                }
                else {
                    c = $cnDatos[b]==undefined ? App.indefinido : $cnDatos[b]
                }
                //console.log(c)
                return c
                
            } 
        )
        return $$
    }

/*    function Procesar( $texto, $datos, $metodo ) {
        if( $texto.indexOf( "[" ) > -1 ) {
            var $ = $texto.replace( $metodo || metodoDelimitadores, function(aa, bb) {
                return bb.replace( /\[(.*?)\]/g, function( a, b ) {
                    var $$ = eval("($datos['"+b+"'])")
                    if( $$ == undefined ) return window.indefinido ;
                    return $$ ;
                })
            })
            return $
        }
        else {
            return $texto.replace( $metodo||metodoDelimitadores, function( a, individual ) {
                return $datos[ individual ] === undefined ? window.indefinido : $datos[ individual ] ;
                }
            )
        }
    }*/
    String.prototype.formato = function( $datos, $metodo ) { // HACK
        return Procesar( this, $datos, $metodo )
    }
    String.prototype.sin = function( $n ) {
        return this.slice( 0, - ($n || 1 ))
    }
    
    function ComoFicha( $str, $sepOut, $sepIn ) {
        var $ = $str.split( $sepOut || /\;|\,/ ) ;
        var F = {} ;
        for( var i in $ ) {
            try {
            var $$ = $[i].split( $sepIn || /:|\s/ ) ;
            try{ F[ $$[0] ] = $$[1] ; } catch($e){} ;
            } catch($e){}
        }
        return F ;
    }
    String.prototype.esFicha = function( $sepOut, $sepIn ) {
        return ComoFicha(this,$sepOut||" ",$sepIn||":") ;
    }

// ----------------------------------------------------[ Parse CSV ]

    CSV = {} ;
    
    CSV.raw = function( $texto, $sepIn ) {
        $sepIn = $sepIn || "\t" ;
        $renglones = $texto.split( "\n" ) ;
        $renglonesLista = [] ;
        for( $i = 0 ; $i < $renglones.length ; $i++ ) {
            $renglon = $renglones[$i].split( $sepIn ) ;
            $ultimo = $renglon.length-1 ;
            if( $renglon.length > 0 ) 
                $renglon[ $ultimo ] = $renglon[$ultimo].trim() 
            ;
            $renglonesLista.push( $renglon ) ;
        }
        return $renglonesLista ;
    }
    
    CSV.raw_json = function( $listaDeLista, $sepIn, $conID ) {
        $titulos = $listaDeLista[0] ;
        $json = [] ;
        for( var i = 1 ; i < $listaDeLista.length ; i++ ) {
            $estaFicha = {} ;
            if( $conID ) $estaFicha["id"] = i-1 ;
            for( var j = 0 ; j < $titulos.length ; j++ ) {
                $estaFicha[ $titulos[j] ] = $listaDeLista[i][j] ;
            }
            $json.push( $estaFicha ) ;
        }
        return $json ;
    }

// ----------------------------------------------------[  Para los números ]
    
    function Aleatorio(_min, _max) {
        return Math.random() * (_max-_min) + _min
    }
    
    function Entero( $numero ) {
        return Math.floor( $numero )
    }
    

// ----------------------------------------------------[  En() y Cada() ]    
    
    U.En = function( $objetoONumero ) {
        if( /Number|String/.test( Object.prototype.toString.call($objetoONumero) ) ) {
            return {
                segundos: function( dadaFn ) {
                    return setTimeout( dadaFn, $objetoONumero*1000 ) ;
                }
                , segundo: function( dadaFn ) {
                    return setTimeout( dadaFn, $objetoONumero*1000 ) ;
                }
                , ms: function( dadaFn ) {
                    return setTimeout( dadaFn, $objetoONumero ) ;
                }
                , milisegundos: function( dadaFn ) {
                    return setTimeout( dadaFn, $objetoONumero ) ;
                }
            }
        }
        return $objetoONumero
    }
    U.Cada = function( $objetoONumero ) {
        if( fnTipo( $objetoONumero ) == "Number" || fnTipo( $objetoONumero ) == "String" ) {
            return {
                segundos: function( dadaFn ) { return setInterval( dadaFn, $objetoONumero*1000 )}
                ,segundo: function( dadaFn ) { return setInterval( dadaFn, $objetoONumero*1000 )}
                , ms: function( dadaFn ) { return setInterval( dadaFn, $objetoONumero )}
                , milisegundos: function( dadaFn ) { return setInterval( dadaFn, $objetoONumero )}
            }
        }
        return fnArray( $objetoONumero )
    }
    
    U.Ahora = function( $n, $b ) {
        function h10( $n ) {
            return ( $n < 10 ) ? ("0"+$n) : $n ;
        }
        var $ = $b ? new Date($b) : new Date() ;
        var dsemana = $.getDay() ;
        var $$ = {
            hora: h10( $.getHours() )
            , minutos: h10( $.getMinutes() )
            , segundos: h10( $.getSeconds() )
            , ms: h10( $.getMilliseconds() )
            , "año": $.getFullYear()
            , mes: h10( $.getMonth()+1 )
            , "día": h10( $.getDate() )
            , dsemana: dsemana
            , semanal: "Domingo.Lunes.Martes.Miércoles.Jueves.Viernes.Sábado".split(".")[dsemana]
            , sem: "Do.Lu.Ma.Mi.Ju.Vi.Sá".split(".")[dsemana]
        }
        if( $n == 256 ) {
            return "{año}{mes}{día}.{hora}{minutos}{segundos}{dsemana}".formato($$) ;
        }
        if( $n == 128 ) {
            return "{dsemana}.{hora}{minutos}{segundos}".formato($$) ;
        }
        if( $n == 64 ) {
            return "{hora}:{minutos}:{segundos}".formato($$) ;
        }
        return $$ ;
    }
    
// ----------------------------------------------------[ Hacks ]

    Number.prototype.cualquiera = function() { // HACK
        return Aleatorio(0, this)
    }
    Number.prototype.entero = function() { // HACK
        return Entero(this) ;
    }
    String.prototype.aNumero = function() { // HACK
        return parseFloat( this ) ;
    }
    Array.prototype.cualquiera = function() { // HACK
        $elegido = this.length.cualquiera().entero() ;
        return this[ $elegido ]
    }
    Frase_.comoFicha = function( $sepOut, $sepIn ) { // HACK
        return ComoFicha( this, $sepOut, $sepIn ) ;
    }
    Lista_.ultimo  = Lista_.ultima = function() {
        if( this.length ) return this[ this.length - 1 ] ;
        return undefined ;
    }
    Lista_.primer = Lista_.primero = Lista_.primera = function() {
        return this[0] ;
    }
    
    Numero_.decimales = function( n ) {
        if( !n ) n = 2 ;
        return parseFloat( this.toFixed(n) ) ;
    }
    
    Numero_.elevadoA = function( potencia ) {
        if( !potencia ) potencia = 1 ;
        return Math.pow( this, potencia )
    }
    
    Numero_.raiz = function( num ) {
        if( !num ) num = 2 ;
        return Math.pow( this, 1/num ) ;
    }
    
    Numero_.absoluto = function() {
        return this < 0 ? -this : this ;
    }
    
    Verdad_.decir = function() {
        console.log( this ? "Sí" : "No" ) ;
    }
    
    Verdad_.frase = function($) {
        if( $ ) return (this)+"" ;
        return this ? "sí" : "no" ;
    }
    
    Verdad_.numero = function() {
        return this ? 1 : 0 ;
    }
    
    Verdad_.negado = Verdad_.negada = Verdad_.no = Numero_.negado = function() {
        return !this ;
    }
    
    Numero_.verdad = function () {
        return !!this ;
    }
    
    Frase_.mayus = Frase_.toUpperCase ;
    Frase_.minus = Frase_.toLowerCase ;
    Frase_.izq = Frase_.hasta  = function( n ) {
        if( n ) return this.substr( 0, n ) ;
    }
    Frase_.der   = function( n ) {
        if( n ) return this.substr( this.length-n ) ;
    }
    Frase_.desde = function( n ) {
        if( n ) return this.substr( n ) ;
    }
    
/*    Frase_.patron = function( m ) {
        return new RegExp( this, m ) ;
    }
    Frase_.patronGlobal = function( m ) {
        if(!m) = "" ;
        return (new RegExp( this, "g"+m ) );
    }
    
    Frase_.seguido = function( m ) {
        return this + m ;
    }
    Frase_.precedido = function( m ) {
        return m + this ;
    }*/
    
    /*Ficha_.cualquiera = function($) {
        var n = Object.keys( this ).cualquiera() ;
        if( typeof $ == "object" ) {
            return {
                clave: n
                , valor: this[n]
            }
        }
        if( typeof $ == "array" ) {
            return [ n, this[n] ] ;
        }
        if( typeof $ == "string" ) {
            return this[n].toString() ;
        }
        else return this[n] ;
    }*/
    
    Numero_.veces = function( fnRepetir, a ) {
        if( typeof fnRepetir == "function" ) {
            if( this < 0 ) {
                for( var i = 0 ; i > this.entero() ; i-- ) fnRepetir( i, this.entero(), this ) ;
            }
            else {
                for( var i = 0 ; i < this.entero() ; i++ ) fnRepetir( i, this.entero(), this ) ;
            }
        }
        else {
            var s = [] ;
            for( var i = 0 ; i < this.absoluto() ; i++ ) s.push( fnRepetir ) ;
            return s.join(a||"") ;
        }
    }
    
    Lista_.iterar = Lista_.forEach ;
    
    Lista_.mayor = function() {
        var masGrande = this[0] ;
        for( var i = 1 ; i < this.length ; i++ ) {
            if( this[i] > masGrande ) masGrande = this[i] ;
        }
        return masGrande ;
    }
    
    Lista_.menor = function() {
        var masChico = this[0] ;
        for( var i = 1 ; i < this.length ; i++ ) {
            if( this[i] < masChico ) masChico = this[i] ;
        }
        return masChico ;
    }
    
    U.si = function( a, b, c ) {
        return (!!a).entonces(b,c) ;
    }

// ----------------------------------------------------[ Funciones con frases ]
    U.extraer = function( dadoTextoOriginal, dadoCoordinador ) {
        var R = [ [], [], [], [], [], [], [], [], [], [] ] ;
        for( var i = 0 ; i < dadoTextoOriginal.length ; i++ ) {
            switch ( dadoCoordinador[i] && dadoCoordinador[i].aNumero() ) {
                case 0: case 1: case 2: case 3: case 4: case 5: case 6: case 7: case 8: case 9:
                    R[ dadoCoordinador[i].aNumero() ].push( dadoTextoOriginal[i] ) ;
            }
        }
        R = R
            .map( function( cadaItem ){ return cadaItem.join("")} )
            .filter( function( cadaItem ){ return !!cadaItem } ) 
        ;
        return R ;
    }

    Frase_.rellenar = function( cantidad, conQue ) {
        if( this.length < cantidad ) {
            falta = cantidad - this.length ;
            s = this ;
            for(var i = 0 ; i < falta ; i++ ) s+=conQue ;
            return s ;
        }
        else return s ;
    }

    Frase_.html = function( $tagName, $atributos, $class ) {
        return "<{tag} {atributos} class='{$class}' >{contenido}</{tag}>"
            .formato({
                tag: $tagName
                , $class: $class
                , contenido: this
                , atributos: (function(){
                        frase = "" ;
                        for( var i in $atributos ) {
                            frase+= " "+i+"='"+$atributos[i]+"' " ;
                        }
                        return frase ;
                    })()
            })
    }
    Frase_.htm = function( $atributos, $class ) {
        return "<{tag} {atributos} class='{$class}' />"
            .formato({
                tag: this
                , $class: $class
                , atributos: (function(){
                        frase = "" ;
                        for( var i in $atributos ) {
                            frase+= " "+i+"='"+$atributos[i]+"' " ;
                        }
                        return frase ;
                    })()
            })
    }
    
    Lista_.unicos = function() {
        return this.filter(function(value,index,self){
            return self.indexOf(value) === index;
        })
        /*var o = {} ;
        for( var i = 0 ; i < this.length ; i++ ){
            o[ this[i] ] = 1 ;
        }
        var r = [] ;
        for(var i in o) r.push(i) ;
        return r ;*/
    }
    
    Lista_.aJson = function( theField ) {
        d = {} ;
        this.iterar(function(x){
            d[x[theField]] = x ;
        })
        return d ;
    }
    
    // https://stackoverflow.com/questions/6132796/how-to-make-a-jsonp-request-from-javascript-without-jquery
    var $jsonp = (function(){
      var that = {};
    
      that.send = function(src, options) {
        var callback_name = options.callbackName || 'callback',
          on_success = options.onSuccess || function(){},
          on_timeout = options.onTimeout || function(){},
          timeout = options.timeout || 10; // sec
    
        var timeout_trigger = window.setTimeout(function(){
          window[callback_name] = function(){};
          on_timeout();
        }, timeout * 1000);
    
        window[callback_name] = function(data){
          window.clearTimeout(timeout_trigger);
          on_success(data);
        }
    
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.async = true;
        script.src = src;
    
        document.getElementsByTagName('head')[0].appendChild(script);
      }
    
      return that;
    })();
    
    U.Ajax = {} ;
    Ajax.jsonp = function( $nombreCallbackFunction, $url, $siOk, $siNo ) {
        $jsonp.send($url, {
            callbackName: $nombreCallbackFunction
            , onSuccess: $siOk
            , onTimeout: $siNo
            , timeout: 5
        })
    }
    Ajax.general = function( $formato, $url, $metodo, $siOk, $siNo ) {
        $siOk = $siOk || function($) { return $ ; }
        if( typeof window === "undefined" ) {
            X = require( "https" ) ;
            X[$metodo.minus()]( $url, function( respuesta ) {
                datosRecibidos = "" ;
                respuesta.on( "data", function( partes ) { datosRecibidos+= partes })
                respuesta.on( "end", function() {
                    if( $formato.minus() == "json" ) {
                        $siOk( JSON.parse( datosRecibidos ) ) ;
                    }
                    else {
                        CSV.raw_json( 
                            CSV.raw(
                                this.responseText
                                , $formato=="tsv" ? "\t":","
                            ) 
                        )                    
                    }
                })
            })
            .on( "error", function( hayError ) {
                $siNo( hayError )
            })
        }
        else {
            var X = new XMLHttpRequest() ;
            X.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    $siOk( 
                        $formato == "json" ? JSON.parse(this.responseText) :(
                        $formato == "tsv" ? CSV.raw_json(CSV.raw(this.responseText,"\t")) :(
                        $formato == "csv" ? CSV.raw_json(CSV.raw(this.responseText,",")) :(
                        this.responseText
                        )))

                    );
                }
                else {
                    if( (!!$siNo).y( typeof $siNo == "function" ) )
                        $siNo( this ) 
                    ;
                }
            };
            X.open( $metodo||"GET", $url, true ) ;
            X.send() ;
        }
    }
    Ajax.get = function( $formato, $url, $siOk, $siNo ) {
        Ajax.general( $formato, $url, "GET", $siOk, $siNo ) ;
    }
    Ajax.post = function( $formato, $url, $datos, $siOk, $siNo ) {
        $siOk = $siOk || function($) { return $ ; }
        if( typeof window === "undefined" ) {
            X = require( "https" ) ;
            X[$metodo.minus()]( $url, function( respuesta ) {
                datosRecibidos = "" ;
                respuesta.on( "data", function( partes ) { datosRecibidos+= partes })
                respuesta.on( "end", function() {
                    if( $formato.minus() == "json" ) {
                        $siOk( JSON.parse( datosRecibidos ) ) ;
                    }
                    else {
                        CSV.raw_json( 
                            CSV.raw(
                                this.responseText
                                , $formato=="tsv" ? "\t":","
                            ) 
                        )                    
                    }
                })
            })
            .on( "error", function( hayError ) {
                $siNo( hayError )
            })
        }
        else {
            var X = new XMLHttpRequest() ;
            X.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    console.log(this.responseText)
                    $siOk( 
                        $formato == "json" ?
                        JSON.parse(this.responseText) 
                        :
                        CSV.raw_json( CSV.raw(this.responseText, $formato=="tsv" ? "\t":",") )
                        
                    );
                }
                else {
                    if( (!!$siNo).y( typeof $siNo == "function" ) )
                        $siNo( this ) 
                    ;
                }
            };
            X.open( "POST", $url, true ) ;
            X.send($datos) ;
        }
    }
    Ajax.gsheets = function( $formato, $idDocumento, $siOk, $siNo ) {
        Ajax.get( 
            $formato||"csv"
            , "https://docs.google.com/spreadsheets/d/e/{0}/pub?gid=0&single=true&output={1}".formato([$idDocumento,$formato||"csv"])
            , $siOk,$siNo 
        );
    }

    Date.prototype.diferencia = function( otraFecha ) {
        utcEste = this.getTime();
        utcOtro = otraFecha.getTime() ;
        var resta = utcEste - utcOtro ;
        return {
            value: parseInt( resta / (24*3600*1000) )
            , dias: parseInt( resta / (24*3600*1000) )
            , semanas: parseInt( resta / (24*3600*1000*7) )
            , meses: ( utcOtro.getMonth() + 12*utcOtro.getFullYear() ) -
                ( utcEste.getMonth() + 12*utcEste.getFullYear() )
            , "años": utcOtro.getFullYear() - utcEste.getFullYear()
        }
    }

    // --------------------------------------------[ Constantes strings ] //

    var __CONTENIDO = "innerHTML"
    var __TIENEATRIBUTO = "hasAttribute"
    var __LEERATRIBUTO = "getAttribute"
    var __CAMBIARATRIBUTO = "setAttribute"
    const ID = "id"
    const CLASE = "clase"
    const ATRIBUTO = "atributo"
    const PRIMER = "primer"
    const TODOS = "todos"
    const CADA = "cada"
    const CONTENIDO = "contenido"
    const SIGUIENTEHERMANO = "nextSibling"
    const ANTERIORHERMANO = "previousSibling"
    const HERMANO = "nextSibling"

// ----------------------------------------------------[ Buscador ]
    U.Buscar = {
        id: function( dadoNombre, dadoOrigen ) {
            return (dadoOrigen||document).getElementById( dadoNombre )
        }
        , atributo: function( dadoNombre, dadoOrigen ) {
            return fnArray( (dadoOrigen||document).querySelectorAll( "["+dadoNombre+"]" ) )
        }
        , primer: function( dadoCSS, dadoOrigen ) {
            var $ = (dadoOrigen||document).querySelector( dadoCSS )
            if( $ )
            $.suContenido = function( texto ) {
                if( arguments.length == 0 ) return $.innerHTML ;
                else $.innerHTML = texto
            }
            return $
        }
        , todos: function( dadoCSS, dadoOrigen ) {
            var R = fnArray( (dadoOrigen||document).querySelectorAll( dadoCSS ) ) ;
            Object.defineProperty(
                R
                , "cuando"
                , {
                    value: function( nombreEvento, accionesCU ) {
                        this.forEach( function(cadaR) {
                            cadaR.addEventListener(nombreEvento, accionesCU, false) ;
                        } )
                    }
                    , enumerable: false
                }
            ) ;
            return R ;
        }
        , contenido: function( dadoTexto, dadoOrigen ) {
            var $ = fnArray( (dadoOrigen||document).querySelectorAll( "*" ) )
            for( var i in $ ) {
                if( $[i].innerHTML.trim().toUpperCase() == dadoTexto.trim().toUpperCase() )
                return $[i]
            }
            return null ;
        }
        , hermano: function( dadoTexto, orden ) {
            if( orden < 0 ) 
                return dadoTexto.previousSibling ;
            if( orden > 0 ) 
                return dadoTexto.nextSibling ;
            return dadoOrigen ;
        }
    }
    Buscar.cada = Buscar.todos
    
    U.B = function( $metodo ) {
        if( $metodo == 1 ) $metodo = "primer" ;
        if( arguments.length == 0 ) $metodo = "todos" ;
        return function( $nombre ) {
            return function( $origen ) {
                return Buscar[$metodo]( $nombre, $origen )
            }
        }
    }
    
    U.Primer = U.Primera = U.Primero = Buscar.primer ;
    U.Todos = U.Todas = Buscar.todos ;
    
    App.formulario = function( $objeto ) {
        return {
            borrar: function() {
                items = B()("[name]")($objeto) ;
                items.iterar(function(named){
                    named.value = "" ;
                });
                itemsAF = B(1)("[autofocus]")($objeto) ;
                if( itemsAF ) {
                    itemsAF.focus() 
                }
                else if( items[0] ) {
                    items[0].focus() ;
                }
            }
            , limpiar: function() {
                items = B()("[name]")($objeto) ;
                items.iterar(function(named){
                    named.value = "" ;
                });
                items[0] && items[0].focus() ;
            }
            , faltan: function() {
                items = B()("[name][required]")($objeto) ;
                $falta = false ;
                items.iterar(function(cadaItem){
                    $faltaEsteItem = cadaItem.value == "" ;
                    if( $faltaEsteItem ) cadaItem.focus() ;
                    $falta = $falta || $faltaEsteItem ;
                    return $falta ;
                })
                return $falta ;
            }
        }
    }
    
// ----------------------------------------------------[ Reactivos ]
    function Reactivo( $objeto ) {
        return {
            buscar: function( $modelo ) {
                todosLosReactivos = Buscar.todos("["+($modelo || "modelo")+"]") ;
                todosLosReactivos.forEach( function( cadaReactivo) {
                    if( cadaReactivo.getAttribute("modelo") )
                    cadaReactivo.id = cadaReactivo.getAttribute("modelo") ;
                    cadaReactivo.modelo = cadaReactivo[__CONTENIDO]
                    cadaReactivo[__CONTENIDO] = ""
                    if( cadaReactivo[__TIENEATRIBUTO]("si-vacio") ) {
                        var sv = cadaReactivo[__LEERATRIBUTO]("si-vacio") ;
                        if( sv[0] == "#" ) {
                            cadaReactivo.siVacio = Buscar.id( sv.substr(1) ).innerHTML || "" ;
                        }
                        else {
                            cadaReactivo.siVacio = sv ;
                        }
                        cadaReactivo[__CONTENIDO] = cadaReactivo.siVacio
                    }
                    else if( cadaReactivo[__TIENEATRIBUTO]("siempre") ) {
                        var s = cadaReactivo[__LEERATRIBUTO]("siempre") ;
                        if( s[0] == "#" ) {
                            cadaReactivo.siempre = Buscar.id( s.substr(1) ).innerHTML || "" ;
                        }
                        else {
                            cadaReactivo.siempre = s ;
                        }
                        cadaReactivo[__CONTENIDO] = cadaReactivo.siempre ;
                    }
                    else cadaReactivo[__CONTENIDO] = "";
                    cadaReactivo[__CAMBIARATRIBUTO]("inicializado", true)
                    cadaReactivo.vacio = true ;
                    cadaReactivo.datos = [] ;
                    cadaReactivo.ultimo = {} ;
                } )
                todosLosReacciona = Buscar.todos("[reacciona]") ;
                todosLosReacciona.forEach( function(cadaReacciona){
                    $f = ComoFicha( cadaReacciona[__LEERATRIBUTO]("reacciona") ) ;
                    for( var i in $f ) {
                        if( $f[i].indexOf("#") > -1 )
                            cadaReacciona["on"+i] = new Function( "Buscar.id('"+$f[i].substr(1)+"').innerHTML = this.value" ) ;
                        else
                        cadaReacciona["on"+i] = new Function( $f[i] + "(this)" ) ;
                    }
                })
            }
            , actualizar: function( $datos ) {
                $objeto.innerHTML = $objeto.siempre || "" ;
                $objeto.innerHTML += $objeto.modelo.formato( $datos ) ;
                $si = Buscar.cada("[si]", En($objeto)) ;
                for( var i = 0 ; i < $si.length ; i++ ) {
                    if( 
                       ! eval( "(" + $si[i][__LEERATRIBUTO]("si").replace( /\[(.*?)\]/g, function(x,b){return "$datos['"+b+"']"} ) + ")")
                    ) $si[i].outerHTML = "" ;
                }
                $objeto.vacio = false
                $objeto.datos = [ $datos ]
            }
            , agregar: function( $datos ) {
                if( $objeto.vacio ) {
                    $objeto.vacio = false ;
                    $objeto[__CONTENIDO] = $objeto.siempre || "" ;
                }
                $objeto[__CONTENIDO] += $objeto.modelo.formato( $datos ) ;
                $objeto.datos.push( $datos ) ;
                $objeto.ultimo = $datos ;
            }
            , lista: function( $listaDatos, $esParaAgregar ) {
                if( $objeto.vacio ) {
                    $objeto[__CONTENIDO] = ""
                    $objeto.vacio = false
                }
                if( 0 ){//!$objeto.modelo ) {
                    $objeto.modelo = $objeto.innerHTML ;
                    $objeto.setAttribute( "inicializado", 1 ) ;
                    $objeto.datos = [] ;
                }
                if( !$esParaAgregar ) $objeto.datos = [] ;
                var $o = $objeto.siempre || "" ;
                for( var i = 0 ; i < $listaDatos.length ; i++ ) {
                    $l = $listaDatos[i] ;
                    $l.$orden = i ;
                    $l.$total = $listaDatos.length ;
                    s = Procesar( $objeto.modelo, $l, /{(.*?)}/g ) ;
                    $o += s
                    $objeto.datos.push( $listaDatos[i] ) ;
                    $objeto.ultimo = $listaDatos[i] ;
                }
                if( $esParaAgregar )
                    $objeto.innerHTML += $o ;
                else
                    $objeto.innerHTML = $o ;
            }
            , ficha: function( $ficha, $esParaAgregar ) {
                var $o = [] ;
                for( var i in $ficha ) {
                    $o.push( { clave: i, valor: $ficha[i] }  )
                }
                Reactivo($objeto).lista( $o, $esParaAgregar ) ;
                $objeto.ficha = $ficha ;
            }
            , borrar: function( $seguridad1, $seguridad2 ) {
                if( $seguridad1 === SI.BORRAR && $seguridad2 == SI.BORRAR  ) {
                    $objeto.innerHTML = $objeto.siVacio || "" ;
                    $objeto.vacio = true ;
                    $r = $objeto.datos ;
                    $objeto.datos = [] ;
                    $objeto.ultimo = {} ;
                    return $r ;
                }
            }
            , formulario: function( $dadoForm, $esParaAgregar ) {
                var datos = Buscar.cada( "[name]", En($dadoForm) ) ;
                var $ = {}
                for( var i in datos ) {
                    $[ datos[i].name ] = datos[i].value ;
                }
                if( $esParaAgregar ) Reactivo( $objeto ).agregar( $ ) ;
                else Reactivo( $objeto ).actualizar( $ ) ;
            }
            , storage: function( $codigo, $esParaRecuperar, $esParaAgregar ) {
                if( $esParaRecuperar ) {
                    var $ = JSON.parse( localStorage.getItem($codigo) || "[]" ) ;
                    Reactivo( $objeto ).lista( $, $esParaAgregar ) ;
                }
                else {
                    //console.log( $objeto.datos )
                    localStorage.setItem( $codigo, JSON.stringify($objeto && $objeto.datos || []) ) ;
                }
            }
            , url: function() {
                var $ = location.href.split("?").length > 1 ? location.href.split("?") : ["",""] ;
                var $$ = ComoFicha( $[1], "&", "=" ) ;
                Reactivo($objeto).actualizar( $$ )
                
            }
            , ajax: function( $formato, $url, $metodo, $siOk, $siNo ) {
                $siOk = $siOk || function($) { return $ ; }
                var X = new XMLHttpRequest() ;
                X.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                      Reactivo($objeto)[
                            $formato == "ficha" ? "ficha" : "lista"
                          ]( 
                          ( 
                            ($formato == "json")||( $formato == "ficha" ) 
                          ) ? 
                            $siOk( JSON.parse(this.responseText) ) :
                            $siOk( CSV.raw_json( CSV.raw(this.responseText, $formato=="tsv" ? "\t":",") ) )
                         
                      );
                    }
                    else if( this.status == 200 ) {
                      if( (!!$siNo).y( typeof $siNo == "function" ) )
                        $siNo( this ) ;
                    }
                };
                X.open( $metodo||"GET", $url, true ) ;
                X.send() ;
            }
            , get: function( $formato, $url, $siOk, $siNo ) {
                Reactivo($objeto).ajax( $formato, $url, "GET", $siOk, $siNo )
            }
            , post: function( $formato, $url, $siOk, $siNo ) {
                Reactivo($objeto).ajax( $formato, $url, "POST", $siOk, $siNo )
            }
            , fetch: function( $url ) {
                return fetch( $url ).then(
                    function (Respuesta) {
                        return JSON.parse( Respuesta )
                    }
                ).then(
                    function ($datos) {
                        Reactivo($objeto).lista($datos)
                    }
                )
            }
            , gsheets: function( $idDocumento, $formato, $luego,$error ) {
                https://docs.google.com/spreadsheets/d/e/2PACX-1vQm9ZTCNrjcaa6U_CxNMY37-2HdxXkyGSsA9QJjAaxgVzDbtDE30pUqBSve7ty2aMI_XGXqhULvEiXB/pub?output=tsv
                Reactivo( $objeto ).get( $formato||"csv", "https://docs.google.com/spreadsheets/d/e/{0}/pub??gid=0&single=true&output={1}".formato([$idDocumento,$formato||"csv"]), $luego,$error )
                
            }
        }
    }
    
    U.Modelo = Reactivo ;
    Buscar.reactivos = Buscar.modelos = function() {
        Reactivo().buscar() ;
    }
    
    document.addEventListener(
        "DOMContentLoaded"
        , function() {
            if(B()("Inicializar, Buscar[modelos]")().length ) {
                Buscar.modelos() ;
                ("[modelo][auto]").ubicar().iterar(function(cu){
                    console.log(cu)
                    Modelo(cu).actualizar({})
                })
            }

            Reactivo().buscar("origen") ;
            B()("[origen]")().iterar(function(x){
                var url = x.getAttribute("origen") ;
                var tipo = x.hasAttribute("es") ? x.getAttribute("es") : "json" ;
                var filtrar = x.hasAttribute("filtrar") ? x.getAttribute("filtrar") : false ;
                var ordenar = x.hasAttribute("ordenar") ? x.getAttribute("ordenar") : false ;
                var procesar = x.hasAttribute("procesar") ? x.getAttribute("procesar") : false ;
                var alterminar = x.hasAttribute("al-terminar") ? x.getAttribute("al-terminar") : false ;
                Ajax.get(tipo, url, function(recuperado){
                    if( procesar ) recuperado = eval( procesar + "(recuperado)" )
                    if( filtrar ) recuperado = recuperado.filter(function($) {return eval( filtrar+"($)" ) ;});
                    if( ordenar ) recuperado = recuperado.sort(function($,$$) {return eval( ordenar+"($,$$)" );});
                    Modelo(x).lista(recuperado);
                    if( alterminar ) eval( alterminar+"(recuperado)") ;
                })
                /*Modelo(x).get(
                    tipo
                    , url
                    , function(y){return y}
                )*/
            })
            console.log( "Iniciado..." ) ;
        }
        , false
    )

// ------------------------------------------[ Herramientas para componentes ] //     
    var Top = {} ;
    Top.toggle = function toggleTop($, $u) {
        fnArray(
            $.parentNode.parentNode.children
        ).forEach(
            function( item ) {
                if( item !== $.parentNode ) item.classList.remove( "activo" ) ;
            }
        );
        $.parentNode.classList.toggle( "activo" ) ;
    }
    
    Top.activar = function ($, $u) {
        if($u)
        fnArray(
            $.parentNode.parentNode.children
        ).forEach(
            function( item ) {
                if( item !== $.parentNode ) item.classList.remove( "activo" ) ;
            }
        );
        $.parentNode.classList.add( "activo" ) ;
    }
    
    Top.tabs =function( $ ) {
        $orden = (
            function () {
                x = -1 ;
                fnArray( $.parentNode.children )
                .forEach(
                    function( item, n ) {
                        if( item === $ ) {
                            x = n ;
                            $.classList.add( "activo" ) ;
                        } else item.classList.remove( "activo" )
                    }
                );
                return x ;
            }
        )() ;
        BB = B()("Contenido")($.parentNode.parentNode)
        BB.forEach( function(bb){ bb.classList.remove( "activo" )} ) ;
        BB[$orden].classList.add( "activo" ) ;
    }

    String.prototype.ubicar = function(cant) {
        var x = Buscar[cant === 1 ? "primer" : "todos"](this) ;
        if( !isNaN(cant) && cant != 1 ) return x.slice(0, cant) ;
        return x ;
    }
    String.prototype.ir = function() {
        location.href = this ;
    }
    
    function dragElement(elmnt) {
    	console.log(elmnt)
    	if( localStorage["ventanita"+elmnt.id] ) {
    		x = JSON.parse(localStorage["ventanita"+elmnt.id]);
    		elmnt.style.top = x[0]
    		elmnt.style.left = x[1]
    	}
      	var posA = [0,0], posB = [0,0]
      	try {
      		elmnt.querySelector("header").onmousedown = dragMouseDown ;
      	}
      	catch(e) {
        	// otherwise, move the DIV from anywhere inside the DIV:
        	elmnt.onmousedown = dragMouseDown;
      	}
    
      	function dragMouseDown(e) {
        	e = e || window.event;
        	e.preventDefault();
        	posB = [e.clientX,e.clientY]
        	doc.onmouseup 	= closeDragElement;
        	doc.onmousemove = elementDrag;
      	}
    
      	function elementDrag(e) {
        	e = e || window.event;
        	e.preventDefault();
        	// calculate the new cursor position:
        	posA = [ posB[0]-e.clientX, posB[1]-e.clientY ] ;
        	posB = [e.clientX,e.clientY];
        	// set the element's new position:
        	elmnt.style.zIndex = 1000 ;
        	elmnt.style.top 	= (elmnt.offsetTop - posA[1]) + "px";
        	elmnt.style.left 	= (elmnt.offsetLeft - posA[0]) + "px";
        	localStorage["ventanita"+elmnt.id] = JSON.stringify( [elmnt.style.top, elmnt.style.left] )
      	}
    
      	function closeDragElement() {
        	// stop moving when mouse button is released:
        	elmnt.style.zIndex = 0 ;
        	document.onmouseup = null;
        	document.onmousemove = null;
      	}
    }