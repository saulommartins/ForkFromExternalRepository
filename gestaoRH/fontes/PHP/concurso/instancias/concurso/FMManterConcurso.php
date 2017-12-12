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
* Página de Formulario de Inclusao/Alteracao de Concurso
* Data de Criação: 29/06/2004

* @author Analista: ???
* @author Desenvolvedor: João Rafael Tissot

* @package URBEM
* @subpackage

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-13 09:55:05 -0300 (Qua, 13 Jun 2007) $

* Casos de uso: uc-04.01.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_CON_NEGOCIO."RConcursoConcurso.class.php"       );
include_once ( CAM_GRH_CON_NEGOCIO."RConcursoCandidato.class.php"      );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConcurso";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once($pgJS);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$obRConcursoNorma = $obRConcursoConcurso = $obRConcursoNormaEdital = new RConcursoConcurso;
$obRConcursoCandidato = new RConcursoCandidato;
$rsConcursoHomologacao = $rsConcursoCandidato = $rsCargosSelecionados = $rsConcurso = $rsAvaliacao = $rsCargosSelecionados = new RecordSet;
$rsTipoNorma = $rsNormaEdital = $rsCargosDisponiveis = new RecordSet;

$inCodEdital = $request->get('inCodEdital');
$obRConcursoCandidato->obRConcursoConcurso->setCodEdital( $inCodEdital );
if ($stAcao == "alterar") {
    $obRConcursoCandidato->listarCandidatoPorEdital( $rsConcursoCandidato );
}

$obRConcursoConcurso->recuperaConfiguracao( $arConfiguracao );
foreach ($arConfiguracao as $key => $valor) {
    if ( $key == 'mascara_concurso'.Sessao::getEntidade() ) {
        $stMascaraConcurso = $valor;
    }
    if ( $key == 'mascara_nota'.Sessao::getEntidade() ) {
        $stMascaraNota = $valor;
    }
    if ( $key == 'tipo_portaria_edital'.Sessao::getEntidade() ) {
        $inTipoNormaEdital = $valor;
    }
}

// mostra atributos selecionados
if ($stAcao == "incluir") {
    $obRConcursoConcurso->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );
} else {
    $arChaveAtributoConcurso =  array( "cod_edital"    => $inCodEdital );
    $obRConcursoConcurso->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoConcurso);
    $obRConcursoConcurso->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );
}

if ($stAcao == "alterar") {
    $obRConcursoConcurso->setCodEdital($inCodEdital);
    $obRConcursoConcurso->consultarConcurso( $rsConcurso, $rsCargosSelecionados );

    if ( !$rsConcurso->eof() ) {
    $obRConcursoConcurso->obRNorma->setCodNorma   ( $rsConcurso->getCampo( 'cod_norma'           ));
    $obRConcursoConcurso->setCodEdital            ( $rsConcurso->getCampo( 'cod_edital'          ));
    $obRConcursoConcurso->setAplicacao            ( $rsConcurso->getCampo( 'dt_aplicacao'        ));
    $obRConcursoConcurso->setProrrogacao          ( $rsConcurso->getCampo( 'dt_prorrogacao'      ));
        $obRConcursoConcurso->setCodEditalHomologacao ( $rsConcurso->getCampo( 'stEditalHomologacao' ));
    $obRConcursoConcurso->setNotaMinima           ( $rsConcurso->getCampo( 'nota_minima'         ));
    $obRConcursoConcurso->setAvaliaTitulacao      ( $rsConcurso->getCampo( 'avalia_titulacao'    ));
        $obRConcursoConcurso->setTipoProva            ( $rsConcurso->getCampo( 'tipo_prova'          ));
    $obRConcursoConcurso->setMesesValidade	      ( $rsConcurso->getCampo( 'meses_validade'      ));

        $obRConcursoConcurso->obRNorma->consultar( $rsNorma );
        $inCodTipoNorma = $obRConcursoConcurso->obRNorma->obRTipoNorma->getCodTipoNorma();
    $stTipoNorma = $inCodTipoNorma;

        $obRConcursoNorma->obRNorma->obRTipoNorma->setCodTipoNorma( $inCodTipoNorma );
        $obRConcursoNorma->obRNorma->listar( $rsNorma );

    $stNorma = $inCodNorma = $obRConcursoConcurso->obRNorma->getCodNorma();

    $stEdital = $nuEdital = $obRConcursoConcurso->getCodEdital();
        $boAvaliaTitulacao = $obRConcursoConcurso->getAvaliaTitulacao();
        $boTipoProva = $obRConcursoConcurso->getTipoProva();

    $obRConcursoNormaEdital->obRNorma->setCodNorma($stEdital);
    $obRConcursoNormaEdital->obRNorma->listar($rsEdital);

    $stLinkEdital    = $rsEdital->getCampo("link");
    $dtPublicacao = $rsEdital->getCampo("dt_publicacao");

    $stLinkNormaRegulamentadora = $obRConcursoConcurso->obRNorma->getUrl();

        $obRConcursoConcurso->consultarConcursoHomologacao( $rsConcursoHomologacao );
    }
}

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao  );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( ""       );

