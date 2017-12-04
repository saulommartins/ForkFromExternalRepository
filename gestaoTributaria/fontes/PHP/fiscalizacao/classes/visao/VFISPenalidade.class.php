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
 * Classe de visão da Penalidade.
 * Data de Criação: 28/07/2008

 * @author Analista    : Heleno Menezes dos Santos
 * @author Programador : Pedro Vaz de Mello de Medeiros

 * @package URBEM
 * @subpackage Visao

 $Id: VFISPenalidade.class.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso:
 */

include_once CAM_GT_FIS_COMPONENTES . 'ITextBoxSelectTipoPenalidade.class.php';
include_once CAM_GT_MON_COMPONENTES . 'IPopUpIndicadorEconomico.class.php';
include_once CAM_GA_ADM_COMPONENTES . 'IPopUpFuncao.class.php';
include_once CAM_GA_ADM_MAPEAMENTO  . 'TAdministracaoModeloDocumento.class.php';
include_once CAM_GA_ADM_COMPONENTES . 'ISelectUnidadeMedida.class.php';
include_once ( CAM_GA_PROT_NEGOCIO."RProcesso.class.php" );

class VFISPenalidade
{
    private $obController;

    /**
     * Método construtor
     * @arParametros $obController objeto da regra de negócio
     */
    public function __construct($obController)
    {
        $this->obController = $obController;
    }

    /**
     * Monta javascript da parte do formulário que processa Penalidades do tipo Multa
     * @arParametros array arParametros array de parâmetros recebidos
     */
    public function montaFormularioMulta(array $arParametros)
    {
        $jsBuffer = '';

        # Lê código da multa
        if ($arParametros['inCodPenalidade']) {
            $rsPenalidadeMulta = $this->obController->getPenalidadeMulta($arParametros['inCodPenalidade']);
            $rsPenalidadeDesconto = $this->obController->getPenalidadeDesconto($arParametros['inCodPenalidade']);
        }

        # Define o formulario
        $obFormulario = new Formulario();

        # Lê indicador econômico da multa
        if (isset($rsPenalidadeMulta) && $rsPenalidadeMulta->inNumLinhas > 0 ) {
            $inCodIndicador = $rsPenalidadeMulta->getCampo('cod_indicador');
            $stCodFuncao = sprintf('%02d.%02d.%03d', $rsPenalidadeMulta->getCampo('cod_modulo'), $rsPenalidadeMulta->getCampo('cod_biblioteca'), $rsPenalidadeMulta->getCampo('cod_funcao'));
            $stCodUnidade = $rsPenalidadeMulta->getCampo('cod_unidade').'-'.$rsPenalidadeMulta->getCampo('cod_grandeza');
        }

        # Define indicador econômico
        $obIPopUpIndicadorEconomico = new IPopUpIndicadorEconomico();
        $obIPopUpIndicadorEconomico->setNull(false);
        $obIPopUpIndicadorEconomico->setRotulo('Indicador Econômico');
        $obIPopUpIndicadorEconomico->setTitle('Indicador Econômico utilizado para o cálculo da multa.');
        $obIPopUpIndicadorEconomico->obCampoCod->setValue($inCodIndicador);
        $obIPopUpIndicadorEconomico->obCampoCod->setNull(false);
        $obIPopUpIndicadorEconomico->geraFormulario($obFormulario);

        # Define Fórmula de Cálculo
        $obIPopUpFuncao = new IPopUpFuncao();
        $obIPopUpFuncao->obInnerFuncao->setRotulo('Fórmula de Cálculo');
        $obIPopUpFuncao->obInnerFuncao->setTitle('Fórmula de cálculo utilizada para o cálculo da multa.');
        $obIPopUpFuncao->obInnerFuncao->setNull(false);
        $obIPopUpFuncao->setCodModulo( 34 );
        $obIPopUpFuncao->setCodBiblioteca( 1 );

        $obBscFormulaCalculo = new BuscaInner;
        $obBscFormulaCalculo->setId('stFormula');
        $obBscFormulaCalculo->setValue($stNomFuncao);
        $obBscFormulaCalculo->setNull(false);
        $obBscFormulaCalculo->obCampoCod->setName('inCodFuncao');
        $obBscFormulaCalculo->obCampoCod->setValue($stCodFuncao);
        $obBscFormulaCalculo->obCampoCod->obEvento->setOnChange("buscaValor('buscaFuncao');");
        $obBscFormulaCalculo->setFuncaoBusca("abrePopUp('".CAM_GA_ADM_POPUPS."funcao/FLBuscarFuncao.php','frm','inCodFuncao','stFormula','todos','".Sessao::getId()."','800','550');");

        # Define Unidade de Medida
        $obISelUnidade = new ISelectUnidadeMedida();
        $obISelUnidade->setValue($stCodUnidade);
        $obISelUnidade->setTitle('Unidade de Medida utilizada para o cálculo da multa.');
        $obISelUnidade->setName('inCodUnidade');
        $obISelUnidade->setRotulo('Unidade de Medida (U.M.)');
        $obISelUnidade->setNull(false);
        $obFormulario->addComponente($obISelUnidade);

        # Define Conceder Desconto (sim)
        $obRadioConcederSim = new Radio();
        $obRadioConcederSim->setName('boConceder');
        $obRadioConcederSim->setRotulo('Conceder Desconto');
        $obRadioConcederSim->setTitle('Conceder desconto para pagamento antecipado de multa.');
        $obRadioConcederSim->setLabel('Sim');
        $obRadioConcederSim->setValue('S');
        $obRadioConcederSim->setNull(false);
        $obRadioConcederSim->obEvento->setOnChange("montaParametrosGET('montaMultaConceder');");

        # Define Conceder Desconto (não)
        $obRadioConcederNao = new Radio();
        $obRadioConcederNao->setName('boConceder');
        $obRadioConcederNao->setLabel('Não');
        $obRadioConcederNao->setValue('N');
        $obRadioConcederNao->setNull(false);
        $obRadioConcederNao->obEvento->setOnChange("montaParametrosGET('montaMultaCancelarConceder');");

        # Lê indicador desconto
        if ($rsPenalidadeDesconto->inNumLinhas > 0) {
            $obRadioConcederSim->setChecked(true);
            $jsBuffer .= $this->montaMultaConceder($arParametros);
            $jsBuffer .= $this->montaRecuperarDescontos($arParametros);
        } else {
            $obRadioConcederNao->setChecked(true);
            $jsBuffer.= $this->montaMultaCancelarConceder($arParametros);
        }

        $obIPopUpFuncao->setCodFuncao($stCodFuncao);
        $obIPopUpFuncao->geraFormulario($obFormulario);
        $obFormulario->agrupaComponentes(array($obRadioConcederSim, $obRadioConcederNao));
        $obFormulario->montaInnerHTML();

        if ($inCodIndicador > 0) {
            $pgOcul = "'".CAM_GT_MON_INSTANCIAS."indicadorEconomico/OCManterIndicador.php?".Sessao::getId()."&".$obIPopUpIndicadorEconomico->obCampoCod->getName()."=".$inCodIndicador."&stNomCampoCod=".$obIPopUpIndicadorEconomico->obCampoCod->getName()."&stIdCampoDesc=".$obIPopUpIndicadorEconomico->getId()."'";
            $js = "ajaxJavaScript(".$pgOcul.",'buscaIndicador');";
        }

        return "$('spnMulta').innerHTML = '" . $obFormulario->getHTML() . "';". $jsBuffer . $js ;
    }

