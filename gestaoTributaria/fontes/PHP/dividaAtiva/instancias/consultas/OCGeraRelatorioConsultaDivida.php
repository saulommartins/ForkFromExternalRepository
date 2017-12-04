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
 * Página de oculta de geração de relatório
 * Data de Criação   : 05/11/2015

 * @author:
 * @author:

 * @ignore:

 $Id: 

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                                                         );
include_once( CAM_FW_PDF."ListaPDF.class.php"                                                           );
include_once( CAM_GT_DAT_MAPEAMENTO."TDATDividaAtiva.class.php"                                         );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");
$boTransacao = false;

$obRRelatorio->setExercicio         ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho    ( $arConfiguracao );
$obPDF->setModulo                   ( "Relatório:" );
$obPDF->setTitulo                   ( "Créditos:"  );
$obPDF->setSubTitulo                ( "Exercício - ".Sessao::getExercicio() );
$obPDF->setUsuario                  ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura       ( $arConfiguracao   );

$arrInformacoes = Sessao::read('arrInformacoes');
$rsListaLancamentos = Sessao::read('rsListaLancamentos');
$rsListaCobrancas = Sessao::read('rsListaCobrancas');
$rsListaParcelas = Sessao::read('rsListaParcelas');
$rsListaInscricoesVinculadas = Sessao::read('rsListaInscricoesVinculadas');


#========================== INICIO CABEÇALHO ========

$arCabecalho = null;

$arCabecalho[] = Array (
    "LabelTitulo"   => "Contribuinte:",
    "LabelValor"    => $arrInformacoes['inNumCGMContrib'].' - '. $arrInformacoes['inNomCGMContrib']
);
$arCabecalho[] = Array (
    "LabelTitulo"   => "Inscrição/Ano:",
    "LabelValor"    => $arrInformacoes['inCodInscricao'].'/'. $arrInformacoes['inExercicio']
);
$arCabecalho[] = Array (
    "LabelTitulo"   => "Data da Inscrição:",
    "LabelValor"    => $arrInformacoes['dtDataBase_br']
);
if ($arrInformacoes['inInscMunic']) {
    $arCabecalho[] = Array (
        "LabelTitulo"   => "Inscrição Imobiliária:",
        "LabelValor"    => $arrInformacoes['inInscMunic']
    );
} else {
    $arCabecalho[] = Array (
        "LabelTitulo"   => "Inscrição Econômica:",
        "LabelValor"    => $arrInformacoes['inInscEcon']
    );
}
$arCabecalho[] = Array (
    "LabelTitulo"   => "Situação:",
    "LabelValor"    => $arrInformacoes['stSituacao']
);

$arCabecalho[] = Array (
    "LabelTitulo"   => "Autoridade Competente:",
    "LabelValor"    => $arrInformacoes['inNumCGMAutorid'].' - '. $arrInformacoes['inNomCGMAutorid']
);

$rsCabecalho = new RecordSet;
$rsCabecalho->preenche  ( $arCabecalho );
$obPDF->addRecordSet    ( $rsCabecalho );
$obPDF->setAlinhamento  ( "R" );
$obPDF->addCabecalho    ( "Dados da Consulta"   ,20 , 14, "B" );
$obPDF->setAlinhamento  ( "L" );
$obPDF->addCabecalho    ( "da Dívida Ativa"    ,65 , 14, "B" );

$obPDF->setAlinhamento  ( "R" );
$obPDF->addCampo        ( "LabelTitulo" , 9, "B");
$obPDF->setAlinhamento  ( "L" );
$obPDF->addCampo        ( "LabelValor"  , 9  );
#========================== FIM CABEÇALHO ===========

//PREENCHE RECORDSET COM A FUNCAO PRINCIPAL
#========================== INICIO LISTA DE LANÇAMENTOS ========
$arTituloCabecalho = null;
$arTituloCabecalho[] = Array (
    "LabelTitulo"   => "Lista de Lançamentos"
);
$rsTituloCabecalho = new RecordSet;
$rsTituloCabecalho->preenche  ( $arTituloCabecalho );
$obPDF->addRecordSet    ( $rsTituloCabecalho );
$obPDF->setQuebraPaginaLista( false );
$obPDF->addCabecalho    ( "" ,80, 11, "B" );
$obPDF->setAlinhamento  ( "L" );
$obPDF->addCampo        ( "LabelTitulo" , 14, "B");

