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
    * Página de Formulario de PRocessamento para exclusão de Dívida Ativa

    * Data de Criação   : 05/10/2006

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho
    * @ignore

    * $Id: PRExcluirInscricao.php 62811 2015-06-22 19:07:59Z lisiane $

    *Casos de uso: uc-05.04.02

*/

/*
$Log$
Revision 1.8  2007/08/15 19:59:29  cercato
alteracao nos documentos.

Revision 1.7  2007/08/14 15:14:54  cercato
adicionando exercicio em funcao de alteracao na base de dados.

Revision 1.6  2007/08/01 20:23:11  cercato
adicionada validacao para nao permitir excluir escricao que possui parcelas da cobranca.

Revision 1.5  2007/07/23 19:22:15  cercato
Bug#9723#

Revision 1.4  2007/07/17 14:38:11  cercato
correcao para rotina de cancelamento de divida.

Revision 1.3  2007/07/17 13:38:19  cercato
correcao para rotina de cancelamento de divida.

Revision 1.2  2007/04/16 13:13:20  cercato
Bug #9107#

Revision 1.1  2006/10/06 16:59:49  dibueno
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaCancelada.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaDocumento.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATProcessoCancelamento.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaParcelamento.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaParcela.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoModeloDocumento.class.php" );

if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "alterar";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ExcluirInscricao";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

//include_once( $pgJs );

Sessao::remove('link');
Sessao::remove('stLink');

$arInscricao = explode ( '/', $request->get('inCodInscricao') );

$inCodInscricao = $arInscricao[0];
$inExercicio    = $arInscricao[1];
$stMotivo       = $request->get('stMotivo');

$stFiltro = "where a.cod_acao = '".Sessao::read('acao')."'";
$obTModeloDocumento = new TAdministracaoModeloDocumento;
$obTModeloDocumento->recuperaRelacionamento($rsDocumentos, $stFiltro);

$stFiltro = " WHERE cod_inscricao = ".$inCodInscricao." AND exercicio = '".$inExercicio."'";
$obTDividaCancelada = new TDATDividaCancelada;
$obTDividaCancelada->recuperaTodos( $rsInscricao, $stFiltro );

if ( !$rsInscricao->Eof() ) {
    SistemaLegado::alertaAviso( $pgForm."?".Sessao::getId()."&stAcao=".$_REQUEST['stAcao'], "Inscrição de Dívida Ativa (".$inCodInscricao.") já estava cancelada.", "n_erro", "erro", Sessao::getId(), "../" );
    exit;
}

$stFiltro = " WHERE cod_inscricao = ".$inCodInscricao." AND exercicio = '".$inExercicio."'";
$obTDATDividaParcelamento = new TDATDividaParcelamento;
$obTDATDividaParcelamento->recuperaTodos( $rsListaNumeracao, $stFiltro, " num_parcelamento DESC LIMIT 1 " );

$obTDATDividaParcela = new TDATDividaParcela;
$stFiltro = " WHERE num_parcelamento = ".$rsListaNumeracao->getCampo("num_parcelamento")." AND paga = false AND cancelada = false ";
$obTDATDividaParcela->recuperaTodos( $rsListaParcelas, $stFiltro );

if ( !$rsListaParcelas->Eof() ) {
    SistemaLegado::alertaAviso( $pgForm."?".Sessao::getId()."&stAcao=".$_REQUEST['stAcao'], "Inscrição de Dívida Ativa (".$inCodInscricao.") possui cobranças em aberto. Não é possível cancelar!", "n_erro", "erro", Sessao::getId(), "../" );
    exit;
}

$stFiltro = " WHERE cod_inscricao = ".$inCodInscricao." AND exercicio = '".$inExercicio."'";
$obTDATDividaParcelamento->recuperaTodos( $rsListaNumeracao, $stFiltro, " num_parcelamento ASC LIMIT 1 " );
$inNumeroParcelamento = $rsListaNumeracao->getCampo( "num_parcelamento" );

Sessao::setTrataExcecao( true );
Sessao::getTransacao()->setMapeamento( $obTDividaCancelada );

    $obTDividaCancelada->setDado ('exercicio',      $inExercicio        );
    $obTDividaCancelada->setDado ('cod_inscricao',  $inCodInscricao     );
    $obTDividaCancelada->setDado ('numcgm',         Sessao::read('numCgm')     );
    $obTDividaCancelada->setDado ('motivo',         $stMotivo           );
    $obTDividaCancelada->inclusao();

    if ( $request->get('inProcesso') ) {
        $arProcesso = explode( "/", $request->get('inProcesso') );
        $obTDATProcessoCancelamento = new TDATProcessoCancelamento;
        $obTDATProcessoCancelamento->setDado( 'cod_inscricao', $inCodInscricao );
        $obTDATProcessoCancelamento->setDado( 'exercicio', $inExercicio );
        $obTDATProcessoCancelamento->setDado( 'cod_processo', $arProcesso[0] );
        $obTDATProcessoCancelamento->setDado( 'ano_exercicio', $arProcesso[1] );
        $obTDATProcessoCancelamento->inclusao();
    }

    $obTDATDividaDocumento = new TDATDividaDocumento;
    $arDocumentos = array();
    $inTotalDocumentos = 0;

    $stDocumentos = $rsDocumentos->getCampo("cod_documento");
    $stTipoDocumentos = $rsDocumentos->getCampo("cod_tipo_documento");

    while ( !$rsDocumentos->Eof() ) {
        $stDocumentos .= ",".$rsDocumentos->getCampo("cod_documento");
        $stTipoDocumentos .= ",".$rsDocumentos->getCampo("cod_tipo_documento");
        $obTDATDividaDocumento->setDado( "num_parcelamento", $inNumeroParcelamento );
        $obTDATDividaDocumento->setDado( "cod_documento", $rsDocumentos->getCampo("cod_documento") );
        $obTDATDividaDocumento->setDado( "cod_tipo_documento", $rsDocumentos->getCampo("cod_tipo_documento") );
        $obTDATDividaDocumento->inclusao();
        $inTotalDocumentos++;

        $rsDocumentos->proximo();
    }

Sessao::encerraExcecao();

if ($_REQUEST["boEmissaoDocumento"] == "on") { //boEmissaoDocumento
    
    $stCaminho = CAM_GT_DAT_INSTANCIAS."emissao/LSManterEmissao.php";

    $stParametros = "&stTipoModalidade=emissao";
    $stParametros .= "&stCodAcao=".Sessao::read('acao');
    $stParametros .= "&stOrigemFormulario=cancelamento_divida";
    $stParametros .= "&inNumeroParcelamento=".$inNumeroParcelamento;
    $stParametros .= "&stDocumentos=".$stDocumentos;
    $stParametros .= "&stTipoDocumentos=".$stTipoDocumentos;

    sistemaLegado::alertaAviso( $stCaminho."?".Sessao::getId().$stParametros."&stAcao=".$_REQUEST['stAcao'],"Inscrição de Dívida Ativa (".$inCodInscricao.") Cancelada.", $_REQUEST['stAcao'],"aviso", Sessao::getId(), "../");
} else {
    sistemaLegado::alertaAviso( $pgForm."?".Sessao::getId()."&stAcao=".$_REQUEST['stAcao'],"Inscrição de Dívida Ativa (".$inCodInscricao.") Cancelada.", $_REQUEST['stAcao'],"aviso", Sessao::getId(), "../" );
}
