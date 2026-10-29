@props(['url'])
{{-- The green DRIP band at the top of every email. The crest travels inside the email (App\Listeners\EmbedEmailLogo). --}}
<tr>
<td class="header">
<table class="header-inner" align="center" width="600" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td class="header-cell">
<a href="{{ $url }}">
<table cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td style="padding-right: 14px; vertical-align: middle;">
<img src="cid:drip-logo" class="brand-logo" width="48" height="48" alt="FUNAAB">
</td>
<td style="vertical-align: middle;">
<span class="brand-name">DRIP</span>
<span class="brand-tag">Directorate of Research, Innovations and Partnerships<br>Federal University of Agriculture, Abeokuta</span>
</td>
</tr>
</table>
</a>
</td>
</tr>
</table>
</td>
</tr>
