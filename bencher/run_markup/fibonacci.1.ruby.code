
def fib(n)
  raise "fib not defined for negative numbers" if n < 0
  new, old = 1, 0
  n.times {new, old = new + old, new}
  old
end

n = ARGV[0].to_i
puts fib(n)
