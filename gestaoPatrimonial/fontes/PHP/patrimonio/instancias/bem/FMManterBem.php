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

    * Data de Criação: 10/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    $Id: FMManterBem.php 62215 2015-04-08 21:28:32Z jean $

    * Casos de uso: uc-03.01.06

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioNatureza.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioGrupo.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioEspecie.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioBem.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioBemPlanoAnalitica.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioDepreciacao.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioReavaliacao.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioSituacaoBem.class.php";
include_once CAM_GP_PAT_COMPONENTES."IMontaClassificacao.class.php";
include_once CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php";
include_once CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php";
include_once CAM_GP_ALM_COMPONENTES."IPopUpMarca.class.php";
include_once CAM_GA_ORGAN_COMPONENTES."IMontaOrganograma.class.php";
include_once CAM_GA_ORGAN_COMPONENTES."IMontaOrganogramaLocal.class.php";
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php";
include_once CAM_GA_ORGAN_MAPEAMENTO."TOrganogramaOrganograma.class.php";
include_once CAM_GA_ORGAN_MAPEAMENTO."TOrganogramaOrgao.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoOrgaoOrcamentario.class.php";
//include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioGrupoPlanoDepreciacao.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioBemPlanoDepreciacao.class.php";
include_once(CAM_GA_PROT_COMPONENTES.'IPopUpProcesso.class.php');
include_once(CAM_GP_LIC_MAPEAMENTO."TLicitacaoLicitacao.class.php");
include_once(CAM_GP_PAT_MAPEAMENTO."TPatrimonioBemProcesso.class.php");

$stPrograma = "ManterBem";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$pgJsonAutocompletePlugin = "JSON".$stPrograma."AutocompletePlugin.php";
$pgJsAutoCompletePlugin   = "JS".$stPrograma."AutocompletePlugin.js";
$pgCssAutoCompletePlugin  = "CSS".$stPrograma."AutocompletePlugin.css";

# Carrega arquivos do plugin autocomplete
include_once $pgJsAutoCompletePlugin;
include_once $pgCssAutoCompletePlugin;
include_once $pgJs;

$stAcao = $request->get('stAcao');

Sessao::remove('rsAtributosDinamicos', '');

Sessao::write('arDepreciacao'        , array());
Sessao::write('arDepreciacaoExcluir' , array());

# Recupera o Organograma Ativo no sistema.
$obTOrganogramaOrganograma = new TOrganogramaOrganograma;
$obTOrganogramaOrganograma->setDado('ativo', true);
$obTOrganogramaOrganograma->recuperaOrganogramasAtivo($rsOrganogramaAtivo);

$inCodOrganogramaAtivo = $rsOrganogramaAtivo->getCampo('cod_organograma');

//se a acao for alterar, recupera os dados da base
if ($stAcao == 'alterar') {
    $obTPatrimonioBem = new TPatrimonioBem();
    $obTPatrimonioBem->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
    $obTPatrimonioBem->recuperaRelacionamento( $rsBem );
    
    $obTPatrimonioBem->recuperaSaldoBem( $rsSaldoBem );

    $obTPatrimonioReavaliacao = new TPatrimonioReavaliacao();
    $obTPatrimonioReavaliacao->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
    $obTPatrimonioReavaliacao->recuperaRelacionamento( $rsReavaliacao );

    # Recupera a última Reavaliação.
    $obTPatrimonioReavaliacao->recuperaUltimaReavaliacao ( $rsUltimaReavaliacao );

    $obTPatrimonioDepreciacao = new TPatrimonioDepreciacao();
    $obTPatrimonioDepreciacao->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
    $obTPatrimonioDepreciacao->recuperaDepreciacao( $rsDepreciacao );

    $obTPatrimonioBemPlanoAnalitica = new TPatrimonioBemPlanoAnalitica;
    $obTPatrimonioBemPlanoAnalitica->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
    $obTPatrimonioBemPlanoAnalitica->setDado( 'exercicio', Sessao::getExercicio());
    $obTPatrimonioBemPlanoAnalitica->recuperaMaxTimestampBemPlanoAnalitica($rsBemPlanoAnalitica);

    //pega a configuracao alterar_bens_exercicio_anterior
    $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();
    $obTAdministracaoConfiguracao->setDado( 'exercicio', Sessao::getExercicio() );
    $obTAdministracaoConfiguracao->setDado( 'cod_modulo', 6 );
    $obTAdministracaoConfiguracao->pegaConfiguracao( $boAltera, 'alterar_bens_exercicio_anterior' );

    $obTPatrimonioBemProcesso = new TPatrimonioBemProcesso();
    $obTPatrimonioBemProcesso->setDado('cod_bem', $_REQUEST['inCodBem']);
    $obTPatrimonioBemProcesso->recuperaPorChave($rsPatrimonioBemProcesso);
    
    //verifica e se necessário aplica o filtro
    if ( $boAltera == 'false' AND substr($rsBem->getCampo('dt_aquisicao'),6,11) < Sessao::getExercicio()) {
        SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao."&pg=".Sessao::read('pg')."&pos=".Sessao::read('pos'),'Permissão negada para excluir/alterar bem de exercício anterior. Verificar Configuração.',"incluir","aviso", Sessao::getId(), "../");
    }

    //cria hidden para o cod_bem
    $obHdnCodBem = new Hidden();
    $obHdnCodBem->setName( 'inCodBem' );
    $obHdnCodBem->setValue( $rsBem->getCampo('cod_bem') );

    //cria hidden para o Valor Atualizado Depreciacao
    $obHdnValorBem = new Hidden();
    $obHdnValorBem->setName( 'inVlBem' );
    $obHdnValorBem->setValue( $rsSaldoBem->getCampo('vl_bem') );

    //cria hidden para Valor Quota Depreciacao Acelerada Anual
    $obHdnDepreciacaoAcelerada = new Hidden();
    $obHdnDepreciacaoAcelerada->setName( 'flDepreciacaoAcelerada' );
    $obHdnDepreciacaoAcelerada->setId( 'flDepreciacaoAcelerada' );
    $obHdnDepreciacaoAcelerada->setValue( $rsBem->getCampo('quota_depreciacao_anual_acelerada') );

    //cria hidden para o Valor Atualizado Depreciacao
    $obHdnValorQuotaDepreciacaoAnual = new Hidden();
    $obHdnValorQuotaDepreciacaoAnual->setName( 'inVlQuotaDepreciacaoAnual' );
    $obHdnValorQuotaDepreciacaoAnual->setValue( $rsBem->getCampo('quota_depreciacao_anual') );

    //cria hidden para o Valor Atualizado Depreciacao
    $obHdnValorAtualizadoDepreciacao = new Hidden();
    $obHdnValorAtualizadoDepreciacao->setName( 'inVlAtualizadoDepreciacao' );
    $obHdnValorAtualizadoDepreciacao->setValue( $rsSaldoBem->getCampo('vl_atualizado') );

    //cria hidden para o Depreciacao Acumulada
    $obHdnDepreciacaoAcumulada = new Hidden();
    $obHdnDepreciacaoAcumulada->setName( 'inVlDepreciacaoAcumulada' );
    $obHdnDepreciacaoAcumulada->setValue( $rsSaldoBem->getCampo('vl_acumulado') );

    //cria hidden para o último plano de conta analitica utilizado
    $obHdnPlanoContaAnalitica = new Hidden();
    $obHdnPlanoContaAnalitica->setName( 'inPlanoContaAnalitica' );
    $obHdnPlanoContaAnalitica->setValue( $rsBemPlanoAnalitica->getCampo('cod_plano') );

    //cria hidden para o último plano de conta analitica utilizado
    $obHdnNomePlanoConta = new Hidden();
    $obHdnNomePlanoConta->setName( 'stNomePlanoConta' );
    $obHdnNomePlanoConta->setValue( $rsBemPlanoAnalitica->getCampo('nom_conta') );

    //cria label para demonstrar o código do bem
    $obLblCodBem = new Label();
    $obLblCodBem->setRotulo( 'Código do Bem' );
    $obLblCodBem->setValue( $rsBem->getCampo('cod_bem') );

    //executa métodos para pegar o max de cada tabela para compor a máscara da classificacao
    $obTPatrimonioNatureza = new TPatrimonioNatureza();
    $obTPatrimonioNatureza->proximoCod( $inMaxCodNatureza );

    $obTPatrimonioGrupo = new TPatrimonioGrupo();
    $obTPatrimonioGrupo->proximoCod( $inMaxCodGrupo );

    $obTPatrimonioEspecie = new TPatrimonioEspecie();
    $obTPatrimonioEspecie->proximoCod( $inMaxCodEspecie );

    //monta o codigo da classificacao
    $stClassificacao = str_pad($rsBem->getCampo('cod_natureza'),strlen($inMaxCodNatureza-1),'0',STR_PAD_LEFT).'.'.str_pad($rsBem->getCampo('cod_grupo'),strlen($inMaxCodGrupo-1),'0',STR_PAD_LEFT).'.'.str_pad($rsBem->getCampo('cod_especie'),strlen($inMaxCodEspecie-1),'0',STR_PAD_LEFT);

    //pega a mascara da localizacao
    $arMascaraLocalizacao = explode( '.', sistemaLegado::pegaConfiguracao( 'mascara_local', 2 ) );
    $arMascaraLocalizacao[4] = explode( '/', $arMascaraLocalizacao[4] );

    //monta a string da localizacao
    $codOrgao = $rsBem->getCampo('cod_orgao');
    $codLocal = $rsBem->getCampo('cod_local');
    $anoExercicio = Sessao::getExercicio();

    # Recupera o nome, organograma e código reduzido do Órgão vinculado no momento
    # da gravação do último registro.
    $obTOrganogramaOrgao = new TOrganogramaOrgao;
    $obTOrganogramaOrgao->setDado('cod_orgao'  , $codOrgao);
    $obTOrganogramaOrgao->setDado('vigencia'   , $rsBem->getCampo('data_historico_bem'));
    $obTOrganogramaOrgao->recuperaDadosUltimoOrgao($rsOrgaoAtualDescricao);

    $stDescricaoOrgaoAtual = $rsOrgaoAtualDescricao->getCampo('descricao');
    $stReduzidoOrgaoAtual  = $rsOrgaoAtualDescricao->getCampo('orgao');
    $inCodOrganogramaAtual = $rsOrgaoAtualDescricao->getCampo('cod_organograma');
  
    $obTPatrimonioBemPlanoDepreciacao = new TPatrimonioBemPlanoDepreciacao();
    $obTPatrimonioBemPlanoDepreciacao->setDado('cod_bem',$rsBem->getCampo('cod_bem'));

    $obTPatrimonioBemPlanoDepreciacao->recuperaRelacionamento($rsLista);
        
    $inCodPlanoDepreciacao = $rsLista->getCampo( 'cod_plano_depreciacao' );
    $stNomContaDepreciacao = $rsLista->getCampo( 'nom_conta_depreciacao' );
   
    $obHdnCodPlanoDepreciacao = new Hidden();
    $obHdnCodPlanoDepreciacao->setName( 'hdnCodPlanoDepreciacao' );
    $obHdnCodPlanoDepreciacao->setValue( $rsLista->getCampo( 'cod_plano_depreciacao' ) );
    
} else {
    $rsBem = new RecordSet();
    $rsSaldoBem = new RecordSet();
    $rsReavaliacao = new RecordSet();
    $rsUltimaReavaliacao = new RecordSet();
    $rsDepreciacao = new RecordSet();
    $rsBemPlanoAnalitica = new RecordSet();
}

