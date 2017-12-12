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
* Página de CO
* Data de Criação: 06/04/2005

* @author Analista: ???
* @author Desenvolvedor: Marcelo Boezzio Paulino

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
include_once ( CAM_GRH_CON_NEGOCIO."RConcurso.class.php"       );

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

$obRConcurso         = new RConcurso;
$rsCargosDisponiveis = $rsCargosSelecionados = new RecordSet;

$arCodConcurso = preg_split( "/[^a-zA-Z0-9]/", $_GET['inCodConcurso'] );
$obRConcurso->setNumero   ( $arCodConcurso[0] );
$obRConcurso->setExercicio( $arCodConcurso[1] );
$obRConcurso->consultar( $rsConcurso, $rsAvaliacao, $rsCargo );

//recupera dados do CONCURSO
$stNorma       = $rsConcurso->getCampo( 'cod_norma' )." - ".$rsConcurso->getCampo( 'nom_norma' );
$inCodConcurso = $rsConcurso->getCampo( 'cod_concurso' );
//$nuEdital      = $rsConcurso->getCampo( 'nr_edital' );
$dtPublicacao  = $rsConcurso->getCampo( 'dt_publicacao' );
$dtAplicacao   = $rsConcurso->getCampo( 'dt_aplicacao' );
$dtProrrogacao = $rsConcurso->getCampo( 'dt_prorrogacao' );

if ( empty( $dtProrrogacao ) ) {

    switch ($stAcao) {
        case 'prorrogar':
            $dtProrrogacao = strftime("%d/%m/%Y", strtotime( $dtAplicacao . " +2 year" ));
        break;

        case 'consultar':
            $dtProrrogacao = "&nbsp";
        break;
    }

    $boProrrogado = false;
} else {
      $boProrrogado  = true;
  }

$nuNotaMinima  = $rsConcurso->getCampo( 'nota_minima' );

//recupera AVALIAÇÃO do CONCURSO
while ( !$rsAvaliacao->eof() ) {
    if ( $rsAvaliacao->getCampo( 'cod_avaliacao' ) != '3' ) {
        $stTipoAvaliacao = $rsAvaliacao->getCampo( 'descricao' );
    } else {
        $boTitulacao = true;
    }
    $rsAvaliacao->proximo();
}

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//

//Instancia o formulário
$obForm = new Form;

//modifica ação do formulário para habilitar o botão de voltar.
if ($stAcao == "consultar") {
    $obForm->setAction( $pgList );
    $obForm->setTarget( "telaPrincipal" );
 } else {
      $obForm->setAction( $pgProc );
      $obForm->setTarget( "oculto" );
   }

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

//Define o objeto HIDDEN para armazenar o CODIGO DO CONCURSO
$obHdnCodConcurso = new Hidden;
$obHdnCodConcurso->setName ( "inCodConcurso" );
$obHdnCodConcurso->setValue( $_GET['inCodConcurso'] );

$obLblNorma = new Label;
$obLblNorma->setName  ( "stNorma" );
$obLblNorma->setValue ( $stNorma );
$obLblNorma->setRotulo( "Norma" );

$obLblCodConcurso = new Label;
$obLblCodConcurso->setName  ( "inCodConcurso" );
$obLblCodConcurso->setValue ( $inCodConcurso );
$obLblCodConcurso->setRotulo( "Edital" );

//$obLblEdital = new Label;
//$obLblEdital->setName  ( "nuEditall" );
//$obLblEdital->setValue ( $nuEdital );
//$obLblEdital->setRotulo( "Edital" );

$obLblDtPublicacao = new Label;
$obLblDtPublicacao->setName  ( "dtPublicacao" );
$obLblDtPublicacao->setValue ( $dtPublicacao );
$obLblDtPublicacao->setRotulo( "Data de publicação" );

$obLblDtAplicacao = new Label;
$obLblDtAplicacao->setName  ( "dtAplicacao" );
$obLblDtAplicacao->setValue ( $dtAplicacao );
$obLblDtAplicacao->setRotulo( "Data de aplicação" );

$obLblDtProrrogacao = new Label;
$obLblDtProrrogacao->setName  ( "dtProrrogacao" );
$obLblDtProrrogacao->setValue ( $dtProrrogacao );
$obLblDtProrrogacao->setRotulo( "Data de prorrogação" );

