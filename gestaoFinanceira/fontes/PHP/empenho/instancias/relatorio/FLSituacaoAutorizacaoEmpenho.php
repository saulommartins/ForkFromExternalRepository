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
    * Página de Filtro de Relatório Situação de Autorizações de Empenho
    * Data de Criação   : 12/10/2006

    * @author Tonismar Régis Bernardo

    * @ignore

    $Revision: 30668 $
    $Name$
    $Author: lbbarreiro $
    $Date: 2008-01-02 08:44:54 -0200 (Qua, 02 Jan 2008) $

    * Casos de uso : uc-02.03.34
*/

/*

$Log:

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeUsuario.class.php");
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoUnidadeOrcamentaria.class.php"   );
include_once( CAM_GF_EMP_COMPONENTES."IPopUpCredor.class.php" );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRecurso.class.php"   );

$stPrograma = 'SituacaoAutorizacaoEmpenho';
$pgOcul = 'OC'.$stPrograma.'.php';

$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget ( "oculto"   );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GF_EMP_INSTANCIAS."relatorio/OCSituacaoAutorizacaoEmpenho.php" );

$obEntidadeUsuario = new ISelectMultiploEntidadeUsuario( $obForm );
$obEntidadeUsuario->setName( 'inCodEntidade' );

$obEntidadeUsuario->obSelect1->obEvento->setOnDblClick('getIMontaAssinaturas()');
$obEntidadeUsuario->obSelect2->obEvento->setOnDblClick('getIMontaAssinaturas()');
$obEntidadeUsuario->obGerenciaSelects->obBotao1->obEvento->setOnClick('getIMontaAssinaturas()');
$obEntidadeUsuario->obGerenciaSelects->obBotao2->obEvento->setOnClick('getIMontaAssinaturas()');
$obEntidadeUsuario->obGerenciaSelects->obBotao3->obEvento->setOnClick('getIMontaAssinaturas()');
$obEntidadeUsuario->obGerenciaSelects->obBotao4->obEvento->setOnClick('getIMontaAssinaturas()');

$obExercicio = new Exercicio();
$obExercicio->setName( 'inExercicio' );

$obPeriodicidade = new Periodicidade();
$obPeriodicidade->setRotulo( 'Periodicidade' );
$obPeriodicidade->setExercicio( Sessao::getExercicio() );
$obPeriodicidade->setValidaExercicio( true );
$obPeriodicidade->setNull(false);

$obDtSituacao = new Data();
$obDtSituacao->setRotulo( 'Situação Até' );
$obDtSituacao->setTitle( 'Informe a data de situação' );
$obDtSituacao->setName( 'stSituacao' );
$obDtSituacao->setValue( $stSituacao );

$obNumAutorizacao = new TextBox;
$obNumAutorizacao->setRotulo( 'Número da Autorização' );
$obNumAutorizacao->setTitle( 'Informe o número da autorização' );
$obNumAutorizacao->setName( 'inNumAutorizacao' );
$obNumAutorizacao->setValue( $inNumAutorizacao );

$obTxtOrgao = new TextBox;
$obTxtOrgao->setRotulo              ( "Órgão"                          );
$obTxtOrgao->setTitle               ( "Selecione o órgão para filtro." );
$obTxtOrgao->setName                ( "inCodOrgaoTxt"                  );
$obTxtOrgao->setValue               ( $inCodOrgaoTxt                   );
$obTxtOrgao->setSize                ( 6                                );
$obTxtOrgao->setMaxLength           ( 3                                );
$obTxtOrgao->setInteiro             ( true                             );
//$obTxtOrgao->obEvento->setOnChange  ( "buscaValor('MontaUnidade');");
$obTxtOrgao->obEvento->setOnChange  ("
ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodOrgao='+this.value,'MontaUnidade');");

$obRUnidade = new ROrcamentoUnidadeOrcamentaria;
$obRUnidade->obROrcamentoOrgaoOrcamentario->setExercicio(Sessao::getExercicio());
$obRUnidade->obROrcamentoOrgaoOrcamentario->listar( $rsOrgao );

$obCmbOrgao = new Select;
$obCmbOrgao->setRotulo              ( "Órgão"                       );
$obCmbOrgao->setName                ( "inCodOrgao"                  );
$obCmbOrgao->setValue               ( $inCodOrgao                   );
$obCmbOrgao->setStyle               ( "width: 200px"                );
$obCmbOrgao->setCampoID             ( "num_orgao"                   );
$obCmbOrgao->setCampoDesc           ( "nom_orgao"                   );
$obCmbOrgao->addOption              ( "", "Selecione"               );
$obCmbOrgao->preencheCombo          ( $rsOrgao                      );
//$obCmbOrgao->obEvento->setOnChange  ( "buscaValor('MontaUnidade');" );
$obCmbOrgao->obEvento->setOnChange  ( "
ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodOrgao='+this.value,'MontaUnidade');");

$obTxtUnidade = new TextBox;
$obTxtUnidade->setRotulo              ( "Unidade"                          );
$obTxtUnidade->setTitle               ( "Selecione a unidade para filtro." );
$obTxtUnidade->setName                ( "inCodUnidadeTxt"                  );
$obTxtUnidade->setValue               ( $inCodUnidadeTxt                   );
$obTxtUnidade->setSize                ( 6                                  );
$obTxtUnidade->setMaxLength           ( 3                                  );
$obTxtUnidade->setInteiro             ( true                               );

$obCmbUnidade= new Select;
$obCmbUnidade->setRotulo              ( "Unidade"                       );
$obCmbUnidade->setName                ( "inCodUnidade"                  );
$obCmbUnidade->setValue               ( $inCodUnidade                   );
$obCmbUnidade->setStyle               ( "width: 200px"                  );
$obCmbUnidade->setCampoID             ( "cod_unidade"                   );
$obCmbUnidade->setCampoDesc           ( "descricao"                     );
$obCmbUnidade->addOption              ( "", "Selecione"                 );

include_once(CAM_GF_ORC_COMPONENTES."IMontaRecursoDestinacao.class.php");
$obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
$obIMontaRecursoDestinacao->setFiltro ( true );

/*
$obTxtRecurso = new TextBox;
$obTxtRecurso->setRotulo              ( "Recurso"                          );
$obTxtRecurso->setTitle               ( "Selecione o recurso para filtro." );
$obTxtRecurso->setName                ( "inCodRecursoTxt"                  );
$obTxtRecurso->setValue               ( $inCodRecursoTxt                   );
$obTxtRecurso->setSize                ( 6                                  );
$obTxtRecurso->setMaxLength           ( 4                                  );
$obTxtRecurso->setInteiro             ( true                               );

$obROrcamentoRecurso = new ROrcamentoRecurso;
$obROrcamentoRecurso->setExercicio(Sessao::getExercicio());
$obROrcamentoRecurso->listar( $rsRecurso );

$obCmbRecurso = new Select;
$obCmbRecurso->setRotulo              ( "Recurso"                       );
$obCmbRecurso->setName                ( "inCodRecurso"                  );
$obCmbRecurso->setValue               ( $inCodRecurso                   );
$obCmbRecurso->setStyle               ( "width: 200px"                  );
$obCmbRecurso->setCampoID             ( "cod_recurso"                   );
$obCmbRecurso->setCampoDesc           ( "nom_recurso"                   );
$obCmbRecurso->addOption              ( "", "Selecione"                 );
$obCmbRecurso->preencheCombo          ( $rsRecurso                      );
*/
$obPopUpCredor = new IPopUpCredor($obForm );
$obPopUpCredor->setNull( true );

