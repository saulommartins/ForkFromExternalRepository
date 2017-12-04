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
    * Novo formulário para inclusão de despesa, agora utilizando ação
    * Data de Criação   : 12/08/2009

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TPB_MAPEAMENTO."TTCEPBModalidadeDespesa.class.php";
include_once CAM_GPC_TPB_MAPEAMENTO."TTCEPBOrcamentoModalidadeDespesa.class.php";
include_once CAM_GPC_TCEPE_MAPEAMENTO."TTCEPEModalidadeDespesa.class.php";
include_once CAM_GPC_TCEPE_MAPEAMENTO."TTCEPEOrcamentoModalidadeDespesa.class.php";
include_once CAM_GF_ORC_NEGOCIO.'ROrcamentoDespesa.class.php';
include_once CAM_GF_ORC_COMPONENTES.'MontaDotacaoOrcamentaria.class.php';
include_once CAM_GF_ORC_NEGOCIO.'ROrcamentoConfiguracao.class.php';
include_once CAM_GF_PPA_NEGOCIO.'RPPAManterAcao.class.php';
include_once CAM_GF_PPA_VISAO.'VPPAManterAcao.class.php';
include_once CAM_GF_ORC_COMPONENTES.'IMontaRecursoDestinacao.class.php';
include_once CAM_GF_ORC_COMPONENTES.'ITextBoxSelectEntidadeUsuario.class.php';

//Define o nome dos arquivos PHP
$stPrograma = 'DespesaAcao';
$pgFilt = 'FL'.$stPrograma.'.php';
$pgList = 'LS'.$stPrograma.'.php';
$pgForm = 'FM'.$stPrograma.'.php';
$pgProc = 'PR'.$stPrograma.'.php';
$pgOcul = 'OC'.$stPrograma.'.php';
$pgJs   = 'JS'.$stPrograma.'.js';

$stAcao = $request->get('stAcao');
$stDespesa = isset($stDespesa) ? $stDespesa : null;
$vlPeriodo = isset($vlPeriodo) ? $vlPeriodo : 0;
$js = isset($js) ? $js : null;
if ($stAcao == 'alterar') {
    $pgList = 'LSDespesa.php';
}

include_once $pgJs;

$obROrcamentoDespesa        = new ROrcamentoDespesa;
$obMontaDotacaoOrcamentaria = new MontaDotacaoOrcamentaria;
$obRConfiguracaoOrcamento   = new ROrcamentoConfiguracao;

if (Sessao::getExercicio() < '2014') {
    $inUnidadesMedidasMetas = $obRConfiguracaoOrcamento->consultarConfiguracaoEspecifica('unidade_medida_metas');
} else {
    $inUnidadesMedidasMetas = $obRConfiguracaoOrcamento->consultarConfiguracaoEspecifica('unidade_medida_metas_despesa');
}

if ($inUnidadesMedidasMetas > 0) {
    $inNumeroColunas = (12/$inUnidadesMedidasMetas);
}

$obRPPAManterAcao = new RPPAManterAcao();
$obVPPAManterAcao = new VPPAManterAcao($obRPPAManterAcao);

$arParametros['inCodAcao'] = $_REQUEST['inCodAcao'];
$arParametros['inAno']     = $_REQUEST['inAno'];

$rsAcao = $obVPPAManterAcao->recuperaAcaoDespesa($arParametros);
$arDespesa  = $rsAcao->getElementos();
$stDespesa .= $rsAcao->getCampo('num_orgao')    .'.';
$stDespesa .= $rsAcao->getCampo('num_unidade')  .'.';
$stDespesa .= $rsAcao->getCampo('cod_funcao')   .'.';
$stDespesa .= $rsAcao->getCampo('cod_subfuncao').'.';
$stDespesa .= $rsAcao->getCampo('num_programa') .'.';
$stDespesa .= $rsAcao->getCampo('num_acao');
if ($request->get('stMascClassDespesa')) {
    $stDespesa .= '.'.str_replace('.', '', $_REQUEST['stMascClassDespesa']);
}