//Define o objeto TEXT para armazenar a DATA PRORROGAÇÂO
$obTxtDtProrrogacao = new Data;
$obTxtDtProrrogacao->setName     ( "dtProrrogacao" );
$obTxtDtProrrogacao->setValue    ( $dtProrrogacao  );
$obTxtDtProrrogacao->setRotulo   ( "Data de Prorrogação" );
$obTxtDtProrrogacao->setNull     ( false );
$obTxtDtProrrogacao->setTitle    ( 'Data da Prorrogação' );

$obLblNotaMinima = new Label;
$obLblNotaMinima->setName  ( "stNotaMinima" );
$obLblNotaMinima->setValue ( $nuNotaMinima );
$obLblNotaMinima->setRotulo( "Nota mínima" );

$obLblTipoAvaliacao = new Label;
$obLblTipoAvaliacao->setName  ( "stTipoAvaliacao" );
$obLblTipoAvaliacao->setValue ( $stTipoAvaliacao );
$obLblTipoAvaliacao->setRotulo( "Tipo de prova" );

$obChkTitulacao = new CheckBox;
$obChkTitulacao->setRotulo     ( "Avalia Titulação"   );
$obChkTitulacao->setName       ( "boAvaliaTitulacao"  );
$obChkTitulacao->setValue      ( '3' );
$obChkTitulacao->setLabel      ( "Sim" );
$obChkTitulacao->setDisabled   ( true );
$obChkTitulacao->setChecked    ( ($boTitulacao == true) );

$obCmbCargos = new Select;
$obCmbCargos->setName      ( "stCargos"     );
$obCmbCargos->setRotulo    ( "Cargos"       );
$obCmbCargos->setStyle     ( "width: 250px" );
$obCmbCargos->setSize      ( "10"           );
$obCmbCargos->setCampoId   ( "cod_cargo"    );
$obCmbCargos->setCampoDesc ( "descricao"    );
$obCmbCargos->preencheCombo( $rsCargo       );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden( $obHdnCtrl                    );
$obFormulario->addHidden( $obHdnAcao                    );
$obFormulario->addHidden( $obHdnCodConcurso             );

$obFormulario->addTitulo( "Dados do concurso".Sessao::getEntidade().""           );

$obFormulario->addComponente( $obLblNorma               );
$obFormulario->addComponente( $obLblCodConcurso         );
//$obFormulario->addComponente( $obLblEdital              );
$obFormulario->addComponente( $obLblDtPublicacao        );
$obFormulario->addComponente( $obLblDtAplicacao         );

//Mostra o Campo de prorrogação, caso o concurso tenha que ser prorrogado.
if ($stAcao == 'prorrogar'  && !$boProrrogado) {
    $obFormulario->addComponente( $obTxtDtProrrogacao );
} else {
      $obFormulario->addComponente( $obLblDtProrrogacao       );
  }

$obFormulario->addComponente( $obLblNotaMinima          );

if ($stAcao != 'prorrogar') {

    $obFormulario->addTitulo( "Avaliação"                   );
    $obFormulario->addComponente( $obLblTipoAvaliacao           );
    $obFormulario->addComponente( $obChkTitulacao           );

    $obFormulario->addTitulo( "Cargos"          );
    $obFormulario->addComponente( $obCmbCargos              );
}

if ($stAcao == "consultar") {

    $obBtnVoltar = new Button;
    $obBtnVoltar->setName( "btnVoltar" );
    $obBtnVoltar->setValue( "Voltar" );
    $obBtnVoltar->setTipo( "submit" );
    $obBtnVoltar->obEvento->setOnClick ( "history.back(-1);" );

    $obFormulario->addLinha();
    $obFormulario->ultimaLinha->addCelula();
    $obFormulario->ultimaLinha->ultimaCelula->setColSpan( 2 );
    $obFormulario->ultimaLinha->ultimaCelula->setClass( "fieldleft" );
    //$obFormulario->ultimaLinha->ultimaCelula->addComponente( $obBtnAdicionarParametro );
    $obFormulario->ultimaLinha->ultimaCelula->addComponente( $obBtnVoltar   );
    $obFormulario->ultimaLinha->commitCelula();
    $obFormulario->commitLinha();
}

if ($stAcao == "incluir") {
    $obFormulario->OK();
 } elseif ($stAcao != "consultar") {
    $obFormulario->Cancelar();
}

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
