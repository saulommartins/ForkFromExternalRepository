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
* Página de Formulario de Prorrogação de Concurso
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

$obRConcursoNorma = $obRConcursoConcurso = $obRConcursoNormaEdital = new RConcursoConcurso;
$rsCargosSelecionados = $rsConcurso = $rsAvaliacao = $rsCargosSelecionados = new RecordSet;
$rsEdital = $rsTipoNorma = $rsNormaEdital = $rsCargosDisponiveis = new RecordSet;

$obRConcursoConcurso->recuperaConfiguracao( $arConfiguracao );
foreach ($arConfiguracao as $key => $valor) {
    if ( $key == 'mascara_concurso'.Sessao::getEntidade() ) {
        $stMascaraConcurso = $valor;
    }
    if ( $key == 'tipo_portaria_edital'.Sessao::getEntidade() ) {
        $inTipoNormaEdital = $valor;
    }
}

    $obRConcursoConcurso->setCodEdital($inCodEdital);
    $obRConcursoConcurso->consultarConcurso( $rsConcurso, $rsCargosSelecionados );
    if ( !$rsConcurso->eof() ) {
        $obRConcursoConcurso->obRNorma->setCodNorma( $rsConcurso->getCampo( 'cod_norma'     ));

        $obRConcursoConcurso->setCodEdital         ( $rsConcurso->getCampo( 'cod_edital'     ));
        $obRConcursoConcurso->setAplicacao         ( $rsConcurso->getCampo( 'dt_aplicacao'  ));
        $obRConcursoConcurso->setProrrogacao       ( $rsConcurso->getCampo( 'dt_prorrogacao'));
        $obRConcursoConcurso->setNotaMinima        ( $rsConcurso->getCampo( 'nota_minima'   ));
        $obRConcursoConcurso->setAvaliaTitulacao   ( $rsConcurso->getCampo('avalia_titulacao'));
        $obRConcursoConcurso->setMesesValidade	   ( $rsConcurso->getCampo('meses_validade'));
        $obRConcursoConcurso->obRNorma->consultar( $rsNorma );
        $inCodTipoNorma = $obRConcursoConcurso->obRNorma->obRTipoNorma->getCodTipoNorma();
        $stTipoNorma = $inCodTipoNorma;

        $obRConcursoNorma->obRNorma->obRTipoNorma->setCodTipoNorma( $inCodTipoNorma );
        $obRConcursoNorma->obRNorma->setCodNorma( $rsConcurso->getCampo( 'cod_norma' ) );
        $obRConcursoNorma->obRNorma->listar( $rsNorma );

        $stEdital = $nuEdital = $obRConcursoConcurso->getCodEdital();

        $obRConcursoNormaEdital->obRNorma->setCodNorma($stEdital);
        $obRConcursoNormaEdital->obRNorma->listar($rsEdital);

        $stNomeEdital = $rsEdital->getCampo("nom_norma");
        $stURL = $rsEdital->getCampo("link");
        $dtPublicacao=$rsEdital->getCampo("dt_publicacao");

        $stLinkNormaRegulamentadora = $obRConcursoConcurso->obRNorma->getUrl();

        $obRConcursoConcurso->consultarConcursoHomologacao( $rsConcursoHomologacao );

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

//Define o objeto de controle
$obHdnCodEdital = new Hidden;
$obHdnCodEdital->setName ( "stEdital" );
$obHdnCodEdital->setValue( $obRConcursoConcurso->getCodEdital()    );

$obHdnDtAplicacao = new Hidden;
$obHdnDtAplicacao->setName ( "dtAplicacao" );
$obHdnDtAplicacao->setValue( $obRConcursoConcurso->getAplicacao()    );

$obHdnDtHomologacao = new Hidden;
$obHdnDtHomologacao->setName ( "dtHomologacao" );
$obHdnDtHomologacao->setValue( $rsConcursoHomologacao->getCampo("dt_publicacao")    );

$obHdnFlNotaMinima = new Hidden;
$obHdnFlNotaMinima->setName ( "nuNotaMinima" );
$obHdnFlNotaMinima->setValue( $obRConcursoConcurso->getNotaMinima()  );

$obHdnInMesesValidade = new Hidden;
$obHdnInMesesValidade->setName ( "inMesesValidade" );
$obHdnInMesesValidade->setValue( $obRConcursoConcurso->getMesesValidade()  );

$obHdnStNorma = new Hidden;
$obHdnStNorma->setName ( "stNorma" );
$obHdnStNorma->setValue( $rsConcurso->getCampo( 'cod_norma')  );

//Define o objeto da ação stAcao
$obHdnExercicio = new Hidden;
$obHdnExercicio->setName ( "inExercicio" );
$obHdnExercicio->setValue( $inExercicio  );

//Define o objeto da ação stAcao
$obHdnPublicacao = new Hidden;
$obHdnPublicacao->setName ( "dtPublicacao" );
$obHdnPublicacao->setValue( $dtPublicacao  );

// Edital
$obLblEdital = new Label;
$obLblEdital->setRotulo ( "Edital de abertura"   );
$obLblEdital->setName   ( "stEdital" );
$obLblEdital->setValue  ( $obRConcursoConcurso->getCodEdital()." - ".$stNomeEdital     );
$obLblEdital->setId     ( "stEdital"    );
$obLblEdital->setNull   ( true              );

//Data de publicação do edital de abertura
$obLblDtPublicacao = new Label;
$obLblDtPublicacao->setRotulo       ( 'Data de publicação'     );
$obLblDtPublicacao->setName         ( 'lbldtPublicacao'        );
$obLblDtPublicacao->setId           ( 'lbldtPublicacao'        );
$obLblDtPublicacao->setValue        ( $dtPublicacao            );

//link do Edital
$obLblLinkEdital = new Label;
$obLblLinkEdital->setRotulo ( "Link do Edital"   );
$obLblLinkEdital->setName   ( "stlblLabelEdital" );
$obLblLinkEdital->setValue  ( $stURL     );
$obLblLinkEdital->setNull   ( true              );

//Tipo Norma
$obRConcursoNorma->obRTipoNorma->setCodTipoNorma( $inCodTipoNorma );
$obRConcursoNorma->obRTipoNorma->listarTodos( $rsTipoNorma );
$obLblTipoNorma = new Label;
$obLblTipoNorma->setRotulo ( "Tipo Norma Regulamentadora"   );
$obLblTipoNorma->setName   ( "stTipoNorma"                  );
$obLblTipoNorma->setValue  ( $inCodTipoNorma ." - ".$rsTipoNorma->getCampo("nom_tipo_norma") );
$obLblTipoNorma->setNull   ( true                           );

// Define o objeto TEXT para armazenar o CODIGO DO TIPO DE NORMA
$obLblNorma = new Label;
$obLblNorma->setRotulo ( "Norma Regulamentadora"   );
$obLblNorma->setName   ( "stTipoNorma"                  );
$obLblNorma->setValue  ( $rsConcurso->getCampo( 'cod_norma' ) ." - ". $rsNorma->getCampo('nom_norma') );
$obLblNorma->setNull   ( true                           );

//link  norma Regulamentadora
$obLblLinkNorma = new Label;
$obLblLinkNorma->setRotulo ( "Link da  Norma                " );
$obLblLinkNorma->setName   ( "stlblLabelNormaRegulamentadora" );
$obLblLinkNorma->setValue  ( $stLinkNormaRegulamentadora       );
$obLblLinkNorma->setId     ( "spnlinkNormaRegulamentadora"    );
$obLblLinkNorma->setNull   ( true                            );

//Define objeto DATA para armazenar a DATA DE Aplicação
$obLblDtAplicacao = new Label;
$obLblDtAplicacao->setRotulo( "Data de aplicação"                 );
$obLblDtAplicacao->setName  ( "dtAplicacacao"                     );
$obLblDtAplicacao->setValue ( $obRConcursoConcurso->getAplicacao());
$obLblDtAplicacao->setNull  ( true                               );

$obLblValidade = new Label;
$obLblValidade->setRotulo   ( "Validade do Concurso"                  );
$obLblValidade->setName     ( "inMesesValidade"                       );
$obLblValidade->setValue    ( $obRConcursoConcurso->getMesesValidade());
$obLblValidade->setNull     ( true                                   );

//Define o objeto TEXT para armazenar a NOTA MINIMA
$obLblNotaMinima = new Label;
$obLblNotaMinima->setRotulo   ( "Nota mínima"                        );
$obLblNotaMinima->setName     ( "nuNotaMinima"                       );
$obLblNotaMinima->setValue    ( $obRConcursoConcurso->getNotaMinima());
$obLblNotaMinima->setNull     ( true                                );

// sugere a data de prorrogacao para 2 anos a data de publicacao, se data for maior que esta não é válida.
if ( $obRConcursoConcurso->getProrrogacao() == '' ) {
    $arDate=explode("/",$dtPublicacao);
    $data = $arDate[1]."/".$arDate[0]."/".$arDate[2];
    $obRConcursoConcurso->setProrrogacao(strftime('%d/%m/%Y',strtotime($data." +2 year")));
}

//Define objeto DATA para armazenar a DATA DE Prorrogacao
$obTxtDtProrrogacao = new Data;
$obTxtDtProrrogacao->setName  ( "dtProrrogacao"                     );
$obTxtDtProrrogacao->setValue ( $obRConcursoConcurso->getProrrogacao());
$obTxtDtProrrogacao->setRotulo( "Data de Prorrogação"                 );
$obTxtDtProrrogacao->setNull  ( false                               );
$obTxtDtProrrogacao->setTitle ( ''                 );

$obLblEditalHomologacao = new Label;
$obLblEditalHomologacao->setRotulo       ( 'Edital de homologação' );
$obLblEditalHomologacao->setName         ( 'stEditalHomologacao'   );
$obLblEditalHomologacao->setValue        ( $rsConcursoHomologacao->getCampo('cod_homologacao').'/'. $rsConcursoHomologacao->getCampo("ano_publicacao") .' - '. $rsConcursoHomologacao->getCampo("nom_norma")   );

$obLblDtHomologacao = new Label;
$obLblDtHomologacao->setRotulo       ( 'Data de publicação'    );
$obLblDtHomologacao->setName         ( 'dtHomologacao'          );
$obLblDtHomologacao->setValue        ( $rsConcursoHomologacao->getCampo("dt_publicacao") );

$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm                              );
$obFormulario->addHidden            ( $obHdnCtrl                           );
$obFormulario->addHidden            ( $obHdnAcao                           );
$obFormulario->addHidden            ( $obHdnCodEdital);
$obFormulario->addHidden            ( $obHdnExercicio                      );
$obFormulario->addHidden            ( $obHdnPublicacao                      );
$obFormulario->addHidden            ( $obHdnDtAplicacao                    );
$obFormulario->addHidden            ( $obHdnDtHomologacao                  );
$obFormulario->addHidden            ( $obHdnInMesesValidade                );
$obFormulario->addHidden            ( $obHdnFlNotaMinima                   );
$obFormulario->addHidden            ( $obHdnStNorma                        );
$obFormulario->addTitulo            ( "Dados para concurso".Sessao::getEntidade().""                );
$obFormulario->addComponente		( $obLblEdital);
$obFormulario->addComponente        ( $obLblDtPublicacao                   );
$obFormulario->addComponente        ( $obLblLinkEdital                     );
$obFormulario->addComponente        ( $obLblTipoNorma                      );
$obFormulario->addComponente        ( $obLblNorma                          );
$obFormulario->addComponente        ( $obLblLinkNorma                      );
$obFormulario->addComponente        ( $obLblEditalHomologacao          );
$obFormulario->addComponente        ( $obLblDtHomologacao              );
$obFormulario->addComponente        ( $obLblDtAplicacao                    );
$obFormulario->addComponente        ( $obLblValidade                       );
$obFormulario->addComponente        ( $obLblNotaMinima                     );
$obFormulario->addComponente        ( $obTxtDtProrrogacao);

if ($stAcao == "incluir") {
    $obFormulario->OK();
} else {
    $obFormulario->Cancelar();
}

$obFormulario->show();
SistemaLegado::executaFramePrincipal( $stJs );

include_once ($pgJS);
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