//cria um novo formulario
$obForm = new Form;
$obForm->setAction ($pgProc);
$obForm->setTarget ("oculto");
$obForm->setEncType( "multipart/form-data" );

//Cria o hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

//cria a acao de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl");
$obHdnCtrl->setValue( '' );

//instancia o componente IMontaClassificacao
$obIMontaClassificacao = new IMontaClassificacao( $obForm );
$obIMontaClassificacao->obTxtCodClassificacao->setValue( $stClassificacao );
$obIMontaClassificacao->obTxtCodClassificacao->obEvento->setOnChange( $obIMontaClassificacao->obTxtCodClassificacao->obEvento->getOnChange().";montaParametrosGET( 'montaAtributos', 'stCodClassificacao' );" );
$obIMontaClassificacao->obISelectEspecie->obSelectEspecie->obEvento->setOnChange( $obIMontaClassificacao->obISelectEspecie->obSelectEspecie->obEvento->getOnChange().";montaParametrosGET( 'montaAtributos', 'inCodNatureza,inCodGrupo,inCodEspecie' );"  );

//caminho para URL que retorna o JSON do autocomplete
$obHdnJson = new Hidden;
$obHdnJson->setName ("stJson" );
$obHdnJson->setId   ("id_stJson");
$obHdnJson->setValue($pgJsonAutocompletePlugin);

//instancia um textbox para a descrição do bem
$obTxtDescricaoBem = new TextBox();
$obTxtDescricaoBem->setRotulo( 'Descrição' );
$obTxtDescricaoBem->setTitle( 'Informe a descrição do bem.' );
$obTxtDescricaoBem->setName( 'stNomBem' );
$obTxtDescricaoBem->setId( 'id_stNomBem' );
$obTxtDescricaoBem->setMaxLength( 100 );
$obTxtDescricaoBem->setSize( 100 );
$obTxtDescricaoBem->setNull( false );
$obTxtDescricaoBem->setValue( $rsBem->getCampo( 'descricao' ) );

if ( $stAcao == 'alterar' && $rsPatrimonioBemProcesso->getNumLinhas() > 0 ){
    $stProcesso = str_pad($rsPatrimonioBemProcesso->getCampo('cod_processo')."/".$rsPatrimonioBemProcesso->getCampo('ano_exercicio'),10,'0',STR_PAD_LEFT);
    $inCodProcesso = $rsPatrimonioBemProcesso->getCampo('cod_processo');
} else {
    $stProcesso = "";
}

$obHdnChaveProcesso = new Hidden;
$obHdnChaveProcesso->setId('hdnChaveProcesso');
$obHdnChaveProcesso->setName('hdnChaveProcesso');
$obHdnChaveProcesso->setValue($stProcesso);

//instancia a informação do processo que deu origem a aquisição do bem
$obPopUpProcesso = new IPopUpProcesso($obForm);
$obPopUpProcesso->setRotulo("Processo Administrativo");
$obPopUpProcesso->setValue ( $inCodProcesso );
$obPopUpProcesso->obCampoCod->setValue($stProcesso);
$obPopUpProcesso->setValidar(true);
$obPopUpProcesso->setNull   (true);
//$obPopUpProcesso->obCampoCod->obEvento->setOnBlur("jq('#stChaveProcesso').val('112');");