$inCodEntidade      = $request->get('inCodEntidade');
$inCodRecurso       = $_REQUEST['inCodRecurso'];
$stDescricaoRecurso = $request->get('stDescricaoRecurso');
$nuValorOriginal    = '';

$arMascDotacao = Mascara::validaMascaraDinamica($obMontaDotacaoOrcamentaria->getMascara(), $stDespesa);
$obMontaDotacaoOrcamentaria->setValue($arMascDotacao[1]);

if ($stAcao == 'alterar') {
    $obROrcamentoDespesa->setCodDespesa($request->get('inCodDespesa'));
    $obErro = $obROrcamentoDespesa->consultar($rsDespesa);

    if (!$obErro->ocorreu()) {
        $arDespesa = $rsDespesa->getElementos();
        $arDespesa[0]['num_programa'] = $rsAcao->getCampo('num_programa');
        $arDespesa[0]['num_acao'] = $rsAcao->getCampo('num_acao');
        $obROrcamentoDespesa->obROrcamentoRecurso->setCodRecurso($inCodRecurso);
        $obErro = $obROrcamentoDespesa->obROrcamentoRecurso->consultar($rsRecurso);

        if (!$obErro->ocorreu()) {
            $nuValorOriginal = $rsDespesa->getCampo('vl_original');
            $nuValorOriginal = number_format($nuValorOriginal, 2, ',', '.');

            /*
             * CONSULTAR METAS
             */
            include_once CAM_GF_ORC_NEGOCIO.'ROrcamentoPrevisaoDespesa.class.php';

            $obRPrevisaoDespesa = new ROrcamentoPrevisaoDespesa;

            $obRPrevisaoDespesa->obROrcamentoDespesa->obROrcamentoEntidade->setCodigoEntidade($inCodEntidade);
            $obRPrevisaoDespesa->listarPeriodo($rsListaDespesa2, ' cod_despesa = '. $obROrcamentoDespesa->getCodDespesa() . ' ');
            $rsListaDespesa2->addFormatacao('vl_previsto', 'NUMERIC_BR');
            $arVlPeriodo = array();
            while (!$rsListaDespesa2->eof()) {
               $arVlPeriodo[$rsListaDespesa2->getCampo('periodo')] = $rsListaDespesa2->getCampo('vl_previsto');
               $rsListaDespesa2->proximo();
            }
            Sessao::write('arVlPeriodo',$arVlPeriodo);
        }
    }
}

$arParametros['tsAcaoDados'] = $rsAcao->getCampo('ultimo_timestamp_acao_dados');
$rsRecurso = $obVPPAManterAcao->recuperaDadosRecursosDespesa($arParametros);

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
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

$obHdnCodAcao = new Hidden;
$obHdnCodAcao->setName ('inCodAcao');
$obHdnCodAcao->setValue($arParametros['inCodAcao']);

$obHdnTsAcaoDados = new Hidden;
$obHdnTsAcaoDados->setName ('tsAcaoDados');
$obHdnTsAcaoDados->setValue($arParametros['tsAcaoDados']);

$obHdnInAno = new Hidden;
$obHdnInAno->setName ('inAno');
$obHdnInAno->setValue($arParametros['inAno']);

$obHdnCodDespesa = new Hidden;
$obHdnCodDespesa->setName ('inCodFixacaoDespesa');
$obHdnCodDespesa->setValue($request->get('inCodDespesa'));

$obROrcamentoDespesa->obROrcamentoEntidade->obRCGM->setNumCGM(Sessao::read('numCgm'));
$obROrcamentoDespesa->obROrcamentoEntidade->listarUsuariosEntidade($rsEntidade, ' ORDER BY cod_entidade');

$obCmbEntidade = new ITextBoxSelectEntidadeUsuario;
$obCmbEntidade->setNull(false);
if ($inCodEntidade) {
    $obCmbEntidade->setCodEntidade($inCodEntidade);
}

$obMontaDotacaoOrcamentaria->setName           ('stDotacaoOrcamentaria');
$obMontaDotacaoOrcamentaria->setRotulo         ('Dotação Orcamentaria');
$obMontaDotacaoOrcamentaria->setActionAnterior ($pgOcul);
$obMontaDotacaoOrcamentaria->setActionPosterior($pgProc);
$obMontaDotacaoOrcamentaria->setTarget         ('oculto');
$obMontaDotacaoOrcamentaria->setNull           (false);