    /**
     * Monta javascript que apaga a parte do formulário que processa Penalidades do tipo Multa.
     * @arParametros array arParametros array de parâmetros recebidos
     */
    public function apagaFormularioMulta(array $arParametros)
    {
        return "$('spnMulta').innerHTML = '';";
    }

    /**
     * Monta javascript que apaga a parte do formulário que processa Penalidades Valor.
     * @arParametros array arParametros array de parâmetros recebidos
     */
    public function apagaMultaValor()
    {
        return "$('spnMultaValor').innerHTML = '';";
    }

    /**
     * Monta javascript do restante do formulário de acordo com o tipo de Penalidade.
     * @arParametros array arParametros array de parâmetros recebidos
     */
    public function montaFormulario(array $arParametros)
    {
        if ($arParametros['inCodTipoPenalidade'] == '1') {
            return $this->montaMultaCancelarConceder($arParametros) . $this->montaFormularioMulta($arParametros);
        } else {
            return $this->apagaMultaValor() . $this->apagaFormularioMulta($arParametros) . $this->montaMultaCancelarConceder($arParametros);
        }
    }

    /**
     * Retorna a descrição da penalidade especificada.
     * @arParametros array arParametros array de parâmetros recebidos
     */
    public function mostraDescricaoPenalidade(array $arParametros)
    {
        return $this->obController->getDescricao($arParametros['inCodTipoPenalidade'], $arParametros['inCodPenalidade']);
    }

