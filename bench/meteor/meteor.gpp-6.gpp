/* The Computer Language Benchmarks Game
   http://benchmarksgame.alioth.debian.org/

   contributed by Stefan Westen

   loosely based on Ben St. John's and Kevin Barnes' implementation

   Main improvements:
      - Check for isolated cells instead of bad islands
      - Pre-calculate lists based on availability of 3 neighbouring cells
      - OpenMP tasks
*/

#include <stdio.h>
#include <omp.h>

const int nPieceCount = 10;
const int pieces[10][5][2]  = {
   {{0, 0}, {1, 0}, {2, 0}, {3, 0}, {3, 1}},
   {{0, 0}, {0, 1}, {-2, 2}, {-1, 2}, {-3, 3}},
   {{0, 0}, {1, 0}, {2, 0}, {-1, 1}, {-1, 2}},
   {{0, 0}, {1, 0}, {2, 0}, {1, 1}, {1, 2}},
   {{0, 0}, {0, 1}, {1, 1}, {-1, 2}, {1, 2}},
   {{0, 0}, {1, 0}, {-2, 1}, {-1, 1}, {0, 1}},
   {{0, 0}, {1, 0}, {0, 1}, {-1, 2}, {-1, 3}},
   {{0, 0}, {2, 0}, {-1, 1}, {0, 1}, {1, 1}},
   {{0, 0}, {0, 1}, {0, 2}, {1, 2}, {1, 3}},
   {{0, 0}, {0, 1}, {0, 2}, {-1, 3}, {0, 3}}
   };

unsigned int g_AllMasks[8192];
unsigned int *g_MaskStart[50][8];

unsigned char g_min_solution[50], g_max_solution[50];
unsigned int g_solutions;

unsigned int EvenRowsLookup[50];
unsigned int LeftBorderLookup[50];
   
bool GoodPiece(unsigned int mask, unsigned int pos)
{
   bool bOK(true);
   const unsigned long long even_rows = 0xf07c1f07c1f07c1f;
   const unsigned long long odd_rows = ~even_rows;   
   const unsigned long long left_border = 0x1084210842108421;
   const unsigned long long right_border = left_border >> 1;
   unsigned long long a,b,a_old,s1,s2,s3,s4,s5,s6,s7,s8;
   
   b = (((unsigned long long)mask) << pos) | 0xFFFC000000000000ULL;
   
   b = ~b;

   while (b)
   {
      a = b&-b;

      do 
      {
         s1 = a << 5;
         s2 = a >> 5;
         s3 = (a << 1)&(~left_border);
         s4 = (a >> 1)&(~right_border);
         s5 = ((a & even_rows) >> 6) &(~right_border);
         s6 = ((a & even_rows) << 4) &(~right_border);
         s7 = ((a & odd_rows) >> 4) & (~left_border);
         s8 = ((a & odd_rows) << 6) &(~left_border);
         a_old = a;
         a = (a|s1|s2|s3|s4|s5|s6|s7|s8)&b;
      } while (a_old!=a);
      if (__builtin_popcountll(a)%5!=0)
      {
         bOK = false;
         break;
      }
      b = b ^ a;
   }
   return bOK;
}

