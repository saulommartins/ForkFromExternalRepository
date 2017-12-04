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
    * Página relatório de Evento
    * Data de Criação   : 11/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30840 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-26 17:12:04 -0300 (Ter, 26 Jun 2007) $

    * Casos de uso: uc-04.05.33
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                                                     );
include_once( CAM_GA_CGM_NEGOCIO."RCGMPessoaFisica.class.php"                                       );
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php"                                 );
include_once( CAM_GRH_PES_NEGOCIO."RPessoalEspecialidade.class.php"                                 );
include_once( CAM_GRH_PES_NEGOCIO."RPessoalCargo.class.php"                                         );

$obRRelatorio           = new RRelatorio;
$obCGM                  = new RCGMPessoaFisica;
$obPDF                  = new ListaPDF(L);
$obRFolhaPagamentoEvento= new RFolhaPagamentoEvento;
//$obROrganogramaLocal    = new ROrganogramaLocal;

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->setCodigoEntidade( Sessao::getCodEntidade($boTransacao) );
$obRRelatorio->setExercicioEntidade( Sessao::getExercicio() );
$arFiltro = Sessao::read('filtroRelatorio');
if ($arFiltro['inCodEventoInicial'] != "") {
    $obPDF->addFiltro( "Código Inicial    "    , $arFiltro['inCodEventoInicial'] );
}
if ($arFiltro['inCodEventoFinal'] != "") {
    $obPDF->addFiltro( "Código Final    "    , $arFiltro['inCodEventoFinal'] );
}
if ($arFiltro['boProvento'] != "") {
    $obPDF->addFiltro( "Natureza    "    , 'Proventos' );
}
if ($arFiltro['boDesconto'] != "") {
    $obPDF->addFiltro( "             "    , 'Descontos' );
}
if ($arFiltro['boInformativo'] != "") {
    $obPDF->addFiltro( "             "    , 'Informativos' );
}
if ($arFiltro['boBase'] != "") {
    $obPDF->addFiltro( "             "    , 'Bases' );
}
if ($arFiltro['boFixo'] != "") {
    $obPDF->addFiltro( "Tipo    "    , "Fixo" );
}
if ($arFiltro['boVariavel'] != "") {
    $obPDF->addFiltro( "         "    , "Variável" );
}
if ($arFiltro['boValor'] != "") {
    $obPDF->addFiltro( "Fixado         "    , "Valor" );
}
if ($arFiltro['boQuantidade'] != "") {
    $obPDF->addFiltro( "               "    , "Quantidade" );
}
if ($arFiltro['inCodConfiguracao'] != "") {
    $obRFolhaPagamentoEvento->addConfiguracaoEvento();
    $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->setCodConfiguracao( $arFiltro['inCodConfiguracao'] );
    $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->listarConfiguracaoEvento($rsConfiguracao);
    $obPDF->addFiltro( "Característica    "    , $rsConfiguracao->getCampo('descricao') );
}
if ($arFiltro['inCodSequencia'] != "") {
    $obRFolhaPagamentoEvento->obRFolhaPagamentoSequencia->setCodSequencia($arFiltro['inCodSequencia']);
    $obRFolhaPagamentoEvento->obRFolhaPagamentoSequencia->listarSequencia( $rsSequencia );
    $obPDF->addFiltro( "Sequência de Cálculo "    ,  $rsSequencia->getCampo('descricao') );
}
if ($arFiltro['inCodEspecialidade'] != "") {
    $obRPessoalEspecialidade = new RPessoalEspecialidade( new RPessoalCargo);
    $obRPessoalEspecialidade->roPessoalCargo->setCodCargo( Sessao::read('inCodCargo') );
    $obRPessoalEspecialidade->setCodEspecialidade( $arFiltro['inCodEspecialidade'] );
    $obRPessoalEspecialidade->consultaEspecialidadeCargo( $rsEspecialidade );
    $obPDF->addFiltro( "Especialidade "    ,   $rsEspecialidade->getCampo('descricao_especialidade'));
}
if ($arFiltro['boApresentar'] != "") {
    $obPDF->addFiltro( "Apresentar Função/Especialidade    "    , "Sim" );
}
if ($arFiltro['inCodMes'] != "") {
    $arMes = array();
    $arMes[1] =  'Janeiro';
    $arMes[2] =  'Fevereiro';
    $arMes[3] =  'Março';
    $arMes[4] =  'Abril';
    $arMes[5] =  'Maio';
    $arMes[6] =  'Junho';
    $arMes[7] =  'Julho';
    $arMes[8] =  'Agosto';
    $arMes[9] =  'Setembro';
    $arMes[10] =  'Outubro';
    $arMes[11] =  'Novembro';
    $arMes[12] =  'Dezembro';
    $stFiltro = $arMes[$arFiltro['inCodMes']]."/".$arFiltro['inAno'];
    $obPDF->addFiltro( "Competência:       "    , $stFiltro );
}
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Folha de Pagamento" );
$obPDF->setTitulo            ( "Relatório de Eventos" );
$obPDF->setSubTitulo         ( Sessao::getExercicio() );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );
$rsRecordSet = Sessao::read("relatorioEvento") ;
$rsVazio = new RecordSet;
if ( $rsRecordSet->getNumLinhas() < 0 ) {
    $obPDF->addRecordSet($rsVazio);
}
while ( !$rsRecordSet->eof() ) {
    $arTemp = $rsRecordSet->getCampo('evento');
    $rsTemp = new RecordSet;
    $rsTemp->preenche( $arTemp );

    $obPDF->addRecordSet($rsTemp);
    $obPDF->setAlinhamento  ( "L"                       );
    $obPDF->addCabecalho    ( "Evento"          ,10, 10 );
    $obPDF->addCabecalho    ( "Descrição"       ,30, 10 );
    $obPDF->addCabecalho    ( "Natureza"        ,9 , 10 );
    $obPDF->addCabecalho    ( "Tipo"            ,5 , 10 );
    $obPDF->addCabecalho    ( "Fixado"          ,7 , 10 );
    $obPDF->setAlinhamento  ( "R"                       );
    $obPDF->addCabecalho    ( "Quantidade/Valor",12, 10 );
    $obPDF->addCabecalho    ( "Un. Quantitativa",15, 10 );
    $obPDF->addCabecalho    ( "Sequencia"       ,10, 10 );

    $obPDF->setAlinhamento  ( "L"                 );
    $obPDF->addCampo        ( "evento"      , 8   );
    $obPDF->addCampo        ( "descricao"   , 8   );
    $obPDF->addCampo        ( "natureza"    , 8   );
    $obPDF->addCampo        ( "tipo"        , 8   );
    $obPDF->addCampo        ( "fixado"      , 8   );
    $obPDF->setAlinhamento  ( "R"                 );
    $obPDF->addCampo        ( "quant_valor" , 8   );
    $obPDF->addCampo        ( "und_quant"   , 8   );
    $obPDF->addCampo        ( "sequencia"   , 8   );

    $obRFolhaPagamentoEvento->addConfiguracaoEvento();
    $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->setCodConfiguracao( $arFiltro['inCodConfiguracao'] );
    $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->listarConfiguracaoEvento($rsConfiguracao);

    while ( !$rsConfiguracao->eof() ) {
        $obPDF->addRecordSet($rsVazio);
        $obPDF->setQuebraPaginaLista( false );
        $obPDF->setAlinhamento  ( "L"                 );
        $obPDF->addCabecalho(trim($rsConfiguracao->getCampo('descricao')), 100, 10 );

        $arTemp = $rsRecordSet->getCampo(trim($rsConfiguracao->getCampo('descricao'))."1");
        $rsTemp = new RecordSet;
        $rsTemp->preenche( $arTemp );

        $obPDF->addRecordSet( $rsTemp );
        $obPDF->setQuebraPaginaLista( false );
        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCabecalho("Rública",         20, 10);
        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCabecalho("Descrição",       40, 10);
        $obPDF->addCabecalho("Fórmula",         20, 10);

        $obPDF->setAlinhamento  ( "R"                   );
        $obPDF->addCampo        ("rubrica", 8           );
        $obPDF->setAlinhamento  ( "L"                   );
        $obPDF->addCampo        ("descricao", 8         );
        $obPDF->addCampo        ("formula", 8           );

        $arTemp = $rsRecordSet->getCampo(trim($rsConfiguracao->getCampo('descricao')).'2');
        $rsTemp = new RecordSet;
        $rsTemp->preenche( $arTemp );

        if ($arFiltro['boApresentar']) {
            $obPDF->addRecordSet( $rsTemp );
            $obPDF->setQuebraPaginaLista( false );
            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCabecalho("Função/Especialidade",      80, 10);

            $obPDF->setAlinhamento  ( "L"                      );
            $obPDF->addCampo        ("funcao_especialidade", 8 );
        }
        $rsConfiguracao->proximo();
    }

    $rsRecordSet->proximo();
}
$obPDF->show();
?>
