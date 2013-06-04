/* The Computer Language Benchmarks Game
   http://benchmarksgame.alioth.debian.org/

   Contributed by Jesse Millikan
   Modified by Matt Baker
   Modified by Joe Farro (ArrayBuffer and TypedArrays)
*/


function charToInt(str) {
    switch (str) {
        case 'a': return 0;
        case 'c': return 1;
        case 'g': return 2;
        case 't': return 3;
    }
}

function toStr(num, len) {
    var res = '';
    while (len > 0) {
        switch (num & 3) {
            case 0: res = 'A' + res; break;
            case 1: res = 'C' + res; break;
            case 2: res = 'G' + res; break;
            case 3: res = 'T' + res; break;
        }
        num = num >>> 2;
        len--;
    }
    return res;
}

function toUint(str) {

    var offset = 2 * str.length,
        uint = new Uint32Array(new ArrayBuffer(4)),
        i = 0;

    while (offset) {
        offset -= 2;
        uint[0] |= (charToInt(str[i]) << offset);
        i++;
    }
    return uint[0];
}


var dataLength = 0;

var byLength = [],
    data1 = {},
    data2 = {},
    data3 = {},
    data4 = {},
    data6 = {},
    data12 = {},
    data18 = {};

byLength[1] = data1;
byLength[2] = data2;
byLength[3] = data3;
byLength[4] = data4;
byLength[6] = data6;
byLength[12] = data12;
byLength[18] = data18;


function readInput() {

    // key-space masks for various lengths
    var m_1 = 3,            // 2^2 - 1
        m_2 = 15,           // 2^4 - 1
        m_3 = 63,           // 2^6 - 1
        m_4 = 255,          // 2^8 - 1
        m_6 = 4095,         // 2^12 - 1
        m_12 = 16777215,    // 2^24 - 1
        m_18 = 262143;      // 2^18 - 1, (smaller bc split between 2 uint32)

    var buf = new ArrayBuffer(8),
        uint32 = new Uint32Array(buf, 0, 1),
        uint32_l = new Uint32Array(buf, 4, 1);

    var len = 0,
        l,
        i,
        char,
        b,
        u8,
        u32,
        u32_l;

    while (readline().substr(0, 3) !== '>TH') {
    }

    l = readline();
    i = 0;
    len = l.length;

    // the first-line is a special case as not all the counts should start
    // saving immediately
    while (i < 18) {

        char = charToInt(l[i]);

        u32_l = uint32_l[0] = uint32_l[0] << 2 | uint32[0] >>> 16;
        u32 = uint32[0] = uint32[0] << 2 | char;

        if (i > 10) {
            u32 &= m_12;
            data12[u32] = (data12[u32] || 0) + 1;
        }

        if (i > 16) {
            u32_l &= m_18;
            u32 &= m_18;
            if (b = data18[u32_l]) {
               b[u32] = (b[u32] || 0) + 1;
            } else {
                (data18[u32_l] = {})[u32] = 1;
            }
        }

        if (i > 4) {
            u32 &= m_6;
            data6[u32] = (data6[u32] || 0) + 1;
        }

        if (i > 2) {
            u32 &= m_4;
            data4[u32] = (data4[u32] || 0) + 1;
        }

        if (i > 1) {
            u32 &= m_3;
            data3[u32] = (data3[u32] || 0) + 1;
        }

        if (i > 0) {
            u32 &= m_2;
            data2[u32] = (data2[u32] || 0) + 1;
        }

        u32 &= m_1;
        data1[u32] = (data1[u32] || 0) + 1;
        i++;
    }

    // use do-loop bc want to preserve `i` position on first line
    do {

        len = l.length;
        dataLength += len;
        while (i < len) {

            char = charToInt(l[i]);
            i++;

            u32_l = uint32_l[0] = (uint32_l[0] << 2 | uint32[0] >>> 16) & m_18;
            u32 = uint32[0] = uint32[0] << 2 | char;

            u32 &= m_12;
            data12[u32] = (data12[u32] || 0) + 1;
            
            u32 &= m_18;
            if (b = data18[u32_l]) {
               b[u32] = (b[u32] || 0) + 1;
            } else {
                (data18[u32_l] = {})[u32] = 1;
            }

            u32 &= m_6;
            data6[u32] = (data6[u32] || 0) + 1;
            u32 &= m_4;
            data4[u32] = (data4[u32] || 0) + 1;
            u32 &= m_3;
            data3[u32] = (data3[u32] || 0) + 1;
            u32 &= m_2;
            data2[u32] = (data2[u32] || 0) + 1;
            u32 &= m_1;
            data1[u32] = (data1[u32] || 0) + 1;
        }
        i = 0;
    } while ((l = readline()) && l[0] !== '>')
}


function sortCounts(seqLen) {

    var data = byLength[seqLen],
        keys = Object.keys(data),
        pctFactor = 100 / (dataLength - seqLen + 1);

    keys.sort(function(a, b) {
        return data[b] - data[a];
    });

    keys.forEach(function(code) {
        print(toStr(code, seqLen), (data[code] * pctFactor).toFixed(3));
    });
    print();
}


function printCount(s, s2) {

    var len,
        count;

    if (s2) {
        len = s.length + s2.length;
        count = byLength[len][toUint(s)][toUint(s2)];
        s += s2;
    } else {
        len = s.length;
        count = byLength[len][toUint(s)];
    }
    print(count + '\t' + s.toUpperCase());
}

readInput();

sortCounts(1);
sortCounts(2);

printCount('ggt');
printCount('ggta');
printCount('ggtatt');
printCount('ggtattttaatt');
printCount('ggtatttta', 'atttatagt');
