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
    * Formulário
    * Data de Criação: 13/10/2008

    * @author Analista      Dagiane Vieira
    * @author Desenvolvedor Rafael Garbin

    * @package URBEM
    * @subpackage

    $Id:
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoEscalaContrato.class.php"                                 );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoEscalaTurno.class.php"                                    );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoEscala.class.php"                                         );

$stFiltro  = " AND escala_contrato.cod_contrato = ".$_REQUEST["inCodContrato"]."            \n";
$stFiltro .= " AND EXISTS ( SELECT 1                                                        \n";
$stFiltro .= "                FROM ponto.escala_turno              \n";
$stFiltro .= "                   , ( SELECT cod_escala                                      \n";
$stFiltro .= "                            , max(timestamp) as timestamp                     \n";
$stFiltro .= "                         FROM ponto.escala_turno     \n";
$stFiltro .= "                     GROUP BY cod_escala ) as max_escala_turno                \n";
$stFiltro .= "               WHERE escala_turno.cod_escala = escala_contrato.cod_escala     \n";
$stFiltro .= "                 AND escala_turno.cod_escala = max_escala_turno.cod_escala    \n";
$stFiltro .= "                 AND escala_turno.timestamp  = max_escala_turno.timestamp      \n";
$stFiltro .= "                 AND escala_turno.dt_turno between to_date('".$_REQUEST["stDataInicial"]."', 'dd/mm/yyyy') AND to_date('".$_REQUEST["stDataFinal"]."', 'dd/mm/yyyy'))  \n";

$obTPontoEscalaContrato = new TPontoEscalaContrato();
$obTPontoEscalaContrato->recuperaContratosEscala($rsEscalaContrato, $stFiltro);

$obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao($obRFolhaPagamentoPeriodoMovimentacao);

$obBtnFechar = new Button;
$obBtnFechar->setName               ( "fechar" );
$obBtnFechar->setValue              ( "Fechar" );
$obBtnFechar->obEvento->setOnClick  ( "window.close();" );

$obFormulario = new Formulario;
$obFormulario->addTitulo       ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"         );
$obFormulario->addTitulo       ( "Dados da Escala"                                                        );

while (!$rsEscalaContrato->eof()) {
    $stFiltro = " WHERE cod_escala = ".$rsEscalaContrato->getCampo("cod_escala");
    $obTPontoEscala = new TPontoEscala();
    $obTPontoEscala->recuperaTodos($rsPontoEscala, $stFiltro);

    $stFiltro = " AND escala_turno.cod_escala = ".$rsEscalaContrato->getCampo("cod_escala");
    $obTPontoEscalaTurno = new TPontoEscalaTurno();
    $obTPontoEscalaTurno->recuperaTurnosAtivos($rsPontoEscalaTurno, $stFiltro);

    $obLista = new Lista;
    $obLista->setTitulo("Programação de Turnos - ".$rsPontoEscala->getCampo("descricao"));
    $obLista->setRecordSet($rsPontoEscalaTurno);
    $obLista->setMostraPaginacao( false );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Data");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Entrada1");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Saida1");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Entrada2");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Saida2");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Tipo");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "dt_turno" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "hora_entrada_1_formatado" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "hora_saida_1_formatado" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "hora_entrada_2_formatado" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "hora_saida_2_formatado" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "tipo_formatado" );
    $obLista->commitDado();

    $obLista->montaHTML();

    $obSpnProgramacaoTurnos = new Span;
    $obSpnProgramacaoTurnos->setId    ( "spnProgramacaoTurnos_".$inCodEscala );
    $obSpnProgramacaoTurnos->setValue ( $obLista->getHTML() );

    $obFormulario->addSpan         ( $obSpnProgramacaoTurnos );

    $rsEscalaContrato->proximo();
}
$obFormulario->defineBarra          ( array( $obBtnFechar ) , '', '');
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
