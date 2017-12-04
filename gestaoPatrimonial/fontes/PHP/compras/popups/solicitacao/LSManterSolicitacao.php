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
/*
 * Arquivo instância para popup de Solicitação
 * Data de Criação   : 21/09/2006

 * @author Analista      Diego Barbosa Victoria
 * @author Desenvolvedor Diego Barbosa Victoria

 * @package URBEM
 * @subpackage

 * @ignore

 $Id: LSManterSolicitacao.php 63032 2015-07-17 18:04:12Z michel $
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stFncJavaScript .= " function insereSolicitacao(cod,desc) {                                    \n";
$stFncJavaScript .= "   var sCod;                                                               \n";
$stFncJavaScript .= "   var sDesc;                                                              \n";
$stFncJavaScript .= "   var d = window.opener.parent.frames['telaPrincipal'].document;          \n";
$stFncJavaScript .= "   sCod = cod;                                                             \n";
$stFncJavaScript .= "   sDesc = desc;                                                           \n";
$stFncJavaScript .= "   if ( d.getElementById('".$_REQUEST["campoNom"]."') ) {                  \n";
$stFncJavaScript .= "       d.getElementById('".$_REQUEST["campoNom"]."').innerHTML = sDesc;    \n";
$stFncJavaScript .= "   }                                                                       \n";
$stFncJavaScript .= "   d.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"].".value = sCod;      \n";
if ($inner!=0) {
$stFncJavaScript .= "   d.".$_REQUEST["nomForm"].".Hdn".$_REQUEST["campoNum"].".value = sCod;   \n";
}
if ($_REQUEST["campoNom"]) {
$stFncJavaScript .= "   d.".$_REQUEST["nomForm"].".".$_REQUEST["campoNom"].".value = sDesc;     \n";
$stFncJavaScript .= "   d.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"].".focus();           \n";
}
$stFncJavaScript .= "   window.close();                                                         \n";
$stFncJavaScript .= " }                                                                         \n";

if ($_REQUEST['stTipoBusca']) {
    include_once CAM_GP_COM_MAPEAMENTO.'TComprasSolicitacao.class.php';
    $obTSolicitacao = new TComprasSolicitacao();

    $stLink   = "&stTipoBusca=".$_REQUEST['stTipoBusca'];
    $stFiltro = "";

    switch ($_REQUEST['stTipoBusca']) {
        case 'solicitacao':
            if ($_REQUEST["campoNom"]) {
                $stLink .= '&campoNom='.$_REQUEST['campoNom'];
            }
            if ($_REQUEST["nomForm"]) {
                $stLink .= '&nomForm='.$_REQUEST['nomForm'];
            }
            if ($_REQUEST["campoNum"]) {
                $stLink .= '&campoNum='.$_REQUEST['campoNum'];
            }
            if ($_REQUEST['inCodEntidade']) {
                $stFiltro .= " AND solicitacao.cod_entidade = ".$_REQUEST["inCodEntidade"]." \n";
                $stLink   .= "&inCodEntidade=".$_REQUEST["inCodEntidade"];
            }
            if ($_REQUEST['stExercicio']) {
                $obTSolicitacao->setDado('exercicio',$_REQUEST['stExercicio']);
                $stLink .= "&stExercicio=".$_REQUEST['stExercicio'];
            }
            if ($_REQUEST['stCodSolicitacaoExcluida']!="") {
                $stFiltro .= " AND solicitacao.exercicio||solicitacao.cod_solicitacao||solicitacao.cod_entidade != '".$_REQUEST['stCodSolicitacaoExcluida']."'\n";
                $stLink   .= "&stCodSolicitacaoExcluida=".$_REQUEST["stCodSolicitacaoExcluida"];
            }
            $stOrdem.= "GROUP BY solicitacao.exercicio                                  \n";
            $stOrdem.= "        ,solicitacao.cod_entidade                               \n";
            $stOrdem.= "        ,solicitacao.cod_solicitacao                            \n";
            $stOrdem.= "        ,solicitacao.timestamp                                  \n";
            $stOrdem.= "        ,solicitante.nom_cgm                                    \n";
            $stOrdem.= "ORDER BY solicitacao.timestamp  DESC                            \n";

            $obTSolicitacao->recuperaRelacionamentoSolicitacao( $rsLista, $stFiltro, $stOrdem);

        break;

        case 'mapa_compras':
            if ($_REQUEST["campoNom"]) {
                $stLink .= '&campoNom='.$_REQUEST['campoNom'];
            }
            if ($_REQUEST["nomForm"]) {
                $stLink .= '&nomForm='.$_REQUEST['nomForm'];
            }
            if ($_REQUEST["campoNum"]) {
                $stLink .= '&campoNum='.$_REQUEST['campoNum'];
            }
            if ($_REQUEST['inCodEntidade']) {
                $stFiltro = " AND solicitacao.cod_entidade = ".$_REQUEST['inCodEntidade']." \n";
                $stLink   .= "&inCodEntidade=".$_REQUEST["inCodEntidade"];
            }
            if ($_REQUEST['stExercicio']) {
                $stFiltro.= " AND solicitacao.exercicio = '".$_REQUEST['stExercicio']."'\n";
                $stLink .= "&stExercicio=".$_REQUEST['stExercicio'];
            }
            
            $boRegistroPreco  = (isset($_REQUEST['boRegistroPreco'])) ? $_REQUEST['boRegistroPreco'] : 'false';
            $stFiltro .= " AND solicitacao.registro_precos = ".$boRegistroPreco." \n";
            $stLink   .= "&boRegistroPreco=".$boRegistroPreco;

            $stOrderBy = " ORDER BY solicitacao.timestamp DESC ";

            $obTSolicitacao->recuperaSolicitacoesNaoAtendidas( $rsLista, $stFiltro, $stOrderBy );
        break;

        // Adicionar novos cases de tipo de busca aqui.
    }
}
    $stLink .= "&stAcao=".$stAcao;

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Data" );
$obLista->ultimoCabecalho->setWidth( 8 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Nr. Solicitação");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Solicitante" );
$obLista->ultimoCabecalho->setWidth( 55 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 6 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "data" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO');
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_solicitacao" );
$obLista->ultimoDado->setAlinhamento ( 'CENTRO');
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_cgm" );
$obLista->ultimoDado->setAlinhamento ('ESQUERDA');
$obLista->commitDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:insereSolicitacao();" );
$obLista->ultimaAcao->addCampo("1","cod_solicitacao");
$obLista->ultimaAcao->addCampo("2","nom_cgm");
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->obJavaScript->addFuncao( $stFncJavaScript );
$obFormulario->show();

?>
