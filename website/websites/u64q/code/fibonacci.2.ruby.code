
def matrix_fib(n)
  if n == 1
    [0,1]
  else
    f = matrix_fib(n/2)
    c = f[0] * f[0] + f[1] * f[1]
    d = f[1] * (f[1] + 2 * f[0])
    n.even? ? [c,d] : [d,c+d]
  end
end

def fib(n)
  n.zero? ? n : matrix_fib(n)[1]
end

n = ARGV[0].to_i
puts fib(n)
