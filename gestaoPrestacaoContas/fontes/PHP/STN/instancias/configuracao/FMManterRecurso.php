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
    * Página de Formulário para Configuração
    * Data de Criação  : 15/05/2008

    * @author Analista Gelson W. Golçalves
    * @author Desenvolvedor Henrique Girardi dos Santos

    * @package URBEM
    * @subpackage

    * $Id: FMManterRecurso.php 66353 2016-08-16 20:04:08Z michel $

    * Casos de uso : uc-06.01.09

*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeGeral.class.php";
require_once CAM_GF_ORC_COMPONENTES."ISelectOrgao.class.php";
require_once CAM_GF_ORC_COMPONENTES."ISelectUnidade.class.php";
require_once CAM_GF_ORC_COMPONENTES."ISelectMultiploRecurso.class.php";
require_once CAM_GPC_STN_MAPEAMENTO."TSTNVinculoRecurso.class.php";
require_once TORC."TOrcamentoEntidade.class.php";
require_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoUnidade.class.php";

$stPrograma = "ManterRecurso";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once $pgJs;

$stAcao = $request->get('stAcao');

if ($stAcao == 1 || $stAcao == 2) {
    $obTSTNVinculoRecurso = new TSTNVinculoRecurso;
    $obTSTNVinculoRecurso->setDado('exercicio'    , Sessao::getExercicio());
    $obTSTNVinculoRecurso->setDado('cod_vinculo'  , $stAcao);
    $stOrder = "ORDER BY vinculo_recurso.cod_entidade, vinculo_recurso.num_orgao, vinculo_recurso.num_unidade, recurso.cod_recurso";
    $obTSTNVinculoRecurso->recuperaRelacionamento( $rsVinculoRelacionamento, "", $stOrder );

    $arRecursos  = array();
    $arStTipoRecurso[1] = "Recursos de Pagamento de Profissionais Magistério";
    $arStTipoRecurso[2] = "Recursos de Outras Despesas";
    $arStTipoEducacaoInfantil[1] = "Creche";
    $arStTipoEducacaoInfantil[2] = "Pré-Escola";

    while (!$rsVinculoRelacionamento->eof()) {
        $obTEntidade = new TOrcamentoEntidade();
        $obTEntidade->setDado('exercicio'    , Sessao::getExercicio());
        $obTEntidade->setDado('cod_entidade' , $rsVinculoRelacionamento->getCampo('cod_entidade'));
        $obTEntidade->recuperaRelacionamentoNomes( $rsEntidadesGeral );

        $obTOrcamentoUnidade = new TOrcamentoUnidade;
        $stFiltro  = "      AND  unidade.exercicio = '".Sessao::getExercicio()."'";
        $stFiltro .= "\n    AND  unidade.num_orgao = ".$rsVinculoRelacionamento->getCampo('num_orgao');
        $stFiltro .= "\n    AND  unidade.num_unidade = ".$rsVinculoRelacionamento->getCampo('num_unidade');
        $obTOrcamentoUnidade->recuperaRelacionamento( $rsUnidade, $stFiltro, 'orcamento.unidade.num_unidade' );

        $arTemp = array();
        $arTemp['inCodEntidade']          = $rsVinculoRelacionamento->getCampo('cod_entidade');
        $arTemp['stNomEntidade']          = $rsEntidadesGeral->getCampo('entidade');
        $arTemp['inCodOrgao']             = $rsVinculoRelacionamento->getCampo('num_orgao');
        $arTemp['stNomOrgao']             = $rsUnidade->getCampo('nom_orgao');
        $arTemp['inCodUnidade']           = $rsVinculoRelacionamento->getCampo('num_unidade');
        $arTemp['stNomUnidade']           = $rsUnidade->getCampo('nom_unidade');
        $arTemp['inTipoRecurso']          = $rsVinculoRelacionamento->getCampo('cod_tipo');
        $arTemp['stTipoRecurso']          = $arStTipoRecurso[$arTemp['inTipoRecurso']];
        $arTemp['inTipoEducacaoInfantil'] = $rsVinculoRelacionamento->getCampo('cod_tipo_educacao');
        $arTemp['stTipoEducacaoInfantil'] = $arStTipoEducacaoInfantil[$arTemp['inTipoEducacaoInfantil']];
        $arTemp['inCodRecurso']           = $rsVinculoRelacionamento->getCampo('cod_recurso');
        $arTemp['stDescricaoRecurso']     = $rsVinculoRelacionamento->getCampo('nom_recurso');
        $arTemp['inCodAcao']              = $rsVinculoRelacionamento->getCampo('cod_acao');
        $arTemp['stDescricaoAcao']        = ($arTemp['inCodAcao'] != '') ? $rsVinculoRelacionamento->getCampo('num_acao').' - '.$rsVinculoRelacionamento->getCampo('nom_acao') : '';

        $arRecursos[] = $arTemp;

        $rsVinculoRelacionamento->proximo();
    }

    Sessao::write("arRecursos", $arRecursos);

    $jsOnLoad = "montaParametrosGET( 'montaListaRecurso');";
}

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCodUnidade = new Hidden;
$obHdnCodUnidade->setName( "inCodigosUnidade" );
$obHdnCodUnidade->setValue( "" );

