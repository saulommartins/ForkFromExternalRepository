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
    * Filtro para Empenho - Ordem de Pagamento
    * Data de Criação   : 29/03/2005

    * @author Analista: Diego B. Victoria
    * @author Desenvolvedor: Cleisson Barboza

    * @ignore

    $Revision: 30805 $
    $Name$
    $Author: leandro.zis $
    $Date: 2006-07-14 12:44:07 -0300 (Sex, 14 Jul 2006) $

    * Casos de uso: uc-02.03.23
*/

/*
$Log$
Revision 1.10  2006/07/14 15:44:07  leandro.zis
Bug #6191#

Revision 1.9  2006/07/14 14:33:42  leandro.zis
Bug #6193#

Revision 1.8  2006/07/05 20:48:56  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once( CAM_GF_INCLUDE."validaGF.inc.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoOrdemPagamento.class.php" );

//Define o nome dos arquivos PHP
$stPrograma      = "ManterEstornoPagamentoOrdem";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";
include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

Sessao::remove('link');
Sessao::write('filtro', array());
Sessao::write('pg', '');
Sessao::write('pos', '');
Sessao::write('paginando', false);

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

$obTxtCodigoEntidade = new TextBox;
$obTxtCodigoEntidade->setName        ( "inCodigoEntidade"             );
$obTxtCodigoEntidade->setId          ( "inCodigoEntidade"             );
$obTxtCodigoEntidade->setValue       ( $inCodigoEntidade              );
$obTxtCodigoEntidade->setRotulo      ( "Entidade"                     );
$obTxtCodigoEntidade->setTitle       ( "Selecione a entidade."        );

if ($rsEntidades->getNumLinhas()==1) {
     $obTxtCodigoEntidade->setValue  ( $rsEntidades->getCampo('cod_entidade')  );
} else {
    $obTxtCodigoEntidade->obEvento->setOnChange( "limparCampos();"        );
}
$obTxtCodigoEntidade->setInteiro     ( true                           );
$obTxtCodigoEntidade->setNull        ( false                          );

// Define Objeto Select para Nome da Entidade
$obCmbNomeEntidade = new Select;
$obCmbNomeEntidade->setName          ( "stNomeEntidade"               );
$obCmbNomeEntidade->setId            ( "stNomeEntidade"               );
$obCmbNomeEntidade->setValue         ( $inCodigoEntidade              );
if ($rsEntidades->getNumLinhas()>1) {
    $obCmbNomeEntidade->addOption              ( "", "Selecione"               );
    $obCmbNomeEntidade->obEvento->setOnChange( "limparCampos();"          );
}
$obCmbNomeEntidade->setCampoId       ( "cod_entidade"                 );
$obCmbNomeEntidade->setCampoDesc     ( "nom_cgm"                      );
$obCmbNomeEntidade->setStyle         ( "width: 520"                   );
$obCmbNomeEntidade->preencheCombo    ( $rsEntidades                   );
$obCmbNomeEntidade->setNull          ( false                          );

//Define o objeto TEXT para Número da Ordem
$obTxtCodigoOrdemPagamento = new TextBox;
$obTxtCodigoOrdemPagamento->setTitle    ( "Informe o número da ordem."   );
$obTxtCodigoOrdemPagamento->setName     ( "inCodigoOrdemPagamento"       );
$obTxtCodigoOrdemPagamento->setValue    ( $inCodigoOrdemPagamento        );
$obTxtCodigoOrdemPagamento->setRotulo   ( "Número da Ordem"              );
$obTxtCodigoOrdemPagamento->setInteiro  ( true                           );
$obTxtCodigoOrdemPagamento->setNull     ( true                           );

// Define Objeto Data para Vencimento
$obDtVencimento = new Data;
$obDtVencimento->setName     ( "stDtVencimento" );
$obDtVencimento->setRotulo   ( "Vencimento" );
$obDtVencimento->setTitle    ( "Informe o vencimento." );
$obDtVencimento->setNull     ( true );

// Define Objeto TEXT para Exercicio do Empenho
$obTxtExercicioEmpenho = new TextBox;
$obTxtExercicioEmpenho->setRotulo    ( '*Número do Empenho' );
$obTxtExercicioEmpenho->setName      ( 'stExercicioEmpenho' );
$obTxtExercicioEmpenho->setid        ( 'stExercicioEmpenho' );
$obTxtExercicioEmpenho->setValue     ( Sessao::getExercicio()   );
$obTxtExercicioEmpenho->setMaxLength ( 4                    );
$obTxtExercicioEmpenho->setSize      ( 4                    );
$obTxtExercicioEmpenho->setNull      ( true                 );
$obTxtExercicioEmpenho->setInteiro   ( true                 );

$obLblBarra = new Label;
$obLblBarra->setValue( '/' );
$obLblBarra->setName ( 'Barra' );

// Define Objeto TEXT para Número do Empenho
$obTxtCodigoEmpenho = new TextBox;
$obTxtCodigoEmpenho->setTitle         ( "Informe o número do empenho e exercício."  );
$obTxtCodigoEmpenho->setRotulo        ( "Número do Empenho"                         );
$obTxtCodigoEmpenho->setName          ( "inCodigoEmpenho"                           );
$obTxtCodigoEmpenho->setValue         ( $_REQUEST["inCodigoEmpenho"]                );
$obTxtCodigoEmpenho->setSize          ( 8                                           );
$obTxtCodigoEmpenho->setMaxLength     ( 8                                           );
$obTxtCodigoEmpenho->setInteiro       ( true                                        );
$obTxtCodigoEmpenho->setNull          ( true                                        );
$obTxtCodigoEmpenho->obEvento->setOnChange ( "buscaLiquidacoes();"                  );

// Define Objeto BuscaInner para Fornecedor
$obBscFornecedor = new BuscaInner;
$obBscFornecedor->setRotulo                 ( "Fornecedor"      );
$obBscFornecedor->setTitle                  ( "Informe o fornecedor." );
$obBscFornecedor->setId                     ( "stNomFornecedor" );
$obBscFornecedor->setValue                  ( $stNomFornecedor  );
$obBscFornecedor->obCampoCod->setName       ( "inCodFornecedor" );
$obBscFornecedor->obCampoCod->setSize       ( 10                );
$obBscFornecedor->obCampoCod->setMaxLength  ( 8                 );
$obBscFornecedor->obCampoCod->setValue      ( $inCodFornecedor  );
$obBscFornecedor->obCampoCod->setAlign      ( "left"            );
$obBscFornecedor->obCampoCod->obEvento->setOnBlur("buscaFornecedor();");
$obBscFornecedor->setFuncaoBusca            ("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodFornecedor','stNomFornecedor','','".Sessao::getId()."','800','550');");

//Define o objeto TEXT para Número da Nota
$obTxtNotaLiquidacao = new TextBox;
$obTxtNotaLiquidacao->setTitle    ( "Informe o número da nota."     );
$obTxtNotaLiquidacao->setName     ( "inCodigoNotaLiquidacao"        );
$obTxtNotaLiquidacao->setValue    ( $inCodigoNotaLiquidacao         );
$obTxtNotaLiquidacao->setRotulo   ( "Nota de Liquidação"            );
$obTxtNotaLiquidacao->setInteiro  ( true                            );
$obTxtNotaLiquidacao->setNull     ( true                            );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction           ( $pgList                  );
$obForm->setTarget           ( "telaPrincipal"          );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm                  );
$obFormulario->addHidden     ( $obHdnCtrl               );
$obFormulario->addHidden     ( $obHdnAcao               );

$obFormulario->addTitulo     ( "Dados para Filtro"      );

$obFormulario->addComponenteComposto( $obTxtCodigoEntidade , $obCmbNomeEntidade );
$obFormulario->addComponente        ( $obTxtCodigoOrdemPagamento                );
$obFormulario->addComponente        ( $obDtVencimento                           );
$obFormulario->agrupaComponentes    ( array( $obTxtCodigoEmpenho, $obLblBarra, $obTxtExercicioEmpenho ) );
$obFormulario->addComponente        ( $obBscFornecedor                          );
$obFormulario->addComponente        ( $obTxtNotaLiquidacao                      );
$obFormulario->Ok                   (                                           );
$obFormulario->show                 (                                           );

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
