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
 * Class de Visão de Ação
 * Data de Criação: 23/09/2008

 * Copyright CNM - Confederação Nacional de Municípios

 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Pedro Vaz de Mello de Medeiros

 * @package URBEM
 * @subpackage visao

 * $Id: VPPAManterAcao.class.php 64234 2015-12-21 17:24:45Z michel $

 * Caso de Uso: uc-02.09.04
 */
include_once 'VPPAUtils.class.php';
include_once 'VPPAManterPrograma.class.php';
require_once CAM_GF_PPA_COMPONENTES.'IPopUpPrograma.class.php';
include_once CAM_GF_PPA_NEGOCIO.'RPPAManterPrograma.class.php';
include_once CAM_GF_PPA_NEGOCIO.'RPPAManterProduto.class.php';
include_once CAM_GF_PPA_VISAO.'VPPAManterPrograma.class.php';
include_once CAM_GF_PPA_MAPEAMENTO.'TPPAProgramaSetorial.class.php';
include_once CAM_GF_PPA_MAPEAMENTO.'TPPAMacroObjetivo.class.php';
include_once CAM_GF_PPA_MAPEAMENTO.'TPPATipoAcao.class.php';
include_once CAM_GF_ORC_NEGOCIO.'ROrcamentoProjetoAtividade.class.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEspecificacaoDestinacaoRecurso.class.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoUnidade.class.php';
include_once CAM_GF_ORC_COMPONENTES.'ISelectFuncao.class.php';
include_once CAM_GF_ORC_COMPONENTES.'ISelectSubfuncao.class.php';
include_once CAM_GF_ORC_COMPONENTES.'IMontaRecursoDestinacao.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoRecurso.class.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoRecursoDestinacao.class.php';
include_once CAM_GF_CONT_NEGOCIO.'RContabilidadePlanoBanco.class.php';

class VPPAManterAcao extends VPPAUtils
{
    private $obCtrl;

    public function __construct($obCtrl)
    {
        $this->obCtrl = $obCtrl;
    }

    public function recuperaListaAcoes($inNumPrograma, $inCodAcaoInicio = '', $inCodAcaoFim = '', $inCodPPA = '')
    {
        $rsAcoes = $this->obCtrl->recuperaListaAcoes($inNumPrograma, $inCodAcaoInicio, $inCodAcaoFim, $inCodPPA);

        # Formata campo numérico valor.
        foreach ($rsAcoes->arElementos as &$arCampos) {
            $arCampos['valor']    = $this->floatToStr($arCampos['valor']);
            $arCampos['cod_acao'] = sprintf('%03d', $arCampos['cod_acao']);
        }

        return $rsAcoes;
    }

    public function buscaDescricaoRegiao(array $arParametros)
    {
        $stDescricao = SistemaLegado::pegaDado('descricao', 'ppa.regiao', ' WHERE cod_regiao = '.$arParametros['inCodRegiao']);

        if ($stDescricao != '') {
            $obFormulario = new Formulario;
            $obFormulario->setForm(new Form);

            $lblDescricao = new Label;
            $lblDescricao->setRotulo('Descrição/Área Abrangência');
            $lblDescricao->setValue($stDescricao);

            $obFormulario->addComponente($lblDescricao);
            $obFormulario->montaInnerHTML();

            $stDescricao = $obFormulario->getHTML();
        }

        return "jq('#spnDescricaoRegiao').html('".$stDescricao."');";
    }

    /**
     * Lista os dados do produto
     */
    public function listaProduto(array $arProdutos, $boJS = true, $boConsulta = false)
    {
        # Define cabeçalhos da lista.
        $arCabecalhos = array(
            array('cabecalho' => 'Produto',          'width' => 30),
            array('cabecalho' => 'U. M.',            'width' => 22),
            array('cabecalho' => 'Quantidade Ano 1', 'width' => 12),
            array('cabecalho' => 'Quantidade Ano 2', 'width' => 12),
            array('cabecalho' => 'Quantidade Ano 3', 'width' => 12),
            array('cabecalho' => 'Quantidade Ano 4', 'width' => 12)
        );

        # Define os campos da lista.
        $arComponentes = array(
            array('tipo'        => 'Label',
                  'alinhamento' => 'CENTRO',
                  'campo'       => 'descricao'),
            array('tipo'        => 'Label',
                  'alinhamento' => 'CENTRO',
                  'campo'       => 'dsc_unidade'),
            array('tipo'        => 'Numerico',
                  'name'        => 'flQuantidadeAno1',
                  'campo'       => 'quantidade_ano_1',
                  'readOnly'    => true),
            array('tipo'        => 'Numerico',
                  'name'        => 'flQuantidadeAno2',
                  'campo'       => 'quantidade_ano_2',
                  'readOnly'    => true),
            array('tipo'        => 'Numerico',
                  'name'        => 'flQuantidadeAno3',
                  'campo'       => 'quantidade_ano_3',
                  'readOnly'    => true),
            array('tipo'        => 'Numerico',
                  'name'        => 'flQuantidadeAno4',
                  'campo'       => 'quantidade_ano_4',
                  'readOnly'    => true)
        );

        return $this->montaLista('Produto', 'Dados do Produto', $arCabecalhos, $arComponentes, $arProdutos, $boConsulta, $boJS, false);
    }

    /**
     * Lista os recursos da Ação.
     * @param  array   $rsRecursos      recordset com lista de recursos
     * @param  boolean $boJS=true       se a função deve retornar texto pronto pra anexar em javascript
     * @param  boolean $boConsulta=true se a função deve retornar texto editável ou apenas para consulta
     * @param  boolean $boTotal=false   se a função deve calcular o valor total
     * @return string  código HTML da lista
     */
    public function listaRecursos(array $arRecursos, $boJS = true)
    {
        $boReadOnly = $boConsulta;

        for ($i = 1; $i < 5; $i++) {
            $obTxtValor = new Numerico;
            $obTxtValor->setId('arValorAno'.$i);
            $obTxtValor->setName('arValorAno'.$i);
            $obTxtValor->setClass('valor');
            $obTxtValor->setValue('[ano'.$i.']');
            $obTxtValor->setMaxLength(14);
            $obTxtValor->setSize(14);
            $obTxtValor->setLabel(true);
            $obTxtValor->setNegativo(false);
            $obTxtValor->setReadOnly($boReadOnly);
            $obNome = 'obTxtValor'.$i;
            $$obNome = $obTxtValor;
        }

        $obTxtValorTotal = new Numerico;
        $obTxtValorTotal->setId('arValorAno'.$i);
        $obTxtValorTotal->setName('arValorAno'.$i);
        $obTxtValorTotal->setClass('valor');
        $obTxtValorTotal->setValue('[ano'.$i.']');
        $obTxtValorTotal->setMaxLength(14);
        $obTxtValorTotal->setSize(14);
        $obTxtValorTotal->setValue('[total]');
        $obTxtValorTotal->setLabel(true);

        $rsRecurso = new RecordSet;
        $rsRecurso->preenche($arRecursos);

        $obTblRecursos = new TableTree;
        $obTblRecursos->setId('obTblRecursos');
        $obTblRecursos->setSummary('Lista de Recursos');
        //$obTblRecursos->setConditional(true, "#efefef");
        $obTblRecursos->setArquivo('OCManterAcao.php');
        $obTblRecursos->setRecordSet($rsRecurso);
        $obTblRecursos->setParametros(array('cod_recurso'));
        $obTblRecursos->setComplementoParametros('stCtrl=montaMetaFisica');
        $obTblRecursos->Head->addCabecalho ('Recurso'       , 37);
        $obTblRecursos->Head->addCabecalho ('Valor Ano 1'   , 12);
        $obTblRecursos->Head->addCabecalho ('Valor Ano 2'   , 12);
        $obTblRecursos->Head->addCabecalho ('Valor Ano 3'   , 12);
        $obTblRecursos->Head->addCabecalho ('Valor Ano 4'   , 12);
        $obTblRecursos->Head->addCabecalho ('Total Recurso' , 12);
        $obTblRecursos->Body->addCampo     ('[nom_cod_recurso]', 'E');
        $obTblRecursos->Body->addComponente($obTxtValor1    , 'D');
        $obTblRecursos->Body->addComponente($obTxtValor2    , 'D');
        $obTblRecursos->Body->addComponente($obTxtValor3    , 'D');
        $obTblRecursos->Body->addComponente($obTxtValor4    , 'D');
        $obTblRecursos->Body->addComponente($obTxtValorTotal, 'D');
        $obTblRecursos->Body->addAcao('excluir','excluirRecurso(\'%s\',\'%s\')',array('cod_acao','cod_recurso'));

        $obTblRecursos->montaHTML($boJS);

        # Gera hidden com número de elementos.
        $obHidden = new Hidden();
        $obHidden->setName('inSizeRecurso');
        $obHidden->setValue(count($arRecursos));
        $obHidden->montaHtml();
        $stHTML =  $obHidden->getHtml();

        foreach ($arRecursos as $arDados) {
            $stHTML .= $this->geraHidden('arCodRecurso', $arDados['cod_recurso']);
            $stHTML .= $this->geraHidden('arNomRecurso', $arDados['nom_cod_recurso']);
        }

        return $stHTML.$obTblRecursos->getHtml();
    }

    public function arredondarValor(array $arParam)
    {
        $arReturn['flValor'] = $this->arredondaValorMonetario($arParam['flValor']);

        return json_encode($arReturn);
    }

    private function limparListaRecurso(array $arParametros)
    {
        $stJS = '';

        if ($arParametros['inSizeRecurso']) {
            $stJS .= 'document.frm.inSizeRecurso.value = 0;';
        }

        return $stJS . "jq('#spnListaRecurso').html('');";
    }

    private function montaListaRecursos(array &$arRecursos, array $arParametros)
    {
        $arMapeamento = array('cod_recurso'     => 'arCodRecurso',
                              'nom_cod_recurso' => 'arNomRecurso',
                              'ano1'            => 'arValorAno1',
                              'ano2'            => 'arValorAno2',
                              'ano3'            => 'arValorAno3',
                              'ano4'            => 'arValorAno4',
                              'anoTotal'        => 'arValorTotal');

        // Recupera os valores anteriores e coloca-os em array.
        if (count($arParametros['arValorAno1'])) {
            foreach (array_keys($arParametros['arValorAno1']) as $inRecurso) {
                $arLinha = array();

                foreach ($arMapeamento as $stChave1 => $stChave2) {
                    $arLinha[$stChave1] = $arParametros[$stChave2][$inRecurso];
                }

                array_unshift($arRecursos, $arLinha);
            }
        }
    }