if ($stAcao == 1 || $stAcao == 2)
    $stJs = "montaParametrosGET( 'montaDadosRecursoFundebMDE', 'inCodEntidade, stCodEntidade, inCodOrgao, inCodUnidade, stAcao' )";
else
    $stJs = "montaParametrosGET( 'montaDadosRecurso', 'inCodEntidade, stCodEntidade, inCodOrgao, inCodUnidade, stAcao' )";

$obInCodEntidade = new ITextBoxSelectEntidadeGeral;
$obInCodEntidade->setExercicio( Sessao::getExercicio() );

$obInCodEntidade->obTextBox->obEvento->setOnChange($stJs);
$obInCodEntidade->obSelect->obEvento->setOnChange($stJs);
if ($stAcao == 1 || $stAcao == 2) {
    $obInCodEntidade->setRotulo('**Entidade');
    $obInCodEntidade->setNull(true);
}else
    $obInCodEntidade->setNull(false);

$obInCodOrgao = new ISelectOrgao;
$obInCodOrgao->setExercicio( Sessao::getExercicio() );
$obInCodOrgao->obEvento->setOnChange("montaParametrosGET( 'montaDadosUnidade', this.name, true ); ".$stJs);
if ($stAcao == 1 || $stAcao == 2) {
    $obInCodOrgao->setRotulo('**Órgao');
    $obInCodOrgao->setNull(true);
}else
    $obInCodOrgao->setNull(false);

$obInCodUnidade = new ISelectUnidade;
$obInCodUnidade->setExercicio( Sessao::getExercicio() );
$obInCodUnidade->setNull(false);
$obInCodUnidade->obEvento->setOnChange($stJs);
if ($stAcao == 1 || $stAcao == 2) {
    $obInCodUnidade->setRotulo('**Unidade');
    $obInCodUnidade->setNull(true);
}else
    $obInCodUnidade->setNull(false);

