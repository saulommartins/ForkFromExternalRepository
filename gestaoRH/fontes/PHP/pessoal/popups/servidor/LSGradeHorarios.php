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
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalGradeHorario.class.php"                                   );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalFaixaTurno.class.php"                                     );

$obRPessoalGradeHorario = new RPessoalGradeHorario;
$obRPessoalFaixaTurno   = new RPessoalFaixaTurno( $obRPessoalGradeHorario );

$obRPessoalFaixaTurno->roRPessoalGradeHorario->setCodGrade( $_REQUEST["inCodGrade"] );
$obRPessoalFaixaTurno->listarFaixaTurno( $rsFaixaTurno, $boTransacao );

$arElementos = array();
while ( !$rsFaixaTurno->eof() ) {
    $arTmp = array();
    $arTmp['inId']           = count($arElementos) + 1;
    $arTmp['stHoraEntrada1'] = $rsFaixaTurno->getCampo("hora_entrada");
    $arTmp['stHoraSaida1']   = $rsFaixaTurno->getCampo("hora_saida");
    $arTmp['stHoraEntrada2'] = $rsFaixaTurno->getCampo("hora_entrada_2");
    $arTmp['stHoraSaida2']   = $rsFaixaTurno->getCampo("hora_saida_2");
    $arTmp['stNomDia']       = trim($rsFaixaTurno->getCampo("nom_dia"));
    $arTmp['inDiaSemana']    = $rsFaixaTurno->getCampo("cod_dia");
    $arElementos[]           = $arTmp;
    $rsFaixaTurno->proximo();
}

$rsRecordSet = new Recordset;
$rsRecordSet->preenche( $arElementos );
$rsRecordSet->ordena('inDiaSemana');
$rsRecordSet->setPrimeiroElemento();

$obLista = new Lista;
$obLista->setMostraPaginacao( false );
$obLista->setTitulo( "Turnos Cadastrados" );
$obLista->setRecordSet( $rsRecordSet );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Dia" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Entrada1" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Saída1" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Entrada2" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Saída2" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "stNomDia");
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "stHoraEntrada1");
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "stHoraSaida1" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "stHoraEntrada2");
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "stHoraSaida2" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();

$obLista->show();

$obFormulario = new Formulario;

$obBtnFechar = new Button;
$obBtnFechar->setName                 ( "Fechar"          );
$obBtnFechar->setValue                ( "Fechar"          );
$obBtnFechar->obEvento->setOnClick    ( "window.close();" );

$obFormulario->defineBarra              ( array( $obBtnFechar) , '', ''  );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
