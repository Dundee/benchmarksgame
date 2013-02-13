/* The Computer Language Benchmarks Game
   http://benchmarksgame.alioth.debian.org/

   contributed by Mark C. Lewis
   modified slightly by Chad Whipkey
   modified by Robert McKee 
*/
using System;
class NBody
{
   public static void Main(String[] args)
   {
      int n = 10000;
      if (args.Length > 0) n = Int32.Parse(args[0]);

      NBodySystem bodies = new NBodySystem();

      Console.WriteLine("{0:f9}", bodies.Energy());
      for (int i = 0; i < n; i++)
         bodies.Advance(0.01);
      Console.WriteLine("{0:f9}", bodies.Energy());
   }
}


class NBodySystem
{
   const double PI = Math.PI;
   const double SOLAR_MASS = 4 * PI * PI;
   const double DAYS_PER_YEAR = 365.24;
   private Body[] bodies;
   const int bl = 5;

   public NBodySystem()
   {
      bodies = new Body[] {
         new Body() { // Sun
            mass = SOLAR_MASS,
         },
         new Body() { // Jupiter
            x = 4.84143144246472090e+00,
            y = -1.16032004402742839e+00,
            z = -1.03622044471123109e-01,
            vx = 1.66007664274403694e-03 * DAYS_PER_YEAR,
            vy = 7.69901118419740425e-03 * DAYS_PER_YEAR,
            vz = -6.90460016972063023e-05 * DAYS_PER_YEAR,
            mass = 9.54791938424326609e-04 * SOLAR_MASS,
         },
         new Body() { // Saturn
            x = 8.34336671824457987e+00,
            y = 4.12479856412430479e+00,
            z = -4.03523417114321381e-01,
            vx = -2.76742510726862411e-03 * DAYS_PER_YEAR,
            vy = 4.99852801234917238e-03 * DAYS_PER_YEAR,
            vz = 2.30417297573763929e-05 * DAYS_PER_YEAR,
            mass = 2.85885980666130812e-04 * SOLAR_MASS,
         },
         new Body() { // Uranus
            x = 1.28943695621391310e+01,
            y = -1.51111514016986312e+01,
            z = -2.23307578892655734e-01,
            vx = 2.96460137564761618e-03 * DAYS_PER_YEAR,
            vy = 2.37847173959480950e-03 * DAYS_PER_YEAR,
            vz = -2.96589568540237556e-05 * DAYS_PER_YEAR,
            mass = 4.36624404335156298e-05 * SOLAR_MASS,
         },
         new Body() { // Neptune
            x = 1.53796971148509165e+01,
            y = -2.59193146099879641e+01,
            z = 1.79258772950371181e-01,
            vx = 2.68067772490389322e-03 * DAYS_PER_YEAR,
            vy = 1.62824170038242295e-03 * DAYS_PER_YEAR,
            vz = -9.51592254519715870e-05 * DAYS_PER_YEAR,
            mass = 5.15138902046611451e-05 * SOLAR_MASS,
         },
      };
      double px = 0;
      double py = 0;
      double pz = 0;
      foreach (Body body in bodies)
      {
         px += body.vx * body.mass;
         py += body.vy * body.mass;
         pz += body.vz * body.mass;
      }
      var sol = bodies[0];
      sol.vx = -px / SOLAR_MASS;
      sol.vy = -py / SOLAR_MASS;
      sol.vz = -pz / SOLAR_MASS;
   }

   public void Advance(double dt)
   {
      double dx, dy, dz, distance, mag, bim, bjm;
      double x, y, z;
      for (int i = 0; i < bl; i++)
      {
         Body bi = bodies[i];
         x = bi.x;
         y = bi.y;
         z = bi.z;

         for (int j = i + 1; j < bl; j++)
         {
            Body bj = bodies[j];
            dx = x - bj.x;
            dy = y - bj.y;
            dz = z - bj.z;

            distance = Math.Sqrt(dx * dx + dy * dy + dz * dz);
            mag = dt / (distance * distance * distance);

            bim = bi.mass * mag;
            bjm = bj.mass * mag;

            bi.vx -= dx * bjm;
            bi.vy -= dy * bjm;
            bi.vz -= dz * bjm;

            bj.vx += dx * bim;
            bj.vy += dy * bim;
            bj.vz += dz * bim;
         }

         bi.x = x + dt * bi.vx;
         bi.y = y + dt * bi.vy;
         bi.z = z + dt * bi.vz;
      }
   }

   public double Energy()
   {
      double dx, dy, dz, distance;
      double e = 0;

      for (int i = 0; i < bl; i++)
      {
         Body bi = bodies[i];
         e += 0.5 * bi.mass * (bi.vx * bi.vx + bi.vy * bi.vy + bi.vz * bi.vz);

         for (int j = i + 1; j < bl; j++)
         {
            Body bj = bodies[j];
            dx = bi.x - bj.x;
            dy = bi.y - bj.y;
            dz = bi.z - bj.z;

            distance = Math.Sqrt(dx * dx + dy * dy + dz * dz);
            e -= (bi.mass * bj.mass) / distance;
         }
      }
      return e;
   }
}


class Body
{
   public double x, y, z, vx, vy, vz, mass;
}