//instancia um text para o detalhamento do bem
$obTxtDetalhamentoBem = new TextArea();
$obTxtDetalhamentoBem->setRotulo( 'Detalhamento' );
$obTxtDetalhamentoBem->setTitle( 'Informe o detalhamento do bem.' );
$obTxtDetalhamentoBem->setName( 'stDetalhamentoBem' );
$obTxtDetalhamentoBem->setNull( true );
$obTxtDetalhamentoBem->setValue( $rsBem->getCampo( 'detalhamento' ) );

//instancia o componente de busca da marca do bem
$obBscMarca = new IPopUpMarca($obForm);
$obBscMarca->setNull(true);
$obBscMarca->setRotulo("Marca");
$obBscMarca->setTitle("Informe a marca do item.");
$obBscMarca->setValue 		     ( $rsBem->getCampo( 'nome_marca' ) );
$obBscMarca->obCampoCod->setValue( $rsBem->getCampo( 'cod_marca' ) );

//instancia o componente IPopUpCGM para o fornecedor
$obIPopUpCGMFornecedor = new IPopUpCGM( $obForm );
$obIPopUpCGMFornecedor->setRotulo           ( 'Fornecedor'            );
$obIPopUpCGMFornecedor->setTitle            ( 'Informe o fornecedor do bem.' );
$obIPopUpCGMFornecedor->setName             ( 'stNomFornecedor'       );
$obIPopUpCGMFornecedor->setId               ( 'stNomFornecedor'       );
$obIPopUpCGMFornecedor->obCampoCod->setName ( 'inCodFornecedor'       );
$obIPopUpCGMFornecedor->obCampoCod->setId   ( 'inCodFornecedor'       );
$obIPopUpCGMFornecedor->setNull             ( false                   );
$obIPopUpCGMFornecedor->setValue 	    ( $rsBem->getCampo( 'nom_fornecedor' ) );
$obIPopUpCGMFornecedor->obCampoCod->setValue( $rsBem->getCampo( 'num_fornecedor' ) );

//instancia um componente Moeda par ao valor do bem

if ($stAcao == 'alterar') {
    $rsReavaliacao = new RecordSet;
    $obTPatrimonioReavaliacao = new TPatrimonioReavaliacao();
    $obTPatrimonioReavaliacao->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
    $obTPatrimonioReavaliacao->recuperaRelacionamento( $rsReavaliacao );

    $rsDepreciacao = new RecordSet;
    $obTPatrimonioDepreciacao = new TPatrimonioDepreciacao();
    $obTPatrimonioDepreciacao->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
    $obTPatrimonioDepreciacao->recuperaDepreciacao( $rsDepreciacao );
}

if ($rsReavaliacao->getNumLinhas() > 0 || $rsDepreciacao->getNumLinhas() > 0) {
    // SE o bem possuir alguma depreciação, não pode alterar seu valor. Ao menos que seja feitra a anulação de todas as competencias.
    $obLblInValorBem = new Label;
    $obLblInValorBem->setRotulo( 'Valor do Bem' );
    $obLblInValorBem->setTitle( 'Informe o valor do bem.' );
    $obLblInValorBem->setName( 'stinValorBem' );
    $obLblInValorBem->setValue( '0,00' );
    $obLblInValorBem->setNull( false );
    $obLblInValorBem->setValue( $rsBem->getCampo( 'vl_bem' ) != '' ? number_format($rsBem->getCampo( 'vl_bem' ),2,',','.') : '' );
    //cria hidden para o valor do bem
    $obHdnInValorBem = new Hidden;
    $obHdnInValorBem->setRotulo( 'Valor do Bem' );
    $obHdnInValorBem->setTitle( 'Informe o valor do bem.' );
    $obHdnInValorBem->setName( 'inValorBem' );
    $obHdnInValorBem->setValue( '0,00' );
    $obHdnInValorBem->setNull( false );
    $obHdnInValorBem->setValue( $rsBem->getCampo( 'vl_bem' ) != '' ? number_format($rsBem->getCampo( 'vl_bem' ),2,',','.') : '' );

} else {
    $obInValorBem = new Moeda();
    $obInValorBem->setRotulo( 'Valor do Bem' );
    $obInValorBem->setTitle( 'Informe o valor do bem.' );
    $obInValorBem->setName( 'inValorBem' );
    $obInValorBem->setValue( '0,00' );
    $obInValorBem->setNull( false );
    $obInValorBem->setValue( $rsBem->getCampo( 'vl_bem' ) != '' ? number_format($rsBem->getCampo( 'vl_bem' ),2,',','.') : '' );
}

# Valor da depreciação inicial.
$obInValorDepreciacao = new Moeda();
$obInValorDepreciacao->setRotulo('Valor da Depreciação Inicial');
$obInValorDepreciacao->setTitle ('Informe o valor da depreciação.');
$obInValorDepreciacao->setName  ('inValorDepreciacao');
$obInValorDepreciacao->setNull  (true);
$obInValorDepreciacao->setValue ($rsBem->getCampo( 'vl_depreciacao' ) != '' ? number_format($rsBem->getCampo( 'vl_depreciacao' ),2,',','.') : '0,00' );

$obLblDepreciacaoAcumuladaExercicio = new Label();
$obLblDepreciacaoAcumuladaExercicio->setRotulo( 'Depreciação Acumulada' );
$obLblDepreciacaoAcumuladaExercicio->setValue( $rsSaldoBem->getCampo('vl_acumulado') != '' ? number_format($rsSaldoBem->getCampo('vl_acumulado'),2,',','.') : '0,00');

$obLblDataUltimaReavaliacao = new Label();
$obLblDataUltimaReavaliacao->setRotulo ( 'Data Última Reavaliação' );
$obLblDataUltimaReavaliacao->setValue  ( $rsUltimaReavaliacao->getCampo('dt_reavaliacao') );

$obLblValorltimaReavaliacao = new Label();
$obLblValorltimaReavaliacao->setRotulo ( 'Valor Última Reavaliação' );
$obLblValorltimaReavaliacao->setValue  ( $rsUltimaReavaliacao->getCampo('vl_reavaliacao') != '' ? number_format($rsUltimaReavaliacao->getCampo('vl_reavaliacao'),2,',','.') : '');

//instancia um compenente data para a data de depreciação
$obDtDepreciacao = new Data();
$obDtDepreciacao->setRotulo( 'Data da Depreciação' );
$obDtDepreciacao->setTitle( 'Informe a data da depreciação do bem.' );
$obDtDepreciacao->setName( 'dtDepreciacao' );
$obDtDepreciacao->setNull( true );
$obDtDepreciacao->setValue( $rsBem->getCampo( 'dt_depreciacao' ) );

//instancia um componente data para a data de aquisição
$obDtAquisicao = new Data();
$obDtAquisicao->setRotulo( 'Data da Aquisição' );
$obDtAquisicao->setTitle( 'Informe a data da aquisição do bem.' );
$obDtAquisicao->setName( 'dtAquisicao' );
$obDtAquisicao->setNull( false );
$obDtAquisicao->setValue( $rsBem->getCampo( 'dt_aquisicao' ) );

//instancia componente TextBox para a vida util do bem
$obInVidaUtil = new Inteiro();
$obInVidaUtil->setRotulo( 'Vida Útil' );
$obInVidaUtil->setTitle( 'Informe a vida útil do bem em anos.' );
$obInVidaUtil->setName( 'inVidaUtil' );
$obInVidaUtil->setValue( $rsBem->getCampo( 'vida_util' ) );
$obInVidaUtil->setMaxLength(3);

