/* The Computer Language Benchmarks Game
   http://benchmarksgame.alioth.debian.org/

   contributed by Robert F. Tobler
   use of Mono.Simd by Markus Uhr
*/

using System;
using Mono.Simd;

using Pair = System.Tuple<Body,Body>;

class Body
{
   public Vector2d xy;
   public Vector2d z0;
   public Vector2d vxvy;
   public Vector2d vz00;
   public Vector2d mass;
}

class NBodySystem {
   private Body[] bodies;
   private Pair[] pairs;

   private const double pi = 3.141592653589793;
   private const double sm = 4.0*pi*pi; // solar mass
   private const double dpy = 365.24;   // days per year

   private Vector2d v2sm = new Vector2d(sm, sm);
   private Vector2d v2dpy = new Vector2d(dpy, dpy);

   public NBodySystem() {
      bodies = new Body[] {
         // Sun
         new Body() {
            mass = new Vector2d(sm, sm),
         },
         // Jupiter
         new Body() {
            xy = new Vector2d( 4.84143144246472090e+00, -1.16032004402742839e+00),
            z0 = new Vector2d(-1.03622044471123109e-01,  0.00000000000000000e+00),
            vxvy = new Vector2d( 1.66007664274403694e-03, 7.69901118419740425e-03)*v2dpy,
            vz00 = new Vector2d(-6.90460016972063023e-05, 0.00000000000000000e+00)*v2dpy,
            mass = new Vector2d( 9.54791938424326609e-04, 9.54791938424326609e-04)*v2sm
         },
         // Saturn
         new Body() {
            xy = new Vector2d( 8.34336671824457987e+00,  4.12479856412430479e+00),
            z0 = new Vector2d(-4.03523417114321381e-01,  0.00000000000000000e+00),
            vxvy = new Vector2d(-2.76742510726862411e-03, 4.99852801234917238e-03)*v2dpy,
            vz00 = new Vector2d( 2.30417297573763929e-05, 0.00000000000000000e+00)*v2dpy,
            mass = new Vector2d( 2.85885980666130812e-04, 2.85885980666130812e-04)*v2sm
         },
         // Uranus
         new Body() {
            xy = new Vector2d( 1.28943695621391310e+01, -1.51111514016986312e+01),
            z0 = new Vector2d(-2.23307578892655734e-01,  0.00000000000000000e+00),
            vxvy = new Vector2d( 2.96460137564761618e-03, 2.37847173959480950e-03)*v2dpy,
            vz00 = new Vector2d(-2.96589568540237556e-05, 0.00000000000000000e+00)*v2dpy,
            mass = new Vector2d( 4.36624404335156298e-05, 4.36624404335156298e-05)*v2sm
         },
         // Neptune
         new Body() {
            xy = new Vector2d( 1.53796971148509165e+01, -2.59193146099879641e+01),
            z0 = new Vector2d( 1.79258772950371181e-01,  0.00000000000000000e+00),
            vxvy = new Vector2d( 2.68067772490389322e-03, 1.62824170038242295e-03)*v2dpy,
            vz00 = new Vector2d(-9.51592254519715870e-05, 0.00000000000000000e+00)*v2dpy,
            mass = new Vector2d( 5.15138902046611451e-05, 5.15138902046611451e-05)*v2sm
         },
      };
      
      pairs = new Pair[bodies.Length*(bodies.Length-1)/2];      
      int pi = 0;
      for (int i = 0; i < bodies.Length-1; i++)
         for (int j = i+1; j < bodies.Length; j++)
            pairs[pi++] = Tuple.Create(bodies[i], bodies[j]);      

      Vector2d pxpy = Vector2d.Zero;
      Vector2d pz00 = Vector2d.Zero;
      foreach (var b in bodies) {
         pxpy -= b.vxvy*b.mass;
         pz00 -= b.vz00*b.mass;
      }
      var sol = bodies[0];
      sol.vxvy = pxpy/v2sm;
      sol.vz00 = pz00/v2sm;
   }

   public void Advance(double dt)
   {
      var dtdt = new Vector2d(dt, dt);
      int npairs = pairs.Length;
      for (int i=0; i<npairs; i++)
      {
         var p = pairs[i];
         var bi = p.Item1;
         var bj = p.Item2;
         var dxdy = bi.xy - bj.xy;
         var dz00 = bi.z0 - bj.z0;
         var tmp = VectorOperations.HorizontalAdd(dxdy*dxdy, dz00*dz00);
         var d2 = VectorOperations.HorizontalAdd(tmp, tmp);
         var mag = dtdt/(d2*VectorOperations.Sqrt(d2));
         bi.vxvy -= dxdy*bj.mass*mag; bj.vxvy += dxdy*bi.mass*mag;
         bi.vz00 -= dz00*bj.mass*mag; bj.vz00 += dz00*bi.mass*mag;
      }
      int nbodies = bodies.Length;
      for (int i=0; i<nbodies; i++)
      {
         var b = bodies[i];
         b.xy += dtdt*b.vxvy;
         b.z0 += dtdt*b.vz00;
      }
   }

   public double Energy()
   {
      var e = 0.0;
      for (int i = 0; i < bodies.Length; i++)
      {
         var bi = bodies[i];
         var tmp = VectorOperations.HorizontalAdd(bi.vxvy*bi.vxvy, bi.vz00*bi.vz00);
         e += 0.5*bi.mass.X*(tmp.X + tmp.Y);
         for (int j = i+1; j < bodies.Length; j++)
         {
            var bj = bodies[j];
            var dxdy = bi.xy - bj.xy;
            var dz00 = bi.z0 - bj.z0;
            var tm2 = VectorOperations.HorizontalAdd(dxdy*dxdy, dz00*dz00);
            e -= (bi.mass.X*bj.mass.X)/Math.Sqrt(tm2.X + tm2.Y);
         }
      }
      return e;
   }
}

class NBody
{
   public static void Main(String[] args)
   {
      int n = args.Length > 0 ? Int32.Parse(args[0]) : 10000;

      NBodySystem bodies = new NBodySystem();
      // initial energy
      Console.WriteLine("{0:f9}", bodies.Energy());

      for (int i=0; i<n; i++)
         bodies.Advance(0.01);
      // final energy
      Console.WriteLine("{0:f9}", bodies.Energy());
   }
}

