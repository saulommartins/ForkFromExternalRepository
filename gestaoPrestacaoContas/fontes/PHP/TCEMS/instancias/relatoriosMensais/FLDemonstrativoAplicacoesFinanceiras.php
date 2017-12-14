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
    * Página de Filtro do Relatório Demonstrativo de Restos a Pagar
    * Data de Criação   : 25/08/2011

    * @author Desenvolvedor: Davi Ritter Aroldi

    * @ignore

    * Casos de uso : uc-02.03.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php"        );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php" );

$stPrograma = 'DemonstrativoAplicacoesFinanceiras';
$pgFiltro   = 'FL'.$stPrograma.'.php';
$pgProc     = 'PR'.$stPrograma.'.php';
$pgOcul     = 'OC'.$stPrograma.'.php';
$pgForm     = 'FM'.$stPrograma.'.php';
$pgJs       = 'JS'.$stPrograma.'.js';
$pgList     = 'LS'.$stPrograma.'.php';
$pgGera     = 'OCGera'.$stPrograma.'.php';

include_once($pgJs);
$rsRecordset = $rsOrgao = $rsUnidade = $rsRecurso = new RecordSet;

$obRUnidade = new ROrcamentoUnidadeOrcamentaria;
$obRUnidade->obROrcamentoOrgaoOrcamentario->setExercicio(Sessao::getExercicio());
$obRUnidade->obROrcamentoOrgaoOrcamentario->listar( $rsOrgao );

$obROrcamentoConfiguracao = new ROrcamentoConfiguracao;
$obROrcamentoConfiguracao->consultarConfiguracao();

$obREmpenhoEmpenho = new REmpenhoEmpenho;
$obREmpenhoEmpenho->recuperaExerciciosRP( $rsExercicio );

$arFiltroNom = Sessao::read('filtroNomRelatorio');

$obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
$obREmpenhoEmpenho->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades , " ORDER BY cod_entidade" );
while ( !$rsEntidades->eof() ) {
    $arFiltroNom['entidade'][$rsEntidades->getCampo( 'cod_entidade' )] = $rsEntidades->getCampo( 'nom_cgm' );
    $rsEntidades->proximo();
}
$rsEntidades->setPrimeiroElemento();

$obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->listar( $rsRecurso );
while ( !$rsRecurso->eof() ) {
    $arFiltroNom['recurso'][$rsRecurso->getCampo( 'cod_recurso' )] = $rsRecurso->getCampo( 'nom_recurso' );
    $rsRecurso->proximo();
}
$rsRecurso->setPrimeiroElemento();

Sessao::write('filtroNomRelatorio', $arFiltroNom);

$obForm = new Form;
$obForm->setAction( $pgGera );
$obForm->setTarget( "telaPrincipal" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GF_EMP_INSTANCIAS."relatorio/OCDemonstrativoAplicacoesFinanceiras.php" );

$obHdnModulo = new Hidden;
$obHdnModulo->setName ( "inCodModulo"        );
$obHdnModulo->setValue( $_REQUEST['modulo']  );

//Define o objeto SelectMultiplo para armazenar os ELEMENTOS
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName   ('inCodEntidade');
$obCmbEntidades->setRotulo ( "Entidades" );
$obCmbEntidades->setTitle  ( "Selecione as entidades para o filtro." );
$obCmbEntidades->setNull   ( false );

// Caso o usuário tenha permissão para somente uma entidade, a mesma já virá selecionada
if ($rsEntidades->getNumLinhas()==1) {
       $rsRecordset = $rsEntidades;
       $rsEntidades = new RecordSet;
}

//Entidade
$obCmbEntidades->SetNomeLista1 ('inCodEntidadeDisponivel');
$obCmbEntidades->setCampoId1   ( 'cod_entidade' );
$obCmbEntidades->setCampoDesc1 ( 'nom_cgm' );
$obCmbEntidades->SetRecord1    ( $rsEntidades );

$obCmbEntidades->SetNomeLista2 ('inCodEntidade');
$obCmbEntidades->setCampoId2   ('cod_entidade');
$obCmbEntidades->setCampoDesc2 ('nom_cgm');
$obCmbEntidades->SetRecord2    ( $rsRecordset );

