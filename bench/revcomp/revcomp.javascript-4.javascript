/* The Computer Language Benchmarks Game
    http://benchmarksgame.alioth.debian.org/

    contributed by Joe Farro
    parts taken from solution contributed by 
    Jos Hirth which was modified by 10iii
*/

var comp = [];
comp[65] = 'T';
comp[66] = 'V';
comp[67] = 'G';
comp[68] = 'H';
comp[71] = 'C';
comp[72] = 'D';
comp[75] = 'M';
comp[77] = 'K';
comp[78] = 'N';
comp[82] = 'Y';
comp[83] = 'S';
comp[84] = 'A';
comp[85] = 'A';
comp[86] = 'B';
comp[87] = 'W';
comp[89] = 'R';
comp[97] = 'T';
comp[98] = 'V';
comp[99] = 'G';
comp[100] = 'H';
comp[103] = 'C';
comp[104] = 'D';
comp[107] = 'M';
comp[109] = 'K';
comp[110] = 'N';
comp[114] = 'Y';
comp[115] = 'S';
comp[116] = 'A';
comp[117] = 'A';
comp[118] = 'B';
comp[119] = 'W';
comp[121] = 'R';

var LA_LEN = 995,
    _PREV = 0,
    _NEXT = 1,
    _POS = 2,
    _DATA = 3;

function newLinkedArray(prev) {
    var ar = new Array(4);
    ar[0] = prev;   // _PREV
                    // _NEXT 
    ar[2] = 0;      // _POS
    ar[3] = [];     // _DATA
    return ar;
}

function reverse(la) {

    var i,
        comps = comp,
        lines = la[_DATA],
        lnIdx = la[_POS] - 1,
        line = lines[lnIdx],
        c = 1,
        buff = [''],
        buffIdx = 1,
        rev = new Array(61);

    rev[0] = '';

    while (true) {

        for (i = line.length; i-- > 0; ++c) {
            rev[c] = comps[line.charCodeAt(i)];
            if (c === 60) {
                buff[buffIdx] = rev.join('');
                buffIdx++;
                c = 0;
            }
        }

        lnIdx--;
        line = lines[lnIdx];

        if (line !== undefined) {
            continue;
        }

        la = la[_PREV];
        if (la === undefined) {
            if (c > 1) {
                buff[buffIdx] = rev.join('').substr(0, c-1);
                buffIdx++;
            }
            if (buffIdx < buff.length) {
                write(buff.slice(0, buffIdx).join('\n'));
            } else {
                write(buff.join('\n'));
            }
            return;
        }

        lines = la[_DATA];
        lnIdx = la[_POS];
        lnIdx--;
        line = lines[lnIdx];
        write(buff.join('\n'));
        buffIdx = 1;
    }
}

var line,
    headLA = newLinkedArray(),
    la = headLA,
    lnIdx = 0,
    lines = la[_DATA];

write(readline());

for (line = readline(); line !== undefined; line = readline()) {

    if (line[0] !== '>') {

        if (lnIdx === LA_LEN) {

            la[_POS] = LA_LEN;

            if (la[_NEXT] === undefined) {
                la = la[_NEXT] = newLinkedArray(la);
            } else {
                la = la[_NEXT];
            }
            lines = la[_DATA];
            lines[0] = line;
            lnIdx = la[_POS] = 1;
        } else {
            lines[lnIdx] = line;
            lnIdx++;
        }
    } else {
        la[_POS] = lnIdx;
        reverse(la);
        write('\n' + line);
        la = headLA;
        lines = la[_DATA];
        la[_POS] = 0;
        lnIdx = 0;
    }
}
la[_POS] = lnIdx;
reverse(la);
