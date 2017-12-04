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
 * Página de Formulario de Seleção de Impressora para Relatorio
 * Data de Criação   : 12/08/2005

 * @author Analista: Muriel Preuss
 * @author Desenvolvedor: Cleisson Barboza

 * @ignore

 * Casos de uso : uc-02.03.26

 $Id: FLProgramacaoPagamentos.php 59612 2014-09-02 12:00:51Z gelson $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php";

$rsRecordset = $rsEntidades = $rsRecurso = new RecordSet;

$arFiltroNom = Sessao::read('filtroNomRelatorio');

$obREmpenhoEmpenho = new REmpenhoEmpenho;
$obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm')                           );
$obREmpenhoEmpenho->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades , " ORDER BY cod_entidade"   );
while ( !$rsEntidades->eof() ) {
    $arFiltroNom['entidade'][$rsEntidades->getCampo( 'cod_entidade' )] = $rsEntidades->getCampo( 'nom_cgm' );
    $rsEntidades->proximo();
}
$rsEntidades->setPrimeiroElemento();

$obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->listar( $rsRecurso       );
while ( !$rsRecurso->eof() ) {
    $arFiltroNom['recurso'][$rsRecurso->getCampo( 'cod_recurso' )] = $rsRecurso->getCampo( 'nom_recurso' );
    $rsRecurso->proximo();
}
$rsRecurso->setPrimeiroElemento();

Sessao::write('filtroNomRelatorio', $arFiltroNom);

$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php"  );
$obForm->setTarget( "oculto" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName  ( "stCaminho" );
$obHdnCaminho->setValue ( CAM_GF_EMP_INSTANCIAS."relatorio/OCProgramacaoPagamentos.php" );

$obHdnModulo = new Hidden;
$obHdnModulo->setName ( "inCodModulo"       );
$obHdnModulo->setValue( $_REQUEST['modulo'] );

//Define o objeto SelectMultiplo para armazenar os ELEMENTOS
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName   ('inCodEntidade'         );
$obCmbEntidades->setRotulo ( "Entidades"            );
$obCmbEntidades->setTitle  ( "Selecione a entidade" );
$obCmbEntidades->setNull   ( false                  );

// Caso o usuário tenha permissão para somente uma entidade, a mesma já virá selecionada
if ($rsEntidades->getNumLinhas()==1) {
    $rsRecordset = $rsEntidades;
    $rsEntidades = new RecordSet;
}

// lista de atributos disponiveis
$obCmbEntidades->SetNomeLista1 ('inCodEntidadeDisponivel' );
$obCmbEntidades->setCampoId1   ( 'cod_entidade'           );
$obCmbEntidades->setCampoDesc1 ( 'nom_cgm'                );
$obCmbEntidades->SetRecord1    ( $rsEntidades             );

// lista de atributos selecionados
$obCmbEntidades->SetNomeLista2 ('inCodEntidade' );
$obCmbEntidades->setCampoId2   ('cod_entidade'  );
$obCmbEntidades->setCampoDesc2 ('nom_cgm'       );
$obCmbEntidades->SetRecord2    ( $rsRecordset   );

$obTxtExercicio = new TextBox;
$obTxtExercicio->setRotulo    ( "Exercício"           );
$obTxtExercicio->setTitle     ( "Informe o Exercício" );
$obTxtExercicio->setName      ( "stExercicio"         );
$obTxtExercicio->setValue     ( Sessao::getExercicio() );
$obTxtExercicio->setSize      ( 6                     );
$obTxtExercicio->setMaxLength ( 4                     );
$obTxtExercicio->setNull      ( false                 );

$obPeriodicidade = new Periodicidade();
$obPeriodicidade->setExercicio      ( Sessao::getExercicio()        );
$obPeriodicidade->setTitle          ( "Informe a periodicidade de vencimento das liquidações dos empenhos");
$obPeriodicidade->setNull           ( false                     );
$obPeriodicidade->setValue          ( 1                         );
$obPeriodicidade->setValidaExercicio( true                      );

//Define Objeto BuscaInner para Despesa
$obBscDespesa = new BuscaInner;
$obBscDespesa->setRotulo ( "Dotação"                                               );
$obBscDespesa->setTitle  ( "Informe a Dotação orçamentária que deseja pesquisar"   );
$obBscDespesa->setNulL   ( true                                                    );
$obBscDespesa->setId     ( "stNomDespesa"                                          );
$obBscDespesa->setValue  ( $stNomDespesa                                           );
$obBscDespesa->obCampoCod->setName ( "inCodDespesa"                                );
$obBscDespesa->obCampoCod->setSize ( 10                                            );
$obBscDespesa->obCampoCod->setMaxLength( 5                                         );
$obBscDespesa->obCampoCod->setValue ( $inCodDespesa                                );
$obBscDespesa->obCampoCod->setAlign ("left"                                        );
$obBscDespesa->setFuncaoBusca("abrePopUp('".CAM_GF_ORC_POPUPS."despesa/LSDespesa.php','frm','inCodDespesa','stNomDespesa','','".Sessao::getId()."','800','550');"  );
$obBscDespesa->setValoresBusca ( CAM_GF_ORC_POPUPS."despesa/OCDespesa.php?".Sessao::getId(), $obForm->getName(),''                                 );

include_once(CAM_GF_ORC_COMPONENTES."IMontaRecursoDestinacao.class.php");
$obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
$obIMontaRecursoDestinacao->setFiltro ( true );

$obBscFornecedor = new BuscaInner;
$obBscFornecedor->setRotulo                ( "Credor"                              );
$obBscFornecedor->setTitle                 ("Informe o credor que deseja pesquisar");
$obBscFornecedor->setId                    ( "stNomFornecedor"                     );
$obBscFornecedor->setValue                 ( $stNomFornecedor                      );
$obBscFornecedor->setNull                  ( true                                  );
$obBscFornecedor->obCampoCod->setName      ( "inCGM"                               );
$obBscFornecedor->obCampoCod->setSize      ( 10                                    );
$obBscFornecedor->obCampoCod->setMaxLength ( 8                                     );
$obBscFornecedor->obCampoCod->setValue     ( $inCGM                                );
$obBscFornecedor->obCampoCod->setAlign     ( "left"                                );
$obBscFornecedor->setFuncaoBusca           ("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCGM','stNomFornecedor','','".Sessao::getId()."','800','550');"   );
$obBscFornecedor->setValoresBusca          ( CAM_GA_CGM_POPUPS."cgm/OCProcurarCgm.php?".Sessao::getId(), $obForm->getName()                                   );

include_once( CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php");
$obMontaAssinaturas = new IMontaAssinaturas;
$obMontaAssinaturas->setEventosCmbEntidades ( $obCmbEntidades );

$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm             );
$obFormulario->addHidden    ( $obHdnCaminho       );
$obFormulario->addHidden    ( $obHdnModulo        );
$obFormulario->addTitulo    ( "Dados para Filtro" );
$obFormulario->addComponente( $obTxtExercicio     );
$obFormulario->addComponente( $obCmbEntidades     );
$obFormulario->addComponente( $obPeriodicidade    );
$obFormulario->addComponente( $obBscDespesa       );

$obIMontaRecursoDestinacao->geraFormulario ( $obFormulario );

$obFormulario->addComponente( $obBscFornecedor );

$obMontaAssinaturas->geraFormulario( $obFormulario );

$obFormulario->OK();
$obFormulario->show();

?>
