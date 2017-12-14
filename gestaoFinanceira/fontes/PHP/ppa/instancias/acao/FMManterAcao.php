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
 * Página de formulário de inclusão/alteração de Ação
 * Data de Criação: 22/09/2008

 * Copyright CNM - Confederação Nacional de Municípios

 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Pedro Vaz de Mello de Medeiros

 * @package URBEM
 * @ignore

 $Id: FMManterAcao.php 64234 2015-12-21 17:24:45Z michel $

 * Caso de Uso: uc-02.09.04
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_PPA_COMPONENTES.'IPopUpProduto.class.php';
include_once CAM_GF_PPA_COMPONENTES.'IPopUpRegiao.class.php';
include_once CAM_GF_PPA_COMPONENTES.'IPopUpPrograma.class.php';
include_once CAM_GF_PPA_COMPONENTES.'ITextBoxSelectPPA.class.php';
include_once CAM_GA_ADM_COMPONENTES.'ISelectUnidadeMedida.class.php';
include_once CAM_GF_PPA_COMPONENTES.'MontaOrgaoUnidade.class.php';
include_once CAM_GA_NORMAS_COMPONENTES.'IPopUpNorma.class.php';
include_once CAM_GF_PPA_VISAO.'VPPAManterAcao.class.php';
include_once CAM_GF_PPA_NEGOCIO.'RPPAManterAcao.class.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoPAOPPAAcao.class.php';
include_once CAM_GF_PPA_MAPEAMENTO.'TPPATipoAcao.class.php';
include_once CAM_GF_PPA_MAPEAMENTO.'TPPAAcaoPeriodo.class.php';

// Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao', 'incluir');

$arLink = Sessao::read('link');

Sessao::write('ppa', $arLink['inCodPPA']);
Sessao::remove('arUnidade');
Sessao::remove('arParametrosMetas');
Sessao::remove('arParametroPeriodo');

// Define o nome dos arquivos PHP
$stProjeto = 'ManterAcao';
$pgFilt = 'FL'.$stProjeto.'.php';
$pgList = 'LS'.$stProjeto.'.php';
$pgForm = 'FM'.$stProjeto.'.php';
$pgProc = 'PR'.$stProjeto.'.php';
$pgOcul = 'OC'.$stProjeto.'.php';
$pgJS   = 'JS'.$stProjeto.'.php';

include_once $pgJS;

// Definição do form
$obForm = new Form();
$obForm->setAction($pgProc);
$obForm->setTarget('oculto');
$obForm->setEncType('multipart/form-data');

// Definição de dados ocultos padrão
$obHdnAcao = new Hidden();
$obHdnAcao->setName('stAcao');
$obHdnAcao->setID('stAcao');
$obHdnAcao->setValue($stAcao);

$obHdnCtrl = new Hidden();
$obHdnCtrl->setName('stCtrl');
$obHdnCtrl->setValue($stCtrl);

// Linha da lista de Recursos a apagar.
$obHdnInRecurso = new Hidden();
$obHdnInRecurso->setName('inRecurso');
$obHdnInRecurso->setID('inRecurso');

// Linha da lista de Recursos a apagar.
$obHdnBoArrendondar = new Hidden();
$obHdnBoArrendondar->setName('boArredondar');
$obHdnBoArrendondar->setId('boArredondar');

$obRPPAManterAcao = new RPPAManterAcao();
$obVPPAManterAcao = new VPPAManterAcao($obRPPAManterAcao);

