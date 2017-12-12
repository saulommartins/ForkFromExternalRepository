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
* Definir grande de horário
* Data de Criação: 12/09/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @ignore

$Revision: 30882 $
$Name$
$Author: andre $
$Date: 2007-06-04 10:30:34 -0300 (Seg, 04 Jun 2007) $

* Casos de uso: uc-04.04.41
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalDiasTurno.class.php"                                     );
#include_once ( CAM_GRH_PES_NEGOCIO."RPessoalGradeHorario.class.php"                                     );
#include_once ( CAM_GRH_PES_NEGOCIO."RPessoalFaixaTurno.class.php"                                       );

$stPrograma = "ManterGrade";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stAcao          = $_REQUEST["stAcao"];
$stIdComponentes = "";

if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao($obRFolhaPagamentoPeriodoMovimentacao);

if ($stAcao == 'alterar') {
    $obRPessoalGradeHorario = new RPessoalGradeHorario;
    $obRPessoalFaixaTurno   = new RPessoalFaixaTurno( $obRPessoalGradeHorario );
    $inCodGrade     = $_GET['inCodGrade'];
    $stDescricao    = $_GET['stDescricao'];

    $obRPessoalFaixaTurno->roRPessoalGradeHorario->setCodGrade( $inCodGrade );
    $obRPessoalFaixaTurno->listarFaixaTurno( $rsFaixaTurno,$boTransacao );

    $arElementos = array();
    while ( !$rsFaixaTurno->eof() ) {
        $arTmp = array();
        $inId                    = count($arElementos) + 1;
        $arTmp['inId']           = $inId;
        $arTmp['stHoraEntrada1'] = $rsFaixaTurno->getCampo("hora_entrada");
        $arTmp['stHoraSaida1']   = $rsFaixaTurno->getCampo("hora_saida");
        $arTmp['stHoraEntrada2'] = $rsFaixaTurno->getCampo("hora_entrada_2");
        $arTmp['stHoraSaida2']   = $rsFaixaTurno->getCampo("hora_saida_2");
        $arTmp['stNomDia']       = trim($rsFaixaTurno->getCampo("nom_dia"));
        $arTmp['inDiaSemana']    = $rsFaixaTurno->getCampo("cod_dia");
        $arElementos[]           = $arTmp;
        $rsFaixaTurno->proximo();
    }
    Sessao::write('Grade', $arElementos);
    $jsOnload = "executaFuncaoAjax('montaGrade', '&stAcao=".$stAcao."');";
}

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCodGrade = new Hidden;
$obHdnCodGrade->setName                 ( "inCodGrade"                                      );
$obHdnCodGrade->setValue                ( $inCodGrade                                       );

$obHdnCodGradeFiltro = new Hidden;
$obHdnCodGradeFiltro->setName           ( "inCodGradeFiltro"                                );
$obHdnCodGradeFiltro->setValue          ( $_REQUEST['inCodGradeFiltro']                     );

$obHdnDescricaoFiltro = new Hidden;
$obHdnDescricaoFiltro->setName          ( "stDescricaoFiltro"                               );
$obHdnDescricaoFiltro->setValue         ( $_REQUEST['stDescricaoFiltro']                    );

$obTxtDescricao = new TextBox;
$obTxtDescricao->setRotulo              ( "Descrição"                                       );
$obTxtDescricao->setTitle               ( "Informe a descrição da grade de efetividade."    );
$obTxtDescricao->setName                ( "stDescricao"                                     );
$obTxtDescricao->setId                  ( "stDescricao"                                     );
$obTxtDescricao->setValue               ( $stDescricao                                      );
$obTxtDescricao->setSize                ( 40                                                );
$obTxtDescricao->setMaxLength           ( 80                                                );
$obTxtDescricao->setNull                ( false                                             );

$obTPessoalDiasTurno = new TPessoalDiasTurno();
$obTPessoalDiasTurno->recuperaTodos($rsDiasTurno);
$arCbx = array();

while (!$rsDiasTurno->eof()) {
    $obCBxDiaSemana = new CheckBox();
    $obCBxDiaSemana->setRotulo("Dia da Semana");
    $obCBxDiaSemana->setName("inDiaSemana_".$rsDiasTurno->getCampo("cod_dia"));
    $obCBxDiaSemana->setId("inDiaSemana-".$rsDiasTurno->getCampo("cod_dia"));
    $obCBxDiaSemana->setLabel($rsDiasTurno->getCampo("nom_dia")."<br>");
    $obCBxDiaSemana->setTitle("Informe o dia da semana.");
    $obCBxDiaSemana->setValue($rsDiasTurno->getCampo("cod_dia"));
    $obCBxDiaSemana->setChecked(true);
    $obCBxDiaSemana->setNullBarra(false);
    $stIdComponentes .= $obCBxDiaSemana->getId().",";

    $arCbx[] = $obCBxDiaSemana;
    $rsDiasTurno->proximo();
}