$stMascaraRubrica    = $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->recuperaMascara();
$obBscRubricaDespesa = new BuscaInner;
$obBscRubricaDespesa->setRotulo                        ('Rubrica de Despesa');
$obBscRubricaDespesa->setTitle                         ('Informe a rubrica de despesa.');
$obBscRubricaDespesa->setNull                          (false);
$obBscRubricaDespesa->setId                            ('stDescricaoDespesa');
$obBscRubricaDespesa->setValue                         ($request->get('stDescricao'));
$obBscRubricaDespesa->obCampoCod->setValue             ($request->get('stMascClassDespesa'));
$obBscRubricaDespesa->obCampoCod->setName              ('inCodDespesa');
$obBscRubricaDespesa->obCampoCod->setId                ('inCodDespesa');
$obBscRubricaDespesa->obCampoCod->setSize              (strlen($stMascaraRubrica));
$obBscRubricaDespesa->obCampoCod->setMaxLength         (strlen($stMascaraRubrica));
$obBscRubricaDespesa->obCampoCod->setAlign             ('left');
$obBscRubricaDespesa->obCampoCod->obEvento->setOnFocus ('selecionaValorCampo(this);');
$obBscRubricaDespesa->obCampoCod->obEvento->setOnChange("preencheComZeros('".$stMascaraRubrica."', this, 'D');");
$obBscRubricaDespesa->obCampoCod->obEvento->setOnKeyUp ("mascaraDinamico('".$stMascaraRubrica."', this, event);");
$obBscRubricaDespesa->setFuncaoBusca                   ("abrePopUp('".CAM_GF_ORC_POPUPS."classificacaodespesa/FLClassificacaoDespesa.php','frm','inCodDespesa','stDescricaoDespesa','&mascClassificacao=".$stMascaraRubrica."','".Sessao::getId()."','800','550');");

$inCodUF = SistemaLegado::pegaConfiguracao('cod_uf');

//Somente Paraiba ou Pernambuco
if($inCodUF == '15' || $inCodUF == '16') {
    
    if ($stAcao == 'alterar') {
        
        $rsOrcamentoModalidadeDespesa = new RecordSet;
        
        if($inCodUF == '15'){
            $obTTCEPBOrcamentoModalidadeDespesa = new TTCEPBOrcamentoModalidadeDespesa;
            $obTTCEPBOrcamentoModalidadeDespesa->setDado('exercicio', Sessao::getExercicio());
            $obTTCEPBOrcamentoModalidadeDespesa->setDado('cod_despesa', $request->get('inCodDespesa'));
            $obTTCEPBOrcamentoModalidadeDespesa->recuperaPorChave($rsOrcamentoModalidadeDespesa);        
        }elseif($inCodUF == '16'){
            $obTTCEPEOrcamentoModalidadeDespesa = new TTCEPEOrcamentoModalidadeDespesa;
            $obTTCEPEOrcamentoModalidadeDespesa->setDado('exercicio', Sessao::getExercicio());
            $obTTCEPEOrcamentoModalidadeDespesa->setDado('cod_despesa', $request->get('inCodDespesa'));
            $obTTCEPEOrcamentoModalidadeDespesa->recuperaPorChave($rsOrcamentoModalidadeDespesa);
        }
    }
    
    $rsModalidadeDespesa  = new RecordSet;
    
    if ($inCodUF == '15'){
        $obTTCEPBModalidadeDespesa = new TTCEPBModalidadeDespesa;
        $obTTCEPBModalidadeDespesa->recuperaTodos($rsModalidadeDespesa);
    } elseif ($inCodUF == '16'){
        $obTTCEPEModalidadeDespesa = new TTCEPEModalidadeDespesa;
        $obTTCEPEModalidadeDespesa->recuperaTodos($rsModalidadeDespesa);
    }
    
    // Define Objeto Select para Nome da Ação
    $obCmbModalidadeDespesa = new Select;
    $obCmbModalidadeDespesa->setName       ( "inCodModalidadeDespesa" );
    $obCmbModalidadeDespesa->setId         ( "inCodModalidadeDespesa" );
    if ($stAcao == 'alterar') {
        $obCmbModalidadeDespesa->setValue( $rsOrcamentoModalidadeDespesa->getCampo('cod_modalidade') );
    }
    $obCmbModalidadeDespesa->setRotulo     ( "Modalidade de Despesa"           );
    $obCmbModalidadeDespesa->setTitle      ( "Selecione a modalidade"          );
    $obCmbModalidadeDespesa->setCampoID    ( 'cod_modalidade'                  );
    $obCmbModalidadeDespesa->setCampoDesc  ( "[cod_modalidade] - [modalidade]" );
    $obCmbModalidadeDespesa->addOption     ( '', 'Selecione'                   );
    $obCmbModalidadeDespesa->setStyle      ( "width: 500px;"                   );
    $obCmbModalidadeDespesa->preencheCombo ( $rsModalidadeDespesa              );
    $obCmbModalidadeDespesa->setNull(false);
}

