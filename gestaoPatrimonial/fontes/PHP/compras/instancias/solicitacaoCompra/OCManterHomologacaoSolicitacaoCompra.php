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
* Oculto de Homologação/Anulação de solicitações
* Data de Criação: 27/09/2006

* @author Analista     : Cleisson
* @author Desenvolvedor: Bruce Cruz de Sena

  $Id: OCManterHomologacaoSolicitacaoCompra.php 63865 2015-10-27 13:55:57Z franver $

* Casos de uso: uc-03.04.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

function montaSpanItens($exercicio, $cod_entidade, $cod_solicitacao, $registro_precos, $stReserva)
{
    include_once CAM_GP_COM_MAPEAMENTO.'TComprasSolicitacaoItem.class.php';
    include_once CAM_GP_COM_MAPEAMENTO.'TComprasSolicitacaoItemDotacao.class.php';

    $obTComprasSolicitacaoItem = new TComprasSolicitacaoItem;
    $obTComprasSolicitacaoItem->setDado('cod_solicitacao' , $cod_solicitacao );
    $obTComprasSolicitacaoItem->setDado('exercicio'       , $exercicio       );
    $obTComprasSolicitacaoItem->setDado('cod_entidade'    , $cod_entidade    );
    $obTComprasSolicitacaoItem->recuperaRelacionamentoItemHomologacao($rsLista);

    $rsLista->setPrimeiroElemento();

    while ( !$rsLista->eof() ) {
        if ($rsLista->getCampo('vl_item_solicitacao') > 0.00)
            $nuVlUnitario = $rsLista->getCampo('vl_item_solicitacao') / $rsLista->getCampo('qnt_item_solicitacao');
        else
            $nuVlUnitario = 0.00;

        $rsLista->setCampo('vl_unitario'    , number_format($nuVlUnitario                               ,2,',','.') );
        $rsLista->setCampo('quantidade'     , number_format($rsLista->getCampo('qnt_item_solicitacao' ) ,4,',','.') );
        $rsLista->setCampo('vl_total'       , number_format($rsLista->getCampo('vl_item_solicitacao' )  ,2,',','.') );
        $rsLista->setCampo('saldo'          , number_format($rsLista->getCampo('saldo' )                ,2,',','.') );
        if ($_REQUEST['stAcao'] == 'anular')
            $rsLista->setCampo('vl_reserva' , number_format($rsLista->getCampo('vl_reserva' )           ,2,',','.') );
        else
            $rsLista->setCampo('vl_reserva' , number_format($rsLista->getCampo('vl_item_solicitacao' )  ,2,',','.') );

        $rsLista->proximo();
    }

    $rsLista->setPrimeiroElemento();

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setRecordSet( $rsLista );
    $obLista->setTitulo( $stTitulo );
    $obLista->setTotaliza( "vl_total,Total,right,7" );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 3 );
    $obLista->ultimoCabecalho->setRowSpan( 2 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo('Item');
    $obLista->ultimoCabecalho->setWidth( 24 );
    $obLista->ultimoCabecalho->setRowSpan( 2 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo('Unidade de Medida');
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->ultimoCabecalho->setRowSpan( 2 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo('Centro de Custo');
    $obLista->ultimoCabecalho->setWidth( 13 );
    $obLista->ultimoCabecalho->setRowSpan( 2 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Saldo da Solicitação");
    $obLista->ultimoCabecalho->setWidth( 28 );
    $obLista->ultimoCabecalho->setColSpan( 3 );
    $obLista->commitCabecalho();

    if($registro_precos=='f' && $stReserva=='reserva_rigida'){
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Reserva Saldos");
        $obLista->ultimoCabecalho->setWidth( 26 );
        $obLista->ultimoCabecalho->setColSpan( 4 );
        $obLista->commitCabecalho();
    }
    
    $obLista->addCabecalho( true );
    $obLista->ultimoCabecalho->addConteudo("Valor Unitário");
    $obLista->ultimoCabecalho->setWidth( 8 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho( );
    $obLista->ultimoCabecalho->addConteudo("Quantidade");
    $obLista->ultimoCabecalho->setWidth( 8 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho( );
    $obLista->ultimoCabecalho->addConteudo("Valor Total");
    $obLista->ultimoCabecalho->setWidth( 8 );
    $obLista->commitCabecalho();
    
    if($registro_precos=='f' && $stReserva=='reserva_rigida'){
        $obLista->addCabecalho( );
        $obLista->ultimoCabecalho->addConteudo("Dotação");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();
    
        $obLista->addCabecalho( );
        $obLista->ultimoCabecalho->addConteudo("Desdobramento");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();
    
        $obLista->addCabecalho( );
        $obLista->ultimoCabecalho->addConteudo("Saldo Dotação");
        $obLista->ultimoCabecalho->setWidth( 8 );
        $obLista->commitCabecalho();
    
        $obLista->addCabecalho( );
        if ($_REQUEST['stAcao'] == 'anular')
            $obLista->ultimoCabecalho->addConteudo("Valor a Anular");
        else
            $obLista->ultimoCabecalho->addConteudo("Valor a Reservar");

        $obLista->ultimoCabecalho->setWidth( 8 );
        $obLista->commitCabecalho();
    }

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( '[cod_item] - [descricao_completa]' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( 'nom_unidade' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[cod_centro] - [descricao]" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento ( 'DIREITA' );
    $obLista->ultimoDado->setCampo( "vl_unitario" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento ( 'DIREITA' );
    $obLista->ultimoDado->setCampo( "quantidade" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento ( 'DIREITA' );
    $obLista->ultimoDado->setCampo( "vl_total" );
    $obLista->commitDado();
    
    if($registro_precos=='f' && $stReserva=='reserva_rigida'){
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "cod_despesa" );
        $obLista->commitDado();
    
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "desdobramento" );
        $obLista->commitDado();
    
        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento ( 'DIREITA' );
        $obLista->ultimoDado->setCampo( "saldo" );
        $obLista->commitDado();
    
        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento ( 'DIREITA' );
        $obLista->ultimoDado->setCampo( "vl_reserva" );
        $obLista->commitDado();
    }

    $obLista->montaHTML();
    $stHTML = str_replace( "\n" ,"" ,$obLista->getHTML() );
    $stHTML = str_replace( chr(13)  ,"<br>" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    $stJS = "d.getElementById('spnItens').innerHTML = '".$stHTML."';";

    return $stJS;
}

?>
