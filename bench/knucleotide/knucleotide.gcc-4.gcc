/* 
 * The Computer Language Benchmarks Game 
 * http://benchmarksgame.alioth.debian.org/
 *
 * Contributed by Mr Ledrug
*/

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <ctype.h>
#include <stdint.h>
#include <pthread.h>

typedef unsigned char byte;
byte * input;
size_t input_len;

byte trans[128];
const char *codes = "ACGT";

// read in all the "acgt"s and translate them to numbers 0-3
void get_input(void) {
   int fd = fileno(stdin);
   size_t buflen = 1<<20, len, i, section = 0;

   input = malloc(buflen + 1);
   input_len = 0;

   while (1) {
      len = read(fd, input, buflen);
      for (i = 0; i < len; i++)
         if (input[i] == '>' && ++section == 3)
            goto found;

      if (len < buflen) return;
   }

found:
   input_len = buflen - i;
   memmove(input, input + i, input_len);
   input[input_len] = 0;

   while (1) {
      if (buflen < input_len * 2) {
         buflen *= 2;
         input = realloc(input, buflen + 1);
      }

      len = read(fd, input + input_len, buflen - input_len);
      input_len += len;
      input[input_len] = 0;

      if (input_len < buflen) {
         byte *in = input, *ptr = input;
         int c;

         while (*in && *in != '\n') in++;

         while ((c = *in++))
            if (c != '\n')
               *ptr++ = trans[c];

         input_len = ptr - input;
         input[input_len] = 0;
         return;
      }
   }
}

#define SIMPLE_MAX 8
typedef struct { int key, count; } count_t;
count_t *counts[SIMPLE_MAX + 1];

typedef uint64_t hkey_t;
typedef struct {
   hkey_t key;
   int count;
} hrec_t;

typedef struct {
   int size, cap, limit, mask;
   hrec_t *table;
   pthread_mutex_t lock;
} ht_table;

ht_table tables[19];

void ht_init(ht_table *t) {
   t->size = 0;
   t->cap = 8;
   t->limit = t->cap / 2;
   t->mask = t->cap - 1;
   t->table = calloc(t->cap, sizeof(hrec_t));
}

void extend_htable(ht_table *t) {
   int i;
   int new_cap = t->cap * 2;
   t->limit = new_cap / 2;
   t->mask = new_cap - 1;

   hrec_t *new = calloc(new_cap, sizeof(hrec_t));
   hrec_t *old = t->table;

   for (i = 0; i < t->cap; i++) {
      if (!old[i].count) continue;
      hrec_t *p = new + (old[i].key & t->mask);
      if (!p->count) {
         *p = old[i];
         continue;
      }
      while (1) {
         if (--p < new)
            p = new + t->mask;
         if (p->count) continue;
         *p = old[i];
         break;
      }
   }
   t->table = new;
   t->cap = new_cap;
}

// after each thread finished a slice, lock and update the overall table
void hash_merge(ht_table *a, ht_table *b) {
   int i;
   hrec_t *in;

   pthread_mutex_lock(&a->lock);

   for (i = 0, in = b->table; i < b->cap; i++, in++) {
      if (!in->key) continue;
      hrec_t *p = a->table + (in->key & a->mask);
      while (1) {
         if (!p->key) {
            *p = *in;
            if (a->size++ == a->limit)
               extend_htable(a);
            break;
         }
         if (p->key == in->key) {
            p->count += in->count;
            break;
         }
         if (--p < a->table)
            p = a->table + a->mask;
      }
   }

   pthread_mutex_unlock(&a->lock);
   free(b->table);
}

// hash key is just len numbers of 2-bit inters joined together
void count_hash(byte *s, byte *e, int len, int step) {
   int i;

   ht_table t;
   ht_init(&t);

   void inc_key(hkey_t key) {
      int k = key & t.mask;
      hrec_t *p = t.table + k;

      while (1) {
         if (p->key == key) {
            p->count++;
            return;
         }
         if (!p->key) {
            p->key = key;
            p->count = 1;
            if (++t.size == t.limit)
               extend_htable(&t);
            return;
         }
         if (--p < t.table)
            p = t.table + t.mask;
      }
   }

   e -= len;
   while (s < e) {
      hkey_t key = 0;
      for (i = 0; i < len; i++)
         key = (key << 2) | s[i];
      inc_key(key + 1);
      s += step;
   }

   hash_merge(tables + len, &t);
}