    /**
     * Retorna resultado de busca por penalidades.
     * @arParametros array arParametros array de parâmetros recebidos
     * @return RecordSet
     */
    public function buscaPenalidades(array $arParametros)
    {
        return $this->obController->getListaPenalidades($arParametros['inCodTipoPenalidade'], $arParametros['stNomPenalidade']);
    }

    public function buscaPenalidadesBaixadas(array $arParametros)
    {
        return $this->obController->getListaPenalidadesBaixadas($arParametros['inCodTipoPenalidade'], $arParametros['stNomPenalidade']);
    }

    /**
     * Retorna todos os dados sobre uma Penalidade.
     * @arParametros array arParametros array de parâmetros recebidos
     * @return RecordSet
     */
    public function recuperaPenalidade(array $arParametros)
    {
        return $this->obController->getPenalidade($arParametros['inCodPenalidade']);
    }

    /**
     * Retorna um span para preencher o Desconto.
     * @arParametros array arParametros array de parâmetros recebidos
     */
    public function montaMultaConceder(array $arParametros)
    {
        # Define o formulario
        $obFormulario = new Formulario();

        # Define Prazo de Antecipação
        $obPrazoAntecipacao = new TextBox();
        $obPrazoAntecipacao->setName('stPrazoAntecipacao');
        $obPrazoAntecipacao->setId('stPrazoAntecipacao');
        $obPrazoAntecipacao->setRotulo('Prazo de Antecipação');
        $obPrazoAntecipacao->setTitle('Prazo para o pagamento com desconto antes da data de vencimento.');
        $obPrazoAntecipacao->setSize(3);
        $obPrazoAntecipacao->setMaxLength(2);
        $obPrazoAntecipacao->setNull(false);
        $obPrazoAntecipacao->setValue($arParametros['stPrazoAntecipacao']);

        # Define Label Dias
        $obLabelDias = new Label();
        $obLabelDias->setRotulo('Prazo de Antecipação');
        $obLabelDias->setTitle('Prazo para o pagamento com desconto antes da data de vencimento.');
        $obLabelDias->setValue('Dias');

        # Define Valor do Desconto
        $obValorDesconto = new Numerico();
        $obValorDesconto->setName('stValorDesconto');
        $obValorDesconto->setRotulo('Valor de Desconto');
        $obValorDesconto->setTitle('Valor de Desconto');
        $obValorDesconto->setValue('');
        $obValorDesconto->setDecimais(2);
        $obValorDesconto->setMaxValue(99.99);
        $obValorDesconto->setNegativo(false);
        $obValorDesconto->setNaoZero(true);
        $obValorDesconto->setSize(3);
        $obValorDesconto->setMaxLength(5);
        $obValorDesconto->setNull(false);

        # Define Label %
        $obLabelPorcento = new Label();
        $obLabelPorcento->setRotulo('Valor de Desconto');
        $obLabelPorcento->setTitle('Valor de Desconto');
        $obLabelPorcento->setValue('%');

        # Define Botões de Inclusão e Limpar Descontos
        $obBtnIncluirDescontos = new Button();
        $obBtnIncluirDescontos->setName('btnIncluirDescontos');
        $obBtnIncluirDescontos->setValue('Incluir');
        $obBtnIncluirDescontos->setTipo('button');
        $obBtnIncluirDescontos->obEvento->setOnClick('incluirDescontos();');
        $obBtnIncluirDescontos->setDisabled(false);

        $obBtnLimparDescontos = new Button();
        $obBtnLimparDescontos->setName('btnLimparDescontos');
        $obBtnLimparDescontos->setValue('Limpar');
        $obBtnLimparDescontos->setTipo('button');
        $obBtnLimparDescontos->obEvento->setOnClick('limparDescontos();');
        $obBtnLimparDescontos->setDisabled(false);

        # Define span Lista de Descontos
        $obSpanListaDescontos = new Span();
        $obSpanListaDescontos->setId('spnListaDescontos');

        $obFormulario->addTitulo('Dados para Desconto');
        $obFormulario->agrupaComponentes(array($obPrazoAntecipacao, $obLabelDias));
        $obFormulario->agrupaComponentes(array($obValorDesconto, $obLabelPorcento));
        $obFormulario->agrupaComponentes(array($obBtnIncluirDescontos, $obBtnLimparDescontos));
        $obFormulario->addSpan($obSpanListaDescontos);
        $obFormulario->montaInnerHTML();

        if ($arParametros['inCodPenalidade'] && array_key_exists('0', Sessao::read('arDescontos')) == true) {

            $opt = array(
                'cabecalho' => 'Lista de Descontos',
                'span'      => 'spnListaDescontos',
                'desc'      => 'Prazo de Antecipação',
                'desc2'     => 'Valor de Desconto',
                'alvo'      => '',
                'codigo'    => $arParametros['stPrazoAntecipacao'],
                'container' => 'arDescontos'
            );

            $stRecuperaDescontos = $this->montaLista(Sessao::read('arDescontos'), '', $opt);
        }

        return "$('spnMultaDescontos').innerHTML = '" . $obFormulario->getHTML() . "';\n" . $stRecuperaDescontos;

    }