$flTotalLancado = $flTotalAtualizado = 0.00;
while ( !$rsListaLancamentos->eof() ) {
    $flTotalLancado     += $rsListaLancamentos->getCampo ( "valor_lancado" );
    $flTotalAtualizado  += $rsListaLancamentos->getCampo ( "valor_atualizado" );
    $rsListaLancamentos->proximo();
}
$arTotaisLancamentos[] = array (
    "label"                 => "Totais:"
    , "total_lancado"       => $flTotalLancado
    , "total_atualizado"    => $flTotalAtualizado
);

$rsListaLancamentos->addFormatacao ('valor_lancado', 'NUMERIC_BR');
$rsListaLancamentos->addFormatacao ('valor_atualizado', 'NUMERIC_BR');
$rsListaLancamentos->setPrimeiroElemento();
$obPDF->addRecordSet($rsListaLancamentos);

$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento  ( "C" );
$obPDF->addCabecalho    ( "Exercício" ,9, 11, "B" );
$obPDF->setAlinhamento  ( "L" );
$obPDF->addCabecalho    ( "Crédito/Grupo de Crédito" ,27, 11, "B" );
$obPDF->setAlinhamento  ( "C" );
$obPDF->addCabecalho    ( "Parcelas" , 8, 11, "B" );
$obPDF->addCabecalho    ( "Valor Lançado (R$)" ,18, 11, "B" );
$obPDF->addCabecalho    ( "Valor Atualizado (R$)" ,18, 11, "B" );

$obPDF->setAlinhamento  ( "C" );
$obPDF->addCampo        ( "exercicio_original" , 10);
$obPDF->setAlinhamento  ( "L" );
$obPDF->addCampo        ( "nom_origem"           , 10  );//origem
$obPDF->setAlinhamento  ( "C" );
$obPDF->addCampo        ( "total_parcelas"   , 10  );
$obPDF->setAlinhamento  ( "R" );
$obPDF->addCampo        ( "valor_lancado"    , 10  );
$obPDF->addCampo        ( "valor_atualizado" , 10  );

$rsTotais = new RecordSet;
$rsTotais->preenche ( $arTotaisLancamentos );

$obPDF->addRecordSet($rsTotais);
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento  ( "R" );
$obPDF->addCabecalho    ( $rsTotais->getCampo('label') ,44, 10, "B" );
$obPDF->addCabecalho    ( number_format($rsTotais->getCampo('total_lancado'),2,',','.'),18, 10, "B" );
$obPDF->addCabecalho    ( number_format($rsTotais->getCampo('total_atualizado'),2,',','.'),18,10,"B" );
$obPDF->addCampo        ( "" , 2, "B");
$obPDF->addCampo        ( "" , 2, "B");
$obPDF->addCampo        ( "" , 2, "B");
#========================== FIM LISTA DE LANÇAMENTOS ========

#========================== INICIO LISTA DE COBRANÇAS ========
$arTituloCabecalho = null;
$arTituloCabecalho[] = Array (
    "LabelTitulo"   => "Lista de Cobranças"
);
$rsTituloCabecalho = new RecordSet;
$rsTituloCabecalho->preenche  ( $arTituloCabecalho );
$obPDF->addRecordSet    ( $rsTituloCabecalho );
$obPDF->setQuebraPaginaLista( false );
$obPDF->addCabecalho    ( "" ,80, 11, "B" );
$obPDF->setAlinhamento  ( "L" );
$obPDF->addCampo        ( "LabelTitulo" , 14, "B");

