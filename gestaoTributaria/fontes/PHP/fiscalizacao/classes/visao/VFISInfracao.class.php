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
 * Classe de visão da Fiscalizacao.
 * Data de Criação: 01/08/2008

 * @author Analista    : Heleno Menezes dos Santos
 * @author Programador : Pedro Vaz de Mello de Medeiros

 * @package URBEM
 * @subpackage Visao

 $Id: VFISInfracao.class.php 63839 2015-10-22 18:08:07Z franver $

 * Casos de uso:
 */

include_once CAM_GT_FIS_MAPEAMENTO . 'TFISPenalidade.class.php';
include_once CAM_GA_ADM_MAPEAMENTO . 'TAdministracaoModeloDocumento.class.php';
include_once CAM_GT_FIS_COMPONENTES . 'IPopUpPenalidade.class.php';

class VFISInfracao
{
    private $obController;

    public function __construct($obController)
    {
        $this->obController = $obController;
    }

    /**
     * Cria lista de penalidades para array de penalidades
     * @param  string $stAcao        a ação a ser realizada
     * @param  array  $arPenalidades array de penalidades
     * @param  string $stLink        o link para a ação associada
     * @return Lista  o objeto lista
     */
    private function preencheListaPenalidades($stAcao, array $arPenalidades, $stLink)
    {
        $rsPenalidades = new RecordSet();
        $rsPenalidades->preenche($arPenalidades);

        return $this->montaListaPenalidades( $stAcao, $rsPenalidades, $stLink );
    }

    /**
     * Monta elementos HIDDEN para tabela de penalidades
     * @param  string $stName    nome do array de HIDDEN
     * @param  array  $arValores array com os valores
     * @return string o código HTML dos HIDDEN
     */
    private function montaListaHidden($stName, array $arValores)
    {
        $stHTML = '';

        foreach ($arValores as $i => $arCampos) {
            foreach ($arCampos as $stChave => $stValue) {
                $obHdn = new Hidden();

                $obHdn->setName( $stName . "[$i][$stChave]" );
                $obHdn->setValue( $stValue );
                $obHdn->montaHTML();
                $stHTML .= $obHdn->getHTML();
            }
        }

        return $stHTML;
    }

    /**
     * Carrega as penalidades e cria lista em HTML.
     * @param $inCodInfracao código da infração
     * @return código HTML da lista
     */
    public function carregaPenalidadesHTML($inCodInfracao)
    {
        $rsPenalidades = new RecordSet();
        $stLink        = "javascript: executaFuncaoAjax('excluirPenalidade');";

        # Obtem lista de penalidades do banco de dados.
        $rsPenalidades = $this->obController->getListaPenalidadesPorInfracao( $inCodInfracao );
        $obLista = $this->montaListaPenalidades('EXCLUIR', $rsPenalidades, $stLink);
        $obLista->montaHTML();

        # Salva as penalidades na sessão.
        $arPenalidades = $rsPenalidades->getElementos();
        Sessao::write('arValores', $arPenalidades);

        # Cria HTML das estruturas HIDDEN.
        $stHTML  = $this->montaListaHidden('arPenalidades', $arPenalidades);
        $stHTML .= $obLista->getHTML();

        return $stHTML;
    }

    public function montaPenalidade(array $arParametros)
    {
        if ($_REQUEST["cmbTipoFiscalizacao"]) {
            # Define dados das Penalidades
            $obFormulario = new Formulario();
            $obFormulario->addTitulo('Dados para Penalidades');
            $obIPopUpPenalidade = new IPopUpPenalidade($obForm);
            $obIPopUpPenalidade->setRotulo('*Penalidade');
            $obIPopUpPenalidade->setTitle('Penalidade gerada pela infração.');
            $obIPopUpPenalidade->setNull(true);
            if ($_REQUEST["cmbTipoFiscalizacao"] == 1) {
                $obIPopUpPenalidade->setCodTipoPenalidade( $_REQUEST["cmbTipoFiscalizacao"] );
            }

            $obIPopUpPenalidade->geraFormulario($obFormulario);

            # Define botão de incluir
            $obBtnIncluirPenalidade = new Button();
            $obBtnIncluirPenalidade->setName('btnIncluir');
            $obBtnIncluirPenalidade->setValue('Incluir');
            $obBtnIncluirPenalidade->setTipo('button');
            $obBtnIncluirPenalidade->obEvento->setOnClick('incluirPenalidade();');
            $obBtnIncluirPenalidade->setDisabled( false );

            # Define botão de limpar
            $obBtnLimparPenalidade = new Button();
            $obBtnLimparPenalidade->setName('btnLimpar');
            $obBtnLimparPenalidade->setValue('Limpar');
            $obBtnLimparPenalidade->setTipo('button');
            $obBtnLimparPenalidade->obEvento->setOnClick('limparPenalidade();');
            $obBtnLimparPenalidade->setDisabled( false );

            # Coloca os botões na página
            $arBotoesPenalidade = array( $obBtnIncluirPenalidade, $obBtnLimparPenalidade );
            $obFormulario->defineBarra( $arBotoesPenalidade, 'left', '' );

            $obFormulario->montaInnerHTML();
            $stHTML = $obFormulario->getHTML();
            $stComando = "$('spnPenalidades').innerHTML = '".$stHTML."';";
            $stHTML = "&nbsp;";
            $stComando .= "$('spnListaPenalidades').innerHTML = '".$stHTML."';";
            Sessao::write('arValores', array()); //limpando lista de penalidades
        } else {
            $stHTML = "&nbsp;";
            $stComando = "$('spnPenalidades').innerHTML = '".$stHTML."';";
            $stComando .= "$('spnListaPenalidades').innerHTML = '".$stHTML."';";
            Sessao::write('arValores', array()); //limpando lista de penalidades
        }

        return $stComando;
    }

