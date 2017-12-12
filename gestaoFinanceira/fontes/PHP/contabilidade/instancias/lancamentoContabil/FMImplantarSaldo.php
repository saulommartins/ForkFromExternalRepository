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
    * Página de Formularioo para Implantar Saldo Inicial
    * Data de Criação   : 17/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Gelson W. Gonçalves

    * @ignore

    * $Id: FMImplantarSaldo.php 62826 2015-06-24 17:45:14Z jean $

    * Casos de uso: uc-02.02.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoValor.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ImplantarSaldo";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgJS );

$obRegra = new RContabilidadeLancamentoValor;

$obRegra->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setExercicio      ( Sessao::getExercicio() );
$obRegra->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade ( $_POST['inCodEntidade'] );
$obRegra->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->consultar( $rs );

$stNomEntidade = $obRegra->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->obRCGM->getNomCGM();

$obRegra->obRContabilidadePlanoContaAnalitica->setCodEstrutural                    ( $_POST['stCodEstrutural'] );
$obRegra->obRContabilidadePlanoContaAnalitica->setCodEstruturalInicial             ( $_POST['stCodEstruturalInicial'] );
$obRegra->obRContabilidadePlanoContaAnalitica->setCodEstruturalFinal               ( $_POST['stCodEstruturalFinal'] );
$obRegra->obRContabilidadePlanoContaAnalitica->setExercicio                        ( Sessao::getExercicio()        );

if ($_REQUEST['inCodPlano']) {
   $obRegraCodPlano = new RContabilidadeLancamentoValor;
   $obRegraCodPlano->obRContabilidadePlanoContaAnalitica->setCodPlano              ( $_POST['inCodPlano'] );
   $obRegraCodPlano->obRContabilidadePlanoContaAnalitica->setExercicio             ( Sessao::getExercicio()        );
   $obRegraCodPlano->obRContabilidadePlanoContaAnalitica->consultar                ();
   $stNomContaCodPlano = $obRegraCodPlano->obRContabilidadePlanoContaAnalitica->getNomConta();
}

if ($_REQUEST['inCodPlanoInicial']) {
   $obRegraCodPlano = new RContabilidadeLancamentoValor;
   $obRegraCodPlano->obRContabilidadePlanoContaAnalitica->setCodPlanoInicial       ( $_POST['inCodPlanoInicial'] );
   $obRegraCodPlano->obRContabilidadePlanoContaAnalitica->setExercicio             ( Sessao::getExercicio()        );
   $obRegraCodPlano->obRContabilidadePlanoContaAnalitica->consultar                ();
   $stNomContaCodPlano = $obRegraCodPlano->obRContabilidadePlanoContaAnalitica->getNomConta();
}

if ($_REQUEST['inCodPlanoFinal']) {
   $obRegraCodPlano = new RContabilidadeLancamentoValor;
   $obRegraCodPlano->obRContabilidadePlanoContaAnalitica->setCodPlanoFinal         ( $_POST['inCodPlanoFinal'] );
   $obRegraCodPlano->obRContabilidadePlanoContaAnalitica->setExercicio             ( Sessao::getExercicio()        );
   $obRegraCodPlano->obRContabilidadePlanoContaAnalitica->consultar                ();
   $stNomContaCodPlano = $obRegraCodPlano->obRContabilidadePlanoContaAnalitica->getNomConta();
}

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

// Define Lista de Contas.
$obRegra = new RContabilidadeLancamentoValor;
$obRegra->obRContabilidadePlanoContaAnalitica->setExercicio    ( Sessao::getExercicio() );

$obRegra->obRContabilidadePlanoContaAnalitica->setCodGrupo( $_POST[ 'inCodGrupo' ] );
$obRegra->obRContabilidadePlanoContaAnalitica->setCodPlanoInicial   ( $_POST[ 'inCodPlanoInicial' ] );
$obRegra->obRContabilidadePlanoContaAnalitica->setCodPlanoFinal     ( $_POST[ 'inCodPlanoFinal' ] );
$obRegra->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $_POST[ 'inCodEntidade' ] );
$obRegra->listarLoteImplantacaoPlanoBanco( $rsContas ) ;

