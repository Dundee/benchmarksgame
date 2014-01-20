// The Computer Language Benchmarks Game
// http://benchmarksgame.alioth.debian.org/
//
// Contributed by Jeremy Zerfas
// Partially based on code by Elam Kolenovic and Sean Stanek

#include <stdint.h>
#include <stdlib.h>
#include <stdio.h>

#define LIMIT_SQUARED   4.0
#define MAX_ITERATIONS   50

int main(int argc, char ** argv){
   // Ensure image_Width_And_Height are multiples of 8.
   const intmax_t image_Width_And_Height=(atoi(argv[1])+7)/8*8;

   // The image will be black and white with one bit for each pixel. Bits with
   // a value of zero are white pixels which are the ones that "escape" from
   // the Mandelbrot set. We'll be working on one line at a time and each line
   // will be made up of pixel groups that are eight pixels in size so each
   // pixel group will be one byte. This allows for some more optimizations to
   // be done.
   const intmax_t pixel_Groups_Per_Line=image_Width_And_Height/8;
   uint8_t * const pixels=malloc(image_Width_And_Height*
     image_Width_And_Height/8);

   // Precompute the initial real and imaginary values for each x and y
   // coordinate in the image.
   double initial_r[image_Width_And_Height], initial_i[image_Width_And_Height];
   const double two_Over_Image_Width_And_Height=2.0/image_Width_And_Height;
#pragma omp parallel for
   for(intmax_t xy=0; xy<image_Width_And_Height; xy++){
      initial_r[xy]=xy*two_Over_Image_Width_And_Height - 1.5;
      initial_i[xy]=xy*two_Over_Image_Width_And_Height - 1.0;
   }

#pragma omp parallel for schedule(guided)
   for(intmax_t y=0; y<image_Width_And_Height; y++){
      const double prefetched_Initial_i=initial_i[y];
      uint8_t * const line_Pixel_Groups=&pixels[y*pixel_Groups_Per_Line];
      for(intmax_t x_Major=0; x_Major<image_Width_And_Height; x_Major+=8){

         // pixel_Group_r and pixel_Group_i will store real and imaginary
         // values for each pixel in the current pixel group as we perform
         // iterations. Set their initial values here.
         double pixel_Group_r[8], pixel_Group_i[8];
         const double * const current_Pixel_Group_Initial_r=
           &initial_r[x_Major];
         for(intmax_t x_Minor=0; x_Minor<8; ++x_Minor){
            pixel_Group_r[x_Minor]=current_Pixel_Group_Initial_r[x_Minor];
            pixel_Group_i[x_Minor]=prefetched_Initial_i;
         }

         // If any pixels from the previous pixel group escaped then we are
         // likely outside the Mandelbrot set or near the edge of it so
         // check whether pixels escape during each iteration. If no pixels
         // from the previous pixel group escaped then the pixels for the
         // current pixel group are likely to be in the Mandelbrot set so
         // we'll just perform all iterations and do one final check at the
         // end to see if any of the pixels escaped. 
         static uint8_t any_Pixels_Escape=1;
         uint8_t eight_Pixels;
         if(any_Pixels_Escape){
            // Assume all pixels are in the Mandelbrot set initially.
            eight_Pixels=0xff;

            intmax_t iteration=MAX_ITERATIONS;
            do{
               uint8_t current_Pixel_Bitmask=0x80;
               for(intmax_t x_Minor=0; x_Minor<8; x_Minor++){
                  // Only process the pixels that are still in the
                  // Mandelbrot set.
                  if(eight_Pixels & current_Pixel_Bitmask){
                     const double r=pixel_Group_r[x_Minor];
                     const double i=pixel_Group_i[x_Minor];

                     pixel_Group_i[x_Minor]=2.0*r*i +
                       prefetched_Initial_i;
                     pixel_Group_r[x_Minor]=r*r - i*i +
                       current_Pixel_Group_Initial_r[x_Minor];

                     // Clear the bit for the pixel if it escapes from
                     // the Mandelbrot set.
                     if(r*r + i*i>LIMIT_SQUARED)
                        eight_Pixels ^= current_Pixel_Bitmask;
                  }

                  current_Pixel_Bitmask>>=1;
               }
            }while(eight_Pixels && --iteration);
         }else{
            // One more iteration is done further below which is why
            // MAX_ITERATIONS-1 iterations are done here instead of
            // MAX_ITERATIONS.
            for(intmax_t iteration=0; iteration<MAX_ITERATIONS-1;
              iteration++){
               for(intmax_t x_Minor=0; x_Minor<8; x_Minor++){
                  const double r=pixel_Group_r[x_Minor];
                  const double i=pixel_Group_i[x_Minor];

                  pixel_Group_i[x_Minor]=2.0*i*r + prefetched_Initial_i;
                  pixel_Group_r[x_Minor]=r*r - i*i +
                    current_Pixel_Group_Initial_r[x_Minor];
               }
            }

            // Assume all pixels escape initially.
            eight_Pixels=0x00;

            uint8_t current_Pixel_Bitmask=0x80;
            for(intmax_t x_Minor=0; x_Minor<8; x_Minor++){
               const double r=pixel_Group_r[x_Minor];
               const double i=pixel_Group_i[x_Minor];

               // Set the bit for pixels that are still in the Mandelbrot
               // set.
               if(r*r + i*i<=LIMIT_SQUARED)
                  eight_Pixels|=current_Pixel_Bitmask;

               current_Pixel_Bitmask>>=1;
            }
         }

         line_Pixel_Groups[x_Major>>3]=eight_Pixels;
         any_Pixels_Escape=eight_Pixels!=0xff;
      }
   }

   // Output the image to stdout.
   printf("P4\n%ju %ju\n", image_Width_And_Height, image_Width_And_Height);
   fwrite(pixels, image_Width_And_Height*image_Width_And_Height/8, 1, stdout);

   free(pixels);

   return 0;
}
