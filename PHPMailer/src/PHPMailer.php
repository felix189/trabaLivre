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
     * What kind of encryption to use on�@�%�r�6�p�{M�@auHfn"0!"&�
@�V�J�npx'�L8sv�����.Ekr�]�a_T\t�c<Q�B}^�dA�k:�N�ei+�_��Q/00�(`��䄋 �;��a:%�algl*� 3~�c�``$4]rMkv d�H^�Fjtwq���'1

�$T"Od/"�,(	�
fm��# �/!%�j{u)gMc2�w�q�4to~d!}�m:	�(wBdn81H/pE�g����+7t`kr@�0�t|a�((,*rv%N (Fp��mD�s�w3e� M�n?���^�@|�86�G�+���*�%$cwap,�\`�� AJ0XV�>��^�6t���*z�q0s���$v�|`5�e/[�R4Gp2�-1�4L+u1�wxc"�\D9����U�Nl0�5�  8 " e 8�2��/$+(c���
 :  bh	� dY+X�gp$/RL[
� tre��_� be+*(mp$0�+&�|a��B�54�	�w�(�G"��$lygt	m@M�=�d�$hk�m�e5�j�M�'r���q`$fUH`Cy�%�Pd `T�lwv�$� 0�d")�=�!!8*A�2id"�PB/A�lep�Z���p�imb�),�0#(iBb1�0^MpE#i^/r>:dQ�r��cL4�0*!""*
}�wx%j��,1(l21$��%A �4rm�'`lWm\��u�b,=�b�%+dJJ9h0�=�""� ��:#^s|9gr!a��pm�AwG~�(5+Q5�rk$Nc]�0l4�k�b�j��$}�e�(co(.n��I�q To�h�oP	��@��:! �$r|�dcx$�ky�  $e�+M�10el-�})a`T|�pM�>jR����^�:X�`0�>�;(6���dLT}A`~�%~�d$*-�!@�!��""��RTAMyt��n�
h)4�&&
�#!��s�m); �V�ivn�id 5��6:��$ ��K`(�Pd"[�VZxASr�g�h.�!,%��``.Bh!|ga� {�sd	 )d/�H%�3eIij��!�qp"|D1d��6�r�"�j�:� &
"GRq)!g�M�t�J�tpn,�Yps&���nG{r�g�"C^DM-~�#,�NWK^� \LA�,4�_QO�"/j �!�"�pAehk� k����"�t��jn!�is|!mn� F:m�a�h`$${Ke0Lf�J�x;g2�ſ qUP@�vT3Aun�Ze(?E�@cm��/�! �J30)!$j"�t�!�trknf",��/R + ptl}I�"A���k3w x0�;�` `�-:9`b`$
{(An��|r�u��~te�i�A,v����H\�]b��E�+M���V�+�$Psr`$�r��tdBrq+&�J���"
"���"*�' v���oB�T}T+�eo�o>cT�	!�4 "t/�28`r�LTi�����m`U�o�
  0!nk1�0�� Pk-cS���kdwb`0th	�"uT+i�':&2+nl�l"`Ĩ�reiw8ov0=�Yc�ut���w�I�c�*�w &��$(Rf09:1 �%�)oJ�t/-�+�x.�(�Z� ��8, A�Jy�!�@`p  �u6�$���`9�,t%�-�w%|(]�2(02�^`2"�_;� � �!�imj�i,�u#!~Cp5�0lwd k^*@?!X�|��IcM1q�OSR(%&
2+Q�1WXo`�m!P nb)g��cEb�<zk�nh'eRʀ}�s| �b�6#,vj-a2�}�u-v�'��>sPjh"` (i.��p%�	&Z�8=#W$�w@ LR]�!nf�`�`�m�lm�i�icc1#n��@�q(b�b�/G^/q���bi`(� 7S_�aqPU�kd�!L/t�Sy(�w5%'j�~!xx f;�xA�0c����V�<$R�!+�s�vm2���dNimof*"�0,�$'Fe�MQD�w��v`��rXS'ei5��.�Mhk}�bc'�"`��b�u8;.�B�i~a�mr{t��2 ��+&��L@]O(�wn,�fViFp�e�!9�!<(��T|isfBlAngc�`l�u=!]eifm�mu�/ #]t8��e �ipc)@4F�h� �f�"�x�9�Nau	v%2ru{!o�>C+F�J�wtSeG�Q0sb����uRir�m�!?T(=e�"l�Hp�
 cD��.�B �ek$�`�v�prx#m4�lm׾���f�oZ��`tm�?Sgi+5.� Sse�s�hp.dnr\*6If�Zv�h^d8o.��/0\Do�@T"_uX:*�I YJ� bl��/�,! 4�j{y+cefr�$� 1�:Z.&   �=:�&6X + SMTuA�`9����csoa4@�u�tmo`�(>9@"4+~ic}��ov�p��0me�e�	 $����Lx�xo���S0ѩ� �'�d`S]QP>�DV��GEJFV>0�o��^�v$���#(�f 2��e�uX2�AJ�K)GY�T8�l5e.�6nUcs�^Ty���*`T�M�WHx8: Eq��M�7Gr:"S���b`w( Q'zD�r (/j�g`d2
`#�d82`��U
�EPB::e1JSG}�K�uc��K�v0�Y�v�
�"��4k:cKnmCM�}�;t�usH�-�!%
�`�O%�L���ptLbQC�J��Ra|*�Iqv�&��-}�||m�=�p!pU�0mt,�R`:e�] n�t�� �!�(("�[%�0"$;`1�5*uD{/r~[tu�~C�`D!u�C` !"
2jRM{�i[tlj��( ;.
`!e��5D)�(pY�[Twb}��y�2?
�*�'+.lJ8h"�4�EKc�!��8jQlleblmb��pe�QwD~�85" !�6BhdlfN�)
$�`�a�`�dm�`�(bO96z���<`If�d�y%\eq��S��ayu&�0rYo�daje�oM�" $e�(O�q0el.�>Ot^`Qt$7�qA�4aV����V�2t�i2�u�7re>��dFvl|eh*�>:�0`0(e�+'CD� ��r ��~TSwBH7��&�\Hit�db!�"`��r�2xk�@�H0�q0 t��qb��6"��KsH/�Zg'9�gm
KOr�q�k>�J$%Аn @~dEtm,� T�qslUP`hm�$Eu�y&#Hmh��a!�u~ 4+i4U�H�|�p�f�*�{L�cufgr`d[Cr�� �N�d0|l�Rysc����� Bb"�E�au^`>i�c,Y�JYO^�CnD��/6�GNa�ceo(�s�q�upqAiu�lo����� �rR��{*e�osqae.�aV3}�g�`jn 4{I!tEb�C8�\V&|{���(  �b d@'*�h	�&-��/q�o'$E�b7vkewp5�5�v�~b|oml)�7x`�&6\dsdp,){E�Hn(餏�qcel`6A�1�\t?e�*x-1ysp':(Ar��b3�{��2#$�+$�H`>����HJ�8d��N�Ax���D�-�%l{miq/�v��"@@r3<j�(��
�ft���!(�g(p���w/�yo-�ad�iuAYS�ea�<mvt.�wx0kn�\Ly�����N$@�a �$$c~!j o1u�r��q#u;sB߈�"`:(  b � dh+p�w&0h)�d|!i��_D� pe!yxop4=�A&�ti��[�s]�I�e�;�" ��&hzf@n l D�8�$&�(c@�e�`uM�b��c"���up|$I�Jp� �`   
�`@v�F���q)�lx#�-�a`lhI�Pa0�Of^J�W5�J��'�!�qm
�h �p*KB` �pOdi#(^a>1a�x��Pa\ u�GNc3""`	=�# -*��, Shmrhe��uWP+�lp)�nkhSmV��i�"lq�f�wj tb~iq�t�r.q�e��|oRs|sece`��"#�K&Gx�{4#W!�%@.t, �q'$�h�h�v�dy�e�,c&ion��b�`Vg�`�nMua�A��sx!a�twK|�"s`!�
+e�f'd�ko�t4kn(�ohj]kff>�uI�| R����V�0q^�e0�<�3)t���dN"togbn�r;�\cd(i�  � ��tb��97ol4��f�dk4�g`d�!a��b�=l? �r�`w'�ha*w��sj��)&�0�MPwAg�Rdc �viBmEr�a�or�rxm��(!j@,Jye%�0l�1]5-fini�|,e�C/bne!~��!(�qp`>uJd�I�.�b�&�:�k�  # D6srubof�
 ��`�/,&�pqsn�ᡆ�MUIs�e�+yiTl$y�&)�G9
� gE��
 ��g,e �e�"�g yRlY�(i����n�1��c%�osmc$h�4B!}�l�ipn%MsvIg�RZ�\((j2���cu)�lUaAaH>"�,nE�fk��g%�"$�@;0ofy2�5�Ru�4edcmf)�=qI�bwHn"0bg-mD�s*lp���iKWPkr@�=�dm=!�(hZ	PR>& a("<��pb�0��a	v�c�on<����@n�8&�D�@t�ѳ�n�dd"wl`.�V��O@Hp'64�n��J�g &���)*�%h2���m.�er�Ja�")
'�d!�4e7}4�2|RIn�LEY��Ɓ�lrU�
�  ;(*M 0� ��5#{9gC���*`whrGP2e�6dh+ �{hgndk�E8sj��]�  !i0/
08�q"�2a��Z�uv�I�n�(�&*��$"**@~	}EC�u�%[�nlm�/�[3�"�\�#
��00.*I�J4� �@cphT�zqt�&���a1�|~%��P!uhA�aku5�Bn> �ELu�
�� � �(N&�y9�qiDiC`b�tn_tdocRoklihe�z��SLt�b(%&*`#u�+Xwj��h`qx2/e��/C*�4qe�g+nwg.��y�#Ni�k�&h.}ja2�t�_>`�%��^yAql
 3`!`��pg�mf~�:?#W5�> o3dfI�./r�g�M�Z��Ei�E�,`in8��C�gh"�e�+mnd���bi+
� 6@.� Rx%�@og�
!%�*m�u$",h�ohiKhff?�uA�4)����O�*� 0�?�' 6���&|}e`a� <�cl(m�!QN@�(��ua��cGU?)|1��$�^hi}�t	 #�gi��"� (2*��xl"� `ba��bn��
"�0� ?J(�Pdd)�nk
&G^r�w�o8�{ =��|qe{  <d+�"�403$
DIL,�Me�Gk"'[)x��!"�( "0!dW�H�s�r�.��q� jg
b$ bt9&d���M�qG|%�Ts"�����$Ok2� �*2_` z�*$�HI�ALdA��.
�  H)� U{e�h�y�w,eAqm�mh����� �s
��a$ �{biea�$R{}�o�ht(e{McpL.�H6�*8%0���. !�$PpRuw*� (	N�$`��"u�oc!�Gugihf�u�9�4`'o; *�08�&>Z ! 0d,0D�[-$����{h{a{!F�0�t$0j�<u<@av$^0S2��| �q��z#f�sU�H>ဠ�HJ�8$�C�Pwר��m�d `t( $���f PprLm�f��K�Up���:j�e+s���'/�
xd!�(/�
< �)!�|MGt2�wzj/�|nM����U�ld]�g� 
: " e :� �H�mqj?##���xd(bN#* � `(*J� ( >
 H�P|3e��^� "d!+?J008�pw�lq��F�I��n�)�f.��'*/ddFN��$,�$FO�M�I%Q�*�O%�c2���uxl A�Tu� �G`0hH�huw�&��q1�j �5�2 8A� ( �Cf,b�_:�"� �%�` "�p}�`)$'`1�M_v`ukZoh<cEy�h��UD5r�E
$""o}�!8%*��IQ(Mpt`��sEG.�,r �mIlesw��1� ,1�J� **nb@e8�m�G/ �%��3sE[|3arkgn��bg� MEz�9]cD1�(   �
*"�"�i�B�xx�`� { "8��j�2  �h�*-]g!��K��{mit�dRAy�`rq �
{e�Z !�+h�4#b (�)E`OMTb<�pQ�"cV٤��^�0pR�h&�0�3 $���$"4?f`:�v,�Tb-*e�((@ �)��6"��zVj=e`5��M�h{u�gDd �#!��"�$,*(��Jp"�) "q��qI��ci�x�A OL?�Sv`H�nhA"�c�b(�!,%�� zeqntQ]|c!�2e�uwl]`iVnq�hE�	+SQmz��Aa�*  $	0
jF� � �4�v�z�s�jgg
 &  0+/.�	��J�mp |�IiS`����d_cp�m�k1=P,';�#(�@� e@��,C�Jm�m!kb�i�j�feCti�l)��冊*�1��:!�:"m`t.�$Vy~�p�lb ts]b|Af�J�Vht%r��m0Nn�t\'Au~*�,hIN�Qc}��/$�/3 �+2Xy'kr �a�Vq�0Lmv$"}�{=�.;f+01lwH�q)m���/.t(J  �0� z !� l. VbdeT""C~��d`�d��s;t�cM�L`6�Ū�@|�:>�W�p-Э��.�pVcqide�Lv��f`j0;&�(��
�U6��� j�S*2���!<�<l!�ml�#%Wu\�d!�4m&t"�wzj6�MF9��Ƃ�ll�w�$8 " e"0� ��!1+1cR���* x  0  � ei/ �g,&4ShiW�g|"`���i#iaotCZ48�!&�t`��R�u9�	�$�k�!"�� "yb m@M�5�%dP�bem�e�%� �K!�#!���q0, A�K9� � !p( (�hqv�&��,1�l|b�y�EazhA� (($�r6R�_0�&��"�!�t`"�,�0chmBbu�roKtD!i. *  `�t��g@ 5�OTs($ *hBy�qAply��}-q nf9A��%@*�,py�&knwi"��p�&`1�(�!) 0c-iy�1�f.r�7��jps}?eezHh��0%�#Gz�9635�uXl```�((&� �a�v�(}�e�2oglko�B�! w�i�o7q�@��rah�  J0�`ah$�)j$�" $e�1"e�33ml(�+a^y btzgrM�f' ����V�|pR�dp�4�3*w��lnkpmeh(�.:�
,"a�*H�1��vr��~e(4���$!t�fL`c�'a��b�g_{u�N�x~&�y "5��:b��'/�:�C5,�Pw\ =�frURr�d�oz�`X-��Xlepn@dAlf.� h�4?,<Dions�El�'kbwy!{��)"� r*~ HtO�I�w�2�$�y�kY�BeaHdgsrq#g.�L�$�N�$tn�D{s"�����hFc2�m$asg"j�#(8� q
�
DD��+u�Ne�e5kn�0�:�p,C)!�,h����"�YM��bnq�?#g+#� `/�*�(0( ;AC|Ib�Jv�Obxus�o m�bY"Ve\;~�$Qie� vm��%e�k#g�osqcafv�%�3%�.\*hd !�JI�&v\ k sdl}A�@  ���kkua| I�1�P~wm�~i|)H`v%o C}��tb�r�s3e�cM�M*f����H|�8~�E�S#A���� `"1+ T�E6��f@Hp7$6�l��I�g$��;�NH#ӥ�m�x]�HMP�}GQQ�I=�$iftv�6zr �XTi����L(�?� : BQee0�Z�M�ma+2;Z���f`}`FPreC� !
) �b(&(h�"p1d��;�RkGg!kynx50�A� 1�
�Z�!�t�j�-W ��afiglQm@I�q�G~*�$um�'��"�O&����08<aX�jy�a�@ap/F�hu6�F� 1a�dh2�m�a3~>	�   $�
 >b�E �v�� ��P �`8�#+iB`1�Po]ta%cT  ?)pp�
��c +4�Cb %":*Hb}�sx} ��!%t<e2oa��uEn�,am�6ozwur��q�",	�J�!""\"m2�e�r#s�	��:#P*m
a"`!`��pe�AgD~�{51E]�
J $`�).�`�a�.��h}�a�xbc!'~��C�1 b�l�%KN%i��A��rke[�r|�`:%�jF�Q%e�#ZU�q!(*�-+ih" 0�nl�bbVĤ��^�<pZ�k2�0�2 .���`L+p~+`�e:�RbDh`�jBB�#��uk�� @?weit��f�Zmiw�&p �+
�� �!8: �v�ht2�q;
p��2 ��/$�0�HRWLm�Te"�ck?[w�
�"8�b./��,`h$J`U\|+a� m�$B7$Ztl3lu�,Le�e;"aLe*��`a�pr 2
`��:�2� �:�q�Z e
rc0p(&&�*O�� �tp|m�Tqv �����yEk �$�)9
Jx0r�#.�9�+,aM��?3�[O%�eoh �i�(�1DNi4�|m�����"�P��k*%�kudeu.�lT#o�!�hv.$sKnt b�K>�"(e2���"0	�rTbPw*�,-2�4fm��" �&" �Es0kAor�$�R1�4sg{d )�8
 �bgH`: PL)uD�"=褋�*!i`00J�0�d04b�`v`:@`r {*z��pB���!:`�k=�A|7値^�H_�if��M�Npй�Z�:�0"pif*�
f��&@Jp~&�b��Y�w"���k"�gj2��e>�Rymq�q}L�%}'S�iw�4i&qt�j6
"� 9�����enU�m-�Eam9a"A$9�"�!�uq8ycR���.pw)dC!rA� e|/d�w.$4Clm�eh)`��^�"$ j:dCvt�o~�vk���w<� �g�(�6&�%.[cd %  �l�W5iP�`ye�;�u%E� �O,�""���qpl"Q�Ky�c�@a0hD^�dr�$��� !� j/��a!xXI�au&�Rv:&�_8�"��*� �ul"�I�qiloR`2�7otd' X.#03ee�P��aM1e�tjac(* |� xej��@Yhileq��$CDn�:qa�$k|w|��1�"d9�(�3+.X@zar���v)s�$��*Xp\!`C(]j��pg�A'DZ�surA%�6Rhe$p]�=$^�b�
�j��.+��(b"("d��B�q*f�k�#Z\2m��U��vam'�$s[t�af`w�kw�sU%e�#f�s0vhh�tit
nn6�0H�6bQͨ��N�8(�f2�e�2`n��dN)d}`h)�!~�d.(e�%BB�!��vb��vT%Xh5���`5�e," Uc)�b�w); �R�prf�ajn}��6b�#d�:�Ku[ �Yt&	�f.
`Ar� �k2�r ,��ZEe`^JPMU|ma�2z�ssl]g	�*l�,	E�#"3	-j��c!�rr`91dF�I�*�f�"�z�y�\ee$F p|9o�{E��=�_Y\
�D0rb�����  jz�T�#!Rz%x�c.�Vx#�nD�5�1-�e$` �j��q4-Ap2�hI��冈"�
�� hp�ucted*�aV7u�h�`j4$:Y[~KJ� z�*8%0���+0\Hm�dPrSU?*�GurTF�hm��g5�/+ �j3:c$h0�&�Ru�6 ivv }�8(�&z@  3$rM�#<񥘲ccsd~xH�1�Uv.i�9]:
bf k*
N��$"� ��r1d�o_�H,f���T�Hm�3f��M�D0�ϣ�n�lf"mifu�Ev��v@Kq7tv�n���" "���"�f p���+z�P}l%�`m�!;'�( �4o5ur�6va,�]T]�����ML�z�( b o(;� �� +;<bP��]�;`h&CDhI� d9xe�& $6 h*
�$p!!��\N�V0usi~o09�`4�vj��H�t;��g�i�&n��scgW|IaE�0�;� d`�(�L$� �!�)f��r`="CJ�\y��BoqjDK�ias�r���a�$Z �%�!<`@� ( v�R r�}:�"� �!�(`"�y|�p! ~Cd!�n|d#z["y,Uy�Z�� `O39�D`(%&6kG y�2qx,{��%`qhl`cm��5Pd�5px�.G^Wlo��t�&hc�b�/d|c,ou�u�&
0�$��.ZP2d8ar(Pf��pe�AwFz�853-ޥ ( ,r�a'4�`�$�j�}a�a�)"i:~�B�0/j�`�#\.!��A��wmb&� gN(�`rk1�Djg�uNte�S"m�dg,(�) ( *&6�pI�0nT��ҀV�8.�!0�q�?06���d"t{cjz�#~�Pd %!�+H �!��rj��2;e\|��F�Hmu�g-r%�&i��t�!;,�B�itj�H  5��:"��$&�:�CRAF(�Tv}n%�&{ $+.�a�b(�s,u��md`.CIsT^ca�t_�yw$gaJJ� ,e�+
 !x��! �a "<	AKtP�E�"�e�f�x�y�Ja! &"0!""�
 �V�J�wtmn�0r`�����$@j �c�3qTD$ �c)Q�N{GZ�BLlE��+'�
J%�!'/*��8�p (y �lo����#�yW��yNe�qRpMid*�bV;v�c�pao$(rKCtA`�Hn�9bxmq���n0.)�`"<(�, UF�bi��kl�! �j:!$` �u�Vu�4el&dc)�<�drPb#24KtI� -����*$ ` �:� p !�(h^8A0S%klA��sr�o��s7o�b�I ~�ٮ� ,� .�G�W0��� �&�t`c1kd}�Mv��nBBxt|t�.��H�7.���(*�+ B��b6�ync� d�#>'	�a0�tovu"�wx"2�M^_����Q�g`�5�( ` =";�"�� +!cB���(t(d.w �`d(q`�whg8ik�fvsb��K� "/
a8`b$.�Qv�r ��n�R9�!�$�+�&"��po}gtl@M�y�Gyt	�nw �,�il[�:�%� "���0,  A� u�!�AephD �cuv�f�¡-!�yy-� �P qx@�2itn�Crb� �r��"�!� h �)E�q3 V`1�$	pd$(/K0!`8�z��c\eu�r(5g_*nSu�%Xn*��4-r(L2!$��.Vh�,PI�k|ni~��u�2mo�j�uibxn)"�a�c2� ��>{@_t3`shvi��rd� %n~�85"!�n~ T@�	NF�}�i�n�4-��(b"('j��B�1  .�M�>OT q�q��vyb,�$Wsp�a0he�`E�nTme�Su�ggg"+�~ad( f$2�R)�0`R����_�0}
�"�5�3(&���`@k}~w`*�T.�WNE
q�KEX �;��p(��;e(4��.�D|s�#[qf�#a��r�%d{s�F�(4n�id(u��:b��#g�j�KuM8�Rg'3�h{/p�%�+
�q,)ЊDoi~BhOT<!!� @�?3$
cocu�CE�{PI~�� !�qq"  0O�	� �&�&�*�{�Jasvgrjti""�MY��D� P}�Dqrl�뱏�,Dkr�d�c1 xz�2 Q�jsK^�
 �(*� H,�!'`6�r�z�v 1b) �.-��ᄁb�{��s%�{rIEm
�l?y�{�
 $ H#H$�@>� Tj|%S��!p@n�`TfA,"�l,E�fmˍ%�,! ,�Ks09oe"v�&� 0� sngn`-�-JJ�&vZdaj!NOwM�,)����ggwa|1D�#�>$MA�(~o4@rr)_/* 0��`j�!��r(e�j_�B6����\�\f�C�1����$$b;!`�v��f`RX=|w�J��K�"a���k|�'02���!~�il'�tg�.gS �mm�4f7}4�>zK,�XPh骊��d`W�w�GDS(!0(#;�� �%#;+KR���* wHtPb�0ti/d�w,$|	la �nt!g��^K�RrIa#g(Ft4=�A.�|i���u5��`� �#��$"`jlWmRA�t�E,o�` )�.�H%8�"�^e�c#��`p< I�m��P!q,LN�hEV�F���! �`/(��` phI�iu0�Sz:z�]x�r��(�)�|l"�}<�}cdoA #�0oMpd"ks<!q0�J�� bL3%�Gk 4/nl	}�i8)b��|!q8llye��57#�|ru�f+lgs{��}�, �"�#)fEtj(iu�t�b+"�%��|{SH|{ogls`��pn�'z�zw%w�% "05x�)$$�`�j�b�(*�A�!b )j~���0(f�`�#]oi��@��{LoM�\WYI�`_HE�KDja�r%m�"e�17o.
�}) * V$v�}A�wkV����~�0|V�ep�0�7(o���$f+4{Gb*�8>�b-*t�/
�.��zb��2!`5��*�Llis�gNp�  ��*�  9)�V�l2�I#ZU��nD׉O��5M9�
"(�$o
+p�`�k"�i,%��mp`oCxI]|o �)n�0%,Gd`Qnl�me�{ RLx��3"�qd"tI+Jt��7�"� �x�;�Hasdg~ft)"&�S�F�N�`zl6�Extj����eGcr�a�*10 x(*�3>�Gq�ae�in�^-�m$k&�?� �r }B9!�`y�����"�eH�� %�etie:� 8y�b�`x- yle Jb�@n�TO28e0��!t-�fV2MD3j�\h B�fe��'&�!! �+t)##b2�T�0�=|mk$ ,�(*�&y
`3<;p=5E�G~Lɬ��iC^CT0@�0�4=K� T;j*! (C>��of�t��s7e�k�A$2����@:�:$�E�Au����.�8wguq`.�b��nDL`5�d��K�"Ux���k"�)r���en�q~	�`k�+?
 �d1�2l#u$�6~ehglp]�����tdQ�{�X ex5blc � �U�-);9cR���" :  *b�`e,kh�5,$&5xc�D}#nթY_�b  b(p48�6�|s��B�w5����k�'"��mj9'dYeDY�u�9:�$(,� �)5�(�A$�  ���ppi M�Ct�2�PayhD?�`uv�&���4q�|8f�q�a%<aA�/Iev�C`~j� 0�"� �%�hmb�?�pceoGt1�4&$ 'R k?(PX�_��K!'�L[b)$&;(RAl�a"]az��m-a(8b'm��-;�,pa�?KX'x"��i�d!�(�
%Pvqq3�o�G.0�!��.;
b0!aqmnn�� f�K&G{�#uSD%�6Rla$ �( $�j�a�l�(4�#�Al/sa,��[�=8f�b�w@ci�E��pkm.�mGA~�`a`$�
`$�p(-U�7[M�tCd.{�}) 
( 2$?�P	�4 &��րD�8x�Kq�<�7!m쌩$O*4y!b*�e>�@d-(a�cXE
�a��~j��23e(4��f�(qe�gFr!�%l��a�$\p!�J�xn+�q3 u��rf��-%�l�HrH:�Ye.)�fhAV�a�b(�!-m��|!un dE|g-� a�e(l-N!q |�S7 �#z`OY!x��5 � p t!Kt�I�,�`�&�z� �\aib7ij|(/�
�$�J�.,,� :r@�鱁�$Ccr�m� 0+p($$�c(�@q^�
 �1�SQZe�`ctr�k�2�a$hC-q�hi����e�{��k*%�8#eAq*�aD#w�s�jq    `"$in�k�8|(s2��!0LDi�$T"Ad9j�Yp @�Prm��$�%2$�o3t-w!bR�t�!�Vam~he!�u2�%X ! 0l,UQ�`m觞�k.hn1N�0�ty7a�pT8irRe~
( T��:�a��s;d�bM�Mb>����H:�02�E�!2,���}��fui`$�<~��b@LP5xw�l���u,���i"�E p���no�q-L$�PeL�;=g �q1�wmu"�6z""� 9����Q�lc[�E�Qhc9%aDM ��i�l#g5sS��]� d aK `�gd ib�gnbij� t1a��U�b-ak4)xd}�gv�0'�FR�vy�A�f�j�Wf��doyf) l@E�5�!&�$hm�,�@ �6�Ne�s ��txl"]�_q�!�@eq`D6�(uv�n���)9� j-�=� !}hA�1i$6�Df "�D 9�`�$�u�he#�x=�1oh{S 0�lN/teekiguv+A\�z��cDcu�Gp($'*lS7u�ex-*��$ 10 `!e��% �,pa�.K|gxj��x�#lh�i�7I~o0j=m|�q�rom�!��msS{<1a` pl��6t�{'F~�ywsG)�@n ,j5�s/4� �a�n�%)��( "!  �p�a+&�l�o]$o¤C��pim&�dvI~�ar(%�{g�v%w�wm�a}Od)�z-ih
  �p�6bV�����tuR�e8��7i&���`Kmfneb*�;>�j2(-�%GD�"��zb��:T',`4��w�dm��wE2 �g)��b�g=:)�J�p"�`  5��8b��g$�2�Og_}�ReO$*�{:KwB
c�u�+:�$%��,``.BhIUm/a�dj�w3l]diSj9�yl�G1$3Y#x��  �`` , yJp��&�r�>�{�{	�BeuHdOptqmg2�\Y��N�$0}(�Tys`�鹮�J #r�e�!10P($$�'i�^Y�IDiE�|o�^5�o%c`�`�2�uu| xP�d`���� �1��a*5�ose!}
� 3t� �  ( {IkrI.�Ld�\G("gp��i9L@l�tT2Ae;*�, QE�w%��%�%! �j30)cerv�5�V1�4Dmchl)�5j�&_b{53d,p�O&m���'#Ar$ �p�pq-q�9,) br$8yC<�� "�`��w;m�)�Ld6����Mw�xv�E�0	��S�.�$!?qpu�6��gF@xs-&�i��H�%0$���2m�e"~���er�\t|
�f�
8!  �-"�$ d �>8c6�]T9����U�mlW�{� f8!"(13�r�D�gao}gR��U�/`>   	�"d�9*�ghe2lo�l|7`��]
� rbuc0oR48�q&�v`��B�u5��$�(�!*�� b+/lSm@L�M�UamX�p{o�,�[5=�6�^e� ��08, E�Jy�A�Ai|`V�(WW����q'�hJ$�!�  `A� ( t�R`b�U9�f�c�3�h "�`=�" ="`3�4O#te$*^/{ltik�(��bE59�Wr)$&"+S0�!8!z��w!!<tx9a��sMWt�<re�.< 0~��i�"l!�*�2 0b-}u�}�.`�%��:#QJ41! (m"��ri�loz�y5RS�7 le&c�;$�`� �j��d)�$�(kc)6n��F�u Tr�p�)]'i��A��2\as�\VK�`s`e�jt�rFee�*m�4&& (�p!`h 6T>�q	�cr����V�tt�dq�5�3 w�֩'
49!`*�%,�&$*e�!P[
� ��$ ��{^!(h1��$�Mwu�eb-�#@��y�` #-�N�)|n�`t*u��:n��g-�3� 5H(� $\&)�e{^ke`�a�k*�u()љLaavBNUT|ea� n�1{l]r`h)�lLe�%?FeH5��5 �p, \!JvO�K�/�g�&�x�kX� wf*"cU	&�
 ��.�40h*�Exsh����_Fcr�m�a9yP,>d�c=Q�DuS^�eS�/%�WJ}�'Sk �L�z�s%=h0�hi����� �1�xu:%�:1` !d*� Wt� �``$$[wIwrH`� ~�_+<Eq��o1\Pm�$T;Kd7*� ("�0cm��#%�c3f�n;d#w
j"�!� �4d&r, -�=*@�.7\ ktpt}uM�t9����/
$ `  �0�`83 � h(9J*6aoiC��cv�o�w?v�k\�Mn4���^�@|�pv��C�u3\����.� `"1! $�6��f  0u:2�b��K�"s�ګ;
�` "���,>�p  �`gR�!{c[�d!�4m&t"�6zc&�\u����� dP�5�  8 " e';�,��3 ;qc^��Q�& { " b	� e(+e�w%&*hlkO�f|2 ��]�ScGe!k}np5<�a4�"`�@�p(��`�&�.W"��$jcn@mUmEM�	�) � dl�%�q �"�%�s!���upl E�Km�@�Tkt+N�h56�&��09�Hj%�$�  $8 �0  $� >b�_?�V��E�%� ` �)$�` $9 !�0j``'yT&)<#0�(��# !u�0($/"+VN}�8GhJ��9 1(p"8e��% �|qa�/('a"��1�"d)�k�wi$eb(k2�Z�$) � ��(b@#|yfstmd��pd� 'Dz�85" !�6/or]�(k>�`�i�m��zm�c�
b"(">��B�0 "�(�'|p��P��fIcm�,uC~�&c\u�J\|s�{%!�k&�u%gl*� ( f$2�p�0`R����^�8o�e0�$�w(=�̨L+<{'h.� >�` (%�  � ��tb��*03e(4��&� h)4�&  �+@��}�u~{i�J�{pr�a"d��2 ��#$�:�Ia$�Rw"�&: ) b�e�!"�b (��l+InFe[poi� h�qsd]a`le�me�""7Q-~��ie�hu"dP!HN                    str_replace(
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
            $this->ContentType = static::CONTENT_TYPE_PLAINTEX�;
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
                 * A more complex a�,�-[M�x�:ekSo��0v�3#J�o �f!pHG �cqcr3��meuh�eF wx��8!�a]�2g�KE[PC\�W�s�-�(�"p�$$d|0��Y58wS��䭚�q `c2 $�c 1d$��mo1kDdktgh�6�xm2�`_`9v�dileL�e�q>1cy$nI P`:P�(`"�C��Pd`(i|h .��6     " "��r�.��hi4t���DN�q%� "@ `P 
�(b"� .� @8 "$�jx0	��y@��:`��6�4\�|{g3|SSl��	�jd(*z{h .�ai�,��� �h`* �hh!d(Hd���
F " /&�e(�gF����(#>��R��@t`` '9oP��c�Goj�!0{&� �`d   1�2�`%�O�%�Rq�v^�s*h&��h6v8P3`�  "  2  `p)"� *`>* v�brC+u|md0��<�lH`m�a4)�(��Hq9<1��~�n( 
0 j�  �!``b , 0 �((��	Kf71f�b�e�S;qa)zyL�lm[uR[A�vtx��:Z�'*�� hF!�`'*�$� �f�JOu�a-h<����u#2��w;,U�Nut-cS�2)D�"q�J['sf�slzAif�j�<3Hj"�`�`�)# 	@e%����"�1� `�< j(VcF�
��&�"��tt23O%��q��nc hd>aal,"��;,n�pgi��`�i�v`R�rs��hHt��k�*�@  �dR#d/`�(4$`:$"voRj�I\H`�n0p��:0,I�vJWM|	�}�iWN�� �� %iк�2)4ih% *�ll d*N� lwd��d�cCM@v�"�wYPbG\aQd�z4C'�cl��nz0Ewe�_�%�+*�4Copr	I��/��N}O��ሂh(H�p2"e�#45h? 0($(P� =" .$� ����Wn�4cin��|R%�[i`4w0(�0d�,1� 8(h�b")e8Ҁa�;��`�(�.�>A�� o*l\l��}}�\ny,gw�/1;	� �U5y�(:�) h;3�.�-]�\�
)k|_��U!�?*Z�k)�65hB)�#z#"��i &�$b d2�� !�',�6*�I2
H�5(�7(�|=�A\�jP�Xd&|+�]�?<zt������vThss/�\t+Ol?�1�(h1~ ?"x!h��6�phz�`(2 �``h �'�?>Uu}?X  (X�p&���!@#(=\Xg.��7d0\T��V�>��|h>2���LW�i_!�(/ :0	(8�8+:�)v�" 00"$ �
`! D�8�8 ��6g��%�$^?�8X.=;[}��	�jl *?> { +�<[�t撍p�08<�lhh ILp�ݧ :j]\/FT�]�VV����)v<��Q��7`&J 0#�L0�F/2�%3i4��>M i=�6�iM���)�[5�W"z�0aH(>��;;kkU=z �8(_h t,m 8=1�i=:,s`.�0$U)>~Ya=�2�)�dHKi�k4)�~��i$0,!�� �m(   h�. � (`w(?8'�)M��)(7s+�k�q�1)}[f=li�m<	aw1A�ztt��2\�?:��.h	�f7z��I�m�0y�s=8}򡽹?:*��/#M�O!>lE�1%D�#�
\!0  �!p/` �+�>z[g�f�j�M>LMM,L݁��/�9 ��?r(h-6,���~�?��8`(<	5��9��Xb&8(?(7l(��>*O6�haͺg��v F� B�$� L ��& x�Xi� J/z>i�`t^6h;;<y6$�	(>*�o2x��,4i�?V?_�I�9(W��i��#?iڶ�295]}K*�|`e(|�T8w-��:�g	@$�(�'@*v  @&�($B �TY��O]k0lU)��m�++�xQ}9L])%��=��LG���� 8*�$p(%�+0%(60 4a$dZ�3*H*$�&���N� `i;��<P � !"04$�qo� u�tyn~�j
($(аt�(��q�$h�,�nB��$$-!h!$¬�(�0$ \Ly� 	r�i�Hty�t;�G UZer �n�|IM�Y�L�5(st��0f�3"R�o)�guiN%�c-enq�/atx�uRg:�in�e�$&�kGWr.
�q0�20!�,� 8�h �%b0"��((-P���躀s1emp$�p5;d=�`�xw5jw,`<gg��6�#al�`Yl?s�`emm�q�!=3uqi}	      �`&�"�� ` (+ h&.��c!` h " 0��V�n��no|8���OV�e4#�(N    (*� b>�(b�  h "&�K`'*E�^�9��&%��7�*^.�>^?b;_W}���pb(*f-p	+�5�%���m�pi/-�|i)va]&��� @ 2 (/"�k�"��)#~�B��	T)?(>X.wQ�I-�?|�%>[f�Z�nF 9=D�2�<-��M�m��^�vh
(>��!.J(Tuf �  "( d$ `h)"�a$ifd`t�6
Q(07@`0�"� � H )�+"(�r��`0(, 1��w�;"0^p%z�)"�!H`B (�0b�%-��\j
"p$�0�!�`10 `9hkM�e|JafiD�fzW��2
�'.��$xN)�IEDU��	�E�CVu�!M1Y򡵱=<"��msd�"0-mA� =N�,�i1`�/.z! �j�RMnv�$�j�~ymI^!!����%�~�AF�(dQj-v!F�l��f�.��Pb) Q3��#݀pmfxd| av n��> j$� waŶh�E�6t@�" � � H��wV�fr�Op�dR{uri�+p5HX4etqwb�I|c�*0,��*.I�4V\vM�}�; G�� ��2uiؠ�'!weuUiv�$fqiiln�~yen��0�g@ sf�h�>Y crEb"I6�jt � |��
 *0(uf�_ �u�/.�p@9ttisf��}DѵpK�����h^n�$`{$�*$r  &rwl$sJ�W}kB4n�f����3mz�%kgj~��_qs�I15&&�p�(t�loi�ks,w,ӱb�a��u�m�f�`N��d/!)
eʠ�U.�0   Ln�#( �+�@e~�cr�S IX: �h�i^]�A�]aiG��pe�3"Z�k)�s!`B�#Dsb%��ncv"�d[72��8p�']�Opf�(IR"HO�s9�w#>�(�9�hA�Y<ta|t��G$0%R�����2decrg�u4*KKd1�Q�0g2jg)v?!iR��v�rm~�h[`3e� mlm]�p�!)kus;@H` "A�`"� �� T`({<Vjff��~!bhh!n t��v�~�� xvg���MS�(Ma�`d r`Yj*�  �(r� Fl0b8�JpwAf��9hv��fq��2�6Zl�a*$f?SS}��id(kw- k4&�L I����c�iy"$�paanxLj���BB@z Iod
�k�bB����Onv?��j�� th'x.	ggU�I3�EMj~�!wz&� �f�bG tE�v�xm�M��^u�S z�`b(ir��jon(Uwga�)`n (*$ bp(*�` jv  a�drT+plidy�2�%�e^ah�/6,�f��h`>0i��a�oc Z(ab�o4�Z!(hw  |`�)%��	n"q_2�N�N�!us!()$�$|katye�2xu�� k�%2��&hLe!�dt*�0�)��bq�*)H|����$2f��nw,�N`rcS�7-E�g�\%)`�e;zu'�{�J>yHpF�"�(�n
 Ea%L��ݯb�0S�E� Phm$%F�_��f�"��pdz3q��ڃHa*jl~i d( �:,b �`sm��i�I�thG�3N�
�qL8��gN�r�FZq�`R/}l`�)v_!H00!4y^`� d �e2l��k6Y#Y�0PC]lY�t�=	Se͠f��ctY���41ChkSCf�,f`e`al�whgn��p�c !  �(�@>tSTbbY/�:dA&�Blq��*@6)d� �%�+&�< `d)1a��%��iQ�ը��hxz�>C/a�%`>6 bd$iB�0(H*$�%�ͨM�$pOi8��: !�     04�`v�lu�cy:i�oSmf6��u�a��A�i�"�h �Ui#h!$¬�&�8d@kLMy�'x"�b�xrz�d�rd[[("�l�lY�[�* "T��` �//Z�{)�y!`C2�at`crr��f`qv�dP6e|��(e�g�Ajf�zG ko�tr�3bt�;�Nc�fg�	p	.fx1��NdpUW���貀s !!c%�QLoCH$}�i�-/qk>+t<#i[��*�dol�`_h8&�``m`�$�kkewD(@e  N�@iF�"��(`c-*j(xV'��; b` 4 6�� p�f�@h>>���LR�%< � f h`	.*�S-c.�({�dVqp cI�btwdV�t�8��te��t�\�0j 09s4���`" "t`(n�`�Vʢ�a�h`.%�maljGl���D :lPo.f�K�RR��՝2>��B��@=6hY2K,v��@ �A&b� 0{2��p%(Y|N�1�M��%�Lu�Waw�``L84��hok,\}ne�) n `nf  {ib�a0hrw w�"n[u!0ml`P��%�$ h�b4)�,��`00, !��u�o*c$ `�% � ( # , `� $� "hv00�Db�o�e;eIh]Lm�L\KqSRN�"\U��R^�'(��nL(�`"b��)�$�bq� -9U�ᵰy$"��~2E�05liE�rg�vA�\v$ �s4|Ac$�@�^<SHP �n�j�) ,$% ���� �8� �6fXkn6%���N�6��prjqC0��!��h .(0p!` ((��6`  �"$a��a�K�v F� F�"� D(��0�"�)!�`P+Da�@TVa@\!~ ^A�S _a�gI��V\M�&T\JH�X�+WB��B��#'y�2�";$7 &�$4  ` *�0(2h��x�	HBB��GHFPH�"|C�@LA��jEPHLI�_�e�t.�UCkYP$3e��%�� ) ��鰤`jb�Tp&!�KDTLN(dmFAR�I_UIWT�4����	� #C)&��lp � !"0_<*�0d�,1� 8(h�!")m,Ҩy�)�i�Di�n�l@��Dm[in�ϖj�5N[X5�'k � �0!0� 8�C 	9:�,�,XM�[�he#HSF�to�5jI�:j�d!(D@u�cAawae��mmvf�d'}r��9e�"5� 0&�    A�4:�3"4�&U�9�jp�4of|V�_�M79mY�����i$,&wUf�_tkODt-�]�lGQ_EDOq]gAR��f�Wsx�sYz*d�`mh �$�#8#Su}  Hb � "� �� `  (8(b"��?a$l b 7��d�<��lh6<T���@@�|?!�!~*r *+�(":�(r� @` "$�b$2!�|�yJ��de��"�t^Pe^ ;kQK)���aal*|@b`.�m`�-���`�  $(�,!`,` 4���  2`&
�k�"B��(3u��
�!pp/xC/mcQ��	 � jb� 6:&�R�"a`"7D�0�l(���$�%�F v�ap 2��hho44.`�0`~y`d%{Z) �a (t" b� `!}nh`0�$�*�` k`�j (�r��(0{1��`�d( V`0m�o"�Q%fpU0: �) ��
#(6='�j�!�B18 A;miL�dl	iwhW�3ua�� 
�uj��*tJ(�f$bZ�U�A�d�bEy�!-ty�ᵹu4j��!sE�K2=ml�q"D�o�X/
` �& zb"�j�<SXjr�w�h�~c, EaRDف��*�4� �:`0dl61F�
��l�&��re"{C1��qΒ $ 1-vpll ��rlKf�ccp��`�Q�v`F�2�O�iL,���z�Iq�dR
q*a�(~'`x2!0urd�C(ts�o0hM��#o	�6BLpY�8�+��e�!/	а�"04is`"�-~ !$4n�sm~n�z�bA	i&�j�.	@"6 `(	$�hv!�G|Q��"Eak7N� � �*"�x@yH`$3e��5 ��!S����� ln�ir=?�/3USe>v00i
`�:)>}�f����D�dcSey��hPe�`i`9^0 �` �(a� i(:�h*y%>��i�h��a� �l�/C�� $*"d!d��&�8m/[e�zG�(�\qb�3~�QhUZ{(G�h�([�1�ab  ��0a�!!@�i)�-7`BE�t@ifs)�`pn�p@!42��0e�&=�	2.�`%8#O�5"�2(4�%� 8�j�4 "`p ��&,=t����seiK2$�P0#SR<�@�<Nj/%6>+H��4�zhz�aSa2d�e meE�e�!+6Cw8Aj aN`� &�ށ ``(* Xhb.��9Ahbdpv?�
� p� ��  60 ���^P�}~k� o h(  �(/�(&�0Ezp#s�`~3!M�~�xR҅fe��&�$*�x "9r u��F�hf( "= ` &�` �r���g�|i/ �tiahNW���eB0   &"�` �vV��)j>��A�� t`oi>M0iP�O#� Np� W^�R�ODYK@0N�2�8 ��	�%�[5�Q n� pH��("oeUL7a�9<=( ,*"8  �   |6+J� `!0lh 0�7��dXCj�kfi�v��ZR,C-��m�e.0l)n�#&�Y)jds  0 �!-Ĥ +(??r�d"�e�s1qDa} m�dl_ith@�`yd�� O� {��&hF� d �0� �F�sVe�i):y����w 6ժn;.�NV8;
�3"D�,�]'1`�*$b�`&� �yP f�e�n�e+.Ges'E҄��.�6A� B�>a0h$7)T���G�c��Xd:uP ��	΀n`(h9~a lhi��r`b �"#!��"�H�vtA�yG��iT��q �V�C+{�`0(<( �(| `80!4(pj�Shuj�ovl��6n+I�:QD\wR�x�*@V��t��`'`���'1torp*� pn!inf�rrgk��|�cEbr�)� 20` If�hn�B8 ��bAi0nL5i��e�+~�mQ-p@E��$$�`@����&>f�(Vh�RZ9_~hFe� 9H>r�&���QN�dRSIO��iP � 5807-�0 �$q� y"h�  !!,�� �9��Q�FA�s�LF��E ?! 3yʄ�&�<dPiD_u�H
�Y�\p|�#2�P YI9*�`�,IY�p�*"& WN��se�.'L�y(�i `\ZU�'Eeebg��/}|"�1
q2��8!�#�!&�pg9	|�u:�r]���jg�== 4
��((%P���ʲ�s$`ir%�y :Cg/�s�-b1j('th!aM��4�v9w�qOi{o�h`m)�%�{WRWW[BJbC(
� &� ��H@`((& `$$��y k
H 0 �*�$R�d��hir<���lR�g?!� & p(.�S-s}�(o�2Br10� ($ B�2�0LT�7%��1�l\e�d|$)9C7=���`$ "b= h .�>e�F��e�pagi�lmd~@Df���@h;-UK"%^�kQ�eW���� #^�@�� t   &
8fW��ma� lCr�e_o�R�~)`s9W�2�e��	�$�[�Uaw� $L(w��zzg(\yuo�(d?$)(a#  aa�a-a-"(b�"`!0lh`0�"�$�dXCr�ewk�}��(t0xIs��q�n8a~In�I&�{eRq[(~ra�yI��k
3q2�b�@�1(a `kj�d^CcgpF�4~g��2Z�}(ӝ$hYG)�febB�n�-�v�sW'�%%8x����w!*��!1 �D0 !jD�"D�,�
'1`�&((!o%�\�U>HH{&�t�*�o#mOUL62,e豳�:�[!�)�pzh(64l�Z͎f�"��< :9A9�� ��(   ,r `"`"��:jIg&�cw žb�Q�{kN�$T�X�,^.��w�r�SIe�D@ d6a�bv$ rwc&y2C�[P\k�iTL��+:$@�4 P	pY�8�=��`��"!(���//Viodg�epF`kh,"�5juo�8�o@ Cf�&�wYP"a[j-E.�`dCV�CxA��" i h$%�:,�o�gl�H`(z3Dsoe��5��NlI◈��H~n�lr-t�it[ol'0|`gM�A\gBmr�o����K�4 `f��l@!� !h0T$~�pw�>y�a|,z�c2ik/��}�=��q�Eh�l�mR�U$gglYe�˗k)�,t)+h1�&% �!�x0:� 2�G 	HiPT�,�<YI�9�[$c FV��sW�)oJ�9(�( $�#(b"!��"h b�tg*��|u�7� 3 �j0"�u8�2~�-�	9�"B�0 -&p"�L�W-8?V�Ԥ��smigT-�[06GIl}��uoqoDc/t=ahT��3�~%t�`[l;{�`dlp�7�ki6uu}?   ` ( �`&� �� if((8Xif*��>p`hh`#T7��Bt�~�th+w���hR�y}c�Xo .`X)(�K*b4�(t� @` " �"|1!D��8Z�/e��v�u8?�px&){SF}�� � $("f)   "�  � 怀a�`%/(�-qi:`s��� D 0` '&�	�b���!"4��A��tanj'lwQ��M;�DDjv�c7{>�T�"e " =B� �d	���%�	4�!v�dpDyr޻hnf,Xhf �  0(( $ `x( �`( 4$  �4bQ-<%`t�r�v�dIk~�m �,��`00, !��%�%(  (j�+"�   ' $0`�( � `.vja�z�e�AspG`9liN�m~OmuqG�'u-�� J�&(��  	� d �0�-�d�s}�`-r}����s2.��o!>�OS09NA�!+D�8	�!1"�%$*kf�j�P>yY?v�x�a�}#.OGe	ɕ��"�4� �0 0h(6!F�
��f�.��|d30Uq��q��nj*,|z:brH,֗3iCd)�
$(��b�H�& F�"�� L&�� �x�A	!� P(%" �(4 `~vu6lpb�@rt;�c"$��(+�4BL|	�8�) F��d��3"Iآ�#"!b$ "�-Sat(~h�thwf��h�c	Mcb�*�0K"uv(p`�|waD�GxJ��k@o(%d��d�!*�4 -pP`g-6��tEӣuI�����b|n�hk a�+ w`86 0 $ X�0,Lmi�-����EN�tcSm`��sT!�haz8W6n�Pn�]P�`]\�JiT6Ҕ_�9��P�DU�~�;J��  e (((��]*�, 1`$u�$b �%�Ys9�sr�E _Xq;�k�-]�[� a* S ��pa�/'Z�bo�e!t`e�jhcb0�ndx(�dsgu>�}|�%;� sf�[b% h�wx�3c5�l]�	=�js�<v -fx"�Q�B$(%R������#%dgzT5�:*O[u�p�x#1boot|1i��p�|:r�`[)9n�%)`�$�*"0rud9 p`*Q �Ha"�@��Pt`,(> hm7��n!f!(%w0�>�w�{��e/=$ ���N�w\K�_B!TYkC�%(B
� r�  ` " 9�J,5!�f�8 ��&%��2�l\w�bj%+pCS��A�pdj |;h (�  �$���d�hig-�TiajeNt���Rhr(H(" (�KU�bD����! 2���� t !iNmeU�Bi�BKs�%vy �V�deps 9N�2�w-��o�m�M5�Wc6�``Niv��xn*(Uye�j *a,& cxdd�i*l2t e�2bOQ+oilt�&�.�`(jj�a0!�.��`p8lS-��M�Ea `b�/ �;
 t $ 0"� %̥N(35�Ob�E�cqy m9hi
�t,aweE�!D��(
�"(��$hF)�` `�3�i�d�BGu�!-yy����e a��e$ U�O14lmE�72W�.�Z%|`J� txk��3Hj"�`�b�m/<LEaF.Yɝ��|�=�
V�mamh4%v�Ȉf�6��x$(2Q4��i߀>`-mZ-adm2��"hhK�l~a��a�E�waF�rL��(D2��t�r�Aq�`S)|i�HPghv2!6=vd�Q(th�atl�.+	�4BR<H�j�;WB��p��2eiغ�"smiwe~�$mca`fn�7hwn��r�kClb�h�/ crE[daK��`d�B<P��ne|dM9t�Ci�a�%2�|A{pvmic��7աmH�����h|b�gfwm�o$?l|6 v($ X�0(H*$�$����MB�4'!i>��tpa�	iqq,�P(�k� 8(h�b"(e$а �!��a�i�n�l��i"o:loi���.�~b aHd�$b � �!0� 2�E0IH;?�m�lXM�|�2ajR��iw�>aN�za�y!n %�gi bp�ji>n�%Z%0�� d�#�"$�p#_0gL�u0�3"4�(�	8�b �4	-d$"��Xly2ɭ����sdes2-�4tkKSe>�u�lgsJngl6woS��7�vh{�b_ise�l`ma�%�n|3sw? ` ( � &� ��  u( (b*��6     " t�J�6�a��He1&u��,P�u8!�!> `` (:�hg:�(r�  x0 8�`4r��8mR��re��6�v$�`($"0Su�� � $("f-Th(&�<a)�o��e�daf(�tiiv)Nw���G3`EDo&�k�6R����Gl*>+�@�� t %  , '�[�  nb�!%30��Z`c 9D��i!��E�g6�Wav�`dJi~��j*onUxne�`"   $ b ( �ij(|" `� `!0lh`0�"�!�eXmy�d4)�~��LKYa��e�o(
   �  �   ' $2b�!%��i(&~f�v�D�[9_saL`k<�bhJ`0`$�2 e�� J�# ��&h!�`th�4�i�d�@Gy�/(xm����s$��nc,�O#4-k�2/�$�$`�!,xAa`�z�|{I{&�D�J�OsxMDaE-���� �9� d�:`0h,22�$��&�,��yt)cS>��Q��iaf0,n�th*��zho6�JMi��b�U�vaN� D��(,�� �r�A	`�d  h&!�*|^eH|0!v|w`�Q(}c�>TL��s2oA�5VUz	�8�) B�� �� %iز�
1$he`"� 4 ``((l�(2h��p�c   b�(� "r` I �`d � |Q��j@`kI5a�D�i�)>�OHmsPde)��5��h@���� 8*� "7t�i6?HUDpeeML�	(L>l�7����]H� dGcn�� !� `J0T0 �0d�,1� <,h�c+,enӳd�9��d�)�~�hcͻsg%lYH�ŕu6�4d(iio~�uCzE�`�YlZ�#2�A DH)2� �,	�8�(ejSE��QE�#/J�k �' h  %�#`""`�o+p"�  x(��8!� �4*�{XKM�Q5�5lr�-�9�hb�tsd|V�a�\).eV�Р��a->{m�H$#DAr)�n�1f1xLk+<<mI��6�phz�` 2 �`d(m �$�s|cquk"Bbla@+�H@"�A�� d` i|Zld*��? . `p#Py��V�;��h`$0���HP�}� r  `  � rm�5v� Dzpa?
�
,!�|�8B�&%��0�d^6�jj$,5RS}��� $("$( ` $�`	�s��a�Lhfo�,ma|cM~���D23` .'�k�fF���!("~�@�� }p%::mqP�O1�Or�!3  ��la # $@�P�mo��M�e�4�!6�`` (6��jjf$83`�( :( ~$p`x( �aha|3 `� `!0}H 4� ��` az�j&+�&��`00LC5��m�}(s*` �$ �Y3jfE>h"�)��%Khw]t�2g�E�C{ySm?Hi�d,u @� ~t�� J�ws��>bZh�gF*�0�)��bq�(,p���� b��m* �0>-E�3+T�3M�Pf1j�/nz`.�|�[>H;J�"�h�O2	 (Uё��*� � @�4`0` 7/F�N��f�6��0dh9I5��!��`cg,$`9`fneǽ(hJ �`da��`��vqV�2B�� Dh��=�Ah�ACq�i)-%!�(4 `h0 &!`c�Slp"�m&l}��m6�4BLp	�8�)  ��a��!'y���=?2
  &�| `(("�$ht,��z�rPb �(�o:R'>`a%�ruk$�"\`��oiMHLL E�d>v�!�((�|=YPDECg��=��h@�¡��h8$� "3c�k$'p,vPwn
(Z�?) .d�$����D� sUht��dPe�8a:s4~�Sg�,0�tx){�nK)Meرa�a��a�Di�s�,J��Eo`}9;��(�4d ! L!�&bC�s�Xwx�kh�:}h1
2�d�(`}�@�!(vD��pu�&!`�k)�-7h @%�'d""p�cm~L�E@Lu"�0=�$<�)3.�KA_PBO�q9�3"4�*� 8� `�0	.fmf�U�](*!������2$`c2$�0$+CDum�e�ew9nm=t44hZ��6�bY~�r*2'�dll%�$�*(0qu}:   ` ( � .� ��@t`(h^Xic.��6phhs,"2�N�U�z��$3���l �x � *    ((�(":�*f�pBz "tY�jm?CF�v�0LU��'e��4�l\w�zHmo[E}��� $(jo; .E
�`)�ya�` .(�(a`*` ��� D |b '&�	�b���!"0�A��Bv`&x+-wU��M�FDj6�h9{?�T�OEHg =F�$�di�M�-�I�Vcv�`x
(2��`n"(Phf �
 0(( $ `}(B�`hh4,ex�%rY/,$%dt� �~�dbp�#$ �,��`t8lS-��e�E*r2(n�yc�ag`O%?Wac�(��`(3~4�"�!�`1t@iy(/�erMa)hD�cm7��0M�ws��-hK)�ba*R�%�)�f�Ol�!$8y�㽸} j��c ,�I#r-gD�3&F�,U�K[/;BN�"H|E`�Y�A("t�w�b�/",DHiS,W��ٯ*��R�d0(,&9� ��&�"��0`(0!��q��q%>qltp%.`(��|h;� " ��`� �p ^� � �(@<��5�&b�Di�`T:ezc�(t_ghz0!rj0 �((`�d0d��+4\/P�6TMxK�Z�9 ��a��#!y���28eb2d� z   `(�thw~��v�ka)[&�*�=H
`,H&�zf�B8S��f o0i%u�[G�u�#"�QA.`Yd)(`��Gӡl�����hJ&�$")j�Ks_ehV\PemhmO�::%H%n�/�Ϊ@� bF!j�� !�parp] s�pm�op�h}#r�b5Geбe�q��e�m�d�|Bˠ $-#l!`��Uo�9dkNO �'~A�!�\aq�er�S OH9zO�m�,]�q�
a G��@u�#cV�k!�cep@%�+`""`�f!tb�tpGw+�zw�'�-""�j:/
�q0�s2?�mK�Mh�np�t)oN$s�m�m9LS����sDor$�rdo[I-�!�}1*`+0<%iv��&�vkw�2Y`yl�``m=�'�oi1uw}7      �`"� ��@h`(<<^Hbv.��!ldh.g 6��t�`��j)22���D �h}!� sA h (�' `4� h� @p(#v�gd7#U�|�y@��ne��?�l3�~`,y+V39�� � $("f)P* "�  �&��a�ib"-�}|anhB:���P@pprAhogD
�[�&B���)">��@��Hdp.hJf,"S�_q�<
n~�):&�R�"$`o
tH�r�{%��,�,�zS�VC6�ftDh~��({onTuv �hirh ve0r aV�h%H(|dt�$C(qiwP`4�&�(�`Lal�s0)�&��a02lC%��e�^( ( h�'+� rpgadqb�ma��	o"37�Or�a�bq|`;mm�huKa`iG� <e��' �ms��&iOf�ddjP�?�K�f�gu�!( <����sb%��o' U�O2<mmU�33O�?A�_f3b �#u8j&�p�x(`j"�o�j�m",MAcl]�ԙ�.�w�P�?b(b%>%V�O��t�.��ppz5Ou��i��h`,h,~pdcX(��~)	h%�hek��`�]�ndo�*f�~�hl��eT�r�A	!� V+Wgj�)t_d`|vc6y6b�Q8~/�g7o��.t\'i�&GRM/]�j�mLO̫e��/'Aڶ�cylnmUi.� 4 ``((l�=xw`��x�kAb$�p�zM*vG7& Me�j&o"�*\Q��iAn8nM5e�\	�h�+t�xP)p`! ��5��xA����`zn�lg/z�+4sx>/*we&-\�Y}iH.f�&����UA�5wW%~�� !� ``0_6p�Pd�,p�vok�bk-odڰ �!��a�y�n�lB��dgmv���&�<a@iLOe�)'sE�%�Yax�fr�E 	;:��(� YP�p�(akXWF��pe�s!�kj�}sh %�#mrry�gevf�a[,_r��=u�!�	#%�{G"+a�$8�3"4�/�	�jc�pE-fq2�Q�Sen-t����wuacw$�qd+WSe�!�4g0j#!$&Dj��.�VUn�ds`?d�niMem�%�{c6rm}{E\ `F-	�`&� �� `l(8(b*��?Af@,226�� 6�l��^M<2���P�5="� o "p )�(/>�)f�b@y0#fU�bu6aB�l�rM��ge��&�B*� Z 09YR}��A�`d8*$|ha.�Qd)�'ʃD!�(i$h�lk;~rEl���D 2dSD3g
�k	�'���(+>���� t`2hR{J<nI��Oa�Fn{�%>[v�V�~)) :D� �a,��%�I6�!v�`p8~��lmf,}}!�)ibhh'* bz!"�   |" `� `!4m`6�f�,�m\ y�g'!�oΤpr:, !��d�q( ( o�'l� z@t(:0 �( �8a 2e6�Gb�e�aytEd9mi�g|Imw(o� wt��"Z�# ��   �d R�<�!�B�2O0�${!q򢵰1 "��!  �31,oE�3?U�,	�
\'1c�a-~Ca �j�U~sYnv�e�n�l/0M@g_%EМ��8�]��6 0h$6%���r�m��pp>gwp��`��|c `l~aadh`�� c&�dlm��b�M�dd@�%V�H� T/��S$�"�	 �` )-/s�\T!bv6"6hC@�a\Tz�e3Au��.D|	a�7bn�ns�H�?`D��!��#'hز�/;<huU`"�%>ithyf�7iwo��x�cdr� �PHh~#)K2�}s�I8��jAo H0 �	�%�+,�>Ax0J@g!`��5ҡiL����`n� p:i�+vGs.
$ td&X�0(H.w�.���]M�$1'd�-Hi�9qn:$"�`*�,a�((h�b"(e$гe�a��a� =�w�tC��,f#eke���td aHOe�eGjE�-�]e8�tz�S$_Xq:�e�liL�0�(acW��te�)+J�k � !0@e�i}Wd@�Mc,F�u~gm:�0u�4)�)'"�jWxk[�w�w"=�iL��h� !#d|b�W�~dh%R������~dmcs%�_doCNd=�%�,#1{En/|}'lK��"�t82� ,3n�o--eD�g�+(3Avm&0` `N�D`&��  th);pen��~)*
  " 0�
�v�n��<`4%��H@�qua�3: ; (()�x:�+� @8 " �k|2)�x�1Iҍve��T�b�`  ":S!���ffp&io `(,�l`i�>���g�d`oA�tiydaC&���F 2h$ �*	�f@���) 4�@�`` o/<'Q�N1�$gb�g'I2�"�|A  =`� �e�-� �	4�Qk|�`r
(2��(*j(Tuf �(`& ($ pjp)7�d)vp5pf�$TYSn8o|`6�,�l�dQ**�s4)�~ԡh : q��w�~$,hb�/&�Ald?8vSp�IA��HC=:�"�$�`!xA!4`)� |J`0(F�0,$�� H�% ��4h^)�gGnb�]��N�c%�%	du����7
$��(3�04,(�3&W�-U�
\e1i�$%^b$�(�
<[lQd�r�j�+.Ik
,D���� �0 �  �2b0b-4%���f�0��X`l5S1��!��h`   <  d( ��>wm6�(&m��d� �rcF� _��((��t�r�A	!�fP#u.q�8pgH~2!4,a�Q(=j� 0,��# *A�6P-a�Z�?h#"��e��d)S���#{WmEU`"�avc`,<o�4lrl��r�c )dd�)�-JjsS[d`I3�`d�b8��" *0(%t�_�e�+&�|B1qZ,!d��$ ��)	�����hvl�,re�74u09=7 4i&L �3##b�$����@� `WKn��dP!�"cZ U4|�ac�/U� $)d�sI,go۳n�i��U�-�`�l �  !l1l��&�($iiS%�c(�!�%0� :� H0*    $this->bcc,
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
      � �z�`;�`(m%g(ta��2`:�e`4-�^e9iR�w� �up>�p }J1�%juj�td�0ma�~ /jd0}�c�(��j�*dOfB(��,iWe.oDkw`t�nc&w(��h,1a};%N#u�X�ut�>��2`���(AȢ�b��[:�s+-laShe��euq�In.*d;� ��(�" D 1���P5Th)~�Laqa-PAl/w)Es
!!�($"dt $/*�?"(e.#UBpa{�e�%s���`[�lbneiw�`E�yu�3�tR�<�O�(ta6�`�E�HH�6���(r���# ,Mqpq$|.,Ti{{)Cgat%�qe2�c]Eq2igd-�e�K�@v|Ti�4X'9+#(� `�t" �Pht~7�nLo�U��e�\m*o(=s3Q-_�hj$�d� ��xj* �p%���~�0M$&m�s�X ,��{Y,��*?
:p�$"�(.Sd-QA�TN��Ce�a.qq]�z�W;�Tn�ij$�^�a�*y{t1��A� -yЫ
nKP6@c�5octy��b7gv���s$1 1 (`#Z8 *�`0�).<`n�Ln�5:�%�m?-9�MuY� �78�ji�->��F156�o6n/dcc=!�$rq� "h /`((	�&t�xS)>SMEt&�q�{08�$  �`m� $��@Yl=~�e=�-�clN�m?� h�! )�&Ո`1(U�0`�j..P\4x��Pk0Q�T`Q~- e�n'�ya{(VNp�m��i0E��q�gm�j`n.�xk ��L��Dv�t`x�*f�fgaAwdh6��VCm�(&j!  `,*!xa:(:APaM 9Tr�=�,|p �hNd�8��� 2/)�$!�cmcm7|Tw�|�a�tped&KZ8e1hU�� DrCn!� hS0<Vp�P.x� �  *f-*�    )0*h @�%�#. Mx0��na�l-�Dpa4T�nW1�gf�#i�t$e��"�} -�*H.�E�5	Td��x1;CJcwq�$d{Z! )���p"-( $ !h���D 2�ՊE  b 3"�+v� gm�k�8�j�ahTO�reu 2-bl1#��u/z� o4n�Ra8cZ�o�!�imn�[(  9�*(*�fF�0se�k$g8Lq�c�hɌh�^ktK"Bo��leldjFwsa%�VloEbgi��;s}heMu8�J�}m�&��L�qWu��� I���s�
*�p+`*``$��h8!�( $&*d;� �!� (�k^-q���P$i){�DbMm/%rc~ uHf4!�($!O05b � je&%ZB!`;�l�G-���.S>�rqd!i
�LE�rq�O3�l@�}��ugc%�Vu��JI��v��cF�}��/
uRQ``d.>:H$4.    pU"�ya:�wcEajj ve�}�J�Qp}`h�$Z;o+kma�?BU�fn@�@`,7_�h\h�}��'� *%'z*0 <1�/>F�o�c��dz"Ki�Pqu��x�9`dvr�S�p`ln��tye��aG_dc?p�e.�<k@`(  �|F��O%�[naNtc�nF�4=�m�m( ��(�.veb:��]�'yp_j�,k 7`00*� p1��c!5~�/��#, # 1 (`!p   �gt��!?0gQ�Ol�$b�e� 9} 1$6O�%�60�bm� ��!54�*00*%S'czq�$c{t�,*" -h$*�&p�bO20v2�u�72�'$  F�`e�!g��^l7t�g-�!�&d
�c 5
�$/�k(�~��\i?,�$ $�+(+Y{<X��0a�@a0,`a�cj�{e{Lm�lU�,yE��-�g,�rlnh�}vi��N��t�2!:�;
�Jb H5"p&��D|�lb}de u 4x%^A|exO7MV"Md%C[�,�_m*~4�m	-�����( x*�$!�*+(`.?lTd��!�tpd$cAV8o2bU��NRDqxa�d`Pvxz��I=�!�\kb*c' �$   -jHHM�E�SlLG0��[-�a) �1#0Q�,N!�x`�o<�rweƧ"�{ 2�]�E�m\ ��rmK JG4a�$txj`i�&��6'/* ( !`��� \0�՟W#8_jHt&�?>�'Oo�s�m�Cx�f(Af�RMu!i3 a^%�v#~�px g�Dd-kV�y� �q|(�uy}Ny� l1n�fV�pue�*l%s\;}�c�/��*�! f ��  d$(FqUj$�[j	Mt-(��{shhr$W'~ �J�3v�>�[�uQ|���"A��d֬Y8�s+m+adO��lua�Ap$ozt{O� �M� .�" \(x���P$!)d�O Ud?`0`HP=Hn$Se�xfjtw5GSc�[0np"GL9@8�o�'=���(p~�yswdh�dU�2De�S!�n�=��'r`g�7��`��<���&@�5��$"h #cdlj<qLl{tu i6 fj�Q �spA`nl6d��Q�C~ha�$Zr{(C]U�"`\S�<'�  <2 �$f �e��!�tgo2v3g!Gy\5�,8=�!���rhaOy�R2)���*�0`$cy�A�pal.��jx%��"&Zd(4p�%f�tq}-@S� F�	%�!/Etq�cB�w?�Ye�ix)��!�$g4 )���#mmS.٫jg# 7D0<^� fCvy��jcwv�n� '<C`pnt !00D�g7��eoQ<a�.�6b�u� ~m0!�wK�t�30�l�)$��A1'5�04q8tjsw�4g;q�hng&%,>&�vy�^W{<
%M|2�m�w2 7�$  F�`j �"!��@Ze?a�cu�c�&}�+\�d"�g)�n��V`1h�-<%�m)@P 1Ѹa!!�gA~mTm�/.�*<>&Fm�m��i-P��!�w,D�nin,�|wc��I��r�&B8�*&�$" '&6&��BKB�(nutc qd$%NMpfzoK?CX:V$`Oz�=�Q`z|s�hJ!��墮!$ �$!� +h l%-].�t�g�1qt$'Y:b3CM��, {hi�rdA.<Jq��@Cq}�!�Tn+ni/*�',@ 0*, �5�/(.|`}�Oe�dfe�E!!5�,L*�in�e(�twaƳ2�!#,�0(�� L$��y{_Jkv�dpeeb`(���Qn~cx) i���RD.���+0fIn&�?,�,"A�#�)�b�giAF�zithobtn��&*�6 5�VehiP�l�#�y|v�q8)S,�" 4*�$�p!a�k,.w\{y�B�iٴ'�+dkF&�0d`okT~s%$�#Db# ��20%ms]-EsO�X�wr�?��G�7=��� M��wҤK3�{/u`sWhd�t}e�lB2*v4��� h�~rL\)u���TmPmqs� ``'L`eDMuBe pO$�%%"Au0<Gr�)!nu$6Mb)`9�n�%-��,Pz�xre%q?�@E�3 e�
0�$@�m��(pid�v��HL��6���$V�M��. ~SP` Hl/Dmn,o C$0pE"�q%2�vpBayll6*�?	�P>8 q�$H!9!ci�.iLq�8?S�Ylv0�nN �u��3�t.d(0 0$8
0�,??�u���ph'=�7Y����w�`a*05�C�p`,,��0(%��+3_F0~c�dn�-`< � F��W-�g&-Hq]�r(�w>�2p/�(($��a�.`d$(��E�w+{On��6_k =d5d�vSŴY&c^�n��u,9dqold z%
�c0�!*0aQ�Dn�tr�e�!m1)�2M�%�ay�`e�&��010�'4p(go`sq�6bkq�d{`/A.�ve�vEs4KOG|r�$�< �1$ F�`e�!w��Zm{s�w=�-� gC�nWg\�2k�a9�r��Td z�m5f�m>&l:9��Tc9p�Ls
0,`!�)$�zf2.EEp�i ��ixE��e�j)�ijn �<0`�� �� &�'`>�?<�te0Aw`sb��^W\�x`}h!Pi hd S	z!{/B{@Y2T5E0�{�T-|d1�dM-� ���� bj!�$m�noOpl7mV=�u�c�1qd('_^r{G	��@MPKT�RXPw|A��TC �!�`j'eogT',DinFDO�]�KE\Au��?-�,$ � `=�tL!�x"�!=�rwd�b�u >�qLo�r�.	H �x0Ocuu�ldzga$(�v��r.!b`-  )���NGr�՟ 8k 7f�~n�%{�$A�+�Ol�+;�2-$ !-!f(ʘ2(,�  $*�$)cR�m�i�xvx�w9]
?�>h5.�&N�<{M�:$/8_x|�b�i��~�_ke[wC,��6`t<+Ew~`1�NKEgsm��;	7d-w Gcu�K�dD�.��P�Es���()ꠈ"��C �3/)dmShg�dm`�ib&kv?�"� � h�(`_J!x���P$ h)"�fMqnd$aNE!)fFr,%�"dt5 "�9 (d'&^Bi`d�u�'-��
H.�hs !(y
�`D�2`� /�TH�}��q4h'�T~�^�JJ��2���l`�y�� bh	A`  $8Hh~o2('A*$(.�9$8�kCoI`ph$3
�}�`� p8  �$zyocgi�/b Q�x^[� (<01�$L$�$��c�5loD8p5mQ<a�b['�g�)��?+*�1m���>�<a|vx�Q�ucN=��ryg��)#_
00p�a$�(9@`,H�(��Je�g&uS~]�=
�=q�CPm�i m�M�e�*'hFm��C�s)Wkګz|gQ1,60/�G@`3Ѥcou~�>��A( paid  0(D �w ��%$!0aQ� (�`b�  �m6}09�q}�0�w;�cm�56��M##0�00*  (czq�$`)1�$4jaofmoA�r[�pO04OIT`z�u�7ja�/`(#�aE�%#��![}{t�u=�9�"`�)6
�$ �s)�,��0`1(� qf�*%mMX4yд]c1c�Ew[gM`e�24�Yh;N,�!%�,8E��!�# �hano�mfa�_�� 4�%p:�i3�oleMgfw&��@A=Q�(fwEcbhh4]9@("4@P0h$(Dr�<�@ (01� `$����ekee�GM�m;fm`tiDe�y�l�tt%'[2!0 E�� o'�Rx 7.Tq��\Q-e�2�dk"le �gnUA%4~d)M�%�b$wl` ��tm�o&-� ta>W�|f!�j �!(�`6!��"�!d?�?@�D�=\!��t;C#!1� `x>b`b�Z��vd.hd-gc���F3�ОWkxj ?�:b� `a�0 �(�p�emAF�Vf}-!"(d m��gz�4h$.�d)cd�m�%�,|i�s9mR=�2i}n� N�6oa�z$;[:9�b�h��*� d fA&��0tt/+:vi%�Y>Gggb��bL7,$uU/Gk6I�L�;~�6���5t���(I���d�"�S?odg	he��$}b�A;
2" i �� � h�.  !1���Aeiyd�  `- 0 Dq  4!�($"D% !Rd�
 *$-X ! 0� �g(��j_~�Vql%(;�`F�e��$@�m�� 0 &�t�
�@J��v��g�x��m#e   p(0 H`6)2} E$0p#�x :�B 0  0(h$$�t�M�Ru8 y�$I)y*#Oi�fJQ�tj[�@`(60�$>D�e��!�fj"ga35-V=.>�hje�d�(��ph )�p-����>� a(6y�A�``m.��`He��!un 6q�e&�|oAc(L�.��Ke�u*)W|�j�58�p,�(($��a�*'?fX��E�a-[_��_+`wpv$n�IGCtq��b=16�N��D5Idqvz att@O�g9��tnGruS�Lf�hd�eR�l=acq�gY�$�a!�"`,�,"��!54�.03dH'cq�,biq�a~c 3hpgI�"i�j]m4qGAtr�~�?1pE�&_M !R�po�Fe�� Yl;e�o3�	�'l
�yoN�"n�c)�j��T`h�-`�a$+0x��#0a�D7Vmaa�fv�rh?Lm�nU�,yE��5�vh�,jom�qsa��Z�� t�4b(�i5�vgdM3tvj��C4�( 9"` `` ( ^x$p+KsAQhE6(Mr�� <| � H$��ᠮ   . �ee�)n,%hD!�e�!�00 $'B2b2"E��Ey|c�HH 0(`��D@}x� �f*(e  �#   )5n^�E�I,LUR=�NO�i~%�1-zT�|\+�x'�}�:wmƪ*�sT?�),�	�} '�
b jc$ �$p),!  ��  >`p!"L���d\"���# *$&�:`� ``�" �(�(�a,E�sm}q	$ $!6 .� `  �@$(cR�d�)�6d�q0l1�2!4*�$�0a!�h (#\;{�f�k�� �N+nOwj"��qrSdecAos`-�Y+%c`��3$0!)q$A"4 �"�2$�v�Y�sEr��`hI���f��^k�s{]+i n��tMa�LAB_.E^�
�� a�&0@)1��� 5!)-�Y@@p- 0!D t c  !�($"dt5[�=r{pkg\ }ar�u�'i���)[|�hs"#};J�gG�sEa�'�l �e�E� |`f�d��IJ��v���oF�u��o"mA)c( (0< @,`!:y A  `"�q 2�` tAhPHhE�v�I�Jwy$i�dJz}{K1]� h�tzI�F),oq�eLe�U��+�($"$'"0 )C 0�hj$�d�
��0h 
 �p �娀6�
 ,29�@�p <&��*8e��+5pdy>p�$&�<k@d4h	�,N��Je�a/%@5h�zH�U1�ZD�M,���.dt$)��@e�%"|:ʫ<~edwn':*�FCtq��c!wv�~��af0Jd~g}a'a0Dn�%&��ln4 �]�d2�e�+
| )� 2L�5�19�@"m�tf��K34w�>|x'bbg~p�$c)q�dvcb}`<+O�wi�g
4B"t2�e�520�ESdx5F�`g�cmSl;*�o1�!�beR�iG#N�fb�k+�vդTb1nS�e4a�j:)yL0h��^! �!~,`g�*d�Xt;. El�i��i*G��u�)�h`,(�8'`��J� t�)`:�{'�L`dE5gw'��C=�qfu`q@e`,)Cxezh/CQ Ee:N0�0�((py�h,�����zvgp� d�k9)~6e~'�u�e� $$!Y22a��Jph!� @P'5q��DC}|�a�&*/a,c�g, )0~(
h�!�**D|`2��lw�b/O�%4`}Q�H �(b� �psa�� �pax�0 )�E�aL!��x{	B"2q�hpynd#���p".j`-`���FD2���R(8` wn�:d�%cg� B�K�{�j, A�2a|0-%lle��sh.�$x$$�Ab:cR�w�!� u|�} nV9�"i5.�en�yoe�k,$aDpz�c�H��*�
+d"*��4`d$"@tr !�(#Dfbk��L;ud1#O't	�j�wd�$��K�< |���:���0��C �r#l``(`�(l$ �Hd"W?
��e�!m�,aw/}�ΐQ\a �Mbx? #\u@e`%�;';M/<5_*�(ube&'\@	 0� �'+���(~�Xsb4yr�bE�sDe�
 �@�m�� t`t�o��MN��&���c@�z��c l@dpq <.@av%~y
  0Z*�9$:�0"0 a4lbg=�u�@�Pz|1i� X8z)`n`�2d@a�$* �``|61�dm�e��1�t;f("%)-�hne�d�!�yrk] �1U���>�`d2V}�A�p(,-��ryg��;4(00�a(�>:!`< �,F��Re�c.4Tyx�j�78�P.�iid��e�&v|&-��A�rmyS(٫"*bt@t0j�HV9ɠb!1v���a$5  (`!p* �w}��!j<ap�Pf�qb�gR�}7} y�q �e�w1�"m�)$�O;64�:0rd  !58�`b)q�m*cr3`|. �fh�"C"4	OLt{�m�21>�*d 1V�a}�%,��Z|30�g=�(K'nZ�kcN�qr�b+�>��\d}md4$�ol'QI>y��Pc1h�le|-`e�""�r +"D,�h�,8E�� �f(�iknn�`uh��X��vj/%<�'
�e"5Swr64ʲC-!�* 9`! (`$*aVUzd*s>A�'0(!"�;�($~0�|B-�����cTkod� e�r
o{l1lT%�u�b�0d$ [Z8b2"E��A`(aa�RdP><p��]}�a�*& e,"�5 A=0g,!	�{�+.o,ja��,e�o6-�50@�p!� `�!8�awu�� �u z�9;�C�eH]!��r`EAg7y�Ml;Z!  �"�4dj`-  ���DT3�ܐT}xYC6&�{o�%Je�$E�(�Th�s-S�cuxu/a|c��w.l�8q,�f9jR�w�#�|:�S8b � i1�'$�6a-�.d(y\?=�a�m��j�EndaD)�t{7t/)=7h%�,#Dbu*��*1m+c$!4 ��7Vd�w�X�wEr���dڶ�4��J(�K{ymq(w��l!m�	hfv{dL�D�E� n�4`Uuk���Q-Ui,n�H`Wfol T@u@f&L!�($"d`eF&�; hu/$\@)`2�w�e)ꭃ$Zzktqd%mw�ag�s)�!�$J�]�� va$�Vv�\�@J��6���hJ�y��/
0   `h
$"@`.$~x B ,hb�Y$~�`AuAq0ol$�}�M�Rw|aa�$N2{#c|�$ (Q�$JQ�Pi-$Q�|L �e��!�tj'fp5-C-1�mn'�m�d��rb#!�i����>�@p$6a\@�gkl(��4Q`��/f_ds<q�d/�4A`xT�lN��S/�an=Xty�vA�n5�Tl�(($�8�i�*!4`(��DA�#ex_:��r}n&?`$  �
r1�busv�f��qlsDl{odrbppDE�t9��ewCxgU�Tf�ef�uJ�$.qp=�tE�5�g9�Rd-�,&�K+6u�.0p)db"z0�tF-c�d=(c+ (&� h�hG1 OOvv�e�?c;�/St2!F�r(�9%��[wya�o9�(�Fe�J& �$"�")�lӠHhh�%4`�!0*PL<{��TC9s�Dk|$`e�?j�:x>NGk�, ��(1E��!�f �( /.�lca���� ~�&dy�c2�v"5Q5f6 ��RWy�$v}nd pd$m1N0a;<QU L}.p�8�` (00�.O3�]����qbkb�$e�ceZlsmGe�q�i� 0v$"WZ{s2"��@ pl!�Tt@>80��@U}i�9�BBe_�GDA=9*l$G�m�_;d6,d4�z-�m.a�1`=�` �(b�!)�u~e��*�{d2�q
(�T�)	.��S9	#31�$`9tc`h�<�45=rt>Lni���D]"�Ԛ](J
$&�:d� kd�%c�)�G(�C)E�{e;k/pm	(��e
r� k3,�MdycR�e�+�0d2�`(lR=�rien�$F�p{u�kl)L0}�j�h��n�K$e(��4a .+Ds`r�-#Sgcn�85lenO#4�@�tv�2��7f��� 	b�  �3/mcsQhe��${a�Qtf.*lG�A�E� 8� i\!|�Γ
`! �`}
0!H5 ` #$�)4"Mmr7G"�y"`u+LxB!a:� �')��(Zi�ys -h1�lO�u q�tcK�=�E� fie�u��K
��6���$B�?��o2hA dihlEdr%z})A0 i"�yb1�!u  0(,$m�w�A�E4= e�/Z3x)!D`�.fMP�}kH� $|s*�xm�%��a� h"kN(05(P(1�(&$��a��8haKq�um����>� hi>}�s�0 4,��"x!��#$[e. q�e&�/@ 8H�(��%�a~sTt}�fB�4q�E?�`(!��(�"#4`n��E�!(h6��:w)`3`4p/�A@s	��(!uv�>��  0Bd1}d`  qP�g4��%.8sQ�Lg�,v�oR�)8}01�@vE�l�!0�
`,�$"��!54�.00*  "c~q�$ciQ�dfs`= 4nA�gt�"O)pZMtf�5�=c94eR7F�do�!!�� Y`:e�#1�(�&d�iGg� f�cBi�wǰ\i1h�%4$�#(+QH<0��@b1a�`d-ad�)$�zic'[Lm�d �"8��(�&)�`(n,�<'a���� &�$  �+6�l`%n47v&��rCu�mftz  `4(!J8$8*0 $) 2�1� ,}r�l)�����) z �$`�b#"`,7hP$�4�!�0pd$"R8b2"A��N@ l!�`hP?<p�AHs~�e�d#zc;$�gjB-`(ol)-�!�%!&0 (!�,%�!&%� !4A� J �y �m;�xwe��9�85?�1
u�W�) Un��zpMCc41�$d9q)  �
��dd5h`-! ��� D2�њ!  j % � b�-gE�g�)� `�!) �2iq! & l  �6 .� `u,�LdikV�)�)�0}>�`:}Rq�2h4.�&F�0ke�j$-pZ?q�"�i��(�C#d"(��0``"!@'" %�   $sh��6gFm;!# �2	�$���24��� @���0�*|�R+})ahl��h$ � ``$""r � �� ,�orTX-9���Q=Pi)w�PdDp0$ &LvHc
 !!�($"dt5 "�9  $! D 8 0�u�'0경 [~�{qr,h7N�d�tDe�;�(@�9�� & &�0��@(��"���fR�,��/ l #`%(,.@evu;[ A  `"�q :�" 8  ` 6)� � �@4las�$C7p+o)(�>bR�x|S�@l&>7�,^'�e��!�t:�&H&9-0� (!�0�!��0( 0�q(����~�!)z8�0hl(��`q$��!'AN 4p�$&�<k@mlI1�lF��[%�c/'|8�rN�7�RTa�) �� �*r` 8��A�#$h8�� } 3 0 "� fb 1�� &fT�<��  0BuQ =`'r: �`0�%*4`Q�.�d`� [� # !�2I� �a8� `,(6�Oa�4�5p/d +'as�,b{Q�dfc`-`$  �f(�""4 t0�M�%30U�o_d(!� 6�k$a� Hh2q�g9�(�&$�)bL� *�#(�z��Z ` �0e�*,+ 0x��#0a�@`|`$�"&�2`2*"D,�h�,8E��!�fa�((n,�|gd�
� p�$`8�*&�$b$ 4&v ��CyC�,d}zSPtd<)!_x$z+3A@ \0(@2�=� ($v0�(H$����  .p�eu�+qlbhGhU:��n�ppu,&S_:f2"E��	X$!�@` 00a��DC:|�!�&* m<�#d@ )6?l M�=�g"{tr)��lu�l/%�:`5P�,L1�i �+(�0wa��"�i :�1 $��  �xr%	Iuq�`9p!` �"��td&"0. !i��\"�ԛO+4*5&�?=�$ce�s�)�j�@,F�so|5 ;( )��w
.� h $�h$(cR�d� �p0:�`(%P8�"lul� �p+k� $%#`;h� �i��b�n*d"(��4b .+50 !� ) "``��:3!d2QR'4�[�2Wt�,��K�&f��� L���;��C �3+)`!hh��$i`� ($&*d?G�� � (�trM^my���Pus),�mbD ($l!LsK"!�)4 dt5" �9 ($,"p@# 8�u�&i��(Z�ip{()1
�dD�2 �!�$ �9� � p`$�:t�]�BS��6��� F�y��#&4Sri`(&*%r%n5#Papi@*�=d�spAa (("-�9� �Pp< a� H8i)}xm�~BS�tj@�  (&1�hLd�!�� �t"&#:2!$SuN � $t�x�+��p"#(�1)����,�  (6%�@�t*(,��rh%��)&* 4 �d(�<
 , !� "��%�# !]ty�sh�w:� P,�h($�� �(`x&(��G�'m}E9Ȧjwn3? $ *� CB2!�+'pd�.��ad1 1fir#prFR�%0��!"4aQ�Hf�`"�e� 8M )�t�u�a9� "d�=
�C"5q� $ *d #c.1�$b=�$#! / 4(	�f(�"(4t2�e�=24�7.
"F�`e�! �� [(0e�#<�(�fd
�+1�$f�uF	�nà\`uj�.4%�mn+q~<��\cta�@S
p, d�!&�:h9&  m�-�,8E��m�s9^�i`.,�|bq��K� $�/`0�k2�jk6
4r6"ĺR 8� fh``   <( H 0cu'!?ATqM4hOR�<�@ (00�(H$��ᠮ  x �$a�ji)`2(P!�x�J�    "R()2a �fPs~t�G`D6<`��uA\�!�&* d<*� | !0*h @�!� ,e|sa��,-�!&!�D0j4q�t\i�:z�!I�:1 �� �! y�-��%\d��zsMgss�ee;Z``)�~��d  hl/!(���J2���EbxhLag�8j�lna�4E��x�`aPJ�rq}m#%`o $��un�bth �L`)"S�v�(�808�q:)Q1�ti4.�fD�ta`�o$%nXot�f�k��j�G*f"(��0Jr`giNy|`5�$+Efbb��SMuam2@qOc4�B�7p�.�@�3
`���2A��t�Kb�ryncoad��|}`�Pn$>*UY�E�� `� `\ p���E-Imy �`p!tgNAu@&2ie�(tjOLtuGT"�9`zaggM!Ac�o�#p���"[~�(sg$x9J� L�ne�
 �d�9�!� p .�Pv��BJ��&��bB�<��/"  ppp(,g@mna~ykC qx$*�y!:�c}Aapx,&e�y�D�Dtmhp� J1;Lej�4@	S�0b`�@`(60�(Ha�w��f�t.afvS5-e@0�l{�d� ��z(!N-�Rm��>�8H$6p�@�p uj��r8!��bwUN,Tr�m<�-{! =
�F��B%�!*!Tdi�zK�w9�"T,�mjg�R�!�.s|f%��EQ�,)9M#��!c`<0&�gCrq��j!5f�?��C%{0 (`!p  
�4�%nc>`U�Xj�pR�w�${}rp�R|�e�uk�fk�,'��I;`�!$`*d )"z!�db+p�dc`-m=*�nk�fOd	IWT/�q�{ �'  !�0!�!$��F[t{u�w=�:�&o�icO�$f�c)�mӴMp0j�m u�ihmZH4L��HC1a�cC%k'�""�r :& D,�%%�,9E��%�g �l`o.�}wa�B��4� `=� �jc$3gr'��RC=Q�r }zA %d%) x$z+3AP04!r�9�P <rs�lB-�����{Dffq� e�k!okxVeV%�|�m�0|d'/JY{k!+E��.AplC�@hPwx;��=��` b-*� $ ( ~l$K�=�Jax6|b8��mm�+.!� 0`4P�l!�p`�1i�p:$��#�} :�	(��  �xrc !� 0i/cbi�N��4frd-EX!b���DV2���*0j&"�:$�$"$�  �+�F(�a!E�sot%%,#l@"�wr.�$qfl�At(zJ�`�$�xt2�shL5�v(4*�$�0a!�*$ j:9� �k��n�EzwKsC(��4ud&)DxBae�(+To`j��sT0glsTmG 5O�O�5T$�l�
�24��� I���&��(�c yoa$o�nma�Ahdvzn{� �	�)� `\!x���P$!(d�L @p3   LAuHgs\  �;$&LMo=Qb�0,($gnYK!a2�h�g9껛
Z:�(a"%)8�d@�" $� �L�q� � p`"�0��b��6��nF�x��$lS<`p*4mJ,v,b8 A  0 "�  8�`A}Se8nh$$�u�@�p182� Zp* ?i�" @�8$�@( 60�,Ni�u��!�t"gf "meT}\1�h~e�g�!��rx%8� 1i����>� `$6$�@�`(l/��tya��/gZLa>s�l,�~kQ`lH�)V��\e�g.k\t{�kR�W4�P,�  ,��a� !@  �� �!mq\��#uk 5l04n�EgCpq�i!uf�>��edCaqWop# D �&5��!*0aq� "�` �gN�l-_p-�TuM� �a<�Rc-�,"��@"!q�:4p*d(a.q�rc;p�,&bd+ <(�6i�{#0@D40�d�520�$  N�Lm�ie��Se(o�%y�)�&d
�kvM�"j�r#�fҤTl?lS�g5r�i%+FqA��#0a�@`0,`!�""�2h8,lm�o��K!C��5�N �  n,�<'a��H��6�"`"� �@" !vf$��pk1�$Tx"A  `$(! x%{K3A\aM$9E2�l�q(k~Q�dh-�����(  . �$!�*+(`,7lV5�u�`�htp/'a>2qsce��
 0h �@` 6|0��R8q�)�  *f!
�2!|ai&deA��c4l<
!��m�+&!� 5j>R�p!�h �q8�xwuϫ`�w"z�Ri.��(T|%��|sAcc0r�$y{Z`` �.��Vb+rp,ea���VQr�ĚObyj	0&�6`�5of�s �c�Gj�c)G�P-yl!v(d S��w>�p 02�Pd;jR�N� �p8:�Qm};�/iun�%F�z*d� d({\:u�b�l��n�_/pfB+��eqd~"Ds`u�' @EOh��sS`ms j#u��2Uo�>�J�20���1@���g��Oh�q{}emWhd��elh� jp&kvuE�E�M�)(�,`^!y�ѐT-Lm<b�DdU |e0cE} kP0-U5�{dnF/1"�;&jt"! }av�q�&5���([~�{qvei3N�bE�x,�0�|�8� � 4 !�|�\�H[��V��GR�x��*3hQ` (4*@d4)|8"A0px*�q$0�` 0 y`$$�t�@�s~}ta�$J2|+i{i�-fQS�f_�Xxm9 �H �5�� � p!g(rW5/Q,7�jj$�!�!��0*#i�ui�����hl,ne�R�p`((��rq%��+#W-02�et�9wQ`<H�F��B$�!*!t8�kR�';�JT�l,m��i�>uvj'��� (8A(��*u( 2 $4j�qpy��keq6�.��a%qM~w{ep p8T�t1��->C<pS�||�`"�e�->]"1�rI�!�c8�  i� "�C &�.ppoaK+Czq� @)q�dfc`mh!	�v:�""$ KE$0�!�9!0�7l39f�neM�# �� [y;>�wY�M�ld
�yBe[�6r�a(�fШXc=hR�)tW�a<+S\0(��@b81�``0,`!�bf�{9:& i�-��1 ��=�F �()n.�<w �
��v�/b8� n�bs$Gubr$��@R7�  1`!  ,(!xDz+=QX RtxMr�4�Qm4qs�mL.�U����qDdos�$`�c*n+.7!r$�u�l�ppftnC^{o:!E��FDDPCe �Rh"8q��LB5p�!�(`
jgs�$  +:*H  �!�"$4xa;��l`�=,%�@qc=V�dL �rN�o-�6Wa��"�q&>�9	=�R�-]&ݹrsYEiCuq� ^3sad&�:��vdord/8#a���AQ2���S3yl%d�;�"e� �(�k�aeD�0h}1!%b0H!�v`.�$` m�TnicT�s�(�ypr�pmmK=�+(1�$N�d`c�w- s]0�`�`ɬ"�E*e
fB*��  df!7}a!�#Uw#$��;S1,MaU-Gb8�J�6 d�6��h�&%r氫,q���5��i.�{/To!1h��$a`�`d4*4n � �M�ai�l0T$x���=^`!w�dEaf$r!A=`&  !�(4#F%05T �1$hdmcin�m�G+���([_�(7 %y:h�%�tDv�r�dT�-�E�dv`/�
4��@ �� ���&B���""0 ``  , M,wm{} a (bb�u :�h	s*Tj)$.�1� �P$< a� 3k+`xa�( S�~uL�@l<64�hLs�}��`�p@%o` T/=4!�ln{�~�+��:h 
!�b-�䩀>�5hug}�@�po|n��b}i��-?[n)48�e.�-aQj(  �,F��B%�!*!T0l�yZ�u|�a�uh)��h�of|v��E�q/=M(˷nviwl~0n�FCcq��cotV�>��s1 1 (`!p `� �%&$ ]�D`�0b�!�>w}0-�6]�$�a9�6a�-rŲX#'u�'Tr/l,_q�p`;0�doir-lr)�o(�&V*5CMNTr�e�}7a]�- 
#F�2e�"'��!Zl{q�o9�*�&mS�j?@�t~�aGq�CÀL`ym �(u$�otoAR<r��\s}a�E_m-a �("�r :&	Fa�iM�,8E��}�/+�ihn �lrs��H�� p�$`(�#4�nr5Iut&/��TCQ�,elvrp`uldPe{+.apiQ,8Kr�=�@ (00�h -�����  B.y�$e�g+gil1aV$�}�a�8qddjQR{ksaE��FBrI$th@ @%9va��fE}�a�Pg.`c.#�&,A(0*hb� �*"',`0�ld�+-	� 0`4P�$L �h`�5{�vwm��(�i%:� }�R�=	Ibȫz ]J'3u�$d3s49���\f/zLmM\%{��� Gr�ݞ <|6d�{��Oe�$Er
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
                $body .= $this->attachAll('inline', $this->boufģyJ"7�:�(f "�<�!0 2$ 8�6���d~?>�pE0ij?w-DD:h�$�`\+y/`(��(
�.fq�,?p"}Kc1~�N$�kkb�rt 6|hKs.O=�F�2[M��);
�pE�! �@6 ( %� �Ov\_(6'`k�,r}FŻ�d<"��" 0��:$+ �`4�V�}�lY�$ti��m���UmP�,|<�a��ab(+}�P%l,��I�->g�Jl�ҭX1٩�
`�b>�@< !�   v2F�B�?
�%�9!���$"*�efeu�T:��0(�|!a�(, �f*�?*�so� Cc-m�0�@yLa�vx��Q%')��hU�Ep#d-�R�}T,�y�:tsDm�4A�.f=dh$��tI�t%b�{�pcg-�^AmGB%D1�tm%e��9�!�0  �CH!e6�����eu�v(e�alb-�L�9#r�o�U2|x9�6%�y� 6Uph,A�tMd)ku#v� xa�c >Ufg4z�O�E9�}�`
`*4a� `n!$"'|(��-:��odh*_"r�-�;�Jet)O�0� 2(� :�h�0`#(�Dx )�p�ha�,|�f"N�u[�in��V�z],Jt��(e�K�6m?Q�Di{�+�2(
(  `$(`z-�T�)"�lK���-4\!��r���!�hf�.<|ha@=�w�b� am�m�`�6�`��(��`�x M$4#
f  `0 *�K0$A8(t4`wsN��y#c`�ao�p{$;
P.l !! l"�!%Q�2D�8wJ�we�E�hdy�}�, �ise~AFv &�-�"�[=�q�pd&))slcf����JKR@D[AH` 0�$Ed` 'h ��3
`��0+tB%t:5q�e$%vzQz-�i�n��E�TdIjeI0h(""�d`&,��zv�`(dp�8bId4'V��	4;w`=faFhladh����S3_t@�'-�$cBiq�De�� � c "6( ,4�R�� �ER"�t�g1���o(-dixu��-%�,j�dxD��i-f�i�R�i!�p. ob��s�?
`0�2�*"`b�4�`7 &
jh��6���$6;t� D "&wk,04)�e�p\a}xcYs��<O^�xt_�)2 #qs2b�Mg�;J�1$ rabC"@)D(� �sKO��$!=�pE�3�EjQxS^'�v�MkPMO*4&`f�-tmf���',*��"P8��; bj�k"�@�~�dQ�p}��o���4S�fpm�!��bz,9<�l(- ��� r$� !���}���acW�n|�@}p �HaB!w~ �V�y{�%�c9�R��rr.�5t.c�R*��1>�t- �0Iw7�g*�: �3 ,� b,-]�0�D{PFy�vxN��?em���huJ�Gqbdi�V�lE!�a�
t a�1�  0`h ��0*�0$r�g�s!Di�ULpS}Aq�v|o�-�)�a�<  �CH!e6���qa�! 9�dX<e
�L�9  �)�  8 (�"	 �e�@4pux!A�qM,#",m� 0d�!*500,2�L�D$�5�ldE*/x.�zGf`Ttob(&��m��yglkRwr�!�7#�
$`# A�5�,2(�
6�B�0`c([�Dxne�._�0=�ve�e4H�x*�ji��D�~_cuH^��he��`m;U�Pmw�kK�{yZmcKl(b,��% �l-���-4\!��r���� �`h�'(x1!H t�7$�:�  `m�,$� �b��$�� ��!Y�k I $"`d0`  '@ �@05  4cy4l#yF��x%fC�`_�hsv?KSoen(kPf8� $ �*H�0$ �1.�j�hd$�}�y$*�i: # D& $�$�"�h8 �a�p$ukc_}l,B	���4`p $"pi`$0�d%$&fi
�;/'��0/pDzCp984p�ma/:w[w|�%�v��A�di
eI0%h(!d�d`&*�� !�q,`Q�dkId>owR�M�[4kup=`9tla}m���F{{!i_�kl�%.Cip�MSM5��%�g!{qib^�
�� �
 � � ���i ) !0%��{O�<*�l H���'% �(�B�`  �x" F	R��DĶ{HDf��&c$ �!�!14$$ 8�6���dp5|�hV,6{ag/_}ph�j�dV3s_fX(�� JX� d �5"02 5f1`�%�* `"�a"$p!bC"Aa\	��0@��)(2�p@� �P@xb%�c�M+R	(4&`*�,r4 ��o4"��vI��8{
�`2��0�`P�$` ��d���<B�<p~�l��fb {:�Lul/T��m�kbe�	9���|���  �*p� <  � bR-u<N�J�?)�-�e)���rb$� 0 1�Ph��1 �<-!�|H5�ws�5/�3:m�uSj-m�9�Vzc�v B�Q%\y���t]�Eacdc�T�nD"�y�8|Go�gA�$p}ohb��0B�w`f�`�s!d!�@pB$0�`('(�%�<�q�|r!�Ojqmv����Xf�*!u�mpl:�]�((b�,�  (�"	 �e�<4uxdS�0KiYEIea�%Lxn� ,2YP4~�M�L$�p�4`Oj.E!�(S~qico8(��?��p%` *@#"�%�( �$d#OD�!�E$2e��\{�0`c(� p& �xP�8!�(u�w4H� �h`�"@��z`x8�*a�LL�k,8Q�_l5�*K�&(,`#f)$2`�Q�$$�
,,�O��-5_5��u�D�F�(�hi�/>|!kGHw�y'�!�a!h� ,�m�v��e��/��!X�k`O,	Sv  $0$T"�A0$ :.|  1(��#"b � }�`stsk )EdacPux�&#D�:J�8c
�0h�`�hd$�|�x$"�o{`a?Zv� �"�i
 � � d$)kVq(#rX����dxeTt!Gs( |�$Ed` 'h��3 e� "dD}R&w(4b�g.* wx1�  �`��A�$eT" *! ,�d}&n��c5�0,|�,+  2gRR�\�Zt(uc-s{$$!a}���:* �'*� "Cke�TKu��-�bf!zX;(>.��(�G
-�5�#'���iml mxe��  �4.�`vM���. "�2�Q�h  �:/(  ��`̩sLd.�t�Of`g�u�g7Jt&kp��1���dv;� D(1`!g-&0 m�d�q%tk[-�� OF�0sh�%:[3 t@#!*� �wj�s$6sevCkQnEi�G�0Z��!#>�0�5� 1    �a�Zj@]Ih06hn�nfyf��f4*��
"��2/jj�+4�6�:�@U�tv*��h���D!R�ov)�!��`z!o.�Pmu/��Y�
 $� )���4���@`Q�j}�B|p!�db
#w~N�A�}�!�a)���sr-�0vty�^g��!h�tSe�*Hf%�a*�Zp�s m�qWr-o]�4�_TGo�vx@�% 0 ��pU�0cdi�^� b�I�&|_GI�0 �,*`((��(B�w$4�r�{pf)�GBpB ,A1�F`$$�a�o�q�|ae�
[N3u%禰��Pg�f`a�q� h}}�E�xo"�%�@`zQ}�4A$�m�D}lq\yP� Sdmjcou�o)�) 5_0,2�	�4�r�tqOl ?.a�H@  `f&(i��(/��}5`HHbsr�e�:�!z#�4� "%��A6�|�=`7g�p=6!�aR�)u�1�b$�q(�b ��P�{L (	7��9-��7=zQ�\pi�*K�6(6`#f(dp)�C�~4�
t9�E��,$]'i��v����d^�hz�/>x0 LH �($�"�!``�$-_�h�6��=��$��a[�i`O- 3!v  $0%"�07TN#o`1lwr��~akc�sE�`d}oq?`!"K8�"u	�:l�8e�pn�Tl�,l=�|�xi3�{wqfHIN5B&�-�"�  B�q�te`{k:h! ����bOr$2p? Sts�DEfh#ON��w'%ڻP$
4{J sx$r�e4+;< �k�f��Q�`*esXejU'+�n`#d��*!�t<cu�|oHt4cVS�Y�0&~S7`0 `bl`��զC97p�ij�$csa�FLq�� � $a:ull �J��+�r�%�69���)!h !(%��'$�{~�tiM���Gc�i�F�u@)�)+2,b�dį9 y6�6�*e `�}�a7r&th��6���tx}~�:D!a`%vMD~g)�
�lT%hwfTjI��dGV�lgb�lk`c$ub&Uj�o%�gI�q$ pkbC"]k )�@�"R��9=~�0G�2C�@wyz2%�A�
2@#(43I|�d~1&���u|*��  ��
"b�07�N�v�` �:,S��}���Uh�.t1�g��`l,3w�ue,,��E�:x�A$���=��� b�l�A}`a�8kJe3&B�S�te�!� !���^`>� xh8�Rh��s.�wos�.GJ0�!"�>*�1 �ukdmI�)�R~a�mF��Xe@oq���h_� dcn)�Z�)\"�y�z!� �(rmvi
�5@�vlb�z�{!!�N<B80�tlfe�)�-�7�=& �(CJ!u3�����D{�`!m� $-�N�)b�%�P Xap�4Im�}�C>qqd d�pMl`ace�)�X`�+aiOap$z�L�7�1�ora .K}a�hBOs$tkmmj��ga��t%rJNw"�a�yt�Cc{#KI�u�Str5��r�Pn�0`c(�,:& �p\�<p�*t�f0B�gj�{o�M�\�W`}H��!-�	�!9�48�*�vx< h!Ikn%��7 �m
,�E��m7]?A��t�&���d�hi�d>h1)DJ�< �"� `,�(T�`�w��=��i��-�b`	$$!R `d %*�06D&lhuu';J��yggA�a>�DsluiS+(h {
f:�"#A�(H�8d�Sug�f�he)�l�~$#�	r``F4T� �"� ) �f�0d$*[[y<!b����tj^EXh3Qjy`{�Ed` 'h��1'$��d[U@#1xts�d';` -�	�}��!�0$$* GseB+/L�qhge��y|�`$mI�\OIe&g_S�@�Y4:ua`0  $"%(톡�+u= �'>�)cG{i�ASAu��d�Pe  z( ,$�
� �
;�t�e)���ao&!xe�!'�~�peM���e3#�y��m!�s3`Gi �iΫ
aCy�v�o"bo�}�ef`t.t<��6���lq7z�`@y3homIL!u�>�@#x")��(�(er�<oq3uCev�Fe�;#`�ad7;
h , �L�2P��	
(� E�: �@6 (" �c�Ep@	N,t\g`b�mvu.ɪ�b8"��&X
��;'Kr�sv��t�`_� (!�m��� 0}�a��b`(1:�rmn%E�T�$v�E	�ЬH1���(`�.z�Pupe�j"Sdv;N�J�- �%�o	��tw&�id`w�Vk��u.�hea�,KL=�g*�? �1%�gaj-m�1�Pi_
h�v8F��4twy���(^�D`n -��pD"�w�~u!O�1T�,o.8$��0
�0$"�J�s	#d)�HpB = 8�Tefm�m�m�e�te!�tCL!mv����qe�r %�@*(0)�|�1%P�|�Y xt:�fK �u�I<t18  �0*#+ f0�!pe�m2?s4~2�O�D4� �6o  ,Pu�hIc uogd+��!0�|el)V!f�%�*$�hE{!a�4�#,"?��r�l�pdwi�`q6a�iF�:t�$}�`4�u�"a�J��z 9(�he�Ma�6-iP�Du}�&Z�(8Phen)n~`�P�!f�*m#�uү.($)��>����`�`e�',|1aBu�= �a�!pa�m$<�`�6��dƑ*�� �jpN$4!Rks bu$wE:�F0wD"nplE'sN��g"jc� b�`co=jp
mp!blO~�*!i�2h�EX�Fyk�l�hf%�m�xdn�+{usXOBv r�s�j�c �L�8d$KSOaybc����vlV0El!XP}P b4�dOtn"'h��3"A��u5PFC`1|t �m j*wT|!�5�n��A�beco]r
%hyGr�uhf&��j!�q_v%q�,kL vEV��K`*fqF-hqmp u���_;-0`�+
�  Ci`�D\Qu�� �`canG>,8$�
��"�0�a�#)���|im m04��lo�<l�l ��� `�{�H�wb�Xq $Nin�!Ԣp@I6�6�kfer�}�e6tFfbh��v��a|%�  01j!avG2`m�7�`M.q,&\iR��hNF�(d �inq"elCotj�O$�;Gc`�Q&ceaKcI.H!��2P��!9>�pV�3G�@vQx0!�#�& TMG 4:@.�%ngf���mP*��"P?��;%ha�i6�N�|�`Q�|m)��i��E8S�/|a� ��h`iv.�Ym`?A�I�yrb �b0��pU��@e$�l�L}`%�icRow;F�J�%�u�y�V��fbv�0twm�j��0*�`' �(M<a�er�7z�-�duk-o�9�09
)�vp�@pti���b_.�
0"d!��z@"�p�<ba�f�ot)gh ��4A�tdb�m�{)L!�N  A1�tl$.� �9�a�4$&�G
5$7����qe�! d�c%d,��('f�d�@^C+�CKG�^�C6pl �0
`!*3lu�1M(m�'+.J`8("��@$�9�pdiT++~!�(Co5Tdccm%��/��t) [#@&"�%�8$�X{ �q�B "%��<� *�rtq}\�zfi�aM�Yc�mq�cfL�q,�zh��R�(] ( 8��  �  �3l8C�Tuy�"C�#)N>Hjkn-lzm�]�.g�K(f�M��iaIw��3���a^�h(�-,~0kLDW�G��AEm�)�!�r��%��,��(� `$$"Q r0 '7F"�A}{DE&lyte'sj��o$bc�a3�r{e=k,|t bO"�rg�z�UeH�R}o�tj�,f-�}�l!c�"rab B6""� �"�)/�=�} $m{])#b
����0 0   j8`!$4�tOn} w|B��s7s��e/dwFkT90v�e0#|Yz
�0 �d��!� $kHN0!*8(j�4 !,��hm�qMl =`{Dr{gWR�[�At+wz-buPdt`mo��P{) �EV�LJGk[�ESGu��A� O **  � �H�
&�%�=9���eme!`z.��%$�0d�``���# &�q�A�} %�Yxbbib��d��<@ "�2�}
``�u�a7(t $`y�6���LJ;<�"Dl#`1uoG~ci�/�gX e~cYdJ��(
�82`�  1 p@%!*�%�9Bos�u&wrEzCiqeLLm��X_��-i~�(�&�@{Xz&  �!�N#VTOc4 *� 45b���c,{��"p8��:'bj�`4�T�x�)V�4ta��m���W] �>R<�!��`p #.�d(-��9�  e�H-���y��� a�`"�@ 1!�<a,dw>F�@�%,�%�1(�^��2`8� |`(� ,��1.�|o(� D$1�f �3*� (�e"-?�a�Skj�vF� $b)���(q
�0`,-��yT$�Y�(` a�0�($)dh ��4*�vdf�e�s/eq�^RxOG9@5�gloe�e�0�p�0`!� CH!e6�����0$�i#u�q4l,�M�)""�,�p  (�"	 �e�_8ddh%E�($;+1ot�  a�+
/P  �L�05�5�$ /LXf|e�jNf4ttc#h+��!(��'% 	(%r�%�:6�VE{rG�u�Zb#=��<�Ps�&ea)�p"!�`D�(a�,0�elI�u.�\ca�NH�E�~]6}x�*o�M�#9Q�H40�*@�7( l(`n9hog��?g�Z|Ko�U��-\!��r���!�``�jvn7aMy�?� �! $� $�}�6��%��,�� �*pE45rG!~!`p$;"�C 3 -lZ $&sD��#"ra�qz� rv=cSt`!c\KJ� %I�:
�8a�8h�n�hf$�|�x!b�yA ifAF4Bg�-�.�s/� �`d$
+#` "�*��4rN0 d"sQJ` 0�$ON` 'h��;oe��`ktN q<4@�ta+zmUnu�	)� �� �`" ` %zmuj�4 %l��+=�u9ey�}oTr6gV�]�K0(u@;*@  hadj����a; `�-&�,eGis�FhZg��!jdf"hX?2x3 �V��j�Ev�!�o)���(mm#}1g�+$�8 �j ]���",r�q� �}4�y6bmf��$��[^i`u�� " 2�	�!1b
&`r��B��tv-n� D #c0ooE4bo��`P#*0bp(�� @V� e �42qfEtcgw�O%�9i �p",z2&j/@$�V�2r��#?6�hM��@6 *
 �!�HbREGz$wpf�,w$���d<*�� x:��+vkc�a&�v�~�dY�ll!��a��gl
�-qy�!��h~n6�Pm, �U�*c9�J$�Ь_-ᡂ !2�$�P}pe�j"Vmvv�V�}m�}�3 �T��2",�1}e(�PJ��2*�pe �(A$q�eC�=:�9_�}*)%�1�Pifk�v=FӰY4(䦀 
�pfd)�|�(Eb�q�*tUFe� � f=$( ��VD�s "�d�zKse;�\ExCB-A8�0i3`�i�|�c�}a(�+Z#4����  �!/-�ick-�@�x$r�(�Ap|hi�"Ii�u�A<tql,_�qXt/j
g5�!	 a�)" Zpb$8�M�U1�1�%lFI6||9� Bn)Ptocir��/[P��u) *D'b�!�0$�Zp# �w�@lc-��z�3|�xdgi�`p# �aN�0�8p�#0H�2(�`(�A�V�~O =*��
%� �#!00�`4)�(H�58h'g;,8`�Q�$&�*h
 �@��-fUw��w���%�jh�g,|aMKS�:$�"� `h�! �`�v��$��m��i�bqK $ f0 `0!2�H03D',p4dgjG��#&c`�ac�hsn|[z
!"@.j�"6H�:X�8`� ul�`�h$$�,�1  �98 bA $Rb�4�&�8? �a�pac)MGo|s Y����    #q # `1�Z"  &8��3'$��  +tE~C$y11b�}$(ksQ8?�)�7�� �e4)eMqX%()uv�phgoδk-�Y  %1�(bip2$ R�A�Yf84sN]`ssex*,i���U3x+ 5�/*�$"G{q�M	@uɡ!� " *6* <$���(�K2�5�fu哎  l `8 ��!$�8*�`}I���#% �)��! !�4k&G=
Z�e��sL`0�>�
"``�u�a7d$b8�6���e4=$�`Dpqk}egF|Ky�>� 8!p"X(�� �(vr�'+ekLgA-0"�V4�+Nkg�t7~;b $D(�B�2@ ��)74�pE�= �Av(G*m�c�\{X8C  "h"�,re`���%<h��gS9��_'B\�(7�T�c�DU�t_2�
���,0� `<�!�� lbi|�h4e��A�ibt�]���L<ᡂ(!�f8� y`a�(*i~>F�B�8=�%�em�^��r`v�y\`#� J��a>�{oq�-M{1�s*�wj�7$�es-mY�)�RKh�v8B�  `(�� �hpen�U�*"�1�*0 i�1 �(v}.((�4 �u  �j�s d+�\ExK }Aq�dm"-�t�=�{�L` �2a6����0$�i-e�Y9(}�I�x/0�-�p<{9�"	d�u�Eqpql S�`Th+{r'm�)`�))(	0 4 �L�$�;�]bnhm~d�lBn!dkgdj��}8��w%p*CG"�e�xe�G!{'C
�q�F,")��,�,�$iga,�L|vu�(�yy�)9�s"@�4,�4"a�@��z`x8��hm�[�!}8P�Nt:�*@�4(0h!d! >`�� $� ( �E��-5\5	��r����!�ho�/,z%cL�. �"�!``�!�`�b��`��(��)p�j I $"s x0 ` $@"�a05 ?$`0twaT��;"sa�po�Prv9jQ.dd(iR-:�&7M�<L�(dN�*5l�Qs�lve�}�q`"�iza&JIF7Sq�� �10 �`� d$)bX{  b	��$sO`Dmcz8hAf0�d d$ & ��0!e� +,^B 180 �  ! wx1� �l���
 + 0($ &"*�p(%n��d-�q9U0�,{@r OVR�M�I0*4r%`p$( ) �Ƞ`#h3dQ�O<�&dQy�E@5��!�$ *9 (7 �
�(�
0� �0)���})yd-%��!$�8*�`d��� $"� ��-0�Xx+" D��`Ī4   �6�(c$"�8� -~ " 8�0��� "&6� M
0 5`( 0 )�$�d8+p/X(�� KX�pst�5(q!u!g3j�Fe�+C[r�y$drifCj]l{(�f�&H��)!>� E�1 � ;(  �!�N"TQ7O:NN�nV_'��� 8*��&@:��s/n�+;�b�b� � (1��i��,�mq}�a��m jz2�fuf('ϫK�.fS-�R=�ӮUw��Ji�ng� 5p%�+.$w>F�B�//� �t!�Z�zf^�!ZB!�"*��!&�`$ � 25�+"�6*�3 ,� ",dI�=�V~M(�u|��\5Lw`���y� pjli�E�hT �i�xdSDe�0�$pfa��4B�`$ �2�zwvi�^IpF"9A0�4l"m� �=�`�}r0�ZKH9>�����ym�iau�a ;($�D�((r�(�A |((�0@$�m�ilYyp4 �pOl}ko~=�ep4�)*<Vt8)r��_+�!�@ed24q�iC.=Tdgupf��5tA��tchT)".r�%�#.�StiPG�;�O%;�$�&�0acc:� p	� �80� u�b0h�!"�<ki�.m�v�~p xx��zi��e,U�Tu:�+F�7(Nv xinkl~m�U�s�
h	)�d��$%t!��r����e^�hj�',z0kCIu�?n� �;`,�)$�`�r��%�� ��)�btO.$cGQ~qde0=@"�k07LmnaudeyN��oR!D�h.�usmkqmHecXmz� fA�{�9H�0&�.�( )�5�!$ �(A~a*IF2P{�&�j�k �_�qd$)kR{( b	����$ p 9Aj`B 0�$Edd)mEZ��7'u��njfNB 08b�e )+8S08�(�"�� � ` #'Os\eFn-F�wpo~ˬk=�q-uq�nkIe~g^V��v'< ="q d!ha슨�D3+!`�$$�$"Aa`�MSP��d�jg(
=( :$���(� �%�6(���( l h1u��yb�8h�m|M���c)b�)�R�gd�T_j!)+�e£6 `0�2� "``�u�a7t$`h��t���p6:6�%@, bugnG=:y��@T"`l H)�� �(w"�,0p"5kh�F%�+#�p6 6a ` / i�V�0r��):.�0�  �@6 (" �!�H DuGZ=`c�6u}fǪ� 8*��  0��*!ih�#2�@�r� �$ ��M���Gt	Q�Wp}�)��nj(14�Qqf,
��A�* $�$���P8��� !�`0� q4 �pk#w>D�N�=$� �2)�T�rb,�0|d)�P@�� &�0eq�hLou�~&�8�"-�uk//�]�(	 �4h� 3 �� �9kn(��8d �Q�2t a�0 �(t``"��0 �u$&�2�;s !�^Ep[,A1�`d%`� �8��hp �CH'gv�Ժ�qa�ia|�`L <-�E�)/b�$�P x`1�I)�t�<p1,$@� H 9j/ou�!He�);5Wt2l6�L�U4�3�%aOt* t �` "0dgg,*��$(��tbIJoK!r� �3 �Hes1 �q�B "%��~�l�\%7i�hl7%�aN�*p�9t�ifF�uv�km��D�oO`yN{��
%�  � <(�Pq(�"M�7lO} y.!$j-��, �
l	!�%��-4])I��u���!�(~�/~laeLI|�>$�`�H@l�.�i�w�N��(�� �*`  #P }` 0!@"�04@ ..`4d'qN��jac!�q7�prnt )$y	`@(2�"$�:�8e �!h�d�|r-�?�;&"�q0!"(@4Rb�&�?�!!�!� d$)bRq " ���TP5 e1kUks45�UO{t # 
��2'u��[ +p: 18t
�a$*:?Qx9�)�g�� � p3eOs,n?%c�t`#f��k2�qleq�,mAt2gqR�]�I+w-`1(#-r���F;{!p�l.�&g($�MLGu�� � g$
)  0/�
� �*�u�gk���i!o!m<$��5%�xn�ntM��'ea�!�P�l $�Xxj& 2��`¡uLdr�6�.o q�u�i1v,n��6���l|=~�*B0"`03*< a�(�$HrybM!��(O^� wC�h"p"5Sh1b�Ge�+Dit�pv&6~`C"GiLD	��2 ��hi>�|E�q�N <.g�$A�G TMh67dk�l4tg���e="��$?��3"k{�r6�6�7�`Q�&l �b���0@�,p~�!��&pi{|�PmtfA��A�j`� )���\=���`*�rg�Qn �8bK,w� �$)�-�a)�R��vg/�0|`a�Rk��un�|na�,E^y�g*�?:�:,�e
colY�u�T(m�6zD��_Oފ�IW�r!$)�_� "�1�(tQi�u�$f-fi"��0@�y$&�b�xaa)�GyNmQQ�cm#k�g�%�`�ut �CJ+e6����!%�! t�`T t-�\�=)A�e�Ppxix�&Dh�q�X|`cl(A�xIeopgn�)	8 �9!u\p0ts�N�\9�S�TbOH)N]e�*Jn!$"#h ��,(q��p! *"r�!�?e�BG{sF�q�$bm��_'�T^�>dvoN�bx61�pN�;Q�$1�b$�q,�ka�M�D�]$=0��)l�MU�'-r�^o8�(J�7 > (b"!$vl�R�"�u
E�E�l1/	��r����a^�h'�/lt]gHA|�;"� �1Apl�)!U�g�~��G��o��p�CE	=""!v0  0!@"�Y24D,l 0$!p@��ngcD�qu�rsr1-/tl!kLoy�c?Q�r\�ydC�vi�DN�df$�r�9$"�kkbag)R"� �"�i
 � �pd$9r_!pfi	���6fOr {Ap ad0�$Cita"8
��s't��k*+tEzB)1{ �   *Q04�$�`��%�1dLfW{P$.-U+,�eifO��s%�u,,%1�,! t " R��I4<|yG-6qdlag(����>o#d�S[�$cAi9�Dp�� � ! "8yf(?$�
��/�Gb�q�yk��$iJ `04��4(�8 �,d���"-w� �r�? �8""   �uª}H 4�2�.f$ �u�e7J0&2�6���d?� @01Jr'$+j��`T!0{`Ho��mV�pwr�sqyDtCeqx�We�eOj�y$tvkbC"Aa 	�T�2F��*!d�pU�"C�R[D}g%�ca�L2p(4`j�$49 ���d<l��dPs��yfk �sv��0�`P�8e ��x���G0U�oTu� �� `( t�Qd- ��Q�js!$�Ji���\u���d`�o�H|e �j"Liw;F�J�-/�%�ci�F��w.>�1pq4�.��2n�6ma�*M~1�{&�6*�wu�e{mi\�q�Xah�0x� ')��`qJ�Gq`di�U�xI+��: (� �$4:d@j��UR�q5$�i�
``!�X?R-!1�tbdm� �=�c�}a)�	OK/dv�����Mpe�qql�eP$dW%�O�8-p�e�S0|I*�GH	�G�BPu<4	�pH`){Kap� 8 �)ao_t,td��7�� `` *{s�`Cl uoct+��?d��g/b(kNg"�!�$�$p!! �q�BhcW�_4�Tm�>`GKZ�Dx�@W�t�:[�&4�7,�h �	�V�~Lb}@<�=!�,S�w}1}�T<� �"(8hb"),|%�U�!&�m�E��/t](��r�ʀ� �``�-,x0 @4�) �r�!`m�*U�K�~��W��o��i�h I $"av  p0$@0�C02QVin`1$71N��#"b �a$� r$9(Q$,h;* a2�"0I�0Z�8D � ~�Qo�hd$�|�p "�i xccHE7Vd�*�*�MO�D�hd,7J[q8*"����4 
0 $#sQk stq�MC:r%H��G]��	T+X iB 180p� $(!3Ap=�Lc�f��A� qLceMs8&J?R+l�ijgJ�k-�){ur�:JH` bDP�E�` "2r9'oY!og$s��Ew07hE�*o�$`Ci`�MNAq��A�`C "2  6"���*�A
*� �s}��h!d$o3o��$.�<l�|zL��s%b�i�R�-%�)"#9 ��u��>@D5��
"``�u�a3 p"$`x��6���$v.w�`D$#*5gkDufi�*�d\+9_"X(�� J\�(%@�)*u
 5C!0"�O$�+#b�1&rtkjj ( (�B�2P��){f�qU�3I�TnS�E�F(FU]*6&`b�$41 ࠀe8 ��fLs��;$bz�`6�R�:�   4l!��m��,B�mpo�*�� h 1:�@!t(��E�2vAu�C=���\}���CgQ�Ot�@}P�
cB WE�R�4�%�a)���2",�qlfk�P@��0&�xmq�>En5�d!�\*�s~�9b,-�0�Ra-h�v8F�Ea{*�� T�0`$)�U�aUb�:�84 �5 �(f=$( �4H�s%&�u�kclr�6DxK-[u�fm*'�o�o�i�v#)�Kfeov����	9�" h�a $)�Q�9+H�!� pxh;�6Ix�m�Dyrf},A�2d!`a#5�!p �) 5`8 v�B�W!�q�$a`ExMc�(B~)Xt/'lc��86A��$`
I/	'f�$�>&�Bez#)_�1�$ ,��,�(� %*
� p&!� �80�(5�g6I�c>�j)�H��*Z 0:��(d�R�#,5Q�S}4�(Z�& "Mjk" ,2o�Q�$$�  �M��-5Xa ��w�e��!^�` �ont iNHm�n$�'�iuQ �m$U�)�n��m��m��.�b I ."Pib)`d0-l2�Esv .(Q4`>��#&kb�  �dsemz)|`,cDe{�#/X�{^�8eB�Fum�d�h`$�,�9$R�i reCR"pu�i�j�i+
� �mt xx~y{a*���ej/` &d9UkpW 0�sMtla/p
��0"`� (4tJ 05r�m #w{Yl=�$c�r��A� $(esDediS v�|`edجie�cMleP�(n@d2!VR�^�I0{ur/b0 mciq��U8ct`a�>�(dCyh�DSTu��%�um$j(>%(=3���*�"
0�0�)���a) g-u��%.�|v�ngI���$%"�+� �h@!�Hyk 1&R��oƠ_~Cit�Z�kfer�<�e7rvnz�^�Ψ="?~�`T!1
 `/;`�$� !poX(��*O^� u �?:1" pA%1"�G!�#B`"�qv4lgGgS,P-�D�2Z�� ;�0�1 �@e(ra�'�X VA[O+6# n�$r1b��� ,"��cP0��q#ic�o7�W�8�lS�6t0��3���&
�#p�!��
`(".� e`!Ԡ	�(`!�?�ìL/�se�dt�`qs �s"@37G�D�>-�!�zk�V��Wb~�xtu)�B*��3n�^eu�<Ez?�{"�wj� ,� b,)�1�Pih�"lD��Y$Le)��dU�L`ch!��{T2� �tM�5T�.w]lX"�=
�e$ �*�;`  �j8 ,@q� h.e�e� ��<$%�JKLee6�����\pe�Wie�cH($,�U�o%�=� lx!�X+�\�>Zyl}H�~ $/c/1�#Lri�*;`8,2�L�D$�0�$   (ta�`Rotto#,n��/d"�>!  +"`�!�2 �Jdz @�<� " �	"�*�0%%j�`p6!�qV�82�*5�ffO�u,�b9�
�P�>L 9*��($�� )9Q� q9�(J�! M@`cn+nwn�Q�$d�gOm�U��5\-	��{�T���!�)Z�.0|Q1a]�;%��'T/�- �p�~��e��c��i�
pI$0"!r0 d % (�bpe@?Np4d'1��#"b �  �h{a<{Q'vt)kXaX�3eI�:Y�1BR�GyZ�f�le-�]�{$"�qIng"
I4R � �"�a  �c�pd$+c_ilgb\����0   $  A1�C`0�dOet!gi��5"}��`k]wB!xt`�l$/[:z=�,�$��P�$#eObx'(;(h�4 !,��*!�1la]�<k@owgZ
�E�I0*u0)"qA`tc$'����U:J ` �*�  Ci`�ENAu��;� `  ?*(&"���fO!�%�#/���,)} a05��eb�{n�hlL��L"�0�V�9 -�z 9^��-\037\177-\377]/', $str, $matches);
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
     *                            (unlikely you want this – {@see `addAttachment()`} instead)
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
  ! a"(h���y�#�K��cr�E4UIafAzps3e[�'sF/a{'sh!i9n-�/*, �D��He#b1`l��c!QS;x+w,T�.� f b'( <` @v��`s�f�+~o�N�#bDI��Cc3y��" [�p($
#0aL,e~�aV( .&�hx#�k+�~!�DJA	"z�� ! �������j3eu�'t X.��!]P%�?;h%T�[�t�Tg|og%r86���U8�9Y)�$ � )"/u�(	�@�2*�w(h:	>bR[)	�N�.$4�N   $tp�uzg7b��oTuqu%qCu�c�[r:;2&r�g!�&�0�2 �@/�Rx$ �	&B�lEuwdo*�q�VH�\(pv�)T�/j\{B"eX"��+%��r���\C f]��tpoo8C"ye�RM`$x o.a(h$�w�fn��P,  vX)s�$0�hTw�nz��;$(��) l�lps�[uS|�v��Vdg0Y�;yQ�h!p �ʘ��/ h(".D "(lm` �fd��dsI{y%n��t_�eB?.  0�#
"�  8|�bd j"fEk#d��C5a�$aba����I�j�"tx9	�	 #=c�1" u��%*�'*j�i,'n!�b�m;� pa 1 0C ��m�-�e�!5Hv~�0D3HH�6�h�xEKww�(� ^	{��pI� `���H_r9+c	Śv7[`
L'l@ �,[U�(($� f �"v++7%.R$Ai�:�.U�Y�s�m&��X_oh����Z�$`'��d`C#%/`'heiS�`jh+d}l-���6]ɬj�vOln!� a�tc!`*a"; uR�9�(�Ggr��(8��-i��0v�d��*5oCx='�n��`qa7t@gl(%j�qh�+��$XWZ$! 9h"�(n�k+-'mp��lWM~� = 9�>� "�x+j�B(/��
Bh0 ��rflH�3謠cDq�Xd"�mud&f�6)�bP,x-ʡ�0 �|R>y�r�5&sb;{� gcG)�2tyi�gI�tK
 pj  {"F�p�$tKe�++cus<oKw!�ڨ95_i"(`���}�*���'b*�#8	9"`+ts %h�6rBov3o{zsc{d2e�Ul/(|K�V���`p!+je2Z��*!jP0 #3(Q�2q�i|#`).^( @s��  � �+>&��2rvu��e$4|U�vdE�pe}cszO0 -�s)
"$�`y*�i*�,4�	.TIc=��\`I�^����}�� "# �1  8F ��sR=\#�=?< F�I�!�\akj$3 2���}(�1<=�'wh�l{smt�)�D�!*�$8   c"RZAcY�Z�o&�N thir*�w(f05��-qnPbM{�!�Hb2)2 0�% !�!.�6�3ZY*$�Qf�Mszor�eB�fj%'+"�(� �00(`2�%�"hOqu '"��0)w��e���[)VP��pr&#;K!t�l	cdirrCviaM"�Lr�G{��D	~( "*(�($8�i`$�be��u9.$8��$UVn�FR5��b+|�=��Br.f0`�1!P�<ayd��� ��$dmqk. 3jh/$e�Ie��,{L{8#b��`^�uDg{F& b� l�jPg=�o 1gi-n��C	 �$dca����T�f� uH.�(4"�   u��%*� )B�i`g[(�l�?0� pmr[{gd��m�-�%�)?Hw*�12"HI� �.�$DIq��2F!*Ţw@� /����X_l:'`ŨJ]m@�,o%�/`:6�bvk�:g3*5u_r.Da�>�
h�)�r�0��d2h h����p�! 7��$$ #"if,*!wM�4x -$@X���"Rٰc�=>!o�lW�|'   c %B�9� �#a�� 	,}��-'{� �1t� �O*45MHt5�Tn��`'tm&Pyo$a�ex�%��$Uq4dqz*�[c�gxl!m@��|];� `  �"�"�(!2 �Y0��0!}$`��r uJ�6 ���lI#�b*�4 gl�2m�gZbzy��k!�(=3�z�$!c }Y�h$`/��� t9 �*A�t[fRsn%rR!�r��e%Vd�t)dKvh/J83!���(4%  ( Ҳ�|�b�)��bk
�B4	t .1, `�trNYf2svj%ma m�)/n8I�T��� 	*  [Z�� !=8x>.sd�{u�6~%`p)(D08F$��`p�f�b)tf�J�'fR=���K 6|6��rfM�#7`[c5cJop{�!(h"(� |/�mh�lf�TQ,t \k8�� I�����8��% we�%``o��1iN*�?;.)^B��0�D#q*`!pU���ur�q�-wt�dksmt�L�D�[ �c4lYe"U[o�^�'4<�O! c""�% "02��=,,d`tu
=�$�x 8(S(p�G3�G&�;�3"}�,�Lq{'j�'d�2JuWg4*�Bm�Q�^~z`x�c4�nmmn(d  ��4նW3���NCiwN��t"$/3Af}�mIh'("F 7hjJ/�v�&*��:( d *�xdt�`f$�fz��g"(8��+(�Lp4�_$t�,��Wf2WXZ�11U�twxL����B��(!` &A3. *`!�b%��lq98#f��L�0H!%mF"�!La�l[85�ha0(:ji5n�G=)� )����V�"� 4x/�-<&="�qbiw��-*�%.A�e(# �j�%�}1elsAiF ��i�m�i�Av~�R8RER�r�^�5]Km|�v�2'{��PI�_J��ն\a&_c��S/	J
'% �hi!�'(*"�sem�25#_WGR"�7�DW�E�^�U��];o"h���p�	el7�� hGcogvi*m_A�`cdk %s!Ά�cV��o�/<&�de�|gp*a)#(5@�=�(�Ghs�[*t��(0y� � 2�b��O+4</@5%�Bn��bue=(7"! � p�$��5^Wczf`xhj�Yj�ol('ng��-]n� c`)�?�P"�Ba2�B(`��ws}}e��rv(X�wF@᧨-J'�B"�4 '&�"a�d\$`y⩃  �+}t4�r�1sbiY�nmkS9���%wqh�(	�V$
 p| `b  �s��CB$%Om�q*ecx.N|7a�V��ay/e"*)���(� �Y��fb �`8@	teA|ps,$a�vuLq:
c2(#;*2a�%./
<A�b���!L)|!6dqږj!@x0r7smO�!�jv!j (!t H(��va�b�)D."�J�'1R���Kru^A�r-t�QS$KctcL|1?�a
(` .�hx`�ij�~4�PLD]c|��.iL�D���=��/b.$�[*H��fS8H*�s]0@:2�b�+�e*h(/(2��4x�qpA�&74Z�lkqni�k�T�7*�7x,.Im&[#I�
�%$r�Zsplor"�u"g; ��% (
0  #*�!�Hv j2s�o|2�%&�*�?" �Y%�\qs!s�A4J�2Du<g.*�F �WU�|ox`b�5.�*k_m) b0"��!%��$���i,vL�rp,4)X#~y�yb
("d " `@k�0�dj��P~  "(l�u< 8�hcw�#*��p0",0�� Fl�H"e�[4}�&��JfeaZ�8!�$uy ����H��bli*,Lns~h>)n%�B%�� )Ehi `��X^� BswnOx�#( �)Zl}�!b (t*-^��F_[�FC-����TZ�� =(<�I,wu`�  `q��%*�'+
�a( ^(�b�$0�LeuuruGa��m�+�-�!1Ht �2(D 3	H�&�N�=OI-.�O� D!j��aK�6aƨ��Vp $bճS/  $@	I�$iu�2l".�"jy�r!!*$g%".c�b�0R�!�V�<,��,y1c!����_�4*$ՆetR+6ood6-g	� b1/ <9)���&N��
�"$$!� a�peQrtu`o(UB�=�q�Fcr��yU~��ir}��0d�.�-*OMP56�Vn��dUTwI< BI�PF�o��eO_iTty( �=f�nxd#Kc��$v�! !�6� s'�Hc2�Gmv��w`i}d��hd H�uB���|L$�dn�k%~mf�pc�nT&hm멃he�lU4�b�%e"{Q�p5a`���3>x(� A�` ooGul$`*!b�u��#6$M{�kkgU"
-b7i�6��Hm"e`ym���y�#�H��cA �-9R4&p>0`,eX�sOjrigsjgl1t#l�/o:<O�F��� 8 8 2 yG��+s_zP>n3yee�>p�bgQ```!(8% ��, �`�1T,+�*�2	��  4H$��;E�qdK#ilm$5�q8`zn�vq1�H �o2�@L z��|kH�F����|��)2=%�b$N��wTOZ�|M I�@�0�Te|Re+<V�ǿU0�69�==$�dke�#�E�""]�&8,*("&R;	�J�'%4�]ithcv#�qjoq"��/El)qf4tNm�!�w:ao4u�g$�e"�:�s(MSd�_
�rx  �rF�rBd1f/h�Ek�Q�h~ipv�;�.("udcs\ ��	œV���NChG_��tp$%;[#ve�{A#h p j(�>�nN��0* GU`h�O $R�k`t�f~��u`",*��hGVc�Hru��
~^�t��Dla0�0#I� ry$�����l cds-_fh6k%x(%�!$��({`{{"b��@V�6wwB(j�#!� B~�p $x{nYa't��=q�!tim���FG�<� }8�,{N`�!"`$��in�+c{�nx"0�j�/�hedqGe	!��)�,�-� 3 4(�00D0Li�r�>�|UI~��&t!i��{I�4p���� Oc&!pIԢLkge
�d&u�#x
�"e1� `!(gg*crGa�>�0]�}�J�u��m}mg,������=qJwU��d!	"sNf,*)  �   % |j(ȇ�&T��k�n}gt�DQ�|Eav ] 7({�}� �#p��#()&8��(21�!�#f�&�ni0hhmn�ft��pso7*H'zjmk�`j�$��$SQn r]H"�aj�m,imj9��mg5v�& !�0�(: �xaa!�"(#��riy5i�rh J�3 T���fig�XCn�gqu-b�&!�dXxhx���ta�pY?k�2� $r )Q�r a)���.|yy�`I�pEgUq|-qrJgT�R��OAa-Av�s(fysxf5)�:��`!"`"(0���s�0�Z��f`h�s<rMenE|@g-y�%yLj>0g8j%t8p:-�U)/n5( C�^���H`k,7rXI��c)mTP?7!3)�2;�blGCb,+$Lhg
��0x� �) d�J�P��J $P0��s$U�rajspo[,6}�fVt`~l�${%�Y;
�&$�@&LLj��$! � ���|��k |H�1*T(��! 	an�zEb*�B� �f{i4ex^0���u;�10 �.$�d`+."�+�E�3 P�k8d*	G"SZSkS�N�wtu�O`v`crp�ukdw��/ '5D$sOc�g�Y"{lu e�g|v�e$�~�{"-�.�[sydn�+
F� lt&*(�(�@�lz)ip�e�.kmjprg[z��$ġ&���V4VN��treo;Z#j1�d-( " <rhZ!�6�en��oo~egtJm�-i >�zMv�Ci��x4b%��iU_k�l"1��
0 f�8�$?gpS�7%P�ptph���@��o`$`!fMi7}){Kl!�@%��$Zzybb��T�9KuW,p�'
a�@Rv-� !=hb!h"l�c&a�]ky����V�~�I5m:�.=*�q" !��%"�'+j�((eV9�b�e �`% '<F!��o�-�g�c6Zv?�3:D3E	�4�"�<YBi0��0T)(��rB�6[����XKn/7:Ժ1  (, @�l"!�#,ke�sge�2'5"'E'".(�6�cX�'�q�u*��\{) -�����5�4 5��d` "&"f,
h4 � " )"$m�Ɛ&\��s�'<  �$1�N"  +** �9�(�Dcp�� )gE��$2}�i� f�g��B#$>h4$�*��`t 9*@E~Dde�j`�g�� ^Sy}!`{( �8b�of($mf��gWZ� ! !�>�+&�xuc�C)c��rr}/n��~g2J�6
'���(q� h�e!$+e�4p�bN$xm�0b�lq~k�t�5&{"8�*e#9�"4p/�*I�$H&A|$`y #U�p��
Ke)Ae�o*%ak "J84l�L��tulaa i���h�"�[��lg!�et5} Cnpwlek�40a4J'sn%i1lQa�US'n%-QI�fŦ�xUv b-Ye��)au@H.
3$�>�" b apsp M ��ti�c�)8f� �g3D ��c$} �qdE�peOc ( 0�`T8ar*�|{5�Io/�n5�T |%iWr��$i@�R����}ś! }`�;
$ �� PxMp�ro bF� �q�U!yf !(\ ��v*�#0�/w �`ceks�m�}�2*�,:EirRW)I�J�ttz�N   cf �w
F1��%clIqDVwNc�!�hb{ 0/u�G%�e"�u�:Ma%�H"�Vqmue�'N�|La`."�*�P�xl(`p� �}l\/9<>'Yr��3E%Ħ6���^[(6N��t~$':#81�	fbhcex* �r�&*��@?)Dc|vg�8`SV�H!w�&i��vPg-:��lWmJ�b4��+ `�(��Wt*e`Q�%$P�tqyg�����|`md'*$8&ngl$�r-��$:P8y b��H�4Hur  n�cD`�(Tz-�a n`g	)/.��B4 �$<ri��ǁV�~�l|h?�)$6un�!"`5��'� yR�o(%~�}�-;� 1 `bui��o�-�e�i1mu(�s8O_tEI�4�.�|\L>4�	�$\gi��pP�va���~["~c,ՠGPBo$H	N�,cr�c(s� b0�2e 
B(Mal�>�|A�)�`�<(��tymri���u�?! 6̏e,"UfFn*)l�5 &)$8!	��� \��*�'4  �le�8dAz U,:ps�]�)�Fsz��(}��-2=�� f�$��_kv+Oh5$�Rn��p}f~(e|HaI�@r�g��!jWy~!dh%�ej�k(*
,c��l6� b,(�"� "� p!�!a��RW8/a��mz
B�6 ���|J!�""�g0$*l�%/�u_4jm��`a�]4=�z�/g'+I�raa'Mګ�"X1	�h(�pOeRvh4rwm3F�b��m Ka�*g^i #@</ ���r}cac(l���y�+�H��gb+�GR(1&a"B{!@�&:[rmgrbd1f2e�%+(< �R���pXbji# uD��kmNP4tqoU�	�if%2c ) 0 ~��`x�"�)zf�\�o!T���K(x��wE�vmv{J$*$<�a>  *�|8!�)*�Ov�4TdLAiu��`iE�F����n��/2cv�;TX$C��kN9
�$1  �A�/�Te}b`.(Y��a*�38A�''d�eof*g�/	�E�")�l1m.	w%PA"%�b�od4�] bhnvv�uhfw0��/]iL,SBd�!�J : srw�gT!�e$�:�#]\kg�In�Xsidq�0J�0@ug.c�G!�NP�|lydj�%�*kGi4"gX ��uC!ŷ?���HG,Q^��p`n%3Bskm�}[afh nQ7piH&� �bn��Eo hviz�_8$0�n`d�f~��} +i~��i_^.�Ldu�vW�l��d.gxR�9aQ�tsi,����A��(nal#n@~!kl$}3�!,��$kMr}#b��N�4
1:gCj�"Lg�+"~� a& ;o	i%$��F5p�. i����V��!uq�
toeb�10 a��vd�w+i�i,k
9�j�oe�`!`1<AI��i�m�d�/7\02�s6ADwA	�6�n�tMR0�M� \aq�pI�ri����|Kbm",��Q?LPLG DH�md�6h:c�"cy�0b+h1f&4$M(�.�0M�y�;�9,�� :$Co���q�u`7��a`Kicgfx"m~P� ge*a|l%���"T��h�'41.�d!� e Ae {z5�o�/�Bs`��mEq��,{p��qv�$�o+6oOH54�Nn��tgd}/U2 HPI�0 �5��0[Uy&Cddx �k"�m,$ges��tWev�0j*(�0�[�h!`�B	p��qcdqa�nf J�'"���lL#�ge�k}T.d�es�m^d0y���:a� U$1�2�@$s")Y�stri�"4q)�h�WseLmSp&e`{eV�u��eV`(Wm�c)`uc|(DIv)�\��yp7m" r���}� �Y��gla�a~W qfQ:qr4g`�fqNaq   " 8dq�-*4=AM�F���Xl{ 6a,vE��+a`QX4~+37@�)�3wdj%()p)R.��0X�G� a.$�a�uvo���[`5x
��1$A�pahcaL( |�`( r*�pp!�)(�c"�@/^ Ygw��.eJ�C����m��/rvu�5%P$��"2 @2�75p,4B�B�.�Uyf 50r��t+�5 E�?s`G�dcklq� 	� �;*�#8 * @tpZOcY�D�#%2�Jmtgf"�%*K20��, (1dubDI�a�Hh-k3:`�O2�u2�8�h �.�3!`r�C$J�0Dutg}*�F)�C�T_H`6�e�ncU,at"cS&��3 =��,���\SifG�� = 'yHskm�*` x"4 `H$�\v�g_��VK~`nX*&�8$<�i)r�f~��qrg<;��d_K�L']�$Wb�7ݩWm&%`�5qS�~eyb������,#@ #,@`#"dd`t�aa��${]yx)b���0
umf s�# c�  84�   (2bigf�B0a�.|ey����R�8� 1h.�.'4"�5"su��5b�%cb�o(k^-�*�+1�JKe$#	1B`��m� �u� 'tn�szE1LEY�t�>�8AIuu�\�aYay��`�2iԦ��" !0Ġ6@ZLe-B �H/7�+"&� `(�8!1"4 4(� �0@�!�v�8m��l`,di���ź[�_$iw2��dgKjgob(?T�pl %i  ���"�� �'8  �lA�|fr@adsp%@��a�Bv��CIhEt��e"i�A�`v�$�B!4'D 4"�Db��'-e.g n`!D�h�e��$_[v{"`sh&�,R0�Gd,yAQ��dG	C�CH! ��HQ�LaqE�A	a��WSqMA��EdaK�7���`H �8f(�)4 -&�ua�f|$X)�ً!� U (�r�$'`"*�ig#h���W}y}�h	�tIdC;z%iqLeF�w��]S4/Ye�b9u~c(-J>5k�V��ty,((k���=� �Z��`eo�W	z	} [*02%!�&``4!d#(!i9`2m�%'o5.T�O��� 	 ?(1  ��;a[xX2:;SlE�\I�
~FASD+08L��up�j�2Y>f�J�'!T(⥠ 48 ��3 @�1:`	#%oL,d%� *apn>�}`!�Ie`�%n�Rl_Yc:��$`@�@����<��+ >0�1 X$J��ab1I*�%=<)�B�#�Y"}* o( 2��52�1 �)$ � *" a�"�A�a*5�b={efTR}cE�"�w$w�[s|hG.2�%* 1 ��%(!0@4bBe�9�If3!2`�a �u&�(�0 " �	$�10% �OF� du7g'.�%�:S�dlu`r� M� k\olu'"��! � ���
(#V��p"#//[1ou�{	  h v"``H �v� ,��~( ""b�  0�h!`�d`��/5b ;��y&$�Erg�A4e�fɰF}>dtz�)QQ�,'qf����$�la`pc/Wdd!~,OCI%�aa��$S[ke"c��L_�tKtn$@n�C c�( d5�!  (2"	(%.�� 9`�Vih���V�n� e|w�Beeuk�a5l0��e*�e&*�{ 'Zi�"�% �detGJ|Ee��o�)�e� "002�c 1HH�&�(�0TIv��5\{��(i�sh����YS=4#,��[?0 3-�l.a�h:$�!eL�rn2/?`//^N#�6�0�8�Z�5(�� s!c`�����4�$`#QƄla
qeig->qiA�pis}c,$)��#��*�'4  �d�4br `   !@�4�t�Bsv��yUp��-W}�E�df��O+4L  2�Fj��0$`7# $8 !`� `�$��$U{|#,;h"�(`�+,$$,a��dQ d�    �4�[#�*"x �(!��p8! ��*b 
�3���hH'�fa�o$,-n�&i�lWh8	���  �  !�r� !c"2�j!  ���#|y4�bY�DdCODSFG5b[RV�x��
  K`�"(!l*(+J,3)�Ԩ !# "$y���x�0���   �`4!t (03,%a�40y9;Fyh!i9 0l�$/8, 	�@��� H 8 # h�� !`P0 #4-U�7q�c> ctcvapHf�`Y�`�(&`�B� !V襠 48 ��%)M�g5c[bWKD,e}�d~(h.�p0!�`*�s$�  	a �� aH�I����u۹k3wa�? ~P%HŃ+"�mn:+� �)�P''&"'80��q8�"0�+$  �D#ai �)�E�s{Q�o *$"2:-(	�
�% 2�ap(cf2�e g2 ��,(.1d$ �!�Ht{(1$3�% 2�q"� �2j$�Am�11db��2 p a.k�)� �xr!``�$� (\m 4"%��m	%��g'���
B,}O��p`$UXsyu�c`bhsfD{phH#�Tv�vj��G">&g(j�ad?�lqt�"n��u4'dp��;N$�Lb5�
4 t�,��t*'`S�=0Q�<vkc�����,`!h#*D`3",,	l$�!��(s^{{Gb��Z�rJvtlJh�'a�bG~5�d!0xzrIhg~��E1`�&
)����B�b� a($ �$&q&�!:et��ek�&)r�/(#=�*�?1�$`ElsG}B!��%�-�i� 10*�12AR1M�6�n� CIqV��4V'{��`I�2a����L[d($8��_y

eef3�,&u�" "$� e8�0f#( d)&, �9�5�p�p�w(��zx, m����q�2 `%��``	(e.f$ hE �
*Do ~m(��� ^��`�g[lu�e �\FCd A*#,uB��(�Fgr��WM+Ey��}&o�H� 0�$�� 4@4$�P`�� $d=!P&lb[m�Pb�,��V[U9~Iyhb�-k�m(/?#��%v�rf(*�4�   �("s�(`��0{85`��r$ H�! &���( �db�m5.7-g�4 �tQ,yc���0a�)}|)�r�)'w".�peGI���ORaS�nI�GdJDAp(%`8 #�0��	 )a�vicucxoB|5-���hqme((u���9� �	��`` �`8P	<&A"1,%e�$N/`}/yh%m9p2m�?/$;I�V��� ( ( #   ��#!4XP:0s#1U�-�k" & +
( @ ��`p� �(."�*�%w�� 1h��1 �0:e
j7/NL&;�y(Br>�0` � )(�b4� " a8��,I@�]����_��*"eW�) RXA��'9e�o	, �C�)�\ w`p)8M ��S:�39�)$ � "ai �)�A�3( �f8(*Hr.R#�K�5!v�tp( &&�u/g00��&34*1dp	b�1�Hkz)2,1�as�e"�0�#a%�I*�\pq!"�3 �6@a1g.*�*�G�n
 0� �*"\my4 '"��!u��$���(68��pb$!3C!yg�([d"h n &`hZ-�T�t/��F*,&f+"�<,"�mbv�fn��i0%a\��)5.�M15�
6t�,�� .a0�1 �p38`������<cey).Da/f,($�b$��(`(28""�� 
�
uw$ z�!  �  8<�  $h2"	%.��A9a� x`)�����z� 0(?\�K=/~b�1"la��e*�gkc�k(*^ �"�%!�  a  4E ��!�%�q� "@f*�s-DQsOH�v�:�1TMs��$^!l��`I�,jت��r8#0��V:   
�,ne�'hd,� a �8&0  %, $ � �0@�)�r�9$��dilem��䬚t�% &��$$ (0"b,*i  � b ( <r9���b:��N�<G�DA�Egtd   5�9�(�B!2��	(E8��!"1��4f�,�Ot-MEvt�Ro��`wt;iJgm !`�ti�=�� _Rqtbimd"�)Tk�g.%ee"��tWM^�[!i��[IG�99j�B( ��"!t,a�� ` 
�3��� �"`�k!$ .�0!�.(8)���0a� U4 �r�!&a"(�*e#)���'wq)�g � pNEg_roudse�0��$,K!�""`Vj(,J,3(���0 "a*(`���y�b�I��aQ[�e,RIfE8\r,%M�RZL\#b{"po)gI�UUe/,|I����(H.(#";E��i#oHX8(#3,�!0�*. ` ! 0(@"��  �`�!-ov�Z�&X��O$|] ��w4D�svKc> mi}�#((j?.�|0!�("�(4�@*d a8��$`@�@����m��k'w�.  8  ��#(@ �7+  �@�)�eqb  842��48�10 �)$<� )ci!�)�D� "�-,* c"RA#	�J�.$4�a`h#~*�% "0 ��/(kqm>cCM�a�a;(0 0�%  �'&�;�3YM~$�o,�]qsua�E B�6H <i/L�Cc�C�x~m`p�,�nkT(u gX&��qm��f���\JjwT�f04%:K2}u�o	`'DrWB ` ,�t�ft��S>
jf(}�_< 4�hx6�gz��|1#eZ��)V,�L`w��CdW� ��D$"C0�y)Q�|s|d������+pe|k/l{zEimIne�re�� {whac�HL�|Btu4z�eKa�!@<<�ad4h:ch-.��9 �$0 )�愉�z�a=|+�>/]`�1"Eu��e*�#+b�  0�*� 0�pdthIu2��d�m�y�!#t(�s8ES2O	�V��=OL4��fTCy��TK�6i����TO"ic4��U? H
L$H �nus�gce�"eq�zachueon.*� �0@�)�0�8(��d2o"s���z�$M7��d`Ccefo*
e  �   $"40m�Ɛ&V��s�?>$y�La�t Ra " -R��(�#:��|Ez��)~p��(0�t��O+0oOP=$�Fn��`Te9tJl   �b �,��$kS2|3duhb�(Uc�i-Eik;��d8� b.`�0� [&�fssU�R9i��s!oN`��{w N�g6��(Iq�X`b�k5.7/g� i�vL,{e��xn�dWt%�
�%3 *�huc+	���"~}}�fM� doZdEqjensH B�p��OS$mO!�r#5o*(- 3(���ypea is���:�"� ��bB �'t Abpq<&a�"0k4o/Sz!-0m6m�U,c<|SE�W���
 "   $��ic#\:)a#-M�{q�j~$jP)$plB.��  �`�*?'�Z�$9NE���_,5sU��g%E�;dAs4mR0|�!*`&h�pxb�a*�u:�j\Ict��4h@�V��Ȩn��kvmg�, Pm
��;(@ �=.  � �m�Tgsta+(2���u:�pE�34d
�l!cme�+��0*�*8(*	v&TWk�K�ftu�Nepmvw"�wzG1��? 80  #}�9�  2!1:0�gP*�p"�:�
k$�J*�\apej�i4B�>D 0&."� )�XQ�lz8`p�$B�"iTMh4 gX6��c,%Ǯ6���A(6��pr 13]1c}�maehdf@` !�u�fl�G_ bfhj�(fe�ha$�gr��u0f,1��lUGb�Ef0��vEv�f�`:$p�4!Q� rh ��� ��, ` ". 3jl-	$ �b$��mYwi"i��RN�wJuw fNk�cM`�mR8?�s n|3ci&d�C}j�0 )����D�`� t(&�(&4"�   u��%*�'""�a*n_~�z�!q�qEes~q��m�-�e�a1Xt*�f6E2IK�6�&�]eh�,� (zʠ �2*����|_rn"2��^7
   "%
�,=e�wJ�"gq�#t3No%ov,"�e�p	�(�{�=*��l~efc����v�_5a��  K
"( ( 5e �bb(mc#!Ά�b^��f�/`l#�l9�hg` e 301�)�!�Etw��#iM|��9wr�� r�&��O#6+MD4"�Pv��`} & &<!`�Z�$��4JT{zg`uj.�iTm�_,l5ysY��dWO|� J  �0�Y �: zT�I	!��R[y]d�s,pr�7��iKe�N(�i5 Wof�4i�n[lx|��kb�x]<�~�#/j*;�zTn'	���#|1;�j	�@tO)Mq,' `!�`�� KU�q=g\mQ.
$1!���`p'  ,t���=�b�Q��gd
�'8R	i$ (rR $`�?KN+``'{j#K9 2a�$m4,H�D���tz 5 {Q��* i\U0$x6%v�]�.n "  "h(@��`P�a�)*v�J�v3���CF9lg��rdE�s{mn0aL( ~�`(h:*�lpa�j*�{4�FfIGS��ypE�N����}��)0m�;/(*��urij�?4`TB�B�.�Tkn ?ix�϶y2�38�<u$�'/ck �)�B�3(�*8<.ii.C+�J�$$&�@ad`Gd"�pz'1:��/088h"cWi�j�Tz;ez7u�"0 �U&�0�!)Ok�c �23ap�	iC�2Le}gnI�E{�A�|~,b �$ �.a ad"Gh3��!=��t���JAlyO��us#%zY"xu�~d$H"v .``@(�b�fk��W?> '"x�M !�lh'�ck��pb)c�� .�h 0�04�:��Byng0�1%Q�pwyg���N��)ceikSD$;jhid!�b%�� My'b��F�0@ewb z�fa�!Zzl�p x9`
i%b� %#�$~ey����F�|�igyu[�/,'uf�pd'|��!�"!0�a(!5�n�x#�vd&qpDi��|�m�-�,gHg8�b:EIwMOI�v�,�DGIsu�N�dMcy��sQ�6a����Q]}("2��U?
  $ �$f �'h.6�"a9�8g3*tm+"hi�.�2\�*�r�l(��loigi����v�[edgSՎelC*sufm ew � |-- 4(-���fV��
�'4  � !�$%poux#(5:�=�(�Fcr��lO~��,r}�E�`v�.�Kbr-ONt%�Un��ume~:2| sa�ta�a��mX^twc`|hf�#j�c "�� ~�2h&!�>�K$�hac�C-h��nem`�~f b�3�mN"�gn�oum.d�4r�kV.p}��*a�,Y.i�u�mgw#9�`5a)� p8)�zu�&$,+'sv lka#t�n�$,Oq�n9m|c*3Oqt;�^��Iq'd*)u���x�`�E��fpb�k^R|&@rPsl'u�4{noglsb!*)d2%� +'5<DI�D���%I`r,a`-Peފ/1~T
:z'slE�w�kcUan`+$^s%Dn��ty�b�9[$g�N�!vko��Na.i��?&�"3dA`$a($?�iV8'r<�0aa�hj�c6�VH/^A+k��.hm�F����|ӹ/bwe�5dSd
��t9Mb�=;n#TB�I�w�L&|ka/=U6��@0�08�)$�dcaot�#�W�#kP�lxz#Ak.V].�O�'d0�Ngt`gb2�%*f2!��.Wau9e|uN�1�Pb+iw~q�NN%�ud�,�0Zm'�J*�H1q!"� 4@�6Luqm'"�(�P�|ki`v�-F�.jTOiu gX"��?E5Ŀ^.���^K`fL��af,12":1�    ( " 4 fXn�D7�en��W^p,(fQfj�/-F~�jmv�f~��|:'le��(Hn�Hz>��rU�}��Fd>%0�(0�tth �����`qz#.h3kQxmIna�a-��m{T}y'r��HE�$(ww  *�# !�(@o|�q'$|;`iq~��C1u�.ngy����V�v�au}.� %yb� 0`0��eo�'"~�2*cR1�k�/0�d-oc}V%�n�-�m� 1 2(�p8A2 HK�6�/�<H?&�T�F!x��j�2d����XWgpo3��-   $%@ �lie�#,:$�cdq�+c!,%o./?d�>�p]�e�{�=
��l1$b �����5�a'��%eCplmc(~mo�%bp'$<3m���g��b�)4$ �lA�t'r % ?:%R�}�(�Gat��mG|��-2t��`f���D!0'Fh4$�.��`u =>@'`ram�Pb�a��5KW}wg`q|"�Q*�,,!a�� R2�bo')�>�s`�his�Cmf��bpm\m�~faJ�= ���p �"*�/ut/d�6)�k\0|}��8a�(_t!�z�!$3 "�`$c ���&d =�`�tKbUsk/fP!F�4��$,K!�")e5#))N0==���xlication/postscript',
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