//Define o objeto da ação stAcao
$obHdnExercicio = new Hidden;
$obHdnExercicio->setName ( "inExercicio" );
$obHdnExercicio->setValue( $request->get('inExercicio')  );

// combo para os editais
$obRConcursoConcurso->listarEditais($inTipoNormaEdital,$rsNormaEdital);

if ($stAcao == 'incluir') {
    $obTxtEdital = new TextBox;
    $obTxtEdital->setName     ( "nuEdital"          );
    $obTxtEdital->setValue    ( $nuEdital           );
    $obTxtEdital->setRotulo   ( "*Edital de abertura");
    $obTxtEdital->setSize     ( 5                   );
    $obTxtEdital->setMaxLength( 5                   );
    $obTxtEdital->setNull     ( false               );
    $obTxtEdital->setInteiro  ( true                );
    $obTxtEdital->setTitle    ( 'Selecione o edital do concurso.'  );
    $obTxtEdital->obEvento->setOnChange("buscaValor('buscaLinkEdital');");

    $obCmbEdital = new Select;
    $obCmbEdital->setName       ( "stEdital"              );
    $obCmbEdital->setRotulo     ( "*Edital de abertura"    );
    $obCmbEdital->setStyle      ( "width: 250px"          );
    $obCmbEdital->setCampoId    ( "cod_norma"             );
    $obCmbEdital->setCampoDesc  ( "nom_norma"             );
    $obCmbEdital->addOption     ( "","Selecione o Edital" );
    $obCmbEdital->preencheCombo ( $rsNormaEdital          );
    $obCmbEdital->setValue      ( $stEdital               );
    $obCmbEdital->setNull       ( true                    );
    $obCmbEdital->setTitle      ( ""                	  );
    $obCmbEdital->obEvento->setOnChange("buscaValor('buscaLinkEdital');");
} else {
    $obLblEdital = new Label;
    $obLblEdital->setRotulo       ( 'Edital de abertura');
    $obLblEdital->setName         ( 'stEditalLabel'     );
    $obLblEdital->setValue        ( $nuEdital.'/'. $rsConcurso->getCampo("ano_publicacao") .' - '. $rsConcurso->getCampo("nom_norma")   );

    $obHdnEdital = new Hidden;
    $obHdnEdital->setName ( "stEdital" );
    $obHdnEdital->setValue( $nuEdital  );
}