if ($obRConfiguracaoOrcamento->consultarConfiguracaoEspecifica('forma_execucao_orcamento') == '1') {
    $obBscRubricaDespesa->obCampoCod->obEvento->setOnBlur("montaParametrosGET('mascaraClassificacao');");
} else {
    $obBscRubricaDespesa->setValoresBusca(CAM_GF_ORC_POPUPS.'classificacaodespesa/OCClassificacaoDespesa.php?'.Sessao::getId(), $obForm->getName(), '');
}

if ($stAcao == 'alterar') {
    include_once CAM_GF_EMP_NEGOCIO.'REmpenhoPreEmpenho.class.php';
    $obREmpenhoPreEmpenho = new REmpenhoPreEmpenho;

    $obREmpenhoPreEmpenho->setExercicio  (Sessao::getExercicio());
    $obREmpenhoPreEmpenho->setCodEntidade($inCodEntidade);
    $obREmpenhoPreEmpenho->obROrcamentoDespesa->setCodDespesa($_REQUEST['inCodDespesa']);
    $obREmpenhoPreEmpenho->consultarExistenciaDespesa();

    if ($obREmpenhoPreEmpenho->getCountDespesaExercicio() != 0) {
        $obBscRubricaDespesa->setLabel(true);
    }

}

$obHdnMascClassificacao = new Hidden;
$obHdnMascClassificacao->setName ('stMascClassificacao');
$obHdnMascClassificacao->setValue($stMascaraRubrica);

$rsRecurso->addFormatacao('vl_estimado', 'NUMERIC_BR');
$rsRecurso->addFormatacao('vl_despesa' , 'NUMERIC_BR');
$rsRecurso->addFormatacao('vl_total'   , 'NUMERIC_BR');

$obListaRecurso = new Lista();
$obListaRecurso->setRecordSet      ($rsRecurso);
$obListaRecurso->setNumeracao      (false);
$obListaRecurso->setMostraPaginacao(false);
$obListaRecurso->setTitulo         ('Recursos estimados na LDO');

$obListaRecurso->addCabecalho();
$obListaRecurso->ultimoCabecalho->addConteudo('Recurso');
$obListaRecurso->ultimoCabecalho->setWidth   (55);
$obListaRecurso->commitCabecalho();

$obListaRecurso->addCabecalho();
$obListaRecurso->ultimoCabecalho->addConteudo('Estimado');
$obListaRecurso->ultimoCabecalho->setWidth   (15);
$obListaRecurso->commitCabecalho();

$obListaRecurso->addCabecalho();
$obListaRecurso->ultimoCabecalho->addConteudo('Utilizado LOA');
$obListaRecurso->ultimoCabecalho->setWidth   (15);
$obListaRecurso->commitCabecalho();

$obListaRecurso->addCabecalho();
$obListaRecurso->ultimoCabecalho->addConteudo('Saldo');
$obListaRecurso->ultimoCabecalho->setWidth   (15);
$obListaRecurso->commitCabecalho();

