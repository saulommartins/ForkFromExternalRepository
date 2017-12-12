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
    * Formulário de filtro Vale-Tranporte Servidor
    * Data de Criação: 24/10/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30922 $
    $Name$
    $Author: souzadl $
    $Date: 2006-09-26 07:00:14 -0300 (Ter, 26 Set 2006) $

    * Casos de uso: uc-04.06.09
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_BEN_NEGOCIO."RBeneficioContratoServidorConcessaoValeTransporte.class.php"        );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php"    );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConcessaoValeTransporte";

$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS   );
include_once( $pgOcul );

Sessao::remove('link', '');

$stAcao = $request->get('stAcao');

$obRBeneficioContratoServidorConcessaoValeTransporte  = new RBeneficioContratoServidorConcessaoValeTransporte;
$obRBeneficioContratoServidorConcessaoValeTransporte->addRBeneficioConcessaoValeTransporte();
$obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->listarMes( $rsMes );

//Instancia o formulário
$obForm = new Form;
$obForm->setAction                              ( $pgList                                               );
$obForm->setTarget                              ( "telaPrincipal"                                       );

$obHdnAcao = new Hidden;
$obHdnAcao->setName                             ( "stAcao"                                              );
$obHdnAcao->setValue                            ( $stAcao                                               );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                              );
$obHdnCtrl->setValue                            ( ""                                                    );

//Define o objeto RADIO para Concessãp
$obRdoConcessaoContrato = new Radio;
$obRdoConcessaoContrato->setName                ( "stConcessao"                                         );
$obRdoConcessaoContrato->setId                  ( "stConcessao"                                         );
$obRdoConcessaoContrato->setTitle               ( "Selecione o filtro para concessão."                  );
$obRdoConcessaoContrato->setRotulo              ( "Concessão"                                           );
$obRdoConcessaoContrato->setLabel               ( "Matrícula"                                            );
$obRdoConcessaoContrato->setValue               ( "contrato"                                            );
$obRdoConcessaoContrato->setNull                ( false                                                 );
$obRdoConcessaoContrato->obEvento->setOnChange  ( "buscaValorFiltro('geraSpan1Filtro');"                );
$obRdoConcessaoContrato->setChecked             ( true                                                  );

$obRdoConcessaoGrupo = new Radio;
$obRdoConcessaoGrupo->setName                   ( "stConcessao"                                         );
$obRdoConcessaoGrupo->setId                     ( "stConcessao"                                         );
$obRdoConcessaoGrupo->setTitle                  ( "Selecione o filtro para concessão."                  );
$obRdoConcessaoGrupo->setRotulo                 ( "Concessão"                                           );
$obRdoConcessaoGrupo->setLabel                  ( "Grupo"                                               );
$obRdoConcessaoGrupo->setValue                  ( "grupo"                                               );
$obRdoConcessaoGrupo->setNull                   ( false                                                 );
$obRdoConcessaoGrupo->obEvento->setOnChange     ( "buscaValorFiltro('geraSpan2Filtro');"                );
$obRdoConcessaoGrupo->setChecked                ( false                                                 );

$obRdoConcessaoVT = new Radio;
$obRdoConcessaoVT->setName                      ( "stConcessao"                                         );
$obRdoConcessaoVT->setId                        ( "stConcessao"                                         );
$obRdoConcessaoVT->setTitle                     ( "Selecione o filtro para concessão."                  );
$obRdoConcessaoVT->setRotulo                    ( "Concessão"                                           );
$obRdoConcessaoVT->setLabel                     ( "Vale-Transporte"                                     );
$obRdoConcessaoVT->setValue                     ( "vale-transporte"                                     );
$obRdoConcessaoVT->setNull                      ( false                                                 );
$obRdoConcessaoVT->obEvento->setOnChange        ( "buscaValorFiltro('geraSpan3Filtro');"                );
$obRdoConcessaoVT->setChecked                   ( false                                                 );

$obRdoConcessaoCGM = new Radio;
$obRdoConcessaoCGM->setName                     ( "stConcessao"                                         );
$obRdoConcessaoCGM->setId                       ( "stConcessao"                                         );
$obRdoConcessaoCGM->setTitle                    ( "Selecione o filtro para concessão."                  );
$obRdoConcessaoCGM->setRotulo                   ( "Concessão"                                           );
$obRdoConcessaoCGM->setLabel                    ( "CGM/Matrícula"                                        );
$obRdoConcessaoCGM->setValue                    ( "cgm_contrato"                                        );
$obRdoConcessaoCGM->setNull                     ( false                                                 );
$obRdoConcessaoCGM->obEvento->setOnChange       ( "buscaValorFiltro('geraSpan4Filtro');"                );
$obRdoConcessaoCGM->setChecked                  ( false                                                 );

