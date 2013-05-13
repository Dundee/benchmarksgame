# The Computer Language Benchmarks Game
# http://benchmarksgame.alioth.debian.org
#
# contributed by Jesse Millikan
# Modified by Wesley Moxam and Michael Klaus
# Modified by  Chris Houhoulis:
#   trigger GC; move stretch_tree into loop; use while instead of a block

def item_check(left, item, right)
  return item if left.nil?
  item + item_check(*left) - item_check(*right)
end

def bottom_up_tree(item, depth)
  return [nil, item, nil] if depth == 0
  item_item = 2 * item
  depth -= 1
  [bottom_up_tree(item_item - 1, depth), item, bottom_up_tree(item_item, depth)]
end

max_depth = ARGV[0].to_i
min_depth = 4

max_depth = [min_depth + 2, max_depth].max

1.times do
  stretch_depth = max_depth + 1
  stretch_tree = bottom_up_tree(0, stretch_depth)
  puts "stretch tree of depth #{stretch_depth}\t check: " +
    "#{item_check(*stretch_tree)}"
end
GC.start

long_lived_tree = bottom_up_tree(0, max_depth)

base_depth = max_depth + min_depth
min_depth.step(max_depth + 1, 2) do |depth|
  iterations = 2 ** (base_depth - depth)

  check, i = 0, 1

  while i <= iterations
    temp_tree = bottom_up_tree(i, depth)
    check += item_check(*temp_tree)

    temp_tree = bottom_up_tree(-i, depth)
    check += item_check(*temp_tree)
    i += 1
  end

  puts "#{iterations * 2}\t trees of depth #{depth}\t check: #{check}"
end
GC.start

puts "long lived tree of depth #{max_depth}\t check: " +
  "#{item_check(*long_lived_tree)}"
