"* The Computer Language Benchmarks Game
    http://benchmarksgame.alioth.debian.org/
    contributed by Nicolas Cellier *"!


Smalltalk defineClass: #MeteorPiece
    superclass: #{Core.Object}
    indexedType: #none
    private: false
    instanceVariableNames: 'mask row '
    classInstanceVariableNames: ''
    imports: ''
    category: ''!

Smalltalk defineClass: #MeteorPieceWithIsland
    superclass: #{Smalltalk.MeteorPiece}
    indexedType: #none
    private: false
    instanceVariableNames: 'islands aPieceCouldFitIntoIsland '
    classInstanceVariableNames: ''
    imports: ''
    category: ''!

Smalltalk defineClass: #MeteorBoard
    superclass: #{Core.Object}
    indexedType: #none
    private: false
    instanceVariableNames: 'pieces ncol ncell twoRows sixRows oddRowsMask evenRowsMask southEdge eastEdge westEdge
        southToNorthMasks eastToWestMasks northWestMask northEastMask southWestMask southEastMask positionsPerPiece loopCount fillMask '
    classInstanceVariableNames: ''
    imports: ''
    category: ''!

!MeteorPiece class methodsFor: 'instance creation'!

mask: p islands: i
    ^i = 0
        ifTrue: [MeteorPiece new mask: p]
        ifFalse: [MeteorPieceWithIsland new mask: p; islands: i]! !


!MeteorPiece methodsFor: 'accessing'!

mask
    ^mask!

fillSolution: aString ncol: ncol withColor: c 
    | offset |
    offset := row * ncol.
    mask bitsDo: [:k | aString at: offset + k put: c]! !

!MeteorPiece methodsFor: 'testing'!

fitOnBoard: aBoardMask
    ^0 == (aBoardMask bitAnd: mask)! !

!MeteorPiece methodsFor: 'initialize-release'!

forRow: rowOffset
    row := rowOffset!

mask: aPieceMask
    mask := aPieceMask! !


!MeteorBoard class methodsFor: 'instance creation'!

default
    ^self basicNew fromString:
'0 0 0 0 1 
 2 2 2 0 1 
2 6 6 1 1 
 2 6 1 5 5 
8 6 5 5 5 
 8 6 3 3 3 
4 8 8 9 3 
 4 4 8 9 3 
4 7 4 7 9 
 7 7 7 9 9'! !


!MeteorBoard methodsFor: 'islands'!

hasEastOrWestIsland: aMask
    ^ (self hasInsetZero: southEdge * (eastEdge bitAnd: aMask))
        or: [(self hasInsetZero: southEdge * (westEdge bitAnd: aMask))
            or: [(aMask bitAnd: eastEdge) > 0 and: [(aMask bitAnd: westEdge) > 0 and: [(self findIsland: aMask) bitCount \\ 5 > 0]]]]!