$obTxtCodMes = new TextBox;
$obTxtCodMes->setRotulo                         ( "Mês"                                                 );
$obTxtCodMes->setTitle                          ( "Informe o mês da concessão"                          );
$obTxtCodMes->setName                           ( "inCodMes"                                            );
$obTxtCodMes->setValue                          ( $inCodMes                                             );
$obTxtCodMes->setMaxLength                      ( 2                                                     );
$obTxtCodMes->setSize                           ( 10                                                    );
$obTxtCodMes->setNull                           ( false                                                 );
$obTxtCodMes->setInteiro                        ( true                                                  );

$obCmbMes = new Select;
$obCmbMes->setName                              ( "stMes"                                               );
$obCmbMes->setTitle                             ( "Informe o mês da concessão"                          );
$obCmbMes->setStyle                             ( "width: 250px"                                        );
$obCmbMes->setRotulo                            ( "Mês"                                                 );
$obCmbMes->setValue                             ( $inCodMes                                             );
$obCmbMes->setNull                              ( false                                                 );
$obCmbMes->addOption                            ( "", "Selecione"                                       );
$obCmbMes->addOption                            ( "0","Todos"                                           );
$obCmbMes->setCampoID                           ( "[cod_mes]"                                           );
$obCmbMes->setCampoDesc                         ( "[descricao]"                                         );
$obCmbMes->preencheCombo                        ( $rsMes                                                );

$obTxtAno = new Inteiro;
$obTxtAno->setName                              ( "inAno"                                               );
$obTxtAno->setRotulo                            ( "Ano"                                                 );
$obTxtAno->setTitle                             ( "Informe o ano da concessão"                          );
$obTxtAno->setValue                             ( $inAno                                                );
$obTxtAno->setNull                              ( false                                                 );
$obTxtAno->setAlign                             ( "RIGHT"                                               );
$obTxtAno->setMaxLength                         ( 4                                                     );
$obTxtAno->setSize                              ( 4                                                     );
$obTxtAno->setMaxValue                          ( 2050                                                  );
$obTxtAno->setMinValue                          ( date("Y")                                             );
$obTxtAno->setNegativo                          ( false                                                 );
$obTxtAno->setValue                             ( date("Y")                                             );

$obIFiltroCompetencia = new IFiltroCompetencia(true);

$obChkAgrupar = new CheckBox;
$obChkAgrupar->setName                          ( "boAgrupar"                                               );
$obChkAgrupar->setRotulo                        ( "Agrupar Concessões"                                      );
$obChkAgrupar->setTitle                         ( "Agrupar Concessoes."                                     );
$obChkAgrupar->setValue                         ( true                                                      );

//Define o objeto SPAN para filtro
$obSpanFiltro = new Span;
$obSpanFiltro->setId                            ( "spnFiltro"                                           );

$obHdnOpcaoEval = new HiddenEval;
$obHdnOpcaoEval->setName                        ( "stOpcaoEval"                                         );
$obHdnOpcaoEval->setValue                       ( ""                                                    );

$obBtnLimpar = new Button;
$obBtnLimpar->setName                           ( "btnLimpar"                                           );
$obBtnLimpar->setValue                          ( "Limpar"                                              );
$obBtnLimpar->setTipo                           ( "button"                                              );
$obBtnLimpar->obEvento->setOnClick              ( "buscaValorFiltro('limparFiltro');"                   );
$obBtnLimpar->setDisabled                       ( false                                                 );

$obBtnOK = new Ok;

//Criação do formulário
$obFormulario = new Formulario;
$obFormulario->addForm                          ( $obForm                                               );
$obFormulario->addHidden                        ( $obHdnCtrl                                            );
$obFormulario->addHidden                        ( $obHdnAcao                                            );
$obFormulario->addTitulo                        ( "Dados para o Filtro"                                 );
if ($stAcao == 'alterar') {
    $obFormulario->agrupaComponentes            ( array($obRdoConcessaoContrato,$obRdoConcessaoCGM,$obRdoConcessaoGrupo,$obRdoConcessaoVT) );
}
if ($stAcao == 'excluir') {
    $obFormulario->agrupaComponentes            ( array($obRdoConcessaoContrato,$obRdoConcessaoCGM,$obRdoConcessaoGrupo)   );
}
//$obFormulario->addComponenteComposto            ( $obTxtCodMes,$obCmbMes                                );
//$obFormulario->addComponente                    ( $obTxtAno                                             );
$obIFiltroCompetencia->geraFormulario           ( $obFormulario                                         );
$obFormulario->addSpan                          ( $obSpanFiltro                                         );
$obFormulario->addHidden                        ( $obHdnOpcaoEval,true                                  );
if ($stAcao == 'excluir') {
    $obFormulario->addComponente                ( $obChkAgrupar                                         );
}
$obFormulario->defineBarra                      ( array( $obBtnOK,$obBtnLimpar )                        );
$obFormulario->show();

geraSpan1Filtro(true);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