$obListaRecurso->addDado();
$obListaRecurso->ultimoDado->setAlinhamento('ESQUERDA');
$obListaRecurso->ultimoDado->setCampo      ('[cod_recurso] - [nom_recurso]');
$obListaRecurso->commitDado();

$obListaRecurso->addDado();
$obListaRecurso->ultimoDado->setAlinhamento('DIREITA');
$obListaRecurso->ultimoDado->setCampo      ('vl_estimado');
$obListaRecurso->commitDado();

$obListaRecurso->addDado();
$obListaRecurso->ultimoDado->setAlinhamento('DIREITA');
$obListaRecurso->ultimoDado->setCampo      ('vl_despesa');
$obListaRecurso->commitDado();

$obListaRecurso->addDado();
$obListaRecurso->ultimoDado->setAlinhamento('DIREITA');
$obListaRecurso->ultimoDado->setCampo      ('vl_total');
$obListaRecurso->commitDado();

$obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
$obIMontaRecursoDestinacao->setCodRecurso      ($inCodRecurso);
$obIMontaRecursoDestinacao->setDescricaoRecurso($stDescricaoRecurso);
$obIMontaRecursoDestinacao->setNull            (false);

$obTxtValorDotacao = new Numerico;
$obTxtValorDotacao->setRotulo            ('Valor da Dotação Orçamentária');
$obTxtValorDotacao->setTitle             ('Informe o valor da dotação orçamentária.');
$obTxtValorDotacao->setName              ('nuValorOriginal');
$obTxtValorDotacao->setId                ('nuValorOriginal');
$obTxtValorDotacao->setValue             ($nuValorOriginal);
$obTxtValorDotacao->setSize              (20);
$obTxtValorDotacao->setMaxLength         (21);
$obTxtValorDotacao->setNull              (false);
$obTxtValorDotacao->setNegativo          (false);
$obTxtValorDotacao->obEvento->setOnChange(" montaParametrosGET('mudaValor','', 'sincrono');");
$obTxtValorDotacao->obEvento->setOnBlur  (" document.getElementById('Ok').focus();");

//***************************************
// Preenche combos e campos Inner
//***************************************

$js .= "buscaValor('preencheInner', '".$pgOcul."', '".$pgProc."', 'oculto', '".Sessao::getId()."');";
SistemaLegado::executaFramePrincipal($js);

/**
 * METAS
 */
$obHdnNumCampos = new Hidden;
$obHdnNumCampos->setName ('inNumCampos');
$obHdnNumCampos->setId   ('inNumCampos');
$obHdnNumCampos->setValue($inNumeroColunas);

$arTxtPorcento = array();
$arTxtValor    = array();

// Retorna a data conforme o numero de dias corridos a somar desde o inicio do ano.
function somaDiasUteis($inDiasSomar)
{
    $stData = '31/12/'.(Sessao::getExercicio()-1);
    $partes = explode('/', $stData);

    return date('d/m/Y', mktime(0,0,0,$partes[1] ,$partes[0] + $inDiasSomar ,$partes[2]));
}

