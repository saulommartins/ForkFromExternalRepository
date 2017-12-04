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
 * Página de processamento de Penalidade
 * Data de Criação: 29/07/2008

 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Pedro Vaz de Mello de Medeiros
 * @ignore

 $Id: PRManterPenalidade.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso: uc-05.07.05
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

include_once CAM_GT_FIS_VISAO.'VFISPenalidade.class.php';
include_once CAM_GT_FIS_NEGOCIO.'RFISPenalidade.class.php';
include_once CAM_GT_FIS_MAPEAMENTO.'TFISPenalidadeBaixa.class.php';
include_once CAM_GT_FIS_MAPEAMENTO.'TFISPenalidadeBaixaProcesso.class.php';

class PRManterPenalidade
{
    /**
     * Pagina de Filtro
     * @access private
     */
    private $pgFilt;

    /**
     * Página de Lista
     * @access private
     */
    private $pgList;

    /**
     * Página de Formulário
     * @access private
     */
    private $pgForm;

    /**
     * Página Oculta
     * @access private
     */
    private $pgOcul;

    /**
     * Página de Javascript
     * @access private
     */
    private $pgJS;

    /**
     * Regra de Negócios
     * @access private
     */
    private $obRegra;

    /**
     * Identificação da Penalidade
     * @access private
     */
    private $inCodPenalidade;

    /**
     * Método construtor
     * @access private
     */
    public function __construct($obRegra)
    {
        $stPrograma = 'ManterPenalidade';
        $this->pgFilt = 'FL' . $stPrograma . '.php';
        $this->pgList = 'LS' . $stPrograma . '.php';
        $this->pgForm = 'FM' . $stPrograma . '.php';
        $this->pgProc = 'PR' . $stPrograma . '.php';
        $this->pgOcul = 'OC' . $stPrograma . '.php';
        $this->pgJS   = 'JS' . $stPrograma . '.js';
        $this->obRegra = $obRegra;
    }

    /**
     * Executa instrução recebida do Menu
     * @arParametros array $param array com os parâmetros recebidos por $_REQUEST
     */
    public function executarAcao(array $arParametros)
    {
        Sessao::setTrataExcecao(true);

        $stMetodo = $arParametros['stAcao'];
        $this->inCodPenalidade = $arParametros['inCodPenalidade'];
        $this->$stMetodo($arParametros);

        Sessao::encerraExcecao();
    }

