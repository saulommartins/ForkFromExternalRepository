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
  * Pagina Oculta para Calculo
  * Data de criacao : 02/06/2005

    * @author Analista: Fabio Bertoldi
    * @author Programador: Diego Bueno Coelho

    * $Id: OCManterMoeda.php 63839 2015-10-22 18:08:07Z franver $

    Caso de uso: uc-05.05.06
**/

/*
$Log$
Revision 1.6  2007/08/06 20:08:46  cercato
Bug#9818#

Revision 1.5  2006/10/24 12:33:55  fabio
adicionado CASE buscaMoeda

Revision 1.4  2006/09/15 14:58:03  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_ADM_NEGOCIO."RFuncao.class.php"           );
include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php"         );
include_once ( CAM_GT_ARR_NEGOCIO."RARRPermissao.class.php"     );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMNivel.class.php"         );
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php"              );

$stCtrl = $_REQUEST['stCtrl'];

$stJs = "";

//Define o nome dos arquivos PHP
$stPrograma      = "ManterMoeda";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgFormGrupo     = "FM".$stPrograma.".php";
$pgFormCredito   = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";

$obRARRGrupo = new RARRGrupo ;
$obRFuncao   = new RFuncao   ;

/*
        FIM DAS FUNÇÕES
*/

switch ($_REQUEST ["stCtrl"]) {

    case "buscaFuncao":
        $arCodFuncao = explode('.',$_REQUEST["inCodFuncao"]);
        $obRFuncao->setCodFuncao                           ( $arCodFuncao[2] );
        $obRFuncao->obRBiblioteca->setCodigoBiblioteca     ( $arCodFuncao[1] );
        $obRFuncao->obRBiblioteca->roRModulo->setCodModulo ( $arCodFuncao[0] );
        $obRFuncao->consultar();

        $inCodFuncao    = $obRFuncao->getCodFuncao () ;
        $stDescricao    = "&nbsp;";
        $stDescricao    = $obRFuncao->getComentario() ;
        $stNomeFuncao   = $obRFuncao->getNomeFuncao();

        if ( !empty($inCodFuncao) ) {
            $stJs .= "d.getElementById('stFormula').innerHTML = '".$inCodFuncao." - ".$stNomeFuncao."';\n";
        } else {
            $stJs .= "f.inCodFuncao.value ='';\n";
            $stJs .= "f.inCodFuncao.focus();\n";
            $stJs .= "d.getElementById('stFormula').innerHTML = '&nbsp;';\n";
            $stJs .= "alertaAviso('@Função informada não existe. (".$_REQUEST["inCodFuncao"].")','form','erro','".Sessao::getId()."');";
        }
    break;
    case "buscaMoeda";
        if ($_REQUEST['inCodMoeda']) {
            include_once ( CAM_GT_MON_MAPEAMENTO."TMONMoeda.class.php" );
            $obTMONMoeda = new TMONMoeda;
            $stFiltro = " WHERE cod_moeda = ". $_REQUEST['inCodMoeda']." \n";
            $obTMONMoeda->recuperaTodos( $rsMoeda, $stFiltro, " ORDER BY cod_moeda " );

            $stDescricao = $rsMoeda->getCampo('descricao_singular');
            $stJs = "retornaValorBscInner( '".$_GET['stNomCampoCod']."', '".$_REQUEST['stIdCampoDesc']."', 'frm', '".$stDescricao."');";

        } else {
            $stJs  = 'f.'.$_GET["stNomCampoCod"].'.value = "";';
            $stJs .= "d.getElementById('".$_REQUEST['stIdCampoDesc']."').innerHTML = '&nbsp;';\n";
        }

        if ($stJs) echo $stJs;
        exit;
    break;

}

SistemaLegado::executaFrameOculto($stJs);
?>