for ($inCountComponente = 1; $inCountComponente <= $inNumeroColunas; $inCountComponente++) {
    if ($stAcao == 'alterar') {
        switch ($inUnidadesMedidasMetas) {
            case 1: $boReadOnly = SistemaLegado::comparaDatas(somaDiasUteis(bcmul($inCountComponente,30 ,2)), date('d/m/Y')) ? false : true; break;
            case 2: $boReadOnly = SistemaLegado::comparaDatas(somaDiasUteis(bcmul($inCountComponente,60 ,2)), date('d/m/Y')) ? false : true; break;
            case 3: $boReadOnly = SistemaLegado::comparaDatas(somaDiasUteis(bcmul($inCountComponente,90 ,2)), date('d/m/Y')) ? false : true; break;
            case 4: $boReadOnly = SistemaLegado::comparaDatas(somaDiasUteis(bcmul($inCountComponente,120,2)), date('d/m/Y')) ? false : true; break;
            case 6: $boReadOnly = SistemaLegado::comparaDatas(somaDiasUteis(bcmul($inCountComponente,180,2)), date('d/m/Y')) ? false : true; break;
        }
    } else {
        $boReadOnly = false;
    }

    $obHdnMesBloqueado = new Hidden();
    $obHdnMesBloqueado->setName ('hdnBlock_' . ($inCountComponente));
    $obHdnMesBloqueado->setId   ('hdnBlock_' . ($inCountComponente));
    $obHdnMesBloqueado->setValue($boReadOnly);

    $arHdnMesBloqueado[$inCountComponente] = $obHdnMesBloqueado;

    if ($boReadOnly) {
        $obTxtPorcento = new Label();
        $obTxtPorcento->setName ('lblvlPorcentagem_' . ($inCountComponente));
        $obTxtPorcento->setId   ('lblvlPorcentagem_' . ($inCountComponente));
        $obTxtPorcento->setValue('' );

        $obHdnPorcento = new Hidden();
        $obHdnPorcento->setName ('vlPorcentagem_' . ($inCountComponente));
        $obHdnPorcento->setId   ('vlPorcentagem_' . ($inCountComponente));
        $obHdnPorcento->setValue('');

        $arTxtPorcento[$inCountComponente] = $obTxtPorcento;
        $arHdnPorcento[$inCountComponente] = $obHdnPorcento;
    } else {
        $obTxtPorcento = new Porcentagem();
        $obTxtPorcento->setName              ('vlPorcentagem_' . ($inCountComponente));
        $obTxtPorcento->setId                ('vlPorcentagem_' . ($inCountComponente));
        $obTxtPorcento->setValue             ('' );
        $obTxtPorcento->setReadOnly          ($boReadOnly);
        $obTxtPorcento->obEvento->setOnChange( "montaParametrosGET('mudaValor');" );

        $arTxtPorcento[$inCountComponente] = $obTxtPorcento;
    }

    if ($boReadOnly) {
        $obTxtValor = new Label();
        $obTxtValor->setName ('lblvlValor_' . ($inCountComponente));
        $obTxtValor->setId   ('lblvlValor_' . ($inCountComponente));
        $obTxtValor->setValue($vlPeriodo);

        $obHdnValor = new Hidden();
        $obHdnValor->setName ('vlValor_' . ($inCountComponente));
        $obHdnValor->setId   ('vlValor_' . ($inCountComponente));
        $obHdnValor->setValue($vlPeriodo);

        $arTxtValor[$inCountComponente] = $obTxtValor;
        $arHdnValor[$inCountComponente] = $obHdnValor;

    } else {
        $obTxtValor = new Numerico;
        $obTxtValor->setName              ('vlValor_' . ($inCountComponente));
        $obTxtValor->setId                ('vlValor_' . ($inCountComponente));
        $obTxtValor->setValue             ($vlPeriodo);
        $obTxtValor->setReadOnly          ($boReadOnly);
        $obTxtValor->setSize              (13);
        $obTxtValor->setMaxLength         (21);
        $obTxtValor->setNegativo          (false);
        $obTxtValor->obEvento->setOnChange("montaParametrosGET('mudaPorcentagem');");

        $arTxtValor[$inCountComponente] = $obTxtValor;
    }

}

$obHdnTotalPorcento = new Hidden();
$obHdnTotalPorcento->setName  ('TotalPorcento');
$obHdnTotalPorcento->setId    ('TotalPorcento');
$obHdnTotalPorcento->setValue ('0,00');
$obHdnTotalPorcento->montaHTML();

$obLblTotalPorcento = new Label();
$obLblTotalPorcento->setName  ('lblTotalPorcento');
$obLblTotalPorcento->setId    ('lblTotalPorcento');
$obLblTotalPorcento->montaHTML();

$obHdnTotalValor = new Hidden();
$obHdnTotalValor->setName  ('TotalValor');
$obHdnTotalValor->setId    ('TotalValor');
$obHdnTotalValor->setValue ('0,00');
$obHdnTotalValor->montaHTML();

$obHdnEntidadeAtual = new hidden();
$obHdnEntidadeAtual->setName ('inHdnEntidadeAtual');
$obHdnEntidadeAtual->setValue($inCodEntidade);