    /**
     * Retorna um span limpo para não conceder o Desconto.
     * @arParametros array arParametros array de parâmetros recebidos
     */
    public function montaMultaCancelarConceder(array $arParametros)
    {
        //Sessao::write('arDescontos', array());
        return "$('spnMultaDescontos').innerHTML = '';";
    }

    /**
     * Retorna um span que lista os Descontos para serem preenchidos
     * @arParametros array arParametros array de parâmetros recebidos
     */
    public function montaIncluirDescontos(array $arParametros)
    {
        $opt = array(
            'cabecalho' => 'Lista de Descontos',
            'span'      => 'spnListaDescontos',
            'desc'      => 'Prazo de Antecipação',
            'desc2'     => 'Valor de Desconto',
            'alvo'      => '',
            'codigo'    => $arParametros['stPrazoAntecipacao'],
            'container' => 'arDescontos'
        );

        $arValores = Sessao::read($opt['container']);

        if ($this->incluirItemLista($opt)) {
            if (!is_numeric($arParametros['inCodDesconto'])) {
                $arParametros['inCodDesconto'] = 'codDesconto';
            }

            $k = count($arValores);
            $arValores[$k]['codigo'] = $arParametros['stPrazoAntecipacao'];
            $arValores[$k]['nome'] = $arParametros['stValorDesconto'];
            $obHdnDiaDesconto =  $this->geraHidden('inDia', $arParametros['stPrazoAntecipacao']);
            $obHdnValorDesconto =  $this->geraHidden('inDesconto', $arParametros['stValorDesconto']);
            $obHdnCodDesconto =  $this->geraHidden('inCodDesconto', $arParametros['inCodDesconto']);
            $arValores[$k]['hidden1'] = $obHdnDiaDesconto;
            $arValores[$k]['hidden2'] = $obHdnValorDesconto;
            $arValores[$k]['hidden3'] = $obHdnCodDesconto;
            Sessao::write($opt['container'], $arValores);

            $lista = $this->montaLista($arValores,'',$opt);
            $result = $lista;
        } else {
            $stMensagem = '@Desconto já informado.(' . $arParametros['stValorDesconto'] . ')';
            $js.= "alertaAviso('" . $stMensagem . "','form','erro','" . Sessao::getId() . "'); \n";
            $result = $js;
        }

        return $result;
    }

    /**
     * Retorna um span que lista os Descontos para serem preenchidos e recuperado do banco
     * @arParametros array arParametros array de parâmetros recebidos
     */
    public function montaRecuperarDescontos(array $arParametros)
    {
        Sessao::write('arDescontos', array());

        $rsPenalidadeDesconto = $this->obController->getPenalidadeDesconto($arParametros['inCodPenalidade']);

        $opt = array(
            'cabecalho' => 'Lista de Descontos',
            'span'      => 'spnListaDescontos',
            'desc'      => 'Prazo de Antecipação',
            'desc2'     => 'Valor de Desconto',
            'alvo'      => '',
            'codigo'    => '',
            'container' => 'arDescontos'
        );

        $count = count($rsPenalidadeDesconto->arElementos);

        for ($i = 0; $i < $count; $i++) {
            $arParametros['stPrazoAntecipacao'] = $rsPenalidadeDesconto->arElementos[$i]['prazo'];
            $arParametros['stValorDesconto'] = $rsPenalidadeDesconto->arElementos[$i]['desconto'];
            $arParametros['inCodDesconto'] = $rsPenalidadeDesconto->arElementos[$i]['cod_desconto'];

            $arValores[$i]['codigo'] = $arParametros['stPrazoAntecipacao'];
            $arValores[$i]['nome'] = $arParametros['stValorDesconto'];
            $obHdnDiaDesconto =  $this->geraHidden('inDia', $arParametros['stPrazoAntecipacao']);
            $obHdnValorDesconto =  $this->geraHidden('inDesconto', $arParametros['stValorDesconto']);
            $obHdnCodDesconto =  $this->geraHidden('inCodDesconto', $arParametros['inCodDesconto']);
            $arValores[$i]['hidden1'] = $obHdnDiaDesconto;
            $arValores[$i]['hidden2'] = $obHdnValorDesconto;
            $arValores[$i]['hidden3'] = $obHdnCodDesconto;
            Sessao::write($opt['container'], $arValores);

            $lista = $this->montaLista($arValores,'',$opt);
        }

        return $lista;
    }