void Initialise()
{   
   for (int i=0;i<50;i++)
   {
      EvenRowsLookup[i] = 0xF07C1F07C1F07C1FULL >> i;
      LeftBorderLookup[i] = 0x1084210842108421ULL >> i;
   }
   
   int nTotalCount(0);
   int x[5], y[5];
   for (int nYBase=2;nYBase<4;nYBase++)
   {
      for (int nXBase=0;nXBase<5;nXBase++)
      {
         int nPos = nXBase+5*nYBase;
         g_MaskStart[nPos][0] = &g_AllMasks[nTotalCount];
         for (int nPiece=0;nPiece<nPieceCount;nPiece++)
         {
            for (int j=0;j<5;j++)
            {
               x[j] = pieces[nPiece][j][0];
               y[j] = pieces[nPiece][j][1];
            }
         
            int nCurrentRotation=0;
            for (nCurrentRotation=0;nCurrentRotation<12;nCurrentRotation++)
            {
               if (nPiece!=3||(nCurrentRotation/3)%2==0)
               {
                  int nMinX = x[0];
                  int nMinY = y[0];
                  for (int i=1;i<5;i++)
                  {
                     if (y[i]<nMinY||(y[i]==nMinY&&x[i]<nMinX))
                     {
                        nMinX=x[i];
                        nMinY=y[i];
                     }
                  }
            
                  unsigned int mask = 0;
                  bool bFit(true);
            
                  for (int i=0;i<5;i++)
                  {
                     int nX = (x[i]-nMinX+(nXBase-nYBase/2))
                              +(y[i]-nMinY+nYBase)/2;
                     int nY = y[i]-nMinY+nYBase;
                     if (nX>=0&&nX<5)
                     {
                        int nBit = nX-nXBase+5*(nY-nYBase);
                        mask |= (1<<nBit);
                     }
                     else
                     {
                        bFit = false;
                     }
                  }
                  if (bFit)
                  {
                     if (GoodPiece(mask,nPos))
                     {
                        g_AllMasks[nTotalCount++] = 
                           mask|(1<<(nPiece+22));
                     }
                  }
               }
               for (int i=0;i<5;i++)
               {
                  int xnew = x[i]+y[i];
                  int ynew = -x[i];
                  x[i] = xnew;
                  y[i] = ynew;
                  if (nCurrentRotation==5)
                  {
                     int xnew = x[i]+y[i];
                     int ynew = -y[i];
                     x[i] = xnew;
                     y[i] = ynew;
                  }      
               }
            }
         }
         g_AllMasks[nTotalCount++] = 0;
      }
   }
   
   // copy rows 2 and 3 to other rows
   
   for (int nYBase=0;nYBase<10;nYBase++)
   {
      if (nYBase!=2&&nYBase!=3)
      {
         for (int nXBase=0;nXBase<5;nXBase++)
         {
            int nPos = nXBase+5*nYBase;
            int nOrigPos = nXBase+5*(nYBase%2+2);
            g_MaskStart[nPos][0] = &g_AllMasks[nTotalCount];
            unsigned int *pMask = g_MaskStart[nOrigPos][0];
            unsigned int bottom = (0xFFFC000000000000ULL>>nPos)
                                 &0x003FFFFF;
            unsigned int last_row = (0xFFFC000000000000ULL>>(nPos+5))
                                 &0x003FFFFF;
            while (*pMask)
            {
               unsigned int mask=*pMask;
               pMask++;
               if ((mask&bottom)==0)
               {
                  if (nYBase==0||(mask&last_row))
                  {
                     if (!GoodPiece(mask&0x003FFFFF,nPos))
                     {
                        continue;
                     }
                  }
                  g_AllMasks[nTotalCount++] = mask;
               }
            }
            g_AllMasks[nTotalCount++] = 0;
         }
      }
   }
   
   for (int nFilter=1;nFilter<8;nFilter++)
   {
      for (int nPos=0;nPos<50;nPos++)
      {
         g_MaskStart[nPos][nFilter] = &g_AllMasks[nTotalCount];
         unsigned int filter_mask;
         filter_mask = ((nFilter&1)<<1)|((nFilter&6)<<
                     (4-(EvenRowsLookup[nPos]&1)));
         unsigned int *pMask = g_MaskStart[nPos][0];
         while (*pMask)
         {
            unsigned int mask=*pMask;
            if ((mask&filter_mask)==0)
            {
               g_AllMasks[nTotalCount++] = mask;
            }
            pMask++;
         }
         g_AllMasks[nTotalCount++] = 0;
      }
   }
}

void CompareSolution(unsigned char* board, unsigned char* min_solution,
               unsigned char* max_solution)
{
   int i,j;
   
   for (i=0;i<50;i++)
   {
      if (board[i]<min_solution[i])
      {
         for (j=0;j<50;j++)
         {
            min_solution[j] = board[j];
         }
         break;
      }
      else if (board[i]>min_solution[i])
      {
         break;
      }
   }
   for (i=0;i<50;i++)
   {
      if (board[i]>max_solution[i])
      {
         for (j=0;j<50;j++)
         {
            max_solution[j] = board[j];
         }
         break;
      }
      else if (board[i]<max_solution[i])
      {
         break;
      }
   }
}

void PrintBoard(unsigned char *board)
{
   int i;
   
   for (i=0;i<50;i++)
   {
      printf ("%d ", board[i]);
      if (i%5==4)
      {
         printf("\n");
         if ((i&1)==0)
         {
            printf (" ");
         }
      }
   }
   printf ("\n");
   
}