$rsAcao = new RecordSet();
$rsAcaoPeriodo = new RecordSet();
$rsPAO = new RecordSet();
$stLstRecursos = '';
if ($stAcao == 'alterar') {
    require_once CAM_GF_LDO_MAPEAMENTO.'TLDOAcaoValidada.class.php';
    $obTLDOAcaoValidada = new TLDOAcaoValidada;

    // Resolve conflito de uso da variável de sessão link entre
    // LSBuscarFuncao.php (de IPopUpFuncao) e LSManterAcao.php.
    Sessao::write('link', null);

    // Array responsavel por guardar as informacoes dos anos que ja forma validados na LDO.
    // Os valores desses anos devem aparecer como label na tabletree
    $arAcaoValidada = array();
    $rsAcao = $obVPPAManterAcao->recuperaAcao($request->getAll());
    if ($rsAcao->getNumLinhas() == 1) {
        $rsRecursos = $obVPPAManterAcao->recuperaRecursos($request->getAll());

        while (!$rsRecursos->eof()) {
            $arRecursos = $rsRecursos->getElementos();
            $stLstRecursos = $obVPPAManterAcao->listaRecursos($arRecursos, false);
            $obVPPAManterAcao->buscarMetasFisicas($arRecursos);

            $stCondicao  = "\n WHERE acao_validada.cod_acao = ".$rsRecursos->getCampo('cod_acao');
            $stCondicao .= "\n   AND acao_validada.timestamp_acao_dados = '".$rsRecursos->getCampo('timestamp_acao_dados')."'";
            $stCondicao .= "\n   AND acao_validada.cod_recurso = ".$rsRecursos->getCampo('cod_recurso');
            $obTLDOAcaoValidada->recuperaTodos($rsAcaoValidada, $stCondicao);

            while (!$rsAcaoValidada->eof()) {
                $arAcaoValidada[str_pad($rsRecursos->getCampo('cod_recurso'), 4, 0, STR_PAD_LEFT)][] = $rsAcaoValidada->getCampo('ano');
                $rsAcaoValidada->proximo();
            }
            $rsRecursos->proximo();
        }

        Sessao::write('arRecursos', $arRecursos);
        Sessao::write('arAcaoValidada', $arAcaoValidada);

        $obTPPAAcaoPeriodo = new TPPAAcaoPeriodo;
        $obTPPAAcaoPeriodo->setDado('cod_acao', $rsAcao->getCampo('cod_acao'));
        $obTPPAAcaoPeriodo->setDado('ultimo_timestamp_acao_dados', $rsAcao->getCampo('ultimo_timestamp_acao_dados'));
        $obTPPAAcaoPeriodo->recuperaPorChave($rsAcaoPeriodo);
    } else {
        $obTOrcamentoPAOPPAAcao = new TOrcamentoPAOPPAAcao;
        $obTOrcamentoPAOPPAAcao->setDado('num_pao', $request->get('inCodAcao'));
        $obTOrcamentoPAOPPAAcao->setDado('exercicio', $request->get('stExercicio'));
        $obTOrcamentoPAOPPAAcao->recuperaDadosPao($rsAcao);
    }
}

// Define código do órgão.
$obHdnOrgao = new Hidden();
$obHdnOrgao->setName('inCodOrgao');
$obHdnOrgao->setValue($request->get('inCodOrgao'));

// Define timestamp para ação_dados.
$obHdnCodDados = new Hidden();
$obHdnCodDados->setName('tsAcaoDados');
$obHdnCodDados->setValue($rsAcao->getCampo('ultimo_timestamp_acao_dados'));

// Define estado de Homologação da ação.
$obHdnHomologado = new Hidden();
$obHdnHomologado->setID('boHomologado');
$obHdnHomologado->setName('boHomologado');
$obHdnHomologado->setValue($request->get('boHomologado'));

// Guarda valores temporariamente
$obHdnCodPPA = new Hidden();
$obHdnCodPPA->setID('inCodPPATmp');

$obHdnPrograma = new Hidden();
$obHdnPrograma->setID('inNumProgramaTmp');

$obHdnDscPrograma = new Hidden();
$obHdnDscPrograma->setID('stDscPrograma');

$obITextBoxSelectPPA = new ITextBoxSelectPPA();
$obITextBoxSelectPPA->setNull(false);

if ($stAcao == 'alterar' AND $rsAcao->getCampo('cod_ppa') != '') {
    $obITextBoxSelectPPA->setLabel(true);
    $obITextBoxSelectPPA->obTextBox->setValue($rsAcao->getCampo('cod_ppa'));
    $obITextBoxSelectPPA->obSelect->setValue($rsAcao->getCampo('cod_ppa'));
} else {
    $obITextBoxSelectPPA->setPreencheUnico(true);
}