$obLblTotalValor = new Label();
$obLblTotalValor->setName  ('lblTotalValor');
$obLblTotalValor->setId    ('lblTotalValor');
$obLblTotalValor->montaHTML();

$arLista = array();

$arLista[0]['titulo'] = 'Porcentagem';
$arLista[1]['titulo'] = 'Valor';

$arLista[0]['total'] = $obHdnTotalPorcento->getHtml().$obLblTotalPorcento->getHTML();
$arLista[1]['total'] = $obHdnTotalValor->getHtml().$obLblTotalValor->getHTML();

for ($i = 1; $i <= $inNumeroColunas ; $i ++) {
    $arHdnMesBloqueado[$i]->montaHTML();
    if ($arHdnPorcento[$i]) {
        $arTxtPorcento[$i]->montaHTML();
        $arHdnPorcento[$i]->montaHTML();
        $arLista[0]['campo_' . $i] = $arTxtPorcento[$i]->getHTML().$arHdnPorcento[$i]->getHTML().$arHdnMesBloqueado[$i]->getHTML();
    } else {
        $arTxtPorcento[$i]->montaHTML();
        $arLista[0]['campo_' . $i] = $arTxtPorcento[$i]->getHTML().$arHdnMesBloqueado[$i]->getHTML();
    }
}

for ($i = 1; $i <= $inNumeroColunas ; $i ++) {
    if ($arHdnValor[$i]) {
        $arTxtValor[$i]->montaHTML();
        $arHdnValor[$i]->montaHTML();
        $arLista[1]['campo_' . $i] = $arTxtValor[$i]->getHTML().$arHdnValor[$i]->getHTML();

    } else {
        $arTxtValor[$i]->montaHTML();
        $arLista[1]['campo_' . $i] = $arTxtValor[$i]->getHTML();
    }
}

if ($stAcao == 'alterar') {
    //Recupera o valor arrecadado para a receita, dividindo por mes
    include_once CAM_GF_ORC_MAPEAMENTO.'FOrcamentoBalanceteDespesa.class.php';

    $obFBalanceteDespesa = new FOrcamentoBalanceteDespesa();

    $obFBalanceteDespesa->setDado('exercicio'             , Sessao::getExercicio());
    $obFBalanceteDespesa->setDado('stFiltro'              ,' AND od.cod_entidade = '.$inCodEntidade.' ');
    $obFBalanceteDespesa->setDado('stEntidade'            , $inCodEntidade);
    $obFBalanceteDespesa->setDado('stCodEstruturalInicial', '');
    $obFBalanceteDespesa->setDado('stCodEstruturalFinal'  , '');
    $obFBalanceteDespesa->setDado('stCodReduzidoInicial'  , $_GET['inCodDespesa']);
    $obFBalanceteDespesa->setDado('stCodReduzidoFinal'    , $_GET['inCodDespesa']);
    $obFBalanceteDespesa->setDado('stControleDetalhado'   , '');
    $obFBalanceteDespesa->setDado('inNumOrgao'            , $rsAcao->getCampo('num_orgao'));
    $obFBalanceteDespesa->setDado('inNumUnidade'          , $rsAcao->getCampo('num_unidade'));

    $stOrder = '';

    $arLista[2]['titulo'] = 'Empenhado';

    for ($i = 1; $i <= $inNumeroColunas ; $i ++) {
        $obFBalanceteDespesa->setDado("stDataInicial",date('d/m/Y',mktime(0,0,0,($i*(12/$inNumeroColunas)-((12/$inNumeroColunas)-1)),01,Sessao::getExercicio())));
        $obFBalanceteDespesa->setDado("stDataFinal",date('d/m/Y',mktime(0,0,0,$i*(12/$inNumeroColunas)+1,0,Sessao::getExercicio())));

        // Serve para fazer uma verificação interna onde não cria e dropa as tabelas temporarias
        if ($i == 1) {
            $obFBalanceteDespesa->setDado("stVerificaCreateDropTables", 'create');
        } elseif ($i != $inNumeroColunas) {
            $obFBalanceteDespesa->setDado("stVerificaCreateDropTables", 'continue');
        } else {
            $obFBalanceteDespesa->setDado("stVerificaCreateDropTables", 'drop');
        }
        $obFBalanceteDespesa->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder );

        while (!$rsRecordSet->eof()) {
            $inVlTotal += $rsRecordSet->getCampo('empenhado_per')-$rsRecordSet->getCampo('anulado_per');
            $arLista[2]['campo_'.$i] += $rsRecordSet->getCampo('empenhado_per')-$rsRecordSet->getCampo('anulado_per');

            $rsRecordSet->proximo();
        }
        $arLista[2]['campo_'.$i] = number_format($arLista[2]['campo_'.$i],2,',','.');
    }

   $arLista[2]['total'] = number_format($inVlTotal,'2',',','.');
}

