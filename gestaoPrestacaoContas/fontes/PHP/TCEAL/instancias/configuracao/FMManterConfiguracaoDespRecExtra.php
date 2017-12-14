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
    * Pacote de configuração do TCEAL
    * Data de Criação   : 08/10/2013

    * @author Analista: Carlos Adriano
    * @author Desenvolvedor: Carlos Adriano
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stPrograma = "ManterConfiguracaoDespRecExtra";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include( $pgJs );

$stAcao = $request->get('stAcao');
Sessao::write('arContas', array());
Sessao::write('arExcluidas', array());

SistemaLegado::executaFrameOculto( "montaLista();" );

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnId = new Hidden;
$obHdnId->setName( "inId" );
$obHdnId->setId  ( "inId" );
$obHdnId->setValue( "" );

$arClassificacao = array(''  =>'Selecione',
                         '01'=>'01 - Restos a Pagar',
                         '02'=>'02 - Serviços da Dívida',
                         '03'=>'03 - Depósitos',
                         '04'=>'04 - Convênios',
                         '05'=>'05 - Débitos da Tesouraria',
                         '06'=>'06 - Outras Operações (Realizável)',
                         '07'=>'07 - Interferências Financeiras');

Sessao::write('arClassificacao', $arClassificacao);

$obCmbClassificacao = new Select();
$obCmbClassificacao->setName   ('inClassificacao');
$obCmbClassificacao->setId     ('inClassificacao');
$obCmbClassificacao->setRotulo ('Classificação');
$obCmbClassificacao->setValue  ('');
$obCmbClassificacao->setOptions($arClassificacao);

$obBscConta = new BuscaInner;
$obBscConta->setRotulo ( "Conta" );
$obBscConta->setTitle  ( "Informe a conta." );
$obBscConta->setId     ( "stConta" );
$obBscConta->setName   ( "stConta" );
$obBscConta->setValue  ( $stConta  );
$obBscConta->obCampoCod->setName ( "inCodConta" );
$obBscConta->obCampoCod->setId   ( "inCodConta" );
$obBscConta->obCampoCod->setNull ( true         );
$obBscConta->obCampoCod->setValue( $inCodConta  );
$obBscConta->obCampoCod->setAlign( "left"       );
$obBscConta->obCampoCod->obEvento->setOnChange("montaParametrosGET('buscaEstrutural','inClassificacao,inCodConta','true');");
$obBscConta->setFuncaoBusca("abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','inCodConta','stConta','conta_analitica_estrutural','".Sessao::getId()."&inCodIniEstrutural=1,2,5,6&tipoBusca2=extmmaa','800','550');");

$obBtnOk = new Button();
$obBtnOk->setId('btnIncluir');
$obBtnOk->setValue('Incluir');
$obBtnOk->obEvento->setOnClick("montaParametrosGET('incluirConta','inClassificacao,inCodConta','true');");

$obBtnAlterar = new Button();
$obBtnAlterar->setId('btnAlterar');
$obBtnAlterar->setValue('Alterar');
$obBtnAlterar->setStyle("display:none;");
$obBtnAlterar->obEvento->setOnClick("montaParametrosGET('alterarConta','inId,inClassificacao,inCodConta','true');");

$obBtnLimpar = new Button();
$obBtnLimpar->setValue('Limpar');
$obBtnLimpar->setId('btnLimpar');
$obBtnLimpar->obEvento->setOnClick("montaParametrosGET('limpaConta');");

$obSpnContas = new Span();
$obSpnContas->setId('spnContas');

//DEFINICAO DOS COMPONENTES
$obFormulario = new Formulario();
$obFormulario->addForm           ($obForm);
$obFormulario->addHidden         ($obHdnAcao);
$obFormulario->addHidden         ($obHdnId);
$obFormulario->addTitulo         ( "Conta" );
$obFormulario->addComponente     ($obCmbClassificacao);
$obFormulario->addComponente     ($obBscConta);
$obFormulario->agrupaComponentes (array($obBtnOk,$obBtnAlterar,$obBtnLimpar));
$obFormulario->addSpan           ($obSpnContas);
$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
