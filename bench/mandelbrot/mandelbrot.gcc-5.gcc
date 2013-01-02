/* The Computer Language Benchmarks Game
 * http://benchmarksgame.alioth.debian.org/
 *
 * Contributed by Mr Ledrug
*/

#define _GNU_SOURCE
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <sched.h>
#include <pthread.h>
#include <stdint.h>

#define MAX_ITER 50
typedef uint8_t byte;

size_t w, h, row_bytes;
int ncpus;
byte *buf;

double xmin = -1.5, xmax = .5, ymin = -1, ymax = 1;
double dx, dy;

void calc_row(size_t row) {
   byte *pos = buf + row * row_bytes;

   double cx, cy = ymin + row * dy;

   byte mask = 1 << 7;
   size_t i, row_bits = row_bytes * 8;

   int bound = 0;
   for (i = 0; i < row_bits; i++) {
      cx = xmin + i * dx;
      double zx = cx, zy = cy;

      int iter;
      if (bound) {
         double p = cx - 1;
         if (cy * cy + p * p < 1./16)
            goto skip;

         p = cy * cy + (cx - 0.25) * (cx - 0.25);
         if ((p * (p + cx - 0.25)) * 4 < cy * cy)
            goto skip;

         for (iter = MAX_ITER - 1; iter--; ) {
            double x2 = zx * zx, y2 = zy * zy;
            zy = cy + zx * zy * 2;
            zx = cx + x2 - y2;
         }
         bound = (zx * zx + zy * zy) <= 4;

      } else {
         for (iter = MAX_ITER; iter--; ) {
            double x2 = zx * zx, y2 = zy * zy;

            if (x2 + y2 > 4) goto skip;

            zy = cy + zx * zy * 2;
            zx = cx + x2 - y2;
         }
         bound = 1;
      }

skip:
      if (bound) *pos |= mask;
      mask >>= 1;
      if (!mask) {
         mask = 1 << 7;
         pos++;
      }
   }
}

size_t next_row;
void *thread_entry(void *dummy) {
   while (1) {
      size_t r = next_row;
      if (!__sync_bool_compare_and_swap(&next_row, r, r + 1)) {
         volatile int i = 3000;
         while (i--);
         sched_yield();
         continue;
      }

      if (r >= h) return 0;
      calc_row(r);
   }
}

int get_cpus(void) {
   cpu_set_t ct;
   sched_getaffinity(0, sizeof(ct), &ct);
   int i, cnt = 0;
   for (i = 0; i < 16; i++)
      if (CPU_ISSET(i, &ct))
         cnt++;
   return cnt;
}

int main(int artc, char **argv) {
   ncpus = get_cpus();

   w = atoi(argv[1]);
   w = (w + 7) / 8 * 8;
   row_bytes = w / 8;

   dx = (xmax - xmin) / w;
   dy = (ymax - ymin) / w;

   h = w / 2 + 1;

   size_t n_bytes = h * row_bytes;
   buf = calloc(1, n_bytes);

   int i;
   pthread_t tid[16];

   for (i = 0; i < ncpus; i++)
      pthread_create(tid + i, 0, thread_entry, 0);

   for (i = 0; i < ncpus; i++)
      pthread_join(tid[i], 0);

   char header[100];
   sprintf(header, "P4\n%d %d\n", (int)w, (int)w);

   int fd = fileno(stdout);
   write(fd, header, strlen(header));
   write(fd, buf, n_bytes);

   h--;
   while (h-- > 1)
      write(fd, buf + h * row_bytes, row_bytes);

   return 0;
}