$rsListaCobrancas->addFormatacao("valor_parcelamento","NUMERIC_BR");
$rsListaCobrancas->setPrimeiroElemento();
while ( !$rsListaCobrancas->eof() ) {
    
    $arCobrancaAtual = array();
    $arCobrancaAtual[] = array (
        "numero_parcelamento"    => $rsListaCobrancas->getCampo ( "numero_parcelamento" )
        , "cod_modalidade"       => $rsListaCobrancas->getCampo ( "cod_modalidade" )
        , "descricao_modalidade" => substr($rsListaCobrancas->getCampo ( "descricao_modalidade"), 0, 40)
        , "dt_parcelamento"      => $rsListaCobrancas->getCampo ( "dt_parcelamento" )
        , "numcgm_usuario"       => $rsListaCobrancas->getCampo ( "numcgm_usuario" )
        , "nomcgm_usuario"       => substr($rsListaCobrancas->getCampo( "nomcgm_usuario"), 0, 30)
        , "qtd_parcelas"         => $rsListaCobrancas->getCampo ( "qtd_parcelas" )
        , "situacao"             => substr($rsListaCobrancas->getCampo ( "situacao" ), 0, 40)
        , "valor_parcelamento"   => $rsListaCobrancas->getCampo ( "valor_parcelamento" )
    );
    
    $arCobrancaAtual[] = array(
         "descricao_modalidade" => substr($rsListaCobrancas->getCampo ( "descricao_modalidade"), 40, strlen($rsListaCobrancas->getCampo("descricao_modalidade")))
        , "nomcgm_usuario"      => substr($rsListaCobrancas->getCampo( "nomcgm_usuario"), 30, strlen($rsListaCobrancas->getCampo("descricao_modalidade")))
    );
    $rsCobrancaAtual = new RecordSet;
    $rsCobrancaAtual->preenche ( $arCobrancaAtual );
    $obPDF->addRecordSet($rsCobrancaAtual);
    
    $obPDF->setQuebraPaginaLista( false );
    
    $obPDF->setAlinhamento  ( "C" );
    $obPDF->addCabecalho    ( "Cobrança"    ,9  , 11, "B" );
    $obPDF->setAlinhamento  ( "L" );
    $obPDF->addCabecalho    ( "Modalidade"  ,27 , 11, "B" );
    $obPDF->setAlinhamento  ( "C" );
    $obPDF->addCabecalho    ( "Data"        ,9  , 11, "B" );
    $obPDF->addCabecalho    ( "Usuário"     ,24, 11, "B" );
    $obPDF->addCabecalho    ( "Parcelas"    , 7, 11, "B" );
    $obPDF->addCabecalho    ( "Situação"    ,11, 11, "B" );
    $obPDF->addCabecalho    ( "Valor (R$)"  ,13, 11, "B" );
    
    $obPDF->setAlinhamento  ( "C" );
    $obPDF->addCampo        ( "numero_parcelamento" , 10);
    $obPDF->setAlinhamento  ( "L" );
    $obPDF->addCampo        ( "[cod_modalidade] - [descricao_modalidade]", 10  );
    $obPDF->setAlinhamento  ( "C" );
    $obPDF->addCampo        ( "dt_parcelamento"     , 10  );
    
    $obPDF->setAlinhamento  ( "L" );
    $obPDF->addCampo        ( "[numcgm_usuario] - [nomcgm_usuario]"    , 10  );
    
    $obPDF->setAlinhamento  ( "C" );
    $obPDF->addCampo        ( "qtd_parcelas"        , 10  );
    $obPDF->addCampo        ( "situacao"            , 10  );
    $obPDF->setAlinhamento  ( "R" );
    $obPDF->addCampo        ( "valor_parcelamento"  , 10  );
    
    #===================================== Motivo cancelamento
    if ( $rsListaCobrancas->getCampo("situacao") == 'Cancelada' ) {
    
        $arIVTotaisLancamentos[] = array (
            "label"                 => "Totais:"
            , "total_lancado"       => $flIVTotalLancado
            , "total_atualizado"    => $flIVTotalAtualizado
        );
    
        $arCancelada = array();
        $arCancelada[] = array(
            "usuario" => $rsListaCobrancas->getCampo("usuario_cancelamento"),
            "data"    => $rsListaCobrancas->getCampo("data_cancelamento"),
        );
    
    $rsCanceladas = new RecordSet;
    $rsCanceladas->preenche( $arCancelada );
    
    $obPDF->addRecordSet($rsCanceladas);
    $obPDF->setQuebraPaginaLista( false );
    
    $obPDF->setAlinhamento  ( "L" );
    $obPDF->addCabecalho    ( "Usuário Responsável"  ,40 , 11, "B" );
    $obPDF->setAlinhamento  ( "C" );
    $obPDF->addCabecalho    ( "Data Cancelamento"        ,30  , 11, "B" );
    
    $obPDF->setAlinhamento  ( "L" );
    $obPDF->addCampo        ( "usuario", 10  );
    $obPDF->setAlinhamento  ( "C" );
    $obPDF->addCampo        ( "data"     , 10  );
    
    $arCancelada = array();
    $stTemp = $rsListaCobrancas->getCampo("motivo_cancelamento");
    
    for ( $x=0; $x<strlen($stTemp); $x++ ) {
        $stTemp2 = '';
        for ($y=0; $y<165; $y++) {
             if ( $x < strlen($stTemp)) {
                 $stTemp2 .= $stTemp[$x];
             }
             $x++;
        }
        $x = $x -1;
        $arCancelada[] = array(
            "motivo" => $stTemp2
        );
    }
    
    $rsCanceladas = new RecordSet;
    $rsCanceladas->preenche( $arCancelada );
    
    $obPDF->addRecordSet($rsCanceladas);
    $obPDF->setQuebraPaginaLista( false );
    
    $obPDF->setAlinhamento  ( "L" );
    $obPDF->addCabecalho    ( "Motivo"  ,100 , 11, "B" );
    
    $obPDF->setAlinhamento  ( "L" );
    $obPDF->addCampo        ( "motivo"     , 10  );
    }
    #======================== LISTA DE INSCRICOES VINCULADAS À CADA COBRANÇA ======
    $flValorFinal = $flValorFinalRed = 0.00;
    
    while ( !$rsListaInscricoesVinculadas->Eof() ) {
        $flValorFinal += $rsListaInscricoesVinculadas->getCampo("valor_atualizado");
        $rsListaInscricoesVinculadas->proximo();
    }
    
    $rsListaInscricoesVinculadas->setPrimeiroElemento();
    $flValorFinalRed = $flValorFinal - $rsListaInscricoesVinculadas->getCampo("valor_reducao");
    
    while ( !$rsListaInscricoesVinculadas->Eof() ) {
        if ( $flValorFinal > 0 and $rsListaInscricoesVinculadas->getCampo('valor_reducao') > 0 ) {
            $flValorTMP = ($rsListaInscricoesVinculadas->getCampo("valor_atualizado")*100) / $flValorFinal;
            $flValorTMP = ($flValorTMP*$flValorFinalRed) / 100;
            $rsListaInscricoesVinculadas->setCampo( "valor_atualizado", $flValorTMP );
        }
        $rsListaInscricoesVinculadas->proximo();
    }
    $rsListaInscricoesVinculadas->setPrimeiroElemento();
    
    if ( $rsListaInscricoesVinculadas->getNumLinhas() > 0 ) {
    
        $flIVTotalLancado = $flIVTotalAtualizado = 0.00;
        while ( !$rsListaInscricoesVinculadas->eof() ) {
            $flIVTotalLancado     += $rsListaInscricoesVinculadas->getCampo ( "valor_lancado" );
            $flIVTotalAtualizado  += $rsListaInscricoesVinculadas->getCampo ( "valor_atualizado" );
            $rsListaInscricoesVinculadas->proximo();
        }
        $arIVTotaisLancamentos[] = array (
            "label"                 => "Totais:"
            , "total_lancado"       => $flIVTotalLancado
            , "total_atualizado"    => $flIVTotalAtualizado
        );
    
        $arTituloCabecalho = null;
        $arTituloCabecalho[] = Array (
            "LabelTitulo"   => "Lista de Inscrições Vinculadas"
        );
        $rsTituloCabecalho = new RecordSet;
        $rsTituloCabecalho->preenche  ( $arTituloCabecalho );
        $obPDF->addRecordSet    ( $rsTituloCabecalho );
        $obPDF->setQuebraPaginaLista( false );
        $obPDF->addCabecalho    ( "" ,5, 11, "B" );
        $obPDF->addCabecalho    ( "" ,80, 11, "B" );
        $obPDF->setAlinhamento  ( "L" );
        $obPDF->addCampo        ( "" ,12  );
        $obPDF->addCampo        ( "LabelTitulo" , 12, "B");
    
        $rsListaInscricoesVinculadas->addFormatacao ('valor_lancado', 'NUMERIC_BR');
        $rsListaInscricoesVinculadas->addFormatacao ('valor_atualizado', 'NUMERIC_BR');
        $rsListaInscricoesVinculadas->setPrimeiroElemento();
    
        $rsListaInscricoesVinculadas->setPrimeiroElemento();
        $obPDF->addRecordSet($rsListaInscricoesVinculadas);
        $obPDF->setQuebraPaginaLista( false );
        $obPDF->setAlinhamento  ( "C" );
        $obPDF->addCabecalho    ( ""    , 5 , 10, "B" );
        $obPDF->setAlinhamento  ( "C" );
        $obPDF->addCabecalho    ( "Inscrição Dívida"    ,15 , 10, "B" );
        $obPDF->setAlinhamento  ( "L" );
        $obPDF->addCabecalho    ( "Origem"              ,15 , 10, "B" );
        $obPDF->setAlinhamento  ( "C" );
        $obPDF->addCabecalho    ( "Parcelas"            ,9 , 10, "B" );
        $obPDF->setAlinhamento  ( "R" );
        $obPDF->addCabecalho    ( "Valor Original (R$)" ,15 , 10, "B" );
        $obPDF->addCabecalho    ( "Valor Cobrança (R$)" ,15 , 10, "B" );
    
        $obPDF->setAlinhamento  ( "C" );
        $obPDF->addCampo        ( "", 9  );
        $obPDF->addCampo        ( "[cod_inscricao] / [exercicio]" , 9);
        $obPDF->setAlinhamento  ( "L" );
        $obPDF->addCampo        ( "origem", 9  );
        $obPDF->setAlinhamento  ( "C" );
        $obPDF->addCampo        ( "total_parcelas"     , 9  );
        $obPDF->setAlinhamento  ( "R" );
        $obPDF->addCampo        ( "valor_lancado"    , 9  );
        $obPDF->addCampo        ( "valor_atualizado"        , 9  );
    
        $rsTotais = new RecordSet;
        $rsTotais->preenche ( $arIVTotaisLancamentos );
    
        $obPDF->addRecordSet( $rsTotais);
        $obPDF->setQuebraPaginaLista( false );
        $obPDF->setAlinhamento  ( "R" );
        $obPDF->addCabecalho    ( $rsTotais->getCampo('label') ,44, 10, "B" );
        $obPDF->addCabecalho    ( number_format($rsTotais->getCampo('total_lancado'),2,',','.'),15, 10, "B");
        $obPDF->addCabecalho    (number_format($rsTotais->getCampo('total_atualizado'),2,',','.'),15,10,"B");
        $obPDF->addCampo        ( "" , 10, "B");
        $obPDF->addCampo        ( "" , 10, "B");
        $obPDF->addCampo        ( "" , 10, "B");
    
    }

#======================== FIM LISTA DE INSCRICOES VINCULADAS À CADA COBRANÇA ===
    
    if ( $rsListaParcelas->getNumLinhas() > 0 ) {

        $arTituloCabecalho = null;
        $arTituloCabecalho[] = Array (
            "LabelTitulo"   => "    Lista de Parcelas"
        );
        $rsTituloCabecalho = new RecordSet;
        $rsTituloCabecalho->preenche  ( $arTituloCabecalho );
        $obPDF->addRecordSet    ( $rsTituloCabecalho );
        $obPDF->setQuebraPaginaLista( false );
        $obPDF->addCabecalho    ( "" ,5, 11, "B" );
        $obPDF->addCabecalho    ( "" ,80, 11, "B" );
        $obPDF->setAlinhamento  ( "L" );
        $obPDF->addCampo        ( "" ,12  );
        $obPDF->addCampo        ( "LabelTitulo" , 12, "B");

        $obPDF->addRecordSet($rsListaParcelas);
        $obPDF->setQuebraPaginaLista( false );
        $obPDF->setAlinhamento  ( "C" );
        $obPDF->addCabecalho    ( ""    , 5 , 10, "B" );
        $obPDF->addCabecalho    ( "Numeração"           ,18 , 10, "B" );
        $obPDF->addCabecalho    ( "Numeração Migrada"   ,18 , 10, "B" );
        $obPDF->setAlinhamento  ( "C" );
        $obPDF->addCabecalho    ( "Parcela"            ,8 , 10, "B" );
        $obPDF->setAlinhamento  ( "C" );
        $obPDF->addCabecalho    ( "Valor (R$)" ,15 , 10, "B" );
        $obPDF->addCabecalho    ( "Vencimento" ,10 , 10, "B" );
        $obPDF->addCabecalho    ( "Situação" ,15 , 10, "B" );

        $obPDF->setAlinhamento  ( "C" );
        $obPDF->addCampo        ( "", 9  );
        $obPDF->addCampo        ( "[numeracao] / [exercicio]" , 9);
        $obPDF->addCampo        ( "[numeracao_migracao] / [prefixo]", 9  );
        $obPDF->addCampo        ( "[num_parcela] / [total_de_parcelas]"     , 9  );
        $obPDF->setAlinhamento  ( "R" );
        $obPDF->addCampo        ( "vlr_parcela"    , 9  );
        $obPDF->setAlinhamento  ( "C" );
        $obPDF->addCampo        ( "vencimento"        , 9  );
        $obPDF->addCampo        ( "situacao"        , 9  );

    }

    $rsListaCobrancas->proximo();
}

#========================== FIM LISTA DE COBRANÇAS ========

$obPDF->show();

?>