// small sequences just map to array indices
void count_simple(int slen) {
   int i, k, mask = (1 << (2 * slen)) - 1;
   byte *end = input_len + input;
   byte *s = input;

   int len = 1 << (2 * slen);
   count_t *buf = counts[slen] = malloc(sizeof(count_t) * len);

   for (i = 0; i < len; i++)
      buf[i].count = 0, buf[i].key = i;

   for (i = 1, k = 0; i < slen; i++)
      k = (k << 2) | *s++;

   while (s < end) {
      k = ((k << 2) | *s++) & mask;
      buf[k].count++;
   }
}

typedef struct work_s {
   byte *start, *end;
   int len, step;
   struct work_s *next;
} work_t;

work_t *jobs;

void add_work(byte *start, byte *end, int len, int step) {
   work_t *w = malloc(sizeof(work_t));
   w->next = jobs;
   jobs = w;
   w->len = len;
   w->start = start;
   w->end = end;
   w->step = step;
}

void add_simple_work(int len) {
   add_work(input, input + input_len, len, 0);
}

void show_works(void) {
   work_t *w = jobs;
   while (w) {
      printf("len %d from %p to %p\n", w->len, w->start, w->end);
      w = w->next;
   }
}

// lock for job control
pthread_mutex_t mux = PTHREAD_MUTEX_INITIALIZER;

void *worker(void *arg) {
   while (1) {
      pthread_mutex_lock(&mux);
      if (!jobs) break;
      work_t *w = jobs;
      jobs = jobs->next;
      pthread_mutex_unlock(&mux);
      if (w->len <= SIMPLE_MAX)
         count_simple(w->len);
      else
         count_hash(w->start, w->end, w->len, w->step);
      free(w);
   }

   pthread_mutex_unlock(&mux);
   return 0;
}

int cmp_count(const void *a, const void *b) {
   const count_t *aa = a, *bb = b;
   if (aa->count < bb->count) return 1;
   if (aa->count > bb->count) return -1;
   if (aa->key < bb->key) return 1;
   return -1;
}

void key_print(int len, int key) {
   char buf[32];
   buf[len] = 0;
   while (len--) {
      buf[len] = codes[key & 3];
      key >>= 2;
   }
   printf("%s", buf);
}

void show_sorted(int len) {
   size_t size = sizeof(count_t) << (2 * len);
   count_t *copy = malloc(size);
   memcpy(copy, counts[len], size);
   qsort(copy, 1 << (2 * len), sizeof(count_t), cmp_count);

   int i, sum = 0;

   for (i = 0; i < 1 << (2 * len); i++)
      sum += copy[i].count;

   for (i = 0; i < 1 << (2 * len); i++) {
      key_print(len, copy[i].key);
      printf(" %.3f\n", (double)copy[i].count / sum * 100);
   }
   puts("");

   free(copy);
}

int count_lookup(char *name) {
   hkey_t key = 0;
   char *s = name;
   int len = 0;
   while (*s) {
      key = (key << 2) | trans[(int)*s];
      s++;
      len++;
   }
   if (len <= SIMPLE_MAX)
      return counts[len][key].count;

   key++;
   ht_table *t = tables + len;
   hrec_t *p = t->table + (key & t->mask);

   while (p->key) {
      if (p->key == key)
         return p->count;
      if (--p <= t->table)
         p = t->table + t->mask;
   }
   return 0;
}

int main(void) {
   int i;
#   define N sizeof(l) / sizeof(l[0])

   for (i = 1; codes[i]; i++) {
      trans[toupper(codes[i])] = i;
      trans[tolower(codes[i])] = i;
   }

   get_input();

   int n_cpus = sysconf(_SC_NPROCESSORS_ONLN);
   if (n_cpus > 4) n_cpus = 4;

   ht_init(tables + 12);
   ht_init(tables + 18);

   // short sequences are pretty fast anyway, just let each
   // thread do the whole piece
   add_simple_work(1);
   add_simple_work(2);
   add_simple_work(3);
   add_simple_work(4);
   add_simple_work(6);

#define S 16
   for (i = 0; i < S; i++) {
      add_work(input+i, input + input_len, 12, S);
      add_work(input+i, input + input_len, 18, S);
   }

   char *names[] = { "GGT", "GGTA", "GGTATT", "GGTATTTTAATT", "GGTATTTTAATTTATAGT", 0 };

   //show_works();

   pthread_t tid[4];
   for (i = 0; i < n_cpus; i++)
      pthread_create(tid + i, 0, worker, 0);

   for (i = 0; i < n_cpus; i++)
      pthread_join(tid[i], 0);

   show_sorted(1);
   show_sorted(2);

   for (i = 0; names[i]; i++) {
      printf("%d\t%s\n", count_lookup(names[i]), names[i]);
   }

   return 0;
}
