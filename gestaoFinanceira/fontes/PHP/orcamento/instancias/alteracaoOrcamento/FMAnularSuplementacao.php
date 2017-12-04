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
    * Página de Formulário de anulação de suplementações
    * Data de Criação   : 15/04/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 31801 $
    $Name$
    $Author: melo $
    $Date: 2007-05-21 16:04:19 -0300 (Seg, 21 Mai 2007) $

    * Casos de uso: uc-02.01.07
*/

/*
$Log$
Revision 1.7  2007/05/21 18:54:40  melo
Bug #9229#

Revision 1.6  2006/07/28 17:39:25  leandro.zis
Bug #6689#

Revision 1.5  2006/07/05 20:42:23  cleisson
Adicionada tag Log aos arquivos

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_ORC_NEGOCIO. "ROrcamentoSuplementacao.class.php");

$stPrograma = "AnularSuplementacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once( $pgJs );

$inCodSuplementacao = $_REQUEST['inCodSuplementacao'];
$obRegra = new ROrcamentoSuplementacao;
$obRegra->setCodSuplementacao( $_REQUEST['inCodSuplementacao'] );
$obRegra->setExercicio( Sessao::getExercicio() );
$obRegra->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodEntidade']);
$obRegra->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setExercicio($_REQUEST['stExercicio']);
$obRegra->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->consultarNomes($rsEntidade);
$obRegra->consultar();
$obRegra->listarSuplementacaoDespesa( $rsSuplementacao );

Sessao::remove('arDespesaSuplementada');
Sessao::remove('arDespesaReducao');
Sessao::remove('rsSuplementacao');
//sessao->transf3 = array();

$inCodTipo     = $obRegra->getCodTipo();
$stNomTipo     = $obRegra->getNomTipo();
$stDtSuplementacao = $obRegra->getDtLancamento();
$inCodNorma    = $obRegra->obRNorma->getCodNorma();
$inCodEntidade = $obRegra->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade();
$stNomEntidade = $obRegra->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getNomeEntidade();

Sessao::write('arDespesaSuplementada',$obRegra->getDespesaSuplementada() );
Sessao::write('arDespesaReducao',$obRegra->getDespesaReducao() );
Sessao::write('rsSuplementacao',$rsSuplementacao);
//sessao->transf3['arDespesaSuplementada'] = serialize( $obRegra->getDespesaSuplementada() );
//sessao->transf3['arDespesaReducao']      = serialize( $obRegra->getDespesaReducao()      );
//sessao->transf3['rsSuplementacao']       = serialize( $rsSuplementacao                   );

SistemaLegado::executaFramePrincipal( 'buscaDado("montaLista");' );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$stFiltro = "";
$arFiltro = Sessao::read('filtro');
foreach ($arFiltro['filtro'] as $key => $value) {
    $stFiltro .= '&'.$key.'='.$value;
}
$stFiltro .= '&pg='.Sessao::read('pg').'&pos='.Sessao::read('pos').'&paginando='.Sessao::read('paginando');

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName( "inCodEntidade" );
$obHdnCodEntidade->setValue( "$inCodEntidade" );

$obHdnCodSuplementacao = new Hidden;
$obHdnCodSuplementacao->setName ( "inCodSuplementacao" );
$obHdnCodSuplementacao->setValue( $inCodSuplementacao  );

$obHdnCodNorma = new Hidden;
$obHdnCodNorma->setName ( "inCodNorma" );
$obHdnCodNorma->setValue( $inCodNorma  );

$obHdnCodTipo = new Hidden;
$obHdnCodTipo->setName ( "inCodTipo" );
$obHdnCodTipo->setValue( $inCodTipo  );

$obHdnDtSuplementacao = new Hidden;
$obHdnDtSuplementacao->setName ( "stDtLancamento"   );
$obHdnDtSuplementacao->setValue( $stDtSuplementacao );

$obHdnDtSuplementacao = new Hidden;
$obHdnDtSuplementacao->setName ( "stDataAnulacao"   );
$obHdnDtSuplementacao->setValue( $stDtSuplementacao );

$obHdnVlTotal = new Hidden;
$obHdnVlTotal->setName ( "nuVlTotal" );
$obHdnVlTotal->setValue( ''         );

// Define objeto Label para Entidade
$obLblEntidade = new Label;
$obLblEntidade->setRotulo    ( "Entidade"                       );
$obLblEntidade->setId        ( "stEntidade"                     );
$obLblEntidade->setValue     ( $inCodEntidade.' - '.$stNomEntidade );

// Define objeto Label para Codigo da suplementação
$obLblCodSuplementacao = new Label;
$obLblCodSuplementacao->setRotulo    ( "Código Suplementação" );
$obLblCodSuplementacao->setId        ( "inCodSuplementacao"   );
$obLblCodSuplementacao->setValue     ( $inCodSuplementacao    );

// Define objeto Label para codigo do tipo da suplementação
$obLblTipoSuplementacao = new Label;
$obLblTipoSuplementacao->setRotulo    ( "Tipo Suplementação"        );
$obLblTipoSuplementacao->setId        ( "inCodTipo"                 );
$obLblTipoSuplementacao->setValue     ( $inCodTipo.' - '.$stNomTipo );

// Define objeto Label para Data da suplementação
$obLblDtSuplementacao = new Label;
$obLblDtSuplementacao->setRotulo ( "Data Suplementação" );
$obLblDtSuplementacao->setId     ( "stDtSuplementacao"  );
$obLblDtSuplementacao->setValue  ( $stDtSuplementacao   );

// Define Objeto Label para Data de anulação
$obLblDtAnulacao = new Label;
$obLblDtAnulacao->setRotulo ( "Data Anulação" );
$obLblDtAnulacao->setId     ( "stDataAnulacao"  );
$obLblDtAnulacao->setValue  ( $stDtSuplementacao   );
//$obTxtDtAnulacao = new Data;
//$obTxtDtAnulacao->setRotulo    ( "Data Anulação"  );
//$obTxtDtAnulacao->setTitle     ( "Informe a data da anulação." );
//$obTxtDtAnulacao->setId        ( "stDataAnulacao" );
//$obTxtDtAnulacao->setName      ( "stDataAnulacao" );
//$obTxtDtAnulacao->setValue     ( date('d/m/Y')    );
//$obTxtDtAnulacao->setNull      ( false            );

// Define Objeto TextArea para Motivo
$obTxtMotivo = new TextArea;
$obTxtMotivo->setName   ( "stMotivo" );
$obTxtMotivo->setId     ( "stMotivo" );
$obTxtMotivo->setTitle  ( "Informe o motivo da anulação." );
$obTxtMotivo->setValue  ( $stMotivo  );
$obTxtMotivo->setRotulo ( "Motivo" );
$obTxtMotivo->setNull   ( true );
$obTxtMotivo->setRows   ( 2 );
$obTxtMotivo->setCols   ( 100 );

// Define objeto Span para lista suplementação
$obSpnSuplementacao = new Span;
$obSpnSuplementacao->setId( "spnSuplementacao" );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm                 );
$obFormulario->setAjuda      ( "UC-02.01.07"           );
$obFormulario->addHidden     ( $obHdnAcao              );
$obFormulario->addHidden     ( $obHdnCtrl              );
$obFormulario->addHidden     ( $obHdnCodEntidade       );
$obFormulario->addHidden     ( $obHdnCodSuplementacao  );
$obFormulario->addHidden     ( $obHdnCodNorma          );
$obFormulario->addHidden     ( $obHdnCodTipo           );
$obFormulario->addHidden     ( $obHdnDtSuplementacao   );
$obFormulario->addHidden     ( $obHdnVlTotal           );
$obFormulario->addTitulo     ( "Dados para anulação"   );
$obFormulario->addComponente ( $obLblEntidade          );
$obFormulario->addComponente ( $obLblCodSuplementacao  );
$obFormulario->addComponente ( $obLblTipoSuplementacao );
$obFormulario->addComponente ( $obLblDtSuplementacao   );
$obFormulario->addComponente ( $obLblDtAnulacao        );
$obFormulario->addComponente ( $obTxtMotivo            );

$obFormulario->addTitulo     ( "Registro de dotações"  );
$obFormulario->addSpan       ( $obSpnSuplementacao     );

$obCancelar  = new Cancelar;
$stLocation = $pgList.'?'.Sessao::getId().$stFiltro;
$obCancelar->obEvento->setOnClick("Cancelar('".$stLocation."','telaPrincipal');");

$obBtnOK =new Ok();
$obBtnOK->obEvento->setOnClick("Salvar();");
$obBtnOK->setId('Ok');
$obBtnOK->setDisabled(true);

$obFormulario->defineBarra(array($obBtnOK, $obCancelar));;
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
