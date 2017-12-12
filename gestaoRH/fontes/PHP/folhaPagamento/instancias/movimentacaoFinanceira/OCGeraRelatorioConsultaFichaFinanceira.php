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
* Página relatório de Ficha Financeira
* Data de Criação   : 14/12/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @ignore

$Revision: 30766 $
$Name$
$Author: souzadl $
$Date: 2007-10-08 09:46:10 -0300 (Seg, 08 Out 2007) $

* Casos de uso: uc-04.05.38
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                                                         );
include_once( CAM_GA_CGM_NEGOCIO."RCGMPessoaFisica.class.php"                                           );
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php"                                     );

$obRRelatorio           = new RRelatorio;
$obCGM                  = new RCGMPessoaFisica;
$obPDF                  = new ListaPDF();
$obRFolhaPagamentoEvento= new RFolhaPagamentoEvento;

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->setCodigoEntidade( Sessao::getCodEntidade($boTransacao) );
$obRRelatorio->setExercicioEntidade( Sessao::getExercicio() );
$arFiltro = Sessao::read("filtroRelatorio");
if ($arFiltro == "contrato") {
    if ($arFiltro['inContrato'] != "") {
        $stFiltro  = $arFiltro['hdnCGM'] ;
        $stFiltro2 = $arFiltro['inContrato'];
        $obPDF->addFiltro( "CGM:       "    , $stFiltro );
        $obPDF->addFiltro( "Matrícula:  "    , $stFiltro2 );
    } else {
        $obPDF->addFiltro( "CGM:       "    , "Todos" );
    }
}
if ($arFiltro['stOpcao'] == "cgm_contrato") {
    if ($arFiltro['inNumCGM'] != "") {
        $stFiltro = $arFiltro['inNumCGM'] ." - ".$arFiltro['inCampoInner'];
        $obPDF->addFiltro( "CGM:       "    , $stFiltro );
    }
    if ($arFiltro['inContrato'] != "") {
        $stFiltro = $arFiltro['inContrato'];
        $obPDF->addFiltro( "Matrícula:       "    , $stFiltro );
    } else {
        $obPDF->addFiltro( "Matrícula:       "    , "Todos" );
    }
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
    $stFiltro = $arMes[(int) $arFiltro['inCodMes']]."/".$arFiltro['inAno'];
    $obPDF->addFiltro( "Competência:       "    , $stFiltro );
}

if ($arFiltro['inCodComplementar'] != "") {
    $obPDF->addFiltro( "Folha Complementar:       "    , $arFiltro['inCodComplementar'] );
}

if ($arFiltro['inCodConfiguracao'] != "") {
    $obRFolhaPagamentoEvento->addConfiguracaoEvento();
    $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->setCodConfiguracao($arFiltro['inCodConfiguracao']);
    $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->listarConfiguracaoEvento($rsConfiguracao);
    $stFiltro = $rsConfiguracao->getCampo('descricao');
    $obPDF->addFiltro( "Tipo de Cálculo:       "    , $stFiltro );
}
$obPDF->addFiltro("(S)","Folha Salário");
$obPDF->addFiltro("(C)","Folha Complementar");

$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Folha de Pagamento" );
$obPDF->setTitulo            ( "Relatório de Ficha Financeira" );
$obPDF->setSubTitulo         ( Sessao::getExercicio() );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );
$rsRecordSet = Sessao::read("rsFichaFinanceira") ;
$rsVazio = new RecordSet;
if ( $rsRecordSet->getNumLinhas() < 0 ) {
    $obPDF->addRecordSet($rsVazio);
}

while ( !$rsRecordSet->eof() ) {
    $arTemp = $rsRecordSet->getCampo('contratos');
    $rsTemp = new RecordSet;
    $rsTemp->preenche( $arTemp );

    $obPDF->addRecordSet($rsTemp);
    $obPDF->setAlinhamento  ( "R"           );
    $obPDF->addCabecalho    ( "",     10, 80);
    $obPDF->addCabecalho    ( "",     80, 10);
    $obPDF->setAlinhamento  ( "R"           );
    $obPDF->addCampo        ( "campo1", 8   );
    $obPDF->setAlinhamento  ( "L"           );
    $obPDF->addCampo        ( "campo2", 8   );

    $arTemp = $rsRecordSet->getCampo('eventos');
    $rsTemp = new RecordSet;
    $rsTemp->preenche( $arTemp );

    $obPDF->addRecordSet($rsVazio);
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->addRecordSet( $rsTemp );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho("Evento",          10, 10);
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("Descrição",       20, 10);
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("Desdobramento",   10, 10);
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho("Quantidade",      15, 10);
    $obPDF->addCabecalho("Proventos",       15, 10);
    $obPDF->addCabecalho("Descontos",       15, 10);

    $obPDF->setAlinhamento  ( "R"                   );
    $obPDF->addCampo        ("evento", 8            );
    $obPDF->setAlinhamento  ( "L"                   );
    $obPDF->addCampo        ("descricao", 8         );
    $obPDF->setAlinhamento  ( "L"                   );
    $obPDF->addCampo        ("desdobramento_texto", 8         );
    $obPDF->setAlinhamento  ( "R"                   );
    $obPDF->addCampo        ("quantidade", 8        );
    $obPDF->addCampo        ("proventos", 8         );
    $obPDF->addCampo        ("descontos", 8         );

    $arTemp = $rsRecordSet->getCampo('titulo1');
    $rsTemp = new RecordSet;
    $rsTemp->preenche( $arTemp );

    $obPDF->addRecordSet( $rsTemp );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho("",          80, 10);

    $obPDF->setAlinhamento  ( "C"                   );
    $obPDF->addCampo        ("campo1", 8            );

    $arTemp = $rsRecordSet->getCampo('bases');
    $rsTemp = new RecordSet;
    $rsTemp->preenche( (is_array($arTemp)) ? $arTemp : array() );

    $obPDF->addRecordSet( $rsTemp );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho("Evento",          10, 10);
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("Descrição",       60, 10);
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho("Valor",           15, 10);

    $obPDF->setAlinhamento  ( "R"                   );
    $obPDF->addCampo        ("codigo", 8            );
    $obPDF->setAlinhamento  ( "L"                   );
    $obPDF->addCampo        ("descricao", 8         );
    $obPDF->setAlinhamento  ( "R"                   );
    $obPDF->addCampo        ("valor", 8             );

    $arTemp = $rsRecordSet->getCampo('titulo2');
    $rsTemp = new RecordSet;
    $rsTemp->preenche( $arTemp );

    $obPDF->addRecordSet( $rsTemp );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho("",          80, 10);

    $obPDF->setAlinhamento  ( "C"                   );
    $obPDF->addCampo        ("campo1", 8            );

    $arTemp = $rsRecordSet->getCampo('descontos');
    $rsTemp = new RecordSet;
    $rsTemp->preenche( (is_array($arTemp)) ? $arTemp : array() );

    $obPDF->addRecordSet( $rsTemp );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho("Evento",          10, 10);
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("Descrição",       60, 10);
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho("Valor",           15, 10);

    $obPDF->setAlinhamento  ( "R"                   );
    $obPDF->addCampo        ("codigo", 8            );
    $obPDF->setAlinhamento  ( "L"                   );
    $obPDF->addCampo        ("descricao", 8         );
    $obPDF->setAlinhamento  ( "R"                   );
    $obPDF->addCampo        ("valor", 8             );

    $rsRecordSet->proximo();
}
$obPDF->show();
?>
