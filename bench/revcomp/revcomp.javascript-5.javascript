/* The Computer Language Benchmarks Game
   http://benchmarksgame.alioth.debian.org/

   contributed by Luka Popijac
*/

var comp = new Uint8Array([
	,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
	84,86,71,72,,,67,68,,,77,,75,78,,,,89,83,65,65,66,87,,82,,,,,,,,
	84,86,71,72,,,67,68,,,77,,75,78,,,,89,83,65,65,66,87,,82
]);

var fromCharCode = String.fromCharCode;

var display = function(node, len) {
	write(fromCharCode.apply(null, node.val));
	while(node = node.next) {
		var v = node.val;
		print(fromCharCode.apply(null, v.subarray(0,60-len)));
		write(fromCharCode.apply(null, v.subarray(60-len,60)));
	}
	print();
}

function List(len, next) {
	this.val = new Uint8Array(len);
	this.next = next;
}

var node = null;
var len, line;
while(line = readline()) {
	if(line[0] !== '>') {
		len = line.length;
		node = new List(len, node);
		var v = node.val;
		for(var i=len; i--;) {
			v[i] = comp[line.charCodeAt(len-i-1)];
		}
	} else {
		if(node) display(node, len);
		node = null;
		print(line);
	}
}

display(node, len);
