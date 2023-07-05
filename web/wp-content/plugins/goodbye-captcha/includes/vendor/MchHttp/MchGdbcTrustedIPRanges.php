<?php
/*
 * Copyright (C) 2015 Mihai Chelaru
 */
final class MchGdbcTrustedIPRanges
{
	public static function isIPInCloudFlareRanges($ipAddress, $ipVersion)
	{
	
		return ( $ipVersion === MchGdbcIPUtils::IP_VERSION_4 )
				?
				self::isIPInRanges($ipAddress, $ipVersion, array(1729491968=>1729492991,1729546240=>1729547263,1730085888=>1730086911,1745879040=>1746927615,1822605312=>1822621695,2197833728=>2197834751,2372222976=>2372239359,2728263680=>2728394751,2889875456=>2890399743,2918526976=>2918531071,3161612288=>3161616383,3193827328=>3193831423,3320508416=>3320509439,3324608512=>3324641279,))
				:
				self::isIPInRanges($ipAddress, $ipVersion, array('2400:cb00::/32'=>1,'2405:8100::/32'=>1,'2405:b500::/32'=>1,'2606:4700::/32'=>1,'2803:f800::/32'=>1,'2a06:98c0::/29'=>1,'2c0f:f248::/32'=>1,));
	
	}
	
	public static function isIPInRackSpaceRanges($ipAddress, $ipVersion)
	{
	
		return ( $ipVersion === MchGdbcIPUtils::IP_VERSION_4 )
				?
				self::isIPInRanges($ipAddress, $ipVersion, array(179828736=>179828991,179829248=>179830271,180076032=>180076543,180092416=>180092671,180220928=>180221951,180222976=>180223231,180223488=>180223999,180289024=>180289535,))
				:
				self::isIPInRanges($ipAddress, $ipVersion, array());
	
	}
	
	public static function isIPInIncapsulaRanges($ipAddress, $ipVersion)
	{
	
		return ( $ipVersion === MchGdbcIPUtils::IP_VERSION_4 )
				?
				self::isIPInRanges($ipAddress, $ipVersion, array(758906880=>758972415,759185408=>759186431,769589248=>769654783,1729951744=>1729952767,1805254656=>1805320191,2508081152=>2508083199,3104537600=>3104538623,3236315136=>3236331519,3331268608=>3331276799,3344138240=>3344140287,))
				:
				self::isIPInRanges($ipAddress, $ipVersion, array('2a02:e980::/29'=>1,));
	
	}
	
	public static function isIPInAmazonCloudFrontRanges($ipAddress, $ipVersion)
	{
	
		return ( $ipVersion === MchGdbcIPUtils::IP_VERSION_4 )
				?
				self::isIPInRanges($ipAddress, $ipVersion, array(50991488=>50991615,51066112=>51066367,52658816=>52658943,58744064=>58744319,59168512=>59168767,65470976=>65471103,65726688=>65726719,65810432=>65810943,65841600=>65841663,220200960=>220332031,220397568=>220463103,221257728=>221257983,221659008=>221659071,222034432=>222034495,225559616=>225559679,225561344=>225561599,226281216=>226281471,231883648=>231883711,232783872=>233046015,233063680=>233063935,233419200=>233419263,234422272=>234487807,264026112=>264026367,265227648=>265227775,265278848=>265278975,314609152=>314609663,315151360=>315151871,316189312=>316189439,317054144=>317054207,583269376=>583269631,584594176=>584594303,585043168=>585043199,585060544=>585060607,585240064=>585240319,597592064=>597592319,597835712=>597835775,598196096=>598196159,610789376=>610789567,753119744=>753119999,753556220=>753556223,753560704=>753560831,873430912=>873430975,875429888=>875446271,875531008=>875531263,875872128=>875872191,876117760=>876117887,876215808=>876216063,876790400=>876790463,877590400=>877590463,877821952=>877830143,877920256=>878051327,880574464=>880607231,885489600=>885489663,886372352=>886372415,886882048=>886882111,886996992=>887029759,917897216=>917962751,918552576=>918618111,921042944=>921092095,921094144=>921108479,921304960=>921305023,921665536=>921690111,921731072=>921747455,989760000=>989760191,1090273280=>1090306047,1091043328=>1091158015,1183055872=>1183072255,1201143808=>1201176575,1666164992=>1666165247,1666449408=>1666514943,1666580480=>1666646015,1865630208=>1865630463,1954669056=>1954669247,1992384832=>1992385023,2006169088=>2006169279,2016676928=>2016676991,2016679520=>2016679551,2016683904=>2016683935,2016713152=>2016713215,2028530688=>2028530879,2029908160=>2029908223,2029908384=>2029908415,2029909376=>2029909471,2192572416=>2192637951,2412511232=>2412576767,2430337024=>2430402559,3030595840=>3030596031,3438715904=>3438723071,3455830016=>3455836159,3455842560=>3455844095,3526567936=>3526568191,3632865280=>3632873471,3745975040=>3745975071,3745990496=>3745990655,))
				:
				self::isIPInRanges($ipAddress, $ipVersion, array());
	
	}
	