    public function carregarPenalidades(array $arParametros)
    {
        if ($arParametros['inCodInfracao']) {
            $stHTML = $this->carregaPenalidadesHTML($arParametros['inCodInfracao']);

            return "$('spnListaPenalidades').innerHTML = '" . $this->quoteJavaScript($stHTML) . "';";
        }

        return '';
    }

    public function inicializarPenalidades(array $arParametros)
    {
        Sessao::write('arValores', array());

        # Monta o HTML da tabela
        $stLink  = "javascript: executaFuncaoAjax('excluirPenalidade');";
        $obLista = $this->preencheListaPenalidades('EXCLUIR', array(), $stLink);
        $obLista->montaHTML();

        # Remove caracteres inválidos das strings em Javascript
        $stHTML  = $stHTML . $this->quoteJavaScript($obLista->getHTML());

        return "$('spnListaPenalidades').innerHTML = '" . $stHTML . "';";
    }

    /**
     * Remove caracteres inválidos das strings dentro de expressões em Javascript.
     * @param  string $st string de entrada
     * @return string saída formatada
     */
    private function quoteJavaScript($stJS)
    {
        $stJS = str_replace('  ', '', $stJS);
        $stJS = str_replace("\n", '', $stJS);
        $stJS = str_replace("'", "\\'", $stJS);

        return $stJS;
    }

    /**
     * Inclui penalidade na lista de penalidades da infração.
     * @param array $arParametros parâmetros
     */
    public function incluirPenalidade(array $arParametros)
    {
        $stHTML = '';

        $arPenalidades = Sessao::read('arValores');

        # Cria novo array de penalidades
        if (!is_array($arPenalidades)) {
            $arPenalidades = array();
        }

        $obTFISPenalidade = new TFISPenalidade();
        $obTFISPenalidade->setDado('cod_penalidade', $arParametros['inCodPenalidade']);

        # Penalidade já inserida na tabela?
        foreach ($arPenalidades as $inIndex => $arCampos) {
            if ($arCampos['cod_penalidade'] == $arParametros['inCodPenalidade']) {
                $stMensagem = "@Penalidade já informada. (" . $arParametros['inCodPenalidade'] . ")";

                return "alertaAviso('" . $stMensagem . "','form','erro','" . Sessao::getId() . "');\n";
            }
        }

        # Busca todos os dados necessários sobre Penalidade
        $stCondicao = 'cod_penalidade = ' . $arParametros['inCodPenalidade'];
        $stOrdem    = 'cod_penalidade DESC';
        $obTFISPenalidade->recuperaListaPenalidades($rsPenalidade, $stCondicao, $stOrdem);

        # Cria novo elemento no array com os dados da penalidade.
        if (!$rsPenalidade->eof()) {
            $inPos = count($arPenalidades);
            $arPenalidades[$inPos]['cod_tipo_penalidade'] = $rsPenalidade->getCampo('cod_tipo_penalidade');
            $arPenalidades[$inPos]['descricao'] = $rsPenalidade->getCampo('descricao');
            $arPenalidades[$inPos]['cod_penalidade'] = $rsPenalidade->getCampo('cod_penalidade');
            $arPenalidades[$inPos]['nom_penalidade'] = $rsPenalidade->getCampo('nom_penalidade');
            Sessao::write('arValores', $arPenalidades);
        } else {
            # Penalidade não encontrada
            $stMensagem = "@Código da Penalidade inválido (" . $arParametros['inCodPenalidade'] . ")";

            return "alertaAviso('" . $stMensagem . "','form','erro','" . Sessao::getId() . "');\n";
        }

        # Monta o HTML da tabela
        $stLink  = "javascript: executaFuncaoAjax('excluirPenalidade');";

        if ( $arPenalidades && count( $arPenalidades ) ) {
            $stHTML .= $this->montaListaHidden('arPenalidades', $arPenalidades);
        }

        # bug: 'excluir' minúsculo não funciona.
        $obLista = $this->preencheListaPenalidades('EXCLUIR', $arPenalidades, $stLink);
        $obLista->montaHTML();

        # Remove caracteres inválidos das strings em Javascript
        $stHTML = $stHTML . $this->quoteJavaScript($obLista->getHTML());

        return "$('spnListaPenalidades').innerHTML = '" . $stHTML . "';";
    }

