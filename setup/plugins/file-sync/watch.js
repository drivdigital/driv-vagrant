/* global require, echo cd, mkdir, cp, mv, rm, test, pwd, exec */
require( 'shelljs/global' );
var chokidar = require( 'chokidar' );

// Usage: node watch source_dir destination_dir

// Process arguments.
var args = process.argv.slice( 2 );
if ( ! args[0] ) {
    echo( 'Missing source dir.' );
    exit( 1 );
}
if ( ! args[1] ) {
    echo( 'Missing destination dir.' );
    exit( 1 );
}

var SRC_DIR = args[0];
var DEST_DIR = args[1];

var ignored = [
    /\/setup/,
    /\/config/,
    /\.vagrant/,
    /\.git/,
    /\.idea/,
    /\.DS_Store/
];

var watcher = chokidar.watch( SRC_DIR, {
    ignored:          ignored,
    ignoreInitial:    true,
    awaitWriteFinish: true,
    persistent:       true,
    usePolling:       true,
    interval:         70,
    binaryInterval:   250
} );

log( 'Started' );

watcher.on( 'all', function ( event, path ) {
    // Ignore undefined.
    if ( path == SRC_DIR ) {
        return;
    }

    switch ( event ) {
        case 'add':
            handle_add( event, path );
            break;
        case 'addDir':
            handle_add_dir( event, path );
            break;
        case 'change':
            handle_change( event, path );
            break;
        case 'unlink':
            handle_unlink( event, path );
            break;
        case 'unlinkDir':
            handle_unlink_dir( event, path );
            break;
        case 'error':
            handle_error( arguments );
            break;
        default:
            log( path + ' was not handled' );
    }
} );

function handle_add( event, src_path ) {
    var dest_path = DEST_DIR + '/' + get_local_path( src_path );
    cp( '-f', src_path, dest_path );
    log( event.toUpperCase(), src_path, dest_path );
}

function handle_change( event, src_path ) {
    var dest_path = DEST_DIR + '/' + get_local_path( src_path );
    cp( '-f', src_path, dest_path );
    log( event.toUpperCase(), src_path, dest_path );
}

function handle_add_dir( event, src_path ) {
    var dest_path = DEST_DIR + '/' + get_local_path( src_path );
    mkdir( '-p', dest_path );
    log( event.toUpperCase(), src_path, dest_path );
}

function handle_unlink( event, src_path ) {
    var dest_path = DEST_DIR + '/' + get_local_path( src_path );
    rm( '-rf', dest_path );
    log( event.toUpperCase(), dest_path );
}

function handle_unlink_dir( event, src_path ) {
    var dest_path = DEST_DIR + '/' + get_local_path( src_path );
    rm( '-rf', dest_path );
    log( event.toUpperCase(), dest_path );
}

function handle_error( arg ) {
    log( arg );
}

function log() {
    // Type juggle arguments to array
    var args = Array.prototype.slice.call( arguments, 0 );

    // Log message to console and log file
    var d = new Date();
    var msg = d + ' - ' + args.join( ', ' );
    console.log( msg );

    (msg + '\n').toEnd( __dirname + '/watch.log' );
}

// Returns the path to the file relative to the site/project
function get_local_path( src_path ) {
    return src_path.split( SRC_DIR + '/' )[1];
}