	public static function isIPInAmazonEC2Ranges($ipAddress, $ipVersion)
	{
	
		return ( $ipVersion === MchGdbcIPUtils::IP_VERSION_4 )
				?
				self::isIPInRanges($ipAddress, $ipVersion, array(50331648=>50462719,50659328=>50678271,50678784=>50681855,50692096=>50702847,50712576=>50714111,50714624=>52166655,52428800=>52494335,52559872=>52690943,55574528=>56885247,56950784=>57016319,57147392=>57409535,57671680=>57933823,58195968=>59768831,63963136=>66060287,66584576=>67108863,221249536=>222035967,225443840=>225705983,226230272=>226492415,231735296=>232128511,233046016=>233832447,234094592=>234225663,234487808=>234618879,261619712=>261685247,261881856=>262012927,262144000=>262275071,262406144=>262537215,263258112=>263281663,263716864=>263847935,263979008=>264044543,264306688=>264308735,264765440=>264830975,265158656=>265289727,266207232=>266338303,266600448=>266731519,267124736=>267255807,268238848=>268369919,310509568=>310575103,310640640=>310968319,311033856=>311558143,312016896=>312082431,312213504=>312475647,312606720=>312737791,312868864=>313262079,313458688=>314179583,314310656=>317587455,318111744=>318177279,318504960=>318636031,387186688=>387448831,583008256=>587202559,591921152=>593494015,597164032=>599261183,750780416=>754974719,775127040=>775147519,775149568=>775159807,780730368=>780795903,839909376=>840171519,846200832=>846266367,872415232=>875429887,875475968=>875478015,875495424=>877821951,877831168=>877832463,877834240=>877836799,877854720=>877920255,878051328=>878444543,878605312=>878606335,878627072=>878627135,878639104=>878639343,878639392=>878639567,878698496=>878700287,878701312=>878701567,878702336=>878706591,880266496=>880266751,884998144=>886571007,886833152=>886996991,910163968=>912261119,915406848=>917897215,917962752=>918552575,918618112=>920518655,920526848=>920528895,920531968=>920532991,920533536=>920533551,920533760=>920534015,920535040=>920535551,920551424=>921042943,921174016=>921632767,921702656=>921702911,921763840=>922746879,1059061760=>1059323903,1073115136=>1073115391,1090273280=>1090279935,1090281984=>1090287103,1090519040=>1090781183,1137311744=>1137328127,1146028032=>1146044415,1172750336=>1172766719,1172799488=>1172815871,1173012480=>1173028863,1173061632=>1173078015,1189134336=>1189150719,1210851328=>1210859519,1264943104=>1264975871,1333592064=>1333624831,1618935808=>1618968575,1666023424=>1666028031,1666028288=>1666032127,1666038272=>1666038783,1666039552=>1666039807,1666053888=>1666054143,1666054656=>1666054911,1666055424=>1666055935,1666121728=>1666318335,1670774784=>1670778879,1679032320=>1679818751,1728317440=>1728319487,1796472832=>1796734975,1820327936=>1820983295,1823422464=>1823424511,2063122432=>2063138815,2360541184=>2360606719,2645491712=>2645557247,2684420096=>2684485631,2713518080=>2713583615,2731927552=>2731928575,2734353408=>2734354431,2927689728=>2927755263,2938732544=>2938765311,2954903552=>2954911743,2955018240=>2955083775,2974253056=>2974285823,3091726336=>3091857407,3098116096=>3098148863,3106961408=>3106962431,3438051328=>3438084095,3495319552=>3495320575,3635863552=>3635867647,))
				:
				self::isIPInRanges($ipAddress, $ipVersion, array());
	
	}
	
