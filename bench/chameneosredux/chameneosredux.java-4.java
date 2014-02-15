/**
 * The Computer Language Benchmarks Game
 * http://benchmarksgame.alioth.debian.org/
 * contributed by Michael Barker
 * modified by Daryl Griffith
 */

import java.util.concurrent.Exchanger;
import java.util.concurrent.Phaser;
import java.util.concurrent.atomic.AtomicInteger;

public class chameneosredux {

    enum Colour {

        BLUE {
            @Override
            Colour complement(Colour colour) {
                return colour.blue;
            }
            
            @Override
            public String toString() {
                return "blue";
            }
        },
        RED {
            @Override
            Colour complement(Colour colour) {
                return colour.red;
            }
            
            @Override
            public String toString() {
                return "red";
            }
        },
        YELLOW {
            @Override
            Colour complement(Colour colour) {
                return colour.yellow;
            }
            
            @Override
            public String toString() {
                return "yellow";
            }
        };

        private Colour blue;
        private Colour red;
        private Colour yellow;

        private void setColours(Colour blue, Colour red, Colour yellow) {
            this.blue = blue;
            this.red = red;
            this.yellow = yellow;
        }

        abstract Colour complement(Colour colour);
    }

    static {

        Colour.BLUE.setColours(Colour.BLUE, Colour.YELLOW, Colour.RED);
        Colour.RED.setColours(Colour.YELLOW, Colour.RED, Colour.BLUE);
        Colour.YELLOW.setColours(Colour.RED, Colour.BLUE, Colour.YELLOW);
    }
    
    static final class CreatureExchange {

        Colour colour;
        int id;
    }

    static final class MeetingPlace {

        private final Exchanger<CreatureExchange> exchanger = new Exchanger<>();
        private final AtomicInteger meetingsLeft = new AtomicInteger();

        public MeetingPlace(final int meetings) {
            meetingsLeft.set(meetings + meetings);
        }

        public CreatureExchange meet(final CreatureExchange info) {
            final int meetings = meetingsLeft.decrementAndGet();

            if (meetings >= 0) {
                try {
                    return exchanger.exchange(info);
                } catch (InterruptedException ex) {
                }
            }
            return null;
        }
    }

    static final class Creature extends Thread {

        private final CreatureExchange exchange = new CreatureExchange();
        private final MeetingPlace place;
        private final Phaser phaser;
        private int count = 0;
        private int sameCount = 0;

        public Creature(final MeetingPlace place
                , final Colour colour
                , final Phaser phaser) {
            this.place = place;
            this.phaser = phaser;
            exchange.id = System.identityHashCode(this);
            exchange.colour = colour;
        }

        @Override
        public void run() {
            CreatureExchange otherCreature;

            for (;;) {
                otherCreature = place.meet(exchange);
                if (otherCreature == null) {
                    phaser.arrive();
                    break;
                }
                exchange.colour
                        = exchange.colour.complement(otherCreature.colour);
                count++;
                if (exchange.id == otherCreature.id) {
                    sameCount++;
                }
            }
        }

        public int printAndGetCount() {
            System.out.append(Integer.toString(count));
            return count;
        }
        
        public int getSameCount() {
            return sameCount;
        }
    }

    final static class CreaturePhaser extends Phaser {

        static final String[] NUMBERS = {
            "zero", "one", "two", "three", "four", "five",
            "six", "seven", "eight", "nine"
        };
        static final Object lock = new Object();
        static boolean firstHasNotFinished = true;
        final boolean isFirst;
        final Colour[] colours;
        final Creature[] creatures;

        public CreaturePhaser(final boolean isFirst
                , final Colour[] colours
                , final Creature[] creatures
                , final int phases) {
            super(phases);
            this.isFirst = isFirst;
            this.colours = colours;
            this.creatures = creatures;
        }

        @Override
        protected boolean onAdvance(final int phase
                , final int registeredParties) {
            synchronized (lock) {
                if (!isFirst) {
                    while (firstHasNotFinished) {
                        try {
                            lock.wait();
                        } catch (InterruptedException ex) {
                        }
                    }
                }
                for (final Colour colour : colours) {
                    System.out.append(' ').append(colour.toString());
                }
                System.out.append('\n');

                int total = 0;
                for (final Creature creature : creatures) {
                    total += creature.printAndGetCount();
                    printNumber(creature.getSameCount());
                }
                printNumber(total);
                System.out.append('\n');
                if (isFirst) {
                    firstHasNotFinished = false;
                    lock.notify();
                }
            }
            return true;
        }
    
        private void printNumber(final int n) {
            final String nStr = Integer.toString(n);

            for (int i = 0; i < nStr.length(); i++) {
                System.out.append(' ')
                        .append(NUMBERS[Character.getNumericValue(nStr.charAt(i))]);
            }
            System.out.append('\n');
        }
    }

    private static void startMeeting(final boolean isFirst
            , final int n
            , final Colour... colours) {
        final int len = colours.length;
        final MeetingPlace place = new MeetingPlace(n);
        final Creature[] creatures = new Creature[len];
        final Phaser latch
                = new CreaturePhaser(isFirst, colours, creatures, len);

        for (int i = 0; i < creatures.length; i++) {
            creatures[i] = new Creature(place, colours[i], latch);
            creatures[i].start();
        }
    }

    public static void main(final String[] args) {
        int n = Integer.parseInt(args[0]);

        startMeeting(true, n, Colour.BLUE, Colour.RED, Colour.YELLOW);
        startMeeting(false, n, Colour.BLUE, Colour.RED, Colour.YELLOW,
                Colour.RED, Colour.YELLOW, Colour.BLUE, Colour.RED,
                Colour.YELLOW, Colour.RED, Colour.BLUE);
        for (final Colour c1 : Colour.values()) {
            for (final Colour c2 : Colour.values()) {
                System.out.append(c1.toString())
                        .append(" + ")
                        .append(c2.toString())
                        .append(" -> ")
                        .append(c1.complement(c2).toString())
                        .append('\n');
            }
        }
        System.out.append('\n');
    }
}
