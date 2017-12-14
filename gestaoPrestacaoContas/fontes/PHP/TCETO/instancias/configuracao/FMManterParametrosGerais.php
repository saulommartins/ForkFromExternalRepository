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
    * Pacote de configuração do TCETO - Formulário Configurar Parâmetros Gerais
    * Data de Criação   : 05/11/2014

    * @author Analista: Silvia Martins Silva
    * @author Desenvolvedor: Michel Teixeira
    * $Id: FMManterParametrosGerais.php 60645 2014-11-05 15:47:16Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracao.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterParametrosGerais";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

$obTExportacaoConfiguracao = new TAdministracaoConfiguracao;
$obTExportacaoConfiguracao->setDado("cod_modulo", 64);
$obTExportacaoConfiguracao->setDado("exercicio",Sessao::getExercicio());

$obTExportacaoConfiguracao->setDado("parametro","tceto_orgao_prefeitura");
$obTExportacaoConfiguracao->consultar();

$obTxtExecutivo = new TextBox;
$obTxtExecutivo->setName        ( "inCodExecutivo"  );
$obTxtExecutivo->setValue       ( $obTExportacaoConfiguracao->getDado("valor")  );
$obTxtExecutivo->setRotulo      ( "Órgão Poder Executivo"                       );
$obTxtExecutivo->setTitle       ( "Informe o código do orgão relativo ao poder executivo" );
$obTxtExecutivo->setInteiro     ( true  );
$obTxtExecutivo->setSize        ( 3 );
$obTxtExecutivo->setMaxLength   ( 2 );
$obTxtExecutivo->setNull        ( false );

$obTExportacaoConfiguracao->setDado("parametro","tceto_unidade_prefeitura");
$obTExportacaoConfiguracao->consultar();
 
$obTxtExecutivoUnidade = new TextBox;
$obTxtExecutivoUnidade->setName     ( "inCodUnidadeExecutivo"   );
$obTxtExecutivoUnidade->setValue    ( $obTExportacaoConfiguracao->getDado("valor")  );
$obTxtExecutivoUnidade->setRotulo   ( "Unidade Poder Executivo"                     );
$obTxtExecutivoUnidade->setTitle    ( "Informe o código da unidade relativo ao poder executivo" );
$obTxtExecutivoUnidade->setInteiro  ( true  );
$obTxtExecutivoUnidade->setSize     ( 3 );
$obTxtExecutivoUnidade->setMaxLength( 4 );
$obTxtExecutivoUnidade->setNull     ( false );

$obTExportacaoConfiguracao->setDado("parametro","tceto_orgao_camara");
$obTExportacaoConfiguracao->consultar();

$obTxtLegislativo = new Textbox;
$obTxtLegislativo->setName      ( "inCodLegislativo"  );
$obTxtLegislativo->setValue     ( $obTExportacaoConfiguracao->getDado("valor")    );
$obTxtLegislativo->setRotulo    ( "Órgão Poder Legislativo"                       );
$obTxtLegislativo->setTitle     ( "Informe o código do orgão relativo ao poder legislativo" );
$obTxtLegislativo->setInteiro   ( true  );
$obTxtLegislativo->setSize      ( 3 );
$obTxtLegislativo->setMaxLength ( 2 );
$obTxtLegislativo->setNull      ( false );

$obTExportacaoConfiguracao->setDado("parametro","tceto_unidade_camara");
$obTExportacaoConfiguracao->consultar();
 
$obTxtLegislativoUnidade = new Textbox;
$obTxtLegislativoUnidade->setName       ( "inCodUnidadeLegislativo" );
$obTxtLegislativoUnidade->setValue      ( $obTExportacaoConfiguracao->getDado("valor")  );
$obTxtLegislativoUnidade->setRotulo     ( "Unidade Poder Legislativo"                   );
$obTxtLegislativoUnidade->setTitle      ( "Informe o código da unidade relativo ao poder legislativo" );
$obTxtLegislativoUnidade->setInteiro    ( true  );
$obTxtLegislativoUnidade->setSize       ( 3 );
$obTxtLegislativoUnidade->setMaxLength  ( 4 );
$obTxtLegislativoUnidade->setNull       ( false );

