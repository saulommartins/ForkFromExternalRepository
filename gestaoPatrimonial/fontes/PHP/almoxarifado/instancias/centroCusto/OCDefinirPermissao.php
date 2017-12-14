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
    * Página de Oculto de Definição de Permissões
    * Data de Criação   : 22/11/2005

    * @author Analista      : Diego
    * @author Desenvolvedor : Rodrigo Schreiner

    * $Id:

    * Casos de uso: uc-03.03.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoPermissaoCentroDeCustos.class.php";
include_once CAM_GA_ADM_NEGOCIO."RUsuario.class.php";
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';

//Define o nome dos arquivos PHP

$stPrograma = "DefinirPermissao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];
$stCGM  = $request->get('codCGM');

$obRegra = new RAlmoxarifadoPermissaoCentroDeCustos();
$obRegraUsuario = new RUsuario;

function montaListaCentroCusto($rsCentroCustoPermissao)
{

    $table = new Table();

    $table->setRecordset( $rsCentroCustoPermissao );

    $table->setSummary('Centros de custos');
    //$table->setConditional( true , "#ddd" );

    $table->Head->addCabecalho( 'Código'           , 10 );
    $table->Head->addCabecalho( 'Descrição'        , 72 );
    $table->Head->addCabecalho( 'Data de Vigência' , 15 );

    $obChkTodosN = new Checkbox;
    $obChkTodosN->setName                        ( "boTodos" );
    $obChkTodosN->setId                          ( "boTodos" );
    $obChkTodosN->setRotulo                      ( "Marcar todas" );
    $obChkTodosN->obEvento->setOnChange          ( "selecionarTodos('n');" );
    $obChkTodosN->montaHTML();

    $table->Head->addCabecalho( $obChkTodosN->getHTML() , 3  );

    $table->Body->addCampo( 'cod_centro'  , 'E' );
    $table->Body->addCampo( 'descricao'   , 'E' );
    $table->Body->addCampo( 'dt_vigencia' , 'C' );

    $obChkPermissao = new CheckBox;
    $obChkPermissao->setName('boPermissao_[cod_centro]');
    $obChkPermissao->setId('boPermissao_[cod_centro]');

    $table->Body->addComponente($obChkPermissao, 'ok');

    $table->montaHTML();
    $stHTML = $table->getHtml();
    $stHTML = str_replace("\n","",$stHTML);
    $stHTML = str_replace("  ","",$stHTML);
    $stHTML = str_replace("'","\\'",$stHTML);

    $stJs = "jq('#spnListaCentroCusto').html('". $stHTML ."'); ";

    $rsCentroCustoPermissao->setPrimeiroElemento();

    // Percorre o Record Set para marcar/desmarcar os checkbox dos centros de custos.
    while (!$rsCentroCustoPermissao->eof()) {
      if ( $rsCentroCustoPermissao->getCampo('marcado') ) {
        if ( $rsCentroCustoPermissao->getCampo('responsavel') == 't' ) {
          $stJs .= "$('boPermissao_".$rsCentroCustoPermissao->getCampo('cod_centro')."').checked = true;";
          $stJs .= "$('boPermissao_".$rsCentroCustoPermissao->getCampo('cod_centro')."').disabled = true;";
        } else {
          $stJs .= "$('boPermissao_".$rsCentroCustoPermissao->getCampo('cod_centro')."').checked = true;";
        }
      } else
          $stJs .= "$('boPermissao_".$rsCentroCustoPermissao->getCampo('cod_centro')."').checked = false;";

      $rsCentroCustoPermissao->proximo();
    }

    return $stJs;
}

$js = '';
switch ($stCtrl) {
case 'buscaUsuario':
    if ($_GET["inNumCGM"] != "") {
        $obRegraUsuario->obRCGM->setNumCGM( $_GET["inNumCGM"] );
        $obRegraUsuario->consultarUsuario( $rsCGM );

        $stNomCGM = $rsCGM->getCampo( "nom_cgm" );

        if (trim($stNomCGM)=='') {
            $js .= "jq('#inNumCGM').val('');";
            $js .= 'jq("#stNomCGM").html("&nbsp;");';
            $js .= 'jq("#spnListaCentroCusto").html("");';
            $js .= "alertaAviso('@CGM (".$_GET["inNumCGM"].") não encontrado','form','erro','".Sessao::getId()."');";
            $js .= "jq('#inNumCGM').focus(); ";
        } else {
            $js .= 'jq("#stNomCGM").html("'.$stNomCGM.'");';
            $js .= "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&codCGM=".$rsCGM->getCampo( "numcgm" )."','carregaCentroDeCusto'); ";
        }
    } else {
            $js .= 'jq("#stNomCGM").html("&nbsp;"); ';
            $js .= 'jq("#spnListaCentroCusto").html("");';
            $js .= "jQuery('#Ok').attr('disabled', 'disabled');";
    }
    break;

case "carregaCentroDeCusto" :
    if (trim($_GET['codCGM']) != '') {
        $inCodCGM = $_GET['codCGM'];
        $obRegra->obRCGMPessoaFisica->setNumCGM( $inCodCGM );
        $stOrder  = " ORDER BY cc.descricao" ;
        $obRegra->listarCentroCustoPermissao($rsCentroCustoPermissao, $stOrder);
        $js .= montaListaCentroCusto( $rsCentroCustoPermissao );
        $js .= "jQuery('#Ok').removeProp('disabled');";
    }
    break;
}

echo $js;
?>
