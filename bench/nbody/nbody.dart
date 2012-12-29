/* The Computer Language Benchmarks Game
   http://benchmarksgame.alioth.debian.org/

   contributed by Usagi Ito
*/

import 'dart:math' as Math;

main(){

  int n = (){
    var args = new Options().arguments;
    return args.length > 0 ? int.parse(args[0]) : constant.default_advance;
  }();

  var s = new solar_system();

  print(s.energy.toStringAsFixed(constant.fixed_digit));

  for(var c = 0; c < n; ++c)
    s.advance(constant.dt);

  print(s.energy.toStringAsFixed(constant.fixed_digit));
}

class solar_system{

  List<body> _bodies;

  solar_system(){
    body sun = new body.sun();
    _bodies = [
      sun,
      new body.jupiter(),
      new body.saturn(),
      new body.uranus(),
      new body.neptune()
    ];

    double
      px = 0.0,
      py = 0.0,
      pz = 0.0;

    for(var b in _bodies){
      px += b.vx * b.mass;
      py += b.vy * b.mass;
      pz += b.vz * b.mass;
    }

    sun.offset_momentum(px, py, pz);
  }

  double get energy {
    double e = 0.0;
    final nend = _bodies.length;
    for(int na = 0; na < nend; ++na){
      body a = _bodies[na];
      e += 0.5 * a.mass * (
        a.vx * a.vx +
        a.vy * a.vy +
        a.vz * a.vz
      );
      for(int nb = na + 1; nb < nend; ++nb){
        body b = _bodies[nb];
        double dx = a.x - b.x;
        double dy = a.y - b.y;
        double dz = a.z - b.z;
        double distance = Math.sqrt(
          dx * dx +
          dy * dy +
          dz * dz
        );
        e -= (a.mass * b.mass) / distance;
      }
    }
    return e;
  }

  void advance(double dt){
    final nend = _bodies.length;
    for(int na = 0; na < nend; ++na){
      body a = _bodies[na];
      for(int nb = na + 1; nb < nend; ++nb){
        body b = _bodies[nb];
        double dx = a.x - b.x;
        double dy = a.y - b.y;
        double dz = a.z - b.z;
        double distance_squared =
          dx * dx +
          dy * dy +
          dz * dz
        ;
        double distance = Math.sqrt(distance_squared);
        double magnitude = dt / (distance_squared * distance);
        double mul_b_mass_magnitude = b.mass * magnitude;
        a.vx -= dx * mul_b_mass_magnitude;
        a.vy -= dy * mul_b_mass_magnitude;
        a.vz -= dz * mul_b_mass_magnitude;
        double mul_a_mass_magnitude = a.mass * magnitude;
        b.vx += dx * mul_a_mass_magnitude;
        b.vy += dy * mul_a_mass_magnitude;
        b.vz += dz * mul_a_mass_magnitude;
      }
    }
    for(var b in _bodies)
      b.update(dt);
  }
}

class body{

  double _x, _y, _z;
  double _vx, _vy, _vz;
  final double _mass;

  static final num_of_initializer_parameter = 7;

  double get x => _x;
  double get y => _y;
  double get z => _z;
  double get vx => _vx;
  double get vy => _vy;
  double get vz => _vz;
  void set vx(double v){ _vx = v; }
  void set vy(double v){ _vy = v; }
  void set vz(double v){ _vz = v; }
  double get mass => _mass;

  void offset_momentum(double px, double py, double pz){
    _vx = -px / constant.solar_mass;
    _vy = -py / constant.solar_mass;
    _vz = -pz / constant.solar_mass;
  }

  void update(double dt){
    _x += dt * _vx;
    _y += dt * _vy;
    _z += dt * _vz;
  }

  body(
    this. _x, this. _y, this. _z,
    this._vx, this._vy, this._vz,
    this._mass
  );

  factory body.initializer_list(List<double> i){
    assert(i.length == num_of_initializer_parameter);
    return new body(i[0], i[1], i[2], i[3], i[4], i[5], i[6]);
  }

  factory body.sun(){
    return new body.initializer_list(constant.initialize_parameters_sun);
  }

  factory body.jupiter(){
    return new body.initializer_list(constant.initialize_parameters_jupiter);
  }

  factory body.saturn(){
    return new body.initializer_list(constant.initialize_parameters_saturn);
  }

  factory body.uranus(){
    return new body.initializer_list(constant.initialize_parameters_uranus);
  }

  factory body.neptune(){
    return new body.initializer_list(constant.initialize_parameters_neptune);
  }
}

class constant{

  static final int fixed_digit = 9;
  static final int default_advance = 10000;
  static final double solar_mass = 4.0 * Math.PI * Math.PI;
  static final double days_per_year = 365.24;
  static final double dt = 0.01;
  static final List<double>
    initialize_parameters_sun = [
      0.0, 0.0, 0.0,
      0.0, 0.0, 0.0,
      solar_mass
    ],
    initialize_parameters_jupiter = [
      4.84143144246472090e+00,
      -1.16032004402742839e+00,
      -1.03622044471123109e-01,
      1.66007664274403694e-03 * days_per_year,
      7.69901118419740425e-03 * days_per_year,
      -6.90460016972063023e-05 * days_per_year,
      9.54791938424326609e-04 * solar_mass
    ],
    initialize_parameters_saturn = [
      8.34336671824457987e+00,
      4.12479856412430479e+00,
      -4.03523417114321381e-01,
      -2.76742510726862411e-03 * days_per_year,
      4.99852801234917238e-03 * days_per_year,
      2.30417297573763929e-05 * days_per_year,
      2.85885980666130812e-04 * solar_mass
    ],
    initialize_parameters_uranus = [
      1.28943695621391310e+01,
      -1.51111514016986312e+01,
      -2.23307578892655734e-01,
      2.96460137564761618e-03 * days_per_year,
      2.37847173959480950e-03 * days_per_year,
      -2.96589568540237556e-05 * days_per_year,
      4.36624404335156298e-05 * solar_mass
    ],
    initialize_parameters_neptune = [
      1.53796971148509165e+01,
      -2.59193146099879641e+01,
      1.79258772950371181e-01,
      2.68067772490389322e-03 * days_per_year,
      1.62824170038242295e-03 * days_per_year,
      -9.51592254519715870e-05 * days_per_year,
      5.15138902046611451e-05 * solar_mass
    ]
  ;
}
