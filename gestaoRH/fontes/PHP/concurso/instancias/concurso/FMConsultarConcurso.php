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
* Página de Formulario de Consulta de Concurso
* Data de Criação: 06/04/2005

* @author Analista: ???
* @author Desenvolvedor: João Rafael Tissot

* @package URBEM
* @subpackage

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

* Casos de uso: uc-04.01.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_CON_NEGOCIO."RConcursoConcurso.class.php"       );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConcurso";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$obRConcursoConcurso         = new RConcursoConcurso;
$rsCargosSelecionados = $rsNormas =  new RecordSet;

$obRConcursoConcurso->setCodEdital($inCodEdital);
$obRConcursoConcurso->consultarConcurso( $rsConcurso , $rsCargosSelecionados );
$stEdital           = $rsConcurso->getCampo( 'cod_edital');
$stNorma            = $rsConcurso->getCampo( 'cod_norma' );
$dtAplicacao        = $rsConcurso->getCampo( 'dt_aplicacao' );
$dtProrrogacao      = $rsConcurso->getCampo( 'dt_prorrogacao' );
$nuNotaMinima       = $rsConcurso->getCampo( 'nota_minima' );
$inMesesValidade    = $rsConcurso->getCampo( 'meses_validade' );
$inAvaliaTitulacao  = $rsConcurso->getCampo( 'avalia_titulacao' );
$inTipoProva        = $rsConcurso->getCampo( 'tipo_prova' );

$obRConcursoConcurso->obRNorma->setCodNorma($stEdital);
$obRConcursoConcurso->obRNorma->listar($rsNormas,"");
$stNomeEdital = $rsNormas->getCampo("nom_norma");

$obRConcursoConcurso->obRNorma->setCodNorma($stNorma);
$obRConcursoConcurso->obRNorma->listar($rsNormas,"");
$stNomeNorma = $rsNormas->getCampo("nom_norma");

$arChaveAtributoConcurso =  array( "cod_edital"    => $_REQUEST["inCodEdital"] );

$obRConcursoConcurso->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoConcurso);
$obRConcursoConcurso->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );

//Instancia o formulário
$obForm = new Form;

$obForm->setAction( $pgList );
$obForm->setTarget( "telaPrincipal" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

//Define o objeto TEXT para armazenar a DATA PRORROGAÇÂO

//Edital
$obLblEdital = new Label;
$obLblEdital->setRotulo ( "Edital"   );
$obLblEdital->setName   ( "stlblLabelEdital" );
$obLblEdital->setValue  ( $stEdital." - ".$stNomeEdital      );

$obLblNorma = new Label;
$obLblNorma->setRotulo ( "Norma"   );
$obLblNorma->setName   ( "stlblLabelNorma" );
$obLblNorma->setValue  ( $stNorma." - ".$stNomeNorma      );

$obLbldtAplicacao= new Label;
$obLbldtAplicacao->setRotulo ( "Data de Aplicação"   );
$obLbldtAplicacao->setName   ( "stlblLabeldtAplicacao" );
$obLbldtAplicacao->setValue  ( $dtAplicacao      );

$obLbldtProrrogacao= new Label;
$obLbldtProrrogacao->setRotulo ( "Data de Prorrogação"   );
$obLbldtProrrogacao->setName   ( "stlblLabeldtProrrogacao" );
$obLbldtProrrogacao->setValue  ( $dtProrrogacao      );

$obLblNotaMinima= new Label;
$obLblNotaMinima->setRotulo ( "Nota Mínima"   );
$obLblNotaMinima->setName   ( "stlblLabelNotaMinima" );
$obLblNotaMinima->setValue  ( $nuNotaMinima      );

$obLblMesesValidade= new Label;
$obLblMesesValidade->setRotulo ( "Validade do Concurso"   );
$obLblMesesValidade->setName   ( "stlblLabelMesesValidade" );
$obLblMesesValidade->setValue  ( $inMesesValidade      );

$obLblAvaliaTitulacao= new Label;
$obLblAvaliaTitulacao->setRotulo ( "Avalia Titulação"   );
$obLblAvaliaTitulacao->setName   ( "stlblLabelAvaliaTitulacao" );
if ($inAvaliaTitulacao) {
    $inAvaliaTitulacao = "Sim";
} else {
    $inAvaliaTitulacao = "Não";
}
$obLblAvaliaTitulacao->setValue  ( $inAvaliaTitulacao      );

$obLblTipoProva= new Label;
$obLblTipoProva->setRotulo ( "Tipo de Prova"   );
$obLblTipoProva->setName   ( "stlblLabelTipoProva" );
if ($inTipoProva==1) {
    $inTipoProva = "Teórico";
} else {
    $inTipoProva = "Teórico / Prático";
}
$obLblTipoProva->setValue  ( $inTipoProva      );

$obCmbCargos = new Select;
$obCmbCargos->setName      ( "stCargos"     );
$obCmbCargos->setRotulo    ( "Cargos"       );
$obCmbCargos->setStyle     ( "width: 250px" );
$obCmbCargos->setSize      ( "10"           );
$obCmbCargos->setCampoId   ( "cod_cargo"    );
$obCmbCargos->setCampoDesc ( "descricao"    );
$obCmbCargos->preencheCombo( $rsCargosSelecionados       );

// atributos sendo setados no objeto para depois serem inseridos no formulario.
$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo     ( "Atributos"  );
$obMontaAtributos->setName       ( "Atributos_" );
$obMontaAtributos->setLabel      ( true         );
$obMontaAtributos->setRecordSet  ( $rsAtributos );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnCtrl                    );
$obFormulario->addHidden( $obHdnAcao                    );
$obFormulario->addTitulo( "Dados do concurso".Sessao::getEntidade().""           );
$obFormulario->addComponente( $obLblEdital);
$obFormulario->addComponente( $obLblNorma);
$obFormulario->addComponente( $obLbldtAplicacao);
$obFormulario->addComponente( $obLbldtProrrogacao);
$obFormulario->addComponente( $obLblNotaMinima);
$obFormulario->addComponente( $obLblMesesValidade);
$obFormulario->addComponente( $obLblAvaliaTitulacao);
$obFormulario->addComponente( $obLblTipoProva);
$obFormulario->addTitulo( "Cargos"          );
$obFormulario->addComponente( $obCmbCargos              );
$obMontaAtributos->geraFormulario( $obFormulario      );

$obBtnVoltar = new Button;
$obBtnVoltar->setName( "btnVoltar" );
$obBtnVoltar->setValue( "Voltar" );
$obBtnVoltar->setTipo( "submit" );
$obBtnVoltar->obEvento->setOnClick ( "history.back(-1);" );
$obFormulario->addLinha();
$obFormulario->ultimaLinha->addCelula();
$obFormulario->ultimaLinha->ultimaCelula->setColSpan( 2 );
$obFormulario->ultimaLinha->ultimaCelula->setClass( "fieldleft" );
$obFormulario->ultimaLinha->ultimaCelula->addComponente( $obBtnVoltar   );
$obFormulario->ultimaLinha->commitCelula();
$obFormulario->commitLinha();

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
