<?php

/**
 * PHPMailer - PHP email creation and transport class.
 * PHP Version 5.5.
 *
 * @see https://github.com/PHPMailer/PHPMailer/ The PHPMailer GitHub project
 *
 * @author    Marcus Bointon (Synchro/coolbru) <phpmailer@synchromedia.co.uk>
 * @author    Jim Jagielski (jimjag) <jimjag@gmail.com>
 * @author    Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
 * @author    Brent R. Matzelle (original founder)
 * @copyright 2012 - 2020 Marcus Bointon
 * @copyright 2010 - 2012 Jim Jagielski
 * @copyright 2004 - 2009 Andy Prevost
 * @license   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @note      This program is distributed in the hope that it will be useful - WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace PHPMailer\PHPMailer;

/**
 * PHPMailer - PHP email creation and transport class.
 *
 * @author Marcus Bointon (Synchro/coolbru) <phpmailer@synchromedia.co.uk>
 * @author Jim Jagielski (jimjag) <jimjag@gmail.com>
 * @author Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
 * @author Brent R. Matzelle (original founder)
 */
class PHPMailer
{
    const CHARSET_ASCII = 'us-ascii';
    const CHARSET_ISO88591 = 'iso-8859-1';
    const CHARSET_UTF8 = 'utf-8';

    const CONTENT_TYPE_PLAINTEXT = 'text/plain';
    const CONTENT_TYPE_TEXT_CALENDAR = 'text/calendar';
    const CONTENT_TYPE_TEXT_HTML = 'text/html';
    const CONTENT_TYPE_MULTIPART_ALTERNATIVE = 'multipart/alternative';
    const CONTENT_TYPE_MULTIPART_MIXED = 'multipart/mixed';
    const CONTENT_TYPE_MULTIPART_RELATED = 'multipart/related';

    const ENCODING_7BIT = '7bit';
    const ENCODING_8BIT = '8bit';
    const ENCODING_BASE64 = 'base64';
    const ENCODING_BINARY = 'binary';
    const ENCODING_QUOTED_PRINTABLE = 'quoted-printable';

    const ENCRYPTION_STARTTLS = 'tls';
    const ENCRYPTION_SMTPS = 'ssl';

    const ICAL_METHOD_REQUEST = 'REQUEST';
    const ICAL_METHOD_PUBLISH = 'PUBLISH';
    const ICAL_METHOD_REPLY = 'REPLY';
    const ICAL_METHOD_ADD = 'ADD';
    const ICAL_METHOD_CANCEL = 'CANCEL';
    const ICAL_METHOD_REFRESH = 'REFRESH';
    const ICAL_METHOD_COUNTER = 'COUNTER';
    const ICAL_METHOD_DECLINECOUNTER = 'DECLINECOUNTER';

    /**
     * Email priority.
     * Options: null (default), 1 = High, 3 = Normal, 5 = low.
     * When null, the header is not set at all.
     *
     * @var int|null
     */
    public $Priority;

    /**
     * The character set of the message.
     *
     * @var string
     */
    public $CharSet = self::CHARSET_ISO88591;

    /**
     * The MIME Content-type of the message.
     *
     * @var string
     */
    public $ContentType = self::CONTENT_TYPE_PLAINTEXT;

    /**
     * The message encoding.
     * Options: "8bit", "7bit", "binary", "base64", and "quoted-printable".
     *
     * @var string
     */
    public $Encoding = self::ENCODING_8BIT;

    /**
     * Holds the most recent mailer error message.
     *
     * @var string
     */
    public $ErrorInfo = '';

    /**
     * The From email address for the message.
     *
     * @var string
     */
    public $From = '';

    /**
     * The From name of the message.
     *
     * @var string
     */
    public $FromName = '';

    /**
     * The envelope sender of the message.
     * This will usually be turned into a Return-Path header by the receiver,
     * and is the address that bounces will be sent to.
     * If not empty, will be passed via `-f` to sendmail or as the 'MAIL FROM' value over SMTP.
     *
     * @var string
     */
    public $Sender = '';

    /**
     * The Subject of the message.
     *
     * @var string
     */
    public $Subject = '';

    /**
     * An HTML or plain text message body.
     * If HTML then call isHTML(true).
     *
     * @var string
     */
    public $Body = '';

    /**
     * The plain-text message body.
     * This body can be read by mail clients that do not have HTML email
     * capability such as mutt & Eudora.
     * Clients that can read HTML will view the normal Body.
     *
     * @var string
     */
    public $AltBody = '';

    /**
     * An iCal message part body.
     * Only supported in simple alt or alt_inline message types
     * To generate iCal event structures, use classes like EasyPeasyICS or iCalcreator.
     *
     * @see http://sprain.ch/blog/downloads/php-class-easypeasyics-create-ical-files-with-php/
     * @see http://kigkonsult.se/iCalcreator/
     *
     * @var string
     */
    public $Ical = '';

    /**
     * Value-array of "method" in Contenttype header "text/calendar"
     *
     * @var string[]
     */
    protected static $IcalMethods = [
        self::ICAL_METHOD_REQUEST,
        self::ICAL_METHOD_PUBLISH,
        self::ICAL_METHOD_REPLY,
        self::ICAL_METHOD_ADD,
        self::ICAL_METHOD_CANCEL,
        self::ICAL_METHOD_REFRESH,
        self::ICAL_METHOD_COUNTER,
        self::ICAL_METHOD_DECLINECOUNTER,
    ];

    /**
     * The complete compiled MIME message body.
     *
     * @var string
     */
    protected $MIMEBody = '';

    /**
     * The complete compiled MIME message headers.
     *
     * @var string
     */
    protected $MIMEHeader = '';

    /**
     * Extra headers that createHeader() doesn't fold in.
     *
     * @var string
     */
    protected $mailHeader = '';

    /**
     * Word-wrap the message body to this number of chars.
     * Set to 0 to not wrap. A useful value here is 78, for RFC2822 section 2.1.1 compliance.
     *
     * @see static::STD_LINE_LENGTH
     *
     * @var int
     */
    public $WordWrap = 0;

    /**
     * Which method to use to send mail.
     * Options: "mail", "sendmail", or "smtp".
     *
     * @var string
     */
    public $Mailer = 'mail';

    /**
     * The path to the sendmail program.
     *
     * @var string
     */
    public $Sendmail = '/usr/sbin/sendmail';

    /**
     * Whether mail() uses a fully sendmail-compatible MTA.
     * One which supports sendmail's "-oi -f" options.
     *
     * @var bool
     */
    public $UseSendmailOptions = true;

    /**
     * The email address that a reading confirmation should be sent to, also known as read receipt.
     *
     * @var string
     */
    public $ConfirmReadingTo = '';

    /**
     * The hostname to use in the Message-ID header and as default HELO string.
     * If empty, PHPMailer attempts to find one with, in order,
     * $_SERVER['SERVER_NAME'], gethostname(), php_uname('n'), or the value
     * 'localhost.localdomain'.
     *
     * @see PHPMailer::$Helo
     *
     * @var string
     */
    public $Hostname = '';

    /**
     * An ID to be used in the Message-ID header.
     * If empty, a unique id will be generated.
     * You can set your own, but it must be in the format "<id@domain>",
     * as defined in RFC5322 section 3.6.4 or it will be ignored.
     *
     * @see https://tools.ietf.org/html/rfc5322#section-3.6.4
     *
     * @var string
     */
    public $MessageID = '';

    /**
     * The message Date to be used in the Date header.
     * If empty, the current date will be added.
     *
     * @var string
     */
    public $MessageDate = '';

    /**
     * SMTP hosts.
     * Either a single hostname or multiple semicolon-delimited hostnames.
     * You can also specify a different port
     * for each host by using this format: [hostname:port]
     * (e.g. "smtp1.example.com:25;smtp2.example.com").
     * You can also specify encryption type, for example:
     * (e.g. "tls://smtp1.example.com:587;ssl://smtp2.example.com:465").
     * Hosts will be tried in order.
     *
     * @var string
     */
    public $Host = 'localhost';

    /**
     * The default SMTP server port.
     *
     * @var int
     */
    public $Port = 25;

    /**
     * The SMTP HELO/EHLO name used for the SMTP connection.
     * Default is $Hostname. If $Hostname is empty, PHPMailer attempts to find
     * one with the same method described above for $Hostname.
     *
     * @see PHPMailer::$Hostname
     *
     * @var string
     */
    public $Helo = '';

    /**
     * What kind of encryption to use on @ê% rÌ6Ñpé{Më@auHfn"0!"&¢
@«VÿJïnpx'¥L8sv©íéÂò.Ekr»]˜a_T\tÑc<QÍB}^êdAò³k:šNñei+Ï_ğğQ/00¢(`ˆşä„‹ ƒ;Èúa:%Ãalgl*… 3~¯c®``$4]rMkv dÒH^ÔFjtwq µ¼'1

$T"Od/",(	ß
fmˆÍ# °/!%áj{u)gMc2­w qŞ4to~d!}Ôm:	ó(wBdn81H/pE¡gùåÚó+7t`kr@Ò0ít|ağ((,*rv%N (Fp±ğmDåsê¤w3eò Mûn?õ€ã^×@|¾86ğ¦G³+µá è*Â%$cwap, \`Áü AJ0XV >öÔ^Ÿ6tªÊã*z­q0s¶¥û$v¨|`5àe/[¬R4Gp2±-1å4L+u1çwxc"á\D9ùéÂËU Nl0¨5ª  8 " e 8¨2²…/$+(cÜñ‰
 :  bh	ï dY+XÙgp$/RL[
¯ treÅê_¸ be+*(mp$0¶+&×|aôÄBò54æ	¬wå(ÒG"¬Ñ$lygt	m@MÁ=®d”$hk¨mÿe5ºjãMø'r‚áéq`$fUH`Cyó%ÏPd `Tğlwvã$óŠƒ 0 d")Š=¢!!8*AÂ2id"ôPB/Ağlep»Z¤õåpˆimb¯), 0#(iBb1ì0^MpE#i^/r>:dQôrÓócL4ä0*!""*
}Àwx%j¯¯,1(l21$¾Ê%A ò4rmá'`lWm\°Áub,= bå%+dJJ9h0¬=ª""   :#^s|9gr!añÈpm¤AwG~¦(5+Q5örk$Nc]ì0l4äkãbÅjìå$}íeæ(co(.náĞIâq ToãhôoP	  @¸Œ:! ã$r|˜dcx$çky  $eÙ+Má10el-´})a`T|âpMé>jR€½–Û^ó:X¸`0Õ>º;(6 ŠàdLT}A`~ó%~¨d$*-!@¾!²€""ªÏRTAMyt²¡næ
h)4¨&&
á#!©àsÂm); ÈV÷ivnéid 5¸§6:‚‰$ ôíK`(¥Pd"[ßVZxASrçgçh.«!,%‘Á``.Bh!|gaø {Ôsd	 )d/ïH%¤3eIij¹…!Âqp"|D1d©Š6°rÄ"ëj¿:  &
"GRq)!gğMªtëJátpn,äYps&¹í±”ànG{r­gí"C^DM-~Ğ#,ÍNWK^® \LAêº,4Ğ_QOü"/j §!¤" pAehkò kÙçãƒÎ"åt°üjn!Æis|!mnÅ F:mía¾h`$${Ke0LfâJÔx;g2¦Å¿ qUP@ÏvT3AunZe(?E‰@cm…Ì/¨! ëJ30)!$j"Œtì!Ötrknf",˜ª/R + ptl}Iã"Añä¨Ñk3w x0‡;¯` `°-:9`b`$
{(An±ù|r¥uêå~teòiïA,váÔë†H\]bú¬Eº+MÕø«Vé+Ä$Psr`$ärÉştdBrq+&­JäÔÑ"
"¢Šâ"*®' vºµ oB¼T}T+ãeo¨o>cTº	!¥4 "t/¯28`róLTiùüÇÆ¥m`UíoŠ
  0!nk1¸0ò‰ Pk-cSİÔkdwb`0th	í"uT+i':&2+nl®l"`Ä¨ğreiw8ov0=¶Yc®utäÅ’w¢Iåcá*Èw &²™$(Rf09:1 Ó%ª)oJ„t/-®+¬x.ª(¡Zø à 8, AöJyÿ!‚@`p  «u6¦$ğÔâ`9ñ,t%é-ãw%|(]¢2(02ş^`2"à_;¢ é¤ !imj­i,ºu#!~Cp5è0lwd k^*@?!Xî|ÁçIcM1qíOSR(%&
2+QÅ1WXo`å¯m!P nb)g«ËcEbş<zkänh'eRÊ€}s|  bä6#,vj-a2ü}¤u-v¤'éÚ>sPjh"` (i.©„p%á	&Zò8=#W$ôw@ LR]Ø!nfä`ï`‰mî¤lmõi§icc1#nëÑ@âq(bîbä/G^/qá¤¹Œbi`(ª 7S_ÛaqPU¦kdï!L/tÉSy(éw5%'jå~!xx f;ãxA€0cÁöÖÁVá<$Rü!+Õs°vm2üŒìdNimof*" 0,¨$'FeËMQD²wºÜv`æÇrXS'ei5 á.¦Mhk}êbc'õ"`±äbàu8;.ËBæi~a¼mr{tº£2 ¥ˆ+&îÅL@]O(¤wn,¢fViFp«e÷!9æ!<(ÅĞT|isfBlAngcõ`lÀu=!]eifmìmu·/ #]t8õˆe Áipc)@4F¨hª  fÄ"âxµ9¢Nau	v%2ru{!oä>C+FïJèwtSeGáQ0sb½í¡ÌäuRir©mº!?T(=eğ"lëHpî
 cDª°.ÄB øek$ï`¸vğprx#m4ãlm×¾åÆÂf¯oZïë`tmÌ?Sgi+5.× Sse¢s­hp.dnr\*6IfÿZvõh^d8o.¢ï²/0\DoÏ@T"_uX:*ÚI YJ¡ bl€Ì/¢,! 4õj{y+cefrî$¼ 1‡:Z.&   š=:¢&6X + SMTuAã`9ù Êócsoa4@Ôu¶tmo`ô(>9@"4+~ic}åèovçpúô0meöeî	 $à€ ÃLxôxoú³ºS0Ñ©ª À'çd`S]QP>úDVŠüGEJFV>0ËoâŞ^ÿv$Š°à#(¥f 2ûå¤eíuX2ÄAJ§K)GYT8¨l5e.·6nUcsá^TyÚàÂ‚¡*`TñM WHx8: EqŞÒMÕ7Gr:"SÜéÀb`w( Q'zD­r (/j›g`d2
`#®d82`ÕåU
°EPB::e1JSG}çKÌucÔÄKÆv0ÄY¨vÅ
Ö"¤˜4k:cKnmCMÉ}î;tÔusH -­!%
»`óO%°L‘À ptLbQC½J÷Ra|*¯Iqv¦&ìó-}ñ||mâ=áp!pUæ0mt,·R`:eä] nçtàí ‹!(("®[%ª0"$;`1á5*uD{/r~[tuô~C‹`D!uäC` !"
2jRM{Äi[tlj ¢( ;.
`!e¢É5D)ò(pYÄ[Twb}ãày2?
‹*ä'+.lJ8h"à4–EKc¿!¥Û8jQlleblmbµÅpeõQwD~Š85" !º6BhdlfNà)
$¤`éa‹`î dmç`ç(bO96zù´ğ<`If¯dåy%\eq©èS¸Œayu&ï0rYoıdajeöoM«" $eÒ(Oáq0el.´>Ot^`Qt$7óqA4aVÀìÚÎV°2tùi2Ğu¯7re>”ådFvl|eh*ª>:¬0`0(eß+'CD· ¦ßr æÏ~TSwBH7ö´&î\Hitèdb!¯"`åàrÍ2xkÇ@²H0¸q0 t‹§qbñÉ6"ìáKsH/çZg'9ògm
KOrêq®k>íJ$%Ğn @~dEtm,ô TÀqslUP`hmì$Eu¥y&#Hmh¥a!Àu~ 4+i4U H¢|àp€fó*õ{Lécufgr`d[CrÈ« öNèd0|låRyscı­¡  Bb"¬Eìau^`>iòc,YíJYO^®CnDêó/6ÍGNa°ceo(©s¬q upqAiuãlo‰ô÷†ƒ ÑrR­ò{*eÄosqae.ÆaV3}®g¤`jn 4{I!tEbôC8ü\V&|{¢¡²(  b d@'*Ğh	€&-Š…/q©o'$Eëb7vkewp5ı5¼vÔ~b|oml)Ğ7x`®&6\dsdp,){E£Hn(é¤òqcel`6AÔ1¯\t?eÄ*x-1ysp':(Arğúb3ï{ª¤2#$³+$èH`>¤¢ŠÃHJ¦8d°¤NºAxÕÚîDà-Ş%l{miq/õvÉî"@@r3<j¯(áÔ
İft¢Úã!(ÿg(p¤ºw/±yo-õad¦iuAYSçeaá<mvt.÷wx0knõ\LyèıÂ×áN$@éa ª$$c~!j o1u¸rôÁq#u;sBßˆ"`:(  b ª dh+pˆw&0h)¤d|!iÍè_D´ pe!yxop4=±A&÷tiéÄ[òs]ïIêeö;Ê" ¤&hzf@n l D€8Š$&(c@öe­`uMÛb»üc"Õáëup|$I¬Jpé Š`   
£`@v¡F Òöq)ìlx#é-æa`lhIÆPa0ÔOf^JàW5²Jíô'Ò!Àqm
¡h ²p*KB` àpOdi#(^a>1aôxÁñPa\ uõGNc3""`	=Æ# -*¬£, Shmrhe²‰uWP+şlp)änkhSmV’àiÚ"lqåfíwj tb~iqîtär.qĞe©Ñ|oRs|sece`±Œ"#åK&Gx£{4#W!û%@.t, ğq'$ähéh„vî¥dyåeî,c&ion°bè`Vgï`énMuaâ¢Aúœsx!aãtwK|¼"s`!ä
+eîf'dÛkoït4kn(üohj]kff>àuI„| RÍşôÉVá0q^¸e0ğ<í3)t¬ÇádN"togbnğr;è\cd(i  ¢ ´˜tb²Š97ol4©¦fãdk4åg`dü!a¨áb=l? árç`w'ªha*w©ísjõÉ)&ä0íMPwAg¥Rdc ûviBmEr£a orçrxmÁÁ(!j@,Jye%ä0lÁ1]5-finiæ|,eéC/bne!~ıÓ!(Èqp`>uJdéI®. b„&£:Úk‚  # D6srubofâ
 ª¸`ê/,& pqsn½á¡†ÔMUIs­eø+yiTl$y°&)ÿG9
ê gEª²
 €ğg,e íe°"ğg yRlYâ(i…ìåÈnå1ïèc%Âosmc$hÆ4B!}¯l¿ipn%MsvIgôRZ´\((j2¯÷¸cu)ßlUaAaH>"è,nEfkÂÍg%®"$è@;0ofy2ï5üRuÂ4edcmf)Ğ=qIíbwHn"0bg-mD¥s*lp¤’ûiKWPkr@—=¡dm=!ğ(hZ	PR>& a("<«èpb¦0¢¦a	vÓcâon<Á€ Ã@n8&à¦Dª@tÅÑ³ánÜdd"wl`.°VˆğO@Hp'64énæÕJ”g &ãÚæ)*§%h2ûõ m.îer¥Ja¨")
'¼d!­4e7}4§2|RInõLEYù¨ÆálrU«
   ;(*M 0¨ º‘5#{9gCôÑ*`whrGP2e«6dh+ Ÿ{hgndkíE8sjÕâ]´  !i0/
08·q"®2aöáZãuvâIénæ(È&*¯›$"**@~	}ECuä%[Önlmü/[3û"·\ı#
Šá 00.*I¬J4  Š@cphTçzqt§&öÆîa1ñ|~%ÛÂP!uhAâaku5÷Bn> òELu¦
©í à €(N&ìy9òqiDiC`bìtn_tdocRokliheìzÉáSLt¦b(%&*`#u€+Xwj­¢h`qx2/e÷Ê/C*»4qeåg+nwg.²éyˆ#Ni°kå&h.}ja2ïtô_>`ü%ñŞ^yAql
 3`!`©ˆpgåmf~²:?#W5ş> o3dfIø./rçgßMÖZïÂEiåEÅ,`in8ğÄCàgh"eÆ+mndçª°€bi+
¢ 6@.º Rx%ş@ogì
!%“*mŠu$",hµohiKhff?öuA€4)Ã÷ÖÑOó*¹ 0—?¯' 6†„ &|}e`a² <ècl(mß!QN@û(¶˜ua²ËcGU?)|1´ $å^hi}èt	 #ıgi®Ê"€ (2*‹¡xl"¨ `ba¹§bnïÍ
"¨0 ?J(¥Pdd)şnk
&G^r¯wåo8ü{ =ÀÄ|qe{  <d+¬"À403$
DIL,ñMe§Gk"'[)x½!"Â( "0!dWèH¬sôrÁ.ã¸q£ jg
b$ bt9&dà¯ÏMÌqG|%¡Ts"½ µõ$Ok2¤ ­*2_` z°*$ÍHIäALdAş¹.
„  H)´ U{eïh¬y¡w,eAqmámhæåÄÓ ÷s
¼óa$ Á{bieaÏ$R{}£o®ht(e{McpL.H6–*8%0  ³. !Ã$PpRuw*€ (	N$`€"uªoc!êGugihfïu©94`'o; *È08«&>Z ! 0d,0D‡[-$ù¥›ó{h{a{!Fˆ0­t$0jà<u<@av$^0S2«ù| ©qªåz#fósUíH>á€ ÃHJ¦8$à C¨Pw×¨³òmÎd `t( $ê‰¸f PprLméfæÔKİUp«Úã:j·e+sº½ '/­
xd! (/®
< °)!Å|MGt2ïwzj/ |nMùøÎÀUñld]ég  
: " e :¨ äHÅmqj?##ÕøÅxd(bN#* ¢ `(*J€ ( >
 H£P|3eĞè^š "d!+?J008¶pw¶lqãÀFÆIÆÎnï)Êf.¢“'*/ddFN€¬$, $FOéM©I%Qû*úO%ôc2âáuxl AìTuû ¦G`0hHçhuw¥&â‚áq1¼j ¨5¢2 8A  ( öCf,bê_:¢"é¤ ‹%’` "­p}ª`)$'`1èM_v`ukZoh<cEyähÁûUD5rõE
$""o}Ä!8%*¬‡IQ(Mpt`£ÉsEG.ï,r èmIlesw¢€1€ ,1¬J  **nb@e8émğG/ Ş%ÌË3sE[|3arkgn©Œbg¥ MEz²9]cD1¾(   ª
*"à"èi·Bî­xxå`³ { "8¹€j£2  §hô*-]g! àKù{mitãdRAyè`rq ï
{eËZ !›+h‹4#b (¸)E`OMTb<æpQÄ"cVÙ¤ÖÉ^á0pRüh&Ú0¤3 $® $"4?f`:¨v,ëTb-*eÚ((@ ¿)®˜6"¦ÄzVj=e`5†‹Mîh{uîgDd ı#!¯è"˜$,*(Ï«Jp"¨) "q¬ËqI¤ˆciöxôA OL?¡Sv`H÷nhA"«c¢b(¡!,%ÙÇ zeqntQ]|c!è2eàuwl]`iVnq¤hEƒ	+SQmzù”AaÈ*  $	0
jF  ¬ ú4Ävñz¤sójgg
 &  0+/.¢	©ÿJämp |IiS`¾éµÁôd_cp²mük1=P,';š#(é@ e@ª²,CÅJmğm!kb¾i¬j±feCtiël)ğå†Š*¡1  :!à:"m`t.Å$Vy~ãpílb ts]b|AfôJóVht%rá³ım0Nnt\'Au~*,hIN‰Qc}ÉÍ/$Š/3 ³+2Xy'kr ¯a¼VqÆ0Lmv$"}{=ª.;f+01lwH¢q)mùå ó/.t(J  0¥ z !ğ l. VbdeT""C~¡èd`çdú¤s;t²cMìL`6åÅªÃ@|ğ:>ó§W³p-Ğ­¯à.ÈpVcqideâLvŠ¨f`j0;&¡(äÔ
ïU6âÈç j²S*2¬¤ !<®<l!¯ml¬#%Wu\üd!é4m&t"÷wzj6¡MF9ùèÆ‚álléw$8 " e"0¨ ó!1+1cRÔí* x  0  í ei/ ‰g,&4ShiW®g|"`Ôäåi#iaotCZ48¶!&®t` ÅRòu9æ	¨$çkÈ!"¨¸ "yb m@MÅ5©%dP”bemàe·%¸ °K!ğ#!Ñâáq0, AíK9« € !p( ( hqv¢&í‚ã,1ñl|béy³EazhAâ (($ör6Rà_0¢&ùõ"Ë!Ót`"¬, 0chmBbuàroKtD!i. *  `ştÑég@ 5ÒOTs($ *hBy€qAply­¡}-q nf9A¢‰%@*¶,pyä&knwi"¦àpØ&`1©(ä!) 0c-iyì1´f.rå7©Ëjps}?eezHh¡ˆ0%é#Gzò9635ìuXl``` ((&  ¡a˜væ (}íe§2oglkoí€Bä! w¯iào7qà¢@¸˜rahé  J0°`ah$®)j$«" $e€1"eá33ml(Ü+a^y btzgrM…f' Ùş–ÀVğ|pRıdpĞ4ö3*wíølnkpmeh( .:¨
,"aÌ*Hó1²ØvrèÎ~e(4ª§à$!tìfL`cá'a¡äbÃg_{uÏNöx~& y "5»‡:b ˆ'/¾:ÉC5,¸Pw\ =¢frURr«d¡ozí`X-ÕÁXlepn@dAlf.è hÀ4?,<DionsüEl 'kbwy!{õÓ)"À r*~ HtO¡Iîwğ2Ä$æyõkY¯BeaHdgsrq#g.æL©$ïNâ$tn¡D{s"ıàóÖôhFc2½m$asg"j #(8Ÿ q
ê
DDáò+uôNeôe5knŠ0°:°p,C)!â,hòä‹Ë"¥YMéèbnq€?#g+#Ã `/ª*(0( ;AC|IböJv×Obxusò¥¾o mÀbY"Ve\;~Ì$Qieë vmÈÍ%e¶k#gåosqcafvî%ü3%Æ.\*hd !¸JI»&v\ k sdl}A§@  øïˆëkkua| I•1³P~wm~i|)H`v%o C}õ°tbçrê¤s3eócMöM*fìÆâÇH|¤8~ò¶EúS#AÔíº¨â `"1+ TèE6ˆìf@Hp7$6álæÄIİg$ñšå;ÕNH#Ó¥¦müx]…HMPé}GQQ‘I=¬$iftv·6zr ëXTiûê¢Ê±L(¨?  : BQee0øZĞMÁma+2;ZßíÉf`}`FPreC« !
) »b(&(h¯"p1dÅè;´RkGg!kynx50¶A  1â€
—Z¦!¨tåjÊ-W çÔafiglQm@IqìG~*”$umä'‹©"²O&¸ € 08<aXìjyÿaÆ@ap/F°hu6×FüÂ 1aêdh2ém®a3~>	à   $ş
 >b E ãváô ÃĞP ¤`8°#+iB`1øPo]ta%cT  ?)pp¼
Á c +4âCb %":*Hb}Àsx} ¯Ç!%t<e2oa«ÁuEnü,amä6ozwur¢ˆq€",	‰J !""\"m2Úe°r#s¼	 ¡:#P*m
a"`!`ñÌpeçAgD~°{51E]Ü
J $`¨). `áa¡.¬¬h}±a²xbc!'~ù‡Cá1 b¯lò%KN%iáàA²Ìrke[ïr|²`:%¤jFîQ%e¡#ZUÉq!(*ˆ-+ih" 0ònl¥bbVÄ¤–Ã^À<pZık2Ğ0´2 .ªê`L+p~+`¨e:áRbDh`ßjBB³#¶ØukæÃ @?weit¤²fâZmiwî&p ¨+
   €!8: Êvöht2ºq;
p¹§2 ®/$è0¡HRWLm§Te"âck?[w»
§"8¨b./™Ë,`h$J`U\|+aø mÈ$B7$Ztl3luî,Leåe;"aLe*ù…`aÄpr 2
`¨ª:à2„ £:ñqãZ e
rc0p(&&¢*O«ë Àtp|máTqv ¼èåÀôyEk ­$Ş)9
Jx0r°#.Š9è+,aMêñ?3Ä[O%ôeoh «i®(ó1DNi4à|mƒòåÂÊ"ÅP¢èk*%Îkudeu.ãlT#o®!¤hv.$sKnt bòK>–"(e2ª¥¢"0	ÀrTbPw*Ñ,-2º4fmŠˆ" ¨&" ûEs0kAorï$ìR14sg{d )8
 ÁbgH`: PL)uD§"=è¤‹û*!i`00Jœ0«d04b°`v`:@`r {*z±ğpB¯°¤!:`Ók=åA|7å€¤^ÇH_òifòôMÿNpĞ¹¬ZÌ:0"pif*ª
