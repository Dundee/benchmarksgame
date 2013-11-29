/* The Computer Language Benchmarks Game 
 * http://benchmarksgame.alioth.debian.org/
 *
 * Contributed by Jon Harrop
 * Modified by Alex Mizrahi
 * Modified by Bruno Coutinho to use apr like the C version
 */

#include <iostream>
#include <sstream>
#include <cstdlib>
 
typedef off_t off64_t;
#include <apr_pools.h>

class Node {
public:
  inline Node(int i2) : l(0), r(0), i(i2) {}

  inline Node(Node* __restrict__ l2, int i2, Node* __restrict__ r2) 
    :l(l2)
    ,r(r2)
    ,i(i2) 
    {}

  inline ~Node() {}

  int check() const {
    if (l) return l->check() + i - r->check(); 
    else return i;
  }

private:
  Node* __restrict__ l;
  Node* __restrict__ r;
  int i;
};

Node* make(int i, int depth, apr_pool_t* pool) {
  Node* __restrict__ addr  = (Node*) apr_palloc(pool, sizeof(Node));
  Node* __restrict__ left  = NULL;
  Node* __restrict__ right = NULL;

  if (depth > 0) {
    left  = make (2*i-1, depth - 1, pool);
    right = make (2*i  , depth - 1, pool);
  }   

  Node* __restrict__ curr = new (addr) Node(left, i, right);
  return curr;
}

int main(int argc, char *argv[]) {
  apr_pool_t* long_lived_pool = NULL;
  int min_depth = 4;
  int max_depth = std::max(min_depth+2,
			 (argc == 2 ? atoi(argv[1]) : 10));
  int stretch_depth = max_depth+1;

  apr_initialize();
  std::cout.sync_with_stdio(false);

  {
    apr_pool_t* store;
    apr_pool_create(&store, NULL);

    Node* c = make(0, stretch_depth, store);
    std::cout << "stretch tree of depth " << stretch_depth << "\t "
      << "check: " << c->check() << std::endl;
    apr_pool_destroy(store);
  }

  std::string* output = new std::string[max_depth +1];
#pragma omp parallel for schedule (dynamic)
  for (int d=min_depth; d<=max_depth; d+=2) {
    int iterations = 1 << (max_depth - d + min_depth);
    apr_pool_t *store;
    int c = 0;

    apr_pool_create (&store, NULL);
    for (int i=1; i<=iterations; ++i) {
      Node* __restrict__ a = make(i, d, store);
      Node* __restrict__ b = make(-i, d, store);

      c += a->check() + b->check();
      apr_pool_clear(store);
    }
    apr_pool_destroy(store);
    
    // each thread write to separate location
    std::stringstream ss;
    ss << (2 * iterations) << "\t trees of depth ";
    ss << d << "\t check: " << c << std::endl;
    output[d] = ss.str();
  }

  // print all results
  for (int d = min_depth; d <= max_depth; d += 2)
    std::cout << output[d];
  delete[] output;

  apr_pool_create(&long_lived_pool, NULL);
  Node* long_lived_tree = make(0, max_depth, long_lived_pool);
  std::cout << "long lived tree of depth " << max_depth << "\t "
	    << "check: " << (long_lived_tree->check()) << "\n";
  apr_pool_destroy(long_lived_pool);

  return 0;
}
