<p><g:plusone annotation="none"></g:plusone></p>

<script type="text/javascript">
  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>

<dl>

<dd>
<p>You won't find the Benchmarks Game with Google or any other search engine. So, if you're interested in programming language comparisons and performance benchmarks, <a href="http://benchmarksgame.alioth.debian.org/">bookmark the website now!</a></p>

<p>Since 2004, The Computer Language Benchmarks Game has been hosted by <a href="https://wiki.debian.org/Alioth">Alioth</a>, and the <a href="https://wiki.debian.org/Alioth/Web">web hosting</a> has mostly worked well enough for a low volume web site.</p>

<p>Not anymore!</p>

<p>As a matter of policy, the Alioth admins have now <b>throttled web crawling</b> to reduce average server load from 30% to near nothing. There's been no suggestion that the benchmarks game website was the primary cause of that server load. It's just the result of the default configuration they use to provide dynamic content for 1,000 projects.</p>

<p>The inevitable consequence is that all year Googlebot has struggled to crawl pages on the benchmarks game website. In mid-September Googlebot repeatedly failed to fetch robots.txt --</p>

<pre>
14 Sept errors/attempts 147 / 148
15 Sept errors/attempts 39 / 39 
24 Sept errors/attempts 80 / 81
</pre>

<p>-- and repeatedly failed to access the benchmarks game website --</p>

<pre>
16 Sept errors/attempts 266 / 271
23 Sept errors/attempts 184 / 188
</pre>

<p>-- and --</p>

<pre>
15 Sept Time spent downloading a page (in milliseconds): 7042
25 Sept Time spent downloading a page (in milliseconds): 5460
28 Sept Time spent downloading a page (in milliseconds): 6116
</pre>

<p>The inevitable consequence is that pages Googlebot cannot access are removed from Google's index. That's been happening all year. Since mid-September the benchmarks game has rapidly been removed from Google's index.</p>

<p><b>You won't find the Benchmarks Game website with Google or any other search engine, because the Alioth admins have throttled web crawling.</b></p>

</dd>
</dl>

