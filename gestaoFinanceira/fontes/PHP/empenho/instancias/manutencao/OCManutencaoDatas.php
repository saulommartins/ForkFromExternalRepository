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
    * Página de Formulário Oculto de Manutenção de Datas
    * Data de Criação   : 07/06/2005

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Cleisson Barboza

    * @ignore

    $Revision: 30805 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.03.16
*/

/*
$Log$
Revision 1.4  2006/07/05 20:48:49  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoManutencaoDatas.class.php"     );

//Define o nome dos arquivos PHP
$stPrograma = "ManutencaoDatas";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

$obREmpenhoManutencaoDatas = new REmpenhoManutencaoDatas;
$obREmpenhoManutencaoDatas->setCodEntidade      ( Sessao::read('inCodEntidade') );
$obREmpenhoManutencaoDatas->setCodEmpenho       ( Sessao::read('inCodEmpenho') );
$obREmpenhoManutencaoDatas->setExercicioEmpenho ( Sessao::read('stExercicioEmpenho') );
$obREmpenhoManutencaoDatas->listarDadosAnulacaoEmpenho($rsAnulacaoEmpenho);
$obREmpenhoManutencaoDatas->listarDadosLiquidacao($rsLiquidacao);
$obREmpenhoManutencaoDatas->listarDadosAnulacaoLiquidacao($rsAnulacaoLiquidacao);
$obREmpenhoManutencaoDatas->listarDadosOrdemPagamento($rsOrdemPagamento);
$obREmpenhoManutencaoDatas->listarDadosAnulacaoOrdemPagamento($rsAnulacaoOrdemPagamento);
$obREmpenhoManutencaoDatas->listarDadosPagamento($rsPagamento);
$obREmpenhoManutencaoDatas->listarDadosAnulacaoPagamento($rsAnulacaoPagamento);

function montaListas($rsRecordset1,$rsRecordset2,$rsRecordset3,$rsRecordset4,$rsRecordset5,$rsRecordset6,$rsRecordset7)
{
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setRecordSet( $rsRecordset1 );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Exercício ");
        $obLista->ultimoCabecalho->setWidth( 25 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Valor");
        $obLista->ultimoCabecalho->setWidth( 60 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Data Anulação");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "exercicio" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "vl_anulado" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();

        $obDt = new Data;
        $obDt->setName     ( "stDataEmpenhoAnulacao_[timestamp_alterado]_" );
        $obDt->setNull     ( false );
        $obDt->setValue    ( "dt_anulacao" );

        $obLista->addDadoComponente( $obDt );
        $obLista->ultimoDado->setCampo( "dt_anulacao" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDadoComponente();

        $obLista->montaHTML();
        $stHTML = $obLista->getHTML();
        $stHTML = str_replace( "\n" ,"" ,$stHTML );
        $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
        $stHTML = str_replace( "  " ,"" ,$stHTML );
        $stHTML = str_replace( "'","\\'",$stHTML );

        $js = "d.getElementById('spnListaAnulacaoEmpenho').innerHTML = '".$stHTML."';";

        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setRecordSet( $rsRecordset2 );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Exercício ");
        $obLista->ultimoCabecalho->setWidth( 25 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Liquidação");
        $obLista->ultimoCabecalho->setWidth( 30 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Valor");
        $obLista->ultimoCabecalho->setWidth( 30 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Data Liquidação");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "exercicio" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "cod_nota" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "vl_total" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();

        $obDt = new Data;
        $obDt->setName     ( "stDataLiquidacao_[exercicio]_[cod_nota]_" );
        $obDt->setNull     ( false );
        $obDt->setValue    ( "dt_liquidacao" );
        $obLista->addDadoComponente( $obDt );
        $obLista->ultimoDado->setCampo( "dt_liquidacao" );
        $obLista->commitDadoComponente();

        $obLista->montaHTML();
        $stHTML = $obLista->getHTML();
        $stHTML = str_replace( "\n" ,"" ,$stHTML );
        $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
        $stHTML = str_replace( "  " ,"" ,$stHTML );
        $stHTML = str_replace( "'","\\'",$stHTML );

        $js .="d.getElementById('spnListaLiquidacao').innerHTML = '".$stHTML."';";

         $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setRecordSet( $rsRecordset3 );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Exercício ");
        $obLista->ultimoCabecalho->setWidth( 25 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Liquidação");
        $obLista->ultimoCabecalho->setWidth( 30 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Valor");
        $obLista->ultimoCabecalho->setWidth( 30 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Data Liquidação");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "exercicio" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "cod_nota" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "vl_anulado" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();

        $obDt = new Data;
        $obDt->setName     ( "stDataLiquidacaoAnulacao_[exercicio]_[cod_nota]_[timestamp_alterado]_" );
        $obDt->setNull     ( false );
        $obDt->setValue    ( "dt_anulacao" );
        $obLista->addDadoComponente( $obDt );
        $obLista->ultimoDado->setCampo( "dt_anulacao" );
        $obLista->commitDadoComponente();

        $obLista->montaHTML();
        $stHTML = $obLista->getHTML();
        $stHTML = str_replace( "\n" ,"" ,$stHTML );
        $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
        $stHTML = str_replace( "  " ,"" ,$stHTML );
        $stHTML = str_replace( "'","\\'",$stHTML );

        $js .="d.getElementById('spnListaAnulacaoLiquidacao').innerHTML = '".$stHTML."';";

        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setRecordSet( $rsRecordset4 );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Exercício ");
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("O.P.");
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Liquidação");
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Valor");
        $obLista->ultimoCabecalho->setWidth( 30 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Data O.P.");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "exercicio" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "cod_ordem" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "exercicio_nota" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "vl_pagamento" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();

        $obDt = new Data;
        $obDt->setName     ( "stDataOrdem_[exercicio]_[cod_ordem]_" );
        $obDt->setNull     ( false );
        $obDt->setValue    ( "dt_emissao" );
        $obLista->addDadoComponente( $obDt );
        $obLista->ultimoDado->setCampo( "dt_emissao" );
        $obLista->commitDadoComponente();

        $obLista->montaHTML();
        $stHTML = $obLista->getHTML();
        $stHTML = str_replace( "\n" ,"" ,$stHTML );
        $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
        $stHTML = str_replace( "  " ,"" ,$stHTML );
        $stHTML = str_replace( "'","\\'",$stHTML );

        $js .="d.getElementById('spnListaOrdemPagamento').innerHTML = '".$stHTML."';";

        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setRecordSet( $rsRecordset5 );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Exercício ");
        $obLista->ultimoCabecalho->setWidth( 25 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("O.P.");
        $obLista->ultimoCabecalho->setWidth( 30 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Valor");
        $obLista->ultimoCabecalho->setWidth( 30 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Data Anulação");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "exercicio" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "cod_ordem" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "vl_anulado" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();

        $obDt = new Data;
        $obDt->setName     ( "stDataOrdemAnulacao_[exercicio]_[cod_ordem]_[timestamp_alterado]_" );
        $obDt->setNull     ( false );
        $obDt->setValue    ( "dt_anulacao" );
        $obLista->addDadoComponente( $obDt );
        $obLista->ultimoDado->setCampo( "dt_anulacao" );
        $obLista->commitDadoComponente();

        $obLista->montaHTML();
        $stHTML = $obLista->getHTML();
        $stHTML = str_replace( "\n" ,"" ,$stHTML );
        $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
        $stHTML = str_replace( "  " ,"" ,$stHTML );
        $stHTML = str_replace( "'","\\'",$stHTML );

        $js .="d.getElementById('spnListaAnulacaoOrdemPagamento').innerHTML = '".$stHTML."';";

        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setRecordSet( $rsRecordset6 );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Exercício ");
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("O.P.");
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Liquidação");
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Valor");
        $obLista->ultimoCabecalho->setWidth( 30 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Data Pagamento");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "exercicio" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "cod_ordem" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "notas" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "vl_pago" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "timestamp" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();

        $obLista->montaHTML();
        $stHTML = $obLista->getHTML();
        $stHTML = str_replace( "\n" ,"" ,$stHTML );
        $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
        $stHTML = str_replace( "  " ,"" ,$stHTML );
        $stHTML = str_replace( "'","\\'",$stHTML );

        $js .="d.getElementById('spnListaPagamento').innerHTML = '".$stHTML."';";

        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setRecordSet( $rsRecordset7 );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Exercício ");
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("O.P.");
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Liquidação");
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Valor");
        $obLista->ultimoCabecalho->setWidth( 30 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Data Pagamento");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "exercicio" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "cod_ordem" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "notas" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "vl_anulado" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "timestamp" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();

        $obLista->montaHTML();
        $stHTML = $obLista->getHTML();
        $stHTML = str_replace( "\n" ,"" ,$stHTML );
        $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
        $stHTML = str_replace( "  " ,"" ,$stHTML );
        $stHTML = str_replace( "'","\\'",$stHTML );

        $js .="d.getElementById('spnListaAnulacaoPagamento').innerHTML = '".$stHTML."';";
        SistemaLegado::executaIFrameOculto($js);
}

switch ($stCtrl) {
    case 'montaListas':
        montaListas($rsAnulacaoEmpenho,$rsLiquidacao, $rsAnulacaoLiquidacao,$rsOrdemPagamento,$rsAnulacaoOrdemPagamento,$rsPagamento,$rsAnulacaoPagamento);
    break;
}
?>
