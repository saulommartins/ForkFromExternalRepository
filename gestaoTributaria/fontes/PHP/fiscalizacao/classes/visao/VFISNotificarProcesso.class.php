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
 * Classe de visão para Notificar Processo
 * Data de Criação: 11/11/2008

 * @author Analista    : Heleno Menezes dos Santos
 * @author Programador : Jânio Eduardo Vasconcellos de Magalhães

 * @package URBEM
 * @subpackage Visao

 $Id: VFISNotificarProcesso.class.php 63839 2015-10-22 18:08:07Z franver $

 * Casos de uso:
 */

//include_once( CAM_GT_FIS_COMPONENTES . "ITextBoxSelectTipoPenalidade.class.php" );

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';

require_once( CAM_GT_FIS_NEGOCIO     . "RFISPenalidade.class.php" );
require_once( CAM_GT_FIS_NEGOCIO     . "RFISInfracao.class.php" );
require_once( CAM_GT_FIS_MAPEAMENTO.'TFISLevantamento.class.php' );
require_once( CAM_GT_FIS_COMPONENTES . "ITextBoxSelectInfracao.class.php" );

class VFISNotificarProcesso
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
     * Remove caracteres inválidos das strings dentro de expressões em Javascript.
     * @param  string $st string de entrada
     * @return string saída formatada
     */
    private function quoteJavaScript($stJS)
    {
        $stJS = str_replace( "  ", "", $stJS );
        $stJS = str_replace( "\n", "", $stJS );
        $stJS = str_replace( "'", "\\'", $stJS );

        return $stJS;
    }

    /**
     * Monta restante do formulário.
     * @param array $arParametros array dos parâmetros recebidos
     */
    public function montaFormulario(array $arParametros)
    {
        $stJS  = $this->montaInscricao( $arParametros );
        $stJS .= $this->montaRegistrosDeInfracao( $arParametros );
        $stJS .= $this->montaListaDePenalidades( $arParametros );

        return $stJS;
    }

    /**
     * Monta label do valor da inscrição.
     * @param  array  $arParametros array dos parâmetros recebidos
     * @return string com comandos em javascript
     */
    private function montaInscricao(array &$arParametros)
    {
        $obFormulario   = new Formulario();
        $obLblInscricao = new Label();

        switch ($arParametros['inTipoFiscalizacao']) {
        case '1':
            $obLblInscricao->setRotulo( "Inscrição Econômica" );
            $obLblInscricao->setTitle( "Inscrição Econômica" );
            break;

        case '2':
            $obLblInscricao->setRotulo( "Inscrição Imobiliária" );
            $obLblInscricao->setTitle( "Inscrição Imobiliária" );
            break;

        default:
            $obLblInscricao->setRotulo( "Inscrição Econômica/Imobiliária" );
            $obLblInscricao->setTitle( "Inscrição Econômica/Imobiliária" );
            break;
        }

        $obLblInscricao->setValue( $arParametros['inInscricao'] );

        $obFormulario->addComponente( $obLblInscricao );
        $obFormulario->montaInnerHTML();

        return "$('spnInscricao').innerHTML = '" . $obFormulario->getHTML() . "';";
    }

    /**
     * Função para montar lista genérica.
     * @param  string    $stTitulo     título da lista
     * @param  array     $arParametros os cabeçalhos da lista
     * @param  RecordSet $rsRecordSet  os valores da lista
     * @return Lista     o objeto lista
     */
    private function montaLista($stTitulo, array &$arLista, RecordSet &$rsRecordSet)
    {
        $obLista = new Lista();
        $obLista->setMostraPaginacao( true );
        $obLista->setTitulo( $stTitulo );
        $obLista->setRecordSet( $rsRecordSet );

        # Define os cabeçalhos.
        foreach ($arLista as $i => $arCampos) {
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo( $arCampos['nome'] );
            $obLista->ultimoCabecalho->setWidth( $arCampos['width'] );
            $obLista->commitCabecalho();
        }

        # Define os dados.
        foreach ($arLista as $i => $arCampos) {
            if ($i == 0) {
                continue;
            }

            if (! isset( $arCampos['acao'] ) ) {
                $obLista->addDado();
                $obLista->ultimoDado->setAlinhamento( $arCampos['alinhamento'] );
                $obLista->ultimoDado->setCampo( $arCampos['campo'] );
                $obLista->commitDado();
            } else {
                $obLista->addAcao();
                $obLista->ultimaAcao->setAcao( $arCampos['acao'] );
                $obLista->ultimaAcao->setLink( $arCampos['link'] );
                $obLista->ultimaAcao->setFuncaoAjax( isset( $arCampos['ajax'] ) ? true : false );
                call_user_func_array( array( $obLista->ultimaAcao, "addCampo" ), $arCampos['campo'] );
                $obLista->commitAcao();
            }
        }

        return $obLista;
    }

    private function montaQuantidadePenalidade(Lista &$obLista, array &$arCampos)
    {
        $obLista->addLinha();

        $obLista->ultimaLinha->addCelula();
        $obLista->ultimaLinha->ultimaCelula->addConteudo( "*Quantidade" );
        $obLista->ultimaLinha->ultimaCelula->setClass( "label" );
        $obLista->ultimaLinha->commitCelula();

        $obNumQuantidade = new Numerico();
        $obNumQuantidade->setName( "fiQuantidade[{$arCampos['cod_penalidade']}]" );
        $obNumQuantidade->setSize( 10 );
        $obNumQuantidade->setRotulo( "*Quantidade" );
        $obNumQuantidade->setValue( isset( $arCampos['quantidade'] ) ? $arCampos['quantidade'] : "0,00" );
        $obNumQuantidade->setNull( false );

        $obLista->ultimaLinha->addCelula();
        $obLista->ultimaLinha->ultimaCelula->setColSpan( 2 );
        $obLista->ultimaLinha->ultimaCelula->setClass( "field" );
        $obLista->ultimaLinha->ultimaCelula->addComponente( $obNumQuantidade );
        $obLista->ultimaLinha->commitCelula();

        $obLista->commitLinha();
    }

    private function montaBaseCalculoPenalidade(Lista &$obLista, array &$arCampos)
    {
        $obLista->addLinha();

        $obLista->ultimaLinha->addCelula();
        $obLista->ultimaLinha->ultimaCelula->addConteudo( "*Base de Cálculo" );
        $obLista->ultimaLinha->ultimaCelula->setClass( "label" );
        $obLista->ultimaLinha->ultimaCelula->setTitle( "Valor para cálculo da multa devida." );
        $obLista->ultimaLinha->commitCelula();

        $obTxtBaseCalculo = new Moeda();
        $obTxtBaseCalculo->setName( "fiBaseCalculo[{$arCampos['cod_penalidade']}]" );
        $obTxtBaseCalculo->setSize( 10 );
        $obTxtBaseCalculo->setRotulo( "*Base de Cálculo" );
        $obTxtBaseCalculo->setValue( isset( $arCampos['valor'] ) ? $arCampos['valor'] : "0,00" );
        $obTxtBaseCalculo->setTitle( "Valor para cálculo da multa devida." );
        $obTxtBaseCalculo->setNull( false );

        $obLista->ultimaLinha->addCelula();
        $obLista->ultimaLinha->ultimaCelula->setColSpan( 2 );
        $obLista->ultimaLinha->ultimaCelula->setClass( "field" );
        $obLista->ultimaLinha->ultimaCelula->addComponente( $obTxtBaseCalculo );
        $obLista->ultimaLinha->commitCelula();

        $obLista->commitLinha();
    }

    private function montaDataPenalidade(Lista &$obLista, array &$arCampos)
    {
        $obLista->addLinha();

        $obLista->ultimaLinha->addCelula();
        $obLista->ultimaLinha->ultimaCelula->addConteudo( "*Data de " . ucfirst( $arCampos['descricao'] ) );
        $obLista->ultimaLinha->ultimaCelula->setClass( "label" );
        $obLista->ultimaLinha->commitCelula();

        $obDtPenalidade = new Data();
        $obDtPenalidade->setName( "dtPenalidade[{$arCampos['cod_penalidade']}]" );
        $obDtPenalidade->setRotulo( "*Data de " . ucfirst ( $arCampos['descricao'] ) );
        $obDtPenalidade->setValue( $arCampos['dt_ocorrencia'] );
        $obDtPenalidade->setNull( false );

        $obLista->ultimaLinha->addCelula();
        $obLista->ultimaLinha->ultimaCelula->setColSpan( 2 );
        $obLista->ultimaLinha->ultimaCelula->setClass( "field" );
        $obLista->ultimaLinha->ultimaCelula->addComponente( $obDtPenalidade );
        $obLista->ultimaLinha->commitCelula();

        $obLista->commitLinha();
    }

    private function montaObservacaoPenalidade(Lista &$obLista, array &$arCampos)
    {
        $obLista->addLinha();

        $obLista->ultimaLinha->addCelula();
        $obLista->ultimaLinha->ultimaCelula->addConteudo( "*Observação" );
        $obLista->ultimaLinha->ultimaCelula->setClass( "label" );
        $obLista->ultimaLinha->commitCelula();

        $obTxtObservacao = new TextArea();
        $obTxtObservacao->setName( "stObservacaoPenalidade[{$arCampos['cod_penalidade']}]" );
        $obTxtObservacao->setRotulo( "*Observação" );
        $obTxtObservacao->setValue( $arCampos['observacao'] );
        $obTxtObservacao->setNull( false );

        $obLista->ultimaLinha->addCelula();
        $obLista->ultimaLinha->ultimaCelula->setColSpan( 2 );
        $obLista->ultimaLinha->ultimaCelula->setClass( "field" );
        $obLista->ultimaLinha->ultimaCelula->addComponente( $obTxtObservacao );
        $obLista->ultimaLinha->commitCelula();

        $obLista->commitLinha();
    }

    private function montaPenalidade(Lista &$obLista, array &$arCampos)
    {
        $stCampoTipo       = $arCampos['cod_tipo_penalidade'] . ' - ' . $arCampos['descricao'];
        $stCampoPenalidade = $arCampos['cod_penalidade'] . ' - ' . $arCampos['nom_penalidade'];

        $obLista->addLinha();
        $obLista->ultimaLinha->addCelula();
        $obLista->ultimaLinha->ultimaCelula->addConteudo( $stCampoTipo );
        $obLista->ultimaLinha->ultimaCelula->setClass( "label" );
        $obLista->ultimaLinha->commitCelula();

        $obLista->ultimaLinha->addCelula();
        $obLista->ultimaLinha->ultimaCelula->addConteudo( $stCampoPenalidade );
        $obLista->ultimaLinha->ultimaCelula->setClass( "field" );
        $obLista->ultimaLinha->commitCelula();

        $obAcExcluirPenalidade = new Acao();
        $obAcExcluirPenalidade->setAcao( "EXCLUIR" );
        $obAcExcluirPenalidade->setFuncaoAjax( true );
        $stParametros = "&inCodPenalidade={$arCampos['cod_penalidade']}&inCodInfracao={$arCampos['cod_infracao']}";
        $obAcExcluirPenalidade->setLink( "javascript: executaFuncaoAjax('excluirPenalidade', '{$stParametros}');" );

        $obLista->ultimaLinha->addCelula();
        $obLista->ultimaLinha->ultimaCelula->addComponente( $obAcExcluirPenalidade );
        $obLista->ultimaLinha->ultimaCelula->setClass( "field" );
        $obLista->ultimaLinha->ultimaCelula->setAlign( "center" );
        $obLista->ultimaLinha->commitCelula();

        $obLista->commitLinha();
    }

    /**
     * Monta lista de penalidades.
     * @param  RecordSet $rsPenalidades RecordSet com as penalidades
     * @return html      da lista de penalidades
     */
    private function montaListaPenalidades(RecordSet &$rsPenalidades)
    {
        $obLista = new Lista();

        if (! $rsPenalidades->getElementos() ) {
            return '';
        }

        # Prepara lista.
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( "Lista de Penalidades" );
        $obLista->setRecordSet( $rsPenalidades );
        $obLista->setNumeracao( false );

        # Define os cabeçalhos.
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Tipo" );
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Penalidade" );
        $obLista->ultimoCabecalho->setWidth( 70 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        # Escreve os campos.
        foreach ( $rsPenalidades->getElementos() as $arCampos ) {
            $this->montaPenalidade( $obLista, $arCampos );

            if ($arCampos['cod_tipo_penalidade'] == 1) { //falta descobrir onde devo armazenar o valor destas variaveis
                $this->montaBaseCalculoPenalidade( $obLista, $arCampos );
                $this->montaQuantidadePenalidade( $obLista, $arCampos );
            } else {
                $this->montaDataPenalidade( $obLista, $arCampos );
                $this->montaObservacaoPenalidade( $obLista, $arCampos );
            }
        }

        $obLista->montaHTML();

        return $this->quoteJavaScript( $obLista->getHTML() );
    }

    /**
     * Monta componente da tela "Lista de Penalidades"
     * @param  array       $arParametros todos os dados recebidos
     * @return instrução javascript
     */
    private function montaListaDePenalidades(array &$arParametros)
    {
        $arPenalidades = Sessao::read( 'arPenalidades' );
        $inCodInfracao = $arParametros['inCodInfracao'] ? $arParametros['inCodInfracao'] : "0";
        $rsPenalidades = new RecordSet();

        # Monta a lista propriamente dita.
        if (! isset( $arPenalidades[$inCodInfracao] ) ) {
            # Obtem as penalidades.
            $obRFISInfracao = new RFISInfracao();
            $rsPenalidades = $obRFISInfracao->getListaPenalidadesPorInfracao( $inCodInfracao );
            $arPenalidades[$inCodInfracao] = $rsPenalidades->getElementos();

            # Associa penalidade a infração.
            foreach ($arPenalidades[$inCodInfracao] as &$arPenalidade) {
                $arPenalidade['cod_infracao'] = $inCodInfracao;
            }

            # Guarda novas penalidades na sessão.
            Sessao::write( 'arPenalidades', $arPenalidades );
        }

        # Monta a lista propriamente dita.
        $rsPenalidades->preenche( $arPenalidades[$inCodInfracao] );
        $stHTML = $this->montaListaPenalidades( $rsPenalidades );

        return "$('spnListaPenalidades').innerHTML = '" . $stHTML . "';";
    }

    private function montaListaInfracoes(RecordSet $rsInfracoes)
    {
        $obLista = new Lista();
        $obLista->setMostraPaginacao( true );
        $obLista->setTitulo( "Registros de Infração" );
        $obLista->setRecordSet( $rsInfracoes );

        # Parâmetros para a lista.
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        # Define nome e tipo de infração.
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Infração" );
        $obLista->ultimoCabecalho->setWidth( 80 );
        $obLista->commitCabecalho();

        # Define campo das ações
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        # Define os dados da infração.
        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
        $obLista->ultimoDado->setCampo( "[cod_infracao] - [nom_infracao]" );
        $obLista->commitDado();

        # Define as ações
        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "ALTERAR" );
        $obLista->ultimaAcao->setLink( "javascript: executaFuncaoAjax('alterarInfracao');" );
        $obLista->ultimaAcao->setFuncaoAjax( true );
        $obLista->ultimaAcao->addCampo( "", "cod_infracao" );
        $obLista->commitAcao();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setLink( "javascript: executaFuncaoAjax('excluirInfracao');" );
        $obLista->ultimaAcao->setFuncaoAjax( true );
        $obLista->ultimaAcao->addCampo( "", "cod_infracao" );
        $obLista->commitAcao();

        return $obLista;
    }

    /**
     * Monta componente da tela "Registros de Infração"
     * @param  array       $arParametros todos os dados recebidos
     * @return instrução javascript
     */
    private function montaRegistrosDeInfracao(array $arParametros)
    {
        # Obtem as penalidades
        $inCodProcesso = $arParametros['inCodProcesso'] ? $arParametros['inCodProcesso'] : 0;

        # Monta a lista propriamente dita
        $arInfracoes = Sessao::read( 'arInfracoes' );
        $obLista = $this->preencheListaInfracoes( $arInfracoes );
        $obLista->montaHTML();

        $stHTML = $this->quoteJavaScript( $obLista->getHTML() );

        return "$('spnRegistrosInfracao').innerHTML = '" . $stHTML . "';";
    }

    private function alterarBotaoIncluir(array $arParametros)
    {
        return "document.frm.btnIncluirInfracao.value = 'Incluir';";
    }

    private function alterarBotaoModificar(array $arParametros)
    {
        return "document.frm.btnIncluirInfracao.value = 'Alterar';";
    }

    private function preencheObservacoesInfracao(array $arParametros)
    {
        $arInfracoes = Sessao::read( "arInfracoes" );
        $inCodInfracao = $arParametros['cod_infracao'];

        # Verifica se infração está repetida.
        foreach ($arInfracoes as $arCampos) {
            if ($arCampos['cod_infracao'] == $inCodInfracao) {
                return "document.frm.stObservacoesInfracao.value = '" . $arCampos['observacao'] . "';";
            }
        }

        return "document.frm.stObservacoesInfracao.value = '';";
    }

    public function alterarInfracao( array $arParametros ) //funcao quando tu aperta pra alterar o conteudo
    {
        if ($arParametros['cod_infracao']) {
            $inCodInfracao = $arParametros['cod_infracao'];
            $stJS  = "preencheInfracao('$inCodInfracao');";
            $arParametros['inCodInfracao'] = $inCodInfracao;
        }

        # Atualiza também a fundamentação legal.
        $stJS .= $this->preencheObservacoesInfracao( $arParametros );
        $stJS .= $this->preencherFundamentacaoLegal( $arParametros );
        $stJS .= $this->montaListaDePenalidades( $arParametros );
        //$stJS .= $this->alterarBotaoModificar( $arParametros );
        return $stJS;
    }

    public function preencherFundamentacaoLegal(array $arParametros)
    {
        $inCodInfracao = $arParametros['inCodInfracao'];
        $obITextBoxSelectInfracao = new ITextBoxSelectInfracao();

        return $obITextBoxSelectInfracao->preencherFundamentacaoLegal( $inCodInfracao );
    }

    private function excluirItemLista($stNomCodigoElemento, $inCodElemento, array &$arElementos)
    {
        $i = 0;

        foreach ($arElementos as $arCampos) {
            if ($arCampos[$stNomCodigoElemento] == $inCodElemento) {
                array_splice( $arElementos, $i, 1 );

                return $i;
            }

            $i++;
        }

        return -1;
    }

    public function excluirPenalidade(array $arParametros)
    {
        $inCodInfracao = $arParametros['inCodInfracao'];
        $inCodPenalidade = $arParametros['inCodPenalidade'];
        $arPenalidades = Sessao::read( "arPenalidades" );

        # Exclui um elemento da lista.
        if ( $this->excluirItemLista( 'cod_penalidade', $inCodPenalidade, $arPenalidades[$inCodInfracao] ) != -1 ) {
            Sessao::write( "arPenalidades", $arPenalidades );
        }

        # Monta o HTML da tabela

        return $this->montaListaDePenalidades( $arParametros );
    }

    private function preencheListaInfracoes(array $arInfracoes)
    {
        $rsInfracoes = new RecordSet();
        $rsInfracoes->preenche( $arInfracoes );

        return $this->montaListaInfracoes( $rsInfracoes );
    }

    /**
     * Inclui infração na lista de infrações da notificação.
     */
    public function incluirInfracao(array $arParametros)
    {

        $obRFISInfracao = new RFISInfracao();
        $inCodInfracao = $arParametros['inCodInfracao'];
        $arInfracoes = Sessao::read( "arInfracoes" );
        $inNumInfracao = count( $arInfracoes );
        $arPenalidades = Sessao::read( "arPenalidades" );
        $boPenalidades = false;
        $boInfracaoExistente = false;
        for ( $inX=0; $inX<count($arPenalidades); $inX++ ) {
            for ( $inY=0; $inY<count($arPenalidades[$inX]); $inY++ ) {
                $inCodPenal = $arPenalidades[$inX][$inY]['cod_penalidade'];
                if ($arPenalidades[$inX][$inY]['cod_tipo_penalidade'] == 1) {
                    $arBase = $_REQUEST["fiBaseCalculo"];
                    $arPenalidades[$inX][$inY]["valor"] = $arBase[$inCodPenal];
                    $arFIqtd = $_REQUEST["fiQuantidade"];
                    $arPenalidades[$inX][$inY]["quantidade"] = $arFIqtd[$inCodPenal];
                } else {
                    $arDTpen = $_REQUEST["dtPenalidade"];
                    $arPenalidades[$inX][$inY]["dt_ocorrencia"] = $arDTpen[$inCodPenal];
                    $arObs = $_REQUEST["stObservacaoPenalidade"];
                    $arPenalidades[$inX][$inY]["observacao"] = $arObs[$inCodPenal];
                }
            }
        }

        # Verifica se infração está repetida.
        foreach ($arInfracoes as $i => $arCampos) {
            if ($arCampos['cod_infracao'] == $inCodInfracao) {
                $boInfracaoExistente = true;
                $inNumInfracao = $i;
            }
        }

        # Verifica se infração existente permite cominar.
        if ((! $boInfracaoExistente ) && ( count( $arInfracoes ) == 1 )) {
            $inCodInfracao2 = $arInfracoes[0]['cod_infracao'];
            $rsInfracao = $obRFISInfracao->getInfracao( $inCodInfracao2 );

            if (! $rsInfracao->eof() ) {
                if ( $rsInfracao->getCampo( 'comminar' ) == 'f' ) {
                    return "alertaAviso('@Infração existente não permite cominar. ($inCodInfracao2)', 'form', 'erro', '" . Sessao::getID() . "');";
                }
            }
        }

        # Verifica se a infração a incluir permite cominar com as outras.
        if ( count( $arInfracoes ) > 0 ) {
            $rsInfracao = $obRFISInfracao->getInfracao( $inCodInfracao );

            if (! $rsInfracao->eof() ) {
                if ( $rsInfracao->getCampo( 'comminar' ) == 'f' ) {
                    return "alertaAviso('@Infração não comina. ($inCodInfracao)', 'form', 'erro', '" . Sessao::getID() . "');";
                }
            }
        }

        $arInfracao = array();
        $arInfracao['cod_infracao'] = $inCodInfracao;
        $arInfracao['observacao'] = $arParametros['stObservacoesInfracao'];
        $arInfracao['nom_infracao'] = $arParametros['stHdnNomeInfracao'];
        $arInfracoes[$inNumInfracao] = $arInfracao;
/*
        if ($arPenalidades[$inCodInfracao]) {
            foreach ($arPenalidades[$inCodInfracao] as &$arCamposPenalidade) {
                $inCodPenalidade = $arCamposPenalidade['cod_penalidade'];

            }
        }
*/
        Sessao::write( 'arInfracoes', $arInfracoes );
        Sessao::write( 'arPenalidades', $arPenalidades );

        # Monta o HTML da lista.
        $obLista = $this->preencheListaInfracoes( $arInfracoes );
        $obLista->montaHTML();

        # Remove caracteres inválidos das strings em Javascript.
        $stHTML  = $this->quoteJavaScript( $obLista->getHTML() );
        $stJs = "$('spnRegistrosInfracao').innerHTML = '" . $stHTML . "';";
        $stJs .= "$('spnListaPenalidades').innerHTML = '&nbsp;';";
        $stJs .= "document.frm.inCodInfracao.value = '';";
        $stJs .= "document.frm.inSelInfracao.value = '';";
        $stJs .= "$('stFundamentacaoLegal').innerHTML = '&nbsp;';";
        $stJs .= "document.frm.stObservacoesInfracao.value = '';";

        return $stJs;
    }

    public function excluirInfracao(array $arParametros)
    {
        $inCodInfracao = $arParametros['cod_infracao'];
        $arInfracoes = Sessao::read( 'arInfracoes' );

        # Exclui um elemento da lista.
        if ( $this->excluirItemLista( 'cod_infracao', $inCodInfracao, $arInfracoes ) != -1 ) {
            Sessao::write( 'arInfracoes', $arInfracoes );
        }

        # Monta o HTML da lista.
        $obLista = $this->preencheListaInfracoes( $arInfracoes );
        $obLista->montaHTML();

        # Remove caracteres inválidos das strings em Javascript.
        $stHTML = $this->quoteJavaScript( $obLista->getHTML() );

        return "$('spnRegistrosInfracao').innerHTML = '" . $stHTML . "';";
    }

    # FIXME
    public function limparInfracao(array $arParametros)
    {
        $stHTML  = "document.frm.inCodInfracao.value = '';";
        $stHTML .= "document.frm.inSelInfracao.value = '';";

        return $stHTML . $this->alterarInfracao( array() );
    }

    /**
     * Executa ação recebida na página de processamento (PR).
     */
    public function executarAcao(array $arParametros)
    {
        Sessao::setTrataExcecao( true );

        $stMetodo = $arParametros['stAcao'];
        $this->inCodProcesso = $arParametros['inCodProcesso'];
        if ( is_string( $stMetodo ) ) {
            $this->$stMetodo( $arParametros );
        }

        Sessao::encerraExcecao();
    }

    public function notificar(array $arParametros)
    {
        $boRetido = $this->obController->verificaServicoComRetencao($this->inCodProcesso);

        if ($boRetido) {
            $obTLevantamento = new TFISLevantamento;
            $obTLevantamento->recuperaTodos($rsLevantamento, ' WHERE cod_processo = '.$this->inCodProcesso);

            if ($rsLevantamento->eof()) {
                return sistemaLegado::alertaAviso('LSNotificarProcesso.php?stAcao=notificar&stTipoFiscalizacao=1', 'Não consta geração da planilha fiscal para este processo (' . $this->inCodProcesso . ')', 'n_incluir', 'erro', Sessao::getId());
            }
        }

        if ($this->obController->verificarNotificacao($this->inCodProcesso)) {
            $arData = $this->obController->emitirNotificacao( $arParametros );
            $this->obController->imprimir($arData);

            return sistemaLegado::alertaAviso("FLManterProcesso.php?stAcao=notificar",$this->obController->getCodProcesso(), "incluir", "aviso", Sessao::getId());
        } else {
            unset($arParametros['stAcao']);

            foreach( $arParametros as $key => $valor )
                $link.= "&".$key."=".$valor;

            return sistemaLegado::alertaAviso("FMNotificarProcesso.php?stAcao=tramitaNotificacao".$link.Sessao::read( 'link' )."",$this->codProcesso, "incluir", "aviso", Sessao::getId());
        }
    }

    public function tramitaNotificacao(array $arParametros)
    {
        $this->obController->tramitaNotificacao( $arParametros );
    }
}