//instancia um componente data para a data de incorporação
$obDtIncorporacao = new Data();
$obDtIncorporacao->setRotulo( 'Data de Incorporação' );
$obDtIncorporacao->setTitle( 'Informe a data de liquidação contábil.' );
$obDtIncorporacao->setName( 'dtIncorporacao' );
$obDtIncorporacao->setNull( true );
$obDtIncorporacao->setValue( $rsBem->getCampo( 'dt_incorporacao' ) );

//instancia um compenente data para a data de vencimento da garantia
$obDtVencimento = new Data();
$obDtVencimento->setRotulo( 'Vencimento da Garantia' );
$obDtVencimento->setTitle( 'Informe a data de vencimento da garantia.' );
$obDtVencimento->setName( 'dtVencimento' );
$obDtVencimento->setNull( true );
$obDtVencimento->setValue( $rsBem->getCampo( 'dt_garantia' ) );

$obHdnRecMax = new Hidden;
$obHdnRecMax->setName ("recuperaMax");
$obHdnRecMax->setValue(($stAcao == 'lote') ? 'true' : 'false');

//cria o componente radio para a placa de identificação
$obRdPlacaIdentificacaoSim = new Radio();
$obRdPlacaIdentificacaoSim->setRotulo ('Placa de Identificação');
$obRdPlacaIdentificacaoSim->setTitle  ('Informe se o item possui placa de identificação.');
$obRdPlacaIdentificacaoSim->setId     ('stPlacaIdentificacao');
$obRdPlacaIdentificacaoSim->setName   ('stPlacaIdentificacao');
$obRdPlacaIdentificacaoSim->setValue  ('sim');
$obRdPlacaIdentificacaoSim->setLabel  ('Sim');

if ($stAcao != 'lote') {
    $obRdPlacaIdentificacaoSim->obEvento->setOnClick( "montaParametrosGET('montaPlacaIdentificacao', 'stPlacaIdentificacao' );" );
} else {
    $obRdPlacaIdentificacaoSim->obEvento->setOnClick( "montaParametrosGET('montaPlacaIdentificacaoLote', 'stPlacaIdentificacao,recuperaMax' );" );
}

$obRdPlacaIdentificacaoNao = new Radio();
$obRdPlacaIdentificacaoNao->setRotulo ('Placa de Identificação');
$obRdPlacaIdentificacaoNao->setTitle  ('Informe se o item possui placa de identificação');
$obRdPlacaIdentificacaoNao->setId     ('stPlacaIdentificacao');
$obRdPlacaIdentificacaoNao->setName   ('stPlacaIdentificacao');
$obRdPlacaIdentificacaoNao->setValue  ('nao');
$obRdPlacaIdentificacaoNao->setLabel  ('Não');

if ($stAcao != 'lote') {
    $obRdPlacaIdentificacaoNao->obEvento->setOnClick( "montaParametrosGET( 'montaPlacaIdentificacao', 'stPlacaIdentificacao' );" );
} else {
    $obRdPlacaIdentificacaoNao->obEvento->setOnClick( "montaParametrosGET( 'montaPlacaIdentificacaoLote', 'stPlacaIdentificacao,recuperaMax' );" );
}

if ($rsBem->getCampo('identificacao') == 't' || $rsBem->getCampo('identificacao') == '') {
    $obRdPlacaIdentificacaoSim->setChecked(true);
} else {
    $obRdPlacaIdentificacaoNao->setChecked(true);
}

//cria span para o número da placa do bem
$obSpnNumeroPlaca = new Span();
$obSpnNumeroPlaca->setId( 'spnNumeroPlaca' );

//span para os atributos da especie
$obSpnAtributos = new Span();
$obSpnAtributos->setId( 'spnAtributos' );

//
//será implementado futuramente a integração com a GF
//

//instancia um select para as entidades
$obSlEntidade = new Select();
$obSlEntidade->setRotulo( 'Entidade' );
$obSlEntidade->setTitle( 'Selecione a entidade do bem.' );
$obSlEntidade->setName( 'inCodEntidade' );
$obSlEntidade->setId( 'inCodEntidade' );
$obSlEntidade->addOption( '','Selecione' );
$obSlEntidade->setNull( false );

### Unidade Orçamentária ###

$obROrcamentoOrgaoOrcamentario = new ROrcamentoOrgaoOrcamentario();
$obROrcamentoOrgaoOrcamentario->setExercicio(Sessao::getExercicio() );
$obROrcamentoOrgaoOrcamentario->listar($rsCombo);

$obTxtOrgao = new TextBox;
$obTxtOrgao->setRotulo   ("Órgão");
$obTxtOrgao->setTitle    ("Informe o órgão para filtro");
$obTxtOrgao->setName     ("inCodOrgaoTxt");
$obTxtOrgao->setId       ("inCodOrgaoTxt");
$obTxtOrgao->setValue    ($rsBem->getCampo('num_orgao'));
$obTxtOrgao->setSize     (6);
$obTxtOrgao->setMaxLength(3);
$obTxtOrgao->setInteiro  (true);
$obTxtOrgao->obEvento->setOnChange("montaParametrosGET('MontaUnidade');" );
                       
$obTxtUnidade = new TextBox;
$obTxtUnidade->setRotulo   ("Unidade");
$obTxtUnidade->setTitle    ("Informe a unidade para filtro");
$obTxtUnidade->setName     ("inCodUnidadeTxt");
$obTxtUnidade->setId       ("inCodUnidadeTxt");
$obTxtUnidade->setValue    ($rsBem->getCampo('num_unidade'));
$obTxtUnidade->setSize     (6);
$obTxtUnidade->setMaxLength(3);
$obTxtUnidade->setInteiro  (true);

$obCmbOrgao = new Select;
$obCmbOrgao->setRotulo            ("Órgão");
$obCmbOrgao->setName              ("inCodOrgao");
$obCmbOrgao->setId                ("inCodOrgao");
$obCmbOrgao->setValue             ($rsBem->getCampo('num_orgao'));
$obCmbOrgao->setStyle             ("width: 200px");
$obCmbOrgao->setCampoID           ("num_orgao");
$obCmbOrgao->setCampoDesc         ("nom_orgao");
$obCmbOrgao->addOption            ('', 'Selecione');
$obCmbOrgao->preencheCombo        ($rsCombo);
$obCmbOrgao->obEvento->setOnChange("montaParametrosGET('MontaUnidade');" );

$obCmbUnidade= new Select;
$obCmbUnidade->setRotulo   ("Unidade");
$obCmbUnidade->setName     ("inCodUnidade");
$obCmbUnidade->setId       ("inCodUnidade");
$obCmbUnidade->setValue    ($rsBem->getCampo('num_unidade'));
$obCmbUnidade->setStyle    ("width: 200px");
$obCmbUnidade->setCampoID  ("cod_unidade");
$obCmbUnidade->setCampoDesc("descricao");
$obCmbUnidade->addOption   ('', 'Selecione');
               
//instancia componente TextBox para o ano do empenho
$obExercicioEmpenho = new Exercicio();
$obExercicioEmpenho->setRotulo( 'Exercício' );
$obExercicioEmpenho->setNull( false);
$obExercicioEmpenho->setId( 'stExercicio' );
$obExercicioEmpenho->setValue( $rsBem->getCampo( 'exercicio' ) );
$obExercicioEmpenho->obEvento->setOnChange( "montaParametrosGET( 'preencheComboEntidade','stExercicio' ); " );