$obCmbOrdem = new Select;
$obCmbOrdem->setRotulo              ( "Ordenação"                     );
$obCmbOrdem->setTitle               ( "Selecione a ordenação."        );
$obCmbOrdem->setName                ( "inOrdenacao"                   );
$obCmbOrdem->setValue               ( $stOrdenacao                    );
$obCmbOrdem->setStyle               ( "width: 150px"                  );
$obCmbOrdem->addOption              ( "1", "Autorização"    		  );
$obCmbOrdem->addOption              ( "2", "Credor"              	  );
$obCmbOrdem->addOption              ( "3", "Data de Empenhamento"  	  );

$obCmbSituacao= new Select;
$obCmbSituacao->setRotulo              ( "Situação"                     );
$obCmbSituacao->setName                ( "inSituacao"                   );
$obCmbSituacao->setTitle               ( "Selecione a situação"         );
$obCmbSituacao->setValue               ( $inSituacao                    );
$obCmbSituacao->setStyle               ( "width: 200px"                 );
$obCmbSituacao->addOption              ( "0", "Todas"                   );
$obCmbSituacao->addOption              ( "1", "Empenhadas"              );
$obCmbSituacao->addOption              ( "2", "Não Empenhadas"          );
$obCmbSituacao->addOption              ( "3", "Anuladas"                );

if (Sessao::getExercicio() > '2015') {
    $obCentroCusto = new TextBox;
    $obCentroCusto->setRotulo ("Centro de Custo");
    $obCentroCusto->setTitle ("Informe o centro de custo");
    $obCentroCusto->setName ('inCentroCusto');
    $obCentroCusto->setId ('inCentroCusto');
    $obCentroCusto->setInteiro (true);
}

include_once( CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php");
$obMontaAssinaturas = new IMontaAssinaturas;

$obFormulario = new Formulario();
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnCaminho );
$obFormulario->addTitulo( 'Dados para Filtro' );
$obFormulario->addComponente( $obEntidadeUsuario );
$obFormulario->addComponente( $obExercicio );
$obFormulario->addComponente( $obPeriodicidade );
$obFormulario->addComponente( $obDtSituacao );
if (Sessao::getExercicio() > '2015') {
    $obFormulario->addComponente( $obCentroCusto );
}
$obFormulario->addComponente( $obNumAutorizacao );
$obFormulario->addComponenteComposto( $obTxtOrgao, $obCmbOrgao );
$obFormulario->addComponenteComposto( $obTxtUnidade, $obCmbUnidade );
//$obFormulario->addComponenteComposto( $obTxtRecurso, $obCmbRecurso );
$obIMontaRecursoDestinacao->geraFormulario( $obFormulario );
$obFormulario->addComponente( $obPopUpCredor );
$obFormulario->addComponente( $obCmbOrdem );
$obFormulario->addComponente( $obCmbSituacao );

$obMontaAssinaturas->geraFormulario( $obFormulario );

$obFormulario->Ok();
$obFormulario->Show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