    public function incluirItemLista(array $arParametros)
    {
        $boLista = true;
        $arValores = Sessao::read($arParametros['container']);

        if (is_array($arValores)) {

            foreach ($arValores as $key=>$value) {

                if ($value['codigo'] == $arParametros['codigo']) {
                    $boLista = false;
                }
            }
        }

        return $boLista;
    }

    public function excluirItemLista($param)
    {
        $arRetorno = array();
        $k = 0;
        $key = trim($param['inId']);
        $arValores = Sessao::read($param['container']);

        if (is_array($arValores)) {

            foreach ($arValores as $value) {

                $keyValue = trim($value['codigo']);

                if ($key !== $keyValue) {
                    $arRetorno[$k]['codigo']  = $value['codigo'];
                    $arRetorno[$k]['nome']    = $value['nome'];
                    $arRetorno[$k]['hidden1'] = $value['hidden1'];
                    $arRetorno[$k]['hidden2'] = $value['hidden2'];
                    $arRetorno[$k]['hidden3'] = $value['hidden3'];
                    $k++;
                }
            }
        }

        Sessao::write($param['container'], $arRetorno);

        return $this->montaLista($arRetorno,'',$param);
    }

    public function geraHidden($nome,$value)
    {
        $obHdn = new Hidden();
        $obHdn->setName("{$nome}[]");
        $obHdn->setValue($value);
        $obHdn->montaHtml();

        return $obHdn->getHtml();
    }

    public function montaLista($arValores, $stAcao = '', $opt)
    {
        $rsRecordSet = new RecordSet();
        $rsRecordSet->preenche($arValores);

        $obLista = new Lista();
        $obLista->setMostraPaginacao(false);
        $obLista->setTitulo($opt['cabecalho']);
        $obLista->setRecordSet($rsRecordSet);

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('&nbsp;');
        $obLista->ultimoCabecalho->setWidth(5);
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo($opt['desc']);
        $obLista->ultimoCabecalho->setWidth(30);
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo($opt['desc2']);
        $obLista->ultimoCabecalho->setWidth(50);
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('Ação');
        $obLista->ultimoCabecalho->setWidth(10);
        $obLista->commitCabecalho();

        # Dados
        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento('ESQUERDA');
        $obLista->ultimoDado->setCampo('codigo');
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento('ESQUERDA');
        $obLista->ultimoDado->setCampo('[nome] [hidden1] [hidden2] [hidden3]');
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao('EXCLUIR');
        $obLista->ultimaAcao->setFuncao(true);
        $obLista->ultimaAcao->setLink("javascript: executaFuncaoAjax('excluirItemLista');");
        $obLista->ultimaAcao->addCampo('', "&inId=[codigo]&span={$opt['span']}&cabecalho={$opt['cabecalho']}&desc={$opt['desc']}&desc2={$opt['desc2']}&alvo={$opt['alvo']}&container={$opt['container']}");
        $obLista->commitAcao();

        $obLista->montaHTML();

        $stHTML = $obLista->getHTML();
        $stHTML = str_replace("\n", '', $stHTML);
        $stHTML = str_replace('  ', '', $stHTML);
        $stHTML = str_replace("'", "\\'", $stHTML);
        $stJS  .= " d.getElementById('{$opt['span']}').innerHTML  = '" . $stHTML . "'; \n";

        return $stJS;
    }

    public function getTipoDocumento($inCodDocumento)
    {
        $rsAdministracao = new RecordSet();
        $obTAdminstracaoModeloDocumento = new TAdministracaoModeloDocumento();

        $stFiltro = ' WHERE cod_documento = ' . $inCodDocumento;
        $obTAdminstracaoModeloDocumento->RecuperaTodos($rsAdministracao, $stFiltro);

        return $rsAdministracao->getCampo('cod_tipo_documento');
    }
}

?>
