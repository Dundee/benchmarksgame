# The Computer Language Benchmarks Game
# http://benchmarksgame.alioth.debian.org
#
# contributed by jose fco. gonzalez
# optimized & parallelized by Rick Branson
# optimized for ruby2 by Aaron Tavistock

require 'fiber'

seq = $stdin.read.force_encoding("ASCII-8BIT")
origin_len = seq.size

seq.gsub!(/>.*\n|\n/,'')
clean_len = seq.size

matchers = [
  'agggtaaa|tttaccct',
  '[cgt]gggtaaa|tttaccc[acg]',
  'a[act]ggtaaa|tttacc[agt]t',
  'ag[act]gtaaa|tttac[agt]ct',
  'agg[act]taaa|ttta[agt]cct',
  'aggg[acg]aaa|ttt[cgt]ccct',
  'agggt[cgt]aa|tt[acg]accct',
  'agggta[cgt]a|t[acg]taccct',
  'agggtaa[cgt]|[acg]ttaccct'
]

results = matchers.map do |matcher|
  Fiber.new do
    count = seq.scan( Regexp.new(matcher) ).size
    Fiber.yield "#{matcher} #{count}"
  end.resume
end

replacements = {
  'B' => '(c|g|t)',
  'D' => '(a|g|t)',
  'H' => '(a|c|t)',
  'K' => '(g|t)',
  'M' => '(a|c)',
  'N' => '(a|c|g|t)',
  'R' => '(a|g)',
  'S' => '(c|t)',
  'V' => '(a|c|g)',
  'W' => '(a|t)',
  'Y' => '(c|t)'
}
seq.gsub!(Regexp.new(replacements.keys.join('|')), replacements)

puts "#{results.join("\n")}\n\n#{origin_len}\n#{clean_len}\n#{seq.size}"
