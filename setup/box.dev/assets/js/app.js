(function () {

    function ajax( action, cb ) {
        fetch( 'ajax.php', {
            headers: {
                "X-Action": action
            }
        } ).then( ( response ) => {
            if ( response.status !== 200 ) {
                console.error( 'Looks like there was a problem. Status Code: ' +
                    response.status );
                return;
            }
            response.text().then( ( data ) => {
                cb( data );
            } )
        } )
    }

    function uptime() {
        ajax( 'uptime', ( data ) => {
            document.querySelector( '.uptime-result' ).textContent = data;
            setTimeout( () => {
                uptime();
            }, 5000 );
        } );
    }

    var hour = new Date().getHours();
    if ( hour > 22 || hour < 6 ) {
        document.body.classList.add( 'dark-grey' );
    }

    uptime();
}());