$obIPopUpPrograma = new BuscaInner($obForm);
$obIPopUpPrograma->setRotulo('Programa');
$obIPopUpPrograma->setTitle('Informe o programa.');
$obIPopUpPrograma->setId('stNomPrograma');
$obIPopUpPrograma->obCampoCod->setId('inCodPrograma');
$obIPopUpPrograma->obCampoCod->setName('inCodPrograma');
$obIPopUpPrograma->obCampoCod->setSize(10);
$obIPopUpPrograma->obCampoCod->setMaxLength(9);
$obIPopUpPrograma->obCampoCod->setAlign('left');
$obIPopUpPrograma->obCampoCod->setMascara('9999');
$obIPopUpPrograma->obCampoCod->setPreencheComZeros('E');
$stFuncaoBusca = "
if (jq('#inCodPPATxt').val() != '') {
    abrePopUp('".CAM_GF_PPA_POPUPS."programa/FLProcurarPrograma.php','".$obForm->getName()."','".$obIPopUpPrograma->obCampoCod->getName()."','".$obIPopUpPrograma->getId()."','&inCodPPA='+jq('#inCodPPATxt').val()+'&','".Sessao::getId()."','800','550');
} else {
    this.value = '';
    alertaAviso('Para selecionar um programa é necessário selecionar um PPA','form','erro','".Sessao::getId()."');
}
";
$obIPopUpPrograma->setFuncaoBusca($stFuncaoBusca);
$stOnChange = "
if (jq('#inCodPPATxt').val() != '') {
    if (this.value != '') {
        montaParametrosGET('preenchePrograma','inCodPrograma,inCodTipo,inCodPPA');
    } else {
        jq('#stNomPrograma').html('&nbsp;');
    }
} else {
    this.value = '';
    alertaAviso('Para selecionar um programa é necessário selecionar um PPA','form','erro','".Sessao::getId()."');
}
";
$obIPopUpPrograma->obCampoCod->obEvento->setOnChange($stOnChange);
$obIPopUpPrograma->setNull(false);
$obIPopUpPrograma->obCampoCod->setNull(false);
$obIPopUpPrograma->obCampoCod->setValue($rsAcao->getCampo('num_programa'));
$obIPopUpPrograma->setValue($rsAcao->getCampo('nom_programa'));

if ($stAcao == 'incluir') {
    // Define tipo de ação
    $obRadTipoOrcamentaria = new Radio();
    $obRadTipoOrcamentaria->setId('inCodTipoAcao');
    $obRadTipoOrcamentaria->setName('inCodTipoAcao');
    $obRadTipoOrcamentaria->setRotulo('Tipo da Ação');
    $obRadTipoOrcamentaria->setLabel('Orçamentária');
    $obRadTipoOrcamentaria->setValue(1);
    $obRadTipoOrcamentaria->setNull(false);
    $obRadTipoOrcamentaria->setChecked(true);
    $obRadTipoOrcamentaria->obEvento->setOnChange("montaParametrosGET('preencheTipoAcao','inCodPPA,inCodTipoAcao');montaParametrosGET('preencheSpanOrcamentaria','inCodTipoAcao');");

    $obRadTipoNaoOrcamentaria = new Radio();
    $obRadTipoNaoOrcamentaria->setId('inCodTipoAcao');
    $obRadTipoNaoOrcamentaria->setName('inCodTipoAcao');
    $obRadTipoNaoOrcamentaria->setRotulo('Tipo da Ação');
    $obRadTipoNaoOrcamentaria->setLabel('Não Orçamentária');
    $obRadTipoNaoOrcamentaria->setValue(2);
    $obRadTipoNaoOrcamentaria->setNull(false);
    $obRadTipoNaoOrcamentaria->obEvento->setOnChange("montaParametrosGET('preencheTipoAcao','inCodPPA,inCodTipoAcao');montaParametrosGET('preencheSpanOrcamentaria','inCodTipoAcao');");
} else {
    $obSlTipoAcao = new Select;
    $obSlTipoAcao->setRotulo('Tipo da Ação');
    $obSlTipoAcao->setName('inCodTipoAcao');
    $obSlTipoAcao->setId('inCodTipoAcao');
    $obSlTipoAcao->addOption('1','Orçamentária');
    $obSlTipoAcao->addOption('2','Não Orçamentária');
    if ($rsAcao->getCampo('cod_tipo') < 4) {
        $obSlTipoAcao->setValue(1);
    } else {
        $obSlTipoAcao->setValue(2);
    }

    $obSlTipoAcao->setLabel(true);
}