if ( $rsConcursoCandidato->getNumLinhas() != -1 ) {
    $obRConcursoNormaEdital->obRNorma->obRTipoNorma->setCodTipoNorma( $stTipoNorma );
    $obRConcursoNormaEdital->obRNorma->obRTipoNorma->consultar($rsNormas);
    $obLblTipoNorma = new Label;
    $obLblTipoNorma->setRotulo       ( 'Tipo da Norma Regulamentadora'  );
    $obLblTipoNorma->setName         ( 'inCodigoTipoNorma'              );
    $obLblTipoNorma->setValue        ( $inCodTipoNorma .' - '.$rsNormas->getCampo( 'nom_tipo_norma' ) );
} else {
    // Define o objeto TEXT para armazenar o CODIGO DO TIPO DE NORMA
    $obTxtCodTipoNorma = new TextBox;
    $obTxtCodTipoNorma->setName     ( "inCodigoTipoNorma"             );
    $obTxtCodTipoNorma->setValue    ( $inCodTipoNorma                 );
    $obTxtCodTipoNorma->setRotulo   ( "Tipo da Norma Regulamentadora" );
    $obTxtCodTipoNorma->setSize     ( 5                               );
    $obTxtCodTipoNorma->setMaxLength( 5                               );
    $obTxtCodTipoNorma->setNull     ( false                           );
    $obTxtCodTipoNorma->setTitle    ( 'Norma regulamentadora do concurso.'    );
    $obTxtCodTipoNorma->obEvento->setOnChange("buscaValor('buscaNormaDesteTipo');");

    // combo do tipo de normas
    $obRConcursoNorma->obRTipoNorma->listarTodos( $rsTipoNorma );
    $obCmbTipoNorma = new Select;
    $obCmbTipoNorma->setName      ( "stTipoNorma"                   );
    $obCmbTipoNorma->setRotulo    ( "Norma Regulamentadora"    );
    $obCmbTipoNorma->setStyle     ( "width: 250px"                  );
    $obCmbTipoNorma->setCampoId   ( "cod_tipo_norma"                );
    $obCmbTipoNorma->setCampoDesc ( "nom_tipo_norma"                );
    $obCmbTipoNorma->addOption    ( "", "Selecione o tipo de Norma" );
    $obCmbTipoNorma->preencheCombo( $rsTipoNorma                    );
    $obCmbTipoNorma->setValue     ( $stTipoNorma                    );
    $obCmbTipoNorma->setNull      ( false                           );
    $obCmbTipoNorma->setTitle     ( ''     );
    $obCmbTipoNorma->obEvento->setOnChange("buscaValor('buscaNormaDesteTipo');");
}

if ( $rsConcursoCandidato->getNumLinhas() != -1 ) {
    $obRConcursoConcurso->obRNorma->setCodNorma( $inCodNorma );
    $obRConcursoConcurso->obRNorma->consultar( $rsNorma );
    $obLblNorma = new Label;
    $obLblNorma->setRotulo       ( 'Norma Regulamentadora'  );
    $obLblNorma->setName         ( 'inCodigoNorma'          );
    $obLblNorma->setValue        ( $inCodNorma .' - '.$rsNorma->getCampo('nom_norma')  );
} else {
    // Define o objeto TEXT para armazenar o CODIGO  DE NORMA
    $obTxtCodNorma = new TextBox;
    $obTxtCodNorma->setName     ( "inCodigoNorma"         );
    $obTxtCodNorma->setValue    ( $inCodNorma             );
    $obTxtCodNorma->setRotulo   ( "Norma Regulamentadora" );
    $obTxtCodNorma->setSize     ( 5                       );
    $obTxtCodNorma->setMaxLength( 5                       );
    $obTxtCodNorma->setNull     ( false                   );
    $obTxtCodNorma->setTitle    ( 'Norma regulamentadora do concurso.'      );

    // combo para as normas
    $obCmbNorma = new Select;
    $obCmbNorma->setName      ( "stNorma"               );
    $obCmbNorma->setRotulo    ( "Norma Regulamentadora" );
    $obCmbNorma->setStyle     ( "width: 250px"          );
    $obCmbNorma->setCampoId   ( "cod_norma"             );
    $obCmbNorma->setCampoDesc ( "nom_norma"             );
    $obCmbNorma->addOption    ( "", "Selecione a Norma" );
    if ($stAcao == "alterar") {
        $obCmbNorma->preencheCombo($rsNorma);
    }
    $obCmbNorma->setValue     ( $stNorma                );
    $obCmbNorma->setNull      ( false                   );
    $obCmbNorma->setTitle     ( ''     );
    $obCmbNorma->obEvento->setOnChange("buscaValor('buscaLinkNorma');");
}

$obLblDtPublicacao = new Label;
$obLblDtPublicacao->setRotulo       ( 'Data de publicação'     );
$obLblDtPublicacao->setName         ( 'lbldtPublicacao'        );
$obLblDtPublicacao->setId           ( 'lbldtPublicacao'        );
$obLblDtPublicacao->setValue        ( $dtPublicacao            );

//Define o objeto da data publicação
$obHdnDtPublicacao = new Hidden;
$obHdnDtPublicacao->setName ( "dtPublicacao" );
$obHdnDtPublicacao->setValue( $dtPublicacao  );

