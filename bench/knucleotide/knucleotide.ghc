--
-- The Computer Language Benchmarks Game
-- http://benchmarksgame.alioth.debian.org/
--
-- Contributed by Branimir Maksimovic
--
import Data.Bits
import Data.Char
import Data.Int
import Data.List
import Data.Array.Base
import Data.Array.Unboxed
import Data.Array.IO
import qualified Data.ByteString.Char8 as S
import Foreign.Ptr
import Foreign.Storable
import Foreign.Marshal.Alloc
import Control.Concurrent
import Text.Printf

main = do
    let skip = do
            l <- S.getLine
            if S.isPrefixOf (S.pack ">THREE") l
                then return ()
                else skip
    skip
    s <- S.getContents
    let content = S.filter ((/=) '\n') s;
    mapM_ (execute content) actions
    
data Actions = I Int | S String
actions = [I 1,I 2,
           S "GGT",S "GGTA",S "GGTATT",S "GGTATTTTAATT",S "GGTATTTTAATTTATAGT"]
execute content (I i) = writeFrequencies content i
execute content (S s) = writeCount content s

writeFrequencies :: S.ByteString -> Int -> IO ()
writeFrequencies input size = do
    ht <- tcalculate input size
    lst <- Main.foldM (\lst (k,v)->do 
        v' <- peek v
        return $ (k,v'):lst) [] ht
    let sorted = sortBy (\(_,x) (_,y) -> y `compare` x) lst
        sum = fromIntegral ((S.length input) + 1 - size)
    mapM_ (\(k,v)-> do
        printf "%s %.3f\n" 
            (toString k) ((100 * (fromIntegral v)/sum)::Double)) sorted
    putChar '\n'

writeCount :: S.ByteString -> String -> IO ()
writeCount input string = do
    let size = length string
        k = T (toNum (S.pack string) 0 size) size
    ht <- tcalculate input size
    res <- Main.lookup ht k
    case res of 
        Nothing -> putStrLn $ string ++ " not found..."
        Just v -> do
            r <- peek v
            printf "%d\t%s\n" r string

tcalculate :: S.ByteString -> Int -> IO HM
tcalculate input size = do
    let 
        l = [0..63]
        actions = map (\i -> (calculate input i size (length l))) l
    vars <- mapM (\action -> do
                    var <- newEmptyMVar
                    forkIO $ do
                        answer <- action
                        putMVar var answer
                    return var) actions
    result <- newTable :: IO HM
    results <- mapM takeMVar vars
    mapM_ (\ht -> Main.foldM (\lst (k,v) -> do 
                            res <- Main.lookup lst k
                            case res of
                                Nothing -> do
                                    r1 <- peek v
                                    r2 <- malloc
                                    poke r2 r1
                                    Main.insert lst k r2
                                Just v1 -> do
                                    r1 <- peek v1
                                    r2 <- peek v
                                    poke v1 (r1+r2)
                            return lst) result ht) results
    return result

calculate :: S.ByteString -> Int -> Int -> Int -> IO HM    
calculate input beg size incr = do
    !ht <- newTable :: IO HM
    let
        calculate' i 
         | i >= ((S.length input)+1 - size) = return ht
         | otherwise = do
            let k =  T (toNum input i size) size
            res <- Main.lookup ht k
            case res of
                Nothing -> do
                    !r <- malloc
                    poke r 1
                    Main.insert ht k r
                Just v -> do 
                    !r <- peek v
                    poke v (r+1)
            calculate' (i+incr)
    calculate' beg

toNum :: S.ByteString -> Int -> Int -> Int64
toNum s beg size = toNum' 0 size
    where
        toNum' v 0 = v
        toNum' v i = toNum' ((v `shiftL` 2) .|. 
                    (toNumA `unsafeAt` (ord (S.index s (beg+i-1))))) (i-1)

toString :: T -> String
toString (T v s) = toString' v s
    where
        toString' v 0 = []
        toString' v i = case v.&.3 of
                        0 -> 'A'
                        1 -> 'C'
                        2 -> 'T'
                        3 -> 'G'
                      : toString' (v `shiftR` 2) (i-1)

toNumA :: UArray Int Int64
toNumA = array (0,255) [(ord 'a',0),(ord 'c',1),(ord 't',2),(ord 'g',3),
            (ord 'A',0),(ord 'C',1),(ord 'T',2),(ord 'G',3)]

data T = T !Int64 !Int
instance Eq T where
    (T a _) == (T b _) = a == b
class Hash h where
    hash :: h -> Int64
instance Hash T where
    hash (T a _) = a

type HM = HashMap T (Ptr Int)
data HashMap k v = HashMap !(IOArray Int64 [(k,v)])
tsz = 4096
newTable :: IO (HashMap k v)
newTable = do
    !array <- newArray (0,(tsz-1)) []
    return $ HashMap array

lookup :: (Eq k, Hash k)=>HashMap k v -> k -> IO (Maybe v)
lookup (HashMap a) k = do
    let h = hash k
    !lst <- readArray a (h .&. (tsz-1))
    let
        loop [] = return Nothing
        loop ((!k',!v):xs) 
            | k /= k' = loop xs
            | otherwise = return $ Just v
    loop lst

insert :: (Eq k, Hash k)=>HashMap k v -> k -> v -> IO ()
insert (HashMap a) k v = do
    let h = hash k
    !lst <- readArray a (h .&. (tsz-1))
    writeArray a (h .&. (tsz-1)) ((k,v):lst)

foldM :: ( a -> (b,c) -> IO a) -> a -> HashMap b c -> IO a
foldM f s (HashMap a) = do
    let 
        loop 0 s' = return s'
        loop i s' = do
            !lst <- readArray a (i-1)
            let 
                loop' [] s' = return s'
                loop' (x:xs) s' = do
                    !s'' <- f s' x
                    loop' xs s''
            !s'' <- loop' lst s'
            loop (i-1) s''
    loop tsz s
