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
/*
    * Formulário de Cadastro de Notas Fiscais TCEMG
    * Data de Criação   : 05/02/2014

    * @author Analista      Sergio Luiz dos Santos
    * @author Desenvolvedor Michel Teixeira

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: FMManterConfiguracaoEMP.php 61800 2015-03-04 20:16:20Z arthur $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoFinanceira/fontes/PHP/orcamento/classes/componentes/ITextBoxSelectEntidadeUsuario.class.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGConfiguracaoEMP.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasModalidade.class.php";

$stPrograma = "ManterConfiguracaoEMP";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once($pgJs);
$obTTCEMGConfiguracaoEMP = new TTCEMGConfiguracaoEMP;
$obTTCEMGConfiguracaoEMP->recuperaTodos($rsConfiguracaoEMP, "", "");

Sessao::write('arListaEmpenho', array());

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( "manter" );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl"            );
$obHdnCtrl->setValue( $_REQUEST['stCtrl'] );

$obHdnId = new Hidden;
$obHdnId->setName( "inId" );
$obHdnId->setId  ( "inId" );

$obTxtExercicio = new TextBox;
$obTxtExercicio->setName     ( "stExercicio"          );
$obTxtExercicio->setId       ( "stExercicio"          );
$obTxtExercicio->setValue    ( Sessao::getExercicio() );
$obTxtExercicio->setRotulo   ( "Exercício"            );
$obTxtExercicio->setTitle    ( "Informe o exercício." );
$obTxtExercicio->setInteiro  ( false                  );
$obTxtExercicio->setMaxLength( 4                      );
$obTxtExercicio->setSize     ( 5                      );
$obTxtExercicio->obEvento->setOnChange( "montaParametrosGET('limparFormEmpenho');");

$obEntidadeUsuario = new ITextBoxSelectEntidadeUsuario;
$obEntidadeUsuario->setCodEntidade($_REQUEST['cod_entidade']);
$obEntidadeUsuario->obTextBox->obEvento->setOnBlur( "montaParametrosGET('limparFormEmpenhoEntidade');");

// Define objeto BuscaInner para descrição e codigo do empenho
$obTxtCodEmpenho = new BuscaInner;
$obTxtCodEmpenho->setTitle  ( "Informe o número do empenho.");
$obTxtCodEmpenho->setRotulo ( "Número do Empenho"           );
$obTxtCodEmpenho->setId     ( "stDescEmpenho"               );
if (isset($stDescEmpenho)) {
    $obTxtCodEmpenho->setValue ( $stDescEmpenho );
}
$obTxtCodEmpenho->setNull                  ( true          );
$obTxtCodEmpenho->obCampoCod->setName      ( "inCodEmpenho");
$obTxtCodEmpenho->obCampoCod->setId        ( "inCodEmpenho");
$obTxtCodEmpenho->obCampoCod->setSize      ( 10            );
$obTxtCodEmpenho->obCampoCod->setMaxLength ( 10            );
$obTxtCodEmpenho->obCampoCod->setInteiro   ( true          );
$obTxtCodEmpenho->obCampoCod->obEvento->setOnBlur  ( "validaEmpenho(this);" );
$obTxtCodEmpenho->setFuncaoBusca("abrePopUp('".CAM_GF_EMP_POPUPS."empenho/FLEmpenho.php','frm','inCodEmpenho','stDescEmpenho','buscaTodosEmpenhos&inCodEntidade='+document.frm.inCodEntidade.value+'&stCampoExercicio=stExercicio&stExercicioEmpenho='+document.frm.stExercicio.value,'".Sessao::getId()."','800','550');");

$obTxtExercicioLicitacao = new TextBox;
$obTxtExercicioLicitacao->setName     ( "stExercicioLicitacao"  );
$obTxtExercicioLicitacao->setId       ( "stExercicioLicitacao"  );
$obTxtExercicioLicitacao->setRotulo   ( "Exercício do Processo Licitatório"            );
$obTxtExercicioLicitacao->setTitle    ( "Informe o exercício do Processo Licitatório." );
$obTxtExercicioLicitacao->setInteiro  ( true );
$obTxtExercicioLicitacao->setMaxLength( 4    );
$obTxtExercicioLicitacao->setSize     ( 5    );

$obTxtCodLicitacao = new TextBox;
$obTxtCodLicitacao->setName     ( "inCodLicitacao" );
$obTxtCodLicitacao->setId       ( "inCodLicitacao" );
$obTxtCodLicitacao->setRotulo   ( "Número do Processo Licitatório"  );
$obTxtCodLicitacao->setTitle    ( "Informe o Número do Processo Licitatório." );
$obTxtCodLicitacao->setInteiro  ( true );
$obTxtCodLicitacao->setMaxLength( 5    );
$obTxtCodLicitacao->setSize     ( 10   );

$obComprasModalidade = new TComprasModalidade();
$obComprasModalidade->recuperaTodos($rsRecordSet);

$obISelectModalidadeLicitacao = new Select();
$obISelectModalidadeLicitacao->setRotulo    ( "Modalidade"                     );
$obISelectModalidadeLicitacao->setTitle     ( "Selecione a modalidade."        );
$obISelectModalidadeLicitacao->setName      ( "inCodModalidade"                );
$obISelectModalidadeLicitacao->setId        ( "inCodModalidade"                );
$obISelectModalidadeLicitacao->setCampoID   ( "cod_modalidade"                 );
$obISelectModalidadeLicitacao->addOption    ( "","Selecione"                   );
$obISelectModalidadeLicitacao->setCampoDesc ( "[cod_modalidade] - [descricao]" );
$obISelectModalidadeLicitacao->preencheCombo( $rsRecordSet                     );

$obBtnIncluir = new Button;
$obBtnIncluir->setValue             ( "Incluir Empenho" );
$obBtnIncluir->setName              ( "btnIncluir"  );
$obBtnIncluir->setId                ( "btnIncluir"  );
$obBtnIncluir->obEvento->setOnClick ( "montaParametrosGET('incluirEmpenho','inId, stExercicio, inCodEntidade, inCodEmpenho, stExercicioLicitacao, inCodLicitacao, inCodModalidade');" );

$obBtnLimpar = new Button;
$obBtnLimpar->setName              ( "btnLimpar");
$obBtnLimpar->setId                ( "limpar" );
$obBtnLimpar->setValue             ( "Limpar" );
$obBtnLimpar->obEvento->setOnClick ( "montaParametrosGET('limparFormEmpenho');" );

$spnLista = new Span;
$spnLista->setId  ( 'spnListaEmpenho' );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addTitulo( "Configuração do Arquivo EMP" );
$obFormulario->addForm      ( $obForm            );
$obFormulario->addHidden    ( $obHdnAcao         );
$obFormulario->addHidden    ( $obHdnCtrl         );
$obFormulario->addHidden    ( $obHdnId           );
$obFormulario->addComponente( $obTxtExercicio    );
$obFormulario->addComponente( $obEntidadeUsuario );
$obFormulario->addComponente( $obTxtCodEmpenho   );
$obFormulario->addComponente( $obTxtExercicioLicitacao );
$obFormulario->addComponente( $obTxtCodLicitacao       );
$obFormulario->addComponente( $obISelectModalidadeLicitacao );
$obFormulario->agrupaComponentes( array( $obBtnIncluir, $obBtnLimpar ),"","" );

$obFormulario->addSpan      ( $spnLista );

$obOk  = new Ok();
$obFormulario->defineBarra(array( $obOk ));

$jsOnload = "montaParametrosGET('carregaDados','');";

$obFormulario->show();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
