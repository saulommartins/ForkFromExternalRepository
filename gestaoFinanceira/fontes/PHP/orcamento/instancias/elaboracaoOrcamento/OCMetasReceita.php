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
    * Página de Formulario de Oculto de Previsão Receitaa
    * Data de Criação   : 28/07/2004

    * @author Desenvolvedor: Roberto Pawelski Rodrigues

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2008-02-13 15:31:44 -0200 (Qua, 13 Fev 2008) $

    * Casos de uso: uc-02.01.06
*/

/*
$Log$
Revision 1.5  2006/07/05 20:43:03  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoPrevisaoReceita.class.php" );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoReceita.class.php"                  );

$obRPrevisaoReceita = new ROrcamentoPrevisaoReceita;
$obROrcamentoReceita         = new ROrcamentoReceita;
$obMascara          = new Mascara;

/**
    * Define o nome dos arquivos PHP
*/
$stPrograma = "MetasReceita";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once($pgJS);

switch ($_POST["stCtrl"]) {

    case "buscaClassReceita":
    if ($_POST["inNumClassReceita"] != "") {
        $obRCGM->setNumCGM ( $_POST["inNumClassReceita"] );
        $stWhere = " numcgm = ".$obRCGM->getNumCGM();
        $null = "&nbsp;";
        $obRCGM->consultar($rsCgm, $stWhere);
        $inNumLinhas = $rsCgm->getNumLinhas();
        if ($inNumLinhas <= 0) {
            $js .= 'f.inNumClassReceita.value = "";';
            $js .= 'f.inNumClassReceita.focus();';
            $js .= 'd.getElementById("campoInner2").innerHTML = "'.$null.'";';
            $js .= "SistemaLegado::alertaAviso('@Valor inválido. (".$_POST["inNumClassReceita"].")','form','erro','".Sessao::getId()."');";
        } else {
            $stNomCgm = $rsCgm->getCampo("nom_cgm");
            $js .= 'd.getElementById("campoInner2").innerHTML = "'.$stNomCgm.'";';
        }
        SistemaLegado::executaFrameOculto($js);
    }
    break;

    case "mascaraClassificacaoFiltro":
        $arMascClassificacao = Mascara::validaMascaraDinamica( $_POST['stMascClassificacao'] , $_POST['inCodClassificacao'] );
        $js .= "f.inCodClassificacao.value = '".$arMascClassificacao[1]."'; \n";
        SistemaLegado::executaFrameOculto( $js );
    break;

    case "mascaraClassificacao":
        //monta mascara da RUBRICA DE DESPESA
        $arMascClassificacao = Mascara::validaMascaraDinamica( $_POST['stMascClassificacao'] , $_POST['inCodReceita'] );
        $js .= "f.inCodReceita.value = '".$arMascClassificacao[1]."'; \n";

        //busca DESCRICAO DA RUBRICA DE DESPESA
        $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setMascClassificacao( $arMascClassificacao[1] );
        $obROrcamentoReceita->obROrcamentoClassificacaoReceita->consultar( $rsRubrica );
        if ( $rsRubrica->getNumLinhas() > -1 ) {
            $js .= 'd.getElementById("stDescricaoReceita").innerHTML = "'.$rsRubrica->getCampo("descricao").'";';
        } else {
            $null = "&nbsp;";
            $js .= 'f.inCodReceita.value = "";';
            $js .= 'f.inCodReceita.focus();';
            $js .= 'd.getElementById("stDescricaoReceita").innerHTML = "'.$null.'";';
            $js .= "SistemaLegado::alertaAviso('@Valor inválido. (".$arMascClassificacao[1].")','form','erro','".Sessao::getId()."');";
        }
        SistemaLegado::executaFrameOculto( $js );
    break;

    case 'preencheInner':
        //busca DESCRICAO DA RUBRICA DE DESPESA
        $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setMascClassificacao( $_POST['inCodReceita'] );
        $obROrcamentoReceita->obROrcamentoClassificacaoReceita->consultar( $rsRubrica );
        if ( $rsRubrica->getNumLinhas() > -1 ) {
            $js .= 'd.getElementById("stDescricaoReceita").innerHTML = "'.$rsRubrica->getCampo("descricao").'";';
        }

        $js .= 'd.getElementById("stDescricaoRecurso").innerHTML  = "'.$_POST["stDescricaoRecurso"].'";';
        SistemaLegado::executaFrameOculto($js);
    break;

    case 'mascaraClassDedutora':
        if ($_POST['inCodClassificacao']) {
            $arMascClassificacao = Mascara::validaMascaraDinamica( $_POST['stMascara'],$_POST['inCodClassificacao'] );
            $js .= "f.inCodClassificacao.value = '".$arMascClassificacao[1]."'; \n";
          }
      SistemaLegado::executaFrameOculto( $js );
    break;

}
?>
