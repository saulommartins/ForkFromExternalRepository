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
    * Data de Criação: 09/01/2008

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    $Id: FLRelatorioPatrimonial.php 66157 2016-07-22 14:07:38Z lisiane $

    * Casos de uso: uc-03.01.09
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_PAT_COMPONENTES.'ISelectEspecie.class.php'                );
include_once( CAM_GP_PAT_COMPONENTES.'IIntervaloPopUpBem.class.php'            );
include_once( CAM_GA_ORGAN_COMPONENTES."IMontaOrganograma.class.php"           );
include_once( CAM_GA_ORGAN_COMPONENTES."IMontaOrganogramaLocal.class.php"      );
include_once( CAM_GP_PAT_COMPONENTES."IMontaClassificacao.class.php"           );
include_once( CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php" );


//Define o nome dos arquivos PHP
$stPrograma = "RelatorioPatrimonial";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgGera = "OCGera".$stPrograma.".php";
$pgJS = "JS".$stPrograma.".js";

require_once $pgJS;

$obForm = new Form;
$obForm->setAction( $pgGera );

//Cria o hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

//cria a acao de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl" );
$obHdnCtrl->setValue("");

$obISelectEntidade = new ITextBoxSelectEntidadeUsuario();
$obISelectEntidade->obTextBox->setNull(false);
$obISelectEntidade->setNull(false);

//instancia o componente IMontaClassificacao
$obIMontaClassificacao = new IMontaClassificacao( $obFormClass );
$obIMontaClassificacao->setNull( true );
$obIMontaClassificacao->obTxtCodClassificacao->setValue( $stClassificacao );
$obIMontaClassificacao->obTxtCodClassificacao->obEvento->setOnChange( $obIMontaClassificacao->obTxtCodClassificacao->obEvento->getOnChange().";montaParametrosGET( 'montaAtributos', 'stCodClassificacao' );" );
$obIMontaClassificacao->obISelectEspecie->obSelectEspecie->obEvento->setOnChange( $obIMontaClassificacao->obISelectEspecie->obSelectEspecie->obEvento->getOnChange().";montaParametrosGET( 'montaAtributos', 'inCodNatureza,inCodGrupo,inCodEspecie' );"  );

//instancia o componente iintervalopopupbem
$obIIntervalorPopUpBem = new IIntervaloPopUpBem( $obForm );
$obIIntervalorPopUpBem->setRotulo( 'Intervalo entre Códigos de Bens' );

//instancia um componente periodicidade
$obPeriodicidadeAquisicao = new Periodicidade();
$obPeriodicidadeAquisicao->setIdComponente( 'Aquisicao' );
$obPeriodicidadeAquisicao->setRotulo('Período da Data de Aquisição' );
$obPeriodicidadeAquisicao->setTitle( 'Selecione o Período de Aquisição.' );
$obPeriodicidadeAquisicao->setNull( true );
$obPeriodicidadeAquisicao->setExercicio ( Sessao::getExercicio() );

//instancia um componente periodicidade
$obPeriodicidadeIncorporacao = new Periodicidade();
$obPeriodicidadeIncorporacao->setIdComponente( 'Incorporacao' );
$obPeriodicidadeIncorporacao->setRotulo( 'Período da Data de Incorporação');
$obPeriodicidadeIncorporacao->setTitle( 'Selecione o Período de Incorporação.' );
$obPeriodicidadeIncorporacao->setNull( true );
$obPeriodicidadeIncorporacao->setExercicio ( Sessao::getExercicio() );

//instancia o componenete IMontaOrganograma
$obIMontaOrganograma = new IMontaOrganograma(true);
$obIMontaOrganograma->setCodOrgao($codOrgao);
$obIMontaOrganograma->setStyle('width:250px');

$obIMontaOrganogramaLocal = new IMontaOrganogramaLocal;
$obIMontaOrganogramaLocal->setValue($codLocal);

//instancia um radio para o tipo do relatorio
$obRdTipoRelatorioResumido = new Radio();
$obRdTipoRelatorioResumido->setRotulo( 'Tipo de Relatório' );
$obRdTipoRelatorioResumido->setTitle( 'Informe o tipo de relatório' );
$obRdTipoRelatorioResumido->setLabel( 'Resumido' );
$obRdTipoRelatorioResumido->setName( 'inTipoRelatorio' );
$obRdTipoRelatorioResumido->setValue( 0 );
$obRdTipoRelatorioResumido->setChecked( true );

$obRdTipoRelatorioCompleto = new Radio();
$obRdTipoRelatorioCompleto->setRotulo( 'Tipo de Relatório' );
$obRdTipoRelatorioCompleto->setTitle( 'Informe o tipo de relatório' );
$obRdTipoRelatorioCompleto->setLabel( 'Completo' );
$obRdTipoRelatorioCompleto->setName( 'inTipoRelatorio' );
$obRdTipoRelatorioCompleto->setValue( 1 );

//define o formulário
$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm                          );
$obFormulario->addHidden        ( $obHdnAcao );
$obFormulario->addHidden        ( $obHdnCtrl );
$obFormulario->setAjuda         ("UC-03.01.09");
$obFormulario->addTitulo        ( 'Dados para o Filtro' );
$obFormulario->addComponente    ( $obISelectEntidade    );
$obIMontaClassificacao->geraFormulario( $obFormulario );
$obFormulario->addComponente    ( $obIIntervalorPopUpBem );
$obFormulario->addComponente    ( $obPeriodicidadeAquisicao );
$obFormulario->addComponente    ( $obPeriodicidadeIncorporacao );

$obIMontaOrganograma->geraFormulario( $obFormulario );
$obIMontaOrganogramaLocal->geraFormulario( $obFormulario );

$obFormulario->agrupaComponentes( array( $obRdTipoRelatorioResumido, $obRdTipoRelatorioCompleto ) );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
