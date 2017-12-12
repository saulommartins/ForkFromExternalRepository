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
    * Página de Filtro para Consulta de Ordens de Pagamento
    * Data de Criação: 30/05/2005

    * @author Analista: Dieine
    * @author Desenvolvedor: Marcelo B. Paulino

    * @ignore

    $Revision: 31583 $
    $Name$
    $Author: cako $
    $Date: 2007-12-07 16:11:31 -0200 (Sex, 07 Dez 2007) $

    * Casos de uso: uc-02.03.05
*/

/*
$Log$
Revision 1.14  2007/01/25 11:50:46  cako
Bug #8175#

Revision 1.13  2006/11/22 20:01:19  cako
Bug #7244#

Revision 1.12  2006/07/14 15:44:07  leandro.zis
Bug #6191#

Revision 1.11  2006/07/14 14:33:42  leandro.zis
Bug #6193#

Revision 1.10  2006/07/05 20:48:56  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoOrdemPagamento.class.php" );

$stPrograma = "ConsultarOrdemPagamento";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";
include_once( $pgJs );

Sessao::write('filtro', array());
Sessao::write('pg', '');
Sessao::write('pos', '');
Sessao::write('paginando', false);
Sessao::remove('link');;

$rsRecordset              = new RecordSet;
$obREmpenhoOrdemPagamento = new REmpenhoOrdemPagamento;

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//

$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "telaPrincipal" );

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

// DEFINE OBJETOS DAS CLASSES
$obREmpenhoOrdemPagamento = new REmpenhoOrdemPagamento;
$rsRecordset = new RecordSet;
$obREmpenhoOrdemPagamento->obROrcamentoEntidade->setExercicio( Sessao::getExercicio()     );
$obREmpenhoOrdemPagamento->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm')   );
$obREmpenhoOrdemPagamento->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades );

// DEFINE OBJETOS DO FORMULARIO

// Define SELECT multiplo para codigo da entidade
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName                       ( 'inCodEntidade'           );
$obCmbEntidades->setRotulo                     ( "Entidades"                  );
$obCmbEntidades->setTitle                      ( "Selecione as entidades."    );
$obCmbEntidades->setNull                       ( false                        );

// Caso o usuário tenha permissão para somente uma entidade, a mesma já virá selecionada
if ($rsEntidades->getNumLinhas()==1) {
       $rsRecordset = $rsEntidades;
       $rsEntidades = new RecordSet;
}

// lista de atributos disponiveis
$obCmbEntidades->SetNomeLista1                 ( 'inCodigoEntidadeDisponivel' );
$obCmbEntidades->setCampoId1                   ( 'cod_entidade'               );
$obCmbEntidades->setCampoDesc1                 ( 'nom_cgm'                    );
$obCmbEntidades->SetRecord1                    ( $rsEntidades                 );
// lista de atributos selecionados
$obCmbEntidades->SetNomeLista2                 ( 'inCodEntidade'           );
$obCmbEntidades->setCampoId2                   ( 'cod_entidade'               );
$obCmbEntidades->setCampoDesc2                 ( 'nom_cgm'                    );
$obCmbEntidades->SetRecord2                    ( $rsRecordset                 );

//Define o objeto TEXT para Exercicio
$obTxtExercicioEmpenho = new TextBox;
$obTxtExercicioEmpenho->setName     ( "stExercicioEmpenho"   );
$obTxtExercicioEmpenho->setValue    ( Sessao::getExercicio()     );
$obTxtExercicioEmpenho->setRotulo   ( "Exercício do Empenho" );
$obTxtExercicioEmpenho->setTitle    ( "Informe o exercício do empenho." );
$obTxtExercicioEmpenho->setMaxLength( 4                      );
$obTxtExercicioEmpenho->setSize     ( 4                      );
$obTxtExercicioEmpenho->setInteiro  ( true                   );
$obTxtExercicioEmpenho->setNull     ( false                  );

//Define o objeto TEXT para Exercicio da OP
$obTxtExercicioOrdem = new TextBox;
$obTxtExercicioOrdem->setName     ( "stExercicioOrdem"   );
$obTxtExercicioOrdem->setValue    ( Sessao::getExercicio()     );
$obTxtExercicioOrdem->setRotulo   ( "Exercício da OP" );
$obTxtExercicioOrdem->setTitle    ( "Informe o exercício da ordem de pagamento." );
$obTxtExercicioOrdem->setMaxLength( 4                      );
$obTxtExercicioOrdem->setSize     ( 4                      );
$obTxtExercicioOrdem->setInteiro  ( true                   );

//Define o objeto TEXT para Codigo da Ordem de Pagamento inicial
$obTxtCodigoOrdemPagamentoInicial = new TextBox;
$obTxtCodigoOrdemPagamentoInicial->setName     ( "inCodigoOrdemPagamentoInicial" );
$obTxtCodigoOrdemPagamentoInicial->setValue    ( $inCodigoOrdemPagamentoInicial  );
$obTxtCodigoOrdemPagamentoInicial->setRotulo   ( "Número da Ordem"               );
$obTxtCodigoOrdemPagamentoInicial->setTitle    ( "Informe o número da ordem."    );
$obTxtCodigoOrdemPagamentoInicial->setInteiro  ( true                            );
$obTxtCodigoOrdemPagamentoInicial->setNull     ( true                            );

//Define objeto Label
$obLblOrdemPagamento = new Label;
$obLblOrdemPagamento->setValue( "&nbsp;a&nbsp;" );

//Define o objeto TEXT para Codigo da Ordem de Pagamento final
$obTxtCodigoOrdemPagamentoFinal = new TextBox;
$obTxtCodigoOrdemPagamentoFinal->setName       ( "inCodigoOrdemPagamentoFinal" );
$obTxtCodigoOrdemPagamentoFinal->setValue      ( $inCodigoOrdemPagamentoFinal  );
$obTxtCodigoOrdemPagamentoFinal->setRotulo     ( "Número da Ordem"             );
$obTxtCodigoOrdemPagamentoFinal->setInteiro    ( true                          );
$obTxtCodigoOrdemPagamentoFinal->setNull       ( true                          );

//Define o objeto TEXT para Código do empenho
$obTxtCodEmpenhoInicial = new TextBox;
$obTxtCodEmpenhoInicial->setName     ( "inCodEmpenhoInicial" );
$obTxtCodEmpenhoInicial->setValue    ( $inCodEmpenhoInicial  );
$obTxtCodEmpenhoInicial->setRotulo   ( "Número do Empenho"   );
$obTxtCodEmpenhoInicial->setTitle    ( "Informe o número do empenho.");
$obTxtCodEmpenhoInicial->setInteiro  ( true                  );
$obTxtCodEmpenhoInicial->setNull     ( true                  );
//Define objeto Label
$obLblEmpenho = new Label;
$obLblEmpenho->setValue( "&nbsp;a&nbsp;" );
//Define o objeto TEXT para Codigo do Empenho final
$obTxtCodEmpenhoFinal = new TextBox;
$obTxtCodEmpenhoFinal->setName       ( "inCodEmpenhoFinal" );
$obTxtCodEmpenhoFinal->setValue      ( $inCodEmpenhoFinal  );
$obTxtCodEmpenhoFinal->setRotulo     ( "Número do Empenho" );
$obTxtCodEmpenhoFinal->setInteiro    ( true                );
$obTxtCodEmpenhoFinal->setNull       ( true                );

//Define o objeto TEXT para Código do empenho
$obTxtCodLiquidacaoInicial = new TextBox;
$obTxtCodLiquidacaoInicial->setName     ( "inCodLiquidacaoInicial" );
$obTxtCodLiquidacaoInicial->setValue    ( $inCodLiquidacaoInicial  );
$obTxtCodLiquidacaoInicial->setRotulo   ( "Número da Liquidação"   );
$obTxtCodLiquidacaoInicial->setTitle    ( "Informe o número da liquidação." );
$obTxtCodLiquidacaoInicial->setInteiro  ( true                     );
$obTxtCodLiquidacaoInicial->setNull     ( true                     );
//Define objeto Label
$obLblLiquidacao = new Label;
$obLblLiquidacao->setValue( "&nbsp;a&nbsp;" );
//Define o objeto TEXT para Codigo do Empenho final
$obTxtCodLiquidacaoFinal = new TextBox;
$obTxtCodLiquidacaoFinal->setName       ( "inCodLiquidacaoFinal" );
$obTxtCodLiquidacaoFinal->setValue      ( $inCodLiquidacaoFinal  );
$obTxtCodLiquidacaoFinal->setRotulo     ( "Número da Liquidacao" );
$obTxtCodLiquidacaoFinal->setInteiro    ( true                   );
$obTxtCodLiquidacaoFinal->setNull       ( true                   );

// Define Objeto BuscaInner para Credor
$obBscCredor = new BuscaInner;
$obBscCredor->setRotulo               ( "Credor"      );
$obBscCredor->setTitle                ( "Informe o credor." );
$obBscCredor->setId                   ( "stNomCredor" );
$obBscCredor->setValue                ( $stNomCredor  );
$obBscCredor->obCampoCod->setName     ( "inCodCredor" );
$obBscCredor->obCampoCod->setSize     ( 10            );
$obBscCredor->obCampoCod->setMaxLength( 8             );
$obBscCredor->obCampoCod->setValue    ( $inCodCredor  );
$obBscCredor->obCampoCod->setAlign    ( "left"        );
$obBscCredor->obCampoCod->obEvento->setOnBlur("buscaDado('buscaCredor');");
$obBscCredor->setFuncaoBusca("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodCredor','stNomCredor','','".Sessao::getId()."','800','550');");

// Define objeto Data para Vencimento
$obDataVencimento = new Data;
$obDataVencimento->setName     ( "dtDataVencimento" );
$obDataVencimento->setRotulo   ( "Vencimento" );
$obDataVencimento->setTitle    ( 'Informe o vencimento.' );
$obDataVencimento->setNull     ( true );

// Define objeto Data inicial para Periodo
$obDataInicial = new Data;
$obDataInicial->setName                        ( "dtDataInicial" );
$obDataInicial->setRotulo                      ( "Período"       );
$obDataInicial->setTitle                       ( 'Informe o período.'              );
$obDataInicial->setNull                        ( true            );
// Define Objeto Label
$obLabel = new Label;
$obLabel->setValue( " até " );
// Define objeto Data final para Periodo
$obDataFinal = new Data;
$obDataFinal->setName                          ( "dtDataFinal"   );
$obDataFinal->setRotulo                        ( "Período"       );
$obDataFinal->setTitle                         ( ''              );
$obDataFinal->setNull                          ( true            );

include_once(CAM_GF_ORC_COMPONENTES."IMontaRecursoDestinacao.class.php");
$obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
$obIMontaRecursoDestinacao->setFiltro ( true );

/*
// Define Objeto BuscaInner para Recurso
$obBscRecurso = new BuscaInner;
$obBscRecurso->setRotulo               ( "Recurso"      );
$obBscRecurso->setTitle                ( "Informe o recurso." );
$obBscRecurso->setId                   ( "stNomRecurso" );
$obBscRecurso->setValue                ( $stNomRecurso  );
$obBscRecurso->obCampoCod->setName     ( "inCodRecurso" );
$obBscRecurso->obCampoCod->setSize     ( 10             );
$obBscRecurso->obCampoCod->setMaxLength( 8              );
$obBscRecurso->obCampoCod->setValue    ( $inCodRecurso  );
$obBscRecurso->obCampoCod->setAlign    ( "left"         );
$obBscRecurso->obCampoCod->obEvento->setOnBlur("buscaDado('buscaRecurso');");
$obBscRecurso->setFuncaoBusca("abrePopUp('".CAM_GF_ORC_POPUPS."recurso/FLRecurso.php','frm','inCodRecurso','stNomRecurso','','".Sessao::getId()."','800','550');");
*/
// Define Objeto Select para Situação da Ordem de Pagamento
$obCmbSituacao = new Select;
$obCmbSituacao->setRotulo    ( "Situação"           );
$obCmbSituacao->setName      ( "inCodSituacao"      );
$obCmbSituacao->setTitle     ( "Selecione a situação." );
$obCmbSituacao->setValue     ( $inCodSituacao       );
$obCmbSituacao->addOption    ( "", "Selecione"      );
$obCmbSituacao->addOption    ( "1", "A Pagar"       );
$obCmbSituacao->addOption    ( "2", "Pagas"         );
$obCmbSituacao->addOption    ( "3", "Anuladas"      );
$obCmbSituacao->setStyle     ( "width: 150px"       );
$obCmbSituacao->setNull      ( true                 );