    private function calculaRecursos(array $arParametros)
    {
        $flTotalRecurso = 0.0;

        if (count($arParametros['arValorAno1'])) {
            for ($i = 1; $i < 5; $i++) {
                foreach ($arParametros["arValorAno$i"] as $flValor) {
                    $flTotalRecurso += $this->strToFloat($flValor);
                }
            }
        }

        return $flTotalRecurso;
    }

    public function atualizarDadosRecursos(array $arParametros)
    {
        $stJS = '';
        $inCodPPA       = $arParametros['inCodPPA'];
        $flTotalPPA     = $this->obCtrl->recuperaTotalPPA($inCodPPA);
        $flTotalReceita = $this->obCtrl->recuperaTotalReceitas($inCodPPA, '', Sessao::getExercicio());
        $flTotalRecurso = $this->calculaRecursos($arParametros);

        # Remove Recurso já computado do cálculo para alterações.
        if ($arParametros['stAcao'] == 'alterar') {
            $inCodAcao = $arParametros['inCodAcao'];
            $flRecursoAnterior = $this->obCtrl->recuperaTotalAcao($inCodAcao);
        }

        $flTotalAcumulado = $flTotalReceita - $flTotalPPA + $flRecursoAnterior - $flTotalRecurso;

        if (!$flTotalRecurso) {
            $stJS .= "$('stTotalAcao').innerHTML = '&nbsp;';";
        } else {
            $stJS .= "$('stTotalAcao').innerHTML = '" . $this->floatToStr($flTotalRecurso) . "';";
        }

        if (!$flTotalAcumulado) {
            $stJS .= "$('stTotalAcumulado').innerHTML = '&nbsp;';";
        } else {
            $stJS .= "$('stTotalAcumulado').innerHTML = '" . $this->floatToStr($flTotalAcumulado) . "';";
        }

        return $stJS;
    }

    public function incluirRecurso(array $arParametros)
    {
        $arParametrosMetas = Sessao::read('arParametrosMetas');

        if ($arParametros['stDestinacaoRecurso']) {
            if ($arParametros['stDestinacaoRecurso'] == '' || $arParametros['inCodUso'] == '' || $arParametros['inCodDestinacao'] == '' ||
                $arParametros['inCodEspecificacao'] == '' || $arParametros['inCodDetalhamento'] == '') {
                return 'alertaAviso("Campo Destinação de Recurso obrigatório! (Selecione todos os campos da Destinação de Recurso)", "form", "erro", "' . Sessao::getID() . '");';
            }
        } else {
            if ($arParametros['inCodRecurso'] == '') {
                return 'alertaAviso("Campo Recurso obrigatório!()", "form", "erro", "' . Sessao::getID() . '");';
            }
        }

        if ($arParametros['inCodRecurso'] != '') {
            // Obtem nome do Recurso.
            $obMapeamento = new TOrcamentoRecurso();
            $stFiltro     = ' WHERE cod_recurso = ' . $arParametros['inCodRecurso'] . ' AND ';
            $stFiltro    .= " exercicio = '" . Sessao::getExercicio() . "' ";
            $obErro = $obMapeamento->recuperaTodos($rsRecurso, $stFiltro);
            // Obtem descrição do Recurso.
            $arParametros['inCodRecurso'] = $arParametros['inCodRecurso'];
            $stDescricaoRecurso  = $arParametros['inCodRecurso'].' - ';
            $stDescricaoRecurso .= $rsRecurso->getCampo('nom_recurso');
        } else {
            $obErro = new Erro;

            $obTOrcamentoEspecificacaoDestinacaoRecurso = new TOrcamentoEspecificacaoDestinacaoRecurso;
            $obTOrcamentoEspecificacaoDestinacaoRecurso->setDado('exercicio'        , Sessao::getExercicio());
            $obTOrcamentoEspecificacaoDestinacaoRecurso->setDado('cod_especificacao', $arParametros['inCodEspecificacao']);
            $obTOrcamentoEspecificacaoDestinacaoRecurso->recuperaPorChave($rsEspecificacao, $boTransacao);

            $stDescricaoRecurso  = $arParametros['stDestinacaoRecurso'].' - ';
            $stDescricaoRecurso .= $rsEspecificacao->getCampo('descricao');

            $stExercicioRecurso = intval(Sessao::getExercicio());
            $stFiltroBuscaExiste  = ' WHERE exercicio         = '.$stExercicioRecurso;
            $stFiltroBuscaExiste .= '   AND cod_uso           = '.$arParametros['inCodUso'];
            $stFiltroBuscaExiste .= '   AND cod_destinacao    = '.$arParametros['inCodDestinacao'];
            $stFiltroBuscaExiste .= '   AND cod_especificacao = '.$arParametros['inCodEspecificacao'];
            $stFiltroBuscaExiste .= '   AND cod_detalhamento  = '.$arParametros['inCodDetalhamento'];
            $obTOrcamentoRecursoDestinacao = new TOrcamentoRecursoDestinacao;
            $obTOrcamentoRecursoDestinacao->recuperaTodos($rsDestinacao, $stFiltroBuscaExiste);
            $inCodRecurso = $rsDestinacao->getCampo('cod_recurso');

            if ($inCodRecurso == '') {
                $arParametros['stExercicioRecurso'] = $stExercicioRecurso;
                $obErro = $this->obCtrl->incluirRecursoDestinacao($inCodRecurso, $arParametros);
                if ($obErro->ocorreu()) {
                    return 'alertaAviso("'.$obErro->getDescricao().'!()", "form", "erro", "' . Sessao::getID() . '");';
                }
            }

            if (!$obErro->ocorreu()) {
                $stCampo  = 'ano_inicio';
                $stTabela = 'ppa.ppa';
                $stFiltro = ' WHERE cod_ppa = '.$arParametros['inCodPPA'];

                $stAnoInicio = SistemaLegado::pegaDado($stCampo, $stTabela, $stFiltro);

                // Cria a destinação de recurso para os 4 exercícios do PPA
                for ($inCount = 0; $inCount <= 3; $inCount++) {
                    $stExercicioRecurso = intval($stAnoInicio) + $inCount;
                    $stFiltroBuscaExiste  = ' WHERE exercicio         = '.$stExercicioRecurso;
                    $stFiltroBuscaExiste .= '   AND cod_uso           = '.$arParametros['inCodUso'];
                    $stFiltroBuscaExiste .= '   AND cod_destinacao    = '.$arParametros['inCodDestinacao'];
                    $stFiltroBuscaExiste .= '   AND cod_especificacao = '.$arParametros['inCodEspecificacao'];
                    $stFiltroBuscaExiste .= '   AND cod_detalhamento  = '.$arParametros['inCodDetalhamento'];
                    $obTOrcamentoRecursoDestinacao = new TOrcamentoRecursoDestinacao;
                    $obTOrcamentoRecursoDestinacao->recuperaTodos($rsDestinacao, $stFiltroBuscaExiste);
                    $inCodRecurso = $rsDestinacao->getCampo('cod_recurso');

                    if ($inCodRecurso == '') {
                        $arParametros['stExercicioRecurso'] = $stExercicioRecurso;
                        $obErro = $this->obCtrl->incluirRecursoDestinacao($inCodRecurso, $arParametros);
                        if ($obErro->ocorreu()) {
                            return 'alertaAviso("'.$obErro->getDescricao().'", "form", "erro", "' . Sessao::getID() . '");';
                        }
                    }
                }
            }

            $arParametros['stDestinacaoRecurso'] = $inCodRecurso;
        }

        $flTotal = 0;
        for ($i = 1; $i<= 4; $i++) {
            $flTotal += str_replace(',','.',str_replace('.','',$arParametros['flValorAno' . $i]));
        }

        if ((float) $flTotal == 0) {
            return 'alertaAviso("O total do recurso não pode ser 0!()", "form", "erro", "' . Sessao::getID() . '");';
        }

        // Testa se Recurso já existe na lista de Recursos.
        if ($arParametros['arCodRecurso']) {
            foreach ($arParametros['arCodRecurso'] as $inCodRecurso) {
                if ($arParametros['inCodRecurso'] == $inCodRecurso || $arParametros['stDestinacaoRecurso'] == $inCodRecurso) {
                    return 'alertaAviso("Recurso já incluído para esta ação!()", "form", "erro", "' . Sessao::getID() . '");';
                }
                if (isset($arParametros['flQuantidade_1_'.$inCodRecurso])) {
                    for ($i = 1; $i<= 4; $i++) {
                        $arParametrosMetas['flQuantidade_'.$i.'_'.$inCodRecurso] = $arParametros['flQuantidade_'.$i.'_'.$inCodRecurso];
                        $arParametrosMetas['flValorTotal_'.$i.'_'.$inCodRecurso] = $arParametros['flValorTotal_'.$i.'_'.$inCodRecurso];
                    }
                    $arParametrosMetas['flQuantidade_total_'.$inCodRecurso] = $arParametros['flQuantidade_total_'.$inCodRecurso];
                    $arParametrosMetas['flValorTotal_total_'.$inCodRecurso] = $arParametros['flValorTotal_total_'.$inCodRecurso];
                }
            }
        }

        Sessao::write('arParametrosMetas', $arParametrosMetas);

        // Acrescenta o novo Recurso na lista de Recursos.
        if (!is_array($arParametros['arCodRecurso'])) {
            $arParametros['arCodRecurso'] = array();
            $arParametros['arNomRecurso'] = array();
            $arParametros['arValorAno1']  = array();
            $arParametros['arValorAno2']  = array();
            $arParametros['arValorAno3']  = array();
            $arParametros['arValorAno4']  = array();
        }

        array_unshift($arParametros['arCodRecurso'], ($arParametros['inCodRecurso'] == '') ? $arParametros['stDestinacaoRecurso'] : $arParametros['inCodRecurso']);
        array_unshift($arParametros['arNomRecurso'], $stDescricaoRecurso);
        array_unshift($arParametros['arValorAno1'], $arParametros['flValorAno1'] ? $arParametros['flValorAno1'] : '0,00');
        array_unshift($arParametros['arValorAno2'], $arParametros['flValorAno2'] ? $arParametros['flValorAno2'] : '0,00');
        array_unshift($arParametros['arValorAno3'], $arParametros['flValorAno3'] ? $arParametros['flValorAno3'] : '0,00');
        array_unshift($arParametros['arValorAno4'], $arParametros['flValorAno4'] ? $arParametros['flValorAno4'] : '0,00');

        $arRecursos = array();

        $this->montaListaRecursos($arRecursos, $arParametros);

        $arAcaoValidada = Sessao::read('arAcaoValidada');
        if (!is_array($arAcaoValidada)) {
            $arAcaoValidada = array();
        }
        // Atualiza o formulário.
        $stJS  = "\n jq('#spnListaRecurso').html('".$this->listaRecursos($arRecursos)."');";
        $stJS .= "\n formataListaRecurso();";
        $stJS .= "\n formatListaRecurso();";
        $stJS .= $this->limparRecurso();
        $stJS .= "\n jq('#obTblRecursos_row_".count($arParametros['arCodRecurso'])."_mais').trigger('click');";
        $stJS .= "\n formatAnosAcaoValidada('".json_encode($arAcaoValidada)."');";

        return $stJS;
    }