//instancia o componente Inteiro para o empenho
$obNumEmpenho = new Inteiro();
$obNumEmpenho->setRotulo( 'Número do Empenho' );
$obNumEmpenho->setTitle( 'Informe o número do empenho do bem.' );
$obNumEmpenho->setName( 'inNumEmpenho' );
$obNumEmpenho->setNull( true );
$obNumEmpenho->setValue( $rsBem->getCampo( 'cod_empenho' ) );

//instancia o componente Inteiro para a nota fiscal
$obNumNotaFiscal = new Inteiro();
$obNumNotaFiscal->setRotulo( 'Número da Nota Fiscal' );
$obNumNotaFiscal->setTitle( 'Informe o número da nota fiscal.' );
$obNumNotaFiscal->setName( 'stNumNotaFiscal' );
$obNumNotaFiscal->setNull( true );
$obNumNotaFiscal->setMaxLength(30);
$obNumNotaFiscal->setSize(25);
$obNumNotaFiscal->setValue( $rsBem->getCampo( 'nota_fiscal' ) );

//instancia o componente Data para data da Nota Fiscal 
$obDataNotaFiscal = new Data();
$obDataNotaFiscal->setRotulo( 'Data da Nota Fiscal ' );
$obDataNotaFiscal->setTitle( 'Informe a data da nota fiscal.' );
$obDataNotaFiscal->setName( 'dataNotaFiscal' );
$obDataNotaFiscal->setNull( true );
$obDataNotaFiscal->setValue( $rsBem->getCampo( 'data_nota_fiscal' ) );

$obFileArquivoNF = new FileBox;
$obFileArquivoNF->setNull   ( true                           );
$obFileArquivoNF->setRotulo ( "Arquivo Nota Fiscal"          );
$obFileArquivoNF->setTitle  ( "Informe o caminho do arquivo" );
$obFileArquivoNF->setName   ( "fileArquivoNF"                );
$obFileArquivoNF->setId     ( "fileArquivoNF"                );
$obFileArquivoNF->setSize   ( 35                             );
$obFileArquivoNF->setValue  ( ""  );
$obFileArquivoNF->obEvento->setOnChange( 'validarArquivo();' );

$obLocalizacao = new Link;
$obLocalizacao->setRotulo("Download da Nota Fiscal");
$obLocalizacao->setHref( CAM_GP_PAT_ANEXOS.$rsBem->getCampo( 'caminho_nf' ));
if($rsBem->getCampo( 'caminho_nf' ) != '') {
    $obLocalizacao->setValue ($rsBem->getCampo( 'caminho_nf' ));
}
$obLocalizacao->setTarget("oculto");


if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 02) {
    include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALTipoDocumentoFiscal.class.php';

    $obTCEALTipoDocumentoFiscal= new TTCEALTipoDocumentoFiscal;
    $obTCEALTipoDocumentoFiscal->recuperaTodos($rsTipoDocumentoFiscal);
    
    $obCmbTipoDocFiscal = new Select();
    $obCmbTipoDocFiscal->setRotulo( 'Tipo do Documento Fiscal '                    );
    $obCmbTipoDocFiscal->setName( 'inCodTipoDocFiscal'                             );
    $obCmbTipoDocFiscal->setId( 'inCodTipoDocFiscal'                               );
    $obCmbTipoDocFiscal->setTitle( 'Informe o tipo do documento fiscal.'           );
    $obCmbTipoDocFiscal->setValue( $rsBem->getCampo( 'cod_tipo_documento_fiscal')  );
    $obCmbTipoDocFiscal->setNull(true                                              );
    $obCmbTipoDocFiscal->setCampoId( 'cod_tipo_documento_fiscal'                   );
    $obCmbTipoDocFiscal->setCampoDesc  ("[cod_tipo_documento_fiscal] - [descricao]");
    $obCmbTipoDocFiscal->addOption('','Selecione'                                  );
    $obCmbTipoDocFiscal->preencheCombo($rsTipoDocumentoFiscal                      );
}

//instancia o componente IPopUpCGM para o responsavel
$obIPopUpCGMResponsavel = new IPopUpCGM( $obForm );
$obIPopUpCGMResponsavel->setRotulo           ( 'Responsável'            );
$obIPopUpCGMResponsavel->setTitle            ( 'Informe o responsável pelo bem.' );
$obIPopUpCGMResponsavel->setName             ( 'stNomResponsavel'       );
$obIPopUpCGMResponsavel->setId               ( 'stNomResponsavel'       );
$obIPopUpCGMResponsavel->obCampoCod->setName ( 'inNumResponsavel'       );
$obIPopUpCGMResponsavel->obCampoCod->setId   ( 'inNumResponsavel'       );
$obIPopUpCGMResponsavel->setNull             ( false                    );
$obIPopUpCGMResponsavel->setValue 			  ( $rsBem->getCampo( 'nom_responsavel' ) );
$obIPopUpCGMResponsavel->obCampoCod->setValue( $rsBem->getCampo( 'num_responsavel' ) );

//instancia o componente data para o responsavel
$obDtInicioResponsavel = new Data();
$obDtInicioResponsavel->setRotulo( 'Data de Início' );
$obDtInicioResponsavel->setTitle( 'Informe a data de início do responsável pelo bem.' );
$obDtInicioResponsavel->setName( 'dtInicioResponsavel' );
$obDtInicioResponsavel->setNull( false );
$obDtInicioResponsavel->setValue( $rsBem->getCampo( 'dt_inicio' ) );

$obLblOrgaoAtual = new Label;
$obLblOrgaoAtual->setName   ('stOrgaoAtual');
$obLblOrgaoAtual->setValue  ($stReduzidoOrgaoAtual.' - '.$stDescricaoOrgaoAtual);
$obLblOrgaoAtual->setRotulo ('Classificação Anterior');

$obLblOrganograma = new Label;
$obLblOrganograma->setName   ( "organogramaAtivo"  );
$obLblOrganograma->setValue  ( $rsOrganogramaAtivo->getCampo('cod_organograma').' - '.$rsOrganogramaAtivo->getCampo('implantacao') );
$obLblOrganograma->setRotulo ( 'Organograma Ativo' );

//instancia o componenete IMontaOrganograma

$obIMontaOrganograma = new IMontaOrganograma(false);

if ($inCodOrganogramaAtual == $inCodOrganogramaAtivo) {
    $obIMontaOrganograma->setCodOrgao($codOrgao);
}

$obIMontaOrganograma->setCadastroOrganograma(true);
$obIMontaOrganograma->setNivelObrigatorio(1);
$obIMontaOrganograma->setStyle('width:300px');

$obIMontaOrganogramaLocal = new IMontaOrganogramaLocal;
$obIMontaOrganogramaLocal->setValue($codLocal);
$obIMontaOrganogramaLocal->setNull(false);

//instancio o componente TextBoxSelect para a situacao do bem
$obITextBoxSelectSituacao = new TextBoxSelect();
$obITextBoxSelectSituacao->setRotulo( 'Situação' );
$obITextBoxSelectSituacao->setTitle( 'Informe a situação do bem.' );
$obITextBoxSelectSituacao->setName( 'inCodTxtSituacao' );
$obITextBoxSelectSituacao->setNull( false );

$obITextBoxSelectSituacao->obTextBox->setName      ( "inCodTxtSituacao" );
$obITextBoxSelectSituacao->obTextBox->setId        ( "inCodTxtSituacao" );
$obITextBoxSelectSituacao->obTextBox->setSize      ( 6                  );
$obITextBoxSelectSituacao->obTextBox->setMaxLength ( 3                  );
$obITextBoxSelectSituacao->obTextBox->setInteiro   ( true               );
$obITextBoxSelectSituacao->obTextBox->setValue 	   ( $rsBem->getCampo('cod_situacao'));