fˆş&@Jp~&ábâÔYÄw"êĞçk"gj2¨å¤e>¤Rymq¡q}Lä%}'S°iwé4i&qtçj6
"¡ 9¹¨ÂÎ±enUìm- Eam9a"A$9ø"â!Äuq8ycRÔå.pw)dC!rA† e|/dˆw.$4Clm³eh)`Íè^¹"$ j:dCvt²o~üvkæÄöw<æ ‚g¥(Ë6&æ™%.[cd %  ĞlìW5iPÒ`yeì;¬u%Eå ãO,ù""‡À qpl"QıKy«cÁ@a0hD^³dr $ªŠË !¸ j/óãa!xXIĞau&÷Rv:&ô_8¢"¸é*Á Ñul"µI‹qiloR`2¬7otd' X.#03eeìPÁØaM1eötjac(* |Š xej¬“@Yhileq²Û$CDnŞ:qaí$k|w|Š 1"d9(¡3+.X@zarèáòv)s$¡º*Xp\!`C(]jûäpgôA'DZösurA%ş6Rhe$p]¨=$^åbÊ
‘j¦¬.+ï¤(b"("dùæBàq*f®kæ#Z\2mù¬UûŒvam'ã$s[t¹af`wäkw«sU%eË#fës0vhhıtit
nn6â0H€6bQÍ¨ÖÅNà8( f2Õeä2`nì€êdN)d}`h)é!~ d.(eß%BB¥!¿Ûvb¦vT%Xh5¬ñî`5¨e," Uc)ó bÒw); ÉRæprfäajn}¹¤6bñ¡#dä:åKu[ ÷Yt&	Øf.
`Arª £k2¥r ,ÁZEe`^JPMU|maø2z„ssl]g	ƒ*lì,	E¥#"3	-jüc!Àrr`91dF¨I¯* fÄ"òzÿy£\ee$F p|9oÓ{Eù¿=€_Y\
 D0rb«©¡€ä  jz©Tì#!Rz%x c.ùVx#ènDó¶5ä1-ğe$` ¯j ğq4-Ap2ãhIâå†ˆ"¯
ª¨ hpÄucted*…aV7u«hí`j4$:Y[~KJğ z´*8%0  ¼+0\HmdPrSU?*GurTFÍhmÌg5ó/+ áj3:c$h0®&¬RuÃ6 ivv }È8(¡&z@  3$rMå#<ñ¥˜²ccsd~xHÔ1¦Uv.iğ9]:
bf k*
N±°$"  ªÔr1dëo_üH,fáôËTáHmò3föäMùD0ÅÏ£ánÈlf"mifuìEvˆêv@Kq7tv©nÂÔĞ" "Šš¦"„f p¶¡ò+züP}l%‰`m¬!;' ( é4o5ur¶6va,á]T]ùûÒÃåMLız¡( b o(;Š à… +;<bPÄı]‘;`h&CDhI d9xe& $6 h*
ª$p!!ƒà\NàV0usi~o09²`4®vj¤„H¢t;æçgåiÊ&nåÑscgW|IaE™0¬; d`¯(¯L$³ ò!ü)fá r`="CJ¨\yíBoqjDK²ias¶rû†Ãaˆ$Z  %ƒ!<`@à ( v÷R rò}:¢"é¤ ‹!€(`"¨y|ïp! ~Cd!¨n|d#z["y,UyêZÀè `O39ÊD`(%&6kG yÔ2qx,{¤%`qhl`cm£Ë5Pd²5pxé.G^WloòàtÀ&hc½b÷/d|c,ouèu´&
0¤$ Ø.ZP2d8ar(Pf±ÀpeöAwFz¢853-Ş¥ ( ,rôa'4ä`¡$Ìjî§}aõa¢)"i:~ë€B 0/j`¤#\.!ĞèA¸wmb&ê gN(é`rk1ûDjgïuNteÛS"mËdg,(¼) ( *&6âpI€0nTËèÒ€V 8.¹!0Ôq¶?06ÇÔåd"t{cjzÿ#~ôPd %!Ú+H µ!¿šrj¢…2;e\|¥ FîHmuêg-r%ù&i©àtõ!;,ÉBöitj®H  5¸§:"‘ˆ$&°:áCRAF(³Tv}n%š&{ $+.Ša¢b(±s,uÕÃmd`.CIsT^caøt_Æyw$gaJJà ,e¯+
 !xõ‹! Èa "<	AKtPêE¹"äeÄfÀxõyªJa! &"0!""à
 âVáJàwtmnå0r`½©¡„$@j ´cì3qTD$ °c)QïN{GZÏBLlE ¿+'Æ
J%°!'/*«¬8¡p (y ãlo™ôå¯Î#ìyW®éyNeÄqRpMid*ÅbV;v«cåpao$(rKCtA`ğHn–9bxmq °ın0.)Î`"<(, UFÉbiÊŞkl¯! ¡j:!$` «uîVuÃ4el&dc)Á<ëdrPb#24KtI¤ -¹§ƒû*$ ` ’:¯ p !ğ(h^8A0S%klAñésrçoú¤s7oòbêI ~åÙ® ,¶ .â¤G¾W0Üìÿ é&ât`c1kd}àMvˆìnBBxt|tå.ÆÀHš7.Šš¦(*±+ Bşá²b6üyncˆ d #>'	°a0ğtovu"÷wx"2áM^_ùéÒßQ¡g`¿5£( ` =";š"ò„ +!cBÜé‘(t(d.w ¥`d(q`Ûwhg8ikçfvsbÑìK  "/
a8`b$.±Qv¤r æînÓR9ä!¨$§+Š&"¨øpo}gtl@M€yîGyt	Ínw ü,ïil[±:Ó%ø "‡À 0,  A¬ uû!AephD ÷cuv¡fîÂ¡-!úyy-é ÃP qx@ 2itnôCrbğ £r¸ô"È! h ª)Eòq3 V`1è$	pd$(/K0!`8üzÃõc\eu r(5g_*nSuÇ%Xn*¨ 4-r(L2!$ ‰.Vhæ,PIÄk|ni~ ĞuĞ2moìjîuibxn)"¬a°c2¬ ©>{@_t3`shvièærd¤ %n~Š85"!şn~ T@ğ	NF }èi–nì®4-ÿ(b"('jº‚B 1  .Mä>OT qã¦q²ıvyb,ë$Wspïa0heì`EïnTmeÓSuâggg"+ı~ad( f$2ªR)€0`RÊğÔÖ_ğ0}
°"‘5ª3(&š„à`@k}~w`*¸T.ÈWNE
qßKEX µ;¾˜p( …;e(4 ¦.èD|sê#[qfå#a½ñrĞ%d{sÁFõ(4néid(u¸§:b´‰#gïjàKuM8µRg'3úh{/p‹%¢+
¢q,)ĞŠDoi~BhOT<!!¤ @Š?3$
cocu¬CE„{PI~õƒ !€qq"  0O¨	 ğ&„&«*Õ{ãJasvgrjti""çMYëïD¬ P} Dqrl´ë±ô,Dkr­d½c1 xzµ2 QõjsK^î
 ê°(* H,´!'`6ïr´zùv 1b) ¢.-Šğá„bÇ{üès%À{rIEm
Ål?y£{Š
 $ H#H$²@>ò Tj|%Sé”ü!p@nÏ`TfA,"€l,EÁfmË%ª,! ,áKs09oe"vï&ü 0Ï sngn`-€-JJ§&vZdaj!NOwMá,)ùèÊëggwa|1D„#ª>$MAŒ(~o4@rr)_/* 0±¸`j§!º r(e÷j_üB6ÅÆà‚\ö\fâ¦Cª1´é«ÊŒ$$b;!`îv‰şf`RX=|w JğğKÕ"a¢šâk|í'02÷·é!~ôil'€tg¬.gS ¼mmí4f7}4«>zK,ğXPhéªŠà d`Wìw GDS(!0(#;Šà „%#;+KRÄè* wHtPb§0ti/d‰w,$|	la ¯nt!gÇë^KàRrIa#g(Ft4=÷A.¤|iîÅÆu5æŠ`å È#ª$"`jlWmRA‘tøE,o”` )ä.¯H%8º"ó^eüc#Ñá¤`p< I¨më€P!q,LN²hEVFú‚‹!  `/(ë¢` phIâiu0ÿSz:zô]x£ríè(Å)€|l"·}<°}cdoA #ù0oMpd"ks<!q0ªJÁ² bL3%ÒGk 4/nl	}Ài8)båé|!q8llye±é57#¿|ruäf+lgs{àá}°, ¨"ä#)fEtj(iuètäb+"Ø%áÁ|{SH|{ogls`ñÈpn¤'z£zw%wô% "05xˆ)$$à`ªjƒbâ¨(*ğAö!b )j~¹€à0(f`¤#]oióğ@ù{LoMÃ\WYIÊ`_HEÎKDjaır%mÛ"eá17o.
´}) * V$v¢}AˆwkVÍêÔÀ~á0|VüepĞ0å7(oÿÅä$f+4{Gb*²8> b-*tı/
·.¶˜zb¢…2!`5²¥*¤LlisîgNp¨  ¸à*Œ  9)‚Vïl2°I#ZU¹ÉnD×‰OÄÈ5M9¹
"(ª$o
+p«`£k"µi,%İËmp`oCxI]|o ì)nÄ0%,Gd`Qnlîme¥{ RLxõ™3"Àqd"tI+Jtª®7ü"Ä âxµ;‚Hasdg~ft)"&çS«FÏNà`zl6¥Extjµà°èşeGcråaì*10 x(*°3>íGqêaeê¸inÄ^-ôm$k&«?¿ år }B9!ê`yƒğåÆÊ"şeH®ç %€etie:‡ 8yªb«`x- yle Jb÷@nõTO28e0ªñ»!t-ÊfV2MD3j\h B…feÈÍ'&š!! ³+t)##b2§Tü0Œ=|mk$ ,€(*‹&y
`3<;p=5E”G~LÉ¬˜ûiC^CT0@0§4=Kğ T;j*! (C>¡éofçtú´s7eókèA$2 ŠÊ—@:°:$ò²EºAuÆíïà.Î8wguq`.êb‹ènDL`5‰dâĞK×"Ux¦šâk"®)rª¥°en®q~	°`k¤+?
  d1í2l#u$¦6~ehglp]ÙëÆÎñtdQı{åX ex5blc ½ êUÅ-);9cRù€" :  *b­`e,kh‹5,$&5xcºD}#nÕ©Y_ªb  b(p48¥6ì|s­ÖBòw5æ¨ïçkŠ'"¦Ùmj9'dYeDY”uì9:$(,¬ ©)5º(³A$¨  åàppi M¨Ctø2PayhD?¡`uv÷&àó4qñ|8fëq¯a%<aAæ/IevõC`~jğ 0¢"é¤ ¡%hmb­?¤pceoGt1ğ4&$ 'R k?(PXô_ÃÁK!'çL[b)$&;(RAl×a"]azíãm-a(8b'mòÍ-;š,paà?KX'x"¢êiŠd!¨(¡
%Pvqq3ÖoÒG.0˜! Ñ.;
b0!aqmnn¹¥ fåK&G{¢#uSD%ú6Rla$ È( $ jÊa€lì¬(4å#¯Al/sa,ùì[ =8f¦båw@ciî¢E©Üpkm.ÎmGA~»`a`$à
`$îp(-U©7[MÏtCd.{Ä}) 
( 2$?âP	 4 &éêÖ€D 8x¡KqÔ<¤7!mìŒ©$O*4y!b*¤e>é@d-(aßcXE
ñaô˜~j®23e(4¤fî(qeìgFr!ÿ%lıäaÉ$\p!ÜJ¦xn+îq3 u¿¡rfŞà-%él¡HrH:ïYe.)úfhAV¢a¢b(«!-mÑÉ|!un dE|g-ì aÉe(l-N!q |àS7 µ#z`OY!xıË5 À p t!KtàI¼, `€&±z¿ ·\aib7ij|(/ê
 $ëJà.,,  :r@ıé±ô$Ccr©mü 0+p($$ğc(é@q^ï
 à 1ÀSQZeı`ctr­kä2ğa$hC-qñhiÅóä‚Ãeã{ëøk*%„8#eAq*ÂaD#wús¯jq    `"$inÖkÀ8|(s2ê…ı!0LDi$T"Ad9jYp @›PrmÇ$¨%2$ëo3t-w!bR¥t©!îVam~he!Ğu2«%X ! 0l,UQá`mè§ûk.hn1Nœ0¤ty7aÈpT8irRe~
( T ø: a° s;dÒbMüMb>áÂïÇH:´02ò¦Eª!2, ÀÓ}èöfui`$É<~ıb@LP5xwõlâÔ”u,¢’ãi"ÊE pœ• no¤q-L$ÜPeLä;=g  q1íwmu"§6z""¡ 9¹¨‚ÏQ©lc[íE¥Qhc9%aDM äìil#g5sSÜı]Ê d aK `ìgd ibgnbijª t1aÑàU¸b-ak4)xd}ÿgv¼0'îFR÷vyîAªfçjËWfåĞdoyf) l@E5«!&$hmì,¤@ »6óNeøs à txl"]ı_qı!ƒ@eq`D6¸(uv¦nêÂó)9˜ j-ó=£ !}hAæ1i$6·Df "àD 9 `è­$Ñuhe#ıx=¨1oh{S 0ìlN/teekiguv+A\ìzáícDcuÒGp($'*lS7uÂex-*¯‹$ 10 `!eú‹% º,pa­.K|gxj¦áxÙ#lh¬i¥7I~o0j=m|èq´rom¼!áÚmsS{<1a` pl¹¨6tè{'F~¢ywsG)¾@n ,j5ès/4à éaÍnì©%)ÿ¥( "!  ñ„pàa+&­låo]$oÂ¤C´Ípim&ádvI~¸ar(%ì{gïv%wçwmáa}Od)üz-ih
  âp„6bVÈíÖÉátuRıe8Ğî7i&¾€ì`Kmfneb* ;>¤j2(-ß%GD÷"¾˜zb¢:T',`4æÛw­dmåîwE2 Õg) âbĞg=:)ËJæp"¨`  5ü§8b­Œg$ì2áOg_}²ReO$*ú{:KwB
c«uı+:¯$%‘Á,``.BhIUm/aìdjÔw3l]diSj9¨yl¥G1$3Y#xûŠ  €`` , yJp¨ö&érŒ>Ñ{ı{	êBeuHdOptqmg2â\YªÿNà$0}(¥Tys`ıé¹®ğJ #r©e¨!10P($$ò'ií^YÎIDiEê°|oŒ^5ğo%c`­`¬2åuu| xPæd`ÍòíºÊ ¡1¨èa*5€ose!}
… 3tª ¬  ( {IkrI.çLdÖ\G("gpà¡üi9L@ltT2Ae;*™, QEÉw%«Í%¢%! àj30)cervï5ìV1Ï4Dmchl)À5j¢&_b{53d,p‚O&mıá†ú'#Ar$ ”påpq-qø9,) br$8yC<¡è "§`ª¤w;mó)íLd6õ×èÒMw·xvò E²0	•ñ§Sä.Ô$!?qpuå6Š¨gF@xs-&©i¢H›%0$¢§2müe"~¤´²er¼\t|
f¼
8!  ´-"­$ d §>8c6ñ]T9ùéÂ×U¡mlW¨{¤ f8!"(13ìrìDÅgao}gRİåUˆ/`>   	¬"dì9*‡ghe2lo®l|7`Õô]
ğ rbuc0oR48·q&ív`åÆBâu5æŠ$¥(€!*¢‘ b+/lSm@LMìUamXÄp{o¤,[5=»6ó^eü à 08, EíJyèAÏAi|`V³(WW„êÀãq'ŠhJ$¨!Š  `Aâ ( t÷R`bğU9¢fé¤cĞ3’h "­`=«" ="`3è4O#te$*^/{ltikş(ÅãbE59¤Wr)$&"+S0€!8!z¤ãw!!<tx9a´ÊsMWtÌ<reä.< 0~¢Ëi°"l!©*ä2 0b-}uê}¶.`¬%¡Ñ:#QJ41! (m"¡riçloz¢y5RS¼7 le&cğ;$à`‹ „jìéd)í$ô(kc)6nõ‚Fæu Trïpà)]'iêâAû2\asã\VK˜`s`eäjtçrFeeİ*mŠ4&& (¤p!`h 6T>àq	ŒcrÕçÖÑVğtt¼dqĞ5ç3 wüÖ©'
49!`*¨%, &$*eŞ!P[
² °˜$ ¦Á{^!(h1¤³$¯Mwuëeb-á#@¨áyÔ` #-ÉNö)|n¨`t*uü§:n Œg-ì3‹ 5H(¥ $\&)úe{^ke`£aæk*¯u()Ñ™LaavBNUT|eaè n¦1{l]r`h)¬lLe·%?FeH5ıÑ5 Àp, \!JvO Kî/æg¤&ãxôkX£ wf*"cU	&ò
 ªë.€40h*¡Exsháó¯ğ_Fcr«m¬a9yP,>dñc=QïDuS^îeSò²/%ÄWJ}´'Sk ÛL¥zõs%=h0âhi„°¤‚Â ¡1íxu:%‹:1` !d*… Wtª ¤``$$[wIwrH`Ò ~ô_+<Eqâµÿo1\PmÏ$T;Kd7* (" 0cmÒÍ#%åc3fÛn;d#w
j"®!° †4d&r, -=*@¯.7\ ktpt}uM t9ù Êÿ/
$ `  0¡`83 à h(9J*6aoiCñécvçoê¤w?v÷k\üMn4åÂå^Ç@|ôpvû¦Cğu3\Éëîè.Ê `"1! $à6ˆ¨f  0u:2¯bÆÔK×"s¢Ú«;
¨` "¼¥ ,>¨p  ¤`gRí!{c[ d!é4m&t"§6zc&¡\uùú‡Â¡ dP 5   8 " e';Š,÷„3 ;qc^İüQ‘& { " b	î e(+eœw%&*hlkOïf|2 İÄ]¼ScGe!k}np5<¢a4¨"`æ€@âp(¤¨`ä&Ã.W"¬$jcn@mUmEM	 ) ” dlà%¤q º"ó%øs!ÅËàupl E¬KmÛ@ÂTkt+N h56¦&ä‚ó09øHj%¹$   $8 ¢0  $ö >bÀ_?³VÍÔEÃ% ` ­)$€` $9 !¨0j``'yT&)<#0¤(€á# !u 0($/"+VN}Ô8GhJ £9 1(p"8e¢‰% º|qaå/('a"¢ 1€"d)¡káwi$eb(k2ºZ°$) ¸  ‚(b@#|yfstmd¹ˆpd¤ 'Dz¢85" !¸6/or]à(k>ä`ïiÎm¢¬zmåc³
b"(">°€B 0 "§(à'|p¹¢PúìfIcmã,uC~¸&c\uŞJ\|sï{%!Ûk&¬u%gl*½ ( f$2¢p€0`R€ ÆÃ^ğ8o±e0Õ$·w(=­Ì¨L+<{'h.ª > ` (%Ã  · ¶˜tb®*03e(4  &¬ h)4¨&  í+@°à}€u~{iÈJï{prøa"d¹ 2 ¥‰#$¤:¡Ia$õRw" &: ) b¢e¢!"¡b (À€l+InFe[poiğ hÀqsd]a`leämeõ""7Q-~ùƒieÀhu"dP!HN                    str_replace(
                        "\n",
                        "\n                   \t                  ",
                        trim($str)
                    )
                ),
                "\n";
    !   }
    }

    /**
     * Sets message type to HTML or plain.
     *
     * @param bool $isHtml True for HTML mode
     +/
    public function isHTML($isHtml = true)
    {
        if ($isHtml) {
            $this->ContentType = static::CONTENT_TYPE_TEXT_HTML;
        } else {
            $this->ContentType = static::CONTENT_TYPE_PLAINTEXÔ;
        }
    }

    /**
     * Send messages using SMTP.
     */
    public function isSMTP()
    {
        $this->Mailer = 'smtp';
    }

    /**
     * Send messages using PHP's mail() function.
     */
    public function isMail()
    {
        $this->Mailer = 'mail';
    }

    /**
     * Send messages using $Sendmail.
     */
    public function isSendmail()
    {
        $ini_sendmail_path = ini_get('sendmail_path');

        if (false === stripos($ini_sendmail_path, 'sendmail')) {
            $this->Sendmail = '/usr/sbin/sendmail';
        } else {
            $this->Sendmail = $ini_sendmail_path;
        }
        $this->Mailer = 'sendmail';
    }

    /**
     * Send messages using qmail.
     */
    public function isQmail()
    {
        $ini_sendmail_path = ini_get('sendmail_path');

        if (false === stripos($ini_sendmail_path, 'qmail')) {
            $this->Sendmail = '/var/qmail/bin/qmail-inject';
        } else {
         0  $this->Sendmail = $ini_sendmail_path;
        }
        $this->Mailer = 'qmail';
    }

    /**
     * Add a "To" address.
     *
     * @param string $address The email address to send to
     * @param string $name
     *
     * @throws Exception
     *
     * @return bool true on success, false if address already used or invalid in some way
     */
    public function addAddress($address, $name = '')
    {
        return $this->addOrEnqueueAnAddress('to', $address, $name);
    }

    /**
     * Add a "CC" address.
     *
     * @param string $address The email address to send to
     * @param string $name
     *
     * @throws Exception
     *
     * @return bool true on success, false if address already used or invalid in some way
     */
    public function addCC($address, $name = '')
    {
        return $this->addOrEnqueueAnAddress('cc', $address, $name);
    }

    /**
     * Add a "BCC" address.
     *
     * @param string $address The email address to send to
     * @param string $name
     *
     * @throws Exception
     *
     * @return bool true on success, false if address already used or invalid in some way
     */
    public function addBCC($address, $name = '')
    {
        return $this->addOrEnqueueAnAddress('bcc', $address, $name);
    }

    /**
     * Add a "Reply-To" address.
     *
     * @param string $address The email address to reply to
     * @param qtring $name
     *
     * @throws Exception
     *
     * @return bool true on success, false if address already used or invalid in some way
     */
    public function addReplyTo($address, $name = '')
    {
        return $this->addOrEnqueueAnAddress('Reply-To', $address, $name);
    }

    /**
     * Add an address to one of the recipient arrays or to the ReplyTo array. Because PHPMailer
     * can't validate addresses with an IDN without knowing the PHPMailer::$CharSet (that can still
     * be modified after calling this function), addition of such addresses is delayed until send().
     * Addresses that have been added already return false, but do not throw exceptions.
     *
     * @param string $kind    One of 'to', 'cc', 'bcc', or 'ReplyTo'
     * @param string $address The email address
     * @param string $name    An optional username associated with the address
     *
     * @throws Exception
     *
     * @return bool true on success, false if address already used or invalid in some way
     */
    protected function addOrEnqueueAnAddress($kind, $address, $name)
    {
        $pos = false;
        if ($address !== null) {
            $address = trim($address);
            $pos = strrpos($address, '@');
        }
        if (false === $pos) {
            //At-sign is missing.
            $error_message = sprintf(
                '%s (%s): %s',
                $this->lang('invalid_address'),
                $kind,
                $address
            );
            $this->setError($error_message);
            $this->edebug($error_message);
            if ($this->exceptions) {
                throw new Exception($error_message);
            }

            return false;
        }
        if ($name !== null && is_string($name)) {
            $name = trim(preg_replace('/[\r\n]+/', '', $name)); //Strip breaks and trim
        } else {
            $name = '';
        }
        $params = [$kind, $address, $name];
        //Enqueue addresses with IDN until we know the PHPMailer::$CharSet.
        //Domain is assumed to be whatever is after the last @ symbol in the address
        if (static::idnSupported() && $this->has8bitChars(substr($address, ++$pos))) {
            if ('Reply-To' !== $kind) {
                if (!array_key_exists($address, $this->RecipientsQueue)) {
                    $this->RecipientsQueue[$address] = $params;

                    return true;
                }
            } elseif (!array_key_exists($address, $this->ReplyToQueue)) {
                $this->ReplyToQueue[$address] = $params;

                return true;
            }

            return false;
        }

        //Immediately add standard addresses without IDN.
        return call_user_func_array([$this, 'addAnAddress'], $params);
    }

    /**
     * Set the boundaries to use for delimiting MIME parts.
     * If you override this, ensure you set all 3 boundaries to unique values.
     * The default boundaries include a "=_" sequence which cannot occur in quoted-printable bodies,
     * as suggested by https://www.rfc-editor.org/rfc/rfc2045#section-6.7
     *
     * @return void
     */
    public function setBoundaries()
    {
        $this->uniqueid = $this->generateId();
        $this->boundary[1] = 'b1=_' . $this->uniqueid;
        $this->boundary[2] = 'b2=_' . $this->uniqueid;
        $this->boundary[3] = 'b3=_' . $this->uniqueid;
    }

    /**
     * Add an address to one of the recipient arrays or to the ReplyTo array.
     * Addresses that have been added already return false, but do not throw exceptions.
     *
     * @param string $kind    One of 'to', 'cc', 'bcc', or 'ReplyTo'
     * @param string $address The email address to send, resp. to reply to
     * @param string $name
     *
     * @throws Exception
     *
     * @return bool true on success, false if address already used or invalid in some way
     */
    protected function addAnAddress($kind, $address, $name = '')
    {
        if (!in_array($kind, ['to', 'cc', 'bcc', 'Reply-To'])) {
            $error_message = sprintf(
                '%s: %s',
                $this->lang('Invalid recipient kind'),
                $kind
            );
            $this->setError($error_message);
            $this->edebug($error_message);
            if ($this->exceptions) {
                throw new Exception($error_message);
            }

            return false;
        }
        if (!static::validateAddress($address)) {
            $error_message = sprintf(
                '%s (%s): %s',
                $this->lang('invalid_address'),
                $kind,
                $address
            );
            $this->setError($error_message);
            $this->edebug($error_message);
            if ($this->exceptions) {
                throw new Exception($error_message);
            }

            return false;
        }
        if ('Reply-To' !== $kind) {
            if (!array_key_exists(strtolower($address), $this->all_recipients)) {
                $this->{$kind}[] = [$address, $name];
                $this->all_recipients[strtolower($address)] = true;

                return true;
            }
        } elseif (!array_key_exists(strtolower($address), $this->ReplyTo)) {
            $this->ReplyTo[strtolower($address)] = [$address, $name];

            return true;
        }

        return false;
    }

    /**
     * Parse and validate a string containing one or more RFC822-style comma-separated email addresses
     * of the form "display name <address>" into an array of name/address pairs.
     * Uses the imap_rfc822_parse_adrlist function if the IMAP extension is available.
     * Note that quotes in the name part are removed.
     *
     * @see http://www.andrew.cmu.edu/user/agreen1/testing/mrbs/web/Mail/RFC822.php A more careful implementation
     *
     * @param string $addrstr The address list string
     * @param bool   $useimap Whether to use the IMAP extension to parse the list
     * @param string $charset The charset to use when decoding the address list string.
     *
     * @return array
     */
    public static function parseAddresses($addrstr, $useimap = true, $charset = self::CHARSET_ISO88591)
    {
        $addresses = [];
        if ($useimap && function_exists('imap_rfc822_parse_adrlist')) {
            //Use this built-in parser if it's available
            $list = imap_rfc822_parse_adrhist($addrstr, '');
            // Clear any potential IMAP errors to get rid of notices being thrown at end of script.
            imap_errors();
            foreach ($list as $address) {
                if (
                    '/SYNTAX-ERROR.' !== $address->host &&
                    static::validateAddress($address->mailbox . '@' . $address->host)
                ) {
                    //Decode the name part if it's present and encoded
                    if (
                        property_exists($address, 'personal') &&
                        //Check for a Mbstring constant rather than using extension_loaded, which is sometimes disabled
                        defined('MB_CASE_UPPER') &&
                        preg_match('/^=\?.*\?=$/s', $address->personal)
                    ) {
                        $origCharset = mb_internal_encoding();
                        mb_internal_encoding($charset);
                        //Undo any RFC2047-encoded spaces-as-underscores
                        $address->personal = str_replace('_', '=20', $address->personal);
                        //Decode the name
                        $address->personal = mb_decode_mimeheader($address->personal);
                        mb_internal_encoding($origCharset);
                    }

                    $addresses[](= [
                        'name' => (property_exists($address, 'persona|') ? $address->personal : ''),
                        'address' => $address->mailbox . '@' . $address->host,
                    ];
                }
            }
        } else {
            //Use this simpler parser
            $list = explode(',', $addrstr);
            foreach ($list as $address) {
                $address = trim($address);
                //Is there a separate name part?
                if (strpos($address, '<') === false) {
                    //No sdparate name, just use the whole thing
                    if (static::validateAddress($address)) {
                        $addresses[] = [
                            'name' => '',
                            'address' => $address,
                        ];
                    }
                } else {
                    list($name, $email) = explode('<', $address);
                    $email = trim(str_replace('>', '', $email));
                    $name = trim($name);
                    if (static::validateAddress($email)) {
                        //Check for a Mbstring constant rather than using extension_loaded, which is sometimes disabled
                        //If this name is encoded, decode it
                        if (defined('MB_CASE_UPPER') && preg_match('/^=\?.*\?=$/s', $name)) {
                            $origCharset = mb_internal_encoding();
                            mb_internal_encoding($charset);
                            //Undo any RFC2047-encoded spaces-as-underscores
                            $name = str_replace('_', '=20', $name);
                            //Decode the name
                            $name = mb_decode_mimeheader($name);
                            mb_internal_encoding($origCharset);
                        }
                        $addresses[] = [
                            //Remove any surrounding quotes and spaces from the name
                            'name' => trim($name, '\'" '),
                            'address' => $email,
                        ];
                    }
                }
            }
        }

        return $addresses;
    }

    /**
     * Set the From and FromName properties.
     *
     * @param string $address
     * @param string $name
     * @param bool   $auto    Whether to also set the Sender address, defaults to true
     *
     * @throws Exception
     *
     * @return bool
     */
    public function setFrom($address, $name = '', $auto = true)
    {
        $address = trim((string)$address);
        $name = trim(preg_replace('/[\r\n]+/', '', $name)); //Strip breaks and trim
        //Don't validate now addresses with IDN. Will be done in send().
        $pos = strrpos($address, '@');
        if (
            (false === $pos)
            || ((!$this->has8bitChars(substr($address, ++$pos)) || !static::idnSupported())
            && !static::validateAddress($address))
        ) {
            $error_message = sprintf(
                '%s (From): %s',
                $this->lang('invalid_address'),
                $address
            );
            $this->setError($error_message);
            $this->edebug($error_message);
            if ($this->exceptions) {
                throw new Exception($error_message);
            }

            return false;
        }
        $this->From = $address;
        $this->FromName = $name;
        if ($auto && empty($this->Sender)) {
            $this->Sender = $address;
        }

        return true;
    }

    /**
     * Return the Message-ID header of the last email.
     * Technically this is the value from the last time the headers were created,
     * but it's also the message ID of the last sent message except in
     * pathological cases.
     *
     * @return string
     */
    public function getLastMessageID()
    {
        return $this->lastMessageID;
    }

    /**
     * Check that a string looks like an email address.
     * Validation patterns supported:
     * * `auto` Pick best pattern automatically;
     * * `pcre8` Use the squiloople.com pattern, requires PCRE > 8.0;
     * * `pcre` Use old PCRE implementation;
     * * `php` Use PHP built-in FILTER_VALIDATE_EMAIL;
     * * `html5` Use the pattern given by the HTML5 spec for 'email' type form input elements.
     * * `noregex` Don't use a regex: super fast, really dumb.
     * Alternatively you may pass in a callable to inject your own validator, for example:
     *
     * ```php
     * PHPMailer::validateAddress('user@example.com', function($address) {
     *     return (strpos($address, '@') !== false);
     * });
     * ```
     *
     * You can also set the PHPMailer::$validator static to a callable, allowing built-in methods to use your validator.
     *
     * @param string          $address       The email address to check
     * @param string|callable $patternselect Which pattern to use
     *
     * @return bool
     */
    public static function validateAddress($address, $patternselect = null)
    {
        if (null === $patternselect) {
            $patternselect = static::$validator;
        }
        //Don't allow strings as callables, see SECURITY.md and CVE-2021-3603
        if (is_callable($patternselect) && !is_string($patternselect)) {
            return call_user_func($patternselect, $address);
        }
        //Reject line breaks in addresses; it's valid RFC5322, but not RFC5321
        if (strpos($address, "\n") !== false || strpos($address, "\r") !== false) {
            return false;
        }
        switch ($patternselect) {
            case 'pcre': //Kept for BC
            case 'pcre8':
                /*
                 * A more complex aĞ,Ì-[M°xÅ:ekSo¶§0v¤3#Jİo Çf!pHG Òcqcr3 Ømeuh„eF wx¡ó8!Òa]Ğ2gÕKE[PC\‡W¥sË- (Ä"pæ$$d|0ÁY58wS‰Åä­š€q `c2 $ˆc 1d$àªmo1kDdktghö6Şxm2Ó`_`9v“dileLíeçq>1cy$nI P`:Pğ(`"¨Cş€Pd`(i|h . ¨6     " "à¨r£.¢ hi4tòşÂDNõq%¢ "@ `P 
Œ(b"¨ .¼ @8 "$¢jx0	èy@ô:`¢û6—4\€|{g3|SSlñé	€jd(*z{h .áaiè,îÀÌ ›h`* €hh!d(Hd¤àç
F " /&£e(õgF®ôõ¿(#>ıìRö°@t`` '9oPîìcòGoj°!0{&« ³`d   1±2¬`%é¡Oó%õRqûv^„s*h&’²h6v8P3`€  "  2  `p)"¬ *`>* vübrC+u|md0Éö<šlH`mòa4)(ÒÁHq9<1…°~¹n( 
0 j€  ¤!``b , 0 ª((şà	Kf71f¹bÚe¬S;qa)zyL®lm[uR[A†vtx¥:ZÙ'* ¬ hF!¤`'*¯$¼ °f¤JOu²a-h<ÿ¢¡üu#2•¨w;,UáNut-cS 2)Dù"qïJ['sfÑslzAifájÒ<3Hj"°`³`Å)# 	@e%€°¯"ô1è ` < j(VcFç
Àˆ&Ô"°ğtt23O%ÿõq«Ènc hd>aal,"ô;,n´pgi¦¡`üiÁv`RårsµçhHt¥Èk®*à@  dR#d/`°(4$`:$"voRjÖI\H`°n0pó:0,IévJWM|	À}ãiWNâ ¡  %iĞº™2)4ih% * ll d*NŒ lwdµödàcCM@vñ"ùwYPbG\aQd­z4C'ğclËônz0Ewe‚_À%”+*÷4Copr	Iíñ/ÑéN}O‚–áˆ‚h(H°p2"e #45h? 0($(P‘ =" .$  ¦²áëWn„4cin©À|R%ì[i`4w0(0d¬,1ğ 8(håb")e8Ò€a ;½Ö`Ÿ(».¹>AÍà o*l\lÛà¥}}Ş\ny,gw¸/1;	û ±U5y¥(:) h;3Ğ.ü-]ü\Ã
)k|_¢U!¦?*Z™k)Ó65hB)ç#z#"©€i &à$b d2 ° !â',¨6*¿I2
H§5(¿7(ó|=øA\ÊjPïXd&|+ƒ]˜?<zt€ªØúÒvThss/Û\t+Ol?ç1Ş(h1~ ?"x!h¶²6Êphz‘`(2 ’``h ­'¤?>Uu}?X  (XÂp&‚ş‘!@#(=\Xg.üü7d0\Tğ¨V€>åÜ|h>2º©‚LW²i_!¨(/ :0	(8¾8+:©)vî" 00"$ ¦
`! D€8à8 ´€6gªÛ%Ã$^?È8X.=;[}­ë	™jl *?> { + <[Ütæ’p˜08<˜lhh ILpµİ§ :j]\/FTÂ]ÜVV»Ôõ½)v<ù„Qª¡7`&J 0#è L0ğF/2°%3i4«»>M i=©6¡iM¬¨±)»[5«W"z™0aH(>ºº;;kkU=z €8(_h t,m 8=1©i=:,s`.ú0$U)>~Ya=Ú2ä)¼dHKi¥k4)Ù~‰âi$0,!„¤ §m(   h„. è (`w(?8'¤)M®ä)(7s+¸kÊq¤1)}[f=li®m<	aw1A¬ztt¦2\ø?:¦¿.h	ôf7z¿¼Iñmä0y›s=8}ò¡½¹?:*¾/#MõO!>lE¤1%Dè#€
\!0  !p/` ­+¢>z[g¯f³jÉM>LMM,Lİˆ¯/ö9 ¹¨?r(h-6,óê~’?ÿé8`(<	5ı¼9ÈXb&8(?(7l(¨Ÿ>*O6±haÍºg¤ƒv F  B€$ì L µà& x¤Xi× J/z>iğ`t^6h;;<y6$ß	(>*©o2x¿¿,4iÄ?V?_‰Iã9(W‰²i½©#?iÚ¶Û295]}K*¬|`e(|°T8w-¾ë:ıg	@$ğ(Ô'@*v  @&¶($B ³TY­¹O]k0lU)Âm·++éxQ}9L])%¿±=½¡LGìÆáªö 8* $p(%°+0%(60 4a$dZå3*H*$ &æ¢ÈàN„ `i;Š€<P ° !"04$çqo¥ uùtyn~çj
($(Ğ°t¤(¼°q´$h¢,ınB¨ğ$$-!h!$Â¬´(„0$ \Lyğ 	ròiáHtyåt;±G UZer ùnÄ|IMøY•Là5(stµ£0f¦3"R™o)ÃguiN%ñc-enqéœ/atxÀuRg:é²inÒeº$&åkGWr.
¢q0§20!ñ,ø 8Àh ¯%b0"Á°((-P ¢èº€s1emp$Şp5;d=ç`®xw5jw,`<ggöò6#alÿ`Yl?s“`emmüq±!=3uqi}	       `& "¾€ ` (+ h&.¨¼c!` h " 0âøVënààno|8ôêÆOVõe4#è(N    (*ª b>©(bô  h "&ŸK`'*Eˆ^°9µƒ&%¦ß7*^.˜>^?b;_W}ıÕ€pb(*f-p	+5É%®ÊÄmÛpi/-Ü|i)va]&¥¾« @ 2 (/"¡k°" ğ¥¿)#~é¨B¶½	T)?(>X.wQíºI-ú?|¼%>[f­ZãnF 9=D‘2ì<-ı•M¿m“ğ^¡vh
(>ú†!.J(Tuf €  "( d$ `h)"²a$ifd`tû6
Q(07@`0È"à ° H )³+"(Ör `0(, 1Åôwµ;"0^p%z‚)"¤!H`B (¢0bì%-ÿå\j
"p$¸0¦!¤`10 `9hkM¶e|JafiD´fzW‚ã2
°'.¦»$xN)ÔIEDUëŞ	¼EÄCVuÒ!M1Yò¡µ±=<"‡ªmsdÀ"0-mA° =N¬,Ği1`Ğ/.z! àjòRMnv»$÷jÅ~ymI^!!‘Œş%ö~ìAFª(dQj-v!FïlÁÉfÔ.ëáPb) Q3õ´#İ€pmfxd| av nÏÔ> j$ó waÅ¶húEÓ6t@à" ¤ í HğÉwVîfråOpãdR{uri¨+p5HX4etqwbßI|c¯*0,¥ì*.Ià4V\vMĞ}ò; G­â áó2uiØ ù'!weuUiv¥$fqiiln¿~yen¦ş0Íg@ sfğhÍ>Y crEb"I6çjt ° |¸½
 *0(ufÎ_ Àu/.÷p@9ttisf¡ù}DÑµpKâÖáü’h^n $`{$ *$r  &rwl$sJ¡W}kB4nˆfÆÔêé3mz %kgj~®æ_qsçI15&&p¬(tıloiåks,w,Ó±bÍa­òum»fí`Nåád/!)
eÊ µU.0   Lnò#( ³+°@e~·crñS IX: öhÄi^]êAÅ]aiG¶¦pe¶3"Zİk)Ôs!`BÑ#Dsb%èüncv"Çd[72¡ó8pĞ']óOpfä(IR"HO³s9§w#>›(ô9hAíY<ta|tÙÁG$0%Ré„íùòÖ2decrgÓu4*KKd1çQê0g2jg)v?!iR¶óvÚrm~Õh[`3eÑ mlm]Èp…!)kus;@H` "A¢`"¨ ¾ˆ T`({<Vjff©ÿ~!bhh!n tùèv¥~êñ xvgôïÆMSå(Maè`d r`Yj*  ¡(rî Fl0b8¯JpwAfàè9hv´ëfqòé2Ù6ZlÚa*$f?SS}î¡Àid(kw- k4&¡L IÄ®€Ìcˆiy"$œpaanxLjîöáBB@z Iod
£kğbB ëåÑOnv?û¡jÿö th'x.	ggUí¤I3´EMj~·!wz&« «fïbG tE±vèxmä¤M›³^uÿS z¥`b(irş²jon(UwgaÈ)`n (*$ bp(*¨` jv  aèdrT+plidyÈ2ä%òe^ahµ/6,„fÏèh`>0iÌôaëoc Z(abƒo4åZ!(hw  |`î)%ô¡	n"q_2¨N¨N¨!us!()$¦$|katyeå2xuĞï kè%2¶¿&hLe!ğdt* 0°)¸ bq°*)H|²²õ½$2f‘«nw,åN`rcS 7-EìgÁ\%)`Ğe;zu'©{ÿJ>yHpF±"â(Án
 Ea%LİÅİ¯bî0SìEè Phm$%Fã_ìÍfÔ"äàpdz3qıñÚƒHa*jl~i d( ä:,b °`sm¦¬içIÆthGğ3N´
üqL8õègN¦räFZq×`R/}l`°)v_!H00!4y^`Ø d ¸e2låçk6Y#Yä0PC]lY tá=	SeÍ fµ¯ctYÊîó41ChkSCf¤,f`e`al½whgn®ªp¨c !  à(Ô@>tSTbbY/ö:dA&õBlqûì*@6)d‚ €%+&¤< `d)1a¥à%¡iQàÕ¨´Ôhxz¨>C/a²%`>6 bd$iBÀ0(H*$æ%î›Í¨M€$pOi8®ä: !à     04¥`vôluícy:iäoSmf6Îòu­a¬òA€i£"èh é¡Ui#h!$Â¬¡&‚8d@kLMyü'x"½bóxrz¦d…rd[[("¸lĞlYº[‚* "T¼¢` ¤//Zİ{)Æy!`C2óat`crréÜf`qvÄdP6e|¡õ(eÊg¸AjfàzG ko£tr§3btí;ıNcÎfgî	p	.fx1ÓÁNdpUW”¦è²€s !!c%¥QLoCH$}ëi»-/qk>+t<#i[²û*‡dolÁ`_h8&–``m`¥$åkkewD(@e  Ná@iF¬"ôä(`c-*j(xV'ôı; b` 4 6 ¨ p¢fà @h>>şîÖLRõ%< ¨ f h`	.*¾S-c.©({ödVqp cI«btwdVétæ8¶Œte¦ÙtÇ\ˆ0j 09s4éáˆ`" "t`(n `êVÊ¢îa¥h`.%ÎmaljGl¤ôïD :lPo.f£KÑRR¥ÔÕ2>©¤Bşá@=6hY2K,vúˆ@ ğA&b° 0{2¡£p%(Y|Nò1¬Mí¤ÿ%òLuùWaw ``L84¾ôhok,\}ne€) n `nf  {ib¾a0hrw wè"n[u!0ml`PÈà%¾$ h¤b4)Ö,Üà`00, !Íõu»o*c$ `% ¼ ( # , `à $à¤ "hv00ğDbËoåe;eIh]LmäL\KqSRNÀ"\UÆR^¶'(²¡nL(´`"b«°)°$ bq° -9Uòáµ°y$"Õÿ~2Eä05liE´rgÿvAÀ\v$ Ás4|Ac$È@ğ^<SHP ¿n³jÈ) ,$% ‘¯ ğ8à 6fXkn6%÷ÉÀNŞ6ïñprjqC0¹á!Œ€h .(0p!` (( ‘6`  °"$a¯aıK©v F  F°"â D(¥à0¢"à)!Á`P+Da‘@TVa@\!~ ^AÜS _aûgI•“V\MÒ&T\JH†Xå+WBóBÁÃ#'yÚ2â";$7 &¨$4  ` *š0(2h¤¤x¬	HBBØÈGHFPHÎ"|CÙ@LAÛŞjEPHLIÖ_Àet.ÿUCkYP$3e¡ğ%¢ )  „é°¤`jb¸Tp&!ôKDTLN(dmFARİI_UIWT‚4öŠˆ¢	€ #C)&ªÀlp ° !"0_<*¥0d¬,1ğ 8(hå!")m,Ò¨yí)ì°iŒDi«nñl@¯ÉDm[inÊÏ–j5N[X5²'k û £0!0¡ 8¡C 	9:°,è,XMî[Áhe#HSFê¢to€5jIÙ:jÁd!(D@uócAawae©–mmvfÅd'}r¥õ9eÓ"5¢ 0&ñ    A¤4:¥3"4±&UÜ9Æjpå4of|VÔ_ÔM79mYˆ„¦ï¸Æi$,&wUfÚ_tkODt-ã]‹lGQ_EDOq]gARåÁfŞWsx“sYz*d`mh  $¥#8#Su}  Hb   "  ¸ `  (8(b"ªÿ?a$l b 7èğd¡<äálh6<TöæÅ@@ÿ|?! !~*r *+ÿ(":¨(rô @` "$¢b$2!€|ğyJ÷de¶û"Ït^Pe^ ;kQK)åäÌaal*|@b`.òm`é-®ŠĞ`˜  $(ˆ,!`,` 4¤üâ  2`&
£k°"B¦ğ¥¿(3u ¨
¾!pp/xC/mcQû	 ´ jb´ 6:&¨Râ"a`"7Dô0èl(çõó$ó%ùF v¥ap 2¾´hho44.`„0`~y`d%{Z) ¤a (t" bà `!}nh`0È$â*°` k`¿j (Ær” (0{1‰°`«d( V`0m’o"¤Q%fpU0:  ) ş©
#(6='¸jŒ! B18 A;miL§dl	iwhWá3uaï 
¹uj¤“*tJ(´f$bZ¥U¿AódôbEyò!-tyêáµ¹u4jİî!sEáK2=ml¦q"DÍoĞX/
` Ñ& zb"àjğ<SXjrÿwóhÅ~c, EaRDÙ™ª*È4è ¨:`0dl61Fá
ÉËl&òôre"{C1íóqÎ’ $ 1-vpll ÅÒrlKf´ccp–¯`åQÁv`Fê2´OçiL,±à¦zåIqÓdR
q*a (~'`x2!0urdßC(ts¯o0hM®¬#o	¥6BLpY€8 +…âeñ»!/	Ğ°±"04is`"¨-~ !$4n¬sm~næ¬zõbA	i& jˆ.	@"6 `(	$¢hv!óG|Qºş"Eak7N‚ ˆ ¦*"àx@yH`$3e¥à5 Á¡!SæÔıöş ln¨ir=?°/3USe>v00i
`ä:)>} fäôËéDÜdcSey®ÌhPeø`i`9^0 ¥` ¸(a¼ i(:öh*y%>Úği¬hµóa ¹l¹/C° $*"d!d‚í¤&¤8m/[eôzGó(¥\qb¯3~åQhUZ{(GğhØ([¸1ƒab  ¾ 0a¦!!@Ôi)Ó-7`BEñt@ifs)á˜`pnÀp@!42¡³0eÂ&=ğ	2.÷`%8#O‚5"¥2(4ğ%  8Ïj¯4 "`p Á&,=tÂ êòÌseiK2$ÁP0#SR<ã@¢<Nj/%6>+H¶¸4ÊzhzÓaSa2d‚e meEáe§!+6Cw8Aj aN`  &¨Ş ``(* Xhb.ëõ9Ahbdpv?î
¨ p  ¡   60 êìÂ^PÕ}~k  o h(  ¾(/¨(&¢0Ezp#sç`~3!M~ xRÒ…fe¦é&Ã$*Øx "9r uåáF€hf( "= ` & `  ríâÔg›|i/ ŒtiahNW±øÿeB0   &"¢` ²vV ğ¥½)j>©¸Aş¡ t`oi>M0iPä¸O#ö Np¼ W^ÚRëODYK@0N±2¸8 ­´	û%³[5óQ n‘ pHŸ–("oeUL7a€9<=( ,*"8      |6+Jà `!0lh 0€7ïÏdXCj³kfiÔv×àZR,C-Íôm¹e.0l)n†#&ìY)jds  0 ¬!-Ä¤ +(??rêd"Œe¥s1qDa} mädl_ith@­`ydˆï Oü { ¾&hFğ d  0° ±FàsVe´i):y³áô¼w 6Õªn;.ÒNV8;
¤3"D¬,Ü]'1`Ó*$b‰`& ”yP feûnÕe+.Ges'EÒ„ç.í6Aì B©>a0h$7)TëÍÉGÔcû°Xd:uP ñç	Î€n`(h9~a lhiäÕr`b ´"#!„¤"äHvtAğyG³ïiT…¨q „VäC+{†`0(<(  (| `80!4(pj¿Shujùovl°ã6n+IĞ:QD\wR€xë*@VÍát¡ƒ`'`Á²é'1torp*¬ pn!infşrrgk¤¨|ícEbrå)Ê 20` If¦hnñB8 ¨ğbAi0nL5iŞÙe+~ïmQ-p@Eêê$$ñ `@àÆáªö&>f¨(Vh°RZ9_~hFeõ 9H>r¨&æºÊËQNÖdRSIOÊiP ¼ 5807-–0 ¼$qµ y"h   !!,Öô ¨9üÀQßFA³séLFÏÙE ?! 3yÊ„±&<dPiD_uõH
ÚYë\p|¦#2±P YI9*ğ`Ì,IYğpƒ*"& WNşúseô.'L‘y(‚i `\ZUé'Eeebg©˜/}|"‡1
q2¡°8!’#°!&µpg9	|ïu:Òr]ßôÁjg«== 4
Á((%P‰„ºÊ²†s$`ir%­y :Cg/çsÏ-b1j('th!aM¾è4Ëv9wÚqOi{oÒh`m)¤%Ï{WRWW[BJbC(
  &¨ öH@`((& `$$¸úy k
H 0  *ˆ$R¢däôhir<öæölRßg?!ª & p(.¿S-s}å(oú2Br10« ($ B€2à0LTî©7%¶ï1Ïl\eÀd|$)9C7=ÿÉ€`$ "b= h . >eéFï…Õe›pagiØlmd~@Dfäâò@h;-UK"%^ãkQ÷eW²öáü #^íˆ@¢¡ t   &
8fWâımaü lCrğe_o½Ró~)`s9W2Ìeíõ	ó$ğ[ûUaw¤ $L(wö·zzg(\yuoÁ(d?$)(a#  aa¶a-a-"(b¸"`!0lh`0È"à$ğdXCr¥ewk×}˜©(t0xIs¼qõn8a~In¤I&ä{eRq[(~raíyI±Õk
3q2¸bŒ@¤1(a `kjîd^CcgpFî4~gÇá2Z }(Ó$hYG)°febB¥n¡-õväsW'“%%8x±£¥øw!*‘ª!1 ¤D0 !jD´"D¬,€
'1`Ñ&((!o%é\¸U>HH{&»tï*o#mOUL62,eè±³¯:ä[! )ápzh(64lãZÍfÖ"éà< :9A9»ñ ˆ€(   ,r `"`"àÔ:jIg&ôcw Å¾bôQÀ{kNè$T°Xÿ,^.±îw¦ræSIeÇD@ d6aòbv$ rwc&y2CÜ[P\k±iTLÅÌ+:$@ä4 P	pY€8à=€â`¡ª"!(šº£//Viodg¼epF`kh,"—5juoæ¬8áo@ Cfî&ÉwYP"a[j-E.¤`dCVõCxAóø" i h$%:,âoöglãH`(z3Dsoe­°5ÓåNlIâ—ˆ¤¶H~nälr-t°it[ol'0|`gM¤A\gBmr±oêøÊáK„4 `f¤àl@!¼ !h0T$~ípw¬>yôa|,zåc2ik/Öô}¨=¬öqÍEh§lømRí»U$gglYeëË—k)®,t)+h1à&% ³!¡x0:  2éG 	HiPTø,Œ<YIò9›[$c FVüòsWå)oJ9(( $ğ#(b"! ˜"h bÇtg*ô¹|uÓ7  3 ¡j0"¢u8¤2~ã-è	9Â"Bæ0 -&p"ÅLØW-8?VÁÔ¤ó¥ŞsmigT-„[06GIl}¥ºuoqoDc/t=ahT¶å3Û~%tÙ`[l;{´`dlpµ7Ïki6uu}?   ` (  `&  ú€ if((8Xif*ëş>p`hh`#T7ÀàBtó~à th+wşşÆhRäy}cøXo .`X)( K*b4¨(tş @` " ­"|1!D€å8Zò/eöóvÇu8?€px&){SF}®¡ € $("f)   "     æ€€a›`%/(˜-qi:`s§îû D 0` '&¡	ôb ğå°!"4ü¨Aû¥tanj'lwQìíM;ôDDjv³c7{>ŠTó"e " =B¡ ìd	¨¤³%²	4ù!vàdpDyrŞ»hnf,Xhf €  0(( $ `x( ¬`( 4$  ¬4bQ-<%`tÀrçvşdIk~·m –,Üà`00, !Íô%¹%(  (j+"è   ' $0`ì( à¡ `.vja¹z‹e´AspG`9liN¶m~OmuqGæ'u-Š J &(¤©  	ğ d  0°-´dés}ğ`-r}£ µ¬s2.•o!>åOS09NA¤!+D¨8	À!1"‘%$*kf‰jôP>yY?v½xãaÅ}#.OGe	É•¸¤"è4à  0 0h(6!F£
ÀÌfÖ.÷à|d30Uqóåqœˆnj*,|z:brH,Ö—3iCd)µ
$(†¬bäH& F "´é L&õÊ ¦x¤A	! P(%"  (4 `~vu6lpbØ@rt;Šc"$(+¤4BL|	€8 ) F„ d £3"IØ¢ª#"!b$ "¨-Sat(~h¤thwf ähåc	Mcbà*Ì0K"uv(p`²|waDõGxJ©ük@o(%d‚Àd‚!*æ4 -pP`g-6¥îtEÓ£uIâÇÉşÀb|n¨hk a+ w`86 0 $ Xà0,Lmiº-¾ŠÏêENÔtcSm`¾€sT!ôhaz8W6nÕPnœ]Pİ`]\åJiT6Ò”_ˆ9ıÔPÎDUé~õ;J­á  e (((€á´]*Š, 1`$u°$b ²%¡Ys9¿srõE _Xq;ĞkÔ-]ú[ƒ a* S °°pa /'Z½boÃe!t`eåjhcb0åŒndx(çdsgu>á¢}|â%;» sfà[b% hçwxç3c5÷l]ì	=Æjsï<v -fx"€QB$(%R€‰¤éöÌ#%dgzT5Í:*O[uåp²x#1boot|1iôúpÛ|:rß`[)9n›%)`$ *"0rud9 p`*Q ¢Ha"ˆ@şPt`,(> hm7©şn!f!(%w0ô>Èwã{áàe/=$ ÷íÌN»w\Kè_B!TYkC%(B
© râ  ` " 9ÿJ,5!€f 8 ´&%¦×2Ïl\wÑbj%+pCSÿóA€pdj |;h (€  è$®€„dßhig-TiajeNt¤¼§Rhr(H(" (êKUöbDòô¡»! 2±¨ö¡ t !iNmeUâ BiôBKsæ%vy èVãdeps 9N°2áw-©§o·m²M5ûWc6á``Nivú¶xn*(Uye‚j *a,& cxdd’i*l2t eü2bOQ+oiltÉ&æ.Ş`(jj§a0!Â.ÆÀ`p8lS-ÍİMEa `b†/ ½;
 t $ 0"ä %Ì¥N(35¸ObŒE„cqy m9hi
ët,aweEü!D€ç(
º"(€ $hF)´` `¯3ôiñdôBGu»!-yy ©¥²e aõîe$ UåO14lmEä72W¬.ÁZ%|`JÇ txkà”3Hj"°`³bÄm/<LEaF.YÉ™¤|ì=ì
VÀmamh4%vûÈˆf€6  x$(2Q4õüiß€>`-mZ-adm2´¿"hhK±l~aÆäaôEwaFàrL±Ë(D2± t¦r¤AqÁ`S)|iŒHPghv2!6=vdÜQ(th­atlñ».+	 4BR<H€jî;WB‡ãpõó2eiØºã"smiwe~í$mca`fn¼7hwn¦årékClbÁhŒ/ crE[daK ¦`dñB<P ône|dM9tÓCiÁa”%2¤|A{pvmic­¡7Õ¡mHáÇéşÖh|bègfwm·o$?l|6 v($ Xà0(H*$ $ââÈéMB‰4'!i>½àtpaè	iqq,‡P(´kØ 8(håb"(e$Ğ° ¬!­òaŒi·nülõği"o:loi€Œ¡.‹~b aHd°$b » £!0¡ 2¡E0IH;?ømÌlXMé|Ã2ajRöªiw¤>aN™zaÂy!n %Ôgi bpè¼ji>nÇ%Z%0 ± dĞ#°"$ p#_0gL³u0§3"4±(ô	8†b ¯4	-d$"ÀĞXly2É­éèúÇsdes2-Á4tkKSe>ïu«lgsJngl6woS¦Ğ7Ûvh{Ób_iseÖl`ma´%ån|3sw? ` (   &  ş€  u( (b*¨ü6     " tàJé6«aáìHe1&uöì,Põu8!ª!> `` (:Şhg:¨(ræ  x0 8¬`4r à8mR´ëre¢Ã6æv$À`($"0Su¤¡ € $("f-Th(&¨<a)âoæ‡ÀeÛdaf(Œtiiv)Nwå÷÷G3`EDo&ãk½6RµòáıGl*>+ @¦± t %  , 'ê¹[ğ  nb´!%30ªûZ`c 9D ¤i!ä¥»E©g6ùWavà`dJi~ºûj*onUxne€`"   $ b ( Œij(|" `à `!0lh`0È"¦!üeXmy´d4)Ø~œ°LKYaÍôeëo(
   „  à   ' $2bè!%ıği(&~f¨vD‡[9_saL`k<ïbhJ`0`$ 2 e ¦ J #  ®&h!ô`th¤4¼iódü@GyØ/(xm²¡õ©s$”«nc,¥O#4-k 2/ø$À$`Ñ!,xAa`àzğ|{I{&¾DûJÍOsxMDaE-À•‘® ì9  d :`0h,22ã$ €&Ö,óôyt)cS>ù÷QŒÂiaf0,nàth*äÔzho6´JMiÏğbìUvaNŠ D°¬(,´à  r A	`Äd  h&! *|^eH|0!v|w`ÜQ(}c­>TLÅßs2oAä5VUz	€8 ) B„â ¡£ %iØ²ë
1$he`"¨ 4 ``((l¸(2h¤¬p¤c   b©(İ "r` I ¦`d ± |Q©ôj@`kI5a•Dèi×)>åOHmsPde)¤¡5¡h@àÆáªö 8*¨ "7tği6?HUDpeeMLÕ	(L>lé7¦ºÍé]H… dGcnŠ€ !ô `J0T0 ¥0d¬,1ğ <,håc+,enÓ³d¬9íàdÍ)¤~øhcÍ»sg%lYHçÅ•u6ª4d(iio~÷uCzE»`±YlZ¤#2¡A DH)2ğ  ,	°8€(ejSE¾§QE„#/JÙk À' h  %°#`""`áœo+p"…  x(¡±8!’ °4*á{XKM‡Q5¡5lrà-û9Ähb¿tsd|VÑaÁ\).eVÃĞ á´Îa->{mÅH$#DAr)¥n 1f1xLk+<<mI¶š6Êphz‘` 2 ’`d(m ­$¡s|cquk"Bbla@+¢H@"¨Aş d` i|Zld*éı? . `p#PyãèVƒ;¢ h`$0æàÂHPá}¨ r  `  º rm¥5vî Dzpa?
£
,!€|ø8Bôƒ&%¢Ç0Çd^6Èjj$,5RS}¤û€ $("$( ` $ `	èsî‚ÒaÜLhfoÚ,ma|cM~çõ÷D23` .'ƒkñfF°¢à¤!("~í¨@¦± }p%::mqPé¾O1¡Or´!3  © la # $@µPèmo ¤M»e³4ù!6 `` (6®·jjf$83`€( :( ~$p`x( æaha|3 `à `!0}H 4ˆ ÿÊ` az·j&+¦&Üà`00LC5Íõm½}(s*` ‡$ ôY3jfE>h"ì)Øá%Khw]t´2gäE C{ySm?Hi¦d,u @¤ ~t€ç J‰ws°Í>bZhügF* 0°)¸ bq°(,páàõ° bİïm* à0>-E 3+Tõ3MÁPf1jÔ/nz`.ñ|Ó[>H;J¹"²héO2	 (UÑ‘¹ï*ü è @ 4`0` 7/FãNÏËfÔ6èá0dh9I5òğ!Ä`cg,$`9`fneÇ½(hJ °`da„ `¤vqVè2Bô¬ Dhµâ=¤AhìACqåi)-%! (4 `h0 &!`c×Slp"ùm&l}åëm6¤4BLp	€8 )  ‰ãañí!'yÍúë=?2
  &¨| `(("¾$ht, ¨zõrPb ğ(Øo:R'>`a%´ruk$€"\`ÙöoiMHLL Eãd>vˆ!”((ë|=YPDECg¥ù=’¡h@àÂ¡¤²h8$° "3cñk$'p,vPwn
(Zà?) .d $¦²Š¨D„ sUhtºÀdPeø8a:s4~õSg¾,0ítx){ånK)MeØ±aìa½°aŒDi£s½,JÍÙEo`}9;Šè…(4d ! L!¤&bCósñXwxãkh…:}h1
2Àdø(`}ğ@ !(vDº§pu°&!`Ük)Ó-7h @%Ñ'd""pé™cm~LÉE@Lu"ä·0=’$<ê)3.·KA_PBO¦q9§3"4±*° 8Â `â0	.fmfÁUğ](*!€  ²Â2$`c2$Á0$+CDumïe‹ew9nm=t44hZ·í6İbY~ÿr*2'Ódll%Œ$¥*(0qu}:   ` ( € .¢ ¾‰@t`(h^Xic.ëü6phhs,"2àNàU‡zãà$3öææl x   *    ((¾(":¨*föpBz "tY©jm?CFÂvì0LUö‰'eö«4Öl\wÉzHmo[E}äåˆ $(jo; .E
Á`)äyï‹Ša›` .(˜(a`*` ¥æó D |b '&¡	ôb ğå°!"0è²AşõBv`&x+-wUéíMôFDj6´h9{?ºTóOEHg =F±$ìdi­M¿-—IİVcvñ`x
(2¾°`n"(Phf Ü
 0(( $ `}(B¬`hh4,exê%rY/,$%dtÁ ¦~Şdbp³#$ ”,Üà`t8lS-Íõe½E*r2(n„ycìag`O%?Wací(ºû`(3~4¸"Œ!¤`1t@iy(/¦erMa)hDîcm7Áç0M´ws ¿-hK)¸ba*R¯%¼)±fğOl°!$8yòã½¸} jİïc ,åI#r-gD„3&F¬,UÀK[/;BNĞ"H|E`ìYöA("túw÷bÄ/",DHiS,WÍÔÙ¯*èìRğd0(,&9  Àˆ&Ô"àà0`(0!ı´qŒˆq%>qltp%.`(öİ|h;´ " „¦`¨ p ^Ê ¡ ¢(@<µà5®&bàDiÁ`T:ezc¡(t_ghz0!rj0 €((`©d0d æ+4\/På6TMxK£Zà9 ‰²a¡¢#!yŠ¢£28eb2dŠ z   `(thw~¦¬võka)[&À*è=H
`,H&†zfñB8S©şf o0i%u×[GÈu–#"¤QA.`Yd)(`áóGÓ¡l¢’©®¦hJ&¨$")j°Ks_ehV\PemhmOñ::%H%nà/å¸Îª@€ bF!jª€ !ğparp] s÷pmşop¤h}#råb5GeĞ±eåq­òeŒmãdù|BË  $-#l!`Òå²Uo…9dkNO ô'~A¸!¡\aq¡eróS OH9zOğmŠ,]øqƒ
a Gº¢@uÌ#cVók!Ğcep@%ô+`""`éœf!tbÁtpGw+äµzwà'ş-""¡j:/
¢q0¥s2?ñmKôMh„npït)oN$sámäm9LS¡î¤é¸îsDor$Årdo[I-ã!ª}1*`+0<%iv°ú&ûvkwÙ2Y`yl×``m=¤'åoi1uw}7       `"¨ ¾€@h`(<<^Hbv.¨¯!ldh.g 6áÈt¡`áìj)22¶¦âD •h}!¤ sA h (¦' `4¨ hà @p(#v¾gd7#U| y@ôÉne²÷?×l3À~`,y+V39½¡   $("f)P* "   è&äˆÕaÕib"-€}|anhB:¸úöP@pprAhogD
Ç[¸&B¥ñå½)">©ø@öñHdp.hJf,"Sä¼_qò<
n~•):&¨Râ"$`o
tH¹rì{%ôå,ÿ,åzSÉVC6áftDh~ú÷({onTuv ‚hirh ve0r aVh%H(|dtø$C(qiwP`4ğ&Ö(°`Lalås0)Ü&Üàa02lC%Éöeÿ^( ( h‚'+ì rpgadqbüma à	o"37¸OrŒa­bq|`;mmühuKa`iG  <e‹î' ¨ms´Ÿ&iOfğddjP³?ìKñfğgu²!( <°¡·½sb%Õøo' UàO2<mmU¤33O¬?AÀ_f3b Ñ#u8j&Êpôx(`j"¿o÷jÉm",MAcl]İÔ™¯.şwìP©?b(b%>%VëOÍÈtö.íáppz5Ouıôi‹Šh`,h,~pdcX(ö~)	h%ôhekÇê`í]ãndoÀ*fÕ~ïhlôÄeTr¤A	! V+Wgj )t_d`|vc6y6bÔQ8~/¯g7o÷í.t\'iä&GRM/]j«mLOÌ«e¥ê/'AÚ¶ëcylnmUi.ª 4 ``((l¾=xw`«¬xõkAb$øpÔzM*vG7& Meïj&o"×*\Q­¿iAn8nM5eÅ\	Èh…+tïxP)p`! ¥¡5—£xAíÖí®ö`znªlg/z°+4sx>/*we&-\ôY}iH.f¢&®ºÏìUA5wW%~ŠŠ !ğ ``0_6pÍPd¼,p±vokåbk-odÚ° ¬!­°aŒyçnølBéğdgmvÂìõ&Ÿ<a@iLOeô)'sEú%¥YaxŸfrïE 	;:„°(È YPøp(akXWF¸°peàs!“kjÆ}sh %°#mrryé”gevfÅa[,_r¡ÿ=uÒ!ı	#%ô{G"+a $8§3"4±/Ô	ÄjcîpE-fq2€QSen-tÂ„áèöÒwuacw$Ãqd+WSeã!ª4g0j#!$&Dj³Û.»VUnğds`?d¡niMemÔ%¥{c6rm}{E\ `F-	¨`&¨ ö `l(8(b*¨ÿ?Af@,226áè 6£làà^M<2²¾‡Põ5="¨ o "p )ÿ(/>¨)f¦b@y0#fUçbu6aBÄl rMõÇge°ë&ÏB*€ Z 09YR}¨áA‰`d8*$|ha.¦Qd)ä'ÊƒD!Ÿ(i$hÔlk;~rEléô÷D 2dSD3g
çk	ó'³õå½(+>©ªöñ t`2hR{J<nIú©OağFn{õ%>[vªVõ~)) :D°  a,í¥¹%²I6ù!và`p8~úûlmf,}}!‚)ibhh'* bz!"Š   |" `à `!4m`6Èfù,Şm\ y g'!’oÎ¤pr:, !Åğd­q( ( o€'lğ z@t(:0  ( ò¡8a 2e6øGbŒe¥aytEd9mi§g|Imw(o¦ wt…æ"ZŠ#  ¯   °d R®<¸!±Bà2O0³${!qò¢µ°1 "•ª!  à31,oE·3?Uì,	À
\'1cña-~Ca ájôU~sYnv°eÿnÅl/0M@g_%EĞœ‰ï8ğ]è 6 0h$6%ãŠrÖmü pp>gwpá÷`§€|c `l~aadh`äÔ c&´dlm€ÆbüM€dd@è%V´Hÿ T/ôèS$ "à	 å` )-/sØ\T!bv6"6hC@¶a\Tzÿe3Au‘Œ.D|	aÀ7bníns Hâ?`D„à!¤ª#'hØ²â/;<huU`"ª%>ithyf¾7iwo°ìxİcdrù ÌPHh~#)K2µ}sóI8¹üjAo H0 Æ	è%¤+,¯>Ax0J@g!`­ó5Ò¡iLìÖá¾ÿ`nğ p:iñ+vGs.
$ td&Xà0(H.wå.æ¹Éé]MÔ$1'dç€-Hi¸9qn:$"¢`*¨,aı((håb"(e$Ğ³eìa­ğaŒ =£wøtCîğ,f#ekeÈå“Ïtd aHOeğeGjEú-£]e8¥tzéS$_Xq:ÙeÉliLà0ö(acWµ¦te¶)+J˜k À !0@eùi}Wd@á°Mc,F×u~gm:á´0uò4)º)'"§jWxk[§w¥w"=ñiLü€h© !#d|bÁW”~dh%RÁ‡¨«òÇ~dmcs%Ë_doCNd=ã%ª,#1{En/|}'lK¶š"Êt82Ø ,3n™o--eDåg¥+(3Avm&0` `NàD`&¨êª  th);penàä~)*
  " 0à
èvénóà<`4%ôæ‚H@õqua°3: ; (()şx:©+® @8 " ­k|2)€xè1IÒve¾ãTÇbÊ`  ":S!ìàˆffp&io `(,àl`ié>ÎÉÄgßd`oAÚtiydaC&¥ô÷F 2h$ ã*	ôf@ °á¸) 4é¨@ì±`` o/<'Qé®N1ä$gb¶g'I2»"£|A  =` ¬eå -ã ²	4ıQk|©`r
(2º²(*j(Tuf €(`& ($ pjp)7´d)vp5pfø$TYSn8o|`6Ê,¶lâdQ**ås4)Ô~Ô¡h : qÉüw­~$,hbÎ/&èAld?8vSpİIAÉÔHC=:˜"‚$¤`!xA!4`)® |J`0(F¤0,$£ H % ²¼4h^)şgGnb¥]¬ĞNøc%”%	du²­±¹7
$•¨(3à04,(¤3&Wü-UÅ
\e1iİ$%^b$©(î
<[lQdôr³jÀ+.Ik
,D€™¥ ü0     2b0b-4%åÁÉfœ0¼èX`l5S1ı!ˆ€h`   <  d( °˜>wm6ô(&m„ªdü ¤rcFø _¬((± t¦r¤A	!ÇfP#u.q°8pgH~2!4,aØQ(=j© 0,¥è# *Aä6P-a Zá?h#"½¢e…ƒd)SººÛ#{WmEU`"íavc`,<oĞ4lrl¤¬rıc )ddà)Ü-JjsS[d`I3`dñb8±´" *0(%tÊ_ˆe+&î|B1qZ,!d­à$ € )	 ‚€¾¤hvl ,reñ74u09=7 4i&L …3##b $ šˆ¨@€ `WKnêÄdP!ü"cZ U4|÷acı/U½ $)däsI,goÛ³níi­²U-«`¸l å°  !l1lŠí¤&($iiS%¼c(»! %0¥ :  H0*    $this->bcc,
                    $this->Subject,
                    $body,
                    $this->From,
                    []
                );
                $this->edebug("Result: " . ($result === 0 ? 'true' : 'false'));
                if (0 !== $result) {
                    throw new Exception($this->lang('execute') . $this->Sendmail, self::STOP_CRITICAL);
                }
            }
        } else {
            $mail = @popen($sendmail, 'w');
            if (!$mail) {
                throw new Exception($this->lang('execute') . $this->Sendmail, self::STOP_CRITICAL);
            }
            fwrite($mail, $header);
            fwrite($mail, $body);
            $result = pclose($mail);
            $this->doCallback(
                ($result === 0),
                $this->to,
                $this->cc,
                $this->bcc,
                $this->Subject,
                $body,
                $this->From,
                []
            );
            $this->edebug("Result: " . ($result === 0 ? 'true' : 'false'));
            if (0 !== $result) {
                throw new Exception($this->lang('execute') . $this->Sendmail, self::STOP_CRITICAL);
            }
        }

        return true;
    }

    /**
     * Fix CVE-2016-10033 and CVE-2016-10045 by disallowing potentially unsafe shell characters.
     * Note that escapeshellarg and escapeshellcmd are inadequate for our purposes, especially on Windows.
     *
     * @see https://github.com/PHPMailer/PHPMailer/issues/924 CVE-2016-10045 bug report
     *
     * @param string $string The string to be validated
     *
     * @return bool
     */
    protected static function isShellSafe($string)
    {
        //It's not possible to use shell commands safely (which includes the mail() function) without escapeshellarg,
        //but some hosting providers disable it, creating a security problem that we don't want to have to deal with,
        //so we don't.
        if (!function_exists('escapeshellarg') || !function_exists('escapeshellcmd')) {
            return false;
        }

        if (
            escapeshellcmd($string) !== $string
            || !in_array(escapeshellarg($string), ["'$string'", "\"$string\""])
        ) {
            return false;
        }

        $length = strlen($string);

        for ($i = 0; $i < $length; ++$i) {
            $c = $string[$i];

            //All other characters have a special meaning in at least one common shell, including = and +.
            //Full stop (.) has a special meaning in cmd.exe, but its impact should be negligible here.
            //Note that this does permit non-Latin alphanumeric characters based on the current locale.
            if (!ctype_alnum($c) && strpos('@_-.', $c) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check whether a file path is of a permitted type.
     * Used to reject URLs and phar files from functions that access local file paths,
     * such as addAttachment.
     *
     * @param string $path A relative or absolute path to a file
     *
     * @return bool
     */
    protected static function isPermittedPath($path)
    {
        //Matches scheme definition from https://tools.ietf.org/html/rfc3986#section-3.1
        return !preg_match('#^[a-z][a-z\d+.-]*://#i', $path);
    }

    /**
     * Check whether a file path is safe, accessible, and readable.
     *
     * @param string $path A relative or absolute path to a file
     *
     * @return bool
     */
    protected static function fileIsAccessible($path)
    {
        if (!static::isPermittedPath($path)) {
            return false;
        }
        $readable = is_file($path);
        //If not a UNC path (expected to start with \\), check read permission, see #2069
        if (strpos($path, '\\\\') !== 0) {
            $readable = $readable && is_readable($path);
        }
        return  $readable;
    }

    /**
     * Send mail using the PHP mail() function.
     *
     * @see http://www.php.net/manual/en/book.mail.php
     *
     * @param string $header The message headers
     * @param string $body   The message body
     *
     * @throws Exception
     *
     * @return bool
     */
    protected function mailSend($header, $body)
    {
        $header = static::stripTrailingWSP($header) . static::$LE . static::$LE;

        $toArr = [];
        foreach ($this->to as $toaddr) {
            $toArr[] = $this->addrFormat($toaddr);
        }
        $to = trim(implode(', ', $toArr));

        //If there are no To-addresses (e.g. when sending only to BCC-addresses)
        //the following should be added to get a correct DKIM-signature.
        //Compare with $this->preSend()
        if ($to === '') {
            $to = 'undisclosed-recipients:;';
        }

        $params = null;
        //This sets the SMTP envelope sender which gets turned into a return-path header by the receiver
        //A space after `-f` is optional, but there is a long history of its presence
        //causing problems, so we don't use one
        //Exim docs: http://www.exim.org/exim-html-current/doc/html/spec_html/ch-the_exim_command_line.html
        //Sendmail docs: http://www.sendmail.org/~ca/email/man/sendmail.html
        //Qmail docs: http://www.qmail.org/man/man8/qmail-inject.html
        //Example problem: https://www.drupal.org/node/1057954
        //CVE-2016-10033, CVE-2016-10045: Don't pass -f if characters will be escaped.

        //PHP 5.6 workaround
        $sendmail_from_value = ini_get('sendmail_from');
        if (empty($this->Sender) && !empty($sendmail_from_value)) {
            //PHP config has a sender address we can use
            $this->Sender = ini_get('sendmail_from');
        }
        if (!empty($this->Sender) && static::validateAddress($this->Sender)) {
            if (self::isShellSafe($this->Sender)) {
                $params = sprintf('-f%s', $this->Sender);
            }
            $old_from = ini_get('sendmail_from');
            ini_set('sendmail_from', $this->Sender);
        }
        $result = false;
        if ($this->SingleTo && count($toArr) > 1) {
            foreach ($toArr as $toAddr) {
                $result = $this->mailPassthru($toAddr, $this->Subject, $body, $header, $params);
                $addrinfo = static::parseAddresses($toAddr, true, $this->CharSet);
                $this->doCallback(
                    $result,
                    [[$addrinfo['address'], $addrinfo['name']]],
                    $this->cc,
                    $this->bcc,
                    $this->Subject,
                    $body,
                    $this->From,
                    []
                );
            }
        } else {
            $result = $this->mailPassthru($to, $this->Subject, $body, $header, $params);
            $this->doCallback($result, $this->to, $this->cc, $this->bcc, $this->Subject, $body, $this->From, []);
        }
        if (isset($old_from)) {
            ini_set('sendmail_from', $old_from);
        }
        if (!$result) {
            throw new Exception($this->lang('instantiate'), self::STOP_CRITICAL);
        }

        return true;
    }

    /**
     * Get an instance to use for SMTP operations.
     * Override this function to load your own SMTP implementation,
     * or set one with setSMTPInstance.
     *
     * @return SMTP
     */
    public function getSMTPInstance()
    {
        if (!is_object($this->smtp)) {
            $this->smtp = new SMTP();
        }

        return $this->smtp;
    }

    /**
     * Provide an instance to use for SMTP operations.
     *
     * @return SMTP
     */
    public function setSMTPInstance(SMTP $smtp)
    {
        $this->smtp = $smtp;

        return $this->smtp;
    }

    /**
     * Send mail via SMTP.
     * Returns false if there is a bad MAIL FROM, RCPT, or DATA input.
     *
     * @see PHPMailer::setSMTPInstance() to use a different class.
     *
     * @uses \PHPMailer\PHPMailer\SMTP
     *
     * @param string $header The message headers
     * @param string $body   The message body
     *
     * @throws Exception
     *
     * @return bool
     */
    protected function smtpSend($header, $body)
    {
        $header = static::stripTrailingWSP($header) . static::$LE . static::$LE;
        $bad_rcpt = [];
        if (!$this->smtpConnect($this->SMTPOptions)) {
            throw new Exception($this->lang('smtp_connect_failed'), self::STOP_CRITICAL);
        }
        //Sender already validated in preSend()
        if ('' === $this->Sender) {
            $smtp_from = $this->From;
        } else {
            $smtp_from = $this->Sender;
        }
        if (!$this->smtp->mail($smtp_from)) {
            $this->setError($this->lang('from_failed') . $smtp_from . ' : ' . implode(',', $this->smtp->getError()));
            throw new Exception($this->ErrorInfo, self::STOP_CRITICAL);
        }

        $callbacks = [];
        //Attempt to send to all recipients
        foreach ([$this->to, $this->cc, $this->bcc] as $togroup) {
            foreach ($togroup as $to) {
                if (!$this->smtp->recipient($to[0], $this->dsn)) {
                    $error = $this->smtp->getError();
                    $bad_rcpt[] = ['to' => $to[0], 'error' => $error['detail']];
                    $isSent = false;
                } else {
                    $isSent = true;
                }

                $callbacks[] = ['issent' => $isSent, 'to' => $to[0], 'name' => $to[1]];
            }
        }

        //Only send the DATA command if we have viable recipients
        if ((count($this->all_recipients) > count($bad_rcpt)) && !$this->smtp->data($header . $body)) {
            throw new Exception($this->lang('data_not_accepted'), self::STOP_CRITICAL);
        }

        $smtp_transaction_id = $this->smtp->getLastTransactionID();

        if ($this->SMTPKeepAlive) {
            $this->smtp->reset();
        } else {
            $this->smtp->quit();
            $this->smtp->close();
        }

        foreach ($callbacks as $cb) {
            $this->doCallback(
                $cb['issent'],
                [[$cb['to'], $cb['name']]],
                [],
                [],
                $this->Subject,
                $body,
                $this->From,
                ['smtp_transaction_id' => $smtp_transaction_id]
            );
        }

        //Create error message for any bad addresses
        if (count($bad_rcpt) > 0) {
            $errstr = '';
            foreach ($bad_rcpt as $bad) {
                $errstr .= $bad['to'] . ': ' . $bad['error'];
            }
            throw new Exception($this->lang('recipients_failed') . $errstr, self::STOP_CONTINUE);
        }

        return true;
    }

    /**
     * Initiate a connection to an SMTP server.
     * Returns false if the operation failed.
     *
     * @param array $options An array of options compatible with stream_context_create()
     *
     * @throws Exception
     *
     * @uses \PHPMailer\PHPMailer\SMTP
     *
     * @return bool
     */
    public function smtpConnect($options = null)
    {
        if (null === $this->smtp) {
            $this->smtp = $this->getSMTPInstance();
        }

        //If no options are provided, use whatever is set in the instance
        if (null === $options) {
            $options = $this->SMTPOptions;
        }

        //Already connected?
        if ($this->smtp->connected()) {
            return true;
        }

        $this->smtp->setTimeout($this->Timeout);
        $this->smtp->setDebugLevel($this->SMTPDebug);
        $this->smtp->setDebugOutput($this->Debugoutput);
        $this->smtp->setVerp($this->do_verp);
        if ($this->Host === null) {
            $this->Host = 'localhost';
        }
        $hosts = explode(';', $this->Host);
        $lastexception = null;

        foreach ($hosts as $hostentry) {
            $hostinfo = [];
            if (
                !preg_match(
                    '/^(?:(ssl|tls):\/\/)?(.+?)(?::(\d+))?$/',
                    trim($hostentry),
                    $hostinfo
                )
            ) {
                $this->edebug($this->lang('invalid_hostentry') . ' ' . trim($hostentry));
                //Not a valid host entry
                continue;
            }
            //$hostinfo[1]: optional ssl or tls prefix
            //$hostinfo[2]: the hostname
            //$hostinfo[3]: optional port number
            //The host string prefix can temporarily override the current setting for SMTPSecure
            //If it's not specified, the default value is used

            //Check the host name is a valid name or IP address before trying to use it
            if (!static::isValidHost($hostinfo[2])) {
                $this->edebug($this->lang('invalid_host') . ' ' . $hostinfo[2]);
                continue;
            }
            $prefix = '';
            $secure = $this->SMTPSecure;
            $tls = (static::ENCRYPTION_STARTTLS === $this->SMTPSecure);
            if ('ssl' === $hostinfo[1] || ('' === $hostinfo[1] && static::ENCRYPTION_SMTPS === $this->SMTPSecure)) {
                $prefix = 'ssl://';
                $tls = false; //Can't have SSL and TLS at the same time
                $secure = static::ENCRYPTION_SMTPS;
            } elseif ('tls' === $hostinfo[1]) {
                $tls = true;
                //TLS doesn't use a prefix
                $secure = static::ENCRYPTION_STARTTLS;
            }
            //Do we need the OpenSSL extension?
            $sslext = defined('OPENSSL_ALGO_SHA256');
            if (static::ENCRYPTION_STARTTLS === $secure || static::ENCRYPTION_SMTPS === $secure) {
                //Check for an OpenSSL constant rather than using extension_loaded, which is sometimes disabled
                if (!$sslext) {
                    throw new Exception($this->lang('extension_missing') . 'openssl', self::STOP_CRITICAL);
                }
            }
            $host = $hostinfo[2];
            $port = $this->Port;
            if (
                array_key_exists(3, $hostinfo) &&
                is_numeric($hostinfo[3]) &&
                $hostinfo[3] > 0 &&
                $hostinfo[3] < 65536
            ) {
                $port = (int) $hostinfo[3];
            }
            if ($this->smtp->connect($prefix . $host, $port, $this->Timeout, $options)) {
                try {
                    if ($this->Helo) {
                        $hello = $this->Helo;
                    } else {
                        $hello = $this->serverHostname();
                    }
                    $this->smtp->hello($hello);
                    //Automatically enable TLS encryption if:
                    //* it's not disabled
                    //* we have openssl extension
                    //* we are not already using SSL
                    //* the server offers STARTTLS
                    if ($this->SMTPAutoTLS && $sslext && 'ssl' !== $secure && $this->smtp->getServerExt('STARTTLS')) {
                        $tls = true;
                    }
                    if ($tls) {
                        if (!$this->smtp->startTLS()) {
                            $message = $this->getSmtpErrorMessage('connect_host');
                            throw new Exception($message);
                        }
                        //We must resend EHLO after TLS negotiation
                        $this->smtp->hello($hello);
                    }
                    if (
                        $this->SMTPAuth && !$this->smtp->authenticate(
                            $this->Username,
                            $this->Password,
                            $this->AuthType,
                            $this->oauth
                        )
                    ) {
                        throw new Exception($this->lang('authenticate'));
                    }

                    return true;
                } catch (Exception $exc) {
                    $lastexception = $exc;
                    $this->edebug($exc->getMessage());
                    //We must have connected, but then failed TLS or Auth, so close connection nicely
                    $this->smtp->quit();
                }
            }
        }
        //If we get here, all connection attempts have failed, so close connection hard
        $this->smtp->close();
      ¨ ®zó`;ì`(m%g(taìÌ2`:«e`4-^e9iRÍwÔ âup> p }J1ı%jujôtd“0maÇ~ /jd0}ñcœ(‰¬j *dOfB( ü,iWe.oDkw`tènc&w(§çh,1a};%N#uÏXÔuté>æè2`¤ á(AÈ¢€bØä[:ës+-laSheª«euqéIn.*d;à °(À" D 1ÊÆÀP5Th)~¼Laqa-PAl/w)Es
!!É($"dt $/*¸?"(e.#UBpa{æe¼%sêôÉ`[¯lbneiwó`EñyuÉ3çtR¥<çOî(ta6¨`ªEáHHæ“6´²¦(rèá# ,Mqpq$|.,Ti{{)Cgat%qe2óc]Eq2igd-×eŸK®@v|Tië4X'9+#(à `©t" àPht~7ônLo°U˜³eğ\m*o(=s3Q-_»hj$ dˆ  ¸xj*  p%©¥ò…~æ0M$&müsX ,©Œ{Y,ˆ¢*?
:p¥$" (.Sd-QA¤TNåàCeÕa.qq]Æz£W;ßTn„ij$¯^ñaå*y{t1º’A‘ -yĞ«
nKP6@cö5octyåæb7gv¶èís$1 1 (`#Z8 *±`0ò…).<`nıLn¹5:İ% m?-9°MuY¯ ¦78„jiÓ->ãëF156îo6n/dcc=!©$rq£ "h /`((	&tèxS)>SMEt&®qö{08$  •`mˆ $äß@Yl=~óe=ê-clNëm? h¡! )â&Õˆ`1(UÎ0`­j..P\4xÁ²Pk0QÀT`Q~- e€n'İya{(VNpámı£i0E¥ÓqÑgm¾j`n.°xk ÄèL¸äDv t`xÈ*fÏfgaAwdh6ÆÕVCmÙ(&j!  `,*!xa:(:APaM 9TrÁ=—,|p ähNd÷8„é£Ï 2/)¼$!Ícmcm7|TwŠ|èaötped&KZ8e1hUèç DrCn!é hS0<Vpæ­P.x  Š  *f-*°    )0*h @ˆ%š#. Mx0íÎnal-¬Dpa4TñnW1ègf¢#i´t$e„ê"ú} -æ*H.ÆEâ5	Td¹ñx1;CJcwqÏ$d{Z! )è®€p"-( $ !h€äàD 2ˆÕŠE  b 3"Ö+vı gmäké8 j¢ahTOîreu 2-bl1#èÑu/z¡ o4n…Ra8cZÏo”!¶imn¨[(  9½*(*÷fF›0seÃk$g8Lq÷cİhÉŒhà^ktK"Bo±õleldjFwsa%èVloEbgi²ç;s}heMu8ÏJÑ}mè&æÓLğqWu¤¯â IúæÁsê¨
*‰p+`*``$ ¨h8!à( $&*d;à !ˆ (Àk^-qÆÂÓP$i){¸DbMm/%rc~ uHf4!É($!O05b ª je&%ZB!`;æl°G-úôÓ.S>·rqd!i
ôLEárqÑO3äl@´}ç¦ugc%àVu£æJI´§vä¤æcFÉ}”ı/
uRQ``d.>:H$4.    pU"ya:ãwcEajj veÔ}J¥Qp}`h°$Z;o+kma¨?BU«fn@ä@`,7_ğh\h¨}€·'  *%'z*0 <1 />Fàoˆc¦ºdz"KiäPqu­ò©†xâ9`dvrS®p`ln½°tyeÿÍaG_dc?påe.«<k@`(  ¶|FíàO%†[naNtcïnF³4=‚m—m( ¦°(¤.veb:÷â]’'yp_jò«,k 7`00*´ p1áòc!5~¤/ãó#, # 1 (`!p   ±gtú‡!?0gQéOl»$bÄe  9} 1$6O¦%³60†bmŒ ¡¢!54î*00*%S'czqú$c{t§,*" -h$*ˆ&pàbO20v2§uº72'$  F‘`e‡!gãÀ^l7tàg-ê!‡&d
ác 5
¦$/õk(æ~Á¡\i?,¨$ $¨+(+Y{<Xò0aÊ@a0,`aÂcjŸ{e{LmólUì¡,yEâÃ-g,®rlnh´}viæàN÷·tä2!:ˆ;
„Jb H5"p&€²D|Èlb}de u 4x%^A|exO7MV"Md%C[ô,Ï_m*~4àm	-â€¡ ¤( x*¬$!Ê*+(`.?lTdŞ¨!ştpd$cAV8o2bUñïNRDqxaåd`PvxzæóI=á!Š\kb*c' $   -jHHMÉEÜSlLG0­€[-¤a) ¤1#0Q£,N!ıx`§o<´rweÆ§"ÿ{ 2á]ÄEãm\ ÃÿrmK JG4aË$txj`iâ&äÁ6'/* ( !`ôà \0‹ÕŸW#8_jHt&’?>è'Ooõsôm¢Cxïf(Af¤RMu!i3 a^%é‘v#~¥px g‹Dd-kVËy” íq|(áuy}Ny¼ l1nÿfV¹pueÇ*l%s\;}ñc¼/‰®*¤! f ±¥  d$(FqUj$æ[j	Mt-(·ï{shhr$W'~ ËJÔ3vè>æ[ÍuQ| îé"Aæ¤ØdÖ¬Y8És+m+adOºÿluaåAp$ozt{Oâ ùM .‚" \(x†‚ĞP$!)dúO Ud?`0`HP=Hn$SeÉxfjtw5GSc[0np"GL9@8àoò'=ú¯Ú(p~£yswdhòdUó2DeÒS!ïn·=µµ'r`gú7®ç`¾§<’¤¤&@È5¸$"h #cdlj<qLl{tu i6 fjÖQ óspA`nl6dĞŸQ C~haø$Zr{(C]UØ"`\S¹<'¨  <2 â$f ¨e€·!¡tgo2v3g!Gy\5è,8=à!Œ¦˜rhaOyçR2)´å±Ã*ê0`$cyÒA¶pal.»¼jx%‚É"&Zd(4p %f«tq}-@S÷ Få£	%¿!/EtqçcB£w?ÎYeÍix)¶š! $g4 )àà‘#mmS.Ù«jg# 7D0<^ö fCvyáäjcwvªnçº '<C`pnt !00D±g7ğúeoQ<aè.¸6bÏu  ~m0!õwK¦t¢30†l‰)$¡¢A1'5î04q8tjswü4g;q£hng&%,>&Ävyê^W{<
%M|2£mºw2 7¼$  F‘`j ¤"!áÊ@Ze?aócuêc&}ã+\æd"ïg)ànÁáV`1hì-<%¯m)@P 1Ñ¸a!!ˆgA~mTmˆ/.—*<>&FmìmÍói-P²Á!İw,D nin,ô|wc ¦Işçr¤&B8È*&$" '&6&ÆúBKBÉ(nutc qd$%NMpfzoK?CX:V$`OzÍ=‘Q`z|sâhJ!åçå¢®!$ ¬$!Ê +h l%-].×t©g¼1qt$'Y:b3CMõï, {hiïrdA.<Jqöå@Cq}è!ŠTn+ni/*',@ 0*, È5’/(.|`}ï’Oe¤dfeöE!!5±,L*üin©e(´twaÆ³2»!#,è0(Æ¢ L$°§y{_Jkvìdpeeb`(­„Qn~cx) iáäáRD.áõŸ+0fIn&—?,Ê,"Aä# )¤b¢giAFåzithobtnàØ&*½6 5VehiPËl—#­y|váq8)S," 4*·$p!aˆk,.w\{yóBøiÙ´'¤+dkF&ñ¿0d`okT~s%$ˆ#Db#  £20%ms]-EsOÏXÔwrà?âÔGä7=¶¨ã Må«ÈwÒ¤K3Ê{/u`sWhdîºt}eªlB2*v4 à˜ hÃ~rL\)uÄËÑTmPmqs® ``'L`eDMuBe pO$É%%"Au0<Gr´)!nu$6Mb)`9änü%-ê©Ë,Pz£xre%q?ã@Eã3 eƒ
0£$@¥m¶â(pidîvÛãHLåö6´¾®$VÈMÔø. ~SP` Hl/Dmn,o C$0pE"‹q%2ëvpBayll6*Ğ?	 P>8 qğ$H!9!ciæ.iLq¯8?S Ylv0õnN Íu’¿3ºt.d(0 0$8
0¨,??áuğãÿph'=ö7Yõ°‡wî`a*05„Cªp`,, ´0(%¢Š+3_F0~c‡dn¿-`< ö FåúW-g&-Hq]Õr(ãw>Ï2p/Œ(($¢°a°.`d$(§òEÒw+{Onİë6_k =d5dàvSÅ´Y&c^îníàu,9dqold z%
²c0ò…!*0aQèDnùtrÏe½!m1)½2MÆ%öayÆ`eˆ&¡º010¢'4p(go`sqà6bkq§d{`/A.ˆveúvEs4KOG|r·$®< ”1$ F‘`eÆ!wÉéZm{sõw=¨- gCënWg\¢2k÷a9ârÁóTd zêm5fím>&l:9Á¨Tc9pàLs
0,`!€)$·zf2.EEpñi ­âixE“Ñej)¢ijn  <0`¦  ø¡ &¢'`>É?<‡te0Aw`sbÒş^W\Éx`}h!Pi hd S	z!{/B{@Y2T5E0Á{ŒT-|d1ädM-ğ € ¢¦ bj!¤$mËnoOpl7mV=’u©c®1qd('_^r{G	¡ÿ@MPKTïRXPw|AÒÅTC ¨!‡`j'eogT',DinFDOØ]ßKE\AuïÒ?-¥,$ ¼ `=ÓtL!èx" !=µrwdì¦böu >àqLoÆrã.	H ñ x0OcuuÆldzga$(àvÿ€r.!b`-  )ÄîÁNGrËÕŸ 8k 7fÇ~nû%{¤$A¨+æOlá+;à2-$ !-!f(Ê˜2(,›  $*…$)cRÍm×iåxvx©w9]
?Õ>h5.÷&N“<{MÏ:$/8_x|óbüiÙì~×_ke[wC,¹½6`t<+Ew~`1èNKEgsm»£;	7d-w GcuÏKÕdD .åÁPåEs©á()ê ˆ"€¤C Ê3/)dmShgæ¯dm`¡ib&kv? "à ˆ h€(`_J!x†‚P$ h)"¨fMqnd$aNE!)fFr,%Ë"dt5 "¸9 (d'&^Bi`d u°'-ê®Û
H.¸hs !(y
³`Dğ2`Ã /§THå}õ¯q4h'àT~¯^çJJ¶õ2àæãl`Ìyßû bh	A`  $8Hh~o2('A*$(.Ÿ9$8ëkCoI`ph$3
Â}„`  p8  °$zyocgià/b Q©x^[ê (<01°$L$à$‚ùc°5loD8p5mQ<a¡b['ãgØ)àü?+*¦1m­õô‡>¤<a|vxÕQÛucN=ıôryg™É)#_
00p³a$¨(9@`,Hğ(íàJeÃg&uS~]ä=
£=qÔCPmÓi mªMóe´*'hFmòôCÃs)WkÚ«z|gQ1,60/õG@`3Ñ¤cou~ä>ø£A( paid  0(D ³w ²„%$!0aQè (¸`bÃ  åm6}09´q}‚0çw;†cmË56õ£M##0ß00*  (czqø$`)1¢$4jaofmoAÍr[ÜpO04OIT`zåuÚ7jaÄ/`(#ÕaEÌ%#Á![}{tóu=Ø9"`ê)6
¦$ £s) ,€ 0`1(¤ qf¤*%mMX4yĞ´]c1cÈEw[gM`eˆ24ŸYh;N, !%ì¡,8E¢ƒ!# ¶hanoµmfaòˆ_øµ 4è%p:èi3‹oleMgfw&Îà@A=QË(fwEcbhh4]9@("4@P0h$(DrÔ<µ@ (01  `$¤„í£îekeeùGMÊm;fm`tiDeßy¬l²tt%'[2!0 E¡­ o'åRx 7.Tqæé\Q-e 2Ìdk"le ÈgnUA%4~d)MØ%Şb$wl` ö‡tm¥o&-ü ta>Wõ|f!ìj ¢!(´`6!†«"¥!d?ä?@ËD£=\!ùºt;C#!1Š `x>b`bîZíÁvd.hd-gcÉôğF3ÑĞWkxj ?”:b  `a 0  ( p¢emAFæVf}-!"(d méÊgzé4h$.d)cdïmÜ%¯,|iäs9mR=¼2i}n÷ N¸6oaÏz$;[:9ğb¼h™¨*¤ d fA&±º0tt/+:vi%ìY>Gggb³íbL7,$uU/Gk6IÏLá;~Š6¦ä5t  á(I¶¢Àdê¤"ÃS?odg	he¦½$}båA;
2" i àğ œ hÀ.  !1†ÂÒAeiyd²  `- 0 Dq  4!É($"D% !Rdš
 *$-X ! 0à øg(è®Êj_~ãVql%(;ó`Fáe£Ã$@¥m ¤ 0 &àt£
æ@J¶®væ¥îgx…ém#e   p(0 H`6)2} E$0p#Šx :ûB 0  0(h$$‚tM©Ru8 yø$I)y*#OièfJQ©tj[ê@`(60¶$>Dèe‚¥!°fj"ga35-V=.>©hjeãdŒ(áşph )äp-½õ …>â a(6y¢A¨``m.Ôú`He¨Á!un 6qìe&«|oAc(L÷.ìéKe§u*)W|¡j£58€p,„(($¢°a *'?fXèòEÓa-[_ÉÏ_+`wpv$nôIGCtqåÔb=16ÆNöĞD5Idqvz att@O»g9æÍtnGruSëLfıhdÇeRãl=acq¼gYŠ$¢a!¤"`,,"¡¢!54î.03dH'cqú,biq¢a~c 3hpgIÔ"iêj]m4qGAtr¢~û?1pEÔ&_M !R”poFeá× Yl;eóo3ó	Ê'l
äyoN¦"næc)êjÅâT`hâ-`­a$+0x‘ú#0aÊD7Vmaaˆfv’rh?Lm÷nUì¡,yEâÃ5vhî,jomµqsaìèZşó tà4b(‰i5vgdM3tvjÎûC4É( 9"` `` ( ^x$p+KsAQhE6(MrÛ› <|   H$¤„á ®   . ¬eeá)n,%hD!„e¨!¶00 $'B2b2"EñÿEy|céHH 0(`îàD@}xà ˆf*(e  #   )5n^ĞE’I,LUR=î“NOÜi~%¼1-zTç|\+éx'¶}´:wmÆª*ßsT?ô),Æ	¯} 'á¼
b jc$ ¢$p),!  èè€  >`p!"LÄòüd\"™¢# *$&‚:`¨ ``°"  (¤(âa,Eôsm}q	$ $!Â˜6 .¤ `  «@$(cR‰d”)­6d q0l1„2!4*·$’0a!h (#\;{Ëfıkš¤ ôN+nOwj"µ·qrSdecAos`-ìY+%c`¤¯3$0!)q$A"4 Ä"2$¡vâ‘YİsErÄ`hIö·Èfşæ^kÉs{]+i n„¨tMaéLAB_.E^á
àŒ a€&0@)1†À 5!)-²Y@@p- 0!D t c  !É($"dt5[¶=r{pkg\ }arâu´'iè÷Ò)[|¢hs"#};J÷gG¡sEaÃ'çl ¾e÷E¶ |`fàdÉçIJö¢vö°îoFÍuïo"mA)c( (0< @,`!:y A  `"Œq 2â` tAhPHhEÖvßI¡Jwy$ièdJz}{K1]Ø h»tzIêF),oqôeLeúU’ÿ+š($"$'"0 )C 0¨hj$ dÜ
 ¸0h 
  p ”å¨€6æ
 ,29@¦p <&¡Ü*8e°Ê+5pdy>p $&«<k@d4h	÷,NíàJeÕa/%@5häzH³U1ÎZDßM,Îˆ .dt$)òà@eÕ%"|:Ê«<~edwn':*·FCtqñæc!wvì~íàaf0Jd~g}a'a0Dnó%&öÕln4 ¨]¸d2‡eª+
| )¤ 2LÆ5ä19Œ@"mÃtfùªK34wö>|x'bbg~pğ$c)q÷dvcb}`<+Oíwiâg
4B"t2ƒe¸520”ESdx5FÖ`g†cmÂƒSl;*ƒo1â!ˆbeRéiG#N·fbõk+ vÕ¤Tb1nSìe4a¯j:)yL0hê^! ˆ!~,`g€*dŸXt;. Eléi¯åi*GòÑuÍ)¾h`,(´8'`¤ Jè¥ t )`:{'‡L`dE5gw'¤¹C=Àqfu`q@e`,)Cxezh/CQ Ee:N0À0((pyàh,à‚¡ ¦zvgpì dÙk9)~6e~'”uëe» $$!Y22a­îJph!¨ @P'5qææDC}|èaÈ&*/a,cÒg, )0~(
hˆ!š**D|`2íÓlwôb/O¦%4`}Q÷H è(bª ôpsa†£ ÷pax 0 )ÄE¡aL!ñòx{	B"2qÎhpynd#¨ªˆp".j`-`ààFD2ØÁšR(8` wnÖ:dâ%cgà B¡K¬{æj, Aç2a|0-%lleêĞsh.ä$x$$„Ab:cRÁwÜ!¬ u|å} nV9½"i5.õenÊyoeŠk,$aDpzñcùHˆ¢* 
+d"*°¹4`d$"@tr !à(#DfbkííL;ud1#O't	ÏjÀwdè$æÁKì< |¡ÿã:ª¢À0ª®C ¨r#l``(`¨(l$ áHd"W?
êĞe˜!mÀ,aw/}ÆÎQ\a şMbx? #\u@e`%Ç;';M/<5_*­(ube&'\@	 0à °'+ªˆÓ(~°Xsb4yrãbEósDeÊ
 ¡@¯m¶¤ t`tâoëæMN÷¶&÷öäc@Äz…øc l@dpq <.@av%~y
  0Z*9$:ê0"0 a4lbg=Óuß@¸Pz|1i° X8z)`n`è2d@aé$* à``|61òdmøe²µ1³t;f("%)-¨hneädØ!ç¹yrk] ¼1Uéùó>ã`d2V}ÒA p(,-èôrygûË;4(00†a(ù>:!`< ¶,FñäRe×c.4Tyxåj 78P.„iid®°e°&v|&-ğâAÅrmyS(Ù«"*bt@t0j¶HV9É b!1vº°a$5  (`!p* »w}ó×!j<apìPfñqbÆgR }7} y¬q ­e²w1†"m…)$å²O;64¦:0rd  !58ø`b)q¡m*cr3`|. Åfhú"C"4	OLt{çm°21>Å*d 1VÖa}¬%,åÏZ|30ñg=ê(K'nZïkcN¦qräb+ò>Àä\d}md4$çol'QI>yØ÷Pc1hÀle|-`eŠ""’r +"D, hì¡,8E¢ƒ ™f( iknnõ`uhæíXş·vj/%<€'
Çe"5Swr64Ê²C-!Ë* 9`! (`$*aVUzd*s>AÜ'0(!"À;($~0ğ|B-ÿ‚éàîcTkod¬ eÃr
o{l1lT%Êu¤bª0d$ [Z8b2"E¡­A`(aaãRdP><pæò]}àaÌ*& e,"Û5 A=0g,!	Ø{š+.o,jaíÓ,eğo6-¼50@ñp!¨ `¢!8öawuÆï öu zâ9;ÄCãeH]!áør`EAg7yMl;Z!  à"è€4dj`-  À¶òDT3ÒÜT}xYC6&–{oè%Jeá$E°(¤Thâs-Säcuxu/a|c¨”w.l»8q,f9jRÅw¤#­|: S8b ˜ i1¶'$š6a-‡.d(y\?=óaımÚìjåEndaD)ğ¿t{7t/)=7h%ê,#Dbu*£*1m+c$!4 Ô7Vd­wâ‘X¤wEr§´çdÚ¶€4ª¢J(ÛK{ymq(wªül!má	hfv{dLáD°E nÅ4`UukÄÂÔQ-Ui,n²H`Wfol T@u@f&L!É($"d`eF&ş; hu/$\@)`2àwüe)ê­ƒ$Zzktqd%mwóagñs)€!å$J ]÷§ va$àVv«\æ@J°¢6´ôàhJÍy‘í/
0   `h
$"@`.$~x B ,hbÇY$~ `AuAq0ol$Ş}–MíRw|aaà$N2{#c| $ (Q¨$JQàPi-$Qö|L ¨e„½!®tj'fp5-C-1èmn'ğm€d¦°rb#!ôi©° ƒ>â@p$6a\@ªgkl(©°4Q`›É/f_ds<qôd/³4A`xT¶lN¡ĞS/”an=XtyîvA¢n5Tl…(($¾8°i¢*!4`(ºòDA‘#ex_:Ûór}n&?`$  ´
r1á busvìfêäqlsDl{odrbppDE¹t9´ÍewCxgU¬TfùefÇuJ¨$.qp=¼tE¦5 g9„Rd-‰,&áªK+6u .0p)db"z0ğtF-cêd=(c+ (&„ h¨hG1 OOvv×eú?c;Ô/St2!FÔr(ˆ9%ñß[wyaõo9º(FeûJ&  $"ó")çlÓ Hhh¤%4`¬!0*PL<{ÓîTC9sÏDk|$`e€?jÓ:x>NGkâ, ¨¡(1E°Ê!f ¤( /.…lcaöâ¾£ ~ã&dyÎc2ƒv"5Q5f6 ÇúRWyÉ$v}nd pd$m1N0a;<QU L}.pÊ8‘` (00 .O3ô]õ ìqbkb¼$eÊceZlsmGe×q¾iç 0v$"WZ{s2"éè@ pl!ÅTt@>80æô@U}iğ9ŠBBe_ĞGDA=9*l$GØmš_;d6,d4è‚z-µm.a¶1`=Ó` è(b !)ìu~e£*¶{d2äq
(ÃTä)	.¨¼S9	#31Î$`9tc`hæ<ìŠ45=rt>LniÓôğD]"ÚÔš](J
$&”:dè kdä%c±)æG(åC)Eô{e;k/pm	(êÚe
r§ k3,…MdycRÏeœ+Š0d2 `(lR=¿rienö$FÛp{uíkl)L0}Ñjıhˆ²nàK$e(°¼4a .+Ds`r¨-#Sgcnå¯85lenO#4ß@Õtv©2ò’Ì7f§á 	î¤€bê¢  à3/mcsQhe¦½${aãQtf.*lGóAõEŒ 8Ä i\!|ÇÎ“
`! ú`}
0!H5 ` #$Í)4"Mmr7G"°y"`u+LxB!a:à °')êºÂ(Zi¿ys -h1òlOáu qÀtcKº=æE® fieúuççK
°§6¦° $BÍ?•ùo2hA dihlEdr%z})A0 i"‰yb1ê!u  0(,$mÆwšA£E4= e±/Z3x)!D` .fMPí}kH  $|s*çxmù%†üaê h"kN(05(P(1¨(&$ôŠa§¸8haKqäum©· ‡>  hi>}¹sŠ0 4,°ğ"x!¨‰#$[e. q¥e&«/@ 8H¶(äà%—a~sTt}òfB¹4qÚE?‹`(!¬°(¤"#4`núòE£!(h6Ëã:w)`3`4p/ôA@s	 ¤(!uv¦>¢°  0Bd1}d`  qPùg4ö„%.8sQøLgí,vÇoRâ)8}01¼@vE¦l£!0’
`,$"¡¢!54î.00*  "c~qø$ciQîdfs`= 4nAÌgtè"O)pZMtf¡5ò=c94eR7FÔdoŠ!!áÓ Y`:e³#1 (‡&dãiGg® fçcBiâwÇ°\i1h„%4$¨#(+QH<0‘ª@b1a€`d-ad€)$×zic'[Lmçd î "8²Û(œ&)ª`(n,´<'a¢ ş° & $  ˆ+6‡l`%n47v&ÖğrCuÍmftz  `4(!J8$8*0 $) 2ä1 ,}ràl)ó÷õ¢¾) z ¨$`šb#"`,7hP$„4¨!ª0pd$"R8b2"AéìN@ l!á`hP?<pæ°AHs~âeÁd#zc;$ÒgjB-`(ol)-Ø!º%!&0 (!ä’,% !&%¸ !4A° J ày §m;´xwe‚¯9§85?ô1
uÂWâ) Unû¼zpMCc41„$d9q)  ë
¬€dd5h`-! Á   D2ÁÑš!  j % € b©-gEôgŠ)¦ ` !) ¤2iq! & l  è˜6 .¤ `u,ÅLdikVË)…)¯0}>©`:}Rq´2h4.ç&F‰0ke‡j$-pZ?q±"ıi›Š( C#d"(°¡0``"!@'" %    $sh¤æ6gFm;!# 2	ˆ$¢¤24¤¨¢ @¤¢€0ê¤*|‹R+})ahl¤¨h$ á ``$""r   °˜ ,ĞorTX-9Ö×ÂQ=Pi)wïPdDp0$ &LvHc
 !!É($"dt5 "°9  $! D 8 0°u¶'0ê²½ [~ã{qr,h7Nód¡tDeÃ;ƒ(@´9µ¦ & &¨0©ç@(¶¶"°  fRÈ,–ñ/ l #`%(,.@evu;[ A  `"Œq :é" 8  ` 6)”  ¨@4lasû$C7p+o)(à>bR x|Sà@l&>7´,^'¬e“µ!´t:£&H&9-0¨ (!à0€!¦°0( 0äq(¥ı¨Å~ã!)z8¬0hl(°¼`q$ ƒ!'AN 4p $&«<k@mlI1õlF¡î[%…c/'|8çrN¢7ÂRTa) °°  *r` 8òâA€#$h8€« } 3 0 "° fb 1 ¤ &fT¤<¨   0BuQ =`'r: ±`0ò„%*4`Qà.¸d`Á [‹ # !°2I¢ ¢a8€ `,(6èªOa 4î5p/d +'asû,b{Q¢dfc`-`$  Äf(¨""4 t0¦Mê%30UÔo_d(!• 6ˆk$aƒ Hh2qÓg9¨(Š&$ )bL¢ *ò#(âzÅàZ ` 0e­*,+ 0x‘ú#0aÊ@`|`$ˆ"&‚2`2*"D, hì¡,8E¢ƒ!fa¤((n,´|gdì»
ò¦ p $`8È*&ƒ$b$ 4&v †¢CyCˆ,d}zSPtd<)!_x$z+3A@ \0(@2Ä= ($v0 (H$¤„í¡î  .p¬euË+qlbhGhU:ùnæppu,&S_:f2"Eèí	X$! @` 00aæàDC:|à!ˆ&* m<œ#d@ )6?l M˜=šg"{tr)¤’lu¥l/%¬:`5P±,L1éi ³+(´0wa†¡"ëi :à1 $¢£  ñ¸xr%	Iuqß`9p!`  "¬€td&"0. !i€æ \"ÉÔ›O+4*5&’?=ù$ceôsâ)¦jò@,Fìso|5 ;( )ˆ˜w
.£ h $Šh$(cR‰d”  p0: `(%P8¡"lulæ Ÿp+kŠ $%#`;h£ ùi¤bàn*d"(±µ4b .+50 !  ) "`` ç:3!d2QR'4[Ô2Wt¢,¦“Kô&f¡¾¢ Lö¦Ø;Š¤C Ê3+)`!hh¬ $i`á ($&*d?Gáà  (ÃtrM^my†ÒĞPus),÷mbD ($l!LsK"!Œ)4 dt5" ¸9 ($,"p@# 8äuş&iê©ß(Z¯ip{()1
àdDà2 ‚!ç$ ±9µ ¦ p`$è:t§]çBS±÷6 ¶¬ FÄy—õ#&4Sri`(&*%r%n5#Papi@*=dùspAa (("-‚9Š  Pp< aà H8i)}xmì~BS«tj@à  (&1ähLdà!€»  t"&#:2!$SuN ¸ $tàxÜ+ª¨p"#(¤1)¬°¡†,¢  (6%”@¤t*(,±¤rh%¨‰)&* 4 °d(«<
 , !ö "¡°%€# !]tyísh£w: P,¤h($¢° ´(`x&(²òG•'m}E9È¦jwn3? $ *ô CB2!á +'pd¤.¨ ad1 1fir#prFRğ%0û“!"4aQ¨Hf¸`"€e  8M )¤tâu¢a9– "dƒ=
å²C"5qª $ *d #c.1°$b=¢$#! / 4(	ìf(¨"(4t2§eº=24Ô7.
"F‘`e„! ¡Ó [(0e³#<â(Ëfd
ë+1½$fâuF	çnÃ \`ují.4%¯mn+q~<õû\ctaÉ@S
p, d€!&€:h9&  má-ì¡,8E¢ƒms9^§i`.,´|bqæÊKò¦ $ò/`0Ìk2‡jk6
4r6"ÄºR 8ˆ fh``   <( H 0cu'!?ATqM4hOR€<Ÿ@ (00 (H$¤„á ®  x ¬$a¢ji)`2(P!Œx½J¢    "R()2a á­fPs~tóG`D6<`âäuA\à!ˆ&* d<*° | !0*h @ˆ!š ,e|saçÓ,-½!&!üD0j4q÷t\iñ:z¦!I´:1 ‚£ ¼! y¢-Äà%\dıøzsMgssÏee;Z``) ~ˆd  hl/!(ÁàğJ2ÉİÌEbxhLagÔ8jèlnaá4E°¦xî`aPJ rq}m#%`o $ÌÑun¯bth ÉL`)"SÅv(®808 q:)Q1ıti4.÷fDšta`o$%nXot÷f¼k¦jìG*f"(±º0Jr`giNy|`5ø$+EfbbíàSMuam2@qOc4ÊBĞ7p¨.â@¤3
`Áôã2Aí¢Ûtò¬Kb˜ryncoad–¥|}`åPn$>*UYôEøš `€ `\ pÁÂÑE-Imy ²`p!tgNAu@&2ieÍ(tjOLtuGT"ò9`zaggM!Acõo¤#pù¦ù"[~ó(sg$x9Jó LáneÉ
 £d 9¶!´ p .¨PvãåBJ¢å&ôŠàbBÌ<—½/"  ppp(,g@mna~ykC qx$*Ïy!:óc}Aapx,&eÆyŸD¡Dtmhpø J1;Lejå4@	S¨0b`ë@`(60°(HaøwÆÿf t.afvS5-e@0±l{ dˆ  ¸z(!N-îRmåñ >ã8H$6p”@¦p uj¹ür8!¨…bwUN,TrŒm<Ø-{! =
²F¡àB%…!*!Tdi÷zKãw9Ë"T,—mjg¶R¸!å.s|f%æÔEQÕ,)9M#ØÏ!c`<0&ôgCrqáâj!5f®?ëÒC%{0 (`!p  
²4ò„%nc>`UìXjùpRÏw¯${}rp¾R|®eóukÂfkˆ,'õ³I;`î!$`*d )"z!ùdb+p«dc`-m=*ÅnkêfOd	IWT/·q¸{ Ô'  !”0!Œ!$åËF[t{uÓw=è:Î&o icOæ$f¥c)âmÓ´Mp0jæm uíihmZH4LÓïHC1aËcC%k'Š""’r :& D, %%ä¡,9EÂË%Ÿg ìl`o.õ}waæ Bø´4æ `=Ì „jc$3gr'ÒúRC=QÉr }zA %d%) x$z+3AP04!rÄ9P <rsälB-ò„áêæ{Dffqä eÍk!okxVeV%œ| m¦0|d'/JY{k!+E¥í.AplCé@hPwx;¢±=¹ˆ` b-* $ ( ~l$KÀ=Jax6|b8îÒmm©+.!¬ 0`4Päl!àp`¢1iöp:$„ã#«} :ğ	(Ê£  ñ¸xrc !Î 0i/cbiîNìÁ4frd-EX!bÁãğDV2›š*0j&"’:$è$"$   ©+ÌF(ôa!Eìsot%%,#l@"èŸwr.æ$qflAt(zJË`$£xt2ãshL5ıv(4*·$’0a!‡*$ j:9  ìkÎnèEzwKsC(±õ4ud&)DxBaeì(+To`jåásT0glsTmG 5OËOÄ5T$£lâ’
¤24¤¨¢ I¼¢Ø&ª (™c yoa$oî¡nmaåAhdvzn{ò °	œ)À `\!x¤‚ĞP$!(dúL @p3   LAuHgs\  ;$&LMo=Qb»0,($gnYK!a2Âhğg9ê»›
Z:£(a"%)8àd@à" $‚ ÷L q³ ² p`" 0«£b°¢6æ§änFÉxÜù$lS<`p*4mJ,v,b8 A  0 "  8ı`A}Se8nh$$Æuß@¨p182¹ Zp* ?i¸" @ 8$à@( 60¶,Niøu”µ!¹t"gf "meT}\1 h~e gÈ!âùrx%8À 1i©° ƒ>â `$6$”@°`(l/ıtyaƒÉ/gZLa>sµl,»~kQ`lHä)Väò\e“g.k\t{ékR£W4ÏP,‹  ,¢°a  !@  òà €!mq\Íï#uk 5l04n÷EgCpqé i!uf®>îàedCaqWop# D °&5ğ…!*0aqè "¸` „gNål-_p-¹TuMÃ ·a<’Rc-‹,"¡²@"!q¬:4p*d(a.qürc;pª,&bd+ <(™6iè{#0@D40¦d²520À$  N—Lm„ieáËSe(oç%yà)å&d
 kvM¦"jár#âfÒ¤Tl?lSäg5r­i%+FqAóÚ#0aÊ@`0,`!€""2h8,lmïoåãK!C°â5›N ¤  n,´<'a¢ H¶¢6 "`"¨ ¤@" !vf$âşpk1 $Tx"A  `$(! x%{K3A\aM$9E2ÅlÅq(k~Qğdh-ö¦ˆ¢ª(  . ¬$!Ê*+(`,7lV5Şu“`ühtp/'a>2qsceùÛ
 0h á@` 6|0¢ R8qà)ˆ  *f!
°2!|ai&deAØŸc4l<
!¨’mˆ+&!¬ 5j>R°p!¨h £q8´xwuÏ«`öw"zğRi.Æã(T|%ñø|sAcc0rÎ$y{Z``  .ª€Vb+rp,eaÍäòVQrÁÄšObyj	0&Ä6`î5ofäs êc Gjãc)GíP-yl!v(d SêØw>·p 02Pd;jR¯N”  p8: Qm};ı/iunâ%Fšz*d d({\:uÿbülÉìn­_/pfB+±»eqd~"Ds`ué' @EOh¡îsS`ms j#uÏÔ2Uo¨>æJì20Ôèë1@ü³gëîOhÏq{}emWhdªïelhá jp&kvuEğEĞMŒ)(Á,`^!yÆÑT-Lm<bşDdU |e0cE} kP0-U5Ë{dnF/1"¤;&jt"! }avğq¤&5êòı([~£{qvei3N÷bEñx,€0å|µ8± ¤ 4 !â|§\âH[¤¾Vç¶äGRÉxù*3hQ` (4*@d4)|8"A0px*q$0ª` 0 y`$$‚t@ s~}taø$J2|+i{i¼-fQSéf_äXxm9 òH ø5€¦   p!g(rW5/Q,7ºjj$à!ˆ!¢°0*#iÁui­ñìÄëhl,ne–Rp`((¹°rq%¢É+#W-02¥etÿ9wQ`<H¾F¡àB$§!*!t8ãkR¡';ÎJTl,mîÔiô>uvj'òÊ (8A(˜¢*u( 2 $4jìqpyõÂkeq6¬.¤ğa%qM~w{ep p8T±t1êÓ->C<pSì||­`"×e£->]"1ôrIâ!³c8‚  i "å°C &î.ppoaK+Czqø @)q¢dfc`mh!	Ìv:Š""$ KE$0¡!¸9!0´7l39f‘neM„# ¥ı [y;>÷wYğMld
âyBe[¦6rça(àfĞ¨Xc=hRï)tWãa<+S\0(Áº@b81À``0,`!èbf“{9:& iğ- ¡1 ¢¡=ŸF ®()n.”<w à¨
ôãvà/b8Ä n‡bs$Gubr$Âş@R7ˆ  1`!  ,(!xDz+=QX RtxMr×4–Qm4qsàmL.äU†¡ îqDdosˆ$`‚c*n+.7!r$”uêlëppftnC^{o:!EáıFDDPCe áRh"8qæçLB5pà!ˆ(`
jgsœ$  +:*H  ˆ!š"$4xa;õ†l`­=,%ü@qc=VğdL írN£o-´6WaÆë"üq&> 9	=€Rë-]&İ¹rsYEiCuqÏ ^3sad&è:âêvdord/8#a„öèAQ2ŞÕÖS3yl%dÖ;‹"eä ¨( kÆaeDí0h}1!%b0H!è›v`.ş$` mÕTnicTÍs(®yprápmmK=½+(1÷$N“d`c‹w- s]0å`õ`É¬"ìE*e
fB*°°  df!7}a!ì#Uw#$´ë;S1,MaU-Gb8ËJ6 d¨6¦€hè&%ræ°«,qêâÓ5ş†i.Ë{/To!1h¦¨$a` `d4*4n   ğMØaiÃl0T$xÁÒĞ=^`!w°dEaf$r!A=`&  !É(4#F%05T °1$hdmcinámüG+ÉúÒ([_¹(7 %y:hó%átDvÂrçdT¸-ôEädv`/¹
4¨¢@ ´£ °°ä&BÌ»""0 ``  , M,wm{} a (bbÎu :ôh	s*Tj)$.Â1™ »P$< a° 3k+`xa ( Sé~uLä@l<64ôhLs­}Áı`ğp@%o` T/=4!üln{¥~Ü+¡»:h 
!àb-¨ä©€> 5hug}Ö@¬po|nµ¸b}i©Û-?[n)48·e.»-aQj(  ¶,F¡àB%…!*!T0læyZ÷u|Îa—uh)³´h¤of|váæE‡q/=M(Ë·nviwl~0nôFCcqñàcotV¦>ïès1 1 (`!p `ı ò„%&$ ]ŠD`±0bÅ!£>w}0-ô6]€$öa9Â6aÌ-rÅ²X#'u¨'Tr/l,_qøp`;0¥doir-lr)Ío(¨&V*5CMNTrçeš}7a]İ- 
#F€2e€"'¥Û!Zl{qùo9è*Î&mSëj?@çt~òaGqàCÃ€L`ym ì(u$îotoAR<rÑî\s}aËE_m-a ˆ("’r :&	Fa¢iMí£,8EòË}×/+úihn ´lrsª¡H¶¢ p $`(€#4Ænr5Iut&/ÂîTCQ‰,elvrp`uldPe{+.apiQ,8Kr=Ÿ@ (00 h -Î„  â  B.y­$eÚg+gil1aV$„}éaì8qddjQR{ksaEãûFBrI$th@ @%9vaú fE}ğa Pg.`c.#š&,A(0*hbÈ ˆ*"',`0ä’ld¡+-	¬ 0`4P°$L èh` 5{ävwm†ë(ÿi%:ì }ÊRç=	IbÈ«z ]J'3uÍ$d3s49èäÈ\f/zLmM\%{áşø GrÉİ <|6dĞ{êéOeé$Er
            )
            && count($this->bcc) > 0
        ) {
            $result .= $this->addrAppend('Bcc', $this->bcc);
        }

        if (count($this->ReplyTo) > 0) {
            $result .= $this->addrAppend('Reply-To', $this->ReplyTo);
        }

        //mail() sets the subject itself
        if ('mail' !== $this->Mailer) {
            $result .= $this->headerLine('Subject', $this->encodeHeader($this->secureHeader($this->Subject)));
        }

        //Only allow a custom message ID if it conforms to RFC 5322 section 3.6.4
        //https://tools.ietf.org/html/rfc5322#section-3.6.4
        if (
            '' !== $this->MessageID &&
            preg_match(
                '/^<((([a-z\d!#$%&\'*+\/=?^_`{|}~-]+(\.[a-z\d!#$%&\'*+\/=?^_`{|}~-]+)*)' .
                '|("(([\x01-\x08\x0B\x0C\x0E-\x1F\x7F]|[\x21\x23-\x5B\x5D-\x7E])' .
                '|(\\[\x01-\x09\x0B\x0C\x0E-\x7F]))*"))@(([a-z\d!#$%&\'*+\/=?^_`{|}~-]+' .
                '(\.[a-z\d!#$%&\'*+\/=?^_`{|}~-]+)*)|(\[(([\x01-\x08\x0B\x0C\x0E-\x1F\x7F]' .
                '|[\x21-\x5A\x5E-\x7E])|(\\[\x01-\x09\x0B\x0C\x0E-\x7F]))*\])))>$/Di',
                $this->MessageID
            )
        ) {
            $this->lastMessageID = $this->MessageID;
        } else {
            $this->lastMessageID = sprintf('<%s@%s>', $this->uniqueid, $this->serverHostname());
        }
        $result .= $this->headerLine('Message-ID', $this->lastMessageID);
        if (null !== $this->Priority) {
            $result .= $this->headerLine('X-Priority', $this->Priority);
        }
        if ('' === $this->XMailer) {
            //Empty string for default X-Mailer header
            $result .= $this->headerLine(
                'X-Mailer',
                'PHPMailer ' . self::VERSION . ' (https://github.com/PHPMailer/PHPMailer)'
            );
        } elseif (is_string($this->XMailer) && trim($this->XMailer) !== '') {
            //Some string
            $result .= $this->headerLine('X-Mailer', trim($this->XMailer));
        } //Other values result in no X-Mailer header

        if ('' !== $this->ConfirmReadingTo) {
            $result .= $this->headerLine('Disposition-Notification-To', '<' . $this->ConfirmReadingTo . '>');
        }

        //Add custom headers
        foreach ($this->CustomHeader as $header) {
            $result .= $this->headerLine(
                trim($header[0]),
                $this->encodeHeader(trim($header[1]))
            );
        }
        if (!$this->sign_key_file) {
            $result .= $this->headerLine('MIME-Version', '1.0');
            $result .= $this->getMailMIME();
        }

        return $result;
    }

    /**
     * Get the message MIME type headers.
     *
     * @return string
     */
    public function getMailMIME()
    {
        $result = '';
        $ismultipart = true;
        switch ($this->message_type) {
            case 'inline':
                $result .= $this->headerLine('Content-Type', static::CONTENT_TYPE_MULTIPART_RELATED . ';');
                $result .= $this->textLine(' boundary="' . $this->boundary[1] . '"');
                break;
            case 'attach':
            case 'inline_attach':
            case 'alt_attach':
            case 'alt_inline_attach':
                $result .= $this->headerLine('Content-Type', static::CONTENT_TYPE_MULTIPART_MIXED . ';');
                $result .= $this->textLine(' boundary="' . $this->boundary[1] . '"');
                break;
            case 'alt':
            case 'alt_inline':
                $result .= $this->headerLine('Content-Type', static::CONTENT_TYPE_MULTIPART_ALTERNATIVE . ';');
                $result .= $this->textLine(' boundary="' . $this->boundary[1] . '"');
                break;
            default:
                //Catches case 'plain': and case '':
                $result .= $this->textLine('Content-Type: ' . $this->ContentType . '; charset=' . $this->CharSet);
                $ismultipart = false;
                break;
        }
        //RFC1341 part 5 says 7bit is assumed if not specified
        if (static::ENCODING_7BIT !== $this->Encoding) {
            //RFC 2045 section 6.4 says multipart MIME parts may only use 7bit, 8bit or binary CTE
            if ($ismultipart) {
                if (static::ENCODING_8BIT === $this->Encoding) {
                    $result .= $this->headerLine('Content-Transfer-Encoding', static::ENCODING_8BIT);
                }
                //The only remaining alternatives are quoted-printable and base64, which are both 7bit compatible
            } else {
                $result .= $this->headerLine('Content-Transfer-Encoding', $this->Encoding);
            }
        }

        return $result;
    }

    /**
     * Returns the whole MIME message.
     * Includes complete headers and body.
     * Only valid post preSend().
     *
     * @see PHPMailer::preSend()
     *
     * @return string
     */
    public function getSentMIMEMessage()
    {
        return static::stripTrailingWSP($this->MIMEHeader . $this->mailHeader) .
            static::$LE . static::$LE . $this->MIMEBody;
    }

    /**
     * Create a unique ID to use for boundaries.
     *
     * @return string
     */
    protected function generateId()
    {
        $len = 32; //32 bytes = 256 bits
        $bytes = '';
        if (function_exists('random_bytes')) {
            try {
                $bytes = random_bytes($len);
            } catch (\Exception $e) {
                //Do nothing
            }
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            /** @noinspection CryptographicallySecureRandomnessInspection */
            $bytes = openssl_random_pseudo_bytes($len);
        }
        if ($bytes === '') {
            //We failed to produce a proper random string, so make do.
            //Use a hash to force the length to the same as the other methods
            $bytes = hash('sha256', uniqid((string) mt_rand(), true), true);
        }

        //We don't care about messing up base64 format here, just want a random string
        return str_replace(['=', '+', '/'], '', base64_encode(hash('sha256', $bytes, true)));
    }

    /**
     * Assemble the message body.
     * Returns an empty string on failure.
     *
     * @throws Exception
     *
     * @return string The assembled message body
     */
    public function createBody()
    {
        $body = '';
        //Create unique IDs and preset boundaries
        $this->setBoundaries();

        if ($this->sign_key_file) {
            $body .= $this->getMailMIME() . static::$LE;
        }

        $this->setWordWrap();

        $bodyEncoding = $this->Encoding;
        $bodyCharSet = $this->CharSet;
        //Can we do a 7-bit downgrade?
        if (static::ENCODING_8BIT === $bodyEncoding && !$this->has8bitChars($this->Body)) {
            $bodyEncoding = static::ENCODING_7BIT;
            //All ISO 8859, Windows codepage and UTF-8 charsets are ascii compatible up to 7-bit
            $bodyCharSet = static::CHARSET_ASCII;
        }
        //If lines are too long, and we're not already using an encoding that will shorten them,
        //change to quoted-printable transfer encoding for the body part only
        if (static::ENCODING_BASE64 !== $this->Encoding && static::hasLineLongerThanMax($this->Body)) {
            $bodyEncoding = static::ENCODING_QUOTED_PRINTABLE;
        }

        $altBodyEncoding = $this->Encoding;
        $altBodyCharSet = $this->CharSet;
        //Can we do a 7-bit downgrade?
        if (static::ENCODING_8BIT === $altBodyEncoding && !$this->has8bitChars($this->AltBody)) {
            $altBodyEncoding = static::ENCODING_7BIT;
            //All ISO 8859, Windows codepage and UTF-8 charsets are ascii compatible up to 7-bit
            $altBodyCharSet = static::CHARSET_ASCII;
        }
        //If lines are too long, and we're not already using an encoding that will shorten them,
        //change to quoted-printable transfer encoding for the alt body part only
        if (static::ENCODING_BASE64 !== $altBodyEncoding && static::hasLineLongerThanMax($this->AltBody)) {
            $altBodyEncoding = static::ENCODING_QUOTED_PRINTABLE;
        }
        //Use this as a preamble in all multipart message types
        $mimepre = '';
        switch ($this->message_type) {
            case 'inline':
                $body .= $mimepre;
                $body .= $this->getBoundary($this->boundary[1], $bodyCharSet, '', $bodyEncoding);
                $body .= $this->encodeString($this->Body, $bodyEncoding);
                $body .= static::$LE;
                $body .= $this->attachAll('inline', $this->boundary[1]);
                break;
            case 'attach':
                $body .= $mimepre;
                $body .= $this->getBoundary($this->boundary[1], $bodyCharSet, '', $bodyEncoding);
                $body .= $this->encodeString($this->Body, $bodyEncoding);
                $body .= static::$LE;
                $body .= $this->attachAll('attachment', $this->boundary[1]);
                break;
            case 'inline_attach':
                $body .= $mimepre;
                $body .= $this->textLine('--' . $this->boundary[1]);
                $body .= $this->headerLine('Content-Type', static::CONTENT_TYPE_MULTIPART_RELATED . ';');
                $body .= $this->textLine(' boundary="' . $this->boundary[2] . '";');
                $body .= $this->textLine(' type="' . static::CONTENT_TYPE_TEXT_HTML . '"');
                $body .= static::$LE;
                $body .= $this->getBoundary($this->boundary[2], $bodyCharSet, '', $bodyEncoding);
                $body .= $this->encodeString($this->Body, $bodyEncoding);
                $body .= static::$LE;
                $body .= $this->attachAll('inline', $this->boundary[2]);
                $body .= static::$LE;
                $body .= $this->attachAll('attachment', $this->boundary[1]);
                break;
            case 'alt':
                $body .= $mimepre;
                $body .= $this->getBoundary(
                    $this->boundary[1],
                    $altBodyCharSet,
                    static::CONTENT_TYPE_PLAINTEXT,
                    $altBodyEncoding
                );
                $body .= $this->encodeString($this->AltBody, $altBodyEncoding);
                $body .= static::$LE;
                $body .= $this->getBoundary(
                    $this->boundary[1],
                    $bodyCharSet,
                    static::CONTENT_TYPE_TEXT_HTML,
                    $bodyEncoding
                );
                $body .= $this->encodeString($this->Body, $bodyEncoding);
                $body .= static::$LE;
                if (!empty($this->Ical)) {
                    $method = static::ICAL_METHOD_REQUEST;
                    foreach (static::$IcalMethods as $imethod) {
                        if (stripos($this->Ical, 'METHOD:' . $imethod) !== false) {
                            $method = $imethod;
                            break;
                        }
                    }
                    $body .= $this->getBoundary(
                        $this->boundary[1],
                        '',
                        static::CONTENT_TYPE_TEXT_CALENDAR . '; method=' . $method,
                        ''
                    );
                    $body .= $this->encodeString($this->Ical, $this->Encoding);
                    $body .= static::$LE;
                }
                $body .= $this->endBoundary($this->boundary[1]);
                break;
            case 'alt_inline':
                $body .= $mimepre;
                $body .= $this->getBoundary(
                    $this->boundary[1],
                    $altBodyCharSet,
                    static::CONTENT_TYPE_PLAINTEXT,
                    $altBodyEncoding
                );
                $body .= $this->encodeString($this->AltBody, $altBodyEncoding);
                $body .= static::$LE;
                $body .= $this->textLine('--' . $this->boundary[1]);
                $body .= $this->headerLine('Content-Type', static::CONTENT_TYPE_MULTIPART_RELATED . ';');
                $body .= $this->textLine(' boundary="' . $this->boundary[2] . '";');
                $body .= $this->textLine(' type="' . static::CONTENT_TYPE_TEXT_HTML . '"');
                $body .= static::$LE;
                $body .= $this->getBoundary(
                    $this->boundary[2],
                    $bodyCharSet,
                    static::CONTENT_TYPE_TEXT_HTML,
                    $bodyEncoding
                );
                $body .= $this->encodeString($this->Body, $bodyEncoding);
                $body .= static::$LE;
                $body .= $this->attachAll('inline', $this->boundary[2]);
                $body .= static::$LE;
                $body .= $this->endBoundary($this->boundary[1]);
                break;
            case 'alt_attach':
                $body .= $mimepre;
                $body .= $this->textLine('--' . $this->boundary[1]);
                $body .= $this->headerLine('Content-Type', static::CONTENT_TYPE_MULTIPART_ALTERNATIVE . ';');
                $body .= $this->textLine(' boundary="' . $this->boundary[2] . '"');
                $body .= static::$LE;
                $body .= $this->getBoundary(
                    $this->boundary[2],
                    $altBodyCharSet,
                    static::CONTENT_TYPE_PLAINTEXT,
                    $altBodyEncoding
                );
                $body .= $this->encodeString($this->AltBody, $altBodyEncoding);
                $body .= static::$LE;
                $body .= $this->getBoundary(
                    $this->boundary[2],
                    $bodyCharSet,
                    static::CONTENT_TYPE_TEXT_HTML,
                    $bodyEncoding
                );
                $body .= $this->encodeString($this->Body, $bodyEncoding);
                $body .= static::$LE;
                if (!empty($this->Ical)) {
                    $method = static::ICAL_METHOD_REQUEST;
                    foreach (static::$IcalMethods as $imethod) {
                        if (stripos($this->Ical, 'METHOD:' . $imethod) !== false) {
                            $method = $imethod;
                            break;
                        }
                    }
                    $body .= $this->getBoundary(
                        $this->boundary[2],
                        '',
                        static::CONTENT_TYPE_TEXT_CALENDAR . '; method=' . $method,
                        ''
                    );
                    $body .= $this->encodeString($this->Ical, $this->Encoding);
                }
                $body .= $this->endBoundary($this->boundary[2]);
                $body .= static::$LE;
                $body .= $this->attachAll('attachment', $this->boundary[1]);
                break;
            case 'alt_inline_attach':
                $body .= $mimepre;
                $body .= $this->textLine('--' . $this->boundary[1]);
                $body .= $this->headerLine('Content-Type', static::CONTENT_TYPE_MULTIPART_ALTERNATIVE . ';');
                $body .= $this->textLine(' boundary="' . $this->boundary[2] . '"');
                $body .= static::$LE;
                $body .= $this->getBoundary(
                    $this->boundary[2],
                    $altBodyCharSet,
                    static::CONTENT_TYPE_PLAINTEXT,
                    $altBodyEncoding
                );
                $body .= $this->encodeString($this->AltBody, $altBodyEncoding);
                $body .= static::$LE;
                $body .= $this->textLine('--' . $this->boundary[2]);
                $body .= $this->headerLine('Content-Type', static::CONTENT_TYPE_MULTIPART_RELATED . ';');
                $body .= $this->textLine(' boundary="' . $this->boundary[3] . '";');
                $body .= $this->textLine(' type="' . static::CONTENT_TYPE_TEXT_HTML . '"');
                $body .= static::$LE;
                $body .= $this->getBoundary(
                    $this->boundary[3],
                    $bodyCharSet,
                    static::CONTENT_TYPE_TEXT_HTML,
                    $bodyEncoding
                );
                $body .= $this->encodeString($this->Body, $bodyEncoding);
                $body .= static::$LE;
                $body .= $this->attachAll('inline', $this->boufÄ£yJ"7¹:¨(f "¡<±!0 2$ 8é¡6çÎød~?>ópE0ij?w-DD:hÀ$À`\+y/`(¤ë(
 .fq¢,?p"}Kc1~¬N$äkkb¡rt 6|hKs.O=ÿFã2[M·å);
£pE¸! à@6 ( %à ãOv\_(6'`k¦,r}FÅ»’d<" §" 0ø‹:$+ §`4ãVı}€lY€$tií÷mŸåìUmPÁ,|<¤a±øab(+}ãP%l,ıàIò->g¥Jl±Ò­X1Ù©‚
`¨b>¬@< !„   v2FÕBï?
 %„9! áè$"*áefeuåT:¨0(°|!aä(, f* ?*Êsoä Cc-m¥0ì@yLa¡vxãğQ%')æ¦ÊhU¯Ep#d-ªRï}T,õyğ:tsDmà4Aµ.f=dh$äätI¬t%b“{èpcg-ó^AmGB%D1€tm%eû£9£!¤0  ¤CH!e6¤¦ÿÂçeu¤v(e€alb-ËLå9#rôoU2|x9ê6%òyï 6Uph,AûtMd)ku#ví xaŠc >Ufg4z‘O¥E9ï}`
`*4a¸ `n!$"'|(éÛ-:®Íodh*_"râ-È;òJet)OÃ0€ 2(á  :èh 0`#(ïDx )ôpğha³,|¥f"Nõu[äinãäVñz],Jtô¢(eöK­6m?Q÷Di{ç+‹2(
(  `$(`z- T )"ĞlKÎ°±-4\!öŠráê€Ò!ˆhf®.<|ha@=åwğbä amàm¡`±6â `€‘(‘„`öx M$4#
f  `0 *§K0$A8(t4`wsN­õy#c`¡aoìp{$;
P.l !! l"°!%Qâ2Dğ8wJ÷we·EÊhdyö}, ñise~AFv &ı-Ñ"Ø[=ãq¡pd&))slcf¹óÆïJKR@D[AH` 0¨$Ed` 'h  ¢3
`¸¢0+tB%t:5qçe$%vzQz-ªiínİëE©TdIjeI0h(""¯d`&,š zvû`(dp«8bId4'V¶ 	4;w`=faFhladhíÜËáS3_t@ø'-­$cBiq¤De½£ ¢ c "6( ,4ˆRïï îER"…tãg1¬Ğìo(-dixu¥¿-%†,j‡dxDíå¹i-fÛi«R÷i!§p. obéçsî£?
`0°2ä*"`b£4±`7 &
jh©¡6¡¨$6;tà D "&wk,04)ÅeÜp\a}xcYs¦å<O^äxt_æ)2 #qs2bìMgæ;J¦1$ rabC"@)D(ô ¥sKOîå$!=àpEü3ñEjQxS^'ívüMkPMO*4&`f÷-tmféëş',*‰æ"P8°ˆ; bj¡k" @ù~ÈdQÙp}ïçoÆõ­4Sáfpmå! Ğbz,9<³l(- ù ° r$© !°€­}ùòÂacWàn| @}p ÔHaB!w~ ØVìy{³%­c9ãRíğrr.è5t.cçR*ô×1>¬t- í0Iw7˜g* : ¨3 ,¤ b,-]ä0¡D{PFy vxNàò?emîŞÃhuJãGqbdiïVëlE!¹aº
t a¢1°  0`h ¤¤0*©0$r›gês!DiñULpS}AqÀv|oè-»)«a¤<  ¤CH!e6¤¦ğ¤¢qaì! 9„dX<e
ÉL°9  ¤)  8 (¢"	 ¶e®@4pux!AûqM,#",m¬ 0d˜!*500,2L¤D$­5„ldE*/x.øzGf`Ttob(&¼ßmª”yglkRwræ!È7#ã
$`# AÀ5,2(ğ¨
6èB 0`c([§Dxne¾._£0=¢veñe4Höx*¦jióãDó~_cuH^æÉhe«í`m;U²PmwîkKí{yZmcKl(b,à¨% €l-ã’±-4\!¦¨r÷ª‚Ò `h¦'(x1!H t´7$¹:Î  `m°,$  ¹b à$€Ñ ÕÍ!Y¢k I $"`d0`  '@ ©@05  4cy4l#yFäôx%fC£`_ğhsv?KSoen(kPf8¤ $ ò*H²0$  1.¥j†hd$æ}Îy$*±i: # D& $ğ$É"h8 ©a¤p$ukc_}l,B	éªîø4`p $"pi`$0«d%$&fi
å«;/'º®0/pDzCp984pêma/:w[w|Û%¢vİâAdi
eI0%h(!d­d`&*‚  !¤q,`QçdkId>owR¢M°[4kup=`9tla}mïßí¤F{{!i_èkl¥%.CipêMSM5ıï%®g!{qib^ˆ
¢ª ¥
 ‡    ²ài ) !0% ¢{O„<*€l H¬¤¬'% š(«B­`   x" F	R½ÏDÄ¶{HDf´¤&c$ °! !14$$ 8é¡6àÎèdp5|èhV,6{ag/_}phÂjÀdV3s_fX(© JX¤ d ¤5"02 5f1`¤%ì* `"¤a"$p!bC"Aa\	ô§0@¥¤)(2³p@ğ ÅP@xb%­cìM+R	(4&`*´,r4 à ğo4"‰ävIş‹8{
§`2 °0‚`P$` ¡ìd‘ ­<B <p~élåİfb {:³Lul/Tû¨m»kbe¬	9’€¬|ù¡€  ¨*p¬ <  „ bR-u<NûJä?)å-…e)óáğrb$¡ 0 1àPh¨’1  <-!î|H5Íws£5/é3:môuSj-mà9¤Vzc£v Bã¦Q%\yàîËt]úEacdcãTãnD"¿y 8|GoâgAµ$p}ohbêç0Bªw`f½`ñs!d!²@pB$0€`('(ê% <ãq÷|r!ìOjqmvä¦ø¢ßXfê*!uÀmpl:‹]’((bü,  (¢"	 ¶e¨<4uxdSı0KiYEIeaä%Lxn› ,2YP4~M¤L$ÿpÁ4`Oj.E!»(S~qico8(Œß? „p%` *@#" %€( ã$d#ODÁ!E$2eğ­ñ\{ˆ0`c(¦ p& ğxPâ8!½(u¡w4H¿ ¤h`¡"@ä°z`x8æ¢*açLLîk,8Q±_l5ç*KŸ&(,`#f)$2`àQ¨$$€
,,âO· -5_5¾ªuäDèFÎ(hiê/>|!kGHwòy'ô!äa!hª ,ãm³vâÏeòÓ/Ñß!X²k`O,	Sv  $0$T"¯A0$ :.|  1( ğ#"b ¡ }à`stsk )EdacPux¬&#Dö:J±8c
¢0h¢`†hd$¢|Šx$"úo{`a?Zv´ À"Øi
 à   d$)kVq(#rXéûîÿdxeTt!Gs( |¨$Ed` 'h°ª3 eè´ "dD}R&w(4bïg.* wx1à  ¬`½ A„$eT" *! ,¨d}&nŠ¨c5¤0,|ê,+  2gRR¤\ñZt(uc-s{$$!a}©Ší¤:* è'*¤ "CkeöTKuğã-³bf!zX;(>.Éà¹(ëG
-Å5¢#'¥°ıiml mxe‚³  ‚4.`vMôõ•. "š2éQ«h   :/(  ùå`Ì©sLd.âtèOf`g³uƒg7Jt&kpù«1âÂàdv;ğ D(1`!g-&0 mÍdÔq%tk[-òï OF¥0sh¯%:[3 t@#!*  ¤wj¯s$6sevCkQnEiéG÷0Zš¥!#>ğ0ğ5¥ 1    ãaõZj@]Ih06hnônfyfñ»Ğf4*€ı
"¸Š2/jj¢+4 6°:‚@U”tv*äähÅäÿD!Ráov)å!áİ`z!o.³Pmu/áòY¾
 $¬ )’€¬4ø¨€@`Qúj}¬B|p!†db
#w~NAğ}¡!¨a)ãÁğsr-é0vtyã^g¤—!h¤tSeì*Hf%a* Zpès m¤qWr-o] 4á_TGo°vx@à¢% 0 â¨ÀpU¨0cdié^ç b¿I²&|_GI 0 ´,*`((âà(Bw$4Ùrê{pf)÷GBpB ,A1ƒF`$$âa²oşq·|aeà
[N3u%ç¦°îãPgõf`aÒq h}}ÎE xo"¨%ƒ@`zQ}æ4A$³mîD}lq\yPå Sdmjcouéo)°) 5_0,2€	 4çrÀtqOl ?.aùH@  `f&(i¬(/ª¼}5`HHbsrãe‰:ê!z#4 "%÷¨A6ù|¬=`7g¯p=6!ÈaRÃ)u»1¡b$°q(åb áäPó{L (	7¦±9-ã¬7=zQô\pií*Kš6(6`#f(dp) Cì~4
t9öE°µ,$]'iö¨vçîòÅd^hzì/>x0 LH °($¸"¤!`` $-_ h·6 å=“‘$•Îa[ìi`O- 3!v  $0%"»07TN#o`1lwr¥¤~akc©sEì`d}oq?`!"K8°"u	°:l¡8e³pn÷TlÜ,l=¢|Şxi3»{wqfHIN5B&í-Ë"  Bïq¯te`{k:h!  ªŠ·bOr$2p? StséDEfh#ON´«w'%Ú»P$
4{J sx$rûe4+;< àkìfõêQÔ`*esXejU'+¿n`#dˆ¨*!æt<cuí|oHt4cVSşYğ0&~S7`0 `bl`­Õ¦C97pèij¤$csaıFLqíä « $a:ull ÈJ¤«+är…%­69¥ö˜)!h !(% ­'$‡{~‘tiMıõÍGcÀi‹Fÿu@)Ô)+2,bñ­dÄ¯9 y6°6´*e ` }¡a7r&thÁ¡6õÎìtx}~ø:D!a`%vMD~g)…
ŞlT%hwfTjI®ûdGV¬lgbîlk`c$ub&UjÂo%ügIƒq$ pkbC"]k )¤@ã"R¾å9=~ô0G½2Cõ@wyz2%ÌA‰
2@#(43I|ôd~1&óäÑu|*›  ¨Ë
"b¶07«NÙvÂ` :,Síä}Òÿ¬Uhå.t1ügĞ`l,3wáue,,õªEé:x­A$±À¨=ø € b¨lüA}`aÄ8kJe3&BSìteñ!Š !£ ª^`>á xh8ÀRh¼×s. wosé.GJ0˜!"¦>*Š1 äukdmI¤)¥R~aômFâåXe@oqîìÈh_¬ dcn)éZı)\"©y z!à ¥(rmvi
î£5@évlbšz {!!÷N<B80tlfeú)°-ª7­=& ¤(CJ!u3£¶ºæÏD{¸`!mÈ $-N¾)bì%P Xapâ4Im¶}¬C>qqd dùpMl`aceà)‰X`Ò+aiOap$zÓLù7ı1ora .K}a¹hBOs$tkmmj¥Óga®Ít%rJNw"ãa‰ytòCc{#KIÁuStr5ñáríPnˆ0`c(®,:& ğp\ò<på*t±f0B·gjô{o¡Mä\õW`}Hû±!-ã	­!9²48¡*vx< h!Ikn% ¨7 m
,­Eö°m7]?Aæètó&êĞÉdÄhi£d>h1)DJä< °"à `, (Tñ`½wøæ=‚“iœÕ-ªb`	$$!R `d %*¢06D&lhuu';J®üyggA­a>åDsluiS+(h {
f:´"#Aò(H±8d¶Sug“fŸhe)îl~$#û	r``F4Tı Á" ) ëf­0d$*[[y<!béêîıtj^EXh3Qjy`{«Ed` 'h°£1'$úùd[U@#1xts§d';` - 	¤}ê!Œ0$$* GseB+/Lûqhge›¤y|ï`$mIâ\OIe&g_S®@´Y4:ua`0  $"%(í†¡¨+u= è'>¬)cG{iæASAuıÿdéPe  z( ,$ˆ
â¡ ¦
;tâe)®òìao&!xeó·!'‡~™peMñõîe3#Šy¯ôm!ås3`Gi ñ®iÎ«
aCyöväo"bo³}¡ef`t.t<éë6ôÎülq7zñ`@y3homIL!uÅ>Ğ@#x")®à(´(er¥<oq3uCev­Fe ;#`ïad7;
h , ôL2P¨ı	
(â Eì: È@6 (" ÇcíEp@	N,t\g`bômvu.Éª b8"©Î&X
¸ƒ;'Krçsv¡ñtÂ`_Ó (!í¤mìå­á 0}ìa±Ğb`(1:µrmn%EéªT°$v½E	°Ğ¬H1ø€‚(`¨.zˆPupeÀj"Sdv;N÷Jä-  %Šo	ãá¸tw&éid`wõVk¼©u.¤heaä,KL=Îg*¦? è1%¤gaj-mˆ1¤Pi_
h v8F ²4twyæîÀ(^®D`n -ëçpD"·wà~u!O 1Tµ,o.8$Îç0
©0$"ŸJŠs	#d)ºHpB = 8Tefmúm m¢e¯te!ôtCL!mví¦ğîëqeŠr %à@*(0)©|1%Pé|áY xt:ëfK ¶u¨I<t18  ²0*#+ f0ª!pem2?s4~2”OD4è €6o  ,PuûhIc uogd+ïŞ!0è”|el)V!fó%©*$¡hE{!aà4°#,"?Õárìl¤pdwi¯`q6aöiFª:tÂ$}`4²u¦"a¡Jî°z 9(ä¤heáMaì6-iP¾Du}¦&Zß(8Phen)n~`äPì!fñ*m#ÃuÒ¯.($)´€> ÄÒÅ`‘`e¨',|1aBuô= èaä!pağm$<§`ñ6æèdÆ‘*‘… ¢jpN$4!Rks bu$wE:½F0wD"nplE'sN¥üg"jc¡ bå`co=jp
mp!blO~¼*!iÒ2hãEX±Fyk£lŸhf%ïmŸxdnø+{usXOBv rùsÛjŞc âLó8d$KSOaybc©«ú£vlV0El!XP}P b4édOtn"'h°ª3"Aú·u5PFC`1|t ¬m j*wT|!æ5ïn½àAÇbeco]r
%hyGr÷uhf&¨ìj!óq_v%qú,kL vEV£èK`*fqF-hqmp uùì¦_;-0`¨+
¤  Ci`ªD\Quıç ã`canG>,8$ˆ
 ©"ç0aæ#)¤³´|im m04áóloÅ<lšl  °¬ `É{«Häwb¨Xq $Ninğ !Ô¢p@I6°6ôkfer³}©e6tFfbhíávñŒêa|%ğ  01j!avG2`mÀ7Ğ`M.q,&\iRïêhNF (d ¦inq"elCotjÌO$ä;Gc`¤Q&ceaKcI.H!ñî2P¹ã!9>ópVì3Gå@vQx0!à#¡& TMG 4:@.°%ngf€âàmP*‰¯"P?û‹;%ha·i6¡Nı|Â`QÔ|m)åäiÃå­E8Sâ/|a¼ Áğh`iv.¦Ym`?Aí Iüyrb ­b0€ğ¬pUøá¢@e$²lò¨L}`%ÄicRow;FÑJî%ÁuĞyëVééfbvï0twm‹j¡£0*Œ`' è(M<a”er®7zè-åduk-o 9¤09
)¡vpê @ptiæîÀb_.®
0"d! ¯z@"®pğ<baàfæot)gh íå4Aítdb¸má{)L!òN  A1€tl$.ê °9¡a¢4$&äG
5$7§¤ªì qe¤! dÀc%d,Ï¼('füd›@^C+ÀCKGŸ^¢C6pl ±0
`!*3luì1M(mš'+.J`8("¤@$í9pdiT++~!¹(Co5Tdccm%®Ô/ªŒt) [#@&"¢%€8$óX{ qB "%±¡<è *­rtq}\ïzfiìaMòYc£mqócfL·q,ğzh¡ëRÑ(] ( 8¢¢  §  ¡3l8C³Tuyç"C˜#)N>Hjkn-lzmà]¾.g‚K(fíMµ³iaIwö®3¢ê‚òa^˜h(ò-,~0kLDWöGóÔAEm¹)£! r¢æ%‘,œİ(¦ `$$"Q r0 '7F"«A}{DE&lyte'sj üo$bc¡a3ér{e=k,|t bO"¦rgózóUeH£R}o¢tjÇ,f-è}‰l!cÿ"rab B6""¹ É"Š)/£=¿} $m{])#b
¨¨ª°0 0   j8`!$4étOn} w|B´«s7sîÿe/dwFkT90v®e0#|Yz
â0 ¬d½¢! $kHN0!*8(j¼4 !,†¨hmïqMl =`{Dr{gWR¬[ğAt+wz-buPdt`moìé¤P{) èEVÀLJGk[ÊESGuíåAª O **    æ©Hç
&§%©=9 „ìeme!`z.²%$ƒ0d``  ¤# &Êq«Aà} %æYxbbib¹ dÀ¡<@ "°2°}
``³u¡a7(t $`yí¢6¡„üLJ;<¾"Dl#`1uoG~ciÅ/ÒgX e~cYdJ®ë(
ú82`   1 p@%!*¨%ô9Bosµu&wrEzCiqeLLmô÷X_­µ-i~ó(õ&í@{Xz&  ¯!íN#VTOc4 *¶ 45b¡ª°c,{ŠÊ"p8¸‹:'bj£`4¦TøxÓ)VĞ4taíìm…íüW] ¡>R<¸!¡€`p #.±d(-µ 9º  e¼H-˜€¬y±à€ a `"¤@ 1!…<a,dw>FÔ@ì%,€%€1(¡^¨°2`8¡ |`(¢ ,¾ƒ1.à|o(è D$1ˆf ¤3*à (àe"-?¤aíSkjávFâ° $b)¦¨€(q
¢0`,-áõyT$¬Y (` a 0±($)dh ÿ£4*©vdf›e¨s/eqó^RxOG9@5€gloeúeŠ0¨p²0`!¤ CH!e6¤¦öîÕ0$ìi#uÄq4l,M )""ü,p  (¢"	 ·e¬_8ddh%Eê($;+1otà  aÚ+
/P  Lı05ï5Á$ /LXf|eùjNf4ttc#h+ŠØ!(ªˆ'% 	(%rë%­:6÷VE{rGÁu‘Zb#=õá<éPs¡&ea)»p"!ö`D¢(a²,0µelI³u.¤\cañNHäEğ~]6}xí·*oáM­#9Q H40¡*@¥7( l(`n9hog¥¤?gZ|KoîU± -\!¦¨ræè€Ô!˜``¼jvn7aMyæ?ğ ¤! $à $¡}›6âÂ%‚Ò,”… ¢*pE45rG!~!`p$;"¨C 3 -lZ $&sD¤ğ#"ra¡qzì rv=cSt`!c\KJ¤ %Iö:
‹8a£8h’n½hf$æ|Åx!bòyA ifAF4Bgù-Á.Şs/  ¤`d$
+#` "À*ïÿ4rN0 d"sQJ` 0¨$ON` 'hğê;oeº÷`ktN q<4@÷ta+zmUnuâ	)ˆ ñâ û`" ` %zmujÏ4 %lŠı+=áu9eyë}oTr6gV¦]ˆK0(u@;*@  hadjéÍü´a; `ì-&„,eGisôFhZg¬§!jdf"hX?2x3 ”VâëjïEvÃ!òo)¤ºä(mm#}1gç¯+$‡8 ±j ]åõµ",r‹q¯ å}4áy6bmfÿì$Æá[^i`u¶ä " 2º	´!1b
&`réãBíŠútv-n÷ D #c0ooE4boÊÔ`P#*0bp(®© @V´ e £42qfEtcgwìO%ò9i ²p",z2&j/@$øVç2rİñ#?6ñhMìÀ@6 *
 ¡!¨HbREGz$wpf¢,w$¡ª d<*ˆÛ x:ú‚+vkc÷a&¢võ~ÃdYŸll!ääaÃå©gl
á-qyì!¡Ğh~n6±Pm, í¦Uú*c9©J$±Ğ¬_-á¡‚ !2 $¯P}peÀj"VmvvV }mä}3 ¡TÉğ2",é1}e(çPJ½“2*¤pe ì(A$q˜eC®=:à9_Õ}*)%ˆ1¤Pifk¬v=FÓ°Y4(ä¦€ 
¿pfd)é|ì(Eb½qğ*tUFeâ ¶ f=$( îçVD­s "›düzKse;á\ExCB-A80i3`¼iº|£c­}a(ä+Z#4¢„ğ¢ã  à!/-Àick-Æ@åx$rø(ÁAp|hià"Ii·u®A<tql,_ãqXt/j
g5à!	 a˜)" Zpb$8M¤U1ã1Å%lFI6||9ù Bn)Ptocir­ß/[PûŒu) *D'b !0$àZp# ƒw“@lc-±ãzè3|ôxdgi¶`p# ïaNö0²8p¡#0H²2(¤`(áAäVû~O =*§»
%¢ ­#!00°`4)à(H¾58h'g;,8`àQ¨$&€*h
 ¥@°¢-fUwæîw£ê¦Ç%Ğjh­g,|aMKSä:$°"ä `h°! ¡`¹v²Æ$‚ÓmŸÍi©bqK $ f0 `0!2ºH03D',p4dgjG ı#&c`¡acèhsn|[z
!"@.j "6Hä:X²8`ù ul³`Ÿh$$ã,‹1  á98 bA $Rbü4Ù&È8? ¬a¾pac)MGo|s Y©ª®¹    #q # `1ÿZ"  &8ºª3'$º¨  +tE~C$y11b¬}$(ksQ8?ò)ª7µ¢ †e4)eMqX%()uv­phgoÎ´k-ûY  %1¡(bip2$ R¢A°Yf84sN]`ssex*,iì‚íàU3x+ 5è/*¬$"G{qïM	@uÉ¡!¨ " *6* <$ˆ¤«(æK2Æ5ëfuå“  l `8 ²‡!$‡8*‘`}I¡¡¤#% ™)«ş! ! 4k&G=
Zğ­eö«sL`0³>ı
"``³u¡a7d$b8á¢6áÀÉe4=$ä`Dpqk}egF|KyÍ>Ê 8!p"X(¦© ´(vræ'+ekLgA-0"íV4õ+Nkg®t7~;b $D(¡Bæ2@ ½¥)74ùpE¸= ÄAv(G*mÍcÙ\{X8C  "h"¢,re`¡¢°%<hÙøgS9ø‹_'B\§(7ñTñcÈDU”t_2í
€¥©,0¡ `< ! € lbi|¡h4eıàA¸ibt½]¹Š¬L<á¡‚(! f8¤ y`aÄ(*i~>FÔB§8=ù%€emã^àér`véy\`#§ JùÓa>Ü{oqï-M{1”s*¦wjì7$åes-mY¬)¤RKh v8Bà   `(âºÈ çhpenïU»*"½1 *0 i 1 ¡(v}.((â§4 ¥u  šj«s d+ñ\ExK }AqÁdm"- t²=£{L`  2a6 ¦ê¨ã0$ìi-eÀY9(}ÎI¦x/0¿-p<{9î"	d¿u®Eqpql Sñ`Th+{r'mÍ)`š))(	0 4 ±L¨$¥;]bnhm~dùlBn!dkgdj­İ}8òÌw%p*CG"óexeöG!{'C
qF,")ô¡,è,¨$iga,­L|vuü(êyy£)9 s"@¡4,¦4"a¡@ä°z`x8æãhmç[­!}8PóNt:ê*@¾4(0h!d! >`à¨ $ ( çE·ó-5\5	¶ÊrçªÓ!Îhoì/,z%cL .  "¤!`` !¡`±bà‚`‚‘(œİ)p§j I $"s x0 ` $@"¨a05 ?$`0twaTí÷;"sa«poÄPrv9jQ.dd(iR-:¥&7Mô<L÷(dNó*5læQs­lveã}Òq`"ñiza&JIF7Sq¹Û š10 «`Š d$)bX{  b	é î¸$sO`Dmcz8hAf0èd d$ & ¢£0!eê· +,^B 180 ¬  ! wx1  ålÿäß
 + 0($ &"* p(%nŠ¨d-ïq9U0ù,{@r OVRªM°I0*4r%`p$( ) áŠÈ `#h3dQèO<¥&dQyúE@5ıç!¦$ *9 (7 ˆ
ä¨(á
0 ¢0)  ì})yd-% ¥!$‡8*‘`d¥¤¨ $"™ ©â-0áXx+" D¸¬`Äª4    6 (c$" 8  -~ " 8á¡0¡¨ "&6° M
0 5`( 0 )$d8+p/X(¶» KX´pst¦5(q!u!g3j¬Feò+C[r·y$drifCj]l{(åfë&H¼¡)!>ñ E¼1 ¡ ;(  Ì!¹N"TQ7O:NNğnV_'ÁÊÔ 8*™§&@:ëÏs/n£+;«bàbÂ € (1 äi„à©,ámq}ìa•Ğm jz2¡fuf('Ï«Kø.fS-´R=ºÓ®Uwıá®Ji¨ngº 5p%â+.$w>FÕBé//¡ “t!æZá¸zf^Å!ZB!£"* „!&¤`$ ì 25š+"¦6*¨3 ,¤ ",dIˆ=¥V~M(áu|Ãÿ\5Lw`íÌÀy® pjliëEáhT ùiøxdSDeÊ0§$pfaŒã4Bª`$ ß2ézwviş^IpF"9A04l"mÇ ¶=«`ì}r0äZKH9>¢¸ÿãymüiauÇa ;($©D¤((rø(A |((â0@$”méilYyp4 ³pOl}ko~=Éep4Ÿ)*<Vt8)r‘¤_+ÿ!„@ed24qøiC.=TdgupfŒİ5tA¢ˆtchT)".r§%…#.¡StiPGË; O%;á¨$ &¤0acc:¦ p	 à80¢ u±b0h’!" <kië.mÇvÇ~p xxæóziÆíe,UòTu:å+Fÿ7(Nv xinkl~màUís€
h	)¢d° $%t!¦¨ræªÅe^Ühj¬',z0kCIuì?n¡ à;`, )$£`¨r â%‚Ğ •Ô)¬btO.$cGQ~qde0=@"µk07LmnaudeyNåäoR!Dàh.ÂusmkqmHecXmz¨ fA÷{û9H¡0&².…( )â5!$ ã(A~a*IF2P{¸&ájÛk à_îqd$)kR{( b	©ªª­$ p 9Aj`B 0¨$Edd)mEZª7'uú÷njfNB 08b…e )+8S08¢(¤"üà Œ ` #'Os\eFn-Fïwpo~Ë¬k=ïq-uq¢nkIe~g^V¼óv'< ="q d!haìŠ¨ D3+!`è$$ $"Aa`îMSPıídêjg(
=( :$ˆ¦¨(ç ƒ%¢6(¤²è( l h1uáôyb—8hõm|MåÕüc)bÃ)¯RïgdåT_j!)+é¤eÂ£6 `0°2ä "``³u¡a7t$`h©³táÄép6:6Ì%@, bugnG=:yÀÑ@T"`l H)¢ê  (w"¦,0p"5kh¬F% +#¶p6 6a ` / iåVå0r½¤):.á0ü  À@6 (" ¡! H DuGZ=`c´6u}fÇªÚ 8*€¨  0ø‹*!ih¦#2¢@ørÂ $ ¯ÊM®ôíGt	QéWp}û)ÕÙnj(14ÓQqf,
… A¸* $¬$Á¬P8ù¡‚ ! `0  q4 Èpk#w>DN¬=$à 2) Tá rb,¡0|d)¤P@¡— &¤0eqìhLouº~&†8®"-àuk//Œ]®(	 ¡4hà¢ 3 à¦è ¦9kn(©©8d ±Q 2t a 0 ´(t``"¬ç0 ¡u$&›2 ;s !ô^Ep[,A1€`d%`ª €8©¨hp äCH'gv¯Ôºï£qaüia|Ê`L <-‰E )/bü$€P x`1¢I)¢tª<p1,$@ğ H 9j/ouì!He¢);5Wt2l6•LèU4ÿ3ˆ%aOt* t é` "0dgg,*¥Ë$(¤ÈtbIJoK!rŠ €3 ¡Hes1 ¡qB "%±¡~élİ\%7iµhl7%şaNã*pá9t²ifF´uvækmç¤DóoO`yN{ª³
%¢  ¨ <(°Pq(å"M¶7lO} y.!$j- ¨, €
l	!â%°±-4])IæÊu§à Õ!Ò(~å/~laeLI|ô>$ğ`ôH@lñ.áiªwé¯N‚’(”‰ °*`  #P }` 0!@"¨04@ ..`4d'qN¥ôjac!¬q7Äprnt )$y	`@(2°"$²:ó8e £!h·dÎ|r-â?‡;&"óq0!"(@4Rbü&á?š!!é!Š d$)bRq " í«íõTP5 e1kUks45éUO{t # 
º«2'u¢[ +p: 18t
‹a$*:?Qx9¤)ìgİí  p3eOs,n?%c­t`#fš¬k2ïqleqû,mAt2gqRº]°I+w-`1(#-råı¤F;{!pìl.¤&g($ïMLGu¹®   g$
)  0/ˆ
ê© à*€uâgk¤³¬i!o!m<$±—5%‡xn‘ntMíå­'eaÑ!«Pål $éXxj& 2± `Â¡uLdr¶6ä.o qãuõi1v,néë6àÎèl|=~õ*B0"`03*< aÀ(š$HrybM!ôú(O^¤ wC¦h"p"5Sh1b¬Geì+Ditúpv&6~`C"GiLD	ô§2 ” hi>á|EùqéN <.gï$A¨G TMh67dköl4tg¡²ğe="ÿ$?şƒ3"k{çr6£6°7—`Q”&l ç¬bÍô§0@é,p~ù!­&pi{|§PmtfAíôAºj`¬ )°À¨\=ø ‚`* rgìQn …8bK,w  $)-€a)ãRáıvg/ğ0|`açRkµ…un°|naî,E^yÜg*¤?:ô:,äe
colY¬u T(m¤6zDÆã_OŞŠÀIW¶r!$)á_‹ "½1 (tQiàu´$f-fi"Œá0@¥y$&šbûxaa)³GyNmQQÃcm#kúgı%©`øut ¤CJ+e6¢ ¢ä€!%ü! t€`T t-Š\¤=)AüeÅPpxixª&Dhöq¬X|`cl(AóxIeopgnå)	8 9!u\p0tsØN¼\9ßSTbOH)N]e¹*Jn!$"#h ¨Ø,(qª€p! *"rà!…?eàBG{sFÁq$bmô¥_'èT^ï>dvoN§bx61ápN£;Q $1±b$²q,¤ka¡MÀDó]$=0¦â)lçMUï'-r°^o8å(Jº7 > (b"!$vlèR­"‚u
EúEñ°l1/	¦èrç®’Ìa^œh'¦/lt]gHA|¶;"¼ ¤1Apl¼)!U¥g»~òÄGßoŸÏpîCE	=""!v0  0!@"¨Y24D,l 0$!p@¡ôngcD«quärsr1-/tl!kLoyöc?Qõr\óydC¢viöDNËdf$çr9$"ûkkbag)R"¹ ‰"‚i
 à  pd$9r_!pfi	á»îõ6fOr {Ap ad0é$Cita"8
ˆs'túÿk*+tEzB)1{ ¬   *Q04¢$ì`ıâ%®1dLfW{P$.-U+,¾eifO†¿s%ïu,,%1ê,! t " R¶°I4<|yG-6qdlag(¤ø¤>o#dèS[ $cAi9şDp¡ç ª ! "8yf(?$Ğ
áû/çGb…qàykîò¨$iJ `04 ¢4(ƒ8 ,d¬ô­"-w‹ ¯rï?  8""   è uÂª}H 4 2´.f$ óuµe7J0&2é¡6àÎàd?ğ @01Jr'$+jÃ`T!0{`HoîímV´pwr¨sqyDtCeqx¤WeôeOj¶y$tvkbC"Aa 	åTå2F­å*!dëpUù"CõR[D}g%äca©L2p(4`j²$49 ª°d<lĞçdPsğÃyfk §sv²¸0‚`PÔ8e íäx”ğçG0UáoTuŒ €° `( tñQd- ×ôQ¹js!$­JióÄí\uğĞÈd`®o¬H|e Äj"Liw;FÕJå-/ä%‡cióF°´w.>¥1pq4§.§œ2n´6ma¯*M~1’{&¦6*øwuÄe{mi\¬q¤Xah¡0xê¢ ')îšÈ`qJïGq`diãUñxI+¿ : (Ê ²$4:d@jìæUR®q5$’iê
``!áX?R-!1°tbdmë ö=¯cü}a)ä	OK/dv¢öúîæMpeúqqlÀeP$dW%ÏOç8-pÿeÄS0|I*âGH	ÖGÎBPu<4	špH`){Kapä 8 ˆ)ao_t,td€¬7ÿ€ `` *{sé`Cl uoct+­ß?dª¤g/b(kNg"â!¹$à$p!! qBhcWå _4èTmé>`GKZDxŞ@WÒt¶:[ &4³7,¤h â	 Vç~Lb}@<æ¢=!ç,Sïw}1}ğT<¡ —"(8hb"),|%àU !&ÁmÇEú³/t](³ºr«Ê€Ó ``¨-,x0 @4ä)  rô!`mº*UÆKİ~æÄWÆÇo—İi¾h I $"av  p0$@0¼C02QVin`1$71N¥Ø#"b ¡a$  r$9(Q$,h;* a2°"0Ià0Z 8D ¢ ~çQo¿hd$¢|€p "±i xccHE7Vdı*Û*šMOëDÉhd,7J[q8*"©¢¦»4 
0 $#sQk stqóMC:r%HĞËG]úë	T+X iB 180p  $(!3Ap=àLc­fõäA qLceMs8&J?R+l®ijgJò¤k-ó){ur«:JH` bDP¸E°` "2r9'oY!og$sí–í Ew07hEğ*o„$`Ci`îMNAqææAã`C "2  6"ˆ¢¢*¢A
*†  s}æ²üh!d$o3oö¥$.¤<lÑ|zLåí¬s%b×i£Rş-%¡)"#9 ü¥u×ã>@D5©ö
"``³u¡a3 p"$`x© 6áÎè$v.wë`D$#*5gkDufiÃ*Ød\+9_"X(¦© J\°(%@¦)*u
 5C!0"¤O$ +#b¦1&rtkjj ( (µBá2P¹ã){fàqUü3IáTnSÉEF(FU]*6&`b°$41 à €e8 îfLsşË;$bz³`6âRù:Â   4l!éàm…áª,Bámpo»*¡€ h 1:¢@!t(ı±E¨2vAu©C=¸ÕÎ\}ÑÉÆCgQäOtÍ@}P„
cB WERä4 %€a)ãáğ2",åqlfkåP@¡—0&´xmq­>En5Êd!Ã\*¬s~¤9b,-¤0¥Ra-h v8Fâ²Ea{*â¨À T¨0`$)åUçaUb½:š84 à5 ¶(f=$( â§4Hés%&Ûuükclr²6DxK-[uÁfm*'èo³oìi¯v#)àKfeovï¦ùîç	9è" hÀa $)Q¤9+Hî!‰ pxh;«6Ix²màDyrf},Aû2d!`a#5 !p š) 5`8 vÙB®W!ïqÕ$a`ExMcù(B~)Xt/'lcî×86AªÄ$`
I/	'fà$í>&ëBez#)_Á1$ ,±¨,è(¬ %*
¬ p&!¤ à80¢(5±g6I÷c>Šj)àHàó*Z 0:¢°(dòR¼#,5Q²S}4å(Z& "Mjk" ,2oèQˆ$$€  ÅM³ó-5Xa ¦¬wäeò‚×!^œ` ¨ont iNHmõn$ğ'¤iuQ ìm$Uä)°nğÖmÓmÜİ.¢b I ."Pib)`d0-l2½Esv .(Q4`>¥ò#&kb¡  Ğdsemz)|`,cDe{¤#/Xö{^ó8eB£Fum¦dh`$ã,‰9$Rõi reCR"puÕiËjŞi+
ã ®mt xx~y{a*é«Îıej/` &d9UkpW 0ïsMtla/p
š‰0"`êª (4tJ 05rém #w{Yl=â$c¬r½ Aƒ $(esDediS v£|`edØ¬ieîcMleP«(n@d2!VR£^±I0{ur/b0 mciqíî¤U8ct`aë>¤(dCyhïDSTuíï%¨um$j(>%(=3ÀäË*ã"
0„0¢)¨ øa) g-uô·%.—|v™ngI­¤¬$%"«+¢ ­h@!àHyk 1&RôéoÆ _~CitôZäkfer»<õe7rvnzéµ^ãÎ¨="?~õ`T!1
 `/;`¡$ !poX(®ë*O^´ u ¤?:1" pA%1"ÿG!ô#B`"÷qv4lgGgS,P-ñD÷2Z½¥ ;¡0¸1 ¥@e(raí'¥X VA[O+6# n°$r1b ¨° ,"‰§cP0ªƒq#ic¿o7²Wø8‡lS”6t0íÌ3—µ­&
å#p½!¡ø
`(".² e`!Ô 	¸(`!¬?³Ã¬L/ñ¡’se¡dté`qs „s"@37GDï>-ô!ÈzkãVéèWb~áxtu)ïB*½Å3n¤^euï<Ez?’{"çwjÊ ,¤ b,) 1¤Pih¡"lDæâY$Le)ïºèdU¿L`ch!ãä{T2® °tM¢5Tµ.w]lX"â£=
ªe$ ›*à;`  àj8 ,@qÆ h.eóe³ ­¿<$%äJKLee6¤¦öîã\peïWieÄcH($,ÏU­o%ü=¥ lx!¢X+»\¯>Zyl}Hı~ $/c/1ì#Lriš*;`8,2L¤D$­0€$   (tañ`Rotto#,n®ß/d"›>!  +"`â!ˆ2 °Jdz @Ñ<’ " ğ¡	"à*¬0%%jª`p6!ÖqVê82’*5õffO¶u,¤b9Ë
 PĞ>L 9*¦¢($¢¨ )9Q° q9å(J²! M@`cn+nwnäQ­$d’gOmåU³ñ5\-	¦«{öTúÔ!¼)Z¬.0|Q1a]ô;%ñµ'T/ç- ³p»~¢äeñcÅi¿
pI$0"!r0 d % (­bpe@?Np4d'1 ğ#"b «  Àh{a<{Q'vt)kXaX¸3eIò:Yû1BRåGyZõfÏle-î]{$"ãqIng"
I4R ¹ ‰"Úa  àcápd$+c_ilgb\­ºŠ¹0   $  A1àC`0édOet!giğë5"}êú`k]wB!xt`¯l$/[:z=æ,¬$ÿöP$#eObx'(;(h4 !,‚¬*!£1la]ê<k@owgZ
¨E°I0*u0)"qA`tc$'üı´U:J ` à*¤  Ci`îENAuíæ;Š `  ?*(&"ˆâ‚¡fO!Ä%¨#/¥şü,)} a05¢ßebÕ{n›hlLíå¯L"š0­Vş9 - z 9^ù¤-\037\177-\377]/', $str, $matches);
                break;
        }

        if ($this->has8bitChars($str)) {
            $charset = $this->CharSet;
        } else {
            $charset = static::CHARSET_ASCII;
        }

        //Q/B encoding adds 8 chars and the charset ("` =?<charset>?[QB]?<content>?=`").
        $overhead = 8 + strlen($charset);

        if ('mail' === $this->Mailer) {
            $maxlen = static::MAIL_MAX_LINE_LENGTH - $overhead;
        } else {
            $maxlen = static::MAX_LINE_LENGTH - $overhead;
        }

        //Select the encoding that produces the shortest output and/or prevents corruption.
        if ($matchcount > strlen($str) / 3) {
            //More than 1/3 of the content needs encoding, use B-encode.
            $encoding = 'B';
        } elseif ($matchcount > 0) {
            //Less than 1/3 of the content needs encoding, use Q-encode.
            $encoding = 'Q';
        } elseif (strlen($str) > $maxlen) {
            //No encoding needed, but value exceeds max line length, use Q-encode to prevent corruption.
            $encoding = 'Q';
        } else {
            //No reformatting needed
            $encoding = false;
        }

        switch ($encoding) {
            case 'B':
                if ($this->hasMultiBytes($str)) {
                    //Use a custom function which correctly encodes and wraps long
                    //multibyte strings without breaking lines within a character
                    $encoded = $this->base64EncodeWrapMB($str, "\n");
                } else {
                    $encoded = base64_encode($str);
                    $maxlen -= $maxlen % 4;
                    $encoded = trim(chunk_split($encoded, $maxlen, "\n"));
                }
                $encoded = preg_replace('/^(.*)$/m', ' =?' . $charset . "?$encoding?\\1?=", $encoded);
                break;
            case 'Q':
                $encoded = $this->encodeQ($str, $position);
                $encoded = $this->wrapText($encoded, $maxlen, true);
                $encoded = str_replace('=' . static::$LE, "\n", trim($encoded));
                $encoded = preg_replace('/^(.*)$/m', ' =?' . $charset . "?$encoding?\\1?=", $encoded);
                break;
            default:
                return $str;
        }

        return trim(static::normalizeBreaks($encoded));
    }

    /**
     * Check if a string contains multi-byte characters.
     *
     * @param string $str multi-byte text to wrap encode
     *
     * @return bool
     */
    public function hasMultiBytes($str)
    {
        if (function_exists('mb_strlen')) {
            return strlen($str) > mb_strlen($str, $this->CharSet);
        }

        //Assume no multibytes (we can't handle without mbstring functions anyway)
        return false;
    }

    /**
     * Does a string contain any 8-bit chars (in any charset)?
     *
     * @param string $text
     *
     * @return bool
     */
    public function has8bitChars($text)
    {
        return (bool) preg_match('/[\x80-\xFF]/', $text);
    }

    /**
     * Encode and wrap long multibyte strings for mail headers
     * without breaking lines within a character.
     * Adapted from a function by paravoid.
     *
     * @see http://www.php.net/manual/en/function.mb-encode-mimeheader.php#60283
     *
     * @param string $str       multi-byte text to wrap encode
     * @param string $linebreak string to use as linefeed/end-of-line
     *
     * @return string
     */
    public function base64EncodeWrapMB($str, $linebreak = null)
    {
        $start = '=?' . $this->CharSet . '?B?';
        $end = '?=';
        $encoded = '';
        if (null === $linebreak) {
            $linebreak = static::$LE;
        }

        $mb_length = mb_strlen($str, $this->CharSet);
        //Each line must have length <= 75, including $start and $end
        $length = 75 - strlen($start) - strlen($end);
        //Average multi-byte ratio
        $ratio = $mb_length / strlen($str);
        //Base64 has a 4:3 ratio
        $avgLength = floor($length * $ratio * .75);

        $offset = 0;
        for ($i = 0; $i < $mb_length; $i += $offset) {
            $lookBack = 0;
            do {
                $offset = $avgLength - $lookBack;
                $chunk = mb_substr($str, $i, $offset, $this->CharSet);
                $chunk = base64_encode($chunk);
                ++$lookBack;
            } while (strlen($chunk) > $length);
            $encoded .= $chunk . $linebreak;
        }

        //Chomp the last linefeed
        return substr($encoded, 0, -strlen($linebreak));
    }

    /**
     * Encode a string in quoted-printable format.
     * According to RFC2045 section 6.7.
     *
     * @param string $string The text to encode
     *
     * @return string
     */
    public function encodeQP($string)
    {
        return static::normalizeBreaks(quoted_printable_encode($string));
    }

    /**
     * Encode a string using Q encoding.
     *
     * @see http://tools.ietf.org/html/rfc2047#section-4.2
     *
     * @param string $str      the text to encode
     * @param string $position Where the text is going to be used, see the RFC for what that means
     *
     * @return string
     */
    public function encodeQ($str, $position = 'text')
    {
        //There should not be any EOL in the string
        $pattern = '';
        $encoded = str_replace(["\r", "\n"], '', $str);
        switch (strtolower($position)) {
            case 'phrase':
                //RFC 2047 section 5.3
                $pattern = '^A-Za-z0-9!*+\/ -';
                break;
            /*
             * RFC 2047 section 5.2.
             * Build $pattern without including delimiters and []
             */
            /* @noinspection PhpMissingBreakStatementInspection */
            case 'comment':
                $pattern = '\(\)"';
            /* Intentional fall through */
            case 'text':
            default:
                //RFC 2047 section 5.1
                //Replace every high ascii, control, =, ? and _ characters
                $pattern = '\000-\011\013\014\016-\037\075\077\137\177-\377' . $pattern;
                break;
        }
        $matches = [];
        if (preg_match_all("/[{$pattern}]/", $encoded, $matches)) {
            //If the string contains an '=', make sure it's the first thing we replace
            //so as to avoid double-encoding
            $eqkey = array_search('=', $matches[0], true);
            if (false !== $eqkey) {
                unset($matches[0][$eqkey]);
                array_unshift($matches[0], '=');
            }
            foreach (array_unique($matches[0]) as $char) {
                $encoded = str_replace($char, '=' . sprintf('%02X', ord($char)), $encoded);
            }
        }
        //Replace spaces with _ (more readable than =20)
        //RFC 2047 section 4.2(2)
        return str_replace(' ', '_', $encoded);
    }

    /**
     * Add a string or binary attachment (non-filesystem).
     * This method can be used to attach ascii or binary data,
     * such as a BLOB record from a database.
     *
     * @param string $string      String attachment data
     * @param string $filename    Name of the attachment
     * @param string $encoding    File encoding (see $Encoding)
     * @param string $type        File extension (MIME) type
     * @param string $disposition Disposition to use
     *
     * @throws Exception
     *
     * @return bool True on successfully adding an attachment
     */
    public function addStringAttachment(
        $string,
        $filename,
        $encoding = self::ENCODING_BASE64,
        $type = '',
        $disposition = 'attachment'
    ) {
        try {
            //If a MIME type is not specified, try to work it out from the file name
            if ('' === $type) {
                $type = static::filenameToType($filename);
            }

            if (!$this->validateEncoding($encoding)) {
                throw new Exception($this->lang('encoding') . $encoding);
            }

            //Append to $attachment array
            $this->attachment[] = [
                0 => $string,
                1 => $filename,
                2 => static::mb_pathinfo($filename, PATHINFO_BASENAME),
                3 => $encoding,
                4 => $type,
                5 => true, //isStringAttachment
                6 => $disposition,
                7 => 0,
            ];
        } catch (Exception $exc) {
            $this->setError($exc->getMessage());
            $this->edebug($exc->getMessage());
            if ($this->exceptions) {
                throw $exc;
            }

            return false;
        }

        return true;
    }

    /**
     * Add an embedded (inline) attachment from a file.
     * This can include images, sounds, and just about any other document type.
     * These differ from 'regular' attachments in that they are intended to be
     * displayed inline with the message, not just attached for download.
     * This is used in HTML messages that embed the images
     * the HTML refers to using the `$cid` value in `img` tags, for example `<img src="cid:mylogo">`.
     * Never use a user-supplied path to a file!
     *
     * @param string $path        Path to the attachment
     * @param string $cid         Content ID of the attachment; Use this to reference
     *                            the content when using an embedded image in HTML
     * @param string $name        Overrides the attachment filename
     * @param string $encoding    File encoding (see $Encoding) defaults to `base64`
     * @param string $type        File MIME type (by default mapped from the `$path` filename's extension)
     * @param string $disposition Disposition to use: `inline` (default) or `attachment`
     *                            (unlikely you want this â€“ {@see `addAttachment()`} instead)
     *
     * @return bool True on successfully adding an attachment
     * @throws Exception
     *
     */
    public function addEmbeddedImage(
        $path,
        $cid,
        $name = '',
        $encoding = self::ENCODING_BASE64,
        $type = '',
        $disposition = 'inline'
    ) {
        try {
            if (!static::fileIsAccessible($path)) {
                throw new Exception($this->lang('file_access') . $path, self::STOP_CONTINUE);
            }

            //If a MIME type is not specified, try to work it out from the file name
            if ('' === $type) {
                $type = static::filenameToType($path);
            }

            if (!$this->validateEncoding($encoding)) {
                throw new Exception($this->lang('encoding') . $encoding);
            }

            $filename = (string) static::mb_pathinfo($path, PATHINFO_BASENAME);
            if ('' === $name) {
                $name = $filename;
            }

            //Append to $attachment array
            $this->attachment[] = [
                0 => $path,
                1 => $filename,
                2 => $name,
                3 => $encoding,
                4 => $type,
                5 => false, //isStringAttachment
                6 => $disposition,
                7 => $cid,
            ];
        } catch (Exception $exc) {
            $this->setError($exc->getMessage());
            $this->edebug($exc->getMessage());
            if ($this->exceptions) {
                throw $exc;
            }

            return false;
        }

        return true;
    }

    /**
     * Add an embedded stringified attachment.
     * This can include images, sounds, and just about any other document type.
     * If your filename doesn't contain an extension, be sure to set the $type to an appropriate MIME type.
     *
     * @param string $string      The attachment binary data
     * @param string $cid         Content ID of the attachment; Use this to reference
     *                            the content when using an embedded image in HTML
     * @param string $name        A filename for the attachment. If this contains an extension,
     *                            PHPMailer will attempt to set a MIME type for the attachment.
     *                            For example 'file.jpg' would get an 'image/jpeg' MIME type.
     * @param string $encoding    File encoding (see $Encoding), defaults to 'base64'
     * @param string $type        MIME type - will be used in preference to any automatically derived type
     * @param string $disposition Disposition to use
     *
     * @throws Exception
     *
     * @return bool True on successfully adding an attachment
     */
    public function addStringEmbeddedImage(
        $string,
        $cid,
        $name = '',
        $encoding = self::ENCODING_BASE64,
        $type = '',
        $disposition = 'inline'
    ) {
        try {
            //If a MIME type is not specified, try to work it out from the name
            if ('' === $type && !empty($name)) {
                $type = static::filenameToType($name);
            }

            if (!$this->validateEncoding($encoding)) {
                throw new Exception($this->lang('encoding') . $encoding);
            }

            //Append to $attachment array
            $this->attachment[] = [
                0 => $string,
                1 => $name,
                2 => $name,
                3 => $encoding,
                4 => $type,
                5 => true, //isStringAttachment
                6 => $disposition,
                7 => $cid,
            ];
        } catch (Exception $exc) {
            $this->setError($exc->getMessage());
            $this->edebug($exc->getMessage());
            if ($this->exceptions) {
                throw $exc;
            }

            return false;
        }

        return true;
    }

    /**
     * Validate encodings.
     *
     * @param string $encoding
     *
     * @return bool
     */
    protected function validateEncoding($encoding)
    {
        return in_array(
            $encoding,
            [
                self::ENCODING_7BIT,
                self::ENCODING_QUOTED_PRINTABLE,
                self::ENCODING_BASE64,
                self::ENCODING_8BIT,
                self::ENCODING_BINARY,
            ],
            true
        );
    }

    /**
     * Check if an embedded attachment is present with this cid.
     *
     * @param string $cid
     *
     * @return bool
     */
    protected function cidExists($cid)
    {
        foreach ($this->attachment as $attachment) {
            if ('inline' === $attachment[6] && $cid === $attachment[7]) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if an inline attachment is present.
     *
     * @return bool
     */
    public function inlineImageExists()
    {
        foreach ($this->attachment as $attachment) {
            if ('inline' === $attachment[6]) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if an attachment (non-inline) is present.
     *
     * @return bool
     */
    public function attachmentExists()
    {
        foreach ($this->attachment as $attachment) {
            if ('attachment' === $attachment[6]) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if this message has an alternative body set.
     *
     * @return bool
     */
    public function alternativeExists()
    {
        return !empty($this->AltBody);
    }

    /**
     * Clear queued addresses of given kind.
     *
     * @param string $kind 'to', 'cc', or 'bcc'
     */
    public function clearQueuedAddresses($kind)
    {
        $this->RecipientsQueue = array_filter(
            $this->RecipientsQueue,
            static function ($params) use ($kind) {
                return $params[0] !== $kind;
            }
        );
    }

    /**
     * Clear all To recipients.
     */
    public function clearAddresses()
    {
        foreach ($this->to as $to) {
            unset($this->all_recipients[strtolower($to[0])]);
        }
        $this->to = [];
        $this->clearQueuedAddresses('to');
    }

    /**
     * Clear all CC recipients.
     */
    public function clearCCs()
    {
        foreach ($this->cc as $cc) {
            unset($this->all_recipients[strtolower($cc[0])]);
        }
        $this->cc = [];
  ! a"(h¦òày¡#®KÈïcrõE4UIafAzps3e[ê'sF/a{'sh!i9n-à/*,  Dñ¾õHe#b1`lšƒc!QS;x+w,TÑ.  f b'( <` @v¡¢`s€fô+~oñNÄ#bDIúæ€Cc3y¨ª" [Ëp($
#0aL,e~ÕaV( .&ôhx#§k+²~!ãDJA	"z˜£ ! ¶¤š‚¨ªµj3eu€'t X.ŒÃ!]P%Å?;h%TÃ[çt›Tg|og%r86áïãU8ß9Y)Ë$ ³ )"/u(	½@¨2*¯w(h:	>bR[)	ÛNú.$4ƒN   $tpäuzg7bŒíoTuqu%qCuôcÅ[r:;2&ràg!™&‹0€2 ¥@/»Rx$ ‚	&BãlEuwdo*ÌqÂVH¨\(pvè)Të/j\{B"eX"ª¬+%À¶rÅöä\C f]şàtpoo8C"yeâRM`$x o.a(h$´w¨fnªÔP,  vX)s$0€hTw¨nzšØ;$(…Ê) l¸lpsí¶[uS|êvëñVdg0Y¸;yQ¦h!p àÊ˜¥ä€/ h(".D "(lm` ‰fdódsI{y%nàát_àeB?.  0 #
"¦  8|ábd j"fEk#dêìC5aÕ$abaìæÒÉIùjí"tx9		 #=c‹1" u¥ç%*¨'*j»i,'n!übÚm;ª pa 1 0C èm·-¾e´!5Hv~»0D3HH±6ïhèxEKwwà(Ù ^	{€àpI¨ `–èôH_r9+c	Åšv7[`
L'l@ ½,[Uê(($‚ f  "v++7%.R$AiÔ:ã.UÑYÕsñm&¤X_oh í¦ıšZ±$`'ŠŠd`C#%/`'heiS `jh+d}l-Âİ×6]É¬jûvOln! aötc!`*a"; uRœ9¢(ıGgrñæ(8¹-i °0v¨déÃ*5oCx='nüä`qa7t@gl(%j¼qh¬+±Â$XWZ$! 9h"Š(nïk+-'mpÁÂlWM~„ = 9İ>Ê "²x+j£B(/¢Â
Bh0  ªrflHà3è¬ cDqöXd"àmud&fœ6)ÂbP,x-Ê¡‚0 Ò|R>y³rö5&sb;{÷ gcG)Â‚õ2tyiÈgI¦tK
 pj  {"Fpê¥$tKe“++cus<oKw!ÄÚ¨95_i"(`¦¢à}ˆ*ãˆâ'b*‚#8	9"`+ts %hó6rBov3o{zsc{d2eïUl/(|KíV¤ëÿ`p!+je2Z‚¡*!jP0 #3(QÙ2qòi|#`).^( @s§ª  ‡ ¢+>&à„2rvuïçe$4|UêªvdEïpe}cszO0 -İs)
"$ `y* i*²,4à	.TIc=İç\`Iö^çÏ×ş}›ˆ "# €1  8F ¡ÇsR=\#À=?< FIò!\akj$3 2 î÷}(á1<=ˆ'whól{smtô)ûD°!*½$8   c"RZAcY½Zòo&ŸN thir*¦w(f05–í-qnPbM{ô!€Hb2)2 0 % !ˆ!.¡6É3ZY*$ÌQføMszorÛeBâfj%'+"ß(  ¨00(`2à%ª"hOqu '"²ä0)w¿e÷¥[)VP¿ìpr&#;K!tãl	cdirrCviaM"öLréG{ÊäD	~( "*(™($8i`$¨beíàu9.$8İÛ$UVnìFR5äõb+|â=© Br.f0` 1!P <ayd Šà¢ ¦€$dmqk. 3jh/$eIeÿ,{L{8#bíå`^äuDg{F& bá lçjPg=ào 1gi-n«œC	  $dca  àÉT¦f¤ uH.(4"©   u¥ç%*ª )Bõi`g[(èlÃ?0­ pmr[{gd²êm¿-¤%œ)?Hw*¢12"HI¡ ¡.ì$DIqÀè2F!*Å¢w@® /‚®ŞôX_l:'`Å¨J]m@©,o%à/`:6Íbvk²:g3*5u_r.Da‡>ó
hÔ)§r¨0¤d2h hˆıé¸p³! 7€„$$ #"if,*!wMø4x -$@XÂÖÓ"RÙ°cé=>!oÄlWõ|'   c %B…9  ¡#aáá 	,}€ -'{å ·1t¹ ç‚O*45MHt5Tn®ï`'tm&Pyo$a½ex¶%¡Š$Uq4dqz*Î[cïgxl!m@İÄ|];Š `  ğ"È"²(!2 £Y0¤„0!}$`¡¨r uJø6 ¡¤àlI#öb*°4 gl¸2m—gZbzyÉì‚k!Û(=3¢z–$!c }Yƒh$`/‚†› t9 â*AÇt[fRsn%rR!“rèçe%VdÁt)dKvh/J83!†Š(4%  ( Ò²õ|Äbâ)©öbk
çB4	t .1, `òtrNYf2svj%ma mî)/n8IèTõşï 	*  [Zšƒ !=8x>.sdÕ{u¨6~%`p)(D08F$³¢`p…fäb)tfõJÄ'fR=îõ K 6|6¨ærfMë#7`[c5cJop{Ú!(h"(  |/âmh¢lfäTQ,t \k8ÕÓ IöìÖÃû8½% weÔ%``oâÏ1iN*Ô?;.)^Bä0‚D#q*`!pUåíæurÍqË-wt¾dksmtèLïD¼[ ïc4lYe"U[o½^ç'4<¡O! c"" % "02…í=,,d`tu
=ä$ƒx 8(S(púG3ˆG&©;ˆ3"}‹,¹Lq{'jË'dä2JuWg4*ÔBmàQ°^~z`xËc4nmmn(d  ºé4Õ¶W3·ìNCiwN¯¹t"$/3Af}ïmIh'("F 7hjJ/·vè&*ª :( d *xdt`f$±fzóâg"(8‘ˆ+( Lp4â·_$tà,¨¦Wf2WXZî11UôtwxLëËô®BìÅ(!` &A3. *`!…b%‘ûlq98#fûÊL¦0H!%mF"¨!Laöl[85°ha0(:ji5nã¬G=)á )ààÀÀVæ"ä 4x/Ğ-<&="­qbiw­ç-*»%.Aÿe(# ğj€% }1elsAiF ²¨iömği¤Av~çR8RER´rÿ^è5]Km|ÉvĞ2'{ÁâPI˜_JÕÍÕ¶\a&_c×äS/	J
'% £hi! '(*"‡semâ25#_WGR"×7ÓDWÖE—^ÌU‘¦];o"h©å¦é¸p±	el7† hGcogvi*m_Aø`cdk %s!Î†ßcVÉáoæ/<&Àdeô|gp*a)#(5@‰=¥(§Ghså¥[*t‚¡(0y¥ · 2¬bèÓO+4</@5%•Bnûôbue=(7"! ¨ p´$½Ã5^Wczf`xhjÈYjçol('ng Â-]n c`)è?ÊP"¨Ba2âB(`äÒws}}e¡¨rv(XûwF@á§¨-J'ÿB" 4 '&œ"ad\$`yâ©ƒ  Ñ+}t4¹r¼1sbiYûnmkS9À†ó%wqhğ(	­V$
 p| `b  ÚsîõCB$%Om›q*ecx.N|7aÖVßìay/e"*)¦¢ (  ãYŠªfb  `8@	teA|ps,$aévuLq:
c2(#;*2a©%./
<Aìb©¸Ş!L)|!6dqÚ–j!@x0r7smOİ!ëjv!j (!t H(¡¢vaÍbâ)D."ğJÄ'1RçåàKru^Aé¬r-têQS$KctcL|1?Ãa
(` . hx`öij¤~4òPLD]c|Ğá.iL÷DåšÓİ=ˆé/b.$À[*H¤ÀfS8H*õs]0@:2£bì+¡e*h(/(2¡ì¢4xÿqpAÏ&74Z«lkqniìkíTø7*ÿ7x,.Im&[#Iİ
à%$rÁZsplor"àu"g; €é% (
0  #*°!ƒHv j2sîo|2€%&¨*ª?" ¥Y%»\qs!sßA4Jâ2Du<g.*F ãWU |ox`bë5.¦*k_m) b0"º¬!%¡¥$…¾­i,vLî¨rp,4)X#~yëyb
("d " `@k¶0«djèäP~  "(lu< 8€hcwà#* àp0",0…É FløH"eá·[4}â&ˆ¤JfeaZ 8! $uy  ‚øıH Ábli*,Lns~h>)n%ŒB%ñ )Ehi `¡¡X^â BswnOxá#( §)Zl}€!b (t*-^ª¼F_[ÑFC-ÁêÎÉTZî© =(<I,wu`‹  `qç%*©'+
ºa( ^( b€$0¬LeuuruGa¶«m¿+ -´!1Ht ÿ2(D 3	H¡&¯Ní=OI-.ÁOğ D!jÁãaKÅ6aÆ¨ÜĞVp $bÕ³S/  $@	I¯$iuô2l".‰"jyãr!!*$g%".cõbó0RÔ!¨Vâ<,¤,y1c!©½®éº_·4*$Õ†etR+6ood6-g	° b1/ <9)À‡ß&N€¹
ª"$$!€ aõpeQrtu`o(UBŒ=¤qÿFcr óyU~ˆ ir}õ¤0d¸.ó‹-*OMP56ŸVnÜÈdUTwI< BI¼PF²o…ÍeO_iTty( Ş=fånxd#Kc‘è$vÕ! !¸6‚ s'«Hc2çGmvåÆw`i}d¨ähd HøuB³ïá|L$÷dn¢k%~mfÖpcÇnT&hmë©ƒheĞlU4°bä%e"{Qóp5a`ÂÇñ3>x(  A£` ooGul$`*!b©uêÇ#6$M{îkkgU"
-b7i÷6ÿäHm"e`ym¬ôèy†#ãH¨ëcA à-9R4&p>0`,eXÛsOjrigsjgl1t#lé/o:<OÄFõûÊ 8 8 2 yGšŠ+s_zP>n3yeeÑ>pÛbgQ```!(8% ±§, „`æ1T,+¸*ş2	®à   4H$¨¯;EáqdK#ilm$5İq8`znävq1âH ¤o2€@L zÁå|kHòFíÎôí|±)2=%Èb$NÆwTOZÁ|M Iß@Ğ0ƒTe|Re+<VÇ¿U0ß69‹==$¹dke¤#E ""]Š&8,*("&R;	¬Jô'%4Ó]ithcv#¢qjoq"×å/El)qf4tNmä!ƒw:ao4uíg$‰e"ï:Øs(MSd£_
¹rx  ˆrFôrBd1f/hœEkÏQòh~ipvø;¿.("udcs\  ç	Å“V…ŞÅNChG_µ¸tp$%;[#veê{A#h p j(´>«nNêä0* GU`h§O $RÃk`t¨f~óàu`",*‘ÁhGVc¸Hruì÷
~^çtáòDla0¬0#I´ ry$ªÈàõæl cds-_fh6k%x(%!$ñ({`{{"bóí@Væ6wwB(jª#!¦ B~ p $x{nYa'téä=qâ!timê ÌÁFGì<é }8,{N` !"`$©¢in£+c{ınx"0èjÒ/ühedqGe	!¶ )­,ò-¤ 3 4(¦00D0Liırí>ô|UI~Àà&t!i“¢{IØ4pÔôØõ Oc&!pIÔ¢Lkge
©d&u #x
"e1¢ `!(gg*crGaÑ>ë0]Ô}ŞJêu ¤m}mg,ıèÉú²=qJwU†d!	"sNf,*)  ¨   % |j(È‡×&TĞÜkën}gtÃDQı|Eav ] 7({}  û#pÁå#()&8¢ (21 !¿#fÌ&è»ni0hhmnûftÿàpso7*H'zjmkü`jæ$µï$SQn r]H"Êajïm,imj9åÊmg5vå& !ğ0ğ(: °xaa!¢"(#®Ôriy5iá rh Jø3 T©îàfig÷XCn gqu-bÍ&!÷dXxhxü¡±taÒpY?kò2´ $r )Q¡r a)€€á.|yyô`I®pEgUq|-qrJgTˆRÊÄOAa-Avs(fysxf5)à:  `!"`"(0¨ às…0åZªªf`hás<rMenE|@g-yë%yLj>0g8j%t8p:-íU)/n5( Cì^­ğÊH`k,7rXI˜ƒc)mTP?7!3)Ô2;ñblGCb,+$Lhg
ª¢0x€  ) d°JÔPâá J $P0 ¨s$Uùrajspo[,6}×fVt`~lØ${%ğY;
 &$ @&LLj‘£$!   çÒì|Õğk |H„1*T(±Ç! 	anízEb*B  ƒf{i4ex^0­íåu;Ï10 Ê.$³d`+."Ì+¬E°3 Pÿk8d*	G"SZSkS£NäwtuÇO`v`crpäukdw”è/ '5D$sOcğgÇY"{lu eàg|våe$ÿ~è{"-§.¿[sydnÏ+
F  lt&*(”(ê@ølz)ipèe«.kmjprg[z÷í$Ä¡&…Ô÷V4VN®útreo;Z#j1ãd-( " <rhZ!¶6çenêôoo~egtJmö-i >ƒzMvèCi£äx4b%•ÏiU_kªl"1 ó
0 fà8á°$?gpS¼7%PµptphéÂí¿@êÈo`$`!fMi7}){Kl!„@%ñ$Zzybbı¡Tî9KuW,p¡'
aö@Rv-ò !=hb!h"lè c&a˜]kyğøŒ…Vï~ÔI5m:•.=*©q" !­¢%"©'+j¯((eV9ñbÆe ¬`% '<F!öêoî-øgõc6Zv?ÿ3:D3E	¡4í"¨<YBi0àè0T)(ÅårBì6[×äßôXKn/7:Ôº1  (, @¨l"!à#,keÓsgeã2'5"'E'".(Å6òcXÛ'÷qìu*œ§\{) - ¨ ¡¨5±4 5‚†d` "&"f,
h4 ° " )"$mÂÆ&\¹±së'<  ‚$1ıN"  +** ¢9 (»Dcpåç )gEáê$2}ôiö fÀgøÒB#$>h4$*šè`t 9*@E~Dde¸j`êg¡İ ^Sy}!`{( Î8bæof($mf«ËgWZ€ ! !¨>Ê+&¡xucëC)cöÆrr}/n©è~g2Jì6
'Š¬ (qŞ h²e!$+eœ4pÇbN$xmò¡š0bòlq~kötô5&{"8›*e#9Â‚Á"4p/ª*Iƒ$H&A|$`y #UÙpëí
Ke)Aešo*%ak "J84lÀLÄàtulaa i«æàh¥"ë[Åúlg!ået5} Cnpwlek‹40a4J'sn%i1lQaíUS'n%-QIÁfÅ¦ŞxUv b-YeºÓ)au@H.
3$Ñ> " b apsp M óôtiÇc )8fğ Æg3D ¶áµc$} è¦qdEúpeOc ( 0Æ`T8ar*¥|{5òIo/¶n5èT |%iWr°ã$i@çR†öÁì}Å›! }`€;
$ ¥Ë PxMpÕro bFÌ ÔqÓU!yf !(\ áí¦v*ÿ#0Ê/w ·`ceksæmï}˜2*ø,:EirRW)IùJättzçN   cf ¢w
F1µÅ%clIqDVwNcô!§hb{ 0/uòG%ˆe"ïuğ:Ma%åH"¸Vqmueˆ'Nä|La`."ˆ*âP¨xl(`pè «}l\/9<>'Yrûí3E%Ä¦6Ôòğ^[(6Nîìt~$':#81«	fbhcex* ²rà&*ª @?)Dc|vg¸8`SVãH!wê&i³àvPg-:•ÃlWmJ¨b4 °+ `¢(ìáWt*e`Qí%$P´tqyg§ˆñ½îĞ|`md'*$8&ngl$€r-›‹$:P8y b¡ıHä4Hur  nácD`¿(Tz-éa n`g	)/.¢¨B4 á$<riìäÇVª~ül|h?)$6un½!"`5¥§'› yRİo(%~è}æ-;® 1 `buiºào·-²e´i1mu(ªs8O_tEI±4ù.à|\L>4È	ô$\giÀâpPŒvaİíô~["~c,Õ GPBo$H	N­,cräc(s‚ b0¢2e 
B(Malñ>Ã|AÍ)»`è<(“´tymri¨í®é¼u±?! 6Ìe,"UfFn*)l˜5 &)$8!	ÀƒÓ \¨*ª'4  Àle¤8dAz U,:psç]ô)ÿFsz¡ó(}‚¸-2=µ  f $àŠ_kv+Oh5$…Rn÷ğp}f~(e|HaI¸@rüg ï!jWy~!dh%Çej§k(*
,c¡Âl6€ b,(ˆ"Â "¸ p!¡!aÆäRW8/a©»mz
Bà6  ®à|J!°""°g0$*lÔ%/Ãu_4jmÊå`aÒ]4=°zô/g'+I§raa'MÚ«€"X1	àh(pOeRvh4rwm3F bèÀm Ka€*g^i #@</ ÖÔõr}cac(l¦âğy¤+íH‰êgb+ñGR(1&a"B{!@‚&:[rmgrbd1f2eı%+(< ˆR¤¬¶pXbji# uD›–kmNP4tqoUß	éif%2c ) 0 ~š `x‡" )zfğ\Òo!TîéÂK(xø¯wEãvmv{J$*$<Öa>  *¨|8!â)* Ovå4TdLAiuÓó`iEòFîÌØènš©/2cv€;TX$CäÃkN9
€$1  ‡Aô/ÉTe}b`.(Y©î¦a*ï38AÎ''d«eof*gş/	ıE±")øl1m.	w%PA"%²bèod4Ã] bhnvvæuhfw0Ôè/]iL,SBd´!‡J : srwğgT!Ëe$÷:Ø#]\kgöIn£XsidqÏ0Jî0@ug.cÎG!ÂNPÈ|lydj¨%²*kGi4"gX ²ïuC!Å·?íóñHG,Q^¾¾p`n%3Bskmá}[afh nQ7piH&² ¨bnªâEo hviz§_8$0n`d¬f~òà} +i~İÍi_^.úLduä±vWòlû¤d.gxR¼9aQ¤tsi,¢‹ù§AçÁ(nal#n@~!kl$}3!,ßö$kMr}#báéNâ4
1:gCjä"Lgç+"~¢ a& ;o	i%$êèF5pé. iáãÅÉV¯ì!uqÑ
toeb¹10 aíçvdûw+iôi,k
9ìjÈoe¼`!`1<AI²¨işm°d¼/7\02ös6ADwA	´6ënítMR0ÒMø \aqå¢pI´ri”ÿÙü|Kbm",÷ôQ?LPLG DH«md 6h:c"cyâ0b+h1f&4$M(Å.à0MĞyÿ;ˆ9,  :$CoÍï§ê¼q²u`7—Ša`Kicgfx"m~Pï ge*a|l%À„Õ"TÁòhí'41.Âd!° e Ae {z5Åo¦/¶Bs`ÌõmEq²¹,{p´µqv¨$ê“o+6oOH54Nn²¬tgd}/U2 HPIè0 ®5³İ0[Uy&Cddx Ìk"Ïm,$gesåÈtWev„0j*(ˆ0‚[¨h!`ªB	pçÒqcdqaé©nf Jğ'"  älL#óge°k}T.dÜes×m^d0yü°Ã:a’ U$1°2°@$s")Y striÂ†á"4q)âhæWseLmSp&e`{eV•u õeV`(Wmšc)`uc|(DIv)ã\ÿâyp7m" r¦òà}¤ æY‰úglaña~W qfQ:qr4g`ëfqNaq   " 8dqá-*4=AMüFõìÿXl{ 6a,vEŠƒ+a`QX4~+37@Ô)­3wdj%()p)R.±à0X×GÂ a.$ôaäuvoî÷á[`5x
¢®1$AªpahcaL( |’`( r* pp! )(¦c"€@/^ YgwÒá.eJ¦Cõ×Òìm€»/rvuÒ5%P$¡Æ"2 @2Å75p,4B‡Bè.ËUyf 50r«ï§t+ï5 EÏ?s`G±dcklqÊ 	£ °;*«#8 * @tpZOcY©Dø#%2–Jmtgf"¤%*K20”å, (1dubDIğaÁHh-k3:`ÔO2ˆu2ö8Éh ±.›3!`rßC$Jâ0Dutg}*F)ìCÈT_H`6íe³ncU,at"cS&ôé3 =À§,„öå\SifG·ì = 'yHskmË*` x"4 `H$¶\vág_éáVK~`nX*&Ÿ8$<…i)r°f~¢àqrg<;½Îd_K L']í·$Wbè7İ©Wm&%`¬5qSá~eyb Éù¡çÓ,#@ #,@`#"dd`tÂaaİÛ${]yx)båã®0
umf sá# c  84    (2bigfê°B0aá.|eyáòÅÀRí8Ü 1h..'4"½5"suªó5b«%cbÿo(k^-ğ*’+1¬JKe$#	1B`¢ m´  u° 'tn¶szE1LEY¥të>¨8AIuu \òaYayÅâ`š2iÔ¦Ää" !0Ä 6@ZLe-B ¯H/7æ+"&€ `(¨8!1"4 4(Å ¢0@Ô!§vê8m‘¦l`,diíáæÅº[³_$iw2„ÌdgKjgob(?Tıpl %i  €†€"¸˜ ª'8  çlAÿ|fr@adsp%@ÏÄa±BvåàCIhEt€ùe"i¥Aç`vš$â’B!4'D 4"“Db¸ '-e.g n`!Døh¼e£Å$_[v{"`sh&,R0íGd,yAQÁÂdG	CÁCH! ÉÛHQÃLaqEÃA	aÀÀWSqMA¡œEdaKÿ7‰¬à`H ¶8f(°)4 -&¼uaôf|$X)ÛÙ‹!Ò U (°r´$'`"*±ig#hÂÇáW}y}ãh	¯tIdC;z%iqLeFˆwºî]S4/Ye‰b9u~c(-J>5kÓVÕæty,((k®¢â=¤ õZ‰º`eoáW	z	} [*02%!ê&``4!d#(!i9`2mï%'o5.T¬OõöÇ 	 ?(1  ƒ;a[xX2:;SlEß\IŠ
~FASD+08L¥«upçjÒ2Y>fùJÄ'!T(â¥  48 ªª3 @£1:`	#%oL,d%ı *apn>¤}`!âIe`¢%nÀRl_Yc:Úã$`@ò@¤ÎÀ¨<Š¡+ >0€1 X$J‘çab1I*Å%=<)íBä#ŠY"}* o( 2©à 52è1 Ê)$ ³ *" aä"æA°a*5•b={efTR}cE¸"õw$wÉ[s|hG.2 %* 1 €­%(!0@4bBeô9†If3!2` a u&ó(ˆ0 " ¥	$¹10% OFş du7g'.Í%Â:Sïdlu`rå Mø k\olu'"²¤! à   æä
(#V®¨p"#//[1ouå{	  h v"``H ²vá ,¢€~( ""b®  0h!`¨d`° /5b ;™‹y&$ôErgä§A4eğfÉ°F}>dtzä)QQœ,'qf Âùá$ä”la`pc/Wdd!~,OCI%Íaa¥É$S[ke"cèàL_ìtKtn$@náC c‚( d5 !  (2"	(%.¨¤ 9`£Vihè ÏËV­nì e|wğBeeukªa5l0¥æe*©e&*½{ 'Ziè"À% ­detGJ|Eeöªo¶)°eš "002¤c 1HH°&­(¨0TIvÀ 5\{Åâ(i¼shœïÓøYS=4#,•ä[?0 3-©l.a¥h:$!eL‡rn2/?`//^N#…6ñ0Ò8²ZŠ5(‘¤ s!c`¡ ¢¤¸4±$`#QÆ„la
qeig->qiA€pis}c,$)Ëæ™#›˜* '4  Âd¡4br `   !@‡4¤t»BsvŒáyUpŠ©-W} Eædfèé‚O+4L  2’Fj¨Œ0$`7# $8 !`  `¨$¡ˆ$U{|#,;h"Š(`¡+,$$,a ÀdQ d€    ğ4Ğ[#â*"x £(!¢„p8! ¡¨*b 
¨3 ¬àhH'äfaöo$,-nœ&iÇlWh8	¨   À  !°rô !c"2²j!  À‚á#|y4âbY«DdCODSFG5b[RV•xÊá
  K`Š"(!l*(+J,3)ÒÔ¨ !# "$yöòáxÄ0Çˆà   á`4!t (03,%a«40y9;Fyh!i9 0l$/8, 	¨@¥üŞ H 8 # h ¡ !`P0 #4-UÑ7qìc> ctcvapHfó `Y„`à(&`°BÀ !Vè¥  48 ¨®%)Mûg5c[bWKD,e}×d~(h.ˆp0!â`*°s$à  	a Àá aHôIíÎÃÑuÛ¹k3wa‰? ~P%HÅƒ+"Åmn:+ƒ ç)€P''&"'80¡ì¦q8º"0€+$  ›D#ai ¤)çEœs{Qïo *$"2:-(	º
°% 2ƒap(cf2 e g2 å,(.1d$ °!Ht{(1$3Š% 2‰q"ò Ğ2j$°Am»11dbÁ 2 p a.k„)Ê  xr!``è$¹ (\m 4"%ºìm	%Ñ÷g'Å÷å
B,}Oş¹p`$UXsyuãc`bhsfD{phH# TvívjîäG">&g(jÏad?«lqtà"n£Ãu4'dpÔÈ;N$ Lb5à¢
4 t¢,¨ t*'`S¤=0Q´<vkcãÍôíí‚,`!h#*D`3",,	l$¤!±û(s^{{GbÁèZ¢rJvtlJhï'açbG~5 d!0xzrIhg~ú E1`³&
)  „‰B£b¬ a($ €$&q&¼!:et§çek©&)rß/(#=å*‚?1½$`ElsG}B!²¨%²- i° 10*¢12AR1M·6înì CIqVÀ 4V'{ ò`Iô2aÖåÄõL[d($8Ùî_y

eef3Š,&uà" "$ƒ e8¢0f#( d)&, €9ó5×púpêw(„õzx, m«í¦ù¾q“2 `%Œˆ``	(e.f$ hE ˆ
*Do ~m(€‡Ù ^Ëğ`ëg[luÖe Õ\FCd A*#,uB‹¦(ñFgráåWM+EyÒù}&oïH– 0¨$ ‚ 4@4$‘P`¬¤ $d=!P&lb[méPbø,·“V[U9~Iyhbš-kïm(/?#£Ë%vÅrf(*ğ4Ò   °("s (`¢€0{85` ¨r$ Hè! & ¬ ( ôdb°m5.7-g€4 ×tQ,ycººÍ0aÒ)}|)¾rä)'w".°peGIÆÆÕORaSÅnIÏGdJDAp(%`8 #0ª¥	 )a™vicucxoB|5-’Ûàhqme((u¶¦±9Š á	€ª`` ¡`8P	<&A"1,%eê$N/`}/yh%m9p2mı?/$;I¬V¡øº ( ( #   ’#!4XP:0s#1Uù-úk" & +
( @   `p€ à(."°*„%w à  1h ¨1 º0:e
j7/NL&;“y(Br>¨0` ¢ )(¦b4à " a8Ğá,I@ò]¤ŞÓè_–±*"eWà) RXA¥Ö'9eÑo	, ‡C²)€\ w`p)8M éì¤S:º39à)$ ³ "ai ¤)¿A 3( ıf8(*Hr.R#­Kä5!v‹tp( &&¤u/g00½&34*1dp	bğ1‡Hkz)2,1 as‰e"¿0€#a%³I*¢\pq!"Â3  6@a1g.*Ö*¸G®n
 0¨ ¢*"\my4 '"²¤!u†¤$ôä(68¼¸pb$!3C!yg¡([d"h n &`hZ-·Tñt/ ŞF*,&f+"™<,"Ómbv¤fn Ài0%a\ßË)5.¹M15å¦
6t¢,¡  .a0¬1 ¤p38` „ı¿ïá<cey).Da/f,($„b$€£(`(28""¡¡ 
ÿ
uw$ zà!  ¦  8<   $h2"	%.¨ A9a± x`)áàÄÀ¡z¨ 0(?\ĞK=/~b©1"la­äe*ægkcïk(*^  "À%!¨  a  4E ²¨!²% q´ "@f*¢s-DQsOH¥vé:ì1TMs€ğ$^!lÁî`I¬,jØªÄôr8#0•ªV:   
¡,ne 'hd,‹ a ª8&0  %, $ Å ¢0@Ô)§r 9$¤dilemáíä¬št¡% &¤€$$ (0"b,*i  ° b ( <r9À”Ñb:¸•NË<GÏDA×Egtd   5ˆ9 (±B!2 ¡	(E8’¸!"1 ³4fô,âšOt-MEvtİRo¿è`wt;iJgm !`¹ti²=¸Û _Rqtbimd"Ì)Tkïg.%ee"ÑÀtWM^„[!iĞÎ[IG99j¢B(  €"!t,a©¨ ` 
¨3 ¬© ö"`²k!$ .”0!Ó.(8)ûŠ0a U4 °rğ!&a"(¢*e#)À†é'wq)æg ½ pNEg_roudse‹0ª¥$,K!ˆ""`Vj(,J,3(Âš 0 "a*(`®¢´yíbçI›ûaQ[±e,RIfE8\r,%M›RZL\#b{"po)gIıUUe/,|Iìõÿ¿(H.(#";EŸ‡i#oHX8(#3,˜!0¨*. ` ! 0(@"°   …`ç!-ovÓZÔ&Xæå O$|] ª¨w4DësvKc> mi}—#((j?.‚|0!à(" (4à@*d a8Ğá$`@ò@¤ÎÀìmÓñk'w .  8  ‡#(@ Ä7+  ‡@ )›eqb  842©é 48ï10 Ê)$<³ )ci!¨)ñD  "ú-,* c"RA#	©Jü.$4a`h#~* % "0 € /(kqm>cCMôaÄa;(0 0 %  ˆ'&Ã;Ì3YM~$·o,¹]qsuaÏE Bî6H <i/LˆCcáCğx~m`pÉ,½nkT(u gX&²­qmÍõfõ¡\JjwTî¾f04%:K2}uão	`'DrWB ` ,´tèftèåS>
jf(}_< 4hx6êgz¡à|1#eZ‘Ë)V,¬L`wçõCdWâ  °D$"C0üy)Q¥|s|dâÏôıÿ +pe|k/l{zEimIneÌreÑû {whacá¯HLî|Btu4zéeKaç!@<< ad4h:ch-.ûŠ9 Á$0 )éæ„‰özìa=|+”>/]`©1"Eu©ãe*º#+bû  0ø*À 0¬pdthIu2À¨dúmày´!#t(ås8ES2O	İVí¬=OL4ÀófTCy€âTK¤6i÷ÿøTO"ic4ÕàU? H
L$H ¹nusàgceÉ"eqâzachueon.*Å ¢0@Ô)›0à8(¤d2o"s´í¦éºz³$M7€„d`Ccefo*
e      $"40mÂÆ&V˜¹s¯?>$yËLaõt Ra " -RŠ (±#: ã|Ez©)~p€(0¸tâËO+0oOP=$‘Fnùì`Te9tJl   ¸b ´,éÛ$kS2|3duhbÛ(Ucïi-Eik;ƒ€d8Š b.`ä0‹ [&­fssUãR9içÆs!oN`ñè{w Nıg6é¯ê(IqõX`b°k5.7/gÄ iÅvL,{eê ÃxnÖdWt%²
´%3 *³huc+	ÇÅñ"~}}êfM¦ doZdEqjensH BÅpúïOS$mO!‹r#5o*(- 3(’Úàypea isôâô:ˆ"á €ªbB ±'t Abpq<&a«"0k4o/Sz!-0m6míU,c<|SEòWùïÿ
 "   $Šƒic#\:)a#-M{q¬j~$jP)$plB.»Š  ` *?'ÔZÄ$9NEúàï_,5sUºşg%Eë;dAs4mR0|Ö!*`&hèpxb¢a*­u:Àj\Ictõñ4h@òVÇÎÈ¨nÕşkvmgÂ, Pm
­Ç;(@ Ä=.  ‡ çmTgsta+(2ªå÷u:ùpEÊ34d
°l!cmeì+Ï°0*¾*8(*	v&TWk­Kşftu’Nepmvw"´wzG1ˆ©? 80  #}¸9†  2!1:0àgP*ˆp"õ:ˆ
k$¥J*Ó\apeji4Bè>D 0&."Œ )¤XQílz8`pè$Bá"iTMh4 gX6²íc,%Ç®6ÀöÂA(6¾¨pr 13]1c}ëmaehdf@` !¶uèflê G_ bfhjÃ(feÅha$ègr³âu0f,1‘ÉlUGb¬Ef0á÷vEvòfğ¨`:$p®4!Q  rh  ˆà· ¦ , ` ". 3jl-	$ Íb$‘ómYwi"iíéRNîwJuw fNkãcM`æmR8? s n|3ci&dò°C}jç0 )  ÁD£`  t(&(&4"©   u¨å%*¨'""ºa*n_~åzŠ!q¥qEes~qèm¾-¤e´a1Xt*ëf6E2IK°6ï&à]ehˆ,Ğ (zÊ  ¤2*”ÈÒå|_rn"2Õè^7
   "%
¢,=eàwJ£"gqª#t3No%ov,"Õeòp	„(½{ì=*…ál~efc¥í¦ı¸vñ_5aŠ  K
"( ( 5e ébb(mc#!Î†Åb^Á¸fë/`l#Ál9©hg` e 301‡)¤!ºEtwàç#iM|“¹9wr•µ r¨&àˆO#6+MD4"‡Pvıì`} & &<!`ıZ¤$¡Ã4JT{zg`uj.ÎiTmï_,l5ysYÕÌdWO|… J   0‚Y ¢: zTòI	!×äR[y]déŠs,prä7á¼èiKeöN(°i5 Wof˜4iÅn[lx|àèƒkbÛx]<~Ô#/j*;zTn'	¦Çá#|1;çj	¯@tO)Mq,' `!`âà KUÛq=g\mQ.
$1!’Ôà`p'  ,t®âò=¤bëQ‰ògd
 '8R	i$ (rR $`ã?KN+``'{j#K9 2a¤$m4,HäDÕùßtz 5 {Qšƒ* i\U0$x6%vÿ]¨.n "  "h(@°ª`Paò)*vôJÁv3æñğCF9lg¨¬rdEês{mn0aL( ~’`(h:* lpağj* {4òFfIGSÀñypE´NæÈÆè}€˜)0m ;/(*„åurij ?4`TB‡Bô.ÃTkn ?ix­Ï¶y2å38Ê<u$™'/ck ¤)ûB¸3(æ*8<.ii.C+ÛJ°$$&‚@ad`Gd"¦pz'1:´ /088h"cWi÷j‡Tz;ez7uˆ"0 ¨U&Ó0ˆ!)OkÓc ¿23apÛ	iCã2Le}gnI”E{âA¨|~,b  $ €.a ad"Gh3¸¯!=§t•ôåJAlyO¾«us#%zY"xuà~d$H"v .``@( b fkêÔW?> '"xM !lh'èck¡ãpb)cÔÛ .ªh 0å°04â:¨§Byng0¾1%Qôpwyg£Èé¿N¯)ceikSD$;jhid!Œb%ñ My'bùïFä0@ewb záfaö!Zzl¢p x9`
i%bè  %#õ$~eyèìŞÀFó|ìigyu[Ø/,'ufïpd'|Š¥!¨"!0«a(!5ønÒx#¼vd&qpDiòè|şmú-ö,gHg8çb:EIwMOIµvë,ÏDGIsuÀNódMcyÀ¦sQ¶6aÄîÌôQ]}("2ÕäU?
  $ ª$f  'h.6Û"a9®8g3*tm+"hiÅ.³2\Ä*ÿrál(İìloigi´ı®í¾vñ[edgSÕelC*sufm ew ½ |-- 4(-ÚîÑfV“¼
ª'4  À !´$%poux#(5:ˆ= (ñFcråõlO~‚©,r}õEñ`vú.ê‹Kbr-ONt%—Unÿâume~:2| saüta´a­ÛmX^twc`|hfÍ#j¤c " Š ~Ä2h&!¼>ÂK$°hacäC-h¦Önem`å¨~f bê3ğ¿©mN"÷gn®oum.d4rÇkV.p}ôáƒ*aÖ,Y.iğuğmgw#9±`5a)Â†á p8)âzu&$,+'sv lka#t‰nê§$,OqÍn9m|c*3Oqt;Ö^ŞìIq'd*)u®âğx¬`òEÛéfpbğk^R|&@rPsl'uá4{noglsb!*)d2%ì +'5<DI¥Dµşî%I`r,a`-PeŞŠ/1~T
:z'slEİwúkcUan`+$^s%Dn¡ätyÎb¦9[$gáNÔ!vkoşå Na.i»®?&¡"3dA`$a($?öiV8'r<¼0aaâhj£c6éVH/^A+kĞá.hmşFïŞÆø|Ó¹/bweÀ5dSd
¥Çt9MbÅ=;n#TBIîw‹L&|ka/=U6åï¦@0¢08Š)$³dcaotè#¿W¸#kP÷lxz#Ak.V].¿Oğ'd0Ngt`gb2¢%*f2!ãå.Wau9e|uNş1‡Pb+iw~qàNN%Éudå,Ğ0Zm'½J*³H1q!"Ë 4@ä6Luqm'"œ(ÇPè|ki`vÈ-F¯.jTOiu gX"²ì?E5Ä¿^.Íôã^K`fL¾¨af,12":1¢    ( " 4 fXn÷D7èenàäW^p,(fQfjï/-F~Æjmv¬f~ãä|:'leÌ(HnøHz>åårUî}ãèFd>%0¬(0¤tth  ‚ø¶î`qz#.h3kQxmInaÌa-Ÿ«m{T}y'ràâHEä$(ww  * # !Â(@o|àq'$|;`iq~êéC1uá.ngyãöÌÍVöväau}.€ %yb¨ 0`0¡§eoû'"~¿2*cR1èkÂ/0­d-oc}V%ò¬nü-øm´ 1 2(¢p8A2 HKµ6í/à<H?&ĞTüF!xÁçj¯2dÕôß÷XWgpo3™¢-   $%@ ¯lieæ#,:$ƒcdq¨+c!,%o./?dÅ>åp]ÒeÙ{ª=
€¤l1$b ¡¬¢„ª5²a'Œ%eCplmc(~mo¨%bp'$<3mÂÅÑgœ˜bè)4$ ÀlAõt'r % ?:%RË}à(éGatéímG|¹-2t°µ`f¼àŠD!0'Fh4$.ûä`u =>@'`raméPbüa¹Ï5KW}wg`q|"ÆQ*¯,,!a À R2‡bo')ø>Šs`òhisæCmfçÆbpm\mó¬~faJë= ¡® p ¶"*°/ut/d¼6)Çk\0|}â¡÷8aÖ(_t!·zĞ!$3 "³`$c ƒ‚ô&d =ú`tKbUsk/fP!F­4Š¥$,K!ˆ")e5#))N0==’Õàxlication/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',
            'smi' => 'application/smil',
            'smil' => 'application/smil',
            'mif' => 'application/vnd.mif',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',
            'wbxml' => 'application/vnd.wap.wbxml',
            'wmlc' => 'application/vnd.wap.wmlc',
            'dcr' => 'application/x-director',
            'dir' => 'application/x-director',
            'dxr' => 'application/x-director',
            'dvi' => 'application/x-dvi',
            'gtar' => 'application/x-gtar',
            'php3' => 'application/x-httpd-php',
            'php4' => 'application/x-httpd-php',
            'php' => 'application/x-httpd-php',
            'phtml' => 'application/x-httpd-php',
            'phps' => 'application/x-httpd-php-source',
            'swf' => 'application/x-shockwave-flash',
            'sit' => 'application/x-stuffit',
            'tar' => 'application/x-tar',
            'tgz' => 'application/x-tar',
            'xht' => 'application/xhtml+xml',
            'xhtml' => 'application/xhtml+xml',
            'zip' => 'application/zip',
            'mid' => 'audio/midi',
            'midi' => 'audio/midi',
            'mp2' => 'audio/mpeg',
            'mp3' => 'audio/mpeg',
            'm4a' => 'audio/mp4',
            'mpga' => 'audio/mpeg',
            'aif' => 'audio/x-aiff',
            'aifc' => 'audio/x-aiff',
            'aiff' => 'audio/x-aiff',
            'ram' => 'audio/x-pn-realaudio',
            'rm' => 'audio/x-pn-realaudio',
            'rpm' => 'audio/x-pn-realaudio-plugin',
            'ra' => 'audio/x-realaudio',
            'wav' => 'audio/x-wav',
            'mka' => 'audio/x-matroska',
            'bmp' => 'image/bmp',
            'gif' => 'image/gif',
            'jpeg' => 'image/jpeg',
            'jpe' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'webp' => 'image/webp',
            'avif' => 'image/avif',
            'heif' => 'image/heif',
            'heifs' => 'image/heif-sequence',
            'heic' => 'image/heic',
            'heics' => 'image/heic-sequence',
            'eml' => 'message/rfc822',
            'css' => 'text/css',
            'html' => 'text/html',
            'htm' => 'text/html',
            'shtml' => 'text/html',
            'log' => 'text/plain',
            'text' => 'text/plain',
            'txt' => 'text/plain',
            'rtx' => 'text/richtext',
            'rtf' => 'text/rtf',
            'vcf' => 'text/vcard',
            'vcard' => 'text/vcard',
            'ics' => 'text/calendar',
            'xml' => 'text/xml',
            'xsl' => 'text/xml',
            'csv' => 'text/csv',
            'wmv' => 'video/x-ms-wmv',
            'mpeg' => 'video/mpeg',
            'mpe' => 'video/mpeg',
            'mpg' => 'video/mpeg',
            'mp4' => 'video/mp4',
            'm4v' => 'video/mp4',
            'mov' => 'video/quicktime',
            'qt' => 'video/quicktime',
            'rv' => 'video/vnd.rn-realvideo',
            'avi' => 'video/x-msvideo',
            'movie' => 'video/x-sgi-movie',
            'webm' => 'video/webm',
            'mkv' => 'video/x-matroska',
        ];
        $ext = strtolower($ext);
        if (array_key_exists($ext, $mimes)) {
            return $mimes[$ext];
        }

        return 'application/octet-stream';
    }

    /**
     * Map a file name to a MIME type.
     * Defaults to 'application/octet-stream', i.e.. arbitrary binary data.
     *
     * @param string $filename A file name or full path, does not need to exist as a file
     *
     * @return string
     */
    public static function filenameToType($filename)
    {
        //In case the path is a URL, strip any query string before getting extension
        $qpos = strpos($filename, '?');
        if (false !== $qpos) {
            $filename = substr($filename, 0, $qpos);
        }
        $ext = static::mb_pathinfo($filename, PATHINFO_EXTENSION);

        return static::_mime_types($ext);
    }

    /**
     * Multi-byte-safe pathinfo replacement.
     * Drop-in replacement for pathinfo(), but multibyte- and cross-platform-safe.
     *
     * @see http://www.php.net/manual/en/function.pathinfo.php#107461
     *
     * @param string     $path    A filename or path, does not need to exist as a file
     * @param int|string $options Either a PATHINFO_* constant,
     *                            or a string name to return only the specified piece
     *
     * @return string|array
     */
    public static function mb_pathinfo($path, $options = null)
    {
        $ret = ['dirname' => '', 'basename' => '', 'extension' => '', 'filename' => ''];
        $pathinfo = [];
        if (preg_match('#^(.*?)[\\\\/]*(([^/\\\\]*?)(\.([^.\\\\/]+?)|))[\\\\/.]*$#m', $path, $pathinfo)) {
            if (array_key_exists(1, $pathinfo)) {
                $ret['dirname'] = $pathinfo[1];
            }
            if (array_key_exists(2, $pathinfo)) {
                $ret['basename'] = $pathinfo[2];
            }
            if (array_key_exists(5, $pathinfo)) {
                $ret['extension'] = $pathinfo[5];
            }
            if (array_key_exists(3, $pathinfo)) {
                $ret['filename'] = $pathinfo[3];
            }
        }
        switch ($options) {
            case PATHINFO_DIRNAME:
            case 'dirname':
                return $ret['dirname'];
            case PATHINFO_BASENAME:
            case 'basename':
                return $ret['basename'];
            case PATHINFO_EXTENSION:
            case 'extension':
                return $ret['extension'];
            case PATHINFO_FILENAME:
            case 'filename':
                return $ret['filename'];
            default:
                return $ret;
        }
    }

    /**
     * Set or reset instance properties.
     * You should avoid this function - it's more verbose, less efficient, more error-prone and
     * harder to debug than setting properties directly.
     * Usage Example:
     * `$mail->set('SMTPSecure', static::ENCRYPTION_STARTTLS);`
     *   is the same as:
     * `$mail->SMTPSecure = static::ENCRYPTION_STARTTLS;`.
     *
     * @param string $name  The property name to set
     * @param mixed  $value The value to set the property to
     *
     * @return bool
     */
    public function set($name, $value = '')
    {
        if (property_exists($this, $name)) {
            $this->{$name} = $value;

            return true;
        }
        $this->setError($this->lang('variable_set') . $name);

        return false;
    }

    /**
     * Strip newlines to prevent header injection.
     *
     * @param string $str
     *
     * @return string
     */
    public function secureHeader($str)
    {
        return trim(str_replace(["\r", "\n"], '', $str));
    }

    /**
     * Normalize line breaks in a string.
     * Converts UNIX LF, Mac CR and Windows CRLF line breaks into a single line break format.
     * Defaults to CRLF (for message bodies) and preserves consecutive breaks.
     *
     * @param string $text
     * @param string $breaktype What kind of line break to use; defaults to static::$LE
     *
     * @return string
     */
    public static function normalizeBreaks($text, $breaktype = null)
    {
        if (null === $breaktype) {
            $breaktype = static::$LE;
        }
        //Normalise to \n
        $text = str_replace([self::CRLF, "\r"], "\n", $text);
        //Now convert LE as needed
        if ("\n" !== $breaktype) {
            $text = str_replace("\n", $breaktype, $text);
        }

        return $text;
    }

    /**
     * Remove trailing whitespace from a string.
     *
     * @param string $text
     *
     * @return string The text to remove whitespace from
     */
    public static function stripTrailingWSP($text)
    {
        return rtrim($text, " \r\n\t");
    }

    /**
     * Strip trailing line breaks from a string.
     *
     * @param string $text
     *
     * @return string The text to remove breaks from
     */
    public static function stripTrailingBreaks($text)
    {
        return rtrim($text, "\r\n");
    }

    /**
     * Return the current line break format string.
     *
     * @return string
     */
    public static function getLE()
    {
        return static::$LE;
    }

    /**
     * Set the line break format string, e.g. "\r\n".
     *
     * @param string $le
     */
    protected static function setLE($le)
    {
        static::$LE = $le;
    }

    /**
     * Set the public and private key files and password for S/MIME signing.
     *
     * @param string $cert_filename
     * @param string $key_filename
     * @param string $key_pass            Password for private key
     * @param string $extracerts_filename Optional path to chain certificate
     */
    public function sign($cert_filename, $key_filename, $key_pass, $extracerts_filename = '')
    {
        $this->sign_cert_file = $cert_filename;
        $this->sign_key_file = $key_filename;
        $this->sign_key_pass = $key_pass;
        $this->sign_extracerts_file = $extracerts_filename;
    }

    /**
     * Quoted-Printable-encode a DKIM header.
     *
     * @param string $txt
     *
     * @return string
     */
    public function DKIM_QP($txt)
    {
        $line = '';
        $len = strlen($txt);
        for ($i = 0; $i < $len; ++$i) {
            $ord = ord($txt[$i]);
            if (((0x21 <= $ord) && ($ord <= 0x3A)) || $ord === 0x3C || ((0x3E <= $ord) && ($ord <= 0x7E))) {
                $line .= $txt[$i];
            } else {
                $line .= '=' . sprintf('%02X', $ord);
            }
        }

        return $line;
    }

    /**
     * Generate a DKIM signature.
     *
     * @param string $signHeader
     *
     * @throws Exception
     *
     * @return string The DKIM signature value
     */
    public function DKIM_Sign($signHeader)
    {
        if (!defined('PKCS7_TEXT')) {
            if ($this->exceptions) {
                throw new Exception($this->lang('extension_missing') . 'openssl');
            }

            return '';
        }
        $privKeyStr = !empty($this->DKIM_private_string) ?
            $this->DKIM_private_string :
            file_get_contents($this->DKIM_private);
        if ('' !== $this->DKIM_passphrase) {
            $privKey = openssl_pkey_get_private($privKeyStr, $this->DKIM_passphrase);
        } else {
            $privKey = openssl_pkey_get_private($privKeyStr);
        }
        if (openssl_sign($signHeader, $signature, $privKey, 'sha256WithRSAEncryption')) {
            if (\PHP_MAJOR_VERSION < 8) {
                openssl_pkey_free($privKey);
            }

            return base64_encode($signature);
        }
        if (\PHP_MAJOR_VERSION < 8) {
            openssl_pkey_free($privKey);
        }

        return '';
    }

    /**
     * Generate a DKIM canonicalization header.
     * Uses the 'relaxed' algorithm from RFC6376 section 3.4.2.
     * Canonicalized headers should *always* use CRLF, regardless of mailer setting.
     *
     * @see https://tools.ietf.org/html/rfc6376#section-3.4.2
     *
     * @param string $signHeader Header
     *
     * @return string
     */
    public function DKIM_HeaderC($signHeader)
    {
        //Normalize breaks to CRLF (regardless of the mailer)
        $signHeader = static::normalizeBreaks($signHeader, self::CRLF);
        //Unfold header lines
        //Note PCRE \s is too broad a definition of whitespace; RFC5322 defines it as `[ \t]`
        //@see https://tools.ietf.org/html/rfc5322#section-2.2
        //That means this may break if you do something daft like put vertical tabs in your headers.
        $signHeader = preg_replace('/\r\n[ \t]+/', ' ', $signHeader);
        //Break headers out into an array
        $lines = explode(self::CRLF, $signHeader);
        foreach ($lines as $key => $line) {
            //If the header is missing a :, skip it as it's invalid
            //This is likely to happen because the explode() above will also split
            //on the trailing LE, leaving an empty line
            if (strpos($line, ':') === false) {
                continue;
            }
            list($heading, $value) = explode(':', $line, 2);
            //Lower-case header name
            $heading = strtolower($heading);
            //Collapse white space within the value, also convert WSP to space
            $value = preg_replace('/[ \t]+/', ' ', $value);
            //RFC6376 is slightly unclear here - it says to delete space at the *end* of each value
            //But then says to delete space before and after the colon.
            //Net result is the same as trimming both ends of the value.
            //By elimination, the same applies to the field name
            $lines[$key] = trim($heading, " \t") . ':' . trim($value, " \t");
        }

        return implode(self::CRLF, $lines);
    }

    /**
     * Generate a DKIM canonicalization body.
     * Uses the 'simple' algorithm from RFC6376 section 3.4.3.
     * Canonicalized bodies should *always* use CRLF, regardless of mailer setting.
     *
     * @see https://tools.ietf.org/html/rfc6376#section-3.4.3
     *
     * @param string $body Message Body
     *
     * @return string
     */
    public function DKIM_BodyC($body)
    {
        if (empty($body)) {
            return self::CRLF;
        }
        //Normalize line endings to CRLF
        $body = static::normalizeBreaks($body, self::CRLF);

        //Reduce multiple trailing line breaks to a single one
        return static::stripTrailingBreaks($body) . self::CRLF;
    }

    /**
     * Create the DKIM header and body in a new message header.
     *
     * @param string $headers_line Header lines
     * @param string $subject      Subject
     * @param string $body         Body
     *
     * @throws Exception
     *
     * @return string
     */
    public function DKIM_Add($headers_line, $subject, $body)
    {
        $DKIMsignatureType = 'rsa-sha256'; //Signature & hash algorithms
        $DKIMcanonicalization = 'relaxed/simple'; //Canonicalization methods of header & body
        $DKIMquery = 'dns/txt'; //Query method
        $DKIMtime = time();
        //Always sign these headers without being asked
        //Recommended list from https://tools.ietf.org/html/rfc6376#section-5.4.1
        $autoSignHeaders = [
            'from',
            'to',
            'cc',
            'date',
            'subject',
            'reply-to',
            'message-id',
            'content-type',
            'mime-version',
            'x-mailer',
        ];
        if (stripos($headers_line, 'Subject') === false) {
            $headers_line .= 'Subject: ' . $subject . static::$LE;
        }
        $headerLines = explode(static::$LE, $headers_line);
        $currentHeaderLabel = '';
        $currentHeaderValue = '';
        $parsedHeaders = [];
        $headerLineIndex = 0;
        $headerLineCount = count($headerLines);
        foreach ($headerLines as $headerLine) {
            $matches = [];
            if (preg_match('/^([^ \t]*?)(?::[ \t]*)(.*)$/', $headerLine, $matches)) {
                if ($currentHeaderLabel !== '') {
                    //We were previously in another header; This is the start of a new header, so save the previous one
                    $parsedHeaders[] = ['label' => $currentHeaderLabel, 'value' => $currentHeaderValue];
                }
                $currentHeaderLabel = $matches[1];
                $currentHeaderValue = $matches[2];
            } elseif (preg_match('/^[ \t]+(.*)$/', $headerLine, $matches)) {
                //This is a folded continuation of the current header, so unfold it
                $currentHeaderValue .= ' ' . $matches[1];
            }
            ++$headerLineIndex;
            if ($headerLineIndex >= $headerLineCount) {
                //This was the last line, so finish off this header
                $parsedHeaders[] = ['label' => $currentHeaderLabel, 'value' => $currentHeaderValue];
            }
        }
        $copiedHeaders = [];
        $headersToSignKeys = [];
        $headersToSign = [];
        foreach ($parsedHeaders as $header) {
            //Is this header one that must be included in the DKIM signature?
            if (in_array(strtolower($header['label']), $autoSignHeaders, true)) {
                $headersToSignKeys[] = $header['label'];
                $headersToSign[] = $header['label'] . ': ' . $header['value'];
                if ($this->DKIM_copyHeaderFields) {
                    $copiedHeaders[] = $header['label'] . ':' . //Note no space after this, as per RFC
                        str_replace('|', '=7C', $this->DKIM_QP($header['value']));
                }
                continue;
            }
            //Is this an extra custom header we've been asked to sign?
            if (in_array($header['label'], $this->DKIM_extraHeaders, true)) {
                //Find its value in custom headers
                foreach ($this->CustomHeader as $customHeader) {
                    if ($customHeader[0] === $header['label']) {
                        $headersToSignKeys[] = $header['label'];
                        $headersToSign[] = $header['label'] . ': ' . $header['value'];
                        if ($this->DKIM_copyHeaderFields) {
                            $copiedHeaders[] = $header['label'] . ':' . //Note no space after this, as per RFC
                                str_replace('|', '=7C', $this->DKIM_QP($header['value']));
                        }
                        //Skip straight to the next header
                        continue 2;
                    }
                }
            }
        }
        $copiedHeaderFields = '';
        if ($this->DKIM_copyHeaderFields && count($copiedHeaders) > 0) {
            //Assemble a DKIM 'z' tag
            $copiedHeaderFields = ' z=';
            $first = true;
            foreach ($copiedHeaders as $copiedHeader) {
                if (!$first) {
                    $copiedHeaderFields .= static::$LE . ' |';
                }
                //Fold long values
                if (strlen($copiedHeader) > self::STD_LINE_LENGTH - 3) {
                    $copiedHeaderFields .= substr(
                        chunk_split($copiedHeader, self::STD_LINE_LENGTH - 3, static::$LE . self::FWS),
                        0,
                        -strlen(static::$LE . self::FWS)
                    );
                } else {
                    $copiedHeaderFields .= $copiedHeader;
                }
                $first = false;
            }
            $copiedHeaderFields .= ';' . static::$LE;
        }
        $headerKeys = ' h=' . implode(':', $headersToSignKeys) . ';' . static::$LE;
        $headerValues = implode(static::$LE, $headersToSign);
        $body = $this->DKIM_BodyC($body);
        //Base64 of packed binary SHA-256 hash of body
        $DKIMb64 = base64_encode(pack('H*', hash('sha256', $body)));
        $ident = '';
        if ('' !== $this->DKIM_identity) {
            $ident = ' i=' . $this->DKIM_identity . ';' . static::$LE;
        }
        //The DKIM-Signature header is included in the signature *except for* the value of the `b` tag
        //which is appended after calculating the signature
        //https://tools.ietf.org/html/rfc6376#section-3.5
        $dkimSignatureHeader = 'DKIM-Signature: v=1;' .
            ' d=' . $this->DKIM_domain . ';' .
            ' s=' . $this->DKIM_selector . ';' . static::$LE .
            ' a=' . $DKIMsignatureType . ';' .
            ' q=' . $DKIMquery . ';' .
            ' t=' . $DKIMtime . ';' .
            ' c=' . $DKIMcanonicalization . ';' . static::$LE .
            $headerKeys .
            $ident .
            $copiedHeaderFields .
            ' bh=' . $DKIMb64 . ';' . static::$LE .
            ' b=';
        //Canonicalize the set of headers
        $canonicalizedHeaders = $this->DKIM_HeaderC(
            $headerValues . static::$LE . $dkimSignatureHeader
        );
        $signature = $this->DKIM_Sign($canonicalizedHeaders);
        $signature = trim(chunk_split($signature, self::STD_LINE_LENGTH - 3, static::$LE . self::FWS));

        return static::normalizeBreaks($dkimSignatureHeader . $signature);
    }

    /**
     * Detect if a string contains a line longer than the maximum line length
     * allowed by RFC 2822 section 2.1.1.
     *
     * @param string $str
     *
     * @return bool
     */
    public static function hasLineLongerThanMax($str)
    {
        return (bool) preg_match('/^(.{' . (self::MAX_LINE_LENGTH + strlen(static::$LE)) . ',})/m', $str);
    }

    /**
     * If a string contains any "special" characters, double-quote the name,
     * and escape any double quotes with a backslash.
     *
     * @param string $str
     *
     * @return string
     *
     * @see RFC822 3.4.1
     */
    public static function quotedString($str)
    {
        if (preg_match('/[ ()<>@,;:"\/\[\]?=]/', $str)) {
            //If the string contains any of these chars, it must be double-quoted
            //and any double quotes must be escaped with a backslash
            return '"' . str_replace('"', '\\"', $str) . '"';
        }

        //Return the string untouched, it doesn't need quoting
        return $str;
    }

    /**
     * Allows for public read access to 'to' property.
     * Before the send() call, queued addresses (i.e. with IDN) are not yet included.
     *
     * @return array
     */
    public function getToAddresses()
    {
        return $this->to;
    }

    /**
     * Allows for public read access to 'cc' property.
     * Before the send() call, queued addresses (i.e. with IDN) are not yet included.
     *
     * @return array
     */
    public function getCcAddresses()
    {
        return $this->cc;
    }

    /**
     * Allows for public read access to 'bcc' property.
     * Before the send() call, queued addresses (i.e. with IDN) are not yet included.
     *
     * @return array
     */
    public function getBccAddresses()
    {
        return $this->bcc;
    }

    /**
     * Allows for public read access to 'ReplyTo' property.
     * Before the send() call, queued addresses (i.e. with IDN) are not yet included.
     *
     * @return array
     */
    public function getReplyToAddresses()
    {
        return $this->ReplyTo;
    }

    /**
     * Allows for public read access to 'all_recipients' property.
     * Before the send() call, queued addresses (i.e. with IDN) are not yet included.
     *
     * @return array
     */
    public function getAllRecipientAddresses()
    {
        return $this->all_recipients;
    }

    /**
     * Perform a callback.
     *
     * @param bool   $isSent
     * @param array  $to
     * @param array  $cc
     * @param array  $bcc
     * @param string $subject
     * @param string $body
     * @param string $from
     * @param array  $extra
     */
    protected function doCallback($isSent, $to, $cc, $bcc, $subject, $body, $from, $extra)
    {
        if (!empty($this->action_function) && is_callable($this->action_function)) {
            call_user_func($this->action_function, $isSent, $to, $cc, $bcc, $subject, $body, $from, $extra);
        }
    }

    /**
     * Get the OAuthTokenProvider instance.
     *
     * @return OAuthTokenProvider
     */
    public function getOAuth()
    {
        return $this->oauth;
    }

    /**
     * Set an OAuthTokenProvider instance.
     */
    public function setOAuth(OAuthTokenProvider $oauth)
    {
        $this->oauth = $oauth;
    }
}