if ( $rsConcursoCandidato->getNumLinhas() != -1 ) {

    $obLblEditalHomologacao = new Label;
    $obLblEditalHomologacao->setRotulo       ( 'Edital de homologação' );
    $obLblEditalHomologacao->setName         ( 'stEditalHomologacao'   );
    $obLblEditalHomologacao->setValue        ( $rsConcursoHomologacao->getCampo('cod_homologacao').'/'. $rsConcursoHomologacao->getCampo("ano_publicacao") .' - '. $rsConcursoHomologacao->getCampo("nom_norma")   );

    $obLblDtHomologacao = new Label;
    $obLblDtHomologacao->setRotulo       ( 'Data de publicação'    );
    $obLblDtHomologacao->setName         ( 'dtHomologacao'          );
    $obLblDtHomologacao->setValue        ( $rsConcursoHomologacao->getCampo("dt_publicacao") );

} else {
    ////Define o objeto TEXT para armazenar o NUMERO DO EDITAL DE HOMOLOGAÇÃO
    $obTxtEditalHomologacao = new TextBox;
    $obTxtEditalHomologacao->setName     ( "nuEditalHomologacao"            );
    $obTxtEditalHomologacao->setValue    ( $rsConcursoHomologacao->getCampo('cod_homologacao') );
    $obTxtEditalHomologacao->setRotulo   ( "Edital de homologação"          );
    $obTxtEditalHomologacao->setSize     ( 5                                );
    $obTxtEditalHomologacao->setMaxLength( 5                                );
    $obTxtEditalHomologacao->setNull     ( true                             );
    $obTxtEditalHomologacao->setInteiro  ( true                             );
    $obTxtEditalHomologacao->setTitle    ( 'Selecione o edital do concurso.' );

    $obCmbEditalHomologacao = new Select;
    $obCmbEditalHomologacao->setName       ( "stEditalHomologacao"          );
    $obCmbEditalHomologacao->setRotulo     ( "Edital de homologação"        );
    $obCmbEditalHomologacao->setStyle      ( "width: 250px"                 );
    $obCmbEditalHomologacao->setCampoId    ( "cod_norma"                    );
    $obCmbEditalHomologacao->setCampoDesc  ( "nom_norma"                    );
    $obCmbEditalHomologacao->addOption     ( "","Selecione o Edital de homologação" );
    $obCmbEditalHomologacao->preencheCombo ( $rsNormaEdital                 );
    $obCmbEditalHomologacao->setValue      ( $rsConcursoHomologacao->getCampo('cod_homologacao') );
    $obCmbEditalHomologacao->setNull       ( true                           );
    $obCmbEditalHomologacao->setTitle      ( ""                );
    $obCmbEditalHomologacao->obEvento->setOnChange("buscaValor('buscaLinkEditalHomologacao');");

    //Define objeto DATA para armazenar a DATA DE Homologação
    $obLblDtHomologacao = new Label;
    $obLblDtHomologacao->setRotulo       ( 'Data de publicação'     );
    $obLblDtHomologacao->setName         ( 'lbldtHomologacao'          );
    $obLblDtHomologacao->setId           ( 'lbldtHomologacao'          );
    $obLblDtHomologacao->setValue        ( $rsConcursoHomologacao->getCampo('dt_publicacao') );

    $obHdnDtHomologacao = new Hidden;
    $obHdnDtHomologacao->setName ( "dtHomologacao" );
    $obHdnDtHomologacao->setValue( $rsConcursoHomologacao->getCampo('dt_publicacao')  );
}

if ( $rsConcursoCandidato->getNumLinhas() != -1 ) {
    $obLblDtAplicacao = new Label;
    $obLblDtAplicacao->setRotulo       ( 'Data de aplicação'      );
    $obLblDtAplicacao->setName         ( 'dtAplicacacao'          );
    $obLblDtAplicacao->setValue        ( $obRConcursoConcurso->getAplicacao() );
} else {
    //Define objeto DATA para armazenar a DATA DE Aplicação
    $obTxtDtAplicacao = new Data;
    $obTxtDtAplicacao->setName  ( "dtAplicacacao"                     );
    $obTxtDtAplicacao->setValue ( $obRConcursoConcurso->getAplicacao());
    $obTxtDtAplicacao->setRotulo( "Data de aplicação"                 );
    $obTxtDtAplicacao->setNull  ( false                               );
    $obTxtDtAplicacao->setTitle ( 'Data de aplicação do concurso.'    );
}