$obITextBoxSelectSituacao->obSelect->setName      ( "inCodSituacao" );
$obITextBoxSelectSituacao->obSelect->setId        ( "inCodSituacao" );
$obITextBoxSelectSituacao->obSelect->setStyle     ( "width: 200px"  );
$obITextBoxSelectSituacao->obSelect->setCampoID   ( "cod_situacao"  );
$obITextBoxSelectSituacao->obSelect->setCampoDesc ( "nom_situacao"  );
$obITextBoxSelectSituacao->obSelect->addOption    ( "", "Selecione" );

//recupero todos os registros da table patrimonio.situacao_bem e preencho o componenete ITextBoxSelect
$obTPatrimonioSituacaoBem = new TPatrimonioSituacaoBem();
$obTPatrimonioSituacaoBem->recuperaTodos( $rsSituacaoBem );

$obITextBoxSelectSituacao->obSelect->preencheCombo( $rsSituacaoBem );
$obITextBoxSelectSituacao->obSelect->setValue( $rsBem->getCampo( 'cod_situacao' ) );

//instancia um componente TextBox para a descricao da situacao
$obTxtDescricaoSituacao = new TextBox();
$obTxtDescricaoSituacao->setRotulo   ( 'Descrição da Situação' );
$obTxtDescricaoSituacao->setTitle    ( 'Informe a descrição da situação do bem.' );
$obTxtDescricaoSituacao->setName     ( 'stDescricaoSituacao' );
$obTxtDescricaoSituacao->setNull     ( true );
$obTxtDescricaoSituacao->setMaxLength( 100 );
$obTxtDescricaoSituacao->setSize     ( 70 );
$obTxtDescricaoSituacao->setValue( $rsBem->getCampo( 'historico_descricao' ) );

$obRdApoliceSim = new Radio();
$obRdApoliceSim->setName  ( 'stApolice' );
$obRdApoliceSim->setRotulo( 'Bem Segurado' );
$obRdApoliceSim->setTitle ( 'Informe se o bem possui apólice.' );
$obRdApoliceSim->setLabel ( 'Sim' );
$obRdApoliceSim->setNull  ( true );
$obRdApoliceSim->setValue ( 'sim' );
$obRdApoliceSim->setNull  ( false );
$obRdApoliceSim->obEvento->setOnClick( "montaParametrosGET( 'montaApolice', 'stApolice' );" );

$obRdApoliceNao = new Radio();
$obRdApoliceNao->setName( 'stApolice' );
$obRdApoliceNao->setRotulo( 'Informe se o bem possui apólice.' );
$obRdApoliceNao->setLabel( 'Não' );
$obRdApoliceNao->setNull( true );
$obRdApoliceNao->setValue( 'nao' );
$obRdApoliceNao->setNull( false );
$obRdApoliceNao->obEvento->setOnClick( "montaParametrosGET( 'montaApolice', 'stApolice' );" );

if ( $rsBem->getCampo( 'cod_apolice' ) != '' ) {
    $obRdApoliceSim->setChecked( true );
} else {
    $obRdApoliceNao->setChecked( true );
}

$obSpnApolice = new Span();
$obSpnApolice->setId( 'spnApolice' );

$obInQtdeLote = new Inteiro();
$obInQtdeLote->setRotulo( 'Quantidade de Bens a Incluir' );
$obInQtdeLote->setTitle( 'Informe a quantidade de bens a incluir.' );
$obInQtdeLote->setName( 'inQtdeLote' );
$obInQtdeLote->setNull( false );
$obInQtdeLote->setMaxLength(7);
$obInQtdeLote->obEvento->setOnChange( "montaParametrosGET( 'verificaIntervalo','stNumeroPlaca,inQtdeLote' );" );

/*
 * Componentes reavaliação
 */

//cria label para demonstrar o código do bem
$obLblCodBemReavaliacao = new Label();
$obLblCodBemReavaliacao->setRotulo( 'Código do Bem' );
$obLblCodBemReavaliacao->setValue( $rsBem->getCampo('cod_bem') );

//instancia um componente data para a data de reavaliação
$obDtReavalicao = new Data();
$obDtReavalicao->setRotulo( '**Data da Reavaliação' );
$obDtReavalicao->setTitle( 'Informe a data da reavaliação do bem.' );
$obDtReavalicao->setName( 'dtReavaliacao' );
$obDtReavalicao->setId( 'dtReavaliacao' );

//instancia componente TextBox para a vida util do bem
$obInVidaUtilReavalicao = new Inteiro();
$obInVidaUtilReavalicao->setRotulo( '**Vida Útil' );
$obInVidaUtilReavalicao->setTitle( 'Informe a vida útil do bem em anos.' );
$obInVidaUtilReavalicao->setName( 'inVidaUtilReavaliacao' );
$obInVidaUtilReavalicao->setId( 'inVidaUtilReavaliacao' );
$obInVidaUtilReavalicao->setMaxLength(3);

//instancia um componente Moeda par ao valor do bem reavaliado
$obFlValorBemReavaliacao = new Moeda();
$obFlValorBemReavaliacao->setRotulo( '**Valor da Reavaliação' );
$obFlValorBemReavaliacao->setTitle( 'Informe o valor da reavaliação do bem.' );
$obFlValorBemReavaliacao->setName( 'flValorBemReavaliacao' );
$obFlValorBemReavaliacao->setId( 'flValorBemReavaliacao' );
$obFlValorBemReavaliacao->setValue( '0,00' );

//instancia um textbox para o motivo da reavaliação
$obTxtMotivo = new TextArea();
$obTxtMotivo->setRotulo( '**Motivo' );
$obTxtMotivo->setTitle( 'Informe o motivo da reavaliação.' );
$obTxtMotivo->setName( 'stMotivoReavaliacao' );
$obTxtMotivo->setId( 'stMotivoReavaliacao' );
$obTxtMotivo->setMaxCaracteres(100);

$obBtnIncluirReavaliacao = new Button;
$obBtnIncluirReavaliacao->setName('stIncluirReavaliacao');
$obBtnIncluirReavaliacao->setValue('Incluir');
$obBtnIncluirReavaliacao->obEvento->setOnClick("montaParametrosGET( 'montaListaReavaliacoes', 'inCodBem,stIncluirReavaliacao,dtReavaliacao,inVidaUtilReavaliacao,flValorBemReavaliacao,stMotivoReavaliacao,dtAquisicao,dtIncorporacao' );");

$obBtnLimparReavaliacao = new Button;
$obBtnLimparReavaliacao->setName('stLimparReavaliacao');
$obBtnLimparReavaliacao->setValue('Limpar');
$obBtnLimparReavaliacao->obEvento->setOnClick("montaParametrosGET( 'montaListaReavaliacoes','stLimparReavaliacao');");

$obSpnListaReavaliacao = new Span;
$obSpnListaReavaliacao->setId('stSpnListaReavaliacao');

/*
 * Componentes depreciação
 */

//cria label para demonstrar o código do bem
$obLblCodBemDepreciacao = new Label();
$obLblCodBemDepreciacao->setRotulo( 'Código do Bem' );
$obLblCodBemDepreciacao->setId( 'obLblCodBemDepreciacao' );
$obLblCodBemDepreciacao->setValue( $rsBem->getCampo('cod_bem') );