$obTExportacaoConfiguracao->setDado("parametro","tceto_orgao_rpps");
$obTExportacaoConfiguracao->consultar();

$obTxtRPPS = new Textbox;
$obTxtRPPS->setName         ( "inCodRPPS"   );
$obTxtRPPS->setValue        ( $obTExportacaoConfiguracao->getDado("valor")  );
$obTxtRPPS->setRotulo       ( "Órgão RPPS"                                  );
$obTxtRPPS->setTitle        ( "Informe o código do orgão relativo ao RPPS"  );
$obTxtRPPS->setInteiro      ( true );
$obTxtRPPS->setSize         ( 3 );
$obTxtRPPS->setMaxLength    ( 2 );

$obTExportacaoConfiguracao->setDado("parametro","tceto_unidade_rpps");
$obTExportacaoConfiguracao->consultar();
 
$obTxtRPPSUnidade = new Textbox;
$obTxtRPPSUnidade->setName      ( "inCodUnidadeRPPS"    );
$obTxtRPPSUnidade->setValue     ( $obTExportacaoConfiguracao->getDado("valor")  );
$obTxtRPPSUnidade->setRotulo    ( "Unidade RPPS"                                );
$obTxtRPPSUnidade->setTitle     ( "Informe o código da unidade relativo ao RPPS");
$obTxtRPPSUnidade->setInteiro   ( true );
$obTxtRPPSUnidade->setSize      ( 3 );
$obTxtRPPSUnidade->setMaxLength ( 4 );

$obTExportacaoConfiguracao->setDado("parametro","tceto_orgao_outros");
$obTExportacaoConfiguracao->consultar();

$obTxtOutros = new Textbox;
$obTxtOutros->setName       ( "inCodOutros" );
$obTxtOutros->setValue      ( $obTExportacaoConfiguracao->getDado("valor")  );
$obTxtOutros->setRotulo     ( "Órgão Outros"                                );
$obTxtOutros->setTitle      ( "Informe o código do orgão para outros poderes" );
$obTxtOutros->setInteiro    ( true );
$obTxtOutros->setSize       ( 3 );
$obTxtOutros->setMaxLength  ( 2 );

$obTExportacaoConfiguracao->setDado("parametro","tceto_unidade_outros");
$obTExportacaoConfiguracao->consultar();

$obTxtOutrosUnidade = new Textbox;
$obTxtOutrosUnidade->setName        ( "inCodUnidadeOutros"  );
$obTxtOutrosUnidade->setValue       ( $obTExportacaoConfiguracao->getDado("valor")  );
$obTxtOutrosUnidade->setRotulo      ( "Unidade Outros"                              );
$obTxtOutrosUnidade->setTitle       ( "Informe o código da unidade para outros poderes" );
$obTxtOutrosUnidade->setInteiro     ( true );
$obTxtOutrosUnidade->setSize        ( 3 );
$obTxtOutrosUnidade->setMaxLength   ( 4 );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addTitulo    ( "Configuração de Órgão/Unidade" );
$obFormulario->addHidden    ( $obHdnAcao                );
$obFormulario->addHidden    ( $obHdnCtrl                );
$obFormulario->addComponente( $obTxtExecutivo           );
$obFormulario->addComponente( $obTxtExecutivoUnidade    );
$obFormulario->addComponente( $obTxtLegislativo         );
$obFormulario->addComponente( $obTxtLegislativoUnidade  );
$obFormulario->addComponente( $obTxtRPPS                );
$obFormulario->addComponente( $obTxtRPPSUnidade         );
$obFormulario->addComponente( $obTxtOutros              );
$obFormulario->addComponente( $obTxtOutrosUnidade       );

$obOk  = new Ok;
$obLimpar = new Button;
$obLimpar->setValue( "Limpar" );
$obLimpar->obEvento->setOnClick( "limpaFormulario();" );

$obFormulario->defineBarra( array( $obOk, $obLimpar ) );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
