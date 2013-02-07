"* The Computer Language Benchmarks Game
    http://benchmarksgame.alioth.debian.org/
    contributed by Nicolas Cellier *"!


Smalltalk defineClass: #RepeatStreamRedux
	superclass: #{Core.ReadStream}
	indexedType: #none
	private: false
	instanceVariableNames: 'repeatPtr repeatLimit '
	classInstanceVariableNames: ''
	imports: ''
	category: ''!

Smalltalk defineClass: #RandomStreamRedux
	superclass: #{Smalltalk.RepeatStreamRedux}
	indexedType: #none
	private: false
	instanceVariableNames: 'random percentages '
	classInstanceVariableNames: ''
	imports: ''
	category: ''!

Smalltalk defineClass: #FastaReduxEnd
	superclass: #{Core.Exception}
	indexedType: #none
	private: false
	instanceVariableNames: ''
	classInstanceVariableNames: ''
	imports: ''
	category: ''!

Smalltalk defineClass: #RandomNumberRedux
	superclass: #{Core.Object}
	indexedType: #none
	private: false
	instanceVariableNames: 'seed scale multiplier increment modulus seedMax '
	classInstanceVariableNames: ''
	imports: ''
	category: ''!


Smalltalk.RandomStreamRedux defineSharedVariable: #LookupSize
	private: false
	constant: false
	category: 'As yet unclassified'
	initializer: nil!



Smalltalk.RandomNumberRedux defineSharedVariable: #FModulus
	private: false
	constant: false
	category: 'As yet unclassified'
	initializer: nil!


Smalltalk.RandomNumberRedux defineSharedVariable: #Increment
	private: false
	constant: false
	category: 'As yet unclassified'
	initializer: nil!


Smalltalk.RandomNumberRedux defineSharedVariable: #Modulus
	private: false
	constant: false
	category: 'As yet unclassified'
	initializer: nil!


Smalltalk.RandomNumberRedux defineSharedVariable: #Multiplier
	private: false
	constant: false
	category: 'As yet unclassified'
	initializer: nil!


Smalltalk.RandomNumberRedux defineSharedVariable: #NextSeedMax
	private: false
	constant: false
	category: 'As yet unclassified'
	initializer: nil!


Smalltalk.RandomNumberRedux defineSharedVariable: #SeedMax
	private: false
	constant: false
	category: 'As yet unclassified'
	initializer: nil!



!RepeatStreamRedux class methodsFor: 'instance creation'!

to: anInteger on: aCollection
   ^(super on: aCollection) to: anInteger! !


!RepeatStreamRedux methodsFor: 'accessing'!

next
   (repeatPtr := repeatPtr + 1) > repeatLimit ifTrue: [FastaReduxEnd raise].
   position >= readLimit ifTrue: [ self position: 0 ].
   ^collection at: (position := position + 1)! !

!RepeatStreamRedux methodsFor: 'initialize-release'!

to: anInteger
   repeatPtr := 0.
   repeatLimit := anInteger! !


!RandomStreamRedux class methodsFor: 'class initialization'!

initialize
	LookupSize := 4 * 1024! !


!RandomStreamRedux methodsFor: 'initialize-release'!

cumulatedPercentagesFor: aCollection scale: scale
   | size cp cumulatedPercentages |
   size := aCollection size.
   cumulatedPercentages := Array new: size.
   cp := 0.0d0.
   1 to: size do: [:i|
      cumulatedPercentages at: i put: (cp := cp + (aCollection at: i) last) * scale.
   ].
   cumulatedPercentages at: size put: scale.
  ^cumulatedPercentages!

on: aCollection
   | size j cumulatedPercentages scale |
   repeatPtr := 0.
   size := LookupSize.
   scale := size - 1.
   random := RandomNumberRedux to: scale.
   cumulatedPercentages := self cumulatedPercentagesFor: aCollection scale: scale.
   percentages := Array new: size.
   collection := Array new: size.
   j := 1.
   1 to: size do: [:i |
      [(cumulatedPercentages at: j) < (i - 1)] whileTrue: [j := j + 1].
      collection at: i put: (aCollection at: j) first.
      percentages at: i put: (cumulatedPercentages at: j).
   ].! !

!RandomStreamRedux methodsFor: 'accessing'!