$arRadTipo = array($obRadTipoOrcamentaria, $obRadTipoNaoOrcamentaria);

$obSlSubTipoAcao = new Select;
$obSlSubTipoAcao->setName('inCodTipo');
$obSlSubTipoAcao->setId  ('inCodTipo');
$obSlSubTipoAcao->setRotulo('Subtipo da Ação');
$obSlSubTipoAcao->setTitle('Selecione o subtipo da ação');
$obSlSubTipoAcao->addOption('','Selecione');
if ($stAcao == 'alterar') {
    $obTPPATipoAcao = new TPPATipoAcao;
    if ($rsAcao->getCampo('cod_ppa') == '' AND $rsAcao->getCampo('cod_tipo') == 4) {
        $stFiltro = ' WHERE cod_tipo >= 4';
        $obSlSubTipoAcao->setLabel(false);
    } else {
        $obSlSubTipoAcao->setLabel(true);
        $obSlSubTipoAcao->setValue($rsAcao->getCampo('cod_tipo'));
    }
    $obTPPATipoAcao->recuperaTodos($rsTipoAcao, $stFiltro);
    while (!$rsTipoAcao->eof()) {
        $obSlSubTipoAcao->addOption($rsTipoAcao->getCampo('cod_tipo'),$rsTipoAcao->getCampo('descricao'));
        $rsTipoAcao->proximo();
    }
} else {
    $obSlSubTipoAcao->addOption('1','Projeto');
    $obSlSubTipoAcao->addOption('2','Atividade');
    $obSlSubTipoAcao->addOption('3','Operação Especial');
}
$obSlSubTipoAcao->obEvento->setOnChange("montaParametrosGET('preenchePeriodo');");
$obSlSubTipoAcao->setNull(false);

//So para estados de AL e TO
$inCodUf = SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(),$boTransacao);
//Estado de AL = 2
//Estado de TO = 27

if ( ($inCodUf == 2) || ($inCodUf == 27)) {
    $obSlIdentificadorAcao = new Select;
    $obSlIdentificadorAcao->setName('inCodIdentificadorAcao');
    $obSlIdentificadorAcao->setId  ('inCodIdentificadorAcao');
    $obSlIdentificadorAcao->setRotulo('Identificador');
    $obSlIdentificadorAcao->setTitle('Selecione o Identificador da ação');
    $obSlIdentificadorAcao->setNull(false);
    $obSlIdentificadorAcao->addOption('','Selecione');
    //Estado de AL = 2
    if ( $inCodUf == 2 ) {
        include_once CAM_GF_PPA_MAPEAMENTO.'TTCEALIdentificadorAcao.class.php';
        $obTTCEALIdentificadorAcao = new TTCEALIdentificadorAcao();
        $obTTCEALIdentificadorAcao->recuperaTodos($rsIdentificador,"","",$boTransacao);
        //Carrega dados para a select
        while (!$rsIdentificador->eof()) {
            $obSlIdentificadorAcao->addOption($rsIdentificador->getCampo('cod_identificador'),$rsIdentificador->getCampo('descricao'));
            $rsIdentificador->proximo();
        }
        if ($stAcao == "alterar") {
            include_once CAM_GF_PPA_MAPEAMENTO.'TTCEALAcaoIdentificadorAcao.class.php';
            $obTTCEALAcaoIdentificadorAcao = new TTCEALAcaoIdentificadorAcao();
            $stFiltro = " WHERE acao_identificador_acao.cod_acao = ".$request->get('inCodAcao')."";
            $obTTCEALAcaoIdentificadorAcao->recuperaAcaoIdentificadorAcao($rsIdentificadorAcao, $stFiltro, "",$boTransacao);
            $jsOnload .=" jQuery('#inCodIdentificadorAcao').val('".$rsIdentificadorAcao->getCampo('cod_identificador')."'); ";
        }
        
    }
    //Estado de TO = 27
    if ( $inCodUf == 27 ) {
        include_once CAM_GF_PPA_MAPEAMENTO.'TTCETOIdentificadorAcao.class.php';
        $obTTCETOIdentificadorAcao = new TTCETOIdentificadorAcao();
        $obTTCETOIdentificadorAcao->recuperaTodos($rsIdentificador,"","",$boTransacao);
        //Carrega dados para a select
        while (!$rsIdentificador->eof()) {
            $obSlIdentificadorAcao->addOption($rsIdentificador->getCampo('cod_identificador'),$rsIdentificador->getCampo('cod_identificador').' - '.$rsIdentificador->getCampo('descricao'));
            $rsIdentificador->proximo();
        }
        if ($stAcao == "alterar") {
            include_once CAM_GF_PPA_MAPEAMENTO.'TTCETOAcaoIdentificadorAcao.class.php';
            $obTTCETOAcaoIdentificadorAcao = new TTCETOAcaoIdentificadorAcao();
            $stFiltro = " WHERE acao_identificador_acao.cod_acao = ".$request->get('inCodAcao')."";
            $obTTCETOAcaoIdentificadorAcao->recuperaAcaoIdentificadorAcao($rsIdentificadorAcao, $stFiltro, "",$boTransacao);
            $jsOnload .=" jQuery('#inCodIdentificadorAcao').val('".$rsIdentificadorAcao->getCampo('cod_identificador')."'); ";
        }
    }    
}//fim IF Identificador Acao