    private function retornar($stAcao, &$obErro, $pgDestino)
    {
        if ($obErro->ocorreu()) {
            # Retorna aviso de erro.
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()), "n_$stAcao", 'erro');
        } else {
            # OK
            SistemaLegado::alertaAviso($pgDestino, $this->inCodPenalidade, $stAcao, 'aviso', Sessao::getId(), '../');
        }
    }

    /**
     * Inclui nova penalidade
     * @arParametros array $param array com os parâmetros recebidos por $_REQUEST
     */
    private function incluir(array $arParametros)
    {
        $obVisao = new VFISPenalidade(null);

        $inCodTipoPenalidade = $arParametros['inCodTipoPenalidade'];
        $inCodNorma          = $arParametros['inCodNorma'];
        $stNomPenalidade     = $arParametros['stNomPenalidade'];
        $inCodDocumento      = $arParametros['stCodDocumentoTxt'];
        $inCodTipoDocumento  = '';

        if ($inCodDocumento) {
            $inCodTipoDocumento = $obVisao->getTipoDocumento($inCodDocumento);
        }

        if (isset($arParametros['inCodIndicador'])) {
            $inCodIndicador = $arParametros['inCodIndicador'];

            if ($inCodIndicador == '') {
                $arErro[] = 'Indicador Econômico Inválido!()';
            }
        }

        if (isset($arParametros['inCodFuncao'])) {
            $stCodFuncao = $arParametros['inCodFuncao'];

            if ($stCodFuncao == '') {
                $arErro[] = 'Fórmula de Cálculo Inválida!()';
            }
        }

        if (isset($arParametros['inCodUnidade'])) {
            $stCodUnidade = $arParametros['inCodUnidade'];

            if ($stCodUnidade == '') {
                $arErro[] = 'Unidade de Medida Inválida!()';
            }
        }

        if (isset( $arParametros['boConceder'])) {
            $boConceder = $arParametros['boConceder'];

            if ($boConceder == 'S') {

                if (isset( $arParametros['inDia'])) {
                    $inDia 	       = $arParametros['inDia'];
                    $inDesconto    = $arParametros['inDesconto'];
                    $stCodDesconto = $arParametros['inCodDesconto'];
                } else {
                    $arErro[] = 'Defina um ou mais Prazo de Antecipação com o Valor de Desconto!()';
                }
            }
        }

        if ($arErro) {
            $arNewErros = array_reverse($arErro);

            foreach ($arNewErros as $stMensagem) {
                SistemaLegado::exibeAviso($stMensagem, 'alerta', 'alerta');
            }
        } else {
            $obErro = $this->obRegra->incluirPenalidade($this->inCodPenalidade, $inCodTipoPenalidade, $inCodNorma, $stNomPenalidade, $inCodIndicador, $stCodFuncao, $stCodUnidade, $boConceder, $inDia, $inDesconto, $stCodDesconto, $inCodTipoDocumento, $inCodDocumento);
            $this->retornar(__FUNCTION__, $obErro, $this->pgForm);
        }
    }

    private function reativar(array $arParametros)
    {
        $obTFISPenalidadeBaixa = new TFISPenalidadeBaixa;
        Sessao::getTransacao()->setMapeamento( $obTFISPenalidadeBaixa );
        $obTFISPenalidadeBaixa->setDado( "cod_penalidade", $arParametros["inCodPenalidade"] );
        $obTFISPenalidadeBaixa->setDado( "timestamp_inicio", $arParametros["stTimestampInicio"] );
        $obTFISPenalidadeBaixa->setDado( "timestamp_termino", date( "Y-m-d H:i:s.u" ) );
        $obErro = $obTFISPenalidadeBaixa->alteracao();

        SistemaLegado::alertaAviso( $this->pgList, "Reativar Penalidade ".$this->inCodPenalidade, 'reativar', "aviso", Sessao::getId(), '../' );
    }

    private function baixar(array $arParametros)
    {
        $obTFISPenalidadeBaixa = new TFISPenalidadeBaixa;
        Sessao::getTransacao()->setMapeamento( $obTFISPenalidadeBaixa );
        $obTFISPenalidadeBaixa->setDado( "cod_penalidade", $arParametros["inCodPenalidade"] );
        $obTFISPenalidadeBaixa->setDado( "motivo", $arParametros["stMotivo"] );
        $obErro = $obTFISPenalidadeBaixa->inclusao();
        if ( $arParametros["stChaveProcesso"] && !$obErro->ocorreu() ) {
            $obTFISPenalidadeBaixa->recuperaUltimoTimestamp( $rsDados, $arParametros["inCodPenalidade"] );
            $arProcesso = explode( "/", $arParametros["stChaveProcesso"] );
            $obTFISPenalidadeBaixaProcesso = new TFISPenalidadeBaixaProcesso;
            $obTFISPenalidadeBaixaProcesso->setDado( "cod_penalidade", $arParametros["inCodPenalidade"] );
            $obTFISPenalidadeBaixaProcesso->setDado( "timestamp_inicio", $rsDados->getCampo("timestamp_inicio") );
            $obTFISPenalidadeBaixaProcesso->setDado( "cod_processo", $arProcesso[0] );
            $obTFISPenalidadeBaixaProcesso->setDado( "ano_exercicio", $arProcesso[1] );
            $obErro = $obTFISPenalidadeBaixaProcesso->inclusao();
        }

        SistemaLegado::alertaAviso( $this->pgList, "Baixar Penalidade ".$this->inCodPenalidade, 'baixar', "aviso", Sessao::getId(), '../' );
    }

    private function excluir(array $arParametros)
    {
        $stCaminho = $this->pgList . "?" . Sessao::getID() . "&stAcao=excluir";
        $obErro = $this->obRegra->excluirPenalidade($this->inCodPenalidade);
        if (is_object($obErro)) {
            $this->retornar(__FUNCTION__, $obErro, $stCaminho);
        }
    }

    /**
     * Altera penalidade existente
     * @arParametros array $param array com os parâmetros recebidos por $_REQUEST
     */
    private function alterar(array $arParametros)
    {
        $obVisao = new VFISPenalidade(null);

        $inCodTipoPenalidade = $arParametros['inCodTipoPenalidade'];
        $inCodNorma          = $arParametros['inCodNorma'];
        $stNomPenalidade     = $arParametros['stNomPenalidade'];
        $inCodDocumento      = $arParametros['stCodDocumentoTxt'];
        $inCodTipoDocumento  = '';

        if ($inCodDocumento) {
            $inCodTipoDocumento = $obVisao->getTipoDocumento($inCodDocumento);
        }

        if (isset($arParametros['inCodIndicador'])) {
            $inCodIndicador  = $arParametros['inCodIndicador'];

            if ($inCodIndicador == '') {
                $arErro[] = "Indicador Econômico Inválido!()";
            }
        }

        if (isset($arParametros['inCodFuncao'])) {
            $stCodFuncao = $arParametros['inCodFuncao'];

            if ($stCodFuncao == '') {
                $arErro[] = 'Fórmula de Cálculo Inválida!()';
            }
        }

        if (isset( $arParametros['inCodUnidade'])) {
            $stCodUnidade = $arParametros['inCodUnidade'];

            if ($stCodUnidade == '') {
                $arErro[] = 'Unidade de Medida Inválida!()';
            }
        }

        if (isset( $arParametros['boConceder'])) {
            $boConceder = $arParametros['boConceder'];

            if ($boConceder == 'S') {

                if (isset($arParametros['inDia'])) {
                    $inDia         = $arParametros['inDia'];
                    $inDesconto    = $arParametros['inDesconto'];
                    $stCodDesconto = $arParametros['inCodDesconto'];
                } else {
                    $arErro[] = 'Defina um ou mais Prazo de Antecipação com o Valor de Desconto!()';
                }
            }
        }

        if ($arErro) {
            $arNewErros = array_reverse($arErro);

            foreach ($arNewErros as $stMensagem) {
                SistemaLegado::exibeAviso($stMensagem, 'alerta', 'alerta');
            }
        } else {
            $obErro = $this->obRegra->alterarPenalidade($this->inCodPenalidade, $inCodTipoPenalidade, $inCodNorma, $stNomPenalidade, $inCodIndicador, $stCodFuncao, $stCodUnidade, $boConceder, $inDia, $inDesconto, $stCodDesconto, $inCodTipoDocumento, $inCodDocumento);
            $this->retornar(__FUNCTION__, $obErro, $this->pgList);
        }
    }
}

# Instanciação dos objetos usados
$obRegra = new RFISPenalidade();
$obVisao = new PRManterPenalidade($obRegra);
$obVisao->executarAcao($_REQUEST);