$obLblValorAtualizadoDepreciacao = new Label();
$obLblValorAtualizadoDepreciacao->setRotulo( 'Valor Atualizado' );
$obLblValorAtualizadoDepreciacao->setValue( $rsSaldoBem->getCampo('vl_atualizado') != '' ? number_format($rsSaldoBem->getCampo('vl_atualizado'),2,',','.') : '');
$obLblValorAtualizadoDepreciacao->setTitle('Valor original menos a depreciação acumulada. Caso tenha reavaliação, valor da última reavaliação menos a depreciação acumulada.');

$obLblDepreciacaoAcumulada = new Label();
$obLblDepreciacaoAcumulada->setRotulo( 'Depreciação Acumulada' );
$obLblDepreciacaoAcumulada->setValue( $rsSaldoBem->getCampo('vl_acumulado') != '' ? number_format($rsSaldoBem->getCampo('vl_acumulado'),2,',','.') : '');

$obRdDepreciavelSim = new Radio;
$obRdDepreciavelSim->setRotulo('Depreciável');
$obRdDepreciavelSim->setName('boDepreciavel');
$obRdDepreciavelSim->setTitle('Define se o bem sofrerá depreciação.');
$obRdDepreciavelSim->setValue('true');
$obRdDepreciavelSim->setLabel('Sim');
$obRdDepreciavelSim->obEvento->setOnChange("montaParametrosGET( 'montaDepreciacao', 'stAcao,boDepreciavel,inVlBem,flDepreciacaoAcelerada,inVlQuotaDepreciacaoAnual,inVlAtualizadoDepreciacao,inVlDepreciacaoAcumulada,inPlanoContaAnalitica,stNomePlanoConta' );");

$obRdDepreciavelNao = new Radio;
$obRdDepreciavelNao->setName('boDepreciavel');
$obRdDepreciavelNao->setTitle('Define se o bem sofrerá depreciação.');
$obRdDepreciavelNao->setValue('false');
$obRdDepreciavelNao->setLabel('Não');

//cria um busca inner para retornar uma Conta Contábil de Depreciação Acumulada
$obBscContaContabilDepreciacao = new BuscaInner;
$obBscContaContabilDepreciacao->setRotulo               ( "Conta Contábil de Depreciação Acumulada" );
$obBscContaContabilDepreciacao->setTitle                ( "Informe a conta do plano de contas."     );
$obBscContaContabilDepreciacao->setId                   ( "stDescricaoContaDepreciacao"             );
$obBscContaContabilDepreciacao->obCampoCod->setName     ( "inCodContaDepreciacao"                   );
$obBscContaContabilDepreciacao->obCampoCod->setSize     ( 10  );
$obBscContaContabilDepreciacao->obCampoCod->setAlign    ("left" );
$obBscContaContabilDepreciacao->setValoresBusca	        ( CAM_GF_CONT_POPUPS."planoConta/OCPlanoConta.php?".Sessao::getId(),$obForm->getName(),"contaContabilDepreciacaoAcumulada");
$obBscContaContabilDepreciacao->setFuncaoBusca 		( "abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','inCodContaDepreciacao','stDescricaoContaDepreciacao','contaContabilDepreciacaoAcumulada','".Sessao::getId()."','800','550');" );
$obBscContaContabilDepreciacao->setNull			( true );
$obBscContaContabilDepreciacao->setValue                ( $stNomContaDepreciacao );
$obBscContaContabilDepreciacao->obCampoCod->setValue    ( $inCodPlanoDepreciacao );

if ($rsBem->getCampo('depreciavel') === 't') {
  $obRdDepreciavelSim->setChecked(true);
} else {
  $obRdDepreciavelNao->setChecked(true);
}

$obRdDepreciavelNao->obEvento->setOnChange("montaParametrosGET( 'montaDepreciacao', 'stAcao,boDepreciavel' );");

$obSpnDepreciacao = new Span;
$obSpnDepreciacao->setId('stDepreciacao');

//monta o formulário
$obFormulario = new FormularioAbas;
$obFormulario->setAjuda('UC-03.01.06');
$obFormulario->addAba('Classificação');
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );
$obFormulario->addHidden    ( $obHdnRecMax );
$obFormulario->addHidden    ( $obHdnJson );

$obFormulario->addTitulo    ( 'Classificação' );

if ($stAcao == 'alterar') {
    $obFormulario->addHidden( $obHdnCodBem );
    $obFormulario->addHidden( $obHdnValorBem);
    $obFormulario->addHidden( $obHdnDepreciacaoAcelerada);
    $obFormulario->addHidden( $obHdnValorQuotaDepreciacaoAnual);
    $obFormulario->addHidden( $obHdnValorAtualizadoDepreciacao);
    $obFormulario->addHidden( $obHdnDepreciacaoAcumulada);
    $obFormulario->addHidden( $obHdnPlanoContaAnalitica);
    $obFormulario->addHidden( $obHdnNomePlanoConta);
    $obFormulario->addHidden( $obHdnCodPlanoDepreciacao);
    $obFormulario->addComponente( $obLblCodBem );
}

$obIMontaClassificacao->geraFormulario( $obFormulario );

$obFormulario->addTitulo    ( 'Informações Básicas' );
$obFormulario->addComponente( $obTxtDescricaoBem );
$obFormulario->addComponente( $obPopUpProcesso );
$obFormulario->addComponente( $obTxtDetalhamentoBem );
$obFormulario->addComponente( $obBscMarca );
$obFormulario->addComponente( $obIPopUpCGMFornecedor );
if ($rsReavaliacao->getNumLinhas() > 0 || $rsDepreciacao->getNumLinhas() > 0) {
    $obFormulario->addComponente( $obLblInValorBem );
    $obFormulario->addHidden( $obHdnInValorBem);
} else {
    $obFormulario->addComponente( $obInValorBem );
}
$obFormulario->addComponente( $obDtAquisicao );
$obFormulario->addComponente( $obDtVencimento );

$obFormulario->agrupaComponentes( array( $obRdPlacaIdentificacaoSim, $obRdPlacaIdentificacaoNao ) );
$obFormulario->addSpan      ( $obSpnNumeroPlaca );

$obFormulario->addSpan 		( $obSpnAtributos );

$obFormulario->addTitulo    ( 'Informações Financeiras' );
$obFormulario->addComponente( $obExercicioEmpenho );
$obFormulario->addComponente( $obSlEntidade );
$obFormulario->addComponenteComposto($obTxtOrgao, $obCmbOrgao);
$obFormulario->addComponenteComposto($obTxtUnidade, $obCmbUnidade);
//$obIMontaUnidadeOrcamentaria->geraFormulario( $obFormulario );
$obFormulario->addComponente( $obInVidaUtil);
$obFormulario->addComponente( $obDtIncorporacao );
$obFormulario->addComponente( $obNumEmpenho );
$obFormulario->addComponente( $obNumNotaFiscal );
$obFormulario->addComponente( $obDataNotaFiscal );
$obFormulario->addComponente( $obFileArquivoNF );

if (($stAcao == 'alterar') && ($rsBem->getCampo('caminho_nf') != '')) {
    $obFormulario->addComponente( $obLocalizacao );
}

if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 02) {
    $obFormulario->addComponente( $obCmbTipoDocFiscal );
}

$obFormulario->addTitulo     ( 'Depreciação Inicial / Última Reavaliação' );
$obFormulario->addComponente ( $obInValorDepreciacao );
$obFormulario->addComponente ( $obLblDepreciacaoAcumuladaExercicio );

