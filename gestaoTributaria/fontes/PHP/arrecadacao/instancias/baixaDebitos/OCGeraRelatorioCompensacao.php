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
    * Página de processamento oculto e geração do relatório para Valores Lançados
    * Data de Criação   : 06/02/2008

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * $Id: OCGeraRelatorioCompensacao.php 59612 2014-09-02 12:00:51Z gelson $

    Caso de uso: uc-05.03.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php" );
include_once( CAM_FW_PDF."ListaPDF.class.php" );

$rsDadosLote = new RecordSet;

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );

$obPDF->setModulo            ( "Arrecadação:"   );
$obPDF->setTitulo            ( "Créditos:" );
$obPDF->setSubTitulo         ( "Exercício - ".Sessao::getExercicio() );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );

$obPDF->setModulo            ( "Arrecadação:" );
$obPDF->setTitulo            ( "Créditos:" );
$obPDF->setSubTitulo         ( "Exercício - ".Sessao::getExercicio() );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$arDadosContribuinte = Sessao::read( 'dados_contribuinte' );
$arTitulo = array();
$arTitulo[] = array (
    "labelA" => "Contribuinte:",
    "labelB" => $arDadosContribuinte['numCGM']." - ".$arDadosContribuinte['nomCGM']
);

if ($arDadosContribuinte["inscricaoEconomica"]) {
    $arTitulo[] = array (
        "labelA" => "Inscrição Economica:",
        "labelB" => $arDadosContribuinte['inscricaoEconomica']
    );
}

if ($arDadosContribuinte["inscricaoMunicipal"]) {
    $arTitulo[] = array (
        "labelA" => "Inscrição Municipal:",
        "labelB" => $arDadosContribuinte['inscricaoMunicipal']
    );
}

$arTitulo[] = array (
    "labelA" => "Exercício:",
    "labelB" => Sessao::getExercicio()
);

$arTitulo[] = array (
    "labelA" => "Saldo Disponpível:",
    "labelB" => number_format( Sessao::read( 'saldo_disponivel' ), 2, ',', '.' )
);

$arTitulo[] = array (
    "labelA" => "Valor das Parcelas Selecionadas:",
    "labelB" => number_format( Sessao::read( 'total_pago' ), 2, ',', '.' )
);

$arTitulo[] = array (
    "labelA" => "Total para Compensação:",
    "labelB" => number_format( Sessao::read( 'total_compensacao' ), 2, ',', '.' )
);

$arTitulo[] = array (
    "labelA" => "Valor a Compensar:",
    "labelB" => number_format( Sessao::read( 'total_compensar' ), 2, ',', '.' )
);

$arTitulo[] = array (
    "labelA" => "Saldo Restante:",
    "labelB" => number_format( Sessao::read( 'saldo_restante' ), 2, ',', '.' )
);

$rsTitulo = new Recordset;
$rsTitulo->preenche ( $arTitulo );
$obPDF->addRecordSet( $rsTitulo );
#$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCabecalho   ( "" , 20, 7 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "" , 70, 7 );

$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo       ( "labelA" , 10, "B" );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo       ( "labelB" , 10  );

#================================================
//titulo
$arTitulo1 = array("tit" => "Parcelas Origem");

$rsTit1 = new Recordset;
$rsTit1->preenche( $arTitulo1 );
$rsTit1->setPrimeiroElemento();

$obPDF->addRecordSet( $rsTit1 );
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "Parcelas Origem"  ,20, 12, "B" );

$rsDados = new Recordset;
$rsDados->preenche( Sessao::read( 'parcelas_pagas' ) );

$obPDF->addRecordSet ( $rsDados );
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "Numeração", 14, 10 );
$obPDF->addCabecalho   ( "Parcela", 8, 10 );
$obPDF->addCabecalho   ( "Origem", 18, 10 );
$obPDF->addCabecalho   ( "Vencimento", 8, 10 );
$obPDF->addCabecalho   ( "Valor", 12, 10 );
$obPDF->addCabecalho   ( "Valor Corrigido", 12, 10 );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo       ( "numeracao", 8 );
$obPDF->addCampo       ( "parcela", 8 );
$obPDF->addCampo       ( "origem", 8 );
$obPDF->addCampo       ( "vencimento", 8 );
$obPDF->addCampo       ( "valor", 8 );
$obPDF->addCampo       ( "valor_pago", 8 );

$obPDF->addRecordSet( $rsTit1 );
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "Parcelas Compensadas"  ,20, 12, "B" );

unset ( $rsDados );
$rsDados = new Recordset;
$rsDados->preenche( Sessao::read( 'parcelas_vencer' ) );

$obPDF->addRecordSet ( $rsDados );
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "Numeração", 14, 10 );
$obPDF->addCabecalho   ( "Parcela", 8, 10 );
$obPDF->addCabecalho   ( "Origem", 18, 10 );
$obPDF->addCabecalho   ( "Vencimento", 8, 10 );
$obPDF->addCabecalho   ( "Valor", 12, 10 );
$obPDF->addCabecalho   ( "Valor Corrigido", 12, 10 );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo       ( "numeracao", 8 );
$obPDF->addCampo       ( "parcela", 8 );
$obPDF->addCampo       ( "origem", 8 );
$obPDF->addCampo       ( "vencimento", 8 );
$obPDF->addCampo       ( "valor", 8 );
$obPDF->addCampo       ( "valor_pago", 8 );

$arParcelasNovas = Sessao::read( 'parcelas_novas' );
if ( count( $arParcelasNovas ) > 0 ) {
    $obPDF->addRecordSet( $rsTit1 );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "Parcelas Novas"  ,20, 12, "B" );

    unset ( $rsDados );
    $rsDados = new Recordset;
    $rsDados->preenche( $arParcelasNovas );

    $obPDF->addRecordSet ( $rsDados );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "Numeração", 14, 10 );
    $obPDF->addCabecalho   ( "Parcela", 8, 10 );
    $obPDF->addCabecalho   ( "Origem", 18, 10 );
    $obPDF->addCabecalho   ( "Vencimento", 8, 10 );
    $obPDF->addCabecalho   ( "Valor", 12, 10 );

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "numeracao", 8 );
    $obPDF->addCampo       ( "parcela", 8 );
    $obPDF->addCampo       ( "origem", 8 );
    $obPDF->addCampo       ( "vencimento", 8 );
    $obPDF->addCampo       ( "valor", 8 );
}

$obPDF->show();
?>