if ( $rsConcursoCandidato->getNumLinhas() != -1 ) {
    $obLblValidade = new Label;
    $obLblValidade->setRotulo       ( 'Validade do Concurso'      );
    $obLblValidade->setName         ( 'inMesesValidade'           );
    $obLblValidade->setValue        ( $obRConcursoConcurso->getMesesValidade() );
} else {
    $obTxtValidade = new TextBox;
    $obTxtValidade->setName     ( "inMesesValidade"                       );
    $obTxtValidade->setValue    ( $obRConcursoConcurso->getMesesValidade());
    $obTxtValidade->setRotulo   ( "Validade do Concurso"                  );
    $obTxtValidade->setSize     ( 10                                      );
    $obTxtValidade->setMaxLength( 2                                      );
    $obTxtValidade->setNull     ( false                                   );
    $obTxtValidade->setTitle    ( 'Validade do concurso.em meses'                              );
}

if ( $rsConcursoCandidato->getNumLinhas() != -1 ) {
    $obLblNotaMinima = new Label;
    $obLblNotaMinima->setRotulo       ( 'Nota mínima'               );
    $obLblNotaMinima->setName         ( 'nuNotaMinima'              );
    $obLblNotaMinima->setValue        ( $obRConcursoConcurso->getNotaMinima() );
} else {
    //Define o objeto TEXT para armazenar a NOTA MINIMA
    $obTxtNotaMinima = new TextBox;
    $obTxtNotaMinima->setName     ( "nuNotaMinima"                       );
    $obTxtNotaMinima->setValue    ( $obRConcursoConcurso->getNotaMinima());
    $obTxtNotaMinima->setRotulo   ( "Nota mínima"                        );
    $obTxtNotaMinima->setSize     ( 10                                   );
    $obTxtNotaMinima->setNull     ( false                                );
    $obTxtNotaMinima->setTitle    ( 'Nota mínima para aprovação'         );
    $obTxtNotaMinima->setMascara  ( $stMascaraNota                       );
    $obTxtNotaMinima->obEvento->setOnChange("buscaValor('validaCampo');" );
}

if ( $rsConcursoCandidato->getNumLinhas() != -1 ) {
    $obLblTeorico = new Label;
    $obLblTeorico->setRotulo       ( 'Tipo de Prova'               );
    $obLblTeorico->setName         ( 'boTipoProva'                 );
    if ($boTipoProva == 1) {
        $obLblTeorico->setValue    ( 'Teórico'                     );
    } else {
        $obLblTeorico->setValue    ( 'Teórico/Prático'             );
    }
} else {
    //Define objetos RADIO para armazenar o TIPO DA AVALIACAO
    $obRdbTeorico = new Radio;
    $obRdbTeorico->setRotulo           ( "Tipo de Prova"                      );
    $obRdbTeorico->setName             ( "boTipoProva"                        );
    $obRdbTeorico->setValue            ( "1"                                  );
    $obRdbTeorico->setLabel            ( "Teórico"                            );
    $obRdbTeorico->setChecked          (($boTipoProva == '1' or !$boTipoProva));
    $obRdbTeorico->setNull             ( false                                );

    $obRdbTeoricoPratico = new Radio;
    $obRdbTeoricoPratico->setRotulo    ( "Tipo de Prova"     );
    $obRdbTeoricoPratico->setName      ( "boTipoProva"       );
    $obRdbTeoricoPratico->setValue     ( "2"                 );
    $obRdbTeoricoPratico->setLabel     ( "Teórico/Prático"   );
    $obRdbTeoricoPratico->setChecked   (($boTipoProva == '2'));
    $obRdbTeoricoPratico->setNull      ( false               );
}

if ( $rsConcursoCandidato->getNumLinhas() != -1 ) {
    $obLblTitulacao = new Label;
    $obLblTitulacao->setRotulo       ( 'Avalia Titulação'            );
    $obLblTitulacao->setName         ( 'boAvaliaTitulacao'           );
    if ($boAvaliaTitulacao == 't') {
        $obLblTitulacao->setValue    ( 'Sim'                         );
    } else {
        $obLblTitulacao->setValue    ( 'Não'                         );
    }
} else {
    //Define objeto CHECKBOX para armazenar AVALIA TITULACAO
    $obChkTitulacao = new CheckBox;
    $obChkTitulacao->setRotulo         ( "Avalia Titulação"        );
    $obChkTitulacao->setName           ( "boAvaliaTitulacao"       );
    $obChkTitulacao->setValue          ( 't'                       );
    $obChkTitulacao->setLabel          ( "Sim"                     );
    $obChkTitulacao->setChecked        (($boAvaliaTitulacao == 't'));
}

