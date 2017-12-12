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
    * Página de Formulario para escolha do Plano de Contas a ser utilizado pela prefeitura
    * Data de Criação   : 01/11/2004

    * @author Analista: Tonismar
    * @author Desenvolvedor: Eduardo

    * @ignore
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_CONT_NEGOCIO.'RContabilidadePlanoContaGeral.class.php';
include_once CAM_GF_CONT_NEGOCIO.'RContabilidadePlanoContaHistorico.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

//Define o nome dos arquivos PHP
$stPrograma = 'ManterEscolhaPlanoConta';
$pgFilt = 'FL'.$stPrograma.'.php';
$pgList = 'LS'.$stPrograma.'.php';
$pgForm = 'FM'.$stPrograma.'.php';
$pgProc = 'PR'.$stPrograma.'.php';
$pgOcul = 'OC'.$stPrograma.'.php';
$pgJS   = 'JS'.$stPrograma.'.js';

// include_once $pgJS;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$obRContabilidadePlanoContaGeral = new RContabilidadePlanoContaGeral;
$obRContabilidadePlanoContaGeral->listarUFs($rsPlanos);

$obRContabilidadePlanoContaHistorico = new RContabilidadePlanoContaHistorico;
$obRContabilidadePlanoContaHistorico->setExercicio(Sessao::getExercicio());
$obRContabilidadePlanoContaHistorico->verificaUltimoPlanoEscolhido($rsPlanoEscolhido);

//Instancia o formulário
$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget('oculto');

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($stAcao);

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ('stCtrl');
$obHdnCtrl->setValue('');

if ($rsPlanoEscolhido->getNumLinhas() > 0) {
        $stVersaoAtual = $rsPlanoEscolhido->arElementos[0]['nom_uf'].' - '.$rsPlanoEscolhido->arElementos[0]['versao'];
} else {
        $stVersaoAtual = "Não foi escolhido nenhum plano para o exercício de ".Sessao::getExercicio();
}
$obLblVersaoAtual = new Label;
$obLblVersaoAtual->setRotulo('Versão Atual');
$obLblVersaoAtual->setName ('stVersaoAtual');
$obLblVersaoAtual->setValue($stVersaoAtual);

$obHdnVersaoAtual = new Hidden;
$obHdnVersaoAtual->setName ('hdnVersaoAtual');
$obHdnVersaoAtual->setValue($rsPlanoEscolhido->arElementos[0]['cod_uf']);

$obHdnNomeVersaoAtual = new Hidden;
$obHdnNomeVersaoAtual->setName ('hdnNomeVersaoAtual');
$obHdnNomeVersaoAtual->setValue($rsPlanoEscolhido->arElementos[0]['nom_uf']);

$obLblExercicio = new Label;
$obLblExercicio->setRotulo('Exercício');
$obLblExercicio->setName  ('lblExercicio' );
$obLblExercicio->setId    ('lblExercicio');
$obLblExercicio->setValue (Sessao::getExercicio());

$obHdnExercicio = new Hidden;
$obHdnExercicio->setId('stExercicio');
$obHdnExercicio->setName('stExercicio');
$obHdnExercicio->setValue(Sessao::getExercicio());

//Instancia uma TableTree para demonstrar os programas
$obTableTree = new TableTree;
$obTableTree->setRecordset            ($rsPlanos);
$obTableTree->setArquivo              ($pgOcul);
$obTableTree->setParametros           (array('cod_uf' => 'cod_uf'));
$obTableTree->setComplementoParametros('stCtrl=montaVersoes');
$obTableTree->setSummary              ('Lista de Planos');
$obTableTree->Head->addCabecalho      ('Sigla',10);
$obTableTree->Head->addCabecalho      ('Estado',75);
$obTableTree->Body->addCampo          ('sigla_uf','C');
$obTableTree->Body->addCampo          ('nom_uf', 'E');
$obTableTree->montaHTML();

//Instancia um span para os programas
$obSpnPlanos = new Span();
$obSpnPlanos->setId   ('spnPlanos');
$obSpnPlanos->setValue($obTableTree->getHTML());

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->addComponente($obLblVersaoAtual);
$obFormulario->addHidden($obHdnVersaoAtual);
$obFormulario->addHidden($obHdnNomeVersaoAtual);
$obFormulario->addHidden($obHdnExercicio);
$obFormulario->addComponente($obLblExercicio);
$obFormulario->addSpan($obSpnPlanos);

$obBtnOk = new Ok(true);

$obBtnLimpar = new Button;
$obBtnLimpar->setName ('Limpar');
$obBtnLimpar->setValue('Limpar');
$obBtnLimpar->setTipo ('Reset');
$obBtnLimpar->obEvento->setOnClick( "executaFuncaoAjax('limparTela')" );

$obFormulario->defineBarra( array ( $obBtnOk , $obBtnLimpar ),"","" );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