$rsContas->addFormatacao( 'vl_lancamento', 'NUMERIC_BR' );
$count=0;
for ($i=0; $i<count($rsContas->arElementos);$i++) {
   if ($rsContas->arElementos[$i]['plano_banco']!='NOK') {
      $rsContas->arElementos[$count] = $rsContas->arElementos[$i];
      $count++;
   }
}
$rsContas->inNumLinhas=$count;
$totalOrig=count($rsContas->arElementos);
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
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Reduzido" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição da Conta" );
$obLista->ultimoCabecalho->setWidth( 80 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Valor" );
$obLista->ultimoCabecalho->setWidth( 80 );
$obLista->commitCabecalho();
$obLista->addCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_estrutural" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_plano" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_conta" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

// Define Objeto Numerico para Valor
$obTxtValor = new Numerico;
$obTxtValor->setName     ( "nuValor_" );
$obTxtValor->setAlign    ( 'RIGHT');
$obTxtValor->setTitle    ( "" );
$obTxtValor->setMaxLength( 19 );
$obTxtValor->setSize     ( 21 );
$obTxtValor->setValue    ( "vl_lancamento" );

$obLista->addDadoComponente( $obTxtValor );
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
$obHdnCodEntidade->setValue( $request->get('inCodEntidade') );

$obHdnCodGrupo= new Hidden;
$obHdnCodGrupo->setName ( "inCodGrupo" );
$obHdnCodGrupo->setValue( $request->get('inCodGrupo') );

$obHdnCodEstrutural = new Hidden;
$obHdnCodEstrutural->setName ( "stCodEstrutural" );
$obHdnCodEstrutural->setValue( $request->get('stCodEstrutural') );

$obHdnCodEstruturalInicial = new Hidden;
$obHdnCodEstruturalInicial->setName ( "stCodEstruturalInicial" );
$obHdnCodEstruturalInicial->setValue( $request->get('stCodEstruturalInicial') );

$obHdnCodEstruturalFinal = new Hidden;
$obHdnCodEstruturalFinal->setName ( "stCodEstruturalFinal" );
$obHdnCodEstruturalFinal->setValue( $request->get('stCodEstruturalFinal') );

$obHdnCodPlanoInicial = new Hidden;
$obHdnCodPlanoInicial->setName ( "inCodPlanoInicial" );
$obHdnCodPlanoInicial->setValue( $request->get('inCodPlanoInicial') );

$obHdnCodPlanoFinal = new Hidden;
$obHdnCodPlanoFinal->setName ( "inCodPlanoFinal" );
$obHdnCodPlanoFinal->setValue( $request->get('inCodPlanoFinal') );

//Define o objeto Label Entidade
$obLblCodEntidade = new Label;
$obLblCodEntidade->setRotulo( "Entidade" );
$obLblCodEntidade->setValue( $request->get('inCodEntidade')." - ".$stNomEntidade );

//Define o objeto Label Código Classificação Contábil
$obLblCodConta = new Label;
$obLblCodConta->setRotulo( "Conta Contábil" );
$obLblCodConta->setValue( $request->get('stCodEstruturalInicial')." - ".$request->get('stDescricaoClassificacaoInicial') );

//Define o objeto Label Código Reduzido
$obLblCodPlano = new Label;
$obLblCodPlano->setRotulo( "Reduzido" );
$obLblCodPlano->setValue( $request->get('inCodPlanoInicial')." - ".$request->get('stDescricaoContaAnaliticaInicial') );

//Define o objeto Label Data de Lançamento
$obLblDtLanc = new Label;
$obLblDtLanc->setRotulo( "Data do Lançamento" );
$obLblDtLanc->setValue( '01/01/'.Sessao::getExercicio() );

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
$obFormulario->addHidden( $obHdnCodEstrutural     );
$obFormulario->addHidden( $obHdnCodEstruturalInicial);
$obFormulario->addHidden( $obHdnCodEstruturalFinal);
$obFormulario->addHidden( $obHdnCodPlanoInicial   );
$obFormulario->addHidden( $obHdnCodPlanoFinal     );

$obFormulario->addTitulo( "Registros de saldos iniciais" );
$obFormulario->addComponente( $obLblCodEntidade   );
if (($_POST['stCodEstruturalInicial']==$_POST['stCodEstruturalFinal'])and($_POST['stCodEstruturalInicial']!="")) {
$obFormulario->addComponente( $obLblCodConta      );}
if (($_POST['inCodPlanoInicial']==$_POST['inCodPlanoFinal'])and($_POST['inCodPlanoInicial']!="")) {
$obFormulario->addComponente( $obLblCodPlano      );}
$obFormulario->addComponente( $obLblDtLanc        );

$obFormulario->addLista     ( $obLista            );

$stLocation = $pgFilt.'?'.Sessao::getId().'&stAcao='.$stAcao ;
$obFormulario->Cancelar($stLocation);

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
