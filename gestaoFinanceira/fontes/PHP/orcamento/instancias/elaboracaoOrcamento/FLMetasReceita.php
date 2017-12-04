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
    * Página de Formulario de Inclusão/Alteração de Metas de Arrecadação de Receita
    * Data de Criação   : 27/07/2004

    * @author Desenvolvedor: Roberto Pawelski Rodrigues

    * @ignore

    $Revision: 30824 $
    $Name$
    $Autor: $
    $Date: 2008-02-13 15:31:44 -0200 (Qua, 13 Fev 2008) $

    * Casos de uso: uc-02.01.06
*/

/*
$Log$
Revision 1.10  2007/05/21 18:58:17  melo
Bug #9229#

Revision 1.9  2007/01/30 18:43:47  luciano
#7316#

Revision 1.8  2006/11/08 12:40:35  cako
Bug #7242#

Revision 1.7  2006/07/14 19:50:12  leandro.zis
Bug #6382#

Revision 1.6  2006/07/05 20:43:03  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CAM_GF_INCLUDE."validaGF.inc.php"														);
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoOrgaoOrcamentario.class.php"    							);
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoUnidadeOrcamentaria.class.php"  							);
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoReceita.class.php"              							);
include_once ( CAM_GF_ORC_COMPONENTES."IIntervaloPopUpEstruturalReceita.class.php"                      );
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoBanco.class.php" 								);

//Define o nome dos arquivos PHP
$stPrograma = "MetasReceita";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ($pgJS);

$obROrcamentoOrgaoOrcamentario   = new ROrcamentoOrgaoOrcamentario;
$obROrcamentoUnidadeOrcamentaria = new ROrcamentoUnidadeOrcamentaria;
$obROrcamentoReceita             = new ROrcamentoReceita;
$obRContabilidadePlanoBanco 	 = new RContabilidadePlanoBanco;
$obIntervaloEstrutural		     = new IIntervaloPopUpEstruturalReceita();

//Recupera Mascara
$obRContabilidadePlanoBanco->setExercicio( Sessao::getExercicio() );
$obRContabilidadePlanoBanco->recuperaMascaraConta( $stMascara );

//Destroi arrays de sessao que armazenam os dados do FILTRO
Sessao::remove('filtro');
Sessao::remove('pg');
Sessao::remove('pos');
Sessao::remove('paginando');
//sessao->transf4 = array( 'filtro' => array(), 'pg' => '' , 'pos' => '', 'paginando' => false );o->link );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "telaPrincipal" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnMasc = new Hidden;
$obHdnMasc->setName ( "stMascara" );
$obHdnMasc->setValue( $stMascara );

//Define o objeto INNER para armazenar o ENTIDADE
$obROrcamentoReceita->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obROrcamentoReceita->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade , " ORDER BY cod_entidade" );

$obHdnNumCGM = new Hidden;
$obHdnNumCGM->setName   ('inNumCGM');
$obHdnNumCGM->setValue  ( $obROrcamentoReceita->obROrcamentoEntidade->obRCGM->getNumCGM() );

$obCmbEntidade = new Select;
$obCmbEntidade->setName      ( 'inCodEntidade'   );
$obCmbEntidade->setValue     (  $inCodEntidade   );
$obCmbEntidade->setTitle     (  "Selecione a entidade."   );
$obCmbEntidade->setRotulo    ( 'Entidade'        );
$obCmbEntidade->setStyle     ( "width: 400px"    );
$obCmbEntidade->setNull      ( true              );
$obCmbEntidade->setCampoId   ( "cod_entidade"    );
$obCmbEntidade->setCampoDesc ( "[cod_entidade] - [nom_cgm]" );

// Caso o usuário tenha permissão para mais de uma entidade, exibe o selecionar.
// Se tiver apenas uma, evita o addOption forçando a primeira e única opção ser selecionada.
if ($rsEntidade->getNumLinhas()>1) {
    $obCmbEntidade->addOption              ( "", "Selecione"               );
}
$obCmbEntidade->preencheCombo( $rsEntidade       );

//Define objeto de busca por dedutoras
$obTxtCodClassDedutora = new TextBox;
$obTxtCodClassDedutora->setName      ( "inCodClassificacao"                           				);
$obTxtCodClassDedutora->setValue     ( $inCodClassificacao                        				);
$obTxtCodClassDedutora->setSize 	 ( 30															);
$obTxtCodClassDedutora->setRotulo    ( "Classificação da Dedutora"                    				);
$obTxtCodClassDedutora->setTitle     ( "Informe o código de classificação da conta dedutora" 		);
$obTxtCodClassDedutora->setNull		 ( true 														);
$obTxtCodClassDedutora->setMascara   ( $stMascara 													);
$obTxtCodClassDedutora->obEvento->setOnKeyPress( "return validaExpressao( this, event, '[0-9.]');"  );
$obTxtCodClassDedutora->obEvento->setOnChange("buscaValor('mascaraClassDedutora','".$pgOcul."','".$pgList."','telaPrincipal','".Sessao::getId()."')");

//Define o objeto TEXT para Codigo do Rubrica Despesa Inicial
$obTxtCodReceitaReduzidoInicial = new TextBox;
$obTxtCodReceitaReduzidoInicial->setRotulo   ( "Código Reduzido"   );
$obTxtCodReceitaReduzidoInicial->setTitle    ( "Informe o número do código reduzido." );
$obTxtCodReceitaReduzidoInicial->setName     ( "inCodReceitaReduzidoInicial" );
$obTxtCodReceitaReduzidoInicial->setNull     ( true                  );
$obTxtCodReceitaReduzidoInicial->setInteiro	 ( true 	 			 );

//Define objeto Label
$obLblCodReceitaReduzido = new Label;
$obLblCodReceitaReduzido->setValue( "a" );

//Define o objeto TEXT para Codigo do Rubrica Despesa Final
$obTxtCodReceitaReduzidoFinal = new TextBox;
$obTxtCodReceitaReduzidoFinal->setRotulo   ( "Rubrica de Despesa" );
$obTxtCodReceitaReduzidoFinal->setTitle    ( "Informe o número do código reduzido." );
$obTxtCodReceitaReduzidoFinal->setName     ( "inCodReceitaReduzidoFinal" );
$obTxtCodReceitaReduzidoFinal->setNull     ( true               );
$obTxtCodReceitaReduzidoFinal->setInteiro  ( true 				);

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda         ( "UC-02.01.06"           		);
$obFormulario->addHidden        ( $obHdnNumCGM            		);
$obFormulario->addHidden        ( $obHdnCtrl          			);
$obFormulario->addHidden        ( $obHdnAcao          			);
$obFormulario->addHidden        ( $obHdnMasc					);
$obFormulario->addTitulo        ( "Dados para Filtro" 			);
$obFormulario->addComponente    ( $obCmbEntidade      			);
$obFormulario->addComponente    ( $obIntervaloEstrutural  		);
$obFormulario->agrupaComponentes( array( $obTxtCodReceitaReduzidoInicial,$obLblCodReceitaReduzido, $obTxtCodReceitaReduzidoFinal ));
if ( Sessao::getExercicio() == '2008' ) {
    $obFormulario->addComponente    ( $obTxtCodClassDedutora		);
}
$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
