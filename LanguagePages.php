<?php
/**
 * MediaWiki LaguagePages Extension
 * {{php}}{{Category:Extensions|LaguagePages}}
 * @package MediaWiki
 * @subpackage Extensions
 * @author Daniel Yount  icarusfactor factorf2@yahoo.com
 * @licence GNU General Public Licence 3.0 or later
 */
 
define('LANGUAGEPAGES_VERSION','0.6, 2012-12-1');
 
$wgExtensionFunctions[] = 'wfSetupLanguagePages';
$wgHooks['LanguageGetMagic'][] = 'wfLanguagePagesLanguageGetMagic';
 
$wgExtensionCredits['parserhook'][] = array(
        'name'        => 'Language Pages',
        'author'      => 'Daniel Yount @icarusfactor',
        'description' => 'A way to setup multiple Languange Pages without templates',
        'url'         => 'N/A',
        'version'     => LANGUAGEPAGES_VERSION
);
 
function wfLanguagePagesLanguageGetMagic(&$magicWords,$langCode = 0) {
        $magicWords['langpages'] = array(0,'langpages');
        return true;
}
 
function wfSetupLanguagePages() {
        global $wgParser;
        $wgParser->setFunctionHook('langpages','wfRenderLanguagePages');
        return true;
}
 
# Renders a table of all the individual month tables
function wfRenderLanguagePages(&$parser) {
        global $wgServer,$wgUsePathInfo,$wgTitle,$wgOut,$wgArticlePath; 
        $parser->mOutput->mCacheTime = -1;
        $argv = array();
        foreach (func_get_args() as $arg) if (!is_object($arg)) {
                if (preg_match('/^(.+?)\\s*=\\s*(.+)$/',$arg,$match)) $argv[$match[1]]=$match[2];
        }
        //if (isset($argv['format']))    $f = $argv['format']; else $f = '%e %B %Y';
        $basescript = str_replace("\$1", "",  $wgArticlePath );
        $context = new RequestContext();
        $sitetitle = str_replace(" ", "_", $context->getTitle() );
        return wfRenderLanguagePagesPanel( $wgServer , $basescript  , $sitetitle );
}
 
# Return a Language Panel
function wfRenderLanguagePagesPanel( $baseurl , $basescript , $sitetitle ) {
        global $wgUsePathInfo;
	$output="";

	$langpages = wfActiveLanguagePages( $basescript , $sitetitle );
        //will need base page URL varaible for the policy link 
	$output .= '<div>';
	$output .= '<div style="white-space: nowrap; padding: 4px 1em; border: 2px dotted #88ACCC;background: #88ACCC;float:left;" >['.$baseurl.$basescript.'Userspace:Language_policy <b>Language</b>]&nbsp;</div>';
	$output .= '<div style="background: #88ACCC;color: white;padding: 4px 1em;border: 2px dotted #88ACCC;float:left;" >'.$langpages.'</div>';
	$output .= '</div>';

	return $output;
}

// Needs to know base page to check all coresponding languange pages.
function wfActiveLanguagePages( $basescript , $sitetitle ) {
  global $wgServer,$wgUsePathInfo,$wgArticlePath,$wgActionPaths; 
  

  $output="";
  $default=""; //empty for English
  $defaultname="English";
  $langpg ="";
  $langname ="";


  $langpagearray = array("en","de","hi","pt","es","jp","it","fr");

  $langnamearray = array(
     "en" => "English",
     "de" => "Deutsch",
     "hi" => "हिन्दी",
     "pt" => "Português",
     "es" => "Español ",
     "jp" => "日本語",
     "it" => "Italiano",
     "fr" => "Français"
  );



//for loop of array languages 
foreach($langpagearray as $langpg ) {
    //Pull language name from page name array.
    $langname = $langnamearray[ $langpg ];

  if( $langpg != $default )
     {
       //Will have to remove current page title. 
       $langpg = "/".$langpg;
       $titleObject = Title::newFromText( $sitetitle.$langpg  );
     }
  else
     {
       //will need to strip off any language page name on link.
       //Will have to grabe current page title. 
       $sitetitle = str_replace("/de", "", $sitetitle );
       $sitetitle = str_replace("/es", "", $sitetitle );
       $sitetitle = str_replace("/it", "", $sitetitle );
       $sitetitle = str_replace("/pt", "", $sitetitle );
       $sitetitle = str_replace("/hi", "", $sitetitle );
       $sitetitle = str_replace("/fr", "", $sitetitle );
       $sitetitle = str_replace("/jp", "", $sitetitle );
       $sitetitle = str_replace("/en", "", $sitetitle );

       $titleObject = Title::newFromText( $sitetitle );
       $langname=$defaultname;
       $langpg="";


     }   

  //find if languange page exist , if so add it to the HTML. 
  //Will need variable of current page and base URL.
  if( $titleObject->exists() ) $output .='&nbsp;&bull;&nbsp;['.$wgServer.$basescript.$sitetitle.$langpg.' '.$langname.']&nbsp;';
}
//end for loop

  return $output;
}