if ($stAcao == 'alterar') {
    $obFormulario->addComponente ( $obLblDataUltimaReavaliacao);
    $obFormulario->addComponente ( $obLblValorltimaReavaliacao);
}

$obFormulario->addTitulo	( 'Responsável' );
$obFormulario->addComponente( $obIPopUpCGMResponsavel );
$obFormulario->addComponente( $obDtInicioResponsavel );

# Histórico do Bem
$obFormulario->addTitulo  	( 'Histórico' );

if ($inCodOrganogramaAtual != $inCodOrganogramaAtivo) {
    $obFormulario->addComponente( $obLblOrgaoAtual );
    $obFormulario->addComponente( $obLblOrganograma );
}

$obIMontaOrganograma->geraFormulario( $obFormulario );
$obIMontaOrganogramaLocal->geraFormulario( $obFormulario );

$obFormulario->addComponente( $obITextBoxSelectSituacao );
$obFormulario->addComponente( $obTxtDescricaoSituacao );

$obFormulario->addTitulo	( 'Apólice' );
$obFormulario->agrupaComponentes( array( $obRdApoliceSim, $obRdApoliceNao ) );
$obFormulario->addSpan 		( $obSpnApolice );


// Se TCM-GO apresenta campo Obra
$obAdministracaoConfiguracao = new TAdministracaoConfiguracao;
$obAdministracaoConfiguracao->recuperaTodos($rsAdministracaoConfiguracao, " WHERE exercicio = '".Sessao::getExercicio()."' and cod_modulo = 2 and parametro = 'cod_uf'");
$inCodUf = $rsAdministracaoConfiguracao->getCampo('valor');
$stSiglaUf = SistemaLegado::pegaDado("sigla_uf","sw_uf","where cod_uf = ".$inCodUf."");

if ($stSiglaUf == "GO") {
    $stJs.= " jq('#inCodNatureza').change(function(){
        if(jq(this).val() == '2') {
            montaParametrosGET( 'montaObra', 'inCodNatureza' );
        } else {
            jq('#spnListaObra').html('');
        }
    });";
    
    $obSpnListaObra = new Span;
    $obSpnListaObra->setId('spnListaObra');
    $obFormulario->addSpan( $obSpnListaObra );
    
    if($stAcao = 'alterar') {
        $stJs.= "jq(document).ready(function(){ montaParametrosGET('montaObra', 'inCodNatureza,inCodBem'); });";
    }
}


//se a acao for "lote", demonstra os campos no formulário
if ($stAcao == 'lote') {
    $obFormulario->addTitulo( 'Lote' );
    $obFormulario->addComponente( $obInQtdeLote );
} else {
    $obHdnQtdLote = new Hidden;
    $obHdnQtdLote->setName ( "inQtdeLote" );
    $obHdnQtdLote->setValue( 1 );
    $obFormulario->addHidden( $obHdnQtdLote );
}

$obFormulario->addAba('Reavaliação');
$obFormulario->addTitulo('Reavaliação');

if ($stAcao == 'alterar') {
    $obFormulario->addComponente( $obLblCodBemReavaliacao );
    $obFormulario->addHidden($obHdnChaveProcesso);
}

$obFormulario->addComponente($obDtReavalicao);
$obFormulario->addComponente($obInVidaUtilReavalicao);
$obFormulario->addComponente($obFlValorBemReavaliacao);
$obFormulario->addComponente($obTxtMotivo);
$obFormulario->defineBarraAba(array($obBtnIncluirReavaliacao,$obBtnLimparReavaliacao), 'left','');
$obFormulario->addSpan($obSpnListaReavaliacao);

$obFormulario->addAba('Depreciação');
$obFormulario->addTitulo('Depreciação');

$obFormulario->addComponente( $obLblCodBemDepreciacao );

if ($stAcao == 'alterar') {
    $obFormulario->addComponente( $obLblValorAtualizadoDepreciacao);
    $obFormulario->addComponente( $obLblDepreciacaoAcumulada);
}

$obFormulario->addComponente( $obBscContaContabilDepreciacao);
$obFormulario->agrupaComponentes(array($obRdDepreciavelSim,$obRdDepreciavelNao));
$obFormulario->addSpan($obSpnDepreciacao);

if ($stAcao == 'alterar') {
    $obFormulario->Cancelar( $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao );
} else {
    $obFormulario->OK();
}

$obFormulario->show();

if ($stAcao == 'incluir') {
    # monta o text da placa de identificação por padrão
    $jsOnLoad = "montaParametrosGET( 'montaPlacaIdentificacao', 'stPlacaIdentificacao' );";
} else {
    //monta os combos da classificacao
    $jsOnLoad = "ajaxJavaScript('".CAM_GP_PAT_PROCESSAMENTO.'OCIMontaClassificacao.php?'.Sessao::getId()."&stCodClassificacao=".$stClassificacao."','preencheCombos');";

    //monta o span das apolices
    if ( $rsBem->getCampo('cod_apolice') != '' ) {
        $jsOnLoad.= "ajaxJavaScript( '".$pgOcul."?".Sessao::getId()."&stApolice=sim&inCodSeguradora=".$rsBem->getCampo('num_seguradora')."','montaApolice' );"	;
        $jsOnLoad.= "ajaxJavaScript( '".$pgOcul."?".Sessao::getId()."&inCodSeguradora=".$rsBem->getCampo('num_seguradora')."&inCodApolice=".$rsBem->getCampo('cod_apolice')."','preencheApolice' );";
    }
    //monta lista de atributos
    $jsOnLoad.= "ajaxJavaScript( '".$pgOcul."?".Sessao::getId()."&stCodClassificacao=".$stClassificacao."&inCodBem=".$rsBem->getCampo('cod_bem')."','montaAtributos' );"	;

    //monta o select das entidades
    $jsOnLoad.= "ajaxJavaScript( '".$pgOcul."?".Sessao::getId()."&stExercicio=".$rsBem->getCampo('exercicio')."&inCodEntidade=".$rsBem->getCampo('cod_entidade')."','preencheComboEntidade' );"	;

    //monta o text da placa de identificacao com seu valor

    $jsOnLoad.= "ajaxJavaScript( '".$pgOcul."?".Sessao::getId()."&stPlacaIdentificacao=".(( $rsBem->getCampo('identificacao') == 't' OR $rsBem->getCampo('identificacao') == '') ? 'sim' : 'nao')."&stNumPlaca=".$rsBem->getCampo('num_placa')."&recuperaMax=".( ($stAcao == 'lote') ? 'true' : 'false'  )."','montaPlacaIdentificacaoLote' );"	;

    //Monta Lista de Reavaliações
    if ($rsReavaliacao->getCampo('cod_reavaliacao') != '') {
        $jsOnLoad .= "montaParametrosGET( 'montaListaReavaliacoes', 'inCodBem' );";
    }
    $jsOnLoad .= "montaParametrosGET('MontaUnidade');";
    $jsOnLoad .= "montaParametrosGET( 'montaDepreciacao', 'stAcao,boDepreciavel,inVlBem,flDepreciacaoAcelerada,inVlQuotaDepreciacaoAnual,inVlAtualizadoDepreciacao,inVlDepreciacaoAcumulada,inPlanoContaAnalitica,stNomePlanoConta,inCodBem' );montaParametrosGET( 'montaDepreciacaoAcelerada', 'boDepreciacaoAcelerada,flDepreciacaoAcelerada' );";
}


SistemaLegado::executaFrameOculto($stJs);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
