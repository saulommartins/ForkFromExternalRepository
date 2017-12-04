<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
* Arquivo de instância para popup de normas
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 4659 $
$Name$
$Author: lizandro $
$Date: 2006-01-04 15:16:06 -0200 (Qua, 04 Jan 2006) $

Casos de uso: uc-01.04.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GA_NORMAS_NEGOCIO . "RNorma.class.php"    );

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

$obRegra = new RNorma;
$rsAtributos = new RecordSet;

function montaLinkName()
{
    $obTxtLink = new TextBox;
    $obTxtLink->setRotulo    ( "Link" );
    $obTxtLink->setTitle     ( "Link para norma" );
    $obTxtLink->setMaxLength ( 80 );
    $obTxtLink->setSize      ( 80 );
    $obTxtLink->setName      ( "stLink" );
    $obTxtLink->setValue     ( $stLink );

    $obFormulario = new Formulario;
    $obFormulario->addComponente ($obTxtLink);
    $obFormulario->montaInnerHTML ();
    $stHTML = $obFormulario->getHTML ();

    return $stHTML;
}

function montaUploadDoc()
{
    $obFileLink = new FileBox;
    $obFileLink->setRotulo    ( "Link" );
    $obFileLink->setTitle     ( "Informe a localização do arquivo da norma" );
    $obFileLink->setSize      ( 80 );
    $obFileLink->setName      ( "stLink" );

    $obFormulario = new Formulario;
    $obFormulario->addComponente ($obFileLink);
    $obFormulario->montaInnerHTML ();
    $stHTML = $obFormulario->getHTML ();

    return $stHTML;
}

// FUNCAO QUE MOSTRA O LINK INCLUÍDO OU LINK QUE ESTA NA BASE DE DADOS
function montaListaLink(&$js)
{
    $stLink = Sessao::read('stNormaLink');

    $obLblLink = new Label;
    $obLblLink->setRotulo  ( "Link atual" );
    $obLblLink->setName    ( "stLink"     );
    $obLblLink->setValue   ( $stLink      );

    $obFormulario = new Formulario;
    $obFormulario->addComponente ($obLblLink);
    $obFormulario->montaInnerHTML ();
    $stHTML = $obFormulario->getHTML ();

    $stCaminhoPadrao = substr ($_SERVER["SCRIPT_FILENAME"], 0, strrpos ($_SERVER["SCRIPT_FILENAME"], "/"))."/../../../anexos/normas/";
    if (is_file ($stCaminhoPadrao.$stLink)) {
        $stHTML2 = montaUploadDoc();
        $js .= "f.boFile[0].checked = true;";
        $js .= "d.getElementById('spanLink').innerHTML = '".$stHTML2."';\n";
    } else {
        $stHTML2 = montaLinkName ();
        $js .= "d.getElementById('spanLink').innerHTML = '".$stHTML2."';\n";
    }

    return $stHTML;
}

// Acoes por pagina
switch ($stCtrl) {
    //Retorna o valor da busca
    case 'buscaPopup':
        if ($_POST['inCodNorma']) {
            $obRegra->setCodNorma( $_POST['inCodNorma'] );
            $obRegra->setExercicio( Sessao::getExercicio() );
            $obErro = $obRegra->consultar( $rsRecordSet );
            if ( !$obErro->ocorreu() ) {
                if ( $obRegra->getNomeNorma() != NULL ) {
                    $stNorma  = $obRegra->obRTipoNorma->getNomeTipoNorma().' '.$obRegra->getNumNorma();
                    $stNorma .= '/'.$obRegra->getExercicio().' - '.$obRegra->getNomeNorma();
                }
            }
        }
        SistemaLegado::executaFrameOculto("retornaValorBscInner( '".$_GET['stNomCampoCod']."', '".$_GET['stIdCampoDesc']."', '".$_GET['stNomForm']."', '".$stNorma."')");
    break;
// Habilita Link de URL na inclusão
    case "linkName":
        $stHTML = montaLinkName();
        $js .= "d.getElementById('spanLink').innerHTML = '".$stHTML."';\n";
    break;

    // Habilita upload de arquivo na inclusao
    case "uploadDoc":
        $stHTML = montaUploadDoc();
        $js .= "d.getElementById('spanLink').innerHTML = '".$stHTML."';\n";
    break;

    // Habilita lista de Link para stAcao != incluir
    case "listaLink":
        $stHTML = montaListaLink ($js);
        $js .= "d.getElementById('spanLista').innerHTML = '".$stHTML."';\n";
    break;
    case "incluirLink":
        if ($boFile == "S") {

//-O codigo abaixo foi colocado para inserir uma figura no diretorio anexos com o código da norma.
            $tmp = explode("=", Sessao::getId());
            $dirSession = $tmp[1];
            $stCaminhoPadrao = CAM_NORMAS."tmp/".$dirSession."/";
            $stCaminhoAnexos = CAM_NORMAS."anexos/";
            if (is_dir($stCaminhoPadrao)) {

                if ($handle = opendir($stCaminhoPadrao)) {
                   while (false !== ($file = readdir($handle))) {
                       if ($file != "." && $file != "..") {
                           echo "$file\n";
                           unlink($stCaminhoPadrao."/".$file);
                           rmdir($stCaminhoPadrao);
                       }
                   }
                   closedir($handle);
                }
            } else {
                   if (Sessao::read('inCodNorma') != '') {
                       //alterar
                      if ($handle = opendir($stCaminhoAnexos)) {
                         while (false !== ($file = readdir($handle))) {
                             if ($file != "." && $file != "..") {
                                 $tmp = explode("_", $file);
                                 if ($tmp[0] == Sessao::read('inCodNorma')) {
                                    unlink($stCaminhoAnexos.$file);
                                 }
                             }
                         }
                         closedir($handle);
                      }
                   }
            }
            mkdir($stCaminhoPadrao, 0777);
//-----------------------
            if (!move_uploaded_file($stLink, $stCaminhoPadrao.$stLink_name)) {
                SistemaLegado::exibeAviso("Erro no upload de arquivo!","n_incluir","erro");
                exit (0);
            }
            $stLink = $stLink_name;
        }
        Sessao::write('stNormaLink',$stLink);

        $stHTML = montaListaLink ($js);
        $js .= "parent.window.opener.document.getElementById('spnlink').innerHTML='".$stLink."';\n";
        $js .= "d.getElementById('spanLista').innerHTML = '".$stHTML."';\n";
    break;
}
if ($js)
    SistemaLegado::executaiFrameOculto($js);