    /**
     * Cria objeto lista com as penalidades.
     * @param  string    $stAcao        a ação a ser realizada
     * @param  RecordSet $rsPenalidades RecordSet das penalidades
     * @param  string    $stLink        o link para a ação associada
     * @return Lista     o objeto lista
     */
    private function montaListaPenalidades($stAcao, $rsPenalidades, $stLink = '')
    {
        # Lista penalidades
        $obLista = new Lista();
        $obLista->setMostraPaginacao( true );
        $obLista->setTitulo('Lista de Penalidades');
        $obLista->setRecordSet($rsPenalidades);

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('&nbsp;');
        $obLista->ultimoCabecalho->setWidth(5);
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('Tipo');
        $obLista->ultimoCabecalho->setWidth(45);
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('Penalidade');
        $obLista->ultimoCabecalho->setWidth(50);
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('&nbsp;');
        $obLista->ultimoCabecalho->setWidth(10);
        $obLista->commitCabecalho();

        #
        # Dados
        #
        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento('ESQUERDA');
        $obLista->ultimoDado->setCampo('[cod_tipo_penalidade] - [descricao]');
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento('ESQUERDA');
        $obLista->ultimoDado->setCampo('[cod_penalidade] - [nom_penalidade]');
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao($stAcao);
        $obLista->ultimaAcao->setFuncaoAjax(true);
        $obLista->ultimaAcao->setLink($stLink);
        $obLista->ultimaAcao->addCampo('', 'cod_penalidade');
        $obLista->commitAcao();

        return $obLista;
    }

    public function excluirPenalidade(array $arParametros)
    {
        $inCodPenalidade = $arParametros['cod_penalidade'];
        $arPenalidades   = Sessao::read('arValores');
        $boEncontrado    = false;
        $stHTML          = "";
        $i               = 0;

        # Encontra o elemento da lista.
        foreach ($arPenalidades as $arCampos) {
            if ($arCampos['cod_penalidade'] == $inCodPenalidade) {
                $boEncontrado = true;
                break;
            }

            $i++;
        }

        if ($boEncontrado) {
            # Remove o elemento da lista.
            array_splice( $arPenalidades, $i, 1 );
        }

        Sessao::write('arValores', $arPenalidades);

        # Monta o HTML da tabela
        $stLink  = "javascript: executaFuncaoAjax('excluirPenalidade');";
        $obLista = $this->preencheListaPenalidades( 'EXCLUIR', $arPenalidades, $stLink );
        $obLista->montaHTML();

        # Monta elementos hidden para lista.
        if ($arPenalidades && count($arPenalidades)) {
            $stHTML .= $this->montaListaHidden('arPenalidades', $arPenalidades);
        }

        # Remove caracteres inválidos das strings em Javascript
        $stHTML  = $stHTML . $this->quoteJavaScript($obLista->getHTML());

        return "$('spnListaPenalidades').innerHTML = '" . $stHTML . "';";
    }

    /**
     * Retorna resultado de busca por infrações.
     * @arParametros array arParametros array de parâmetros recebidos
     * @return RecordSet
     */
    public function searchInfracoes(array $arParametros)
    {
        return $this->obController->getListaInfracoes($arParametros['inTipoFiscalizacao'], $arParametros['stNomInfracao']);
    }

    public function searchInfracoesBaixa(array $arParametros)
    {
        return $this->obController->getListaInfracoesBaixa($arParametros['inTipoFiscalizacao'], $arParametros['stNomInfracao']);
    }

    /**
     * Retorna dados da infração
     * @param integer $inCodInfracao código da infração
     */
    public function getInfracao($inCodInfracao)
    {
        return $this->obController->getInfracao($inCodInfracao);
    }
}