hasNorthIsland: aPieceMask row: iRow
    | bitReverse |
    bitReverse := (#(-1 -1 6 4 2) at: iRow) * ncol.
    ^self hasSouthIsland: (aPieceMask bitReverse: bitReverse)!

hasInsetZero: aMask
    | allOnes |
    allOnes := aMask bitOr: aMask - 1.
    ^(allOnes bitAnd: allOnes + 1) > 0!

findIsland: aMask
    | nextFreeCellMask open |
    nextFreeCellMask := 1 + aMask bitAnd: -1 - aMask.
    fillMask :=  aMask.
    open := false.
    self fillMaskStartingAt: nextFreeCellMask stoppingAbove: (1 bitShift: fillMask highBit - 1 // ncol * ncol - 1) ifFoundEnough: [open := true].
    ^open
        ifTrue: [0]
        ifFalse: [fillMask - aMask]!

hasSouthIsland: aMask
    ^(self findIsland: aMask) bitCount \\ 5 > 0
        or: [(self findIsland: fillMask) bitCount \\ 5 > 0]!

islandsFor: aPieceMask
    | islands aMask nextFreeCellMask open top |
    islands := 0.
    fillMask := aPieceMask.
    top := 1 bitShift: (fillMask highBit - 1 // ncol * ncol - 1).
    [(nextFreeCellMask := 1 + fillMask bitAnd: -1 - fillMask) <= top]
        whileTrue:
            [open := false.
            aMask := fillMask.
            self fillMaskStartingAt: nextFreeCellMask stoppingAbove: top ifFoundEnough: [open := true].
            open ifFalse: [islands := islands + (fillMask - aMask)]].
    ^islands!

fillMaskStartingAt: pos stoppingAbove: maxCell ifFoundEnough: exitBlock
    (fillMask bitAnd: pos) = 0 ifFalse: [^self].
    (pos > maxCell) ifTrue: [^exitBlock value].
    fillMask := fillMask + pos.
    (self canShiftE: pos) ifTrue: [self fillMaskStartingAt: (self shiftE: pos) stoppingAbove: maxCell ifFoundEnough: exitBlock].
    (self canShiftNE: pos) ifTrue: [self fillMaskStartingAt: (self shiftNE: pos) stoppingAbove: maxCell ifFoundEnough: exitBlock].
    (self canShiftNW: pos) ifTrue: [self fillMaskStartingAt: (self shiftNW: pos) stoppingAbove: maxCell ifFoundEnough: exitBlock].
    (self canShiftW: pos) ifTrue: [self fillMaskStartingAt: (self shiftW: pos) stoppingAbove: maxCell ifFoundEnough: exitBlock].
    ^self!

northIslandsFor: aPieceMask row: iRow
    | filled isleSEW bitReverse isleNE isleNW |
    bitReverse := (#(-1 -1 6 4 2) at: iRow) * ncol.
    filled := aPieceMask bitOr: aPieceMask - 1.
    isleSEW := self islandsFor: filled.
    (aPieceMask bitAnd: (eastEdge bitOr: westEdge)) = 0 ifFalse: [^isleSEW].
    (isleSEW bitAnd: (eastEdge bitOr: westEdge)) = 0 ifFalse: [^isleSEW].
    (southEdge bitAnd: aPieceMask) = 0
        ifTrue: [filled := (filled bitShift: 0 - ncol) bitShift: ncol].
    isleNE := ((self islandsFor: (filled bitReverse: bitReverse)) bitReverse: bitReverse) bitOr: isleSEW.
    isleNW := ((1 bitShift: bitReverse) - 1 - (isleNE bitOr: (aPieceMask bitOr: aPieceMask - 1))) bitOr: isleSEW.
    ^isleNW bitCount < isleNE bitCount
        ifTrue: [isleNW]
        ifFalse: [isleNE]! !

!MeteorBoard methodsFor: 'generating'!

shiftE: aPieceMask
    ^aPieceMask bitShift: -1!

placesFor: aPieceMask do: aBlock
    | westMask eastMask |
    eastMask := self shiftSEmost: aPieceMask.
    
    [[westMask := eastMask.
    [westMask lowBit > twoRows ifTrue: [^self].
    (self hasEastOrWestIsland: westMask) ifFalse: [aBlock value: westMask].
    self canShiftW: westMask] whileTrue: [westMask := self shiftW: westMask].
    self canShiftNE: eastMask] whileTrue: [eastMask := self shiftNE: eastMask].
    self canShiftNW: eastMask] whileTrue: [eastMask := self shiftNW: eastMask]!

flip: aPieceMask
    ^self shiftSEmost: ((southToNorthMasks
        inject: 0 into: [:mask :rowMask |
            (mask bitShift:  ncol) + ((rowMask bitAnd: aPieceMask) bitShift: 1 - rowMask lowBit)])
                bitShift: 0 - ncol)!

possiblePositionsOnTwoRows
    ^pieces collect: [:aPieceMask |
        | possible iRot |
        possible := (Array new: twoRows) collect: [:freeCell | Array new: 12 withAll: (MeteorPiece new mask: 0)].
        iRot := 0.
        self rotationsOf: aPieceMask do: [:rotated |
            iRot := iRot + 1.
            self placesFor: rotated do: [:shifted |
                (possible at: shifted lowBit) at: iRot put: (MeteorPiece
                    mask: ((self hasEastOrWestIsland: shifted) ifTrue: [0] ifFalse: [shifted])
                    islands: (self islandsFor: (shifted bitOr: shifted - 1)))]].
        possible]!

shiftNE: aPieceMask
    | evens odds |
    odds := oddRowsMask bitAnd: aPieceMask.
    evens := evenRowsMask bitAnd: aPieceMask.
    ^(odds bitShift: -1) + evens bitShift: ncol!

shiftNW: aPieceMask
    | evens odds |
    odds := oddRowsMask bitAnd: aPieceMask.
    evens := evenRowsMask bitAnd: aPieceMask.
    ^(evens bitShift: 1) + odds bitShift: ncol!

shiftW: aPieceMask
    ^aPieceMask bitShift: 1!

canShiftSW: aPieceMask
    ^(southEastMask bitAnd: aPieceMask) = 0!

shiftSE: aPieceMask
    | evens odds |
    odds := oddRowsMask bitAnd: aPieceMask.
    evens := evenRowsMask bitAnd: aPieceMask.
    ^(odds bitShift: -1) + evens bitShift: 0 - ncol!

shiftSEmost: aPieceMask
    | mostSEMask eastColumn lowBit |
    aPieceMask odd ifTrue: [^aPieceMask].
    lowBit := aPieceMask lowBit.
    mostSEMask := aPieceMask bitShift: 0 -  (lowBit - 1 // twoRows * twoRows).
    (mostSEMask bitAnd: southEdge) = 0
        ifTrue: [mostSEMask := (self canShiftSE: mostSEMask)
            ifTrue: [self shiftSE: mostSEMask]
            ifFalse: [self shiftSW: mostSEMask]].
    eastColumn := eastToWestMasks findFirst: [:e | (e bitAnd: mostSEMask) > 0].
    ^mostSEMask bitShift: 1 - eastColumn!

canShiftNE: aPieceMask
    ^(northEastMask bitAnd: aPieceMask) = 0!

canShiftSE: aPieceMask
    ^(southEastMask bitAnd: aPieceMask) = 0!

canShiftW: aPieceMask
    ^(westEdge bitAnd: aPieceMask) = 0!

rotate: aPieceMask
    | rotatedMask pivot rotatedPivot irow row |
    rotatedMask := 0.
    irow := 1.
    row := aPieceMask bitAnd: (southToNorthMasks at: irow).
    rotatedPivot := pivot := 1 bitShift: row highBit - 1.
    
    [rotatedMask := rotatedMask + rotatedPivot.
    [(row bitAnd: pivot - 1) = 0]
        whileFalse:
            [pivot := self shiftE: pivot.
            rotatedPivot := self shiftNE: rotatedPivot.
            (row bitAnd: pivot) = 0
                ifFalse:
                    [rotatedMask := rotatedMask + rotatedPivot]].
    (row := aPieceMask bitAnd: (southToNorthMasks at: (irow := irow + 1))) = 0]
        whileFalse:
            [(self canShiftNE: pivot)
                ifTrue:
                    [pivot := self shiftNE: pivot.
                    rotatedPivot := self shiftNW: rotatedPivot]
                ifFalse:
                    [pivot := self shiftNW: pivot.
                    rotatedPivot := self shiftW: rotatedPivot].
            [row >= (pivot bitShift: 1)]
                whileTrue:
                    [pivot := self shiftW: pivot.
                    (self canShiftSW: rotatedPivot)
                        ifFalse:
                            [rotatedPivot := rotatedPivot bitShift: twoRows.
                            rotatedMask := rotatedMask bitShift: twoRows.].
                    rotatedPivot := self shiftSW: rotatedPivot]].
    ^self shiftSEmost: rotatedMask!

canShiftE: aPieceMask
    ^(eastEdge bitAnd: aPieceMask) = 0!

canShiftNW: aPieceMask
    ^(northWestMask bitAnd: aPieceMask) = 0!

shiftSW: aPieceMask
    | evens odds |
    odds := oddRowsMask bitAnd: aPieceMask.
    evens := evenRowsMask bitAnd: aPieceMask.
    ^(evens bitShift: 1) + odds bitShift: 0 - ncol!

rotationsOf: aPieceMask do: aBlock
    | next |
    aBlock value: (next := aPieceMask); value: (self flip: next).
    5 timesRepeat:  [aBlock value: (next := self rotate: next); value: (self flip: next)]! !

!MeteorBoard methodsFor: 'initialize-release'!

initializePossiblePositions
    | positionsPerPiecePerCell thePieceWhichBreakSymmetry |
    positionsPerPiecePerCell := self possiblePositionsOnTwoRows.
    thePieceWhichBreakSymmetry := 6.
    positionsPerPiece := (1 to: 5) collect: [:iRow |
        | maxMaskForRow northRow |
        maxMaskForRow := (1 bitShift: (#(6 6 6 4 2) at: iRow) * ncol) - 1.
        northRow :=  southEdge bitShift: ((#(-1 -1 6 4 2) at: iRow) - 1 * ncol).
        (1 to: twoRows) collect: [:cellNumber |
            (1 to: pieces size) collect: [:pieceNumber |
                | orientations n str |
                orientations := (positionsPerPiecePerCell at: pieceNumber) at: cellNumber.
                n := pieceNumber = thePieceWhichBreakSymmetry ifTrue: [6] ifFalse: [12].
                str := (Array new: n) writeStream.
                1 to: n do: [:i |
                    | aPiece |
                    aPiece := orientations at: i.
                    (aPiece mask > 0 and: [aPiece mask <= maxMaskForRow])
                        ifTrue:
                            [(iRow = 1 and: [cellNumber <= ncol])
                                ifTrue: [(self hasSouthIsland: aPiece mask)
                                    ifFalse: [str nextPut: (MeteorPiece mask: aPiece mask islands: 0)]]
                                ifFalse: [(aPiece mask bitAnd: northRow) > 0
                                    ifTrue: [(self hasNorthIsland: aPiece mask row: iRow)
                                        ifFalse:
                                            [| isle |
                                            isle := iRow = 5
                                                ifTrue: [0]
                                                ifFalse: [self northIslandsFor: aPiece mask row: iRow].
                                            str nextPut: (MeteorPiece mask: aPiece mask islands: isle)]]
                                    ifFalse: [str nextPut: aPiece]]]].
                str contents]]]!

initializeRowColMasks
    southEdge := (1 bitShift: ncol) - 1.
    southToNorthMasks := (0 to: 5) collect: [:i | southEdge bitShift: ncol * i].
    eastEdge := (1 bitShift: sixRows)-1/southEdge.
    eastToWestMasks := (0 to: ncol - 1) collect: [:i | eastEdge bitShift: i].
    westEdge := eastToWestMasks last.
    oddRowsMask := (1 bitShift: sixRows)-1/((1 bitShift: twoRows)-1)*southEdge.
    evenRowsMask := oddRowsMask bitShift: ncol.
    northWestMask := westEdge bitAnd: evenRowsMask.
    northEastMask := eastEdge bitAnd: oddRowsMask.
    southWestMask := southEdge bitOr: (westEdge bitAnd: evenRowsMask).
    southEastMask := southEdge bitOr: (eastEdge bitAnd: oddRowsMask).!

fromString: aString
    | rawString |
    rawString := aString reject: [:e | e isSeparator].
    ncell := rawString size.
    ncol := 0.
    (aString readStream upTo: Character cr) do: [:e | e isSeparator ifFalse: [ncol := ncol + 1]].
    twoRows := ncol * 2.
    sixRows := ncol * 6.
    self initializeRowColMasks.
    pieces := rawString asSet sorted collect: [:char |
        self shiftSEmost:
            (rawString inject: 0 into: [:pmask :c | pmask * 2 + (c = char ifTrue: [1] ifFalse: [0])])].
    self initializePossiblePositions! !

!MeteorBoard methodsFor: 'printing'!

printSolution: aString on: aStream
    | src i odd |
    src := aString readStream.
    i := 0. odd := true.
    [src atEnd]
        whileFalse:
            [aStream nextPut: src next; space.
            (i := i + 1 \\ ncol) = 0
                ifTrue:
                    [aStream nl.
                    (odd := odd not)  ifFalse: [aStream space]]]! !

!MeteorBoard methodsFor: 'solving'!

solvedPuzzleDo: solutionBlock
    loopCount := 0.
    self
        searchPuzzlesWithColorMask: (1 bitShift: pieces size) - 1
        boardMask: 0
        rowOffset: 0
        pieces: pieces copy
        ifFound: [:solution |
            solutionBlock value: solution; value: solution reverse].
    ^loopCount!

boardStringWithPieces: pArray
    | board |
    board := String new: ncell.
    1 to: pArray size do: [:i | | c |
        c := '0123456789*' at: i.
        (pArray at: i) fillSolution: board ncol: ncol withColor: c].
    ^board!

searchPuzzlesWithColorMask: colorMask boardMask: bMask rowOffset: rowOff pieces: pArray ifFound: solutionBlock
    | nextFreeCell possibles colorBit iRow boardMask |
    colorMask = 0 ifTrue: [ ^solutionBlock value: (self boardStringWithPieces: pieces) ].
    loopCount := loopCount + 1.
    boardMask := bMask.
    iRow := rowOff.
    [(nextFreeCell := (boardMask + 1) lowBit) > twoRows]
        whileTrue:
            [ iRow := iRow + 2.
            boardMask := boardMask bitShift: 0 - twoRows ].
    possibles := (positionsPerPiece at: iRow // 2 + 1) at: nextFreeCell.
    colorBit := 1.
    1 to: pieces size do: [:pieceNumber |
        (colorMask bitAnd: colorBit) = 0
            ifFalse:
                [ | positions |
                positions := possibles at: pieceNumber.
                1 to: positions size do: [:i |
                    | aPiece |
                    ((aPiece := positions at: i) fitOnBoard: boardMask)
                        ifTrue:
                            [pieces at: pieceNumber put: (aPiece forRow: iRow).
                            self
                                searchPuzzlesWithColorMask: colorMask - colorBit
                                boardMask: boardMask + aPiece mask
                                rowOffset: iRow
                                pieces: pArray
                                ifFound: solutionBlock]]].
        colorBit := colorBit * 2].
    ^nil! !


!MeteorPieceWithIsland methodsFor: 'testing'!

fitOnBoard: aBoardMask
    | occupied |
    ^0 == (aBoardMask bitAnd: mask) and:
        [(occupied := aBoardMask bitAnd: islands) = islands
            or: [aPieceCouldFitIntoIsland and: [(islands - occupied) bitCount = 5]]]! !

!MeteorPieceWithIsland methodsFor: 'initialize-release'!

islands: islandMask
    islands := islandMask.
    aPieceCouldFitIntoIsland := islands bitCount >= 5! !


!Tests class methodsFor: 'benchmark scripts'!

meteor
    self meteor: self arg to: self stdout.
    ^''! !

!Tests class methodsFor: 'benchmarking'!

meteor: nMax to: outputStream
    | board count minSolution maxSolution |
    count := 0.
    minSolution := String new: 50 withAll: $9.
    maxSolution := String new: 50 withAll: $0.
    (board := MeteorBoard default) solvedPuzzleDo:
        [:aString |
            count := count + 1.
            aString < minSolution ifTrue: [minSolution := aString].
            aString > maxSolution ifTrue: [maxSolution := aString]]. 
    outputStream print: count; nextPutAll: ' solutions found'; nl; nl.
    board printSolution: minSolution on: outputStream.
    outputStream nl.
    board printSolution: maxSolution on: outputStream.
    outputStream nl.! !


!Core.Integer methodsFor: 'bit manipulation'!

bitCount
    | count remainder |
    count := 0.
    remainder := self.
    [count := count + (remainder bitAnd: 255) bitCountOfByte.
    remainder > 255]
        whileTrue:
            [remainder := remainder bitShift: -8].
    ^count!

bitReverse: highBit 
    | v r s |
    highBit < self highBit ifTrue: [ self error: 'Not enough bits.' ].
    v := self.
    r := v bitAnd: 1.
    s := highBit - 1.
    [ v := v bitShift: -1.
    v = 0 ] whileFalse:
        [ r := r bitShift: 1.
        r := r bitOr: (v bitAnd: 1).
        s := s - 1 ].
    ^ r bitShift: s!

bitsDo: aBlock
    | mask |
    self < 0 ifTrue: [^self error: 'Cannot enumerate bits of a negative integer'].
    mask := self.
    [mask = 0]
        whileFalse:
            [aBlock value: mask lowBit.
            mask := mask bitAnd: mask - 1]! !


!Core.SmallInteger methodsFor: 'bit manipulation'!

bitCountOfByte
    "Count the number of bits set to 1 in a byte."

    ^#[0 1 1 2 1 2 2 3 1 2 2 3 2 3 3 4
       1 2 2 3 2 3 3 4 2 3 3 4 3 4 4 5
       1 2 2 3 2 3 3 4 2 3 3 4 3 4 4 5
       2 3 3 4 3 4 4 5 3 4 4 5 4 5 5 6
       1 2 2 3 2 3 3 4 2 3 3 4 3 4 4 5
       2 3 3 4 3 4 4 5 3 4 4 5 4 5 5 6
       2 3 3 4 3 4 4 5 3 4 4 5 4 5 5 6
       3 4 4 5 4 5 5 6 4 5 5 6 5 6 6 7
       1 2 2 3 2 3 3 4 2 3 3 4 3 4 4 5
       2 3 3 4 3 4 4 5 3 4 4 5 4 5 5 6
       2 3 3 4 3 4 4 5 3 4 4 5 4 5 5 6
       3 4 4 5 4 5 5 6 4 5 5 6 5 6 6 7
       2 3 3 4 3 4 4 5 3 4 4 5 4 5 5 6
       3 4 4 5 4 5 5 6 4 5 5 6 5 6 6 7
       3 4 4 5 4 5 5 6 4 5 5 6 5 6 6 7
       4 5 5 6 5 6 6 7 5 6 6 7 6 7 7 8] at: self + 1

    "Evaluate this expression to form above byte array:
    ((0 to: 255)
        collect: [:i | 
            | bitCount n |
            n := i.
            bitCount := 0.
            [n = 0]
                whileFalse:
                    [bitCount := bitCount + 1.
                    n := n bitAnd: n - 1].
            bitCount]
        as: ByteArray)"!

byteReversed
    "Answer the receiver with bits reversed in a byte.
    The receiver must be between 0 and 255.
    The constant has been obtained by this snippet:
    (0 to: 255) collect: [:e |
        | r |
        r := ((e bitAnd: 2r11110000) bitShift: -4) + ((e bitAnd: 2r00001111) bitShift: 4).
        r := ((r bitAnd: 2r11001100) bitShift: -2) + ((r bitAnd: 2r00110011) bitShift: 2).
        ((r bitAnd: 2r10101010) bitShift: -1) + ((r bitAnd: 2r01010101) bitShift: 1).] as: ByteArray"
    
    ^#[  0 128  64 192  32 160  96 224  16 144  80 208  48 176 112 240
         8 136  72 200  40 168 104 232  24 152  88 216  56 184 120 248
         4 132  68 196  36 164 100 228  20 148  84 212  52 180 116 244
        12 140  76 204  44 172 108 236  28 156  92 220  60 188 124 252
         2 130  66 194  34 162  98 226  18 146  82 210  50 178 114 242
        10 138  74 202  42 170 106 234  26 154  90 218  58 186 122 250
         6 134  70 198  38 166 102 230  22 150  86 214  54 182 118 246
        14 142  78 206  46 174 110 238  30 158  94 222  62 190 126 254
         1 129  65 193  33 161  97 225  17 145  81 209  49 177 113 241
         9 137  73 201  41 169 105 233  25 153  89 217  57 185 121 249
         5 133  69 197  37 165 101 229  21 149  85 213  53 181 117 245
        13 141  77 205  45 173 109 237  29 157  93 221  61 189 125 253
         3 131  67 195  35 163  99 227  19 147  83 211  51 179 115 243
        11 139  75 203  43 171 107 235  27 155  91 219  59 187 123 251
         7 135  71 199  39 167 103 231  23 151  87 215  55 183 119 247
        15 143  79 207  47 175 111 239  31 159  95 223  63 191 127 255] at: 1 + self! !


!Core.LargePositiveInteger methodsFor: 'bit manipulation'!

bitReverse: highBit 
    "This implementation is faster than super"
    
    | digitSize reversed |
    highBit < self highBit ifTrue: [ self error: 'Not enough bits.' ].
    digitSize := highBit + 7 // 8.
    reversed := self class basicNew: digitSize.
    1 to: self digitLength do: [:i |
        reversed digitAt: digitSize + 1 - i put: (self digitAt: i) byteReversed].
    ^reversed bitShift: highBit - (digitSize * 8)!

bitsDo: aBlock
    | mask offset |
    1 to: self digitLength do: [:iByte |
        offset := iByte - 1 bitShift: 3.
        mask := self digitAt: iByte.
        [mask = 0]
            whileFalse:
                [aBlock value: mask lowBit + offset.
                mask := mask bitAnd: mask - 1]]!

bitCount
    "Count the number of bits set to 1 in self"

    | bitCount |
    bitCount := 0.
    1 to: self digitLength do: [:i |
        bitCount := bitCount + (self digitAt: i) bitCountOfByte].
    ^bitCount! !