if ($stAcao == 1 || $stAcao == 2) {
    $obISelectMultiplRecurso2 = new ISelectMultiploRecurso;
    $obISelectMultiplRecurso2->setName("inCodRecurso2");
    $obISelectMultiplRecurso2->setNomeLista1 ("inCodRecursoDisponivel2");
    $obISelectMultiplRecurso2->setNomeLista2 ("inCodRecursoSelecionado2");
    $obISelectMultiplRecurso2->setRotulo("Recursos de Pagamento de Profissionais Magistério");
    $obISelectMultiplRecurso2->setExercicio( Sessao::getExercicio() );
    $obISelectMultiplRecurso2->setCarregarDados( false );
    $obISelectMultiplRecurso2->setFiltro( $stFiltro );

    $obCmbTipoRecurso = new Select;
    $obCmbTipoRecurso->setRotulo( "**Tipo de Recurso"                                         );
    $obCmbTipoRecurso->setName  ( "inTipoRecurso"                                             );
    $obCmbTipoRecurso->setId    ( "inTipoRecurso"                                             );
    $obCmbTipoRecurso->setTitle ( "Informe o tipo de recurso."                                );
    $obCmbTipoRecurso->addOption( ""    , "Selecione"                                         );
    $obCmbTipoRecurso->addOption( "1"   , "Recursos de Pagamento de Profissionais Magistério" );
    $obCmbTipoRecurso->addOption( "2"   , "Recursos de Outras Despesas"                       );
    $obCmbTipoRecurso->setNull  ( true                                                        );

    $obCmbTipoEducacaoInfantil = new Select;
    $obCmbTipoEducacaoInfantil->setRotulo( "Tipo Educação Infantil"                          );
    $obCmbTipoEducacaoInfantil->setName  ( "inTipoEducacaoInfantil"                          );
    $obCmbTipoEducacaoInfantil->setId    ( "inTipoEducacaoInfantil"                          );
    $obCmbTipoEducacaoInfantil->setTitle ( "Informe o tipo de recurso da educação infantil." );
    $obCmbTipoEducacaoInfantil->addOption( ""    , "Selecione"                               );
    $obCmbTipoEducacaoInfantil->addOption( "1"   , "Creche"                                  );
    $obCmbTipoEducacaoInfantil->addOption( "2"   , "Pré-Escola"                              );
    $obCmbTipoEducacaoInfantil->setNull  ( true                                              );
    $obCmbTipoEducacaoInfantil->setDisabled( true                                            );

    $obCmbRecurso = new Select;
    $obCmbRecurso->setRotulo( "**Recurso"                  );
    $obCmbRecurso->setName  ( "inCodRecurso"               );
    $obCmbRecurso->setId    ( "inCodRecurso"               );
    $obCmbRecurso->setTitle ( "Selecione o recurso"        );
    $obCmbRecurso->addOption( ""    , "Selecione"          );
    $obCmbRecurso->setNull  ( true                         );
    $obCmbRecurso->obEvento->setOnChange("montaParametrosGET('montaAcaoRecurso');");

    $obCmbAcao = new Select;
    $obCmbAcao->setRotulo( "Ação"                        );
    $obCmbAcao->setName  ( "inCodAcao"                   );
    $obCmbAcao->setId    ( "inCodAcao"                   );
    $obCmbAcao->setTitle ( "Selecione a ação do recurso" );
    $obCmbAcao->addOption( ""    , "Selecione"           );
    $obCmbAcao->setNull  ( true                          );
    $obCmbAcao->obEvento->setOnChange("montaParametrosGET('liberaTipoEducacao');");

    $obSpanListaRecurso = new Span;
    $obSpanListaRecurso->setId( "spnListaRecurso" );

    $obBtIncluir = new Button();
    $obBtIncluir->setId('btnIncluir');
    $obBtIncluir->setValue('Incluir');
    $obBtIncluir->obEvento->setOnClick("montaParametrosGET('incluirRecurso')");

    $obBtLimpar = new Button();
    $obBtLimpar->setId('btnLimpar');
    $obBtLimpar->setValue('Limpar');
    $obBtLimpar->obEvento->setOnClick("montaParametrosGET('limparRecurso');");
}

$obISelectMultiplRecurso = new ISelectMultiploRecurso;
if ($stAcao == '1') {$obISelectMultiplRecurso->setRotulo("Recursos de Outras Despesas");}
$obISelectMultiplRecurso->setExercicio( Sessao::getExercicio() );
$obISelectMultiplRecurso->setCarregarDados( false );
$obISelectMultiplRecurso->setFiltro( $stFiltro );

$stTitulo = SistemaLegado::pegaDado('descricao', 'stn.vinculo_stn_recurso', 'WHERE vinculo_stn_recurso.cod_vinculo = '.$stAcao);

//DEFINICAO DOS COMPONENTES

$obFormulario = new Formulario();
$obFormulario->addForm($obForm);

$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obHdnCodUnidade);
$obFormulario->addTitulo( "Vincular Recurso com ".mb_strtoupper($stTitulo,'UTF-8') );
$obFormulario->addComponente( $obInCodEntidade );
$obFormulario->addComponente( $obInCodOrgao );
$obFormulario->addComponente( $obInCodUnidade );

if ($stAcao == 1)
    $obFormulario->addComponente( $obCmbTipoRecurso );

if ($stAcao == 1 || $stAcao == 2) {
    $obFormulario->addComponente( $obCmbRecurso );
    $obFormulario->addComponente( $obCmbAcao );
    $obFormulario->addComponente( $obCmbTipoEducacaoInfantil );
    $obFormulario->agrupaComponentes( array($obBtIncluir,$obBtLimpar) );
    $obFormulario->addSpan( $obSpanListaRecurso );
}else{
    $obISelectMultiplRecurso->geraFormulario($obFormulario);
}

$obOk = new Ok();

$obLimpar = new Limpar;
$obLimpar->obEvento->setOnClick( "montaParametrosGET('limpar');");

$obFormulario->defineBarra( array( $obOk, $obLimpar ) );
$obFormulario->show();

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