$rsLista = new RecordSet();
$rsLista->preenche($arLista);

$obLista = new Lista();
$obLista->setRecordSet      ($rsLista);
$obLista->setNumeracao      (false);
$obLista->setMostraPaginacao(false);
$obLista->setTitulo         ('Registros de Metas de Execução da Despesa');

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Despesa');
$obLista->ultimoCabecalho->setWidth   (5);
$obLista->ultimoCabecalho->setRowSpan (2);
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Períodos');
$obLista->ultimoCabecalho->setWidth   (5);
$obLista->ultimoCabecalho->setColSpan ($inNumeroColunas + 1);
$obLista->commitCabecalho();

$bo = true;
for ($i = 1; $i <= $inNumeroColunas; $i++) {
    $obLista->addCabecalho($bo);
    $obLista->ultimoCabecalho->addConteudo($i.'º');
    $obLista->ultimoCabecalho->setWidth   (10);
    $obLista->commitCabecalho();
    $bo = false;
}

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Total');
$obLista->ultimoCabecalho->setWidth   (10);
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('CSS');
$obLista->ultimoDado->setClass      ('label');
$obLista->ultimoDado->setCampo      ('titulo');
$obLista->commitDado();

for ($i = 1; $i <= $inNumeroColunas; $i ++) {
    $obLista->addDado();
    $obLista->ultimoDado->setCampo("[campo_".$i."]");
    $obLista->commitDadoComponente();
}

$obLista->addDado();
$obLista->ultimoDado->setCampo('total');
$obLista->commitDadoComponente();

$obSimNaoAcao = new SimNao();
$obSimNaoAcao->setRotulo ('Permanecer nesta ação?');
$obSimNaoAcao->setTitle  ('Deseja permanecer nesta ação?');
$obSimNaoAcao->setName   ('rdMesmaAcao');
$obSimNaoAcao->setId     ('rdMesmaAcao');
$obSimNaoAcao->setNull   (true);
$obSimNaoAcao->setChecked('Não');

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm  ($obForm);
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnMascClassificacao);
$obFormulario->addHidden($obHdnNumCampos);
$obFormulario->addHidden($obHdnCodAcao);
$obFormulario->addHidden($obHdnTsAcaoDados);
$obFormulario->addHidden($obHdnInAno);
$obFormulario->addHidden($obHdnCodDespesa);
$obFormulario->addHidden($obHdnEntidadeAtual);
$obFormulario->addTitulo('Dados para Despesa');
$obMontaDotacaoOrcamentaria->geraFormulario($obFormulario, 'alterar', 1, $arDespesa);
$obFormulario->addComponente($obBscRubricaDespesa);
$obFormulario->addLista($obListaRecurso);
$obIMontaRecursoDestinacao->geraFormulario($obFormulario);
$obFormulario->addComponente($obCmbEntidade);
$obFormulario->addComponente($obTxtValorDotacao);

if($inCodUF == '15' || $inCodUF == '16') {
    $obFormulario->addComponente($obCmbModalidadeDespesa);
}

$obFormulario->addLista($obLista);

$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao;

$obFormulario->Cancelar($stLocation);
$obFormulario->addComponente($obSimNaoAcao);
$obFormulario->show();

if ($stAcao == 'alterar') {
    $jsOnload .= "montaParametrosGET('preencheMetas','','sincrono');";
    $jsOnload .= "montaParametrosGET('mudaPorcentagem','','sincrono');";
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