random: aRandomNumber
   random := aRandomNumber!

next
   | r i |
   (repeatPtr := repeatPtr + 1) > repeatLimit ifTrue: [FastaReduxEnd raise].
   r := random next.
   i := r truncated.
   [r > (percentages at: (i := i + 1))] whileTrue.
   ^collection at: i!

random
	^random! !


!RandomNumberRedux class methodsFor: 'class initialization'!

initialize
   FModulus := 139968.0d0.
   Increment := 29573.
   Modulus := 139968.
   Multiplier := 3877.
   SeedMax := SmallInteger maxVal - Increment // Multiplier.
   NextSeedMax := SeedMax * Multiplier + Increment \\ Modulus.! !

!RandomNumberRedux class methodsFor: 'instance creation'!

to: anInteger
   ^self basicNew to: anInteger! !


!RandomNumberRedux methodsFor: 'private'!

to: anInteger
   seed := 42.
   scale := anInteger / FModulus.
   multiplier := Multiplier.
   increment := Increment.
   modulus := Modulus.
   seedMax := SeedMax! !

!RandomNumberRedux methodsFor: 'accessing'!

next
   ^seed > seedMax
      ifTrue: [scale * (seed := seed - seedMax * multiplier + NextSeedMax \\ modulus)]
      ifFalse: [scale * (seed := seed * multiplier + increment \\ modulus)]! !


!Tests class methodsFor: 'benchmark scripts'!

fastaredux
   self fastaRedux: self arg to: self stdoutSpecial.
   ^''! !

!Tests class methodsFor: 'benchmarking'!

writeFastaRedux: aString from: inStream to: outStream lineLength: lineLength
   | i |
   outStream nextPut: $>; nextPutAll: aString; nl.
   i := 0.
  [ [
      outStream nextPut: inStream next.
     (i := i + 1) == lineLength ifTrue: [outStream nl. i := 0]
   ] repeat ]
         on: FastaReduxEnd do: [:ignoreThisException | ].
   i = 0 ifFalse: [outStream nl]!

fastaRedux: n to: out
   | lineLength iub sapiens |
   lineLength := 60.
   self
      writeFastaRedux: 'ONE Homo sapiens alu'
      from:
         ( RepeatStreamRedux
            to: n*2
            on:'GGCCGGGCGCGGTGGCTCACGCCTGTAATCCCAGCACTTTGG',
               'GAGGCCGAGGCGGGCGGATCACCTGAGGTCAGGAGTTCGAGA',
               'CCAGCCTGGCCAACATGGTGAAACCCCGTCTCTACTAAAAAT',
               'ACAAAAATTAGCCGGGCGTGGTGGCGCGCGCCTGTAATCCCA',
               'GCTACTCGGGAGGCTGAGGCAGGAGAATCGCTTGAACCCGGG',
               'AGGCGGAGGTTGCAGTGAGCCGAGATCGCGCCACTGCACTCC',
               'AGCCTGGGCGACAGAGCGAGACTCCGTCTCAAAAA' )
      to: out
      lineLength: lineLength.

   iub :=  RandomStreamRedux
            to: n*3
            on: #(   #($a 0.27d0)
                  #($c 0.12d0)
                  #($g 0.12d0)
                  #($t 0.27d0)

                  #($B 0.02d0)
                  #($D 0.02d0)
                  #($H 0.02d0)
                  #($K 0.02d0)
                  #($M 0.02d0)
                  #($N 0.02d0)
                  #($R 0.02d0)
                  #($S 0.02d0)
                  #($V 0.02d0)
                  #($W 0.02d0)
                  #($Y 0.02d0)).
   sapiens :=  RandomStreamRedux
            to: n*5
            on: #(   #($a 0.3029549426680d0)
                  #($c 0.1979883004921d0)
                  #($g 0.1975473066391d0)
                  #($t 0.3015094502008d0)).

   sapiens random: iub random. "Share random sequence"

   self
      writeFastaRedux: 'TWO IUB ambiguity codes'
      from: iub
      to: out
      lineLength: lineLength.

   self
      writeFastaRedux: 'THREE Homo sapiens frequency'
      from: sapiens
      to: out
      lineLength: lineLength.

   out flush.! !



#{RandomStreamRedux} initialize!

#{RandomNumberRedux} initialize!