    public function excluirRecurso(array $arParametros)
    {
        $stMsg  = '';
        $stJs = '';

        $cod_acao = $arParametros['cod_acao'];
        $cod_recurso = $arParametros['cod_recurso'];

        $arRecursosTemp = array();
        $arRecursos = Sessao::read('arRecursos');

        $arAcaoValidadaTemp = array();
        $arAcaoValidada = Sessao::read('arAcaoValidada');

        // Verificação se o Recurso foi validado em LDO.
        //Se validado, não pode excluir recurso da ação.
        foreach ($arAcaoValidada as $key => $value) {
            if( $key == str_pad($cod_recurso, 4, 0, STR_PAD_LEFT) ){
                $stMsg .= 'O recurso '.$cod_recurso.' não pode ser excluído pois o mesmo está validado na LDO';
                break;
            }else{
                $arAcaoValidadaTemp[$key] = $value;
            }
        }
        $arAcaoValidada = $arAcaoValidadaTemp;

        if($stMsg==''){
            foreach ($arRecursos as $key => $value) {
                if( $value['cod_acao'].'.'.$value['cod_recurso'] != $cod_acao.'.'.$cod_recurso ){
                    $arRecursosTemp[] = $value;
                }
            }

            $arRecursos = $arRecursosTemp;
            if (is_array($arRecursos) && count($arRecursos) > 0) {
                $stListaRecurso = $this->listaRecursos($arRecursos);
            } else {
                $stListaRecurso = '';
            }

            // Gera o Javascript para atualizar a tela.
            $stJs .= "\n jq('#spnListaRecurso').html('".$stListaRecurso."');";
            $stJs .= "\n formatListaRecurso();";
            $stJs .= "\n formatAnosAcaoValidada('".json_encode($arAcaoValidada)."');";

            Sessao::write('arRecursos', $arRecursos);
            Sessao::write('arAcaoValidada', $arAcaoValidada);
        }else{
            $stJs .= "\n alertaAviso('".$stMsg."!', 'form', 'erro','".Sessao::getId()."');";
        }

        return $stJs;
    }

    public function limparRecurso()
    {
        $stJs = "jq('#inCodRecurso').val('');";
        $stJs .= "jq('#stDestinacaoRecurso').val('');";
        $stJs .= "jq('#inCodUso').selectOptions('',true);";
        $stJs .= "jq('#inCodDestinacao').selectOptions('',true);";
        $stJs .= "jq('#inCodEspecificacao').selectOptions('',true);";
        $stJs .= "jq('#inCodDetalhamento').selectOptions('',true);";

        $stJs .= "jq('#stDescricaoRecurso').html('&nbsp;');";
        $stJs .= "jq('#flValorAno1').val('0,00');";
        $stJs .= "jq('#flValorAno2').val('0,00');";
        $stJs .= "jq('#flValorAno3').val('0,00');";
        $stJs .= "jq('#flValorAno4').val('0,00');";

        return $stJs;
    }

    public function mostrarListaRecurso(array $arParametros)
    {
        $arRecursos = array();

        $this->montaListaRecursos($arRecursos, $arParametros);
        $stHTML = $this->listaRecursos($arRecursos);

        $stJS  = "\n jq('#spnListaRecurso').html('".$stHTML."');";
        $stJS .= "\n formataListaRecurso();";
        $stJS .= "\n formatListaRecurso();";

        return $stJS;

    }

    /**
     * Retorna próximo número da ação formatado com string com 2 zeros a esquerda.
     * @param  array  $arParametros parâmetros recebidos da página
     * @return string próximo número da ação formatado
     */
    public function buscaCodAcao(array $arParam)
    {
        if (!$arParam['inCodPrograma'] || !$arParam['inCodTipo']) {
            return "$('inCodAcao').value = '';";
        }

        $inCodAcao = sprintf('%04d', $this->obCtrl->recuperaProxCodAcao($arParam['inCodPrograma'], $arParam['inCodTipo']));

        return "$('inCodAcao').value = '" . $inCodAcao . "';";
    }

    /**
     * Verifica a validade do número da Ação
     * @param array $arParametros parâmetros recebidos da página
     */
    public function verificarCodAcao(array $arParam)
    {
        if ($arParam['inCodPPA'] != '') {
            if ($arParam['inCodAcao'] != '' AND $arParam['inCodTipo'] != '') {
                $obROrcamentoProjetoAtividade = new ROrcamentoProjetoAtividade;
                $obROrcamentoProjetoAtividade->obRConfiguracaoOrcamento->consultarConfiguracao();
                $inPos = $obROrcamentoProjetoAtividade->obRConfiguracaoOrcamento->getNumPAOPosicaoDigitoID();
                switch ($arParam['inCodTipo']) {
                case 1:
                    $arDig = explode(',',$obROrcamentoProjetoAtividade->obRConfiguracaoOrcamento->getNumPAODigitosIDProjeto());
                    break;
                case 2:
                    $arDig = explode(',',$obROrcamentoProjetoAtividade->obRConfiguracaoOrcamento->getNumPAODigitosIDAtividade());
                    break;
                case 3:
                    $arDig = explode(',',$obROrcamentoProjetoAtividade->obRConfiguracaoOrcamento->getNumPAODigitosIDOperEspeciais());
                    break;
                default :
                    $arDig = explode(',',$obROrcamentoProjetoAtividade->obRConfiguracaoOrcamento->getNumPAODigitosIDNaoOrcamentarios());
                    break;
                }

                if (in_array(substr($arParam['inCodAcao'],($inPos - 1),1), $arDig)) {
                    $obTPPA = new TPPA;
                    $obTPPA->recuperaPPA($rsPPA,' WHERE cod_ppa = ' . $arParam['inCodPPA'] . ' ');

                    $obROrcamentoProjetoAtividade->setExercicio($rsPPA->getCampo('ano_inicio'));
                    $obROrcamentoProjetoAtividade->setNumeroProjeto($arParam['inCodAcao']);
                    $obROrcamentoProjetoAtividade->listar($rsLista);

                    $rsAcao = $this->obCtrl->verificaAcao('',$arParam['inCodAcao'],$arParam['inCodPPA']);

                    if ($rsLista->getNumLinhas() > 0 OR $rsAcao->getNumLinhas() > 0) {
                        $stJs  = "alertaAviso('O Código ".$arParam['inCodAcao']." já está cadastrado no sistema!', 'form', 'erro','".Sessao::getId()."');";
                        $stJs .= "jq('#inCodAcao').val('');";
                    }
                } else {
                    $stJs  = "alertaAviso('O Código ".$arParam['inCodAcao']." não pertence ao subtipo da ação selecionado!', 'form', 'erro','".Sessao::getId()."');";
                    $stJs .= "jq('#inCodAcao').val('');";
                }
            } else {
                $stJs  = "alertaAviso('Informe o subtipo da ação!', 'form', 'erro','".Sessao::getId()."');";
                $stJs .= "jq('#inCodAcao').val('');";
            }
        } else {
            $stJs  = "alertaAviso('Informe o PPA!', 'form', 'erro','".Sessao::getId()."');";
            $stJs .= "jq('#inCodAcao').val('');";
        }

        return $stJs;
    }

    /**
     * Verifica disponibilidade de receita para registrar ação.
     * @param  array $arParametros parâmetros recebidos da página
     * @return bool  se o novo valor da ação é permitido
     */
    private function verificaAcumulado(array $arParametros)
    {
        $stJS = '';
        $inCodPPA       = $arParametros['inCodPPA'];
        $inCodAcao      = $arParametros['inCodAcao'];
        $flTotalPPA     = $this->obCtrl->recuperaTotalPPA($inCodPPA);
        $flTotalReceita = $this->obCtrl->recuperaTotalReceitas($inCodPPA, '', Sessao::getExercicio());
        $flTotalRecurso = $this->calculaRecursos($arParametros);

        # Remove Recurso já computado do cálculo para alterações.
        if ($arParametros['stAcao'] == 'alterar') {
            $flRecursoAnterior = $this->obCtrl->recuperaTotalAcao($inCodAcao);
        }

        $flTotalAcumulado = ($flTotalReceita + $flRecursoAnterior) - $flTotalPPA - $flTotalRecurso;

        return (bool) $flTotalAcumulado >= 0;
    }