$obSpnPeriodo = new Span;
$obSpnPeriodo->setId('spnPeriodo');
if ($rsAcaoPeriodo->getNumLinhas() > 0) {
    $arParametros = array( 'inCodPrograma' => $rsAcao->getCampo('cod_programa'),
                           'inCodTipo' => $rsAcao->getCampo('cod_tipo'),
                           'stDataInicial' => $rsAcaoPeriodo->getCampo('data_inicio'),
                           'stDataFinal' => $rsAcaoPeriodo->getCampo('data_termino'),
                           'flValorEstimado' => $rsAcao->getCampo('valor_estimado'),
                           'flMetaEstimada' => $rsAcao->getCampo('meta_estimada'),
                         );
    Sessao::write('arParametroPeriodo', $arParametros);
    $stHTML = $obVPPAManterAcao->preenchePeriodo($arParametros,false);
    $obSpnPeriodo->setValue($stHTML);
}

// Define número da ação
$obTxtCodAcao = new TextBox();
$obTxtCodAcao->setID('inCodAcao');
$obTxtCodAcao->setName('inCodAcao');
$obTxtCodAcao->setRotulo('Ação');
$obTxtCodAcao->setTitle('Escolha um número para a ação.');
$obTxtCodAcao->setValue($rsAcao->getCampo('num_acao'));
$obTxtCodAcao->setMascara('9999');
$obTxtCodAcao->setPreencheComZeros('E');
if ($stAcao == 'alterar') {
    $obTxtCodAcao->setLabel(true);
    $obTxtCodAcao->setNull(true);
} else {
    $obTxtCodAcao->setSize(8);
    $obTxtCodAcao->setInteiro(true);
    $obTxtCodAcao->setMaxLength(4);
    $obTxtCodAcao->setNull(false);
    $obTxtCodAcao->obEvento->setOnChange("montaParametrosGET('verificarCodAcao', 'inCodAcao,inCodTipo,inCodPPA', true);");
}

// Define descrição
$obTxtTituloAcao = new TextArea;
$obTxtTituloAcao->setName     ('stTitulo');
$obTxtTituloAcao->setId       ('stTitulo');
$obTxtTituloAcao->setValue    ($request->get('stTitulo'));
$obTxtTituloAcao->setRotulo   ('Título da Ação');
$obTxtTituloAcao->setTitle    ('Informe o título da Ação.');
$obTxtTituloAcao->setMaxCaracteres(480);
$obTxtTituloAcao->setNull     (false);
$obTxtTituloAcao->setValue    ($rsAcao->getCampo('titulo'));

$obTxtFinalidade = new TextArea;
$obTxtFinalidade->setName  ('stFinalidade');
$obTxtFinalidade->setId('stFinalidade');
$obTxtFinalidade->setRotulo('Finalidade da Ação');
$obTxtFinalidade->setTitle('Informe a finalidade da ação');
$obTxtFinalidade->setMaxCaracteres(480);
$obTxtFinalidade->setNull(false);
$obTxtFinalidade->setValue($rsAcao->getCampo('finalidade'));

