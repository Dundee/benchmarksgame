/* The Computer Language Benchmarks Game
   http://benchmarksgame.alioth.debian.org/

   contributed by Branimir Maksimovic
*/

#include <cstdlib>
#include <cstdio>
#include <cstring>
#include <algorithm>
#include <immintrin.h>

int checksum;
int maxflips;
typedef unsigned char int_t;

void reverse(int_t* p,int n)
{
   static __attribute__((aligned(16))) unsigned long long w[11][2] =
   {   {0x0706050403020001ll,0x0f0e0d0c0b0a0908ll}, 
      {0x0706050403000102ll,0x0f0e0d0c0b0a0908ll},
      {0x0706050400010203ll,0x0f0e0d0c0b0a0908ll},
      {0x0706050001020304ll,0x0f0e0d0c0b0a0908ll},
      {0x0706000102030405ll,0x0f0e0d0c0b0a0908ll},
      {0x0700010203040506ll,0x0f0e0d0c0b0a0908ll},
      {0x0001020304050607ll,0x0f0e0d0c0b0a0908ll},
      {0x0102030405060708ll,0x0f0e0d0c0b0a0900ll},
      {0x0203040506070809ll,0x0f0e0d0c0b0a0001ll},
      {0x030405060708090all,0x0f0e0d0c0b000102ll},
      {0x0405060708090a0bll,0x0f0e0d0c00010203ll},
   };
   
   __m128i a,b;
   b = _mm_load_si128((__m128i*)&w[n-2][0]);
   a = _mm_load_si128((__m128i*)p);
   a = _mm_shuffle_epi8(a,b);
   _mm_store_si128((__m128i*)p,a);
}

void rotate(int_t* p, int n)
{
   static __attribute__((aligned(16))) unsigned long long w[11][2] =
   {   {0x0706050403020001ll,0x0f0e0d0c0b0a0908ll}, 
      {0x0706050403000201ll,0x0f0e0d0c0b0a0908ll},
      {0x0706050400030201ll,0x0f0e0d0c0b0a0908ll},
      {0x0706050004030201ll,0x0f0e0d0c0b0a0908ll},
      {0x0706000504030201ll,0x0f0e0d0c0b0a0908ll},
      {0x0700060504030201ll,0x0f0e0d0c0b0a0908ll},
      {0x0007060504030201ll,0x0f0e0d0c0b0a0908ll},
      {0x0807060504030201ll,0x0f0e0d0c0b0a0900ll},
      {0x0807060504030201ll,0x0f0e0d0c0b0a0009ll},
      {0x0807060504030201ll,0x0f0e0d0c0b000a09ll},
      {0x0807060504030201ll,0x0f0e0d0c000b0a09ll},
   };
   
   __m128i a,b;
   b = _mm_load_si128((__m128i*)&w[n-1][0]);
   a = _mm_load_si128((__m128i*)p);
   a = _mm_shuffle_epi8(a,b);
   _mm_store_si128((__m128i*)p,a);
}

bool next_permutation(int_t* beg, int n, int_t* c)
{
   int i = 1;
   while(i<n)
   {
      rotate(beg,i);
      if(c[i]>=i)c[i++]=0;
      else break;
   }
   if(i>=n)return false;
   ++c[i];
   return true;
}

struct next{
      next():n(0){}
      int_t operator ()(){ return ++n;}
      int_t n;
};

void fannkuch(int n)
{
   checksum = 0;
   maxflips = 0;
   __attribute__((aligned(16))) int_t perm[16];
   __attribute__((aligned(16))) int_t tperm[16];
   int permcount = 0;
   
   int_t count[16]={0};
   
   std::generate(perm,perm+n,next());
   do
   {
      std::copy(perm,perm+n,tperm);
      int flips = 0;
      while(tperm[0] != 1)
      {
         reverse(tperm,tperm[0]);
         ++flips;
      }
      checksum += (permcount%2 == 0)?flips:-flips;
      maxflips = std::max(maxflips,flips);
   }while(++permcount,next_permutation(perm,n,count));
}

int main(int argc, char** argv)
{
   int n = 7;
   if(argc > 1)n = atoi(argv[1]);
   if(n < 3 || n > 12)
   {
      printf("n should be between 3 and 12\n");
      return 0;
   }
   fannkuch(n);
   printf("%d\nPfannkuchen(%d) = %d\n",checksum,n,maxflips);
}