    /**
     * Formata e completa dados lidos da página para serem processados pela regra de negócio.
     * @param  array $arParametros parâmetros recebidos da página
     * @return array array contendo recursos formatado
     */
    private function formataDados(array &$arParametros)
    {
        # Guarda o timestamp antigo
        $arParametros['tsAcaoDadosAnterior'] = $arParametros['tsAcaoDados'];

        # Define timestamp atual para acao_dados.
        $arParametros['tsAcaoDados'] = date('Y-m-d H:i:s');

        # Define o default do ano de exercício da unidade orçamentária como o ano atual.
        if (!isset($arParametros['inExercicioUnidade'])) {
            $arParametros['inExercicioUnidade'] = Sessao::getExercicio();
        }

        # Define o default do ano de exercício como o ano atual.
        if (!isset($arParametros['inExercicio'])) {
            $arParametros['inExercicio'] = Sessao::getExercicio();
        }

        # Separa dados da unidade de medida e grandeza.
        if (isset($arParametros['stUnidadeMedida'])) {
            list($inCodUnidadeMedida, $inCodGrandeza) = explode('-', $arParametros['stUnidadeMedida']);
            $arParametros['inCodUnidadeMedida'] = (int) $inCodUnidadeMedida;
            $arParametros['inCodGrandeza']      = (int) $inCodGrandeza;
        }

        # Separa dados de órgão e unidade orçamentária.
        if (isset($arParametros['stUnidadeOrcamentaria'])) {
            list($inNumOrgao, $inNumUnidade) = explode('.', $arParametros['stUnidadeOrcamentaria']);
            $arParametros['inNumOrgao']   = (int) $inNumOrgao;
            $arParametros['inNumUnidade'] = (int) $inNumUnidade;
        }

        # Formata os dados dos recursos.
        $arRecursos  = array();
        $flTotalAcao = 0;

        for ($i = 0; $i < $arParametros['inSizeRecurso']; ++$i) {
            for ($j = 1; $j < 5; ++$j) {
                $arRecursos[$j][$i] = $this->strToFloat($arParametros["arValorAno$j"][$i]);
                $flTotalAcao += $arRecursos[$j][$i];
            }
        }

        $arParametros['arRecursos'] = $arRecursos;
        $arParametros['flTotalAcao'] = $flTotalAcao;

        # Formata dados na lista de recursos.
        for ($i = 0; $i < $arParametros['inSizeRecurso']; ++$i) {
            for ($j = 1; $j < 5; ++$j) {
                $arParametros["arValorAno$j"][$i] = $this->strToFloat($arParametros["arValorAno$j"][$i]);
            }

            # O widget de recursos só busca recursos do ano atual.
            $arParametros['arExeRecurso'][$i] = Sessao::getExercicio();
        }

        # Formata os dados de quantidade.
        $arQuantidades = array();

        for ($i = 1; $i < 5; ++$i) {
            $arQuantidades[$i] = $this->strToFloat($arParametros["flQuantidadeAno$i"]);
        }

        $arParametros['arQuantidades'] = $arQuantidades;
    }

    /**
     * Faz a inclusão de Ação.
     * @params array $arParametros parametros recebidos da página FMManterAcao.php
     */
    public function incluir(array $arParametros)
    {
        $obRPPAManterPrograma = new RPPAManterPrograma();
        $obVPPAManterPrograma = new VPPAManterPrograma($obRPPAManterPrograma);

        $this->formataDados($arParametros);

        $rsPrograma = $obVPPAManterPrograma->buscaPrograma($arParametros);

        if ($rsPrograma->getCampo('cod_tipo_programa') != 4) {
            if ($arParametros['inCodProduto'] == '') {
                SistemaLegado::LiberaFrames(true,false);

                return SistemaLegado::exibeAviso('Preencha o campo produto!', 'form', 'erro');
            }
            if ($arParametros['stUnidadeMedida'] == '') {
                SistemaLegado::LiberaFrames(true,false);

                return SistemaLegado::exibeAviso('Preencha o campo unidade de medida!', 'form', 'erro');
            }
        }

        if (!isset($arParametros['arNomRecurso']) || !count($arParametros['arNomRecurso'])) {
            SistemaLegado::LiberaFrames(true,false);

            return SistemaLegado::exibeAviso('Necessário especificar pelo menos um recurso!()', 'form', 'erro');
        }

        if (!isset($arParametros['arNomRecurso']) || !count($arParametros['arNomRecurso'])) {
            SistemaLegado::LiberaFrames(true,false);

            return SistemaLegado::exibeAviso('Necessário especificar pelo menos um recurso!()', 'form', 'erro');
        }

        if ($_REQUEST['inCodTipoAcao'] == 1) {
            if ($_REQUEST['slNaturezaDespesa'] == '') {
                SistemaLegado::LiberaFrames(true,false);

                return SistemaLegado::exibeAviso('Preencha o campo natureza da despesa!', 'form', 'erro');
            }
            if ($_REQUEST['inCodFuncao'] == '') {
                SistemaLegado::LiberaFrames(true,false);

                return SistemaLegado::exibeAviso('Preencha o campo função!', 'form', 'erro');
            }
            if ($_REQUEST['inCodSubFuncao'] == '') {
                SistemaLegado::LiberaFrames(true,false);

                return SistemaLegado::exibeAviso('Preencha o campo subfunção!', 'form', 'erro');
            }
        }

        if (isset($arParametros['stDataInicial'])) {
            if ($arParametros['stDataInicial'] == '' OR $arParametros['stDataFinal'] == '') {
                SistemaLegado::LiberaFrames(true,false);

                return SistemaLegado::exibeAviso('É necessário informar o período!', 'form', 'erro');
            }
        }

        if ( isset($arParametros['inCodIdentificadorAcao'])) {
            if ($arParametros['inCodIdentificadorAcao'] == '' OR $arParametros['inCodIdentificadorAcao'] == '') {
                SistemaLegado::LiberaFrames(true,false);

                return SistemaLegado::exibeAviso('É necessário informar o Identificador!', 'form', 'erro');
            }   
        }

        $obErro = $this->obCtrl->incluir($arParametros);

        if ($obErro->ocorreu()) {
            $stMessage = urlencode($obErro->getDescricao());
            SistemaLegado::LiberaFrames(true,false);
            SistemaLegado::exibeAviso($stMessage, 'n_' . __FUNCTION__, 'erro');
        } else {
            $stCaminho = 'FMManterAcao.php?stAcao=incluir';
            SistemaLegado::LiberaFrames(true,false);
            SistemaLegado::alertaAviso($stCaminho, $arParametros['inNumAcao'], __FUNCTION__, 'aviso', Sessao::getID(), '../');
        }
    }

