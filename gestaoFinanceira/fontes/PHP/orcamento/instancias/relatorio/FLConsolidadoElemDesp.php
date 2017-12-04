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
    * Data de Criação   : 02/03/2005

    * @author Desenvolvedor: Vandré Miguel Ramos

    * @ignore

    $Revision: 30762 $
    $Name$
    $Autor:$
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.01.23
*/

/*
$Log$
Revision 1.9  2007/08/14 15:55:14  bruce
Bug#9907#

Revision 1.8  2006/07/17 18:44:29  andre.almeida
Bug #6400#

Revision 1.7  2006/07/05 20:43:28  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"   );
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php");
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRelatorioConsolidadoElemDesp.class.php"  );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php"       );
include_once CAM_GF_ORC_COMPONENTES.'ISelectFuncao.class.php';
include_once CAM_GF_ORC_COMPONENTES.'ISelectSubfuncao.class.php';

include_once 'JSConsolidadoElemDesp.js';

$rsEntidadesDisponiveis = $rsEntidadesSelecionadas = new RecordSet;

$obREntidade = new ROrcamentoEntidade;
$obREntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
$obREntidade->listarUsuariosEntidade( $rsEntidadesDisponiveis , " ORDER BY cod_entidade" );
$rsRecordset = $rsOrgao =  new RecordSet;
$arNomFiltro = Sessao::read('filtroNomRelatorio');
while ( !$rsEntidadesDisponiveis->eof() ) {
    $arNomFiltro['entidade'][$rsEntidadesDisponiveis->getCampo( 'cod_entidade' )] = $rsEntidadesDisponiveis->getCampo( 'nom_cgm' );
    $rsEntidadesDisponiveis->proximo();
}

$rsEntidadesDisponiveis->setPrimeiroElemento();

$obROrcamentoConsolidadoElemDesp   = new ROrcamentoRelatorioConsolidadoElemDesp;
$obROrcamentoConsolidadoElemDesp->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setExercicio(Sessao::getExercicio());
$obROrcamentoConsolidadoElemDesp->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->listar( $rsOrgao );

while ( !$rsOrgao->eof() ) {
    $arNomFiltro['orgao'][$rsOrgao->getCampo( 'num_orgao' )] = $rsOrgao->getCampo( 'nom_orgao' );
    $rsOrgao->proximo();
}
Sessao::write('filtroNomRelatorio', $arNomFiltro);

$rsOrgao->setPrimeiroElemento();

$obROrcamentoConfiguracao = new ROrcamentoConfiguracao;
$obROrcamentoConfiguracao->consultarConfiguracao();

$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GF_ORC_INSTANCIAS."relatorio/OCConsolidadoElemDesp.php" );

//Define o objeto SelectMultiplo para armazenar os ELEMENTOS
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName   ('inCodEntidade');
$obCmbEntidades->setRotulo ( "Entidades" );
$obCmbEntidades->setTitle  ( "Selecione as entidades para o filtro." );
$obCmbEntidades->setNull   ( false );

// Caso o usuário tenha permissão para somente uma entidade, a mesma já virá selecionada
if ($rsEntidadesDisponiveis->getNumLinhas()==1 ) {
       $rsEntidadesSelecionadas = $rsEntidadesDisponiveis;
       $rsEntidadesDisponiveis = new RecordSet;
}

// lista de atributos disponiveis
$obCmbEntidades->SetNomeLista1 ('inCodEntidadeDisponivel');
$obCmbEntidades->setCampoId1   ( 'cod_entidade' );
$obCmbEntidades->setCampoDesc1 ( 'nom_cgm' );
$obCmbEntidades->SetRecord1    ( $rsEntidadesDisponiveis );

// lista de atributos selecionados
$obCmbEntidades->SetNomeLista2 ('inCodEntidade');
$obCmbEntidades->setCampoId2   ('cod_entidade');
$obCmbEntidades->setCampoDesc2 ('nom_cgm');
$obCmbEntidades->SetRecord2    ( $rsEntidadesSelecionadas );

$obPeriodicidade = new Periodicidade();
$obPeriodicidade->setExercicio      ( Sessao::getExercicio());
$obPeriodicidade->setNull           ( false             );
$obPeriodicidade->setValue          ( 4                 );

//orgao e unidade inicial

$obTxtOrgao = new TextBox;
$obTxtOrgao->setRotulo              ( "Órgão Inicial"              );
$obTxtOrgao->setTitle               ( "Informe o órgão para filtro");
$obTxtOrgao->setName                ( "inCodOrgaoTxt"              );
$obTxtOrgao->setValue               ( $inCodOrgaoTxt               );
$obTxtOrgao->setSize                ( 6                            );
$obTxtOrgao->setMaxLength           ( 3                            );
$obTxtOrgao->setInteiro             ( true                         );
$obTxtOrgao->obEvento->setOnChange  ( "buscaValor('montaUnidade');");

$obCmbOrgao = new Select;
$obCmbOrgao->setRotulo              ( "Órgão Inicial"               );
$obCmbOrgao->setName                ( "inCodOrgao"                  );
$obCmbOrgao->setValue               ( $inCodOrgao                   );
$obCmbOrgao->setStyle               ( "width: 200px"                );
$obCmbOrgao->setCampoID             ( "num_orgao"                   );
$obCmbOrgao->setCampoDesc           ( "nom_orgao"                   );
$obCmbOrgao->addOption              ( "", "Selecione"               );
$obCmbOrgao->preencheCombo          ( $rsOrgao                      );
$obCmbOrgao->obEvento->setOnChange  ( "buscaValor('montaUnidade');" );

$obTxtUnidade = new TextBox;
$obTxtUnidade->setRotulo              ( "Unidade Inicial"               );
$obTxtUnidade->setTitle               ( "Informe a unidade para filtro" );
$obTxtUnidade->setName                ( "inCodUnidadeTxt"               );
$obTxtUnidade->setValue               ( $inCodUnidadeTxt                );
$obTxtUnidade->setSize                ( 6                               );
$obTxtUnidade->setMaxLength           ( 3                               );
$obTxtUnidade->setInteiro             ( true                            );

$obCmbUnidade= new Select;
$obCmbUnidade->setRotulo              ( "Unidade Inicial"               );
$obCmbUnidade->setName                ( "inCodUnidade"                  );
$obCmbUnidade->setValue               ( $inCodUnidade                   );
$obCmbUnidade->setStyle               ( "width: 200px"                  );
$obCmbUnidade->setCampoID             ( "cod_unidade"                   );
$obCmbUnidade->setCampoDesc           ( "descricao"                     );
$obCmbUnidade->addOption              ( "", "Selecione"                 );

//orgao e unidade final

$obTxtOrgaoFinal = new TextBox;
$obTxtOrgaoFinal->setRotulo              ( "Órgão Final"                );
$obTxtOrgaoFinal->setTitle               ( "Informe o órgão para filtro");
$obTxtOrgaoFinal->setName                ( "inCodOrgaoFinalTxt"         );
$obTxtOrgaoFinal->setValue               ( $inCodOrgaoFinalTxt               );
$obTxtOrgaoFinal->setSize                ( 6                            );
$obTxtOrgaoFinal->setMaxLength           ( 3                            );
$obTxtOrgaoFinal->setInteiro             ( true                         );
$obTxtOrgaoFinal->obEvento->setOnChange  ( "buscaValor('montaUnidadeFinal');");

$obCmbOrgaoFinal = new Select;
$obCmbOrgaoFinal->setRotulo              ( "Órgão Final"                 );
$obCmbOrgaoFinal->setName                ( "inCodOrgaoFinal"             );
$obCmbOrgaoFinal->setValue               ( $inCodOrgaoFinal              );
$obCmbOrgaoFinal->setStyle               ( "width: 200px"                );
$obCmbOrgaoFinal->setCampoID             ( "num_orgao"                   );
$obCmbOrgaoFinal->setCampoDesc           ( "nom_orgao"                   );
$obCmbOrgaoFinal->addOption              ( "", "Selecione"               );
$obCmbOrgaoFinal->preencheCombo          ( $rsOrgao                      );
$obCmbOrgaoFinal->obEvento->setOnChange  ( "buscaValor('montaUnidadeFinal');" );

$obTxtUnidadeFinal = new TextBox;
$obTxtUnidadeFinal->setRotulo              ( "Unidade Final"                 );
$obTxtUnidadeFinal->setTitle               ( "Informe a unidade para filtro" );
$obTxtUnidadeFinal->setName                ( "inCodUnidadeFinalTxt"          );
$obTxtUnidadeFinal->setValue               ( $inCodUnidadeFinalTxt           );
$obTxtUnidadeFinal->setSize                ( 6                               );
$obTxtUnidadeFinal->setMaxLength           ( 3                               );
$obTxtUnidadeFinal->setInteiro             ( true                            );

$obCmbUnidadeFinal= new Select;
$obCmbUnidadeFinal->setRotulo              ( "Unidade Final"               );
$obCmbUnidadeFinal->setName                ( "inCodUnidadeFinal"             );
$obCmbUnidadeFinal->setValue               ( $inCodUnidadeFinal              );
$obCmbUnidadeFinal->setStyle               ( "width: 200px"                  );
$obCmbUnidadeFinal->setCampoID             ( "cod_unidade"                   );
$obCmbUnidadeFinal->setCampoDesc           ( "descricao"                     );
$obCmbUnidadeFinal->addOption              ( "", "Selecione"                 );

$obCmbTipo = new Select;
$obCmbTipo->setRotulo              ( "Demonstrar Sintéticas"    );
$obCmbTipo->setTitle               ( "Selecione se deseja demonstrar sintéticas." );
$obCmbTipo->setName                ( "inTipo"              );
$obCmbTipo->setValue               ( $inTipo   );
$obCmbTipo->setStyle               ( "width: 200px"       );
$obCmbTipo->setNull                ( false );
$obCmbTipo->addOption              ( "1", "Não"      );
$obCmbTipo->addOption              ( "2", "Sim"      );

include_once(CAM_GF_ORC_COMPONENTES."IMontaRecursoDestinacao.class.php");
$obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
$obIMontaRecursoDestinacao->setFiltro ( true );

$obISelectFuncao     = new ISelectFuncao;
$obISelectSubfuncao  = new ISelectSubfuncao;

// Instanciação do objeto Lista de Assinaturas
// Limpa papeis das Assinaturas na Sessão
$arAssinaturas = Sessao::read('assinaturas');
$arAssinaturas['papeis'] = array();
Sessao::write('assinaturas',$arAssinaturas);

include_once( CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php");
$obMontaAssinaturas = new IMontaAssinaturas;
$obMontaAssinaturas->setEventosCmbEntidades ( $obCmbEntidades );

$obFormulario = new Formulario;
$obFormulario->setAjuda( 'UC-02.01.23' );
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnCaminho );
$obFormulario->addTitulo    ( "Dados para Filtro" );
$obFormulario->addComponente( $obCmbEntidades      );
$obFormulario->addComponente        ( $obPeriodicidade                  );
$obFormulario->addComponenteComposto( $obTxtOrgao, $obCmbOrgao  );
$obFormulario->addComponenteComposto( $obTxtOrgaoFinal, $obCmbOrgaoFinal  );
$obFormulario->addComponenteComposto( $obTxtUnidade, $obCmbUnidade  );
$obFormulario->addComponenteComposto( $obTxtUnidadeFinal, $obCmbUnidadeFinal  );
$obIMontaRecursoDestinacao->geraFormulario ( $obFormulario );
$obFormulario->addComponente($obISelectFuncao);
$obFormulario->addComponente($obISelectSubfuncao);
$obFormulario->addComponente( $obCmbTipo      );

// Injeção de código no formulário
$obMontaAssinaturas->geraFormulario( $obFormulario );

$obFormulario->OK();
$obFormulario->show();

?>
