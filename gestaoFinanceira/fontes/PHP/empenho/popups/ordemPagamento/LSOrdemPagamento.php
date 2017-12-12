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
* Arquivo instância para popup de Ordem de Pagamento
* Data de Criação: 15/02/2006

* @author Analista: Lucas Leusin Oaigen
* @author Desenvolvedor: Jose Eduardo Porto

$Revision: 30668 $
$Name$
$Author: cako $
$Date: 2007-01-24 16:33:53 -0200 (Qua, 24 Jan 2007) $

Casos de uso: uc-02.04.20
*/

/*
$Log$
Revision 1.3  2007/01/24 18:33:53  cako
Bug #7884#

Revision 1.2  2006/07/05 20:49:46  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoOrdemPagamento.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "OrdemPagamento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stFncJavaScript .= " function insereOrdemPagamento(num,nom) {  \n";
$stFncJavaScript .= " var sNum;                  \n";
$stFncJavaScript .= " var sNom;                  \n";
$stFncJavaScript .= " sNum = num;                \n";
$stFncJavaScript .= " sNom = nom;                \n";
$stFncJavaScript .= " if ( window.opener.parent.frames['telaPrincipal'].document.getElementById('".$_REQUEST["campoNom"]."') ) { window.opener.parent.frames['telaPrincipal'].document.getElementById('".$_REQUEST["campoNom"]."').innerHTML = sNom; } \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"].".value = sNum; \n";
if ($_REQUEST["campoNom"]) {
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNom"].".value = sNom; \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNom"].".focus(); \n";
}
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].mostraSpanOrdem(''); \n";
$stFncJavaScript .= " window.close();            \n";
$stFncJavaScript .= " }                          \n";

$obREmpenhoOrdemPagamento = new REmpenhoOrdemPagamento;
$stFiltro = "";
$stLink  .= "&stAcao=".$_REQUEST['stAcao'];
//Definição do filtro de acordo com os valores informados
$stLink .= "&stTipoPessoa=".$_REQUEST["stTipoPessoa"];

if ($_REQUEST["campoNom"]) {
    $stLink .= '&campoNom='.$_REQUEST['campoNom'];
}
if ($_REQUEST["nomForm"]) {
    $stLink .= '&nomForm='.$_REQUEST['nomForm'];
}
if ($_REQUEST["campoNum"]) {
    $stLink .= '&campoNum='.$_REQUEST['campoNum'];
}

if ($_REQUEST["inCodEntidade"]) {
    $obREmpenhoOrdemPagamento->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
    $stLink   .= "&inCodEntidade=".$_REQUEST["inCodEntidade"];
}
if ($_REQUEST["stExercicio"]) {
    $obREmpenhoOrdemPagamento->setExercicio( $_REQUEST['stExercicio'] );
    $stLink   .= "&stExercicio=".$_REQUEST["stExercicio"];
}
if ($_REQUEST["inCodOrdemPagamento"]) {
    $obREmpenhoOrdemPagamento->setCodigoOrdem( $_REQUEST['inCodOrdemPagamento'] );
    $stLink   .= "&inCodOrdemPagamento=".$_REQUEST["inCodOrdemPagamento"];
}

$rsLista = new RecordSet;
$obREmpenhoOrdemPagamento->setListarNaoPaga( true );
switch ($_REQUEST['stTipoBusca']){
    case 'bordero':
        $obREmpenhoOrdemPagamento->listarDadosPagamentoBordero( $rsLista, $boTransacao );
    break;

    case "bordero_recurso":
        include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoRecurso.class.php"          );
        $obTContabilidadePlanoRecurso          = new TContabilidadePlanoRecurso;
        $obTContabilidadePlanoRecurso->setDado('exercicio', $_REQUEST["stExercicio"] );
        $obTContabilidadePlanoRecurso->setDado('cod_plano', $_REQUEST['hdnCodPlano'] );
        $obTContabilidadePlanoRecurso->recuperaPorChave($rsRecurso,"","",$boTransacao);

        $stLink .= "&inCodRecurso=".$rsRecurso->getCampo('cod_recurso');
        $stLink .= "&hdnCodPlano=".$_REQUEST['hdnCodPlano'];
        $stLink .= "&stTipoBusca=bordero_recurso";
        $obREmpenhoOrdemPagamento->stExercicio = $_REQUEST["stExercicio"];
        $obREmpenhoOrdemPagamento->obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->setCodRecurso($rsRecurso->getCampo('cod_recurso'));
            
        $obREmpenhoOrdemPagamento->listarDadosPagamentoBorderoContaRecurso( $rsLista, $boTransacao);
    break;
    
    default:
        $obREmpenhoOrdemPagamento->listarDadosPagamento( $rsLista );
    break;
} 

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "&nbsp;"     );
$obLista->ultimoCabecalho->setWidth   ( 5            );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Ordem"      );
$obLista->ultimoCabecalho->setWidth   ( 10           );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nota/Empenho" );
$obLista->ultimoCabecalho->setWidth   ( 30           );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Data Emissão" );
$obLista->ultimoCabecalho->setWidth   ( 10           );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Valor"      );
$obLista->ultimoCabecalho->setWidth   ( 10           );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "&nbsp;"     );
$obLista->ultimoCabecalho->setWidth   ( 5            );
$obLista->commitCabecalho();
$obLista->addDado();
$obLista->ultimoDado->setCampo      ( "cod_ordem"   );
$obLista->ultimoDado->setAlinhamento( "DIREITA"     );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo      ( "nota_empenho" );
$obLista->ultimoDado->setAlinhamento( "CENTRO"                     );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo      ( "dt_emissao" );
$obLista->ultimoDado->setAlinhamento( "CENTRO"                     );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo      ( "valor_pagamento" );
$obLista->ultimoDado->setAlinhamento( "DIREITA"                    );
$obLista->commitDado();
$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:insereOrdemPagamento();" );
$obLista->ultimaAcao->addCampo("1","cod_ordem");
$obLista->ultimaAcao->addCampo("2","beneficiario");
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->obJavaScript->addFuncao( $stFncJavaScript );
$obFormulario->show();
?>
