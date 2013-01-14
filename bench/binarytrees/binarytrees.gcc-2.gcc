/* 
 * The Computer Language Benchmarks Game 
 * http://benchmarksgame.alioth.debian.org/
 *
 * Contributed by Mr Ledrug
*/

#include <stdio.h>
#include <stdlib.h>

typedef struct node_s {
   int item;
   struct node_s *left, *right;
} node;

node * make_tree(int depth, int root_item) {
   const int len = 1 << depth;

   // allocate the whole tree in one go, more of a bin heap really
   node * const root = (node*)calloc(len, sizeof(node));

   root[1].item = root_item;
   root[1].left = root + 2;
   root[1].right = root + 3;

   for (int i = 2; i < len; i++) {
      root[i].item = 2 * root[i/2].item - 1 + (i&1);

      if (i * 2 >= len) continue;

      root[i].left = root + i * 2;
      root[i].right = root + i * 2 + 1;
   }

   return root + 1;
}

void del_tree(node *s) { free(s - 1); }

// It's actually faster to do the check sequentially because the whole
// tree is in one array.  Here traversing the tree by pointer is just
// to show the bin heap functions as a linked tree
int check_tree(const node* const s) {
   if (!s->left)
      return s->item;

   return s->item + check_tree(s->left) - check_tree(s->right);
}

int main(int argc, char **argv) {
   int min = 4;
   int max = atoi(argv[1]);

   if (max < 6) max = 6;

   node *stretch = make_tree(max + 1, 0);
   printf("stretch tree of depth %d\t check: %d\n",
      max + 1, check_tree(stretch));
   del_tree(stretch);

   node *longlived = make_tree(max, 0);

   for (int d = min; d <= max; d+= 2) {
      int iter = 1 << (max + min - d);
      int check = 0;

      /* this can be easily parallel, unfortunately malloc's locking
         slows down the thing by a lot with either -pthread
         or -fopenmp, so much so that it's not worth the hassle */
      for (int i = 1; i <= iter; i++){
         node *s = make_tree(d, i);
         check += check_tree(s);
         del_tree(s);

         s = make_tree(d, -i);
         check += check_tree(s);
         del_tree(s);
      }

      printf("%d\t trees of depth %d\t check: %d\n",
         iter * 2, d, check);
   }

   printf("long lived tree of depth %d\t check: %d\n",
      max, check_tree(longlived));
   del_tree(longlived);

   return 0;
}