if ( $rsConcursoCandidato->getNumLinhas() != -1 ) {
    $obRConcursoConcurso->obRPessoalCargo->obTPessoalCargo->listarCargos( $rsCargosDisponiveis );
    $arCargosDisponiveis  = $rsCargosDisponiveis->getElementos();
    $arCargosSelecionados = $rsCargosSelecionados->getElementos();
    $stCargosSelecionados = "";
    foreach ($arCargosDisponiveis as $ind1 => $ar1) {
        $bocontrole = true;
        $inCodCargo1 = $ar1["cod_cargo"];
        foreach ($arCargosSelecionados as $ind2 => $ar2) {
            $inCodCargo2 = $ar2["cod_cargo"];
            if ($inCodCargo1 == $inCodCargo2) {
                $stCargosSelecionados .= $ar2["descricao"] . "<br>";
            }
        }
    }
    $obLblCargos = new Label;
    $obLblCargos->setRotulo       ( 'Cargos disponíveis para o concurso.' );
    $obLblCargos->setName         ( 'stCargos'                            );
    $obLblCargos->setValue        ( $stCargosSelecionados );
} else {
    $obRConcursoConcurso->obRPessoalCargo->obTPessoalCargo->listarCargos( $rsCargosDisponiveis );
    $obCmbCargos = new SelectMultiplo();
    $obCmbCargos->setName  ( 'inCodCargosSelecionados'            );
    $obCmbCargos->setRotulo( "Cargos"                             );
    $obCmbCargos->setNull  ( false                                );
    $obCmbCargos->setTitle ( 'Cargos disponíveis para o concurso.' );

    // lista de CARGOS disponiveis
    $arCargosDisponiveis  = $rsCargosDisponiveis->getElementos();
    $arCargosSelecionados = $rsCargosSelecionados->getElementos();
    $arDiferenca = array();
    if ( is_array($arCargosDisponiveis)) {
        foreach ($arCargosDisponiveis as $ind1 => $ar1) {
            $bocontrole = true;
            $inCodCargo1 = $ar1["cod_cargo"];
            if ( is_array($arCargosSelecionados)) {
                foreach ($arCargosSelecionados as $ind2 => $ar2) {
                    $inCodCargo2 = $ar2["cod_cargo"];
                    if ($inCodCargo1 == $inCodCargo2) {
                        $bocontrole = false;
                        break;
                    }
                }
            }
            if ($bocontrole) {
                $arDiferenca[] = $ar1;
            }
        }
    }
    $rsCargosDisponiveis->preenche( $arDiferenca );
    $rsCargosDisponiveis->setPrimeiroElemento();
    $obCmbCargos->SetNomeLista1( 'inCodCargosDisponiveis' );
    $obCmbCargos->setCampoId1  ( 'cod_cargo'              );
    $obCmbCargos->setCampoDesc1( 'descricao'              );
    $obCmbCargos->SetRecord1   ( $rsCargosDisponiveis     );

    // lista de CARGOS selecionados
    $obCmbCargos->SetNomeLista2( 'inCodCargosSelecionados' );
    $obCmbCargos->setCampoId2  ( 'cod_cargo'               );
    $obCmbCargos->setCampoDesc2( 'descricao'               );
    $obCmbCargos->SetRecord2   ( $rsCargosSelecionados     );
}

//link do Edital
$obLblLinkEdital = new Label;
$obLblLinkEdital->setRotulo ( "Link do Edital"   );
$obLblLinkEdital->setName   ( "stlblLabelEdital" );
$obLblLinkEdital->setValue  ( $stLinkEdital      );
$obLblLinkEdital->setId     ( "spnlinkEdital"    );
$obLblLinkEdital->setNull   ( true              );

//link  norma Regulamentadora
$obLblLinkNorma = new Label;
$obLblLinkNorma->setRotulo ( "Link da  Norma                " );
$obLblLinkNorma->setName   ( "stlblLabelNormaRegulamentadora" );
$obLblLinkNorma->setValue  ( $stLinkNormaRegulamentadora       );
$obLblLinkNorma->setId     ( "spnlinkNormaRegulamentadora"    );
$obLblLinkNorma->setNull   ( true                            );