$js .= "document.frm.inCodigoEntidadeDisponivel.focus();";
SistemaLegado::executaFramePrincipal( $js );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction           ( $pgList         );
$obForm->setTarget           ( "telaPrincipal" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm         );
$obFormulario->addHidden     ( $obHdnCtrl      );
$obFormulario->addHidden     ( $obHdnAcao      );

$obFormulario->addTitulo     ( "Dados para Filtro" );

$obFormulario->addComponente    ( $obCmbEntidades        );
$obFormulario->addComponente    ( $obTxtExercicioEmpenho );
$obFormulario->addComponente    ( $obTxtExercicioOrdem );
$obFormulario->agrupaComponentes( array( $obTxtCodEmpenhoInicial, $obLblEmpenho, $obTxtCodEmpenhoFinal )                            );
$obFormulario->agrupaComponentes( array( $obTxtCodLiquidacaoInicial, $obLblLiquidacao, $obTxtCodLiquidacaoFinal )                   );
$obFormulario->agrupaComponentes( array( $obTxtCodigoOrdemPagamentoInicial, $obLblOrdemPagamento, $obTxtCodigoOrdemPagamentoFinal ) );
$obFormulario->addComponente    ( $obBscCredor   );
$obFormulario->agrupaComponentes( array( $obDataInicial,$obLabel, $obDataFinal ) );
$obFormulario->addComponente    ( $obDataVencimento );
$obIMontaRecursoDestinacao->geraFormulario ( $obFormulario );
//$obFormulario->addComponente    ( $obBscRecurso  );
$obFormulario->addComponente    ( $obCmbSituacao );
$obFormulario->Ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
