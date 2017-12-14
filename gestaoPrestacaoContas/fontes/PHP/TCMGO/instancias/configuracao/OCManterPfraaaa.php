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
    * Página de Formulário para configuração
    * Data de Criação   : 22/01/2007

    * @author Henrique Boaventura

    * @ignore

    * Casos de uso : uc-06.03.00
*/

/*
$Log$
Revision 1.1  2007/05/17 13:01:56  hboaventura
Arquivos para geração do TCMGO

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once(TTGO."TTGOBalancoPfraaaa.class.php" );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php"                    );

$stPrograma = "ManterPfraaaa";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$obRegra = new ROrcamentoDespesa;

$obRegra->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );

$obRegra->obROrcamentoClassificacaoDespesa->setExercicio( Sessao::getExercicio() );
$stMascaraRubrica = $obRegra->obROrcamentoClassificacaoDespesa->recuperaMascara();

$obRegra->setExercicio                              ( $_REQUEST['exercicio']      );
$obRegra->setCodDespesa                             ( $_REQUEST['cod_despesa']     );

$obRegra->consultarDotacao($rsDotacao);

$obLblEntidade = new Label;
$obLblEntidade->setRotulo( "Entidade"       );
$obLblEntidade->setId    ( "inCodEntidade" );
$obLblEntidade->setValue ( $rsDotacao->getCampo("cod_entidade") . " - " . $rsDotacao->getCampo("entidade") );

$obLblDotacao = new Label;
$obLblDotacao->setRotulo( "Dotação Orçamentária"       );
$obLblDotacao->setId    ( "inCodDotacao" );
$obLblDotacao->setValue ( $rsDotacao->getCampo("cod_despesa") . " - " . $rsDotacao->getCampo("descricao") );

$obLblOrgao = new Label;
$obLblOrgao->setRotulo( "Órgão Orçamentário"       );
$obLblOrgao->setId    ( "inCodOrgao" );
$obLblOrgao->setValue ( $rsDotacao->getCampo("num_orgao") . " - " . $rsDotacao->getCampo("nom_orgao") );

$obLblUnidade = new Label;
$obLblUnidade->setRotulo( "Unidade Orçamentária"       );
$obLblUnidade->setId    ( "inCodUnidade" );
$obLblUnidade->setValue ( $rsDotacao->getCampo("num_unidade") . " - " . $rsDotacao->getCampo("nom_unidade") );

$obLblFuncao = new Label;
$obLblFuncao->setRotulo( "Função"       );
$obLblFuncao->setId    ( "inCodFuncao"  );
$obLblFuncao->setValue ( $rsDotacao->getCampo("cod_funcao") . " - " . $rsDotacao->getCampo("funcao") );

$obLblSubFuncao = new Label;
$obLblSubFuncao->setRotulo( "Sub-Função"    );
$obLblSubFuncao->setId    ( "inCodSubFuncao");
$obLblSubFuncao->setValue ( $rsDotacao->getCampo("cod_subfuncao") . " - " . $rsDotacao->getCampo("subfuncao") );

$obLblPrograma = new Label;
$obLblPrograma->setRotulo( "Programa"       );
$obLblPrograma->setId    ( "inCodPrograma"  );
$obLblPrograma->setValue ( $rsDotacao->getCampo("cod_programa") . " - " . $rsDotacao->getCampo("programa") );

$obLblPAO = new Label;
$obLblPAO->setRotulo( "PAO"       );
$obLblPAO->setId    ( "inCodPAO"  );
$obLblPAO->setValue ( $rsDotacao->getCampo("num_pao") . " - " . $rsDotacao->getCampo("nom_pao") );

$obLblDesdobramento = new Label;
$obLblDesdobramento->setRotulo( "Desdobramento"    );
$obLblDesdobramento->setId    ( "inCodEstrurutal");
$obLblDesdobramento->setValue ( $rsDotacao->getCampo("cod_estrutural"));

$obLblRecurso = new Label;
$obLblRecurso->setRotulo( "Recurso"       );
$obLblRecurso->setId    ( "inCodRecurso"  );
$obLblRecurso->setValue ( $rsDotacao->getCampo("cod_recurso") . " - " . $rsDotacao->getCampo("nom_recurso") );

$obFormulario = new Formulario();
$obFormulario->addTitulo( 'Detalhamento' );
$obFormulario->addComponente( $obLblEntidade        );
$obFormulario->addComponente( $obLblDotacao         );
$obFormulario->addComponente( $obLblOrgao           );
$obFormulario->addComponente( $obLblUnidade         );
$obFormulario->addComponente( $obLblFuncao          );
$obFormulario->addComponente( $obLblSubFuncao       );
$obFormulario->addComponente( $obLblPrograma        );
$obFormulario->addComponente( $obLblPAO             );
$obFormulario->addComponente( $obLblDesdobramento   );
$obFormulario->addComponente( $obLblRecurso         );
$obFormulario->montaHTML();
echo $obFormulario->getHTML();