$obTxtDescricao = new TextArea;
$obTxtDescricao->setName  ('stDescricao');
$obTxtDescricao->setId('stDescricao');
$obTxtDescricao->setRotulo('Descrição da Ação');
$obTxtDescricao->setTitle('Informe a descrição da ação');
$obTxtDescricao->setMaxCaracteres(480);
$obTxtDescricao->setNull(false);
$obTxtDescricao->setValue($rsAcao->getCampo('descricao'));

$obTxtDetalhamento = new TextArea;
$obTxtDetalhamento->setName  ('stDetalhamento');
$obTxtDetalhamento->setId    ('stDetalhamento');
$obTxtDetalhamento->setRotulo('Detalhamento da Implementação');
$obTxtDetalhamento->setTitle('Informe o detalhamento da implementação.');
$obTxtDetalhamento->setMaxCaracteres(480);
$obTxtDetalhamento->setNull(false);
$obTxtDetalhamento->setValue($rsAcao->getCampo('detalhamento'));

$obSlFormaImplementacao = new Select;
$obSlFormaImplementacao->setName    ('slFormaImplementacao');
$obSlFormaImplementacao->setId      ('slFormaImplementacao');
$obSlFormaImplementacao->setRotulo  ('Forma de Implementação');
$obSlFormaImplementacao->setTitle   ('Selecione a forma de implementação.');
$obSlFormaImplementacao->addOption  ('','Selecione');
$obSlFormaImplementacao->addOption  ('1','Direta');
$obSlFormaImplementacao->addOption  ('2','Descentralizada');
$obSlFormaImplementacao->addOption  ('3','Transferência Obrigatória');
$obSlFormaImplementacao->addOption  ('4','Transferência Voluntária');
$obSlFormaImplementacao->addOption  ('5','Transferência Outras');
$obSlFormaImplementacao->addOption  ('6','Linha de Crédito');
$obSlFormaImplementacao->setNull    (false);
$obSlFormaImplementacao->setValue   ($rsAcao->getCampo('cod_forma'));

// Define popup de região de abrangência
$obIPopUpRegiao = new IPopUpRegiao($obForm);
$obIPopUpRegiao->setNull(false);
$obIPopUpRegiao->obCampoCod->setValue($rsAcao->getCampo('cod_regiao'));
$obIPopUpRegiao->setValue($rsAcao->getCampo('nom_regiao'));

$obSpnDescricaoRegiao = new Span;
$obSpnDescricaoRegiao->setId('spnDescricaoRegiao');

// Define popup de produto
$obIPopUpProduto = new IPopUpProduto($obForm);
$obIPopUpProduto->setNull(true);
$obIPopUpProduto->obCampoCod->setValue($request->get('inCodProduto'));
$obIPopUpProduto->setValue($request->get('stDscProduto'));
$obIPopUpProduto->obCampoCod->obEvento->setOnBlur("montaParametrosGET('preencheProduto','inCodProduto');");
$obIPopUpProduto->obCampoCod->setValue($rsAcao->getCampo('cod_produto'));
$obIPopUpProduto->setValue($rsAcao->getCampo('nom_produto'));
$obIPopUpProduto->setNull( false );

//Define um span para a especificacao do produto
$obSpnProduto = new Span;
$obSpnProduto->setId('spnProduto');

$obSlTipoOrcamento = new Select;
$obSlTipoOrcamento->setName     ('slTipoOrcamento');
$obSlTipoOrcamento->setId       ('slTipoOrcamento');
$obSlTipoOrcamento->setRotulo   ('Tipo de Orçamento');
$obSlTipoOrcamento->setTitle    ('Selecione o tipo de orçamento');
$obSlTipoOrcamento->addOption   ('','Selecione');
$obSlTipoOrcamento->addOption   ('1','Fiscal');
$obSlTipoOrcamento->addOption   ('2','Seguridade');
$obSlTipoOrcamento->addOption   ('3','Investimento das Estatais');
$obSlTipoOrcamento->addOption   ('4','Não Orçamentário');
$obSlTipoOrcamento->setNull     (false);
$obSlTipoOrcamento->setValue    ($rsAcao->getCampo('cod_tipo_orcamento'));