//Orgão
$obTxtOrgao = new TextBox;
$obTxtOrgao->setRotulo              ( "Órgão"                      );
$obTxtOrgao->setTitle               ( "Selecione o órgão para filtro.");
$obTxtOrgao->setName                ( "inCodOrgaoTxt"              );
$obTxtOrgao->setValue               ( $inCodOrgaoTxt               );
$obTxtOrgao->setSize                ( 6                            );
$obTxtOrgao->setMaxLength           ( 3                            );
$obTxtOrgao->setInteiro             ( true                         );
$obTxtOrgao->obEvento->setOnChange  ( "buscaValor('MontaUnidade');");
$obTxtOrgao->setObrigatorio         ( true                         );

$obCmbOrgao = new Select;
$obCmbOrgao->setRotulo              ( "Órgão"                       );
$obCmbOrgao->setName                ( "inCodOrgao"                  );
$obCmbOrgao->setValue               ( $inCodOrgao                   );
$obCmbOrgao->setStyle               ( "width: 200px"                );
$obCmbOrgao->setCampoID             ( "num_orgao"                   );
$obCmbOrgao->setCampoDesc           ( "nom_orgao"                   );
$obCmbOrgao->addOption              ( "", "Selecione"               );
$obCmbOrgao->preencheCombo          ( $rsOrgao                      );
$obCmbOrgao->obEvento->setOnChange  ( "buscaValor('MontaUnidade');" );
$obCmbOrgao->setObrigatorio         ( true                          );

//Unidade
$obTxtUnidade = new TextBox;
$obTxtUnidade->setRotulo              ( "Unidade"                       );
$obTxtUnidade->setTitle               ( "Selecione a unidade para filtro." );
$obTxtUnidade->setName                ( "inCodUnidadeTxt"               );
$obTxtUnidade->setValue               ( $inCodUnidadeTxt                );
$obTxtUnidade->setSize                ( 6                               );
$obTxtUnidade->setMaxLength           ( 3                               );
$obTxtUnidade->setInteiro             ( true                            );
$obTxtUnidade->setObrigatorio         ( true                            );

$obCmbUnidade= new Select;
$obCmbUnidade->setRotulo              ( "Unidade"                       );
$obCmbUnidade->setName                ( "inCodUnidade"                  );
$obCmbUnidade->setValue               ( $inCodUnidade                   );
$obCmbUnidade->setStyle               ( "width: 200px"                  );
$obCmbUnidade->setCampoID             ( "cod_unidade"                   );
$obCmbUnidade->setCampoDesc           ( "descricao"                     );
$obCmbUnidade->addOption              ( "", "Selecione"                 );
$obCmbUnidade->setObrigatorio         ( true                            );

//Define o objeto de periodicidade para o formulário
$obDtPeriodicidade = new Periodicidade();
$obDtPeriodicidade->setExercicio      ( Sessao::getExercicio()          );
$obDtPeriodicidade->setObrigatorio    ( true                            );

// Ações disparadas por eventos
$obCmbEntidades->obSelect1->obEvento->setOnDblClick('getIMontaAssinaturas()');
$obCmbEntidades->obSelect2->obEvento->setOnDblClick('getIMontaAssinaturas()');
$obCmbEntidades->obGerenciaSelects->obBotao1->obEvento->setOnClick('getIMontaAssinaturas()');
$obCmbEntidades->obGerenciaSelects->obBotao2->obEvento->setOnClick('getIMontaAssinaturas()');
$obCmbEntidades->obGerenciaSelects->obBotao3->obEvento->setOnClick('getIMontaAssinaturas()');
$obCmbEntidades->obGerenciaSelects->obBotao4->obEvento->setOnClick('getIMontaAssinaturas()');

include_once( CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php");
$obMontaAssinaturas = new IMontaAssinaturas;

$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm                                 );
$obFormulario->addHidden    ( $obHdnCaminho                           );
$obFormulario->addHidden    ( $obHdnModulo                            );
$obFormulario->addTitulo    ( "Dados para Filtro"                     );
$obFormulario->addComponente( $obCmbEntidades                         );
$obFormulario->addComponenteComposto( $obTxtOrgao, $obCmbOrgao        );
$obFormulario->addComponenteComposto( $obTxtUnidade, $obCmbUnidade    );
$obFormulario->addComponente( $obDtPeriodicidade                      );

// Injeção de código no formulário
$obMontaAssinaturas->geraFormulario( $obFormulario );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
