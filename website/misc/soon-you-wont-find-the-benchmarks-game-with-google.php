
<dl>


<dd>
<p><i>One response to this series of disruptions is that many more benchmarks game pages ask to be archived.</i></p>
<br/><br/>
</dd>

<dd><dl>

<dt>&nbsp;<a href="#throttled" name="throttled">Throttling a web server</a></dt>
<dd>
<p class="timestamp"><? printf('%s GMT', '06 Oct 2013, Sunday, 4:12 pm') ?></p>
<p>You can no longer find the Benchmarks Game with Google or any other search engine. So, if you're interested in programming language comparisons and performance benchmarks, <a href="http://benchmarksgame.alioth.debian.org/">bookmark the website now!</a></p>

<p>Since 2004, The Computer Language Benchmarks Game has been hosted by <a href="https://wiki.debian.org/Alioth">Alioth</a>, and the <a href="https://wiki.debian.org/Alioth/Web">web hosting</a> has mostly worked well enough for a low volume web site.</p>

<p>Not anymore!</p>

<p>As a matter of policy, the Alioth admins have now <b>throttled web crawling</b> to reduce average server load from 30% to near nothing. There's been no suggestion that the benchmarks game website was the primary cause of that server load. It's just the result of the default configuration they use to provide dynamic content for 1,000 projects.</p>

<p>The inevitable consequence is that Googlebot has struggled to crawl pages on the benchmarks game website. In mid-September Googlebot repeatedly failed to fetch robots.txt --</p>

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

<p>You can no longer find the Benchmarks Game website with Google or any other search engine, because the Alioth admins have throttled web crawling.</p>
<br/><br/>
</dd>



<dt>&nbsp;<a href="#xtag" name="xtag">Taking control with X-Robots-Tag</a></dt>
<dd>
<p class="timestamp"><? printf('%s GMT', '06 Nov 2013, Wednesday, 7:52 am') ?></p>
<p><br/>Luckily I found a way for the Alioth admins to take some control of the upstream FusionForge generated pages (<a href="http://alioth.debian.org/projects/benchmarksgame/">for example</a>) without requiring changes to FusionForge. So, for now, the Alioth admins have throttled web crawlers less and <b>the website is once again findable with Google</b>.</p>

<p>(All the upstream FusionForge generated pages were being served without any HTML robots meta tags: a minimum of 90 pages, for each of 1000 projects, duplicated at 4 different sub-domains. In other words, web crawlers were visiting Alioth because Alioth pages repeatedly asked to be indexed and crawled. That problem has now been fixed using the HTTP <a href="https://developers.google.com/webmasters/control-crawl-index/docs/robots_meta_tag">X-Robots-Tag "noindex, nofollow"</a> header.)</p>
<br/><br/>
</dd>


<dt>&nbsp;<a href="#crash" name="crash">Crash</a></dt>
<dd>
<p class="timestamp"><? printf('%s GMT', '26 Nov 2013, Tuesday, 4:51 pm GMT') ?></p>
<p><br/>The machine hosting the storage for alioth.debian.org suffered
<a href="http://lists.debian.org/debian-infrastructure-announce/2013/11/msg00001.html">catastrophic disk failure&#8230;</a></p>
<blockquote>"Most of the data on vasks was stored on a RAID5. One of the disks in the RAID
experienced a small hiccup, leading to it being thrown from the RAID, at which
point mdadm started recovery using a hotspare. During this, another disk in the
RAID threw hard errors and we were stuck with a non-working RAID. 

Investigations showed this did not affect the /srv partition, but it affected
the PostgreSQL partition. <a href="http://lists.debian.org/debian-infrastructure-announce/2013/11/msg00002.html">Due to various misconfigurations, we did not have any
recent backups of Postgres.</a>"</blockquote>
<p>With the inevitable consequence that for 2 weeks Googlebot could not access the website and&#8230;</p>
<br/><br/>
</dd>



</dl></dd>
</dl>

