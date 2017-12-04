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

    * Página de Formularioo para Configuracao Contas Bancarias TCEMG
    * Data de Criação   : 14/02/2014

    * @author Analista: Eduardo Schitz
    * @author Desenvolvedor: Carolina Schwaab Marçal

    * @ignore
    *
    * $Id: FMManterConfiguracaoContaBancaria.php 59842 2014-09-15 19:23:06Z lisiane $
    *
    * $Revision: $
    * $Author: $
    * $Date: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoValor.class.php" );
include_once (CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGContaBancaria.class.php");
include_once (CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGTipoAplicacao.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoContaBancaria";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgJS );
$obRegra = new RContabilidadeLancamentoValor;

//$obTCEMGContaBancaria = new TTCEMGContaBancaria;

$obRegra->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setExercicio      ( Sessao::getExercicio() );
$obRegra->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade ( $_POST['inCodEntidade'] );
$obRegra->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->consultar( $rs );
$stNomEntidade = $obRegra->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->obRCGM->getNomCGM();

$obRegra->obRContabilidadePlanoContaAnalitica->setCodEstrutural                    ( $_POST['stCodEstrutural'] );
$obRegra->obRContabilidadePlanoContaAnalitica->setCodEstruturalInicial             ( $_POST['stCodEstruturalInicial'] );
$obRegra->obRContabilidadePlanoContaAnalitica->setCodEstruturalFinal               ( $_POST['stCodEstruturalFinal'] );
$obRegra->obRContabilidadePlanoContaAnalitica->setExercicio                        ( Sessao::getExercicio()        );

if ($_POST['stCodEstrutural']) {
   $obRegraCodEstrutural = new RContabilidadeLancamentoValor;
   $obRegraCodEstrutural->obRContabilidadePlanoContaAnalitica->setCodEstrutural         ( $_POST['stCodEstrutural'] );
   $obRegraCodEstrutural->obRContabilidadePlanoContaAnalitica->setExercicio             ( Sessao::getExercicio()        );
   $obRegraCodEstrutural->obRContabilidadePlanoContaAnalitica->consultar                ();
   $stNomContaCodEstrutural = $obRegraCodEstrutural->obRContabilidadePlanoContaAnalitica->getNomConta();
}

if ($_POST['stCodEstruturalInicial']) {
   $obRegraCodEstrutural = new RContabilidadeLancamentoValor;
   $obRegraCodEstrutural->obRContabilidadePlanoContaAnalitica->setCodEstruturalInicial  ( $_POST['stCodEstruturalInicial'] );
   $obRegraCodEstrutural->obRContabilidadePlanoContaAnalitica->setExercicio             ( Sessao::getExercicio()        );
   $obRegraCodEstrutural->obRContabilidadePlanoContaAnalitica->consultar                ();
   $stNomContaCodEstrutural = $obRegraCodEstrutural->obRContabilidadePlanoContaAnalitica->getNomConta();
}

if ($_POST['stCodEstruturalFinal']) {
   $obRegraCodEstrutural = new RContabilidadeLancamentoValor;
   $obRegraCodEstrutural->obRContabilidadePlanoContaAnalitica->setCodEstruturalFinal    ( $_POST['stCodEstruturalFinal'] );
   $obRegraCodEstrutural->obRContabilidadePlanoContaAnalitica->setExercicio             ( Sessao::getExercicio()        );
   $obRegraCodEstrutural->obRContabilidadePlanoContaAnalitica->consultar                ();
   $stNomContaCodEstrutural = $obRegraCodEstrutural->obRContabilidadePlanoContaAnalitica->getNomConta();
}

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "excluir";
}

if($_POST['inCodOrdenacao'] ==1 ){
   $stOrdem = "ORDER BY pc.cod_estrutural, banco.num_banco, agencia.num_agencia, conta_corrente.num_conta_corrente";
}else{
   $stOrdem = "ORDER BY banco.num_banco, agencia.num_agencia, conta_corrente.num_conta_corrente";
}

// Define Lista de Contas.
$obTCEMGContaBancaria = new TTCEMGContaBancaria;
$obTCEMGContaBancaria->setDado    ('exercicio', Sessao::getExercicio() );
$obTCEMGContaBancaria->setDado('cod_entidade', $_POST[ 'inCodEntidade' ] );
$obTCEMGContaBancaria->recuperaPlanoContaAnalitica( $rsContas, "", $stOrdem ) ;

Sessao::write('stOrdem',$stOrdem);

$count=0;
for ($i=0; $i<count($rsContas->arElementos);$i++) {
   if ($rsContas->arElementos[$i]['plano_banco']!='NOK') {
      $rsContas->arElementos[$count] = $rsContas->arElementos[$i];
      $count++;
   }
}
$rsContas->inNumLinhas=$count;

for ($count2=$count; $totalOrig>$count2;$totalOrig--) {
   unset($rsContas->arElementos[$totalOrig-1]);
}

$obLista = new Lista;
$obLista->setMostraPaginacao( false );
$obLista->setTitulo( "Conta Contábil" );

$obLista->setRecordSet( $rsContas );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Código Estrutural" );
$obLista->ultimoCabecalho->setWidth( 12 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Reduzido" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição da Conta" );
$obLista->ultimoCabecalho->setWidth( 35 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Tipo Aplicação" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Dados Bancários" );
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Código CTB Anterior" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();


$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_estrutural" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_plano" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_conta" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

$obTipoAplicacao= new TTCEMGTipoAplicacao();
$obTipoAplicacao->recuperaTodos($rsTipoAplicacao);

//Select Tipo de Aplicacao
$obCmbTipoAplicacao = new Select();
$obCmbTipoAplicacao->addOption    ( "", "Selecione" );
$obCmbTipoAplicacao->setName      ( "inCodTipoAplicacao_" );
$obCmbTipoAplicacao->setCampoId   ( "[cod_tipo_aplicacao]" );
$obCmbTipoAplicacao->setCampoDesc ( "[descricao]" );
$obCmbTipoAplicacao->setValue ( "cod_tipo_aplicacao" );
$obCmbTipoAplicacao->preencheCombo( $rsTipoAplicacao );
$obCmbTipoAplicacao->setNull      ( false );

$obLista->addDadoComponente( $obCmbTipoAplicacao );
$obLista->ultimoDado->setCampo( "cod_tipo_aplicacao" );
$obLista->commitDadoComponente();


$obLista->addDado();
$obLista->ultimoDado->setCampo( "Banco:[num_banco] Agência:[num_agencia] Conta Corrente:[num_conta_corrente]" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

$obTxtCodCTBAnterior = new TextBox;
$obTxtCodCTBAnterior->setName     ( 'inCodCTBAnterior' );
$obTxtCodCTBAnterior->setRotulo   ( 'Código CTB Anterior' );
$obTxtCodCTBAnterior->setMaxLength( 10   );
$obTxtCodCTBAnterior->setSize     ( 10   );
$obTxtCodCTBAnterior->setInteiro  ( true );
$obTxtCodCTBAnterior->setValue    ( "[cod_ctb_anterior]" );


$obLista->addDadoComponente( $obTxtCodCTBAnterior );
$obLista->ultimoDado->setCampo( 'cod_ctb_anterior' );
$obLista->commitDadoComponente();

$obHdnCodConta = new Hidden;
$obHdnCodConta->setName ( "inCodConta" );
$obHdnCodConta->setValue( "cod_conta"  );

$obLista->addDadoComponente( $obHdnCodConta );
$obLista->commitDadoComponente();

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
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

$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName ( "inCodEntidade" );
$obHdnCodEntidade->setValue( $_POST['inCodEntidade']  );

$obHdnCodGrupo= new Hidden;
$obHdnCodGrupo->setName ( "inCodGrupo" );
$obHdnCodGrupo->setValue( $_POST[ 'inCodGrupo' ]  );

//Define o objeto Label Entidade
$obLblCodEntidade = new Label;
$obLblCodEntidade->setRotulo( "Entidade" );
$obLblCodEntidade->setValue( $_POST['inCodEntidade']." - $stNomEntidade" );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda('UC-02.02.04');
$obFormulario->addHidden( $obHdnAcao              );
$obFormulario->addHidden( $obHdnCtrl              );
$obFormulario->addHidden( $obHdnCodEntidade       );
$obFormulario->addHidden( $obHdnCodGrupo          );

$obFormulario->addTitulo( "Registros de saldos iniciais" );
$obFormulario->addComponente( $obLblCodEntidade   );
if (($_POST['stCodEstruturalInicial']==$_POST['stCodEstruturalFinal'])and($_POST['stCodEstruturalInicial']!="")) {
$obFormulario->addComponente( $obLblCodConta      );}
if (($_POST['inCodPlanoInicial']==$_POST['inCodPlanoFinal'])and($_POST['inCodPlanoInicial']!="")) {
$obFormulario->addComponente( $obLblCodPlano      );}

$obFormulario->addLista     ( $obLista            );

$stLocation = $pgFilt.'?'.Sessao::getId().'&stAcao='.$stAcao ;
$obFormulario->Cancelar($stLocation, true);

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