	public static function isIPInAutomatticRanges($ipAddress, $ipVersion)
	{
	
		return ( $ipVersion === MchGdbcIPUtils::IP_VERSION_4 )
				?
				self::isIPInRanges($ipAddress, $ipVersion, array(1076022784=>1076023039,1117481344=>1117481407,1163590912=>1163591167,1279981696=>1279981823,1279983360=>1279983487,1476042752=>1476050943,3221241856=>3221258239,3333780480=>3333781503,))
				:
				self::isIPInRanges($ipAddress, $ipVersion, array('2001:1978:1e00:3::/64'=>1,'2620:115:c000::/40'=>1,));
	
	}
	
	public static function isIPInSucuriCloudProxyRanges($ipAddress, $ipVersion)
	{
	
		return ( $ipVersion === MchGdbcIPUtils::IP_VERSION_4 )
				?
				self::isIPInRanges($ipAddress, $ipVersion, array(1123600384=>1123601407,3109938176=>3109939199,3227026944=>3227027455,3229415680=>3229415935,))
				:
				self::isIPInRanges($ipAddress, $ipVersion, array('2a02:fe80::/29'=>1,));
	
	}
	
	public static function isIPInTrustedRanges($ipAddress, $ipVersion)
	{
		return self::isIPInCloudFlareRanges($ipAddress, $ipVersion) || self::isIPInRackSpaceRanges($ipAddress, $ipVersion) || self::isIPInIncapsulaRanges($ipAddress, $ipVersion) || self::isIPInAmazonCloudFrontRanges($ipAddress, $ipVersion) || self::isIPInAmazonEC2Ranges($ipAddress, $ipVersion) || self::isIPInAutomatticRanges($ipAddress, $ipVersion) || self::isIPInSucuriCloudProxyRanges($ipAddress, $ipVersion) ;
	}
	
	private static function isIPInRanges($ipAddress, $ipVersion, $arrIPs)
	{
		if(	$ipVersion === MchGdbcIPUtils::IP_VERSION_4 )
		{
			$ipNumber = (float)MchGdbcIPUtils::ipAddressToNumber($ipAddress, $ipVersion, false);

			if( isset($arrIPs[$ipNumber]) )
				return true;

			foreach($arrIPs as $minIpNumber => $maxIpNumber)
			{
				$minIpNumber < 0 ? $minIpNumber += 4294967296 : null; 
				if( $ipNumber < $minIpNumber ) // the array is already sorted by key
					return false;

				if( ($minIpNumber <= $ipNumber) && ($ipNumber <= $maxIpNumber) )
					return true;
			}

			return false;
		}

		foreach($arrIPs as $cidrBlock => $maxIpNumber)
		{
			if( ! MchGdbcIPUtils::isIpInCIDRRange($ipAddress, $cidrBlock, MchGdbcIPUtils::IP_VERSION_6, true) )
				continue;

			return true;
		}

		return false;
	}
	
	
}