    /**
     * Faz a inclusão de despesa.
     * @params array $arParametros parametros recebidos da página FMManterAcao.php
     */
    public function alterar(array $arParametros)
    {
        $this->formataDados($arParametros);

        $obRPPAManterPrograma = new RPPAManterPrograma();
        $obVPPAManterPrograma = new VPPAManterPrograma($obRPPAManterPrograma);

        $rsPrograma = $obVPPAManterPrograma->buscaPrograma($arParametros);

        if ($rsPrograma->getCampo('cod_tipo_programa') != 4) {
            if ($arParametros['inCodProduto'] == '') {
                SistemaLegado::LiberaFrames(true,false);

                return SistemaLegado::exibeAviso('Preencha o campo produto!', 'form', 'erro');
            }
            if ($arParametros['stUnidadeMedida'] == '') {
                SistemaLegado::LiberaFrames(true,false);

                return SistemaLegado::exibeAviso('Preencha o campo unidade de medida!', 'form', 'erro');
            }
        }

        if (!isset($arParametros['arNomRecurso']) || !count($arParametros['arNomRecurso'])) {
            SistemaLegado::LiberaFrames(true,false);

            return SistemaLegado::exibeAviso('necessário especificar pelo menos um recurso', 'n_' . __FUNCTION__, 'erro');
        }

        if (!isset($arParametros['inCodPrograma']) || $arParametros['inCodPrograma'] == '') {
            SistemaLegado::LiberaFrames(true,false);

            return SistemaLegado::exibeAviso('Preencha o campo Programa', 'n_' . __FUNCTION__, 'erro');
        }

        if ($_REQUEST['inCodTipoAcao'] == 1) {
            if ($_REQUEST['slNaturezaDespesa'] == '') {
                SistemaLegado::LiberaFrames(true,false);

                return SistemaLegado::exibeAviso('Preencha o campo natureza da despesa!', 'form', 'erro');
            }
            if ($_REQUEST['inCodFuncao'] == '') {
                SistemaLegado::LiberaFrames(true,false);

                return SistemaLegado::exibeAviso('Preencha o campo função!', 'form', 'erro');
            }
            if ($_REQUEST['inCodSubFuncao'] == '') {
                SistemaLegado::LiberaFrames(true,false);

                return SistemaLegado::exibeAviso('Preencha o campo subfunção!', 'form', 'erro');
            }
        }

        if (isset($arParametros['stDataInicial'])) {
            if ($arParametros['stDataInicial'] == '' OR $arParametros['stDataFinal'] == '') {
                SistemaLegado::LiberaFrames(true,false);

                return SistemaLegado::exibeAviso('É necessário informar o período!', 'form', 'erro');
            }
        }

        $obErro = $this->obCtrl->alterar($arParametros);

        if ($obErro->ocorreu()) {
            SistemaLegado::LiberaFrames(true,false);
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()), 'n_' . __FUNCTION__, 'erro');
        } else {
            SistemaLegado::LiberaFrames(true,false);
            SistemaLegado::alertaAviso('LSManterAcao.php', $arParametros['inNumAcao'], __FUNCTION__, 'aviso', Sessao::getID(), '../');
        }
    }

    /**
     * Executa ação recebida na página de processamento (PR).
     */
    public function executarAcao(array $arParametros)
    {
        $stMetodo = $arParametros['stAcao'];
        $this->inCodAcao = $arParametros['inCodAcao'];

        if (is_string($stMetodo)) {
            echo $this->$stMetodo($arParametros);
        }
    }

    /////////////////////////////////////
    //
    //
    // comeco da implementacao correta
    //
    //
    /////////////////////////////////////

    public function preencheMacroObjetivo(array $arParam, $boLimpa = true)
    {
        $inCodPPA = $arParam['inCodPPA'];
        //limpa os combos
        $stJs  = "jq('#inCodMacroObjetivo').removeOption(/./);";
        $stJs .= "jq('#inCodProgramaSetorial').removeOption(/./);";
        $stJs .= "jq('#inCodMacroObjetivoTxt').val('');";
        $stJs .= "jq('#inCodProgramaSetorialTxt').val('');";
        $stJs .= "jq('#inCodPrograma').val('');";
        $stJs .= "jq('#stNomPrograma').html('&nbsp;');";
        $stJs .= "var arOption = { '' : 'Selecione', ";

        if ($inCodPPA != '') {
            $obTTPPAMacroObjetivo = new TPPAMacroObjetivo;
            $obTTPPAMacroObjetivo->recuperaTodos($rsMacroObjetivo,' WHERE cod_ppa = ' . $inCodPPA . ' ');

            while (!$rsMacroObjetivo->eof()) {
                $stJs .= " '" . $rsMacroObjetivo->getCampo('cod_macro') . "' : '" . $rsMacroObjetivo->getCampo('descricao') . "', ";

                $rsMacroObjetivo->proximo();
            }
        }

        $stJs .= "};";
        $stJs .= "jq('#inCodMacroObjetivo').addOption(arOption,false);";

        return $stJs;
    }

    public function preencheProgramaSetorial(array $arParam, $boLimpaCampos = true)
    {
        $inCodMacro = ($arParam['inCodMacroObjetivo'] == '') ? $arParam['inCodMacroObjetivoTxt'] : $arParam['inCodMacroObjetivo'];
        //limpa os combos
        $stJs  = "jq('#inCodProgramaSetorial').removeOption(/./);";
        $stJs .= "jq('#inCodProgramaSetorialTxt').val('');";
        if ($boLimpaCampos) {
            $stJs .= "jq('#inCodPrograma').val('');";
            $stJs .= "jq('#stNomPrograma').html('&nbsp;');";
        }
        $stJs .= "var arOption = { '' : 'Selecione', ";
        if ($inCodMacro != '') {
            $obTTPPAProgramaSetorial = new TPPAProgramaSetorial;
            $obTTPPAProgramaSetorial->recuperaTodos($rsProgramaSetorial,' WHERE cod_macro = ' . $inCodMacro . ' ');

            while (!$rsProgramaSetorial->eof()) {
                $stJs .= " '" . $rsProgramaSetorial->getCampo('cod_setorial') . "' : '" . $rsProgramaSetorial->getCampo('descricao') . "', ";

                $rsProgramaSetorial->proximo();
            }
        }

        $stJs .= "};";
        $stJs .= "jq('#inCodProgramaSetorial').addOption(arOption,false);";

        return $stJs;
    }

    public function preenchePrograma(array $param)
    {
        $stJs .= "jq('#stNomPrograma').html('&nbsp;');";
        if ($param['inCodPrograma'] != '') {
            if ($param['inCodPPA'] != '') {
                $obRPPAManterPrograma  = new RPPAManterPrograma();
                $obVPPAManterPrograma  = new VPPAManterPrograma($obRPPAManterPrograma);

                $param['inNumPrograma'] = $param['inCodPrograma'];
                $param['inCodPPA'] = $param['inCodPPA'];
                unset($param['inCodPrograma']);

                $rsPrograma = $obVPPAManterPrograma->buscaPrograma($param, false);

                if ($rsPrograma->inNumLinhas > 0) {
                    $stIdentificacao = str_replace(array("\n", "\r"), ' ', $rsPrograma->getCampo('identificacao'));

                    $param['inCodPrograma'] = str_pad($param['inCodPrograma'],4,0,STR_PAD_LEFT);

                    $stJs .= "jq('#stNomPrograma').html('".addslashes($stIdentificacao)."');\n";
                    $stJs .= "jq('#hdnInCodPrograma').val('".$rsPrograma->getCampo('cod_programa')."');\n";

                    $param['inCodPPA'] = $rsPrograma->getCampo('cod_ppa');

                    $stJs .= "montaParametrosGET('preenchePeriodo');";
                } else {
                    $stJs .= "jq('#inCodPrograma').val('');";
                    $stJs .= "alertaAviso('Programa inválido','form','erro','".Sessao::getId()."');";
                }
            } else {
                $stJs .= "jq('#inCodPPA').focus();";
                $stJs .= "alertaAviso('Selecione um PPA','form','erro','".Sessao::getId()."');";
            }
        }

        return $stJs;
    }

    public function preencheProduto(array $arParam)
    {
        if ($arParam['inCodProduto'] != '') {
            $obRPPAProduto = new RPPAManterProduto;
            $rsProduto = $obRPPAProduto->getListaProdutos($arParam['inCodProduto']);

            if ($rsProduto->getNumLinhas() > 0) {
                $obForm = new Form;

                $obFormulario = new Formulario;
                $obFormulario->setForm($obForm);

                $obLblEspecificacao = new Label;
                $obLblEspecificacao->setRotulo('Especificação do Produto');
                $obLblEspecificacao->setValue($rsProduto->getCampo('especificacao'));

                $obFormulario->addComponente($obLblEspecificacao);
                $obFormulario->montaInnerHTML();

                $stForm = $obFormulario->getHTML();
            }
        }

        $stJs = "jq('#spnProduto').html('".$stForm."');";

        return $stJs;
    }

    public function montaMetaFisica(array $arParam)
    {
        /* Recupera os dados inseridos pelo usuário na tabela de metas dentro da tabletree dos recursos
         * serve para recuprar os dados que o usuário possivelmente digitou e fez uma inserção ou exclusão de recurso
         * como a tabletree é reescrita, perderia os valores dos inputs, então esses valores são guardados aqui para poderem ser colocados
         * noavamente */
        $arParametrosMetas = Sessao::read('arParametrosMetas');

        if ($arParam['inCodAcao'] && $arParam['tsAcaoDados']) {
            $rsQuantidade = $this->recuperaQuantidades(array('inCodAcao'=> $arParam['inCodAcao'], 'tsAcaoDados' => $arParam['tsAcaoDados']));
            $arQuantidade = $rsQuantidade->arElementos;
        }

        //Cria um recordSet com os 4 anos
        $rsRecordSet = new RecordSet;
        for ($i = 0; $i < 4; $i++) {
            $flQuantidade = (is_array($arQuantidade)) ? $arQuantidade[$i]['quantidade'] : '0';

            // Realiza a verificação se o valor da quantidade nesta posição está preenchido para poder recuperá-lo, assim substituindo o possivel
            // valor que já possua na base
            if ($arParametrosMetas['flQuantidade_'.($i+1).'_'.$arParam['cod_recurso']] != '') {
                $flQuantidade = $arParametrosMetas['flQuantidade_'.($i+1).'_'.$arParam['cod_recurso']];
            }

            // Realiza a verificação se o 'valor do valor'(hehehe) nesta posição está preenchido para poder recuperá-lo, assim substituindo o possivel
            // valor que já possua na base
            if ($arParametrosMetas['flValorTotal_'.($i+1).'_'.$arParam['cod_recurso']] != '') {
                $flValor = $arParametrosMetas['flValorTotal_'.($i+1).'_'.$arParam['cod_recurso']];
            }

            $rsRecordSet->add(array('ano'        => ($i+1),
                                    'descricao'  => '<strong>Ano ' . ($i + 1) . ' do PPA</strong>',
                                    'quantidade' => number_format($flQuantidade,2,',','.'),
                                    'total'      => number_format($flValor,2,',','.'),
                                   )
                             );
            $flQuantidadeTotal += $flQuantidade;
            $flTotalizador += $flValor;
        }
        $rsRecordSet->add(array('ano'        => 'total',
                                'descricao'  => '<strong>Total</strong>',
                                'quantidade' => number_format($flQuantidadeTotal,2,',','.'),
                                'total'      => number_format($flTotalizador,2,',','.')
                                )
                         );

        $obTxtQuantidade = new Numerico;
        $obTxtQuantidade->setId('flQuantidade_[ano]_'.$arParam['cod_recurso']);
        $obTxtQuantidade->setName('flQuantidade_[ano]_'.$arParam['cod_recurso']);
        $obTxtQuantidade->setLabel(true);
        $obTxtQuantidade->setClass('valor');
        $obTxtQuantidade->setValue('[quantidade]');
        $obTxtQuantidade->setMaxLength(14);
        $obTxtQuantidade->setSize(14);
        $obTxtQuantidade->setStyle('text-align:right');
        $obTxtQuantidade->setNegativo(false);
        $obTxtQuantidade->obEvento->setOnChange("somaValorMetaFisica(this);");

        $obTxtValorTotal = new Numerico;
        $obTxtValorTotal->setId('flValorTotal_[ano]_'.$arParam['cod_recurso']);
        $obTxtValorTotal->setName('flValorTotal_[ano]_'.$arParam['cod_recurso']);
        $obTxtValorTotal->setLabel(true);
        $obTxtValorTotal->setClass('valor');
        $obTxtValorTotal->setValue('[total]');
        $obTxtValorTotal->setMaxLength(14);
        $obTxtValorTotal->setSize(14);

        $obTable = new Table;
        $obTable->setId('tblMetaFisica_'.$arParam['cod_recurso']);
        $obTable->setRecordset      ($rsRecordSet);
        $obTable->setTitle          ('Metas Físicas');
        $obTable->setSummary        ('Metas Físicas');
        $obTable->setLineNumber     (false);

        $obTable->Head->addCabecalho('&nbsp;&nbsp;'  , 57);
        $obTable->Head->addCabecalho('Quantidade'    , 13);
        $obTable->Head->addCabecalho('Valor Total'   , 13);

        $obTable->Body->addCampo    ('descricao'     , 'C');
        $obTable->Body->addCampo    ($obTxtQuantidade, 'R');
        $obTable->Body->addCampo    ($obTxtValorTotal, 'R');

        $obTable->montaHTML();

        return $obTable->getHtml()."<script type='text/javascript> montaMetaFisica('".$arParam['cod_recurso']."'); </script>";

    }

    public function montaListaUnidade($arUnidade,$boJs = true)
    {
        $rsRecordSet = new RecordSet;
        $rsRecordSet->preenche($arUnidade);

        $obTable = new Table;
        $obTable->setId('tblUnidadeExecutora');
        $obTable->setRecordset      ($rsRecordSet);
        $obTable->setTitle          ('Unidade Executora');
        $obTable->setStyle('background-color:#fff;');
        $obTable->setSummary        ('Unidade Executora');

        $obTable->Head->addCabecalho('Código'    , 5);
        $obTable->Head->addCabecalho('Descrição' , 80);

        $obTable->Body->addCampo    ('unidade'       , 'C');
        $obTable->Body->addCampo    ('descricao'     , 'L');

        $obTable->Body->addAcao     ('excluir',"executaFuncaoAjax('%s', '&stUnidadeOrcamentaria=%s')", array('excluirUnidade','unidade'));

        $obTable->montaHTML(true);

        return $obTable->getHTML();
    }

    public function incluirUnidade($arParam)
    {
        $arUnidade = (array) Sessao::read('arUnidade');
        if ($arParam['stUnidadeOrcamentaria'] != '') {
            foreach ($arUnidade as $arTemp) {
                if ($arTemp['unidade'] == $arParam['stUnidadeOrcamentaria']) {
                    return 'alertaAviso("Esta unidade executora já está na lista!", "form", "erro", "' . Sessao::getID() . '");';
                }
            }

            $arAux = explode('.',$arParam['stUnidadeOrcamentaria']);

            $obTOrcamentoUnidade = new TOrcamentoUnidade;
            $obTOrcamentoUnidade->setDado('exercicio'  ,Sessao::getExercicio());
            $obTOrcamentoUnidade->setDado('num_orgao'  ,$arAux[0]);
            $obTOrcamentoUnidade->setDado('num_unidade',$arAux[1]);
            $obTOrcamentoUnidade->recuperaPorChave($rsUnidade);

            $arUnidade[] = array('unidade'   => $arParam['stUnidadeOrcamentaria'],
                                 'descricao' => $rsUnidade->getCampo('nom_unidade'),
                           );

            Sessao::write('arUnidade',$arUnidade);
            $stJs .= "jq('#spnUnidade').html('" . $this->montaListaUnidade($arUnidade) . "');";
            $stJs .= "limparUnidade();";

            return $stJs;
        } else {
            return 'alertaAviso("Informe uma unidade executora!", "form", "erro", "' . Sessao::getID() . '");';
        }
    }

    public function excluirUnidade($arParam)
    {
        $arUnidade = Sessao::read('arUnidade');

        foreach ($arUnidade as $arTemp) {
            if ($arTemp['unidade'] != $arParam['stUnidadeOrcamentaria']) {
                $arUnidadeNew[] = $arTemp;
            }
        }

        Sessao::write('arUnidade', (array) $arUnidadeNew);

        return "jq('#spnUnidade').html('" . $this->montaListaUnidade((array) $arUnidadeNew) . "');";
    }

    public function preencheTipoAcao($arParam)
    {
        $stJs .= "jq('#inCodTipo').removeOption(/./);";
        $stJs .= "var arOption = { '' : 'Selecione', ";
        $obTPPATipoAcao = new TPPATipoAcao;
        if ($arParam['inCodTipoAcao'] == 1) {
            $stFiltro = ' WHERE cod_tipo BETWEEN 1 AND 3 ';
        } else {
            $stFiltro = ' WHERE cod_tipo BETWEEN 4 AND 8 ';
        }
        $obTPPATipoAcao->recuperaTodos($rsTipoAcao,$stFiltro);

        while (!$rsTipoAcao->eof()) {
            $stJs .= "'".$rsTipoAcao->getCampo('cod_tipo')."':'".$rsTipoAcao->getCampo('descricao')."',";

            $rsTipoAcao->proximo();
        }

        $stJs .= "};";
        $stJs .= "jq('#inCodTipo').addOption(arOption);";
        $stJs .= "jq('#inCodTipo').selectOptions('',true);";
        if ($arParam['inCodPPA'] != '') {
            $stJs .= "jq('#spnPeriodo').html('" . $this->preenchePeriodo($arParam,false) . "');";
        }

        return $stJs;
    }

    public function preencheSpanOrcamentaria($arParam, $boJs = true)
    {
        $obFormulario = new Formulario;
        $obFormulario->addForm(new Form);

        if ($arParam['inCodTipoAcao'] != 2) {
            $obRdNaturezaDespesaCorrente = new Radio;
            $obRdNaturezaDespesaCorrente->setName   ('slNaturezaDespesa');
            $obRdNaturezaDespesaCorrente->setId     ('slNaturezaDespesaCorrente');
            $obRdNaturezaDespesaCorrente->setRotulo ('Natureza da Despesa');
            $obRdNaturezaDespesaCorrente->setTitle  ('Selecione a natureza da despesa.');
            $obRdNaturezaDespesaCorrente->setLabel  ('Corrente');
            $obRdNaturezaDespesaCorrente->setValue  (1);
            $obRdNaturezaDespesaCorrente->setNull   (false);
            if ($arParam['inCodNatureza'] == 1) {
               $obRdNaturezaDespesaCorrente->setChecked(true);
            }

            $obRdNaturezaDespesaCapital = new Radio;
            $obRdNaturezaDespesaCapital->setName   ('slNaturezaDespesa');
            $obRdNaturezaDespesaCapital->setId     ('slNaturezaDespesaCapital');
            $obRdNaturezaDespesaCapital->setRotulo ('Natureza da Despesa');
            $obRdNaturezaDespesaCapital->setTitle  ('Selecione a natureza da despesa.');
            $obRdNaturezaDespesaCapital->setLabel  ('Capital');
            $obRdNaturezaDespesaCapital->setValue  (2);
            $obRdNaturezaDespesaCapital->setNull   (false);
            if ($arParam['inCodNatureza'] == 2) {
               $obRdNaturezaDespesaCapital->setChecked(true);
            }

            $obISelectFuncao = new ISelectFuncao;
            $obISelectFuncao->setValue($arParam['inCodFuncao']);
            $obISelectFuncao->setNull(false);

            $obISelectSubFuncao = new ISelectSubFuncao;
            $obISelectSubFuncao->setValue($arParam['inCodSubFuncao']);
            $obISelectSubFuncao->setNull(false);

            $obFormulario->agrupaComponentes            (array($obRdNaturezaDespesaCorrente,$obRdNaturezaDespesaCapital));
            $obFormulario->addComponente                ($obISelectFuncao);
            $obFormulario->addComponente                ($obISelectSubFuncao);

        }
        $obFormulario->montaInnerHTML(true);

        if ($boJs) {
            return "jq('#spnOrcamentaria').html('".$obFormulario->getHTML()."');";
        } else {
            return $obFormulario->getHTML();
        }
    }

    public function preenchePeriodo($arParam, $boJs = true)
    {
        $inCodPrograma = $arParam['inCodPrograma'];
        $arParametroPeriodo = Sessao::read('arParametroPeriodo');
        if (is_array($arParametroPeriodo)) {
            $arParam = $arParametroPeriodo;
        }

        $arParam['inCodPrograma'] = $inCodPrograma;
        if ($arParam['inCodPrograma'] != '') {
            $obRPPAManterPrograma  = new RPPAManterPrograma();
            $obVPPAManterPrograma  = new VPPAManterPrograma($obRPPAManterPrograma);

            $rsPrograma = $obVPPAManterPrograma->buscaPrograma($arParam, false);

            $obForm = new Form;
            $obFormulario = new Formulario;
            $obFormulario->addForm($obForm);

            if ($arParam['inCodTipo'] == 1 AND $rsPrograma->getCampo('continuo') == 'Temporário') {
                $obPeriodo = new Periodo;
                $obPeriodo->setRotulo('Período');
                $obPeriodo->setTitle('Informe o período.');
                $obPeriodo->setNull(false);
                $obPeriodo->obDataInicial->obEvento->setOnChange("montaParametrosGET('validaPeriodo','stDataInicial,stDataFinal,inCodPrograma');");
                $obPeriodo->obDataInicial->setId($obPeriodo->obDataInicial->getName());
                $obPeriodo->obDataInicial->setValue($arParam['stDataInicial']);
                $obPeriodo->obDataFinal->obEvento->setOnChange("montaParametrosGET('validaPeriodo','stDataInicial,stDataFinal,inCodPrograma');");
                $obPeriodo->obDataFinal->setId($obPeriodo->obDataFinal->getName());
                $obPeriodo->obDataFinal->setValue($arParam['stDataFinal']);

                $obTxtValorEstimado = new Numerico;
                $obTxtValorEstimado->setName    ('flValorEstimado');
                $obTxtValorEstimado->setId      ('flValorEstimado');
                $obTxtValorEstimado->setRotulo  ('Valor Estimado');
                $obTxtValorEstimado->setTitle   ('Informe o valor estimado.');
                $obTxtValorEstimado->setNull    (false);
                $obTxtValorEstimado->setMaxLength(14);
                $obTxtValorEstimado->setNegativo(false);
                $obTxtValorEstimado->setValue   (($arParam['flValorEstimado'] == '') ? '0,00' : number_format($arParam['flValorEstimado'],2,',','.'));

                $obTxtValorMetaEstimada = new Numerico;
                $obTxtValorMetaEstimada->setName    ('flValorMetaEstimada');
                $obTxtValorMetaEstimada->setId      ('flValorMetaEstimada');
                $obTxtValorMetaEstimada->setRotulo  ('Meta Estimada');
                $obTxtValorMetaEstimada->setTitle   ('Informe a meta estimada.');
                $obTxtValorMetaEstimada->setNull    (false);
                $obTxtValorMetaEstimada->setMaxLength(14);
                $obTxtValorMetaEstimada->setNegativo(false);
                $obTxtValorMetaEstimada->setValue   (($arParam['flMetaEstimada'] == '') ? '0,00' : number_format($arParam['flMetaEstimada'],2,',','.'));

                $obFormulario->addComponente($obPeriodo);
                $obFormulario->addComponente($obTxtValorEstimado);
                $obFormulario->addComponente($obTxtValorMetaEstimada);

            }

            if (!$boJs) {
                $obFormulario->montaHTML();

                return $obFormulario->getHTML();
            } else {
                $obFormulario->montaInnerHTML();
                if ($rsPrograma->getCampo('cod_tipo_programa') == 4) {
                    $stLblUnidade = 'Unidade de Medida (U.M.)';
                    $stLblProduto = 'Produto';
                } else {
                    $stLblUnidade = '*Unidade de Medida (U.M.)';
                    $stLblProduto = '*Produto';
                }
                $stJs  = " var obHtml = jq('#stUnidadeMedida').parent().parent(); ";
                $stJs .= "jq('td.label',obHtml).html('" . $stLblUnidade . "'); ";
                $stJs .= "var obHtml = jq('#inCodProduto').parent().parent().parent().parent().parent().parent();";
                $stJs .= "jq('td.label',obHtml).html('" . $stLblProduto . "');";
                $stJs .= "jq('#spnPeriodo').html('" . $obFormulario->getHTML() . "');";

                return $stJs;
            }
        }
    }

    public function validaPeriodo($arParam)
    {
        if ($arParam['stDataInicial'] != '' AND $arParam['stDataFinal'] != '') {
            $stDtInicial = implode('',array_reverse(explode('/',$arParam['stDataInicial'])));
            $stDtFinal   = implode('',array_reverse(explode('/',$arParam['stDataFinal'])));

            if ($stDtInicial > $stDtFinal) {
                $stJs .= "jq('#stDataInicial').val('');";
                $stJs .= "jq('#stDataFinal').val('');";
                $stJs .= 'alertaAviso("Período informado inválido!", "form", "erro", "' . Sessao::getID() . '");';

                return $stJs;
            }

            $obRPPAManterPrograma  = new RPPAManterPrograma();
            $obVPPAManterPrograma  = new VPPAManterPrograma($obRPPAManterPrograma);

            $rsPrograma = $obVPPAManterPrograma->buscaPrograma($arParam, false);
            $stDtInicialProg = implode('',array_reverse(explode('/',$rsPrograma->getCampo('dt_inicial'))));
            $stDtFinalProg   = implode('',array_reverse(explode('/',$rsPrograma->getCampo('dt_final'))));

            if (!($stDtInicial >= $stDtInicialProg AND $stDtFinal <= $stDtFinalProg)) {
                $stJs .= "jq('#stDataInicial').val('');";
                $stJs .= "jq('#stDataFinal').val('');";
                $stJs .= 'alertaAviso("O período do Projeto deve estar dentro do período de vigência do Programa!", "form", "erro", "' . Sessao::getID() . '");';
            }

        }

        return $stJs;
    }

    ////////////////////////////////////////
    //
    //
    // fim da implementacao correta
    //
    //
    ////////////////////////////////////////

    public function mostrarNorma()
    {
        $obFormulario = new Formulario();

        # Monta popup de Norma caso PPA tenha sido homologado.
        $obIPopUpNorma = new IPopUpNorma();
        $obIPopUpNorma->obInnerNorma->setTitle('Define norma de inclusão.');
        $obIPopUpNorma->obInnerNorma->obCampoCod->stId = 'inCodNorma';
        $obIPopUpNorma->obInnerNorma->setRotulo("Número da Norma");
        $obIPopUpNorma->obLblDataNorma->setRotulo( "Data da Norma" );
        $obIPopUpNorma->obLblDataPublicacao->setRotulo( "Data da Publicação" );
        $obIPopUpNorma->setExibeDataNorma(true);
        $obIPopUpNorma->setExibeDataPublicacao(true);
        $obIPopUpNorma->geraFormulario($obFormulario);

        $obFormulario->montaInnerHTML();
        $stHTML = $obFormulario->getHTML();

        return "$('spnNorma').innerHTML = '" . $stHTML . "';";
    }

    public function ocultarNorma()
    {
        return "$('spnNorma').innerHTML = '';";
    }

    /**
     * Liga ou desliga o componente de norma da tela.
     */
    public function atualizarNorma(array $arParametros)
    {
        $obRPPAManterPrograma = new RPPAManterPrograma();
        $obVPPAManterPrograma = new VPPAManterPrograma($obRPPAManterPrograma);
        $inCodPPA             = (int) $arParametros['inCodPPA'];
        $boHomologado = false;

        # Verifica se o PPA escolhido já foi homologado.
        if ($inCodPPA) {
            $boHomologado = $obVPPAManterPrograma->verificaHomologacao($inCodPPA);
        }

        if ($boHomologado) {
            return $this->mostrarNorma();
        } else {
            return $this->ocultarNorma();
        }
    }

    private function limparDadosPrograma()
    {
        $stJS = '';

        # Atualiza interface.
        //$stJS .= '$("stTotalReceita").innerHTML = "&nbsp;";';
        //$stJS .= '$("stTotalPPA").innerHTML = "&nbsp;";';
        //$stJS .= '$("stTotalPrograma").innerHTML = "&nbsp;";';
        //$stJS .= '$("stTotalAcumulado").innerHTML = "&nbsp;";';
        //$stJS .= '$("stTotalAcao").innerHTML = "&nbsp;";';
        return $stJS;
    }

    private function limparDadosAcao()
    {
        # Atualiza interface.
        $stJS  = '$("inCodAcao").value = "";';

        return $stJS;
    }

    public function atualizarDadosPPA(array $arParametros)
    {
        $inCodPPA = $arParametros['inCodPPA'];
        $stJS              = '';
        $stTotalReceita    = '&nbsp;';
        $stTotalPPA        = '&nbsp;';
        $stTotalAcumulado  = '&nbsp;';

        # Calcula dados do PPA.
        if ($inCodPPA) {
            $flTotalPPA        = '';//$this->obCtrl->recuperaTotalPPA($inCodPPA);
            $flSubtotalPPA     = $flTotalPPA - $arParametros['flTotalAcao'];
            //$flTotalReceita    = $this->obCtrl->recuperaTotalReceitas($inCodPPA, '', Sessao::getExercicio());
            $flSubtotalReceita = $flTotalReceita - $flSubtotalPPA;
            $flTotalAcumulado  = $flTotalReceita - $flTotalPPA;

            # Exibição na tela.
            //$stTotalReceita    = $this->floatToStr($flTotalReceita);
            $stTotalPPA        = $this->floatToStr($flTotalPPA);
            $stTotalAcumulado  = $this->floatToStr($flTotalAcumulado);
        }

        # Atualiza interface.
        //$stJS .= "$('stTotalReceita').innerHTML = '"   . $stTotalReceita    . "';";
        //$stJS .= "$('stTotalPPA').innerHTML = '"       . $stTotalPPA        . "';";
        //$stJS .= "$('stTotalAcumulado').innerHTML = '" . $stTotalAcumulado  . "';";
        return $stJS;
    }

    /**
     * Atualiza dados da interface quando o usuário escolhe um programa no componente.
     */
    public function atualizarDadosPrograma(array $arParametros)
    {
        $inCodPPA          = $arParametros['inCodPPA'];
        $inNumPrograma     = $arParametros['inNumPrograma'];
        $stJS              = '';
        $stTotalPrograma   = '&nbsp;';

        # Obtem dados do Programa
        if ($inCodPPA && $inNumPrograma) {
            $obRPPAManterPrograma = new RPPAManterPrograma();
            $stFiltro = '';

            # Obtem código do programa e do PPA.
            $stFiltro .= 'p.cod_ppa = ' . $inCodPPA . ' AND ';
            $stFiltro .= "p.num_programa = '" . $inNumPrograma . "' AND ativo = 't'";
            $rsPrograma = $obRPPAManterPrograma->recuperaPrograma($stFiltro, 'recuperaPrograma');

            if (!$rsPrograma->eof()) {
                $inCodPrograma = (int) $rsPrograma->getCampo('cod_programa');
                $flTotalPrograma = $this->obCtrl->recuperaTotalPrograma($inCodPPA, $inCodPrograma);
                $stTotalPrograma = $this->floatToStr($flTotalPrograma);
            }
        }

        # Atualiza interface.
        //$stJS .= "$('stTotalPrograma').innerHTML = '"  . $stTotalPrograma   . "';";
        return $stJS;
    }

    public function atualizarPrograma(array $arParametros)
    {
        $stJS .= $this->atualizarRecurso($arParametros);
        $stJS .= $this->atualizarDadosPrograma($arParametros);
        $stJS .= $this->atualizarAcao($arParametros);

        return $stJS;
    }

    public function recuperaAcao(array $arParametros)
    {
        return $this->obCtrl->recuperaAcao($arParametros['inCodAcao']);
    }

    public function recuperaQuantidades(array $arParametros)
    {
        return $this->obCtrl->recuperaQuantidades($arParametros['inCodAcao'], $arParametros['tsAcaoDados']);
    }

    public function recuperaRecursos(array $arParametros)
    {
        $rsRecursos = $this->obCtrl->recuperaRecursos($arParametros['inCodAcao'], $arParametros['tsAcaoDados'], '', $arParametros['exercicio'], '');

        # Mostra recursos formatados.
        while (!$rsRecursos->eof()) {
            $flAno1  = $this->floatToStr($rsRecursos->getCampo('ano1'));
            $flAno2  = $this->floatToStr($rsRecursos->getCampo('ano2'));
            $flAno3  = $this->floatToStr($rsRecursos->getCampo('ano3'));
            $flAno4  = $this->floatToStr($rsRecursos->getCampo('ano4'));
            $flTotal = $this->floatToStr($rsRecursos->getCampo('total'));

            $rsRecursos->setCampo('ano1', $flAno1);
            $rsRecursos->setCampo('ano2', $flAno2);
            $rsRecursos->setCampo('ano3', $flAno3);
            $rsRecursos->setCampo('ano4', $flAno4);
            $rsRecursos->setCampo('total', $flTotal);

            $rsRecursos->proximo();
        }

        $rsRecursos->setPrimeiroElemento();

        return $rsRecursos;
    }

    public function buscarMetasFisicas(array $arRecursos)
    {
        $arCodRecurso = array();
        foreach ($arRecursos as $arDados) {
            $arCodRecurso['codigo'][] = $arDados['cod_recurso'];
            $arCodRecurso['exercicio'][] = "'".$arDados['exercicio_recurso']."'";
        }
        $arCodRecurso['exercicio'] = array_unique($arCodRecurso['exercicio']);

        $arConfig = array(
                'inCodAcao'           => $arRecursos[0]['cod_acao']
            ,   'tsAcaoDados'         => $arRecursos[0]['timestamp_acao_dados']
            ,   'arCodRecurso'        => $arCodRecurso['codigo']
            ,   'arExercicioRecurso'  => $arCodRecurso['exercicio']
        );

        $rsMetas = $this->obCtrl->buscarMetasFisicas($arConfig);

        //inicia os valores para poder fazer o somatorio de cada ano
        $arAnoValores = array(1=>0, 2=>0, 3=>0, 4=>0);
        $flTotalValores = 0;

        while (!$rsMetas->eof()) {
            $inCodRecurso = $rsMetas->getCampo('cod_recurso');
            for ($i = 1; $i<= 4; $i++) {
                $arParametrosMetas['flQuantidade_'.$i.'_'.$inCodRecurso] = $rsMetas->getCampo('ano'.$i);
                $arParametrosMetas['flValorTotal_'.$i.'_'.$inCodRecurso] = $rsMetas->getCampo('ano'.$i.'_valor');
                $flTotalValores   += $rsMetas->getCampo('ano'.$i.'_valor');
            }
            $arParametrosMetas['flQuantidade_total_'.$inCodRecurso] = $rsMetas->getCampo('total');
            $arParametrosMetas['flValorTotal_total_'.$inCodRecurso] = $flTotalValores;

            $flTotalValores = 0;
            $rsMetas->proximo();
        }

        Sessao::write('arParametrosMetas', $arParametrosMetas);
    }

    public function recuperaProduto(array $arParametros)
    {
        $rsQuantidades = $this->obCtrl->recuperaQuantidades($arParametros['inCodAcao'], $arParametros['tsAcaoDados']);

        $arProduto = array();

        foreach ($rsQuantidades->getElementos() as $arCampos) {
            $inAno = $arCampos['ano'];
            $arProduto[0]["quantidade_ano_$inAno"] = $this->floatToStr($arCampos['quantidade']);
        }

        $arProduto[0]['sequencia']   = $arParametros['inCodProduto'];
        $arProduto[0]['descricao']   = $arParametros['stDscProduto'];
        $arProduto[0]['dsc_unidade'] = $arParametros['stDscUnidade'];

        return $arProduto;
    }

    public function excluir(array $arParametros)
    {
        $this->formataDados($arParametros);
        $obErro = $this->obCtrl->excluir($arParametros);

        $stCaminho = 'LSManterAcao.php?stAcao=excluir';
        if ($obErro->ocorreu()) {
            SistemaLegado::alertaAviso($stCaminho, $obErro->getDescricao(), 'n_incluir', 'erro', Sessao::getID(), '../');
        } else {
            SistemaLegado::alertaAviso($stCaminho, $arParametros['inNumAcao'], __FUNCTION__, 'aviso', Sessao::getID(), '../');
        }
    }

    public function listaAcao($param)
    {
        return $this->obCtrl->buscaAcao($param);
    }

    /**
     * Verifica se um PPA está homologado
     * Retorna true se o PPA estiver homologado
     *
     * @param  int  $inCodPPA
     * @return bool
     */
    public function isPPAHomologado($inCodPPA)
    {
        return $this->obCtrl->isPPAHomologado($inCodPPA);
    }

    /* Mostra componente IPopUpRecurso com os parâmetros corretos.
     * @param  array  $arParametros parametros recebidos do formulário
     * @return string código javascript
     */
    public function mostrarRecurso($arParametros)
    {
        $inCodPPA      = (int) $arParametros['inCodPPA'];
        $boDestinacao  = false;

        if ($inCodPPA) {
            $boDestinacao = $this->obCtrl->pesquisaDestinacao($inCodPPA);
        }

        $obForm = new Form();
        $obFormulario = new Formulario();

        // Define dados de recurso
        $obFormulario->addTitulo('Dados para Cadastro de Fontes de Recurso');

        //Define o componente IMontaRecursoDestinacao
        $obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
        $obIMontaRecursoDestinacao->setNull( false );
        $obIMontaRecursoDestinacao->geraFormulario($obFormulario);

        // Define valor ano 1 (obrigatório)
        $obTxtRecursoAno1 = new Numerico();
        $obTxtRecursoAno1->setName('flValorAno1');
        $obTxtRecursoAno1->setID('flValorAno1');
        $obTxtRecursoAno1->setValue('0,00');
        $obTxtRecursoAno1->setRotulo('Valor Ano 1');
        $obTxtRecursoAno1->setTitle('Valor Ano 1');
        $obTxtRecursoAno1->setNegativo(false);
        $obTxtRecursoAno1->setMaxLength(14);
        $obFormulario->addComponente($obTxtRecursoAno1);

        // Define valor ano 2 (opcional)
        $obTxtRecursoAno2 = new Numerico();
        $obTxtRecursoAno2->setName('flValorAno2');
        $obTxtRecursoAno2->setID('flValorAno2');
        $obTxtRecursoAno2->setValue('0,00');
        $obTxtRecursoAno2->setRotulo('Valor Ano 2');
        $obTxtRecursoAno2->setTitle('Valor Ano 2');
        $obTxtRecursoAno2->setNegativo(false);
        $obTxtRecursoAno2->setMaxLength(14);
        $obFormulario->addComponente($obTxtRecursoAno2);

        // Define valor ano 3 (opcional)
        $obTxtRecursoAno3 = new Numerico();
        $obTxtRecursoAno3->setName('flValorAno3');
        $obTxtRecursoAno3->setID('flValorAno3');
        $obTxtRecursoAno3->setValue('0,00');
        $obTxtRecursoAno3->setRotulo('Valor Ano 3');
        $obTxtRecursoAno3->setTitle('Valor Ano 3');
        $obTxtRecursoAno3->setNegativo(false);
        $obTxtRecursoAno3->setMaxLength(14);
        $obFormulario->addComponente($obTxtRecursoAno3);

        // Define valor ano 4 (opcional)
        $obTxtRecursoAno4 = new Numerico();
        $obTxtRecursoAno4->setName('flValorAno4');
        $obTxtRecursoAno4->setID('flValorAno4');
        $obTxtRecursoAno4->setValue('0,00');
        $obTxtRecursoAno4->setRotulo('Valor Ano 4');
        $obTxtRecursoAno4->setTitle('Valor Ano 4');
        $obTxtRecursoAno4->setNegativo(false);
        $obTxtRecursoAno4->setMaxLength(14);
        $obFormulario->addComponente($obTxtRecursoAno4);

        // Define botoes de recurso.
        $obBtnIncluir = new Button();
        $obBtnIncluir->setName('btnIncluir');
        $obBtnIncluir->setValue('Incluir');
        $obBtnIncluir->obEvento->setOnClick('incluirRecurso()');

        $obBtnLimpar = new Button();
        $obBtnLimpar->setName('btnLimpar');
        $obBtnLimpar->setValue('Limpar');
        $obBtnLimpar->obEvento->setOnClick('limparRecurso()');

        $arButoes = array($obBtnIncluir, $obBtnLimpar);
        $obFormulario->defineBarra($arButoes, '', '');

        $obFormulario->montaInnerHTML();
        $stHTML = $obFormulario->getHTML();

        return "jq('#spnRecurso').html('".$stHTML."'); formataRecurso();";
    }

    public function ocultarRecurso()
    {
        return "jq('#spnRecurso').html('');";
    }

    public function atualizarRecurso($arParametros)
    {
        $stJS = '';

        if ($arParametros['inCodPrograma']) {
            return $stJS . $this->mostrarRecurso($arParametros);
        } else {
            return $stJS . $this->ocultarRecurso();
        }
    }

    public function ocultarPrograma()
    {
        return "jq('#spnPrograma').html('');";
    }

    public function atualizarPPA(array $arParametros)
    {
        $inCodPPA = $arParametros['inCodPPA'];
        $stJS = '';

        if ($inCodPPA) {
            $stJS .= $this->mostrarPrograma($arParametros, true);
        } else {
            $stJS .= $this->ocultarPrograma();
            $stJS .= $this->ocultarNorma();
        }

        $stJS .= $this->limparDadosAcao();
        $stJS .= $this->limparDadosPrograma();
        $stJS .= $this->atualizarNorma($arParametros);
        $stJS .= $this->atualizarRecurso($arParametros);
        $stJS .= $this->atualizarDadosPPA($arParametros);

        return $stJS;
    }

    public function atualizarAcao(array $arParametros)
    {
        $stJS = $this->buscaCodAcao($arParametros);

        return $stJS;
    }

    public function limparFormulario(array $arParametros)
    {
        $stJS  = $this->ocultarPrograma();
        $stJS .= $this->ocultarNorma();
        $stJS .= $this->limparDadosAcao();
        $stJS .= $this->limparDadosPrograma();

        if ($arParametros['inSizeRecurso']) {
            $stJS .= $this->limparListaRecurso($arParametros);
        }

        return $stJS;
    }

    public function atualizarQuantidade(array $arParametros)
    {
        $flTotalQuantidade = 0.0;

        for ($i = 1; $i < 5; $i++) {
            $flTotalQuantidade += $this->strToFloat($arParametros["flQuantidadeAno$i"]);
        }

        if (!$flTotalQuantidade) {
            return "$('stQuantidadeTotal').innerHTML = '&nbsp;';";
        } else {
            return "$('stQuantidadeTotal').innerHTML = '" . $this->floatToStr($flTotalQuantidade) . "';";
        }
    }

    public function atualizar(array $arParametros)
    {
        $stJS = '';
        $stJS .= $this->atualizarQuantidade($arParametros);
        $stJS .= $this->mostrarListaRecurso($arParametros);
        $stJS .= $this->atualizarDadosRecursos($arParametros);

        return $stJS;
    }

    public function limparSessaoParametrosMetas()
    {
        Sessao::remove('arParametrosMetas');
    }

    public function recuperaAcaoDespesa(array $arParametros)
    {
        return $this->obCtrl->recuperaAcaoDespesa($arParametros['inCodAcao'], $arParametros['inAno'], $arParametros['inCodRecurso']);
    }

    public function recuperaDadosRecursos(array $arParametros)
    {
        $rsRecursos = $this->obCtrl->recuperaDadosRecursos($arParametros['inCodAcao'], $arParametros['tsAcaoDados'], $arParametros['inCodRecurso'], $arParametros['inAno'], '');

        return $rsRecursos;
    }

    public function recuperaDadosRecursosDespesa(array $arParametros)
    {
        $rsRecursos = $this->obCtrl->recuperaDadosRecursosDespesa($arParametros['inCodAcao'], $arParametros['inCodRecurso'], $arParametros['inAno'], '');

        return $rsRecursos;
    }

    public function verificaArrendondarValor(array $arParametros)
    {
        $stValue = '';
        if ($arParametros['inCodPPA'] != '') {
            $stValue = $this->obCtrl->verificaArrendondarValor($arParametros['inCodPPA']);
        }

        return "jq('#boArredondar').val('".$stValue."')";
    }
}
