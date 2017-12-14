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
    * Página de Filtro para Relatório de Evolução da Receita
    * Data de Criação  : 15/07/2008

    * @author Leopoldo Braga Barreiro

    * Casos de uso : uc-02.01.37

    * $Id: $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/HTML/Exercicio.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/HTML/SimNao.class.php';

include_once(CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeUsuario.class.php"                                 );
include_once(CAM_GF_ORC_COMPONENTES."IPopUpRecurso.class.php"                                                  );

include_once(CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"                                                 );
include_once( CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php");

include_once(CAM_GF_ORC_COMPONENTES."IMontaRecursoDestinacao.class.php");
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php"       );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoReceita.class.php" );

$stPrograma = "EvolucaoReceita";
if ( empty( $stAcao ) ) {
    $stAcao = "imprimir";
}

$pgFilt = "FL".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$obROrcamentoEntidade = new ROrcamentoEntidade;
$obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$stOrdem = " ORDER BY cod_entidade";
$obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades, $stOrdem );
$arFiltroNom = Sessao::read('filtroNomRelatorio');
while ( !$rsEntidades->eof() ) {
    $arFiltroNom['entidade'][$rsEntidades->getCampo( 'cod_entidade' )] = $rsEntidades->getCampo( 'nom_cgm' );
    $rsEntidades->proximo();
}
Sessao::write('filtroNomRelatorio', $arFiltroNom);

$rsEntidades->setPrimeiroElemento();

//Recupera Mascara da Classificacao de Receita
$obROrcamentoReceita = new ROrcamentoReceita;
$obROrcamentoReceita->obROrcamentoClassificacaoReceita->setExercicio(Sessao::getExercicio());
$mascClassificacao = $obROrcamentoReceita->obROrcamentoClassificacaoReceita->recuperaMascara();

// Form

$obForm = new Form;
$obForm->setAction ( 'OCGeraEvolucaoReceita.php' );
$obForm->setTarget ( 'telaPrincipal' );

// Formulário

$obFormulario = new Formulario;

$obFormulario->addForm( $obForm );
$obFormulario->setAjuda ( 'UC-02.01.37' );
$obFormulario->addTitulo( "Dados para o filtro" );

// Componentes do Formulario
// Componente Hidden Caminho

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GF_ORC_INSTANCIAS."relatorio/OCGeraEvolucaoReceita.php" );
$obFormulario->addHidden( $obHdnCaminho );

// Componente Hidden Ctrl

$obHdnStCtrl = new Hidden;
$obHdnStCtrl->setName( "stCtrl" );
$obFormulario->addHidden( $obHdnStCtrl );

// Componente Hidden Acao

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );
$obFormulario->addHidden( $obHdnAcao );

// Mascara Classificação

$obHdnMascClassificacao = new Hidden;
$obHdnMascClassificacao->setName ( "stMascClassificacao" );
$obHdnMascClassificacao->setValue( $mascClassificacao );
$obFormulario->addHidden( $obHdnMascClassificacao );

// Componente Entidades permitidas para o Usuario

$obISelectEntidadeUsuario = new ISelectMultiploEntidadeUsuario();
$obFormulario->addComponente ( $obISelectEntidadeUsuario );

// Componente Exercicio

$obExercicio = new Exercicio();
$obFormulario->addComponente ( $obExercicio );

// Componente SimNao

$obSimNao = new SimNao();
$obSimNao->setName( "stDemonstraSinteticas" );
$obSimNao->setRotulo("*Demonstrar Sintéticas");
$obSimNao->setChecked( "S" );
$obFormulario->addComponente ( $obSimNao );

// Recurso
$obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
$obIMontaRecursoDestinacao->setFiltro ( true );
$obIMontaRecursoDestinacao->geraFormulario ( $obFormulario );

// Componente Lista de Assinaturas

// Limpa papeis das Assinaturas na Sessão
$arAssinaturaPapel = array();
$arAssinaturas = Sessao::read('assinaturas');
$arAssinaturas['papeis'] = $arAssinaturaPapel;
Sessao::write('assinaturas', $arAssinaturas);

$obMontaAssinaturas = new IMontaAssinaturas;
$obMontaAssinaturas->setEventosCmbEntidades ( $obISelectEntidadeUsuario );
$obMontaAssinaturas->setPapeisDisponiveis( array("Selecione", "Assinante 1", "Assinante 2", "Assinante 3") );

$obMontaAssinaturas->geraFormulario( $obFormulario ); // Injeção de código no formulário

// Exibição do Formulário

$obFormulario->Ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
