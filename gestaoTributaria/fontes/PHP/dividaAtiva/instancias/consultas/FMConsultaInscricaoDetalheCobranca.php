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
  * Página de Formulario de Detalhes da Parcela Selecionada
  * Data de criação : 14/02/2007

    * @author Analista: Fabio Bertoldi
    * @author Programador: Fernando Piccini Cercato

    * $Id: FMConsultaInscricaoDetalheCobranca.php 63839 2015-10-22 18:08:07Z franver $

    Caso de uso: uc-05.04.09
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaAtiva.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaParcela.class.php" );

$obTDATDividaParcela = new TDATDividaParcela;
$obTDATDividaAtiva = new TDATDividaAtiva;

if ($_REQUEST['situacao'] == 'Cancelada') {// and $_REQUEST['motivo_cancelamento'] ) {
    $obLblMotivo = new Label;
    $obLblMotivo->setName   ('stMotivo');
    $obLblMotivo->setValue  ($_REQUEST['motivo_cancelamento']);
    $obLblMotivo->setRotulo ( 'Motivo Cancelamento');

    $obLblDataCancelada = new Label;
    $obLblDataCancelada->setName    ('dtDataCancelamento');
    $obLblDataCancelada->setRotulo  ('Data de Cancelamento');
    $obLblDataCancelada->setValue   ( $_REQUEST['data_cancelamento'] );

    $obLblUsuarioCancelou = new Label;
    $obLblUsuarioCancelou->setName   ( 'stUsuarioCancelou' );
    $obLblUsuarioCancelou->setRotulo ( 'Usuário Responsável' );
    $obLblUsuarioCancelou->setTitle  ( 'Usuário Responsável' );
    $obLblUsuarioCancelou->setValue  ( $_REQUEST['usuario_cancelamento']   );

    $obFormCancelada = new Formulario;
    $obFormCancelada->addComponente($obLblUsuarioCancelou);
    $obFormCancelada->addComponente($obLblDataCancelada );
    $obFormCancelada->addComponente($obLblMotivo);
    $obFormCancelada->show();
}
$stFiltro = " WHERE num_parcela = 1 and num_parcelamento = ".$_REQUEST["num_parcelamento"];
$obTDATDividaParcela->recuperaTodos( $rsParcela, $stFiltro );

if ( !$rsParcela->Eof() ) {
    $obTDATDividaAtiva->setDado( 'data_base', $rsParcela->getCampo( "dt_vencimento_parcela" ) );
} else {
    $obTDATDividaAtiva->setDado( 'data_base', $_REQUEST['dt_parcelamento'] );
}

    $stFiltro = " AND inscricao.num_parcelamento = ".$_REQUEST["num_parcelamento"];
    $obTDATDividaAtiva->listaConsultaInscricoesSimples( $rsListaInscricoes, $stFiltro );

    $flValorFinal = $flValorFinalRed = 0.00;
    //BUSCA OS TOTAIS DA BASE PARA MONTAR OS VALORES DAS COBRANÇAS CONFORME A LISTA DE VALORES LANÇADOS
    $obTDATDividaParcela->recuperaTotaisParcelamento($rsTotaisPArcelamento, $_REQUEST["num_parcelamento"]);

    $flValorFinal = $rsTotaisPArcelamento->getCampo("vlr_credito") + $rsTotaisPArcelamento->getCampo('vlr_acrescimo');
    $flValorFinalRed = $flValorFinal -  $rsTotaisPArcelamento->getCampo('vlr_reducao');

    $nuPorcentagem = 1;
    while ( !$rsListaInscricoes->Eof() ) {
        if ( $flValorFinal > 0 and $rsListaInscricoes->getCampo('valor_reducao') > 0 ) {
            $nuPorcentagem = ($rsListaInscricoes->getCampo('valor_lancado') * 100) / $rsTotaisPArcelamento->getCampo('vlr_credito');
            $flValorTMP = ($rsTotaisPArcelamento->getCampo('vlr_parcela') * $nuPorcentagem) / 100;

            $rsListaInscricoes->setCampo( "valor_atualizado", $flValorTMP );
        }
        $rsListaInscricoes->proximo();
    }

    $rsListaInscricoes->addFormatacao ('valor_lancado', 'NUMERIC_BR');
    $rsListaInscricoes->addFormatacao ('valor_atualizado', 'NUMERIC_BR');

    $table = new Table();
    $table->setRecordset( $rsListaInscricoes );
    $table->setSummary('Lista de Inscrições Vinculadas');

    // lista zebrada
    ////$table->setConditional( true , "#efefef" );

    $table->Head->addCabecalho( 'Inscrição Dívida' , 15  );
    $table->Head->addCabecalho( 'Origem' , 30  );
    $table->Head->addCabecalho( 'Parcelas' , 4  );
    $table->Head->addCabecalho( 'Valor Original (R$)' , 13  );
    $table->Head->addCabecalho( 'Valor Cobrança (R$)' , 15  );

    $table->Body->addCampo( '[cod_inscricao] / [exercicio]', "C" );
    $table->Body->addCampo( '[origem]', "C" );
    $table->Body->addCampo( '[total_parcelas]', "C" );
    $table->Body->addCampo( 'valor_lancado', "D" );
    $table->Body->addCampo( 'valor_atualizado', "D" );

    $table->Foot->addSoma ( 'valor_lancado', "D" );
    $table->Foot->addSoma ( 'valor_atualizado', "D" );

    $table->montaHTML();
    echo $table->getHtml();

$stFiltro = " AND dp.num_parcelamento = ".$_REQUEST["num_parcelamento"]." order by num_parcela ASC ";
$obTDATDividaAtiva->ListaConsultaParcelas( $rsListaParcelas, $stFiltro );

if ( $rsListaParcelas->getNumLinhas() > 0 ) {
    $table = new TableTree();
    $table->setRecordset( $rsListaParcelas );
    $table->setSummary('Lista de Parcelas');
    $table->setArquivo( 'FMConsultaInscricaoDetalheValor.php' );
    $table->setParametros( array( "cod_lancamento" , "numeracao" , 'exercicio', 'cod_parcela', 'pagamento', 'database_br', 'vencimento', 'ocorrencia_pagamento', 'info_parcela', 'num_parcelamento') );

    // lista zebrada
    //$table->setConditional( true , "#efefef" );

    $table->Head->addCabecalho( 'Numeração' , 20  );
    $table->Head->addCabecalho( 'Numeração Migrada' , 10  );
    $table->Head->addCabecalho( 'Parcela' , 10  );
    $table->Head->addCabecalho( 'Valor (R$)' , 10  );
    $table->Head->addCabecalho( 'Vencimento' , 10  );
    $table->Head->addCabecalho( 'Situação' , 10  );

    $table->Body->addCampo( '[numeracao] / [exercicio]', "C" );
    $table->Body->addCampo( '[numeracao_migracao] / [prefixo]', "C" );
    $table->Body->addCampo( '[num_parcela] / [total_de_parcelas]', "C" );
    $table->Body->addCampo( 'vlr_parcela', "D" );
    $table->Body->addCampo( 'vencimento', "C" );
    $table->Body->addCampo( 'situacao', "C" );

    $table->montaHTML();
    echo $table->getHtml();
}