$obIPopUpNorma = new IPopUpNorma();
$obIPopUpNorma->obInnerNorma->obCampoCod->setId('inCodNorma');
$obIPopUpNorma->obInnerNorma->setRotulo("Número da Norma");
$obIPopUpNorma->setExibeDataNorma(false);
$obIPopUpNorma->setExibeDataPublicacao(false);
$obIPopUpNorma->obInnerNorma->setNull(true);
$obIPopUpNorma->obInnerNorma->obCampoCod->setValue($rsAcao->getCampo('cod_norma'));
$obIPopUpNorma->obInnerNorma->setValue($rsAcao->getCampo('nom_norma'));

// Define unidade de medida.
$obISelUnidade = new ISelectUnidadeMedida();
$obISelUnidade->setName('stUnidadeMedida');
$obISelUnidade->setId('stUnidadeMedida');
$obISelUnidade->setRotulo('Unidade de Medida (U.M.)');
$obISelUnidade->setNull(false);
$obISelUnidade->setValue($rsAcao->getCampo('cod_unidade_medida').'-'.$rsAcao->getCampo('cod_grandeza'));

// Define unidade orçamentária responsável
$obIMontaUnidadeOrcamentaria = new MontaOrgaoUnidade();
$obIMontaUnidadeOrcamentaria->setRotulo('Unidade Executora');
if ($rsAcao->getCampo('cod_ppa') != '') {
    $obIMontaUnidadeOrcamentaria->setValue($rsAcao->getCampo('num_orgao') . '.' . $rsAcao->getCampo('num_unidade'));
    $obIMontaUnidadeOrcamentaria->setCodOrgao((int) $rsAcao->getCampo('num_orgao'));
    $obIMontaUnidadeOrcamentaria->setCodUnidade((int) $rsAcao->getCampo('num_unidade'));
}

$obIMontaUnidadeOrcamentaria->setActionPosterior($pgProc);
$obIMontaUnidadeOrcamentaria->setNull(false);

$obHdnInCodAcao = new Hidden();
$obHdnInCodAcao->setID('hdnInCodAcao');
$obHdnInCodAcao->setValue($rsAcao->getCampo('cod_acao'));
$obHdnInCodAcao->setName('hdnInCodAcao');

$obHdnInNumAcao = new Hidden();
$obHdnInNumAcao->setID('hdnInNumAcao');
$obHdnInNumAcao->setValue($rsAcao->getCampo('num_acao'));
$obHdnInNumAcao->setName('hdnInNumAcao');

$obHdnInCodPrograma = new Hidden();
$obHdnInCodPrograma->setID('hdnInCodPrograma');
$obHdnInCodPrograma->setValue($rsAcao->getCampo('cod_programa'));
$obHdnInCodPrograma->setName('hdnInCodPrograma');

$obHdnRegistro = new Hidden();
$obHdnRegistro->setID('inRegistro');
$obHdnRegistro->setValue($request->get('inRegistro'));
$obHdnRegistro->setName('inRegistro');

$obSpnOrcamentaria = new Span;
$obSpnOrcamentaria->setId('spnOrcamentaria');
if ($rsAcao->getCampo('cod_tipo') == '' OR $rsAcao->getCampo('cod_tipo') < 4) {
    $obSpnOrcamentaria->setValue($obVPPAManterAcao->preencheSpanOrcamentaria(array('inCodTipoAcao'  => 1,
                                                                                   'inCodNatureza'  => $rsAcao->getCampo('cod_natureza'),
                                                                                   'inCodFuncao'    => $rsAcao->getCampo('cod_funcao'),
                                                                                   'inCodSubFuncao' => $rsAcao->getCampo('cod_subfuncao'),
                                                                                  ),false));
} else {
    $obSpnOrcamentaria->setValue($obVPPAManterAcao->preencheSpanOrcamentaria(array('inCodTipoAcao'  => 2,
                                                                                   'inCodNatureza'  => $rsAcao->getCampo('cod_natureza'),
                                                                                   'inCodFuncao'    => $rsAcao->getCampo('cod_funcao'),
                                                                                   'inCodSubFuncao' => $rsAcao->getCampo('cod_subfuncao'),
                                                                                  ),false));
}

// Define Span do popup do recurso.
$obSpnRecurso = new Span();
$obSpnRecurso->setID('spnRecurso');

// Reserva espaço para lista de recursos.
$obSpanListaRecurso = new Span();
$obSpanListaRecurso->setID('spnListaRecurso');
$obSpanListaRecurso->setValue($stLstRecursos);