// atributos sendo setados no objeto para depois serem inseridos no formulario.
$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo     ( "Atributos"  );
$obMontaAtributos->setName       ( "Atributo_"  );
$obMontaAtributos->setRecordSet  ( $rsAtributos );

// CRIA O FORMULARIO E MONTA OS CAMPOS
$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm                              );
$obFormulario->addHidden            ( $obHdnCtrl                           );
$obFormulario->addHidden            ( $obHdnAcao                           );
$obFormulario->addHidden            ( $obHdnExercicio                      );
if ($stAcao != 'incluir') {
    $obFormulario->addHidden            ( $obHdnEdital                         );
}
$obFormulario->addTitulo            ( "Dados para concurso".Sessao::getEntidade().""                );
if ( $rsConcursoCandidato->getNumLinhas() != -1 ) {
    $obFormulario->addComponente        ( $obLblEdital                     );
    $obFormulario->addComponente        ( $obLblDtPublicacao               );
    $obFormulario->addComponente        ( $obLblLinkEdital                 );
    $obFormulario->addComponente        ( $obLblTipoNorma                  );
    $obFormulario->addComponente        ( $obLblNorma                      );
    $obFormulario->addComponente        ( $obLblLinkNorma                  );
    $obFormulario->addComponente        ( $obLblEditalHomologacao          );
    $obFormulario->addComponente        ( $obLblDtHomologacao              );
    $obFormulario->addComponente        ( $obLblDtAplicacao                );
    $obFormulario->addComponente        ( $obLblValidade                   );
    $obFormulario->addComponente        ( $obLblNotaMinima                 );
    $obFormulario->addTitulo            ( "Avaliação"                      );
    $obFormulario->addComponente        ( $obLblTeorico                    );
    $obFormulario->addComponente        ( $obLblTitulacao                  );
    $obFormulario->addTitulo            ( "Cargos Disponíveis"             );
    $obFormulario->addComponente        ( $obLblCargos                     );
    $obMontaAtributos->setLabel         ( true                             );
    $obMontaAtributos->geraFormulario   ( $obFormulario                    );

    $stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao;
    $obButtonVoltar = new Button;
    $obButtonVoltar->setName  ( "Voltar" );
    $obButtonVoltar->setValue ( "Voltar" );
    $obButtonVoltar->obEvento->setOnClick( "Cancelar('".$stLocation."');" );
    $obFormulario->defineBarra( array( $obButtonVoltar ), "left", "" );
} else {
    //$obFormulario->addHidden            ( $obHdnEdital                         );
    $obFormulario->addHidden            ( $obHdnDtPublicacao                   );
    $obFormulario->addHidden            ( $obHdnDtHomologacao                  );
    if ($stAcao == 'incluir') {
        $obFormulario->addComponenteComposto( $obTxtEdital ,$obCmbEdital           );
    } else {
        $obFormulario->addComponente        ( $obLblEdital                     );
    }
    $obFormulario->addComponente        ( $obLblDtPublicacao                   );
    $obFormulario->addComponente        ( $obLblLinkEdital                     );
    $obFormulario->addComponenteComposto( $obTxtCodTipoNorma , $obCmbTipoNorma );
    $obFormulario->addComponenteComposto( $obTxtCodNorma , $obCmbNorma         );
    $obFormulario->addComponente        ( $obLblLinkNorma                      );
    $obFormulario->addComponenteComposto( $obTxtEditalHomologacao ,$obCmbEditalHomologacao );
    $obFormulario->addComponente        ( $obLblDtHomologacao                  );
    $obFormulario->addComponente        ( $obTxtDtAplicacao                    );
    $obFormulario->addComponente        ( $obTxtValidade                       );
    $obFormulario->addComponente        ( $obTxtNotaMinima                     );
    $obFormulario->addTitulo            ( "Avaliação" );
    $obFormulario->addComponenteComposto( $obRdbTeorico , $obRdbTeoricoPratico );
    $obFormulario->addComponente        ( $obChkTitulacao );
    $obFormulario->addTitulo            ( "Cargos Disponíveis"                  );
    $obFormulario->addComponente        ( $obCmbCargos                          );
    $obMontaAtributos->geraFormulario( $obFormulario      );

    if ($stAcao == "incluir") {
        $obFormulario->OK();
    } else {
        $stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao;
        $obFormulario->Cancelar( $stLocation );
    }
}

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