$obHrHoraEntrada1 = new Hora;
$obHrHoraEntrada1->setRotulo             ( "Hora Entrada1"                     );
$obHrHoraEntrada1->setTitle              ( "Informe o horário de entrada."     );
$obHrHoraEntrada1->setName               ( "stHoraEntrada1"                    );
$obHrHoraEntrada1->setId                 ( "stHoraEntrada1"                    );
$obHrHoraEntrada1->setValue              ( $stHoraEntrada1                     );
$obHrHoraEntrada1->setSize               ( 10                                  );
$obHrHoraEntrada1->setMaxLength          ( 10                                  );
$obHrHoraEntrada1->setNullBarra          ( false                               );
$stIdComponentes .= $obHrHoraEntrada1->getId().",";

$obHrHoraSaida1 = new Hora;
$obHrHoraSaida1->setRotulo               ( "Hora Saída1"                       );
$obHrHoraSaida1->setTitle                ( "Informe o horário de saída."       );
$obHrHoraSaida1->setName                 ( "stHoraSaida1"                      );
$obHrHoraSaida1->setId                   ( "stHoraSaida1"                      );
$obHrHoraSaida1->setValue                ( $stHoraSaida1                       );
$obHrHoraSaida1->setSize                 ( 10                                  );
$obHrHoraSaida1->setMaxLength            ( 10                                  );
$obHrHoraSaida1->setNullBarra            ( false                               );
$stIdComponentes .= $obHrHoraSaida1->getId().",";

$obHrHoraEntrada2 = new Hora;
$obHrHoraEntrada2->setRotulo             ( "Hora Entrada2"                     );
$obHrHoraEntrada2->setTitle              ( "Informe o horário de entrada."     );
$obHrHoraEntrada2->setName               ( "stHoraEntrada2"                    );
$obHrHoraEntrada2->setId                 ( "stHoraEntrada2"                    );
$obHrHoraEntrada2->setValue              ( $stHoraEntrada2                     );
$obHrHoraEntrada2->setSize               ( 10                                  );
$obHrHoraEntrada2->setMaxLength          ( 10                                  );
$stIdComponentes .= $obHrHoraEntrada2->getId().",";

$obHrHoraSaida2 = new Hora;
$obHrHoraSaida2->setRotulo               ( "Hora Saída2"                       );
$obHrHoraSaida2->setTitle                ( "Informe o horário de saída."       );
$obHrHoraSaida2->setName                 ( "stHoraSaida2"                      );
$obHrHoraSaida2->setId                   ( "stHoraSaida2"                      );
$obHrHoraSaida2->setValue                ( $stHoraSaida2                       );
$obHrHoraSaida2->setSize                 ( 10                                  );
$obHrHoraSaida2->setMaxLength            ( 10                                  );
$stIdComponentes .= $obHrHoraSaida2->getId();

$obSpnListaHorario = new Span;
$obSpnListaHorario->setID( "spnListaHorario" );

$arCamposTemp = array($obHrHoraEntrada1,$obHrHoraSaida1,$obHrHoraEntrada2,$obHrHoraSaida2);
$arCampos = array_merge($arCamposTemp, $arCbx);

//Monta FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm                   );
$obFormulario->addTitulo        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden        ( $obHdnCtrl                );
$obFormulario->addHidden        ( $obHdnAcao                );
$obFormulario->addHidden        ( $obHdnCodGrade            );
$obFormulario->addHidden        ( $obHdnCodGradeFiltro      );
$obFormulario->addHidden        ( $obHdnDescricaoFiltro     );
$obFormulario->addTitulo        ( "Dados da Grade"          );
$obFormulario->addComponente    ( $obTxtDescricao           );
$obFormulario->addTitulo        ( "Turno"                   );
$obFormulario->agrupaComponentes( $arCbx                    );
$obFormulario->addComponente    ( $obHrHoraEntrada1         );
$obFormulario->addComponente    ( $obHrHoraSaida1           );
$obFormulario->addComponente    ( $obHrHoraEntrada2         );
$obFormulario->addComponente    ( $obHrHoraSaida2           );
$obFormulario->IncluirAlterar   ( "Grade", $arCampos, false  );
$obFormulario->addSpan          ( $obSpnListaHorario        );

if ($stAcao == "incluir") {
    $obFormulario->OK();
} else {
    $stFiltro = "&inCodGrade=".$_REQUEST['inCodGradeFiltro']."&stDescricao=".$_REQUEST['stDescricaoFiltro'];
    $obFormulario->Cancelar( $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro );
}
$obFormulario->show();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