// Define botoes de ação.
$obBtnOK = new Ok(true);
$obBtnOK->obEvento->setOnClick('validarAcao();');

if ($stAcao == 'incluir') {
    $obBtnLimpar = new Button;
    $obBtnLimpar->setValue('Limpar');
    $obBtnLimpar->obEvento->setOnClick('Limpar(true);');
} else {
    $obBtnLimpar = new Cancelar();
    $obBtnLimpar->obEvento->setOnClick('cancelarAcao();');
}

$arBotoes = array($obBtnOK, $obBtnLimpar);

// Definição do Formulário
$obFormulario = new Formulario();
$obFormulario->addForm($obForm);

$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnInCodAcao);
$obFormulario->addHidden($obHdnInNumAcao);
$obFormulario->addHidden($obHdnInCodPrograma);
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obHdnInRecurso);
$obFormulario->addHidden($obHdnOrgao);
$obFormulario->addHidden($obHdnCodDados);
$obFormulario->addHidden($obHdnHomologado);
$obFormulario->addHidden($obHdnCodPPA);
$obFormulario->addHidden($obHdnPrograma);
$obFormulario->addHidden($obHdnDscPrograma);
$obFormulario->addHidden($obHdnBoArrendondar);

$obFormulario->addTitulo('Dados para as Ações do PPA');
$obITextBoxSelectPPA->geraFormulario($obFormulario);

$obFormulario->addComponente($obIPopUpPrograma);

if ($stAcao == 'incluir') {
    $obFormulario->agrupaComponentes($arRadTipo);
} else {
    $obFormulario->addComponente($obSlTipoAcao);
}
$obFormulario->addComponente                ($obSlSubTipoAcao);
if ( ($inCodUf == 2) || ($inCodUf == 27) )
    $obFormulario->addComponente            ($obSlIdentificadorAcao);
$obFormulario->addSpan                      ($obSpnPeriodo);
$obFormulario->addComponente                ($obTxtCodAcao);
$obFormulario->addComponente                ($obTxtTituloAcao);
$obFormulario->addComponente                ($obTxtFinalidade);
$obFormulario->addComponente                ($obTxtDescricao);
$obFormulario->addComponente                ($obTxtDetalhamento);
$obFormulario->addComponente                ($obSlFormaImplementacao);
$obFormulario->addComponente                ($obIPopUpRegiao);
$obFormulario->addSpan                      ($obSpnDescricaoRegiao);
$obFormulario->addComponente                ($obIPopUpProduto);
$obFormulario->addSpan                      ($obSpnProduto);
$obIPopUpNorma->geraFormulario              ($obFormulario);
$obFormulario->addComponente                ($obSlTipoOrcamento);
$obIMontaUnidadeOrcamentaria->geraFormulario($obFormulario);
$obFormulario->addSpan                      ($obSpnOrcamentaria);
$obFormulario->addHidden                    ($obHdnRegistro);
$obFormulario->addSpan                      ($obSpnRecurso);
$obFormulario->addSpan                      ($obSpanListaRecurso);
$obFormulario->addComponente                ($obISelUnidade);
$obFormulario->defineBarra                  ($arBotoes);
$obFormulario->show();

if ($stAcao == 'alterar') {
    $jsOnload .= "\n formatListaRecurso();";
    $jsOnload .= "\n montaParametrosGET('preencheProduto','inCodProduto');";
    if ($rsAcao->getCampo('cod_tipo_programa') == 4) {
        $stLblUnidade = 'Unidade de Medida (U.M.)';
        $stLblProduto = 'Produto';
    } else {
        $stLblUnidade = '*Unidade de Medida (U.M.)';
        $stLblProduto = '*Produto';
    }
    $jsOnload .= "\n var obHtml = jq('#stUnidadeMedida').parent().parent(); ";
    $jsOnload .= "\n jq('td.label',obHtml).html('".$stLblUnidade."'); ";
    $jsOnload .= "\n var obHtml = jq('#inCodProduto').parent().parent().parent().parent().parent().parent();";
    $jsOnload .= "\n jq('td.label',obHtml).html('".$stLblProduto."');";
    $jsOnload .= "\n formatAnosAcaoValidada('".json_encode($arAcaoValidada)."');";
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
