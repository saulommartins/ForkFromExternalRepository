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
    * Página de geração de relatorio para simulação de cobrança
    * Data de Criação   : 12/04/2007

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho

    * @ignore

    * $Id: OCGeraRelatorioSimulacaoCobranca.php 62853 2015-06-30 11:50:35Z evandro $

    * Casos de uso: uc-05.04.04
*/

/*
$Log$
Revision 1.8  2007/10/01 15:47:04  cercato
reducao do espado no relatorio de simulacao.

Revision 1.7  2007/09/11 14:12:27  cercato
correcao da verificacao dos creditos da modalidade e mudancas na simulacao de cobranca.

Revision 1.6  2007/09/06 16:11:14  cercato
alteracao para apresentar todas inscricoes na lista do relatorio.

Revision 1.5  2007/09/05 16:00:10  cercato
adicionando acrescimos dinamicos.

Revision 1.4  2007/08/09 15:02:08  cercato
alterando relatorio de cobranca e mostrando grupo original.

Revision 1.3  2007/07/31 19:05:02  cercato
alterando forma de apresentar relatorio de cobranca.

Revision 1.2  2007/07/24 18:57:59  dibueno
*** empty log message ***

Revision 1.1  2007/04/12 21:11:12  dibueno
Bug #9096#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_PDF."RRelatorio.class.php" );
include_once ( CAM_FW_PDF."ListaPDF.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaAtiva.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATModalidade.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCreditoGrupo.class.php" );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );

$obPDF->setModulo            ( "Dívida Ativa:"   );
$obPDF->setTitulo            ( "Simulação de Cobrança:" );
$obPDF->setSubTitulo         ( "Exercício - ".Sessao::getExercicio() );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

#================================================ DADOS DA COBRANÇA

$rsInscricoes       = new Recordset;
$arInscricoesSessao = Sessao::read('inscricoes');
$arDadosSessao      = Sessao::read('dados');
$arParcelasSessao   = Sessao::read('parcelas');

$rsInscricoes->preenche ( $arInscricoesSessao );
for ( $inX=0; $inX<count($arDadosSessao); $inX++ ) {
    $arDadosSessao[$inX]["origem"] = preg_replace('/([\d\.]+) (.*)/', '$1', $arDadosSessao[$inX]["origem"]) . ' - ';
}

$rsDados = new Recordset;
$rsDados->preenche ( $arDadosSessao );
$flValorTotal      = 0.00;
$flValorOrigem     = 0.00;
$flReducao         = 0.00;
$arAcrescimos      = array();

while ( !$rsDados->eof() ) {
    for ( $inJ=0; $inJ<$rsDados->getCampo("total_de_acrescimos"); $inJ++ ) {
        $arAcrescimos[$inJ] += $rsDados->getCampo( 'valor_acrescimo_'.$inJ );
        $rsDados->setCampo( 'valor_acrescimo_'.$inJ, number_format( $rsDados->getCampo( "valor_acrescimo_".$inJ ), 2, ',', '.' ) );
    }

    $flValorOrigem += $rsDados->getCampo('vlr_parcela');
    $flReducao += $rsDados->getCampo('reducao');
    $flValorTotal += $rsDados->getCampo('vlr_final');

    $rsDados->setCampo( 'vlr_parcela', number_format( $rsDados->getCampo( "vlr_parcela" ), 2, ',', '.' ) );
    $rsDados->setCampo( 'reducao', number_format( $rsDados->getCampo( "reducao" ), 2, ',', '.' ) );
    $rsDados->setCampo( 'vlr_final', number_format( $rsDados->getCampo( "vlr_final" ), 2, ',', '.' ) );

    $rsDados->proximo();
}

$rsDados->setPrimeiroElemento();

while ( !$rsDados->eof() ) {

    if ( $inCodDividaAtual != $rsDados->getCampo('cod_inscricao') ) {

        $inCodDividaAtual = $rsDados->getCampo('cod_inscricao');
        $stCredito = $rsDados->getCampo('grupo_original');
        $arCredito = explode( '<br>', $stCredito );
        $contCredito = 0;
        while ( $contCredito < (count( $arCredito ) - 1 ) ) {
            $arDadosTMP[$contCredito] = array(
                "credito" => $arCredito[$contCredito],
                "data_vencimento" => null,
                "vlr_parcela" => null,
                "vlr_reducao" => null,
                "juros" => null,
                "multa" => null,
                "correcao" => null,
                "vlr_final" => null
            );

            for ( $inJ=0; $inJ<$rsDados->getCampo("total_de_acrescimos"); $inJ++ ) {
                $arDadosTMP[$contCredito][ "valor_acrescimo_".$inJ ] = null;
            }

            $contCredito++;
        }

        $arDadosTMP[$contCredito] = array(
            "cod_inscricao"     => $inCodDividaAtual,
            "exercicio"         => $rsDados->getCampo('exercicio'),
            "inscricao_tipo"    => $rsDados->getCampo('inscricao_tipo'),
            "credito"           => $arCredito[$contCredito],
            "data_vencimento"   => $rsDados->getCampo('dt_vencimento_parcela_br'),
            "vlr_parcela"       => number_format ( $rsDados->getCampo('vlr_parcela'), 2, ',', '.' ),
            "vlr_reducao"       => number_format ( $rsDados->getCampo('vlr_reducao'), 2, ',', '.' ),
            "juros"             => number_format ( $rsDados->getCampo('juros'), 2, ',', '.' ),
            "multa"             => number_format ( $rsDados->getCampo('multa'), 2, ',', '.' ),
            "correcao"          => number_format ( $rsDados->getCampo('correcao'), 2, ',', '.' ),
            "vlr_final"         => number_format ( $rsDados->getCampo('vlr_final'), 2, ',', '.' )
        );

        for ( $inJ=0; $inJ<$rsDados->getCampo("total_de_acrescimos"); $inJ++ ) {
            $arDadosTMP[$contCredito][ "valor_acrescimo_".$inJ ] = number_format( $rsDados->getCampo( "valor_acrescimo_".$inJ ), 2, ',', '.' );
        }
    }

    $rsDados->proximo();
}

$rsDados->setPrimeiroElemento();
$rsDadosOK = new Recordset;
$rsDadosOK->preenche ( $arDadosTMP );
$rsDadosOK->setPrimeiroElemento();

$rsParcelas = new Recordset;
$rsParcelas->preenche ( $arParcelasSessao );

    $arTitulo = array();
    $arTitulo[] = array (
        "labelA" => "Contribuinte:",
        "labelB" => $_REQUEST['inCGM']." - ".$_REQUEST['stNomCGM']
    );

    $arTitulo[] = array (
        "labelA" => "Exercício:",
        "labelB" => Sessao::getExercicio()
    );

    $obTDATDividaAtiva = new TDATDividaAtiva;
    $arInscricao = array();
    $inTotal = 0;
    while ( !$rsInscricoes->Eof() ) {
        $stFiltro = " WHERE ddc.cod_inscricao = ".$rsInscricoes->getCampo("cod_inscricao")." AND ddc.exercicio = '".$rsInscricoes->getCampo("exercicio")."'";
        $obTDATDividaAtiva->RecuperaEnderecoInscricao( $rsEndereco, $stFiltro );
        if ( !$rsEndereco->Eof() ) {
            $boIncluir = true;
            for ($inX=0; $inX<$inTotal; $inX++) {
                if ( $arInscricao[$inX] == $rsEndereco->getCampo( "inscricao" ) ) {
                    $boIncluir = false;
                    break;
                }
            }

            if ($boIncluir) {
                $arTitulo[] = array (
                    "labelA" => "Inscrição:",
                    "labelB" => $rsEndereco->getCampo( "inscricao" )
                );

                $arTitulo[] = array (
                    "labelA" => "Endereço:",
                    "labelB" => $rsEndereco->getCampo( "nom_logradouro" )
                );

                $arInscricao[$inTotal] = $rsEndereco->getCampo( "inscricao" );
                $inTotal++;
            }
        }

        $rsInscricoes->proximo();
    }

    $arTitulo[] = array (
        "labelA" => "Modalidade:",
        "labelB" => $_REQUEST['inCodModalidade']." - ".$_REQUEST['stDescModalidade']
    );

    $rsTitulo = new Recordset;
    $rsTitulo->preenche ( $arTitulo );
    $obPDF->addRecordSet( $rsTitulo );
    $obPDF->setAlturaLinha ( 5 );    
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho   ( "" , 10, 7 );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "" , 70, 7 );

    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ( "labelA" , 10, "B" );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "labelB" , 10  );

    # LISTA DE CREDITOS/GRUPOS DA COBRANÇA
    $arTituloCredito[] = array( "titulo" => "Lista de dívidas vinculadas à cobrança" );
    $rsTituloCredito = new Recordset;
    $rsTituloCredito->preenche ( $arTituloCredito );
    $obPDF->addRecordSet( $rsTituloCredito );
    $obPDF->setAlturaLinha ( 3 );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho   ( "", 70, 2 );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "[titulo]", 11, "B" );

    $arAux = $rsDados->getElementos();

    $i = 0;
    foreach ($arAux as $key => $value) {
        $arAux[$i]["vlr_reducao"] = number_format($arAux[$i]["vlr_reducao"] ,2 ,",",".");
        $i++;
    }
    unset( $rsDados );
    $rsDados = new Recordset;
    $rsDados->preenche ( $arAux );

    $obPDF->addRecordSet( $rsDados );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlturaLinha ( 3 );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "Inscrição Dívida", 7.5, 8, "B" );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho   ( "Exercício Devedor", 7, 8, "B" );
    $obPDF->addCabecalho   ( "Tipo", 5, 8, "B" );
    $obPDF->addCabecalho   ( "Valor Origem", 5, 8, "B" );

    for ( $inJ=0; $inJ<$rsDados->getCampo("total_de_acrescimos"); $inJ++ ) {
        $obPDF->addCabecalho ( $rsDados->getCampo( "nome_acrescimo_".$inJ ), 8, 8, "B" );
    }

    $obPDF->addCabecalho   ( "Redução", 6.5, 8, "B" );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho   ( "Sub-Total", 6, 8, "B" );

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "[cod_inscricao]/[exercicio]", 8 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo       ( "[exercicio_original]", 8 );
    $obPDF->addCampo       ( "[inscricao_tipo]", 8 );

    $obPDF->addCampo       ( "[vlr_parcela]", 8 );

    for ( $inJ=0; $inJ<$rsDados->getCampo("total_de_acrescimos"); $inJ++ ) {
        $obPDF->addCampo ( "[valor_acrescimo_".$inJ."]", 8 );
    }

    $obPDF->addCampo       ( "[reducao]", 8 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ( "[vlr_final]", 8 );

    $arSomatorioCredito[] = array( "labelListaParcela" => "Detalhamento do tributo de dívidas vinculadas" );
    unset( $rsSomatorioParcela );
    $rsSomatorioParcela = new Recordset;
    $rsSomatorioParcela->preenche ( $arSomatorioCredito );

    $flValorTotal   = number_format ( $flValorTotal, 2, ',', '.' );
    $flValorOrigem  = number_format ( $flValorOrigem, 2, ',', '.' );
    $flReducao      = number_format ( $flReducao, 2, ',', '.' );

    $obPDF->addRecordSet( $rsSomatorioParcela );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlturaLinha ( 3 );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "Totais:", 20.5, 8, "B" );
    $obPDF->addCabecalho   ( $flValorOrigem, 6.5, 8, "B" );
    
    for ( $inJ=0; $inJ<count($arAcrescimos); $inJ++ ) {
        if ( $inJ == (count($arAcrescimos)-1) ){
            $inTamanhoUltimoCampo = 7.5;
        }else{
            $inTamanhoUltimoCampo = 8;
        }

        $arAcrescimos[$inJ] = number_format ( $arAcrescimos[$inJ], 2, ',', '.' );
        $obPDF->addCabecalho   ( $arAcrescimos[$inJ], $inTamanhoUltimoCampo, 8, "B" );
    }

    $obPDF->addCabecalho   ( $flReducao, 7, 8, "B" );
    $obPDF->addCabecalho   ( $flValorTotal, 10, 8, "B" );

    unset( $arSomatorioCredito );
    $arSomatorioCredito[] = array( "labelListaParcela" => "Detalhamento do tributo de dívidas vinculadas" );
    unset( $rsSomatorioParcela );
    $rsSomatorioParcela = new Recordset;
    $rsSomatorioParcela->preenche ( $arSomatorioCredito );

    $obPDF->addRecordSet( $rsSomatorioParcela );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlturaLinha ( 1 );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "Detalhamento do tributo de dívidas vinculadas", 40, 10, "B" );

    unset( $rsDados );
    $rsDados = new Recordset;
    $rsDados->preenche ( $arDadosSessao );

    $obPDF->addRecordSet( $rsDados );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlturaLinha ( 3 );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "Inscrição Dívida", 12, 8, "B" );
    $obPDF->addCabecalho   ( "Grupo Origem", 14, 8, "B" );
    $obPDF->addCabecalho   ( "Credito Origem", 60, 8, "B" );
    $obPDF->addCampo       ( "[cod_inscricao]/[exercicio]", 8 );
    $obPDF->addCampo       ( "[grupo_original]", 8 );
    $obPDF->addCampo       ( "[origem][descricao_credito];", 8 );

    unset( $arSomatorioCredito );
    $arSomatorioCredito[] = array( "labelListaParcela" => "Lista de Parcelas" );
    $rsSomatorioParcela = new Recordset;
    $rsSomatorioParcela->preenche ( $arSomatorioCredito );

    $obPDF->addRecordSet( $rsSomatorioParcela );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlturaLinha ( 1 );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "Lista de Parcelas", 20, 10, "B" );
    # LISTA DE PARCELAS

    $rsParcelas->addFormatacao("vlr_parcela", "NUMERIC_BR");
    $obPDF->addRecordSet( $rsParcelas );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlturaLinha ( 3 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho   ( "Parcela", 8, 8, "B" );
    $obPDF->addCabecalho   ( "Vencimento", 8, 8, "B" );
    $obPDF->addCabecalho   ( "Valor", 7, 8, "B" );

    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo       ( "[nr_parcela]", 8 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo       ( "[data_vencimento]", 8 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ( "[vlr_parcela]", 8 );

$obPDF->show();
?>