void RecordSolution(unsigned int current_solution[])
{
   unsigned char board[50], flip_board[50];
   int i;
   unsigned long piece;
   unsigned int mask, pos, current_bit, b1;
   unsigned long count;
   b1 = 0;
   pos = 0;
   for (i=0;i<10;i++)
   {
      mask = current_solution[i];
      piece = __builtin_ctz(mask>>22);
      mask &= 0x003FFFFF;
      b1 |= mask;
      while (mask)
      {
         current_bit = mask&-mask;
         count = __builtin_ctz(current_bit);
         board[count+pos] = piece;
         flip_board[49-count-pos] = piece;
         mask ^= current_bit;
      }
      count = __builtin_ctz(~b1);
      pos+=count;
      b1 >>= count;
   }
   if (g_solutions==0)
   {
      for (i=0;i<50;i++)
      {
         g_min_solution[i] = g_max_solution[i] = board[i];
      }
   }
   else
   {
      CompareSolution(board, g_min_solution, g_max_solution);
      CompareSolution(flip_board, g_min_solution, g_max_solution);
   }
   
   g_solutions+=2;
}
     
void searchLinear(unsigned int board, unsigned int pos, unsigned int used, 
         unsigned int placed, unsigned int current_solution[])
{
   unsigned long count;
   unsigned int even_rows, odd_rows, left_border, right_border, s1, s2, s3,
                  s4, s5, s6, s7, s8;
   if (placed==10)
   {
      #pragma omp critical
      RecordSolution(current_solution);
   }
   else
   {
      even_rows = EvenRowsLookup[pos];

      odd_rows = ~even_rows;
      
      left_border = LeftBorderLookup[pos];
      right_border = left_border>>1;

      s1 = (board << 1) | left_border;
      s2 = (board >> 1) | right_border;
      s3 = (board << 4) | ((1<<4)-1) | right_border;
      s4 = (board >> 4) | left_border;
      s5 = (board << 5) | ((1<<5)-1);
      s6 = (board >> 5);
      s7 = (board << 6) | ((1<<6)-1) | left_border;
      s8 = (board >> 6) | right_border;

      if (~board&s1&s2&s5&s6&((even_rows&s4&s7)|(odd_rows&s3&s8)))
      {
         return;
      }
      
      count = __builtin_ctz(~board);
      pos+=count;
      board >>= count;
      
      unsigned int f;
      f = ((board>>1)&1)|((board>>(4-(EvenRowsLookup[pos]&1)))&6);   
      unsigned int board_and_used = board|used;
      
      unsigned int *masks = g_MaskStart[pos][f];
      unsigned int mask;
      
      while (*masks)
      {
         while ((*masks)&board_and_used)
         {
            masks++;
         }
         if (*masks)
         {
            mask = *masks;
            current_solution[placed] = mask;
            searchLinear(board|((mask&0x003FFFFF)), pos, used|(mask&0xFFC00000),
                  placed+1, current_solution);
            masks++;
         }
      }
   }
}

void searchParallel(unsigned int board, unsigned int pos, unsigned int used, 
         unsigned int placed, unsigned int first_piece)
{
   unsigned long count;
                     
   count = __builtin_ctz(~board);
   pos+=count;
   board >>= count;
   
   unsigned int board_and_used = board|used;
   
   unsigned int *masks = g_MaskStart[pos][0];
   unsigned int mask;
   
   if (placed==0)
   {
      while (*masks)
      {
         while ((*masks)&board_and_used)
         {
            masks++;
         }
         if (*masks)
         {
            mask = *masks++;
            {
               searchParallel(board|((mask&0x003FFFFF)), pos, used|(mask&0xFFC00000),
                  placed+1, mask);
            }
         }
      }
   }
   else
   {   // placed==1
      while (*masks)
      {
         while ((*masks)&board_and_used)
         {
            masks++;
         }
         if (*masks)
         {
            mask = *masks++;
            #pragma omp task default(none) firstprivate(board, mask, pos, used, placed, first_piece)
            {
               unsigned int current_solution[10];
               current_solution[0] = first_piece;
               current_solution[placed] = mask;
               searchLinear(board|((mask&0x003FFFFF)), pos, used|(mask&0xFFC00000),
                  placed+1, current_solution);
            }
         }
      }
   }
}


int main(int argc, char* argv[])
{
   if (argc > 2)
     return 1;

   Initialise();

   g_solutions = 0;

   #pragma omp parallel
   {
      #pragma omp single
      {
         searchParallel(0,0,0,0,0);
      }
   }
   
   printf ("%d solutions found\n\n",g_solutions);
   PrintBoard(g_min_solution);
   PrintBoard(g_max_solution);
                        
   return 0;
}
