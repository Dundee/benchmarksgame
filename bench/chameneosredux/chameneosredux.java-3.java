/* The Computer Language Benchmarks Game
   http://benchmarksgame.alioth.debian.org/

   contributed by Kirill Ilyukhin
*/
import java.util.concurrent.CountDownLatch;
import java.util.concurrent.Exchanger;
import java.util.concurrent.atomic.AtomicInteger;

public class chameneosredux {

   static MeetingPlace meetingPlace;
   static CountDownLatch latch;
   static AtomicInteger meetingsLeft;

   public static void main(String[] args) throws InterruptedException {
      int N = 6_000_000;
      if (args.length > 0) {
         try {
            N = Integer.parseInt(args[0]);
         } catch (NumberFormatException ignore) {
         }
      }
      for (Color color1 : Color.colors) {
         for (Color color2 : Color.colors) {
            System.out.println(color1 + " + " + color2 + " -> " + Color.complement(color1, color2));
         }
      }
      System.out.println();
      run(N, Color.blue, Color.red, Color.yellow);
      run(N, Color.blue, Color.red, Color.yellow, Color.red, Color.yellow, Color.blue, Color.red, Color.yellow, Color.red, Color.blue);
   }

   private static void run(final int N, final Color... colors) throws InterruptedException {
      meetingPlace = new MeetingPlace();
      latch = new CountDownLatch(2*N);
      meetingsLeft = new AtomicInteger(2*N);
      Creature[] creatures = new Creature[colors.length];
      for (int i=0; i < colors.length; i++) {
         System.out.print(" " + colors[i]);
         creatures[i] = new Creature(colors[i]);
      }
      System.out.println();
      for (Creature creature : creatures) {
         creature.start();
      }
      latch.await();
      for (Creature creature : creatures) {
         creature.interrupt();
      }
      for (Creature creature : creatures) {
         creature.join();
      }
      int m = 0;
      for (Creature creature : creatures) {
         System.out.println("" + creature.meetings + spell(creature.meetingsWithSelf));
         m += creature.meetings;
      }
      System.out.println(spell(m));
      System.out.println();
   }

   private static final String[] DIGITS = {
         " zero",
         " one",
         " two",
         " three",
         " four",
         " five",
         " six",
         " seven",
         " eight",
         " nine"
   };
   static String spell(int number) {
      if (number == 0) {
         return DIGITS[0];
      }
      String s = "";
      while (number > 0) {
         s = DIGITS[number % 10] + s;
         number /= 10;
      }
      return s;
   }

   static class Creature extends Thread {
      private static int nameCounter;
      private Color color;
      private final int name;
      int meetings = 0;
      int meetingsWithSelf = 0;

      Creature(Color color) {
         this.name = ++nameCounter;
         this.color = color;
      }

      private Agent createAgent() {
         return new Agent(this);
      }

      @Override
      public void run() {
         while (true) {
            try {
               Agent agent = meetingPlace.enter(this.createAgent());
               if (agent == null) {
                  return;
               }
               if (agent.name == this.name) {
                  meetingsWithSelf++;
               }
               color = Color.complement(this.color, agent.color);
               meetings++;
            } catch (InterruptedException e) {
               break;
            }
         }
      }

   }

   static class MeetingPlace {
      private final Exchanger<Agent> room;

      MeetingPlace() {
         room = new Exchanger<>();
      }

      public Agent enter(Agent visitor) throws InterruptedException {
         if (meetingsLeft.get() < 0) {
            return null;
         }
         Agent agent = room.exchange(visitor);
         latch.countDown();
         if (meetingsLeft.decrementAndGet() < 0) {
            return null;
         }
         return agent;
      }

   }

   static class Agent {
      final int name;
      final Color color;

      Agent(Creature creature) {
         this.name = creature.name;
         this.color = creature.color;
      }
   }

   enum Color {
      blue,
      red,
      yellow;

      static final Color[] colors = {Color.blue, Color.red, Color.yellow};

      public static Color complement(final Color color1, final Color color2) {
         switch (color1) {
            case blue:
               switch (color2) {
                  case blue:      return blue;
                  case red:      return yellow;
                  case yellow:   return red;
               }
            case red:
               switch (color2) {
                  case blue:      return yellow;
                  case red:      return red;
                  case yellow:   return blue;
               }
            case yellow:
               switch (color2) {
                  case blue:      return red;
                  case red:      return blue;
                  case yellow:   return yellow;
               }
         }
         return null;
      }
   }

}
