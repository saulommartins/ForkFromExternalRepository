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
  * Página de Processamento da Abertura de Cobranca Judicial
  * Data de criação : 11/09/2007

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

    * $Id: PRCobrancaJudicial.php 59612 2014-09-02 12:00:51Z gelson $

    Caso de uso: uc-05.04.02
**/

/*
$Log$
Revision 1.1  2007/09/11 20:44:13  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATCobrancaJudicial.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaDocumento.class.php" );

//MANTEM O FILTRO E A PAGINACAO
$stLink = Sessao::read('stLink');

//Define o nome dos arquivos PHP
$stPrograma = "CobrancaJudicial";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=".$_REQUEST['stAcao'];
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=".$_REQUEST['stAcao'];
$pgOcul = "OC".$stPrograma.".php";

switch ($_REQUEST['stAcao']) {
    case "cobrar":
        $obTDATCobrancaJudicial = new TDATCobrancaJudicial;
        $obTDATDividaDocumento = new TDATDividaDocumento;
        $obTDATCobrancaJudicial->recuperaListaDocumentos( $rsListaDocumentos, Sessao::read('acao') );

        $arNumDocumentos = array();
        $inTotalDocumentos = 0;

        Sessao::setTrataExcecao( true );
        Sessao::getTransacao()->setMapeamento( $obTDATCobrancaJudicial );

            foreach ($_REQUEST as $valor => $key) {
                if ( preg_match( "/boSelecionada_[0-9]/", $valor ) ) {
                    $arKey = explode( '§', $key );

                    $inCodInscricao = $arKey[0];
                    $inExercicio = $arKey[1];
                    $inNumCGM = $arKey[2];
                    $inNumParcelamento = $arKey[3];

                    $obTDATCobrancaJudicial->setDado ( 'cod_inscricao', $inCodInscricao );
                    $obTDATCobrancaJudicial->setDado ( 'exercicio', $inExercicio );
                    $obTDATCobrancaJudicial->setDado ( 'numcgm', Sessao::read('numCgm') );
                    $obTDATCobrancaJudicial->inclusao();

                    while ( !$rsListaDocumentos->Eof() ) {
                        $inTotalDocumentos++;

                        $obTDATDividaDocumento->setDado( 'num_parcelamento', $inNumParcelamento );
                        $obTDATDividaDocumento->setDado( 'cod_tipo_documento', $rsListaDocumentos->getCampo("cod_tipo_documento") );
                        $obTDATDividaDocumento->setDado( 'cod_documento', $rsListaDocumentos->getCampo("cod_documento") );
                        $obTDATDividaDocumento->inclusao();

                        $rsListaDocumentos->proximo();
                    }
                }
            }

//echo "finito<br>";
//exit;

        Sessao::encerraExcecao();

        if ( ( $inTotalDocumentos > 0 ) && ( $_REQUEST["boEmissao"] == "on" ) ) {
            $stCaminho = CAM_GT_DAT_INSTANCIAS."emissao/LSManterEmissao.php";

            $stParametros = "&stTipoModalidade=emissao";
            $stParametros .= "&stCodAcao=".Sessao::read('acao');
            $stParametros .= "&stOrigemFormulario=cobranca_judicial";
            $stParametros .= "&inNumeroParcelamento=".$inNumParcelamento;
            Sessao::remove('stLink');
            Sessao::remove('link');

            sistemaLegado::alertaAviso( $stCaminho."?".Sessao::getId().$stParametros."&stAcao=incluir","Cobrança Judicial de dívida efetuada com sucesso!", "incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso( $pgForm, "Cobrança Judicial de dívida efetuada com sucesso!", $_REQUEST['stAcao'], "aviso", Sessao::getId(), "../");
        }
        break;
}
