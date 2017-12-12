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
    * Pagina Oculta de Autorização por licitação
    * Data de Criação   : 05/01/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 30805 $
    $Name$
    $Author: gelson $
    $Date: 2007-02-23 13:15:05 -0200 (Sex, 23 Fev 2007) $

    * Casos de uso: uc-02.03.02
                    uc-02.03.15
                    uc-02.01.08
*/

/*
$Log$
Revision 1.6  2007/02/23 15:15:05  gelson
Sempre que for autorização tem que ir a reserva. Adicionado em todos arquivos o caso de uso da reserva.

Revision 1.5  2006/07/05 20:47:28  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoAutorizacaoEmpenho.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterAutorizacaoLicitacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

$obREmpenhoAutorizacaoEmpenho = new REmpenhoAutorizacaoEmpenho;
$obREmpenhoAutorizacaoEmpenho->setExercicio( Sessao::getExercicio() );
//$obREmpenhoAutorizacaoEmpenho->checarFormaExecucaoOrcamento( $boDetalhadoExecucao );

function montaLista($arRecordSet , $boExecuta = true)
{
        global $boDetalhadoExecucao;
        $rsLista = new RecordSet;
        $rsLista->preenche( $arRecordSet );
        $rsLista->addFormatacao( "nuVlTotal", "NUMERIC_BR" );
        if ( !$rsLista->eof() ) {
            $obLista = new Lista;
            $obLista->setMostraPaginacao( false );
            $obLista->setRecordSet( $rsLista );
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("&nbsp;");
            $obLista->ultimoCabecalho->setWidth( 5 );
            $obLista->commitCabecalho();
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Fornecedor");
            $obLista->ultimoCabecalho->setWidth( 50 );
            $obLista->commitCabecalho();
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Dotação");
            $obLista->ultimoCabecalho->setWidth( 8 );
            $obLista->commitCabecalho();
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Valor Total");
            $obLista->ultimoCabecalho->setWidth( 10 );
            $obLista->commitCabecalho();

//            if ($boDetalhadoExecucao) {
//                $obLista->addCabecalho();
//                $obLista->ultimoCabecalho->addConteudo("Desdobramento");
//                $obLista->ultimoCabecalho->setWidth( 19 );
//                $obLista->commitCabecalho();
//            }

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("&nbsp;");
            $obLista->ultimoCabecalho->setWidth( 8 );
            $obLista->commitCabecalho();

            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "stFornecedor" );
            $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
            $obLista->commitDado();
            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "inDotacao" );
            $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
            $obLista->commitDado();
            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "nuVlTotal" );
            $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
            $obLista->commitDado();

//            if ($boDetalhadoExecucao) {
//                //Define objeto BuscaInner para desdobramento
//                $obBscDesdobramento = new BuscaInner;
//                $obBscDesdobramento->obCampoCod->setName    ( "stDesdobramento_" );
//                $obBscDesdobramento->obCampoCod->setValue   ( "" );
//                $obBscDesdobramento->obCampoCod->setReadOnly( true     );
//                $obBscDesdobramento->obCampoCod->setSize    ( 21       );
//                $obBscDesdobramento->obCampoCod->setMaxLength( 25       );
//                $obBscDesdobramento->setFuncaoBusca("abrePopUp('".CAM_GF_EMP_POPUPS."empenho/LSConsultaDesdobramento.php','frm','stDesdobramento','','juridica','".Sessao::getId()."&inCodDotacao=[inDotacao]&inCodEntidade='+document.frm.inCodEntidade.value,'800','550')" );
//
//                $obLista->addDadoComponente( $obBscDesdobramento );
//                $obLista->commitDadoComponente();
//            }

            $obLista->addAcao();
            $obLista->ultimaAcao->setAcao( "Consultar Itens" );
            $obLista->ultimaAcao->setFuncao( true );
            $obLista->ultimaAcao->setLink( "JavaScript:consultaItens();" );
            $obLista->ultimaAcao->addCampo("1","stNumCgm" );
            $obLista->ultimaAcao->addCampo("2","inDotacao");
            $obLista->commitAcao();

            $obLista->montaHTML();
            $stHTML = $obLista->getHTML();
            $stHTML = str_replace( "\n" ,"" ,$stHTML );
            $stHTML = str_replace( chr(13), "<br>", $stHTML);
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );

            $stLista    = "d.getElementById('spnLista').innerHTML = '".$stHTML."'; ";
        } else {
            $stLista    = "d.getElementById('spnLista').innerHTML = ''; ";
            Sessao::remove('arItens');
        }

        if ($boExecuta) {
            SistemaLegado::executaFrameOculto($stLista.$stVlTotal);
        } else {
            return $stLista.$stVlTotal;
        }

}

switch ($stCtrl) {
    case 'montaListaLicitacao':
        $obREmpenhoAutorizacaoEmpenho->setExercicio( Sessao::getExercicio() );
        $obREmpenhoAutorizacaoEmpenho->setNumLicitacao( $_POST['inCodLicitacao']    );
        $obREmpenhoAutorizacaoEmpenho->setTipoLicitacao( $_POST['stTipoModalidade'] );
        $obREmpenhoAutorizacaoEmpenho->listarItensLicitacao( $rsLista );

        Sessao::write('arItens', $rsLista );

        $nuVlTotal = 0;
        $inCount   = 0;
        while ( !$rsLista->eof() ) {

            if ( $rsLista->getCampo( 'dotacao' ) != $inDotacaoOld or $rsLista->getCampo( 'numcgm' ) != $inNumCgmOld ) {
                $arLicitacao[$inCount]['stFornecedor'] = $rsLista->getCampo( 'nom_cgm' );
                $arLicitacao[$inCount]['stNumCgm']     = $rsLista->getCampo( 'numcgm'  );
                $arLicitacao[$inCount]['inDotacao']    = $rsLista->getCampo( 'dotacao' );
            }

            $nuVlTotal = bcadd( $nuVlTotal, $rsLista->getCampo( 'vl_total' ), 4 );

            $inNumCgmOld  = $rsLista->getCampo( 'numcgm'  );
            $inDotacaoOld = $rsLista->getCampo( 'dotacao' );

            $rsLista->proximo();

            if ( $rsLista->getCampo( 'dotacao' ) != $inDotacaoOld or $rsLista->getCampo( 'numcgm' ) != $inNumCgmOld ) {
                $arLicitacao[$inCount]['nuVlTotal'] = $nuVlTotal;
                $nuVlTotal = 0;
                $inCount++;
            }

        }

        $js  = montaLista( $arLicitacao, false );
        $js .= 'LiberaFrames( true, false );';
        SistemaLegado::executaFrameOculto($js);
    break;
}
?>
