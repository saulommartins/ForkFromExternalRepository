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
    * Formulário de Inicializacao de Vale-Tranporte Servidor
    * Data de Criação: 01/11/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Eduardo Antunez

    * @ignore

    $Revision: 30931 $
    $Name$
    $Author: souzadl $
    $Date: 2006-09-26 07:00:14 -0300 (Ter, 26 Set 2006) $

    * Casos de uso: uc-04.06.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO.'RFolhaPagamentoPeriodoMovimentacao.class.php'                      );

//Define o nome dos arquivos PHP
$stPrograma = "ManterInicializacaoValeTransporte";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JSManterConcessaoValeTransporte.js";

include_once ($pgJS);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction      ( ($stAcao=='excluir') ? $pgList : $pgProc );
$obForm->setTarget      ( ($stAcao=='excluir') ? "telaPrincipal" : "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName     ( "stAcao" );
$obHdnAcao->setValue    ( $stAcao  );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName     ( "stCtrl" );
$obHdnCtrl->setValue    ( ""       );

//Define o objeto RADIO para Inicializacao
$obRdoInicializacaoContrato = new Radio;
$obRdoInicializacaoContrato->setName                ( "stInicializacao"                                             );
$obRdoInicializacaoContrato->setId                  ( "stInicializacao"                                             );
$obRdoInicializacaoContrato->setTitle               ( "Informe o tipo de inicialização de vale-transporte"          );
$obRdoInicializacaoContrato->setRotulo              ( "Inicializar Concessão"                                       );
$obRdoInicializacaoContrato->setLabel               ( "Matrícula"                                                    );
$obRdoInicializacaoContrato->setValue               ( "contrato"                                                    );
$obRdoInicializacaoContrato->setNull                ( false                                                         );
$obRdoInicializacaoContrato->obEvento->setOnChange  ( "buscaDado('geraSpan');"                                     );
$obRdoInicializacaoContrato->setChecked             ( true                                                          );

$obRdoInicializacaoCGM = new Radio;
$obRdoInicializacaoCGM->setName                     ( "stInicializacao"                                             );
$obRdoInicializacaoCGM->setId                       ( "stInicializacao"                                             );
$obRdoInicializacaoCGM->setTitle                    ( "Informe o tipo de inicialização de vale-transporte"          );
$obRdoInicializacaoCGM->setRotulo                   ( "Inicializar Concessão"                                       );
$obRdoInicializacaoCGM->setLabel                    ( "CGM/Matrícula"                                                );
$obRdoInicializacaoCGM->setValue                    ( "cgm"                                                         );
$obRdoInicializacaoCGM->setNull                     ( false                                                         );
$obRdoInicializacaoCGM->obEvento->setOnChange       ( "buscaDado('geraSpan');"                                     );

$obRdoInicializacaoGrupo = new Radio;
$obRdoInicializacaoGrupo->setName                   ( "stInicializacao"                                             );
$obRdoInicializacaoGrupo->setId                     ( "stInicializacao"                                             );
$obRdoInicializacaoGrupo->setTitle                  ( "Informe o tipo de inicialização de vale-transporte"          );
$obRdoInicializacaoGrupo->setRotulo                 ( "Inicializar Concessão"                                       );
$obRdoInicializacaoGrupo->setLabel                  ( "Grupo"                                                       );
$obRdoInicializacaoGrupo->setValue                  ( "grupo"                                                       );
$obRdoInicializacaoGrupo->setNull                   ( false                                                         );
$obRdoInicializacaoGrupo->obEvento->setOnChange     ( "buscaDado('geraSpan');"                                     );

$obRdoInicializacaoGeral = new Radio;
$obRdoInicializacaoGeral->setName                   ( "stInicializacao"                                             );
$obRdoInicializacaoGeral->setId                     ( "stInicializacao"                                             );
$obRdoInicializacaoGeral->setTitle                  ( "Informe o tipo de inicialização de vale-transporte"          );
$obRdoInicializacaoGeral->setRotulo                 ( "Inicializar Concessão"                                       );
$obRdoInicializacaoGeral->setLabel                  ( "Geral"                                                       );
$obRdoInicializacaoGeral->setValue                  ( "geral"                                                       );
$obRdoInicializacaoGeral->setNull                   ( false                                                         );
$obRdoInicializacaoGeral->obEvento->setOnChange     ( "buscaDado('geraSpan');"                                     );

//Define o objeto SPAN para Inicialização por contrato, cgm/contrato ou grupo
$obSpanInicializacao = new Span;
$obSpanInicializacao->setId                         ( "spnInicializacao"                                            );

$obHdnOpcaoEval = new HiddenEval;
$obHdnOpcaoEval->setName                            ( "stOpcaoEval"                                                 );
$obHdnOpcaoEval->setValue                           ( ""                                                            );

$obTxtAno = new TextBox;
$obTxtAno->setName                                  ( "inAno"                                                       );
$obTxtAno->setTitle                                 ( "Informe o ano referente a inicialização"                     );
$obTxtAno->setRotulo                                ( "Ano"                                                         );
$obTxtAno->setNull                                  ( false                                                         );
$obTxtAno->setMaxLength                             ( 4                                                             );
$obTxtAno->setSize                                  ( 4                                                             );
$obTxtAno->setInteiro                               ( true                                                          );

$obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
$obRFolhaPagamentoPeriodoMovimentacao->listarUltimaMovimentacao($rsUltimaMovimentacao);
$stDataUltimaMovimentacao = $rsUltimaMovimentacao->getCampo( 'dt_final' );
$stDataUltimaMovimentacao = explode( '/', $stDataUltimaMovimentacao );

$obTxtAno->setValue                                 ( $stDataUltimaMovimentacao[2]                                  );
$obTxtAno->obEvento->setOnBlur                      ( "buscaDado('preencheMes')"                                    );

$obTxtMes = new TextBox;
$obTxtMes->setName                                  ( "inMes"                                                       );
$obTxtMes->setTitle                                 ( "Informe o mês referente a inicialização"                     );
$obTxtMes->setRotulo                                ( "Mês"                                                         );
$obTxtMes->setNull                                  ( false                                                         );
$obTxtMes->setMaxLength                             ( 2                                                             );
$obTxtMes->setSize                                  ( 2                                                             );
$obTxtMes->setInteiro                               ( true                                                          );

$obCmbMes = new Select;
$obCmbMes->setName                                  ( "stMes"                                                       );
$obCmbMes->setStyle                                 ( "width: 250px"                                                );
$obCmbMes->setRotulo                                ( "Mês"                                                         );
$obCmbMes->setNull                                  ( false                                                         );
$obCmbMes->addOption                                ( "", "Selecione"                                               );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden        ( $obHdnCtrl                                                );
$obFormulario->addHidden        ( $obHdnAcao                                                );
$obFormulario->addTitulo        ( "Tipo da Inicialização da Concessão do Vale-Transporte"   );
$obFormulario->agrupaComponentes( array( $obRdoInicializacaoContrato,$obRdoInicializacaoCGM,$obRdoInicializacaoGrupo,$obRdoInicializacaoGeral) );
$obFormulario->addSpan          ( $obSpanInicializacao                                      );
$obFormulario->addHidden        ( $obHdnOpcaoEval,true                                      );
$obFormulario->addTitulo        ( "Dados da Inicialização"                                  );
$obFormulario->addComponente    ( $obTxtAno                                                 );
$obFormulario->addComponenteComposto ( $obTxtMes , $obCmbMes                                );
$obFormulario->OK();

$stJs .= "buscaDado('iniciaFormulario'); \n";
SistemaLegado::executaFramePrincipal($stJs);

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
