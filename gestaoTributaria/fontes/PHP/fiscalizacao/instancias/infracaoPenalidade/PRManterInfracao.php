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
 * Página de processamento de Infração
 * Data de Criação: 05/08/2008

 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Pedro Vaz de Mello de Medeiros
 * @ignore

 $Id: PRManterInfracao.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso: uc-05.07.06
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

include_once CAM_GT_FIS_NEGOCIO . 'RFISInfracao.class.php';
include_once CAM_GT_FIS_VISAO . 'VFISInfracao.class.php';
include_once CAM_GT_FIS_MAPEAMENTO.'TFISInfracaoBaixa.class.php';
include_once CAM_GT_FIS_MAPEAMENTO.'TFISInfracaoBaixaProcesso.class.php';

class PRManterInfracao
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
     * Identificação da Infração
     * @access private
     */
    private $inCodInfracao;

    /**
     * Método construtor
     * @param Regra $obRegra objeto da regra de negócios
     */
    private $obVisao;

    /**
     * Método construtor
     * @param Visao $obVisao objeto da visao
     */

    public function __construct($obRegra)
    {
        $stPrograma    = 'ManterInfracao';
        $this->pgFilt  = 'FL' . $stPrograma . '.php';
        $this->pgList  = 'LS' . $stPrograma . '.php';
        $this->pgForm  = 'FM' . $stPrograma . '.php';
        $this->pgProc  = 'PR' . $stPrograma . '.php';
        $this->pgOcul  = 'OC' . $stPrograma . '.php';
        $this->pgJS    = 'JS' . $stPrograma . '.php';
        $this->obRegra = $obRegra;
        $this->obVisao = new VFISInfracao(null);
    }

    /**
     * Executa instrução recebida do Menu
     * @arParametros array $param array com os parâmetros recebidos por $_REQUEST
     */
    public function executarAcao(array $arParametros)
    {
        Sessao::setTrataExcecao(true);

        $stMetodo = $arParametros['stAcao'];
        $this->inCodInfracao = $arParametros['inCodInfracao'];
        $this->$stMetodo($arParametros);

        Sessao::encerraExcecao();
    }

    private function retornar($stAcao, &$obErro, $pgDestino)
    {
        if ($obErro->ocorreu()) {
            # Retorna aviso de erro.
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()), "n_$stAcao", 'erro');
        } else {
            # Tudo OK
            SistemaLegado::alertaAviso($pgDestino, $this->inCodInfracao, $stAcao, "aviso", Sessao::getId(), '../');
        }
    }

    /**
     * Inclui nova infração
     * @arParametros array $param array com os parâmetros recebidos por $_REQUEST
     */
    private function incluir(array $arParametros)
    {
        $stNomInfracao         = $arParametros['stNomInfracao'];
        $boCominar             = $arParametros['boCominar'];
        $inCodTipoFiscalizacao = $arParametros['inTipoFiscalizacao'];
        $inCodNorma            = $arParametros['inCodNorma'];
        $arPenalidades         = $arParametros['arPenalidades'];

        $obErro = $this->obRegra->incluirInfracao($this->inCodInfracao, $stNomInfracao, $boCominar, $inCodTipoFiscalizacao, $inCodNorma, $arPenalidades);
        $this->retornar(__FUNCTION__, $obErro, $this->pgForm);
    }

    /**
     * Exclui infração existente
     * @arParametros array $param array com os parâmetros recebidos por $_REQUEST
     */
    private function excluir(array $arParametros)
    {
        $stCaminho = $this->pgList . '?' . Sessao::getID() . '&stAcao=excluir';

        $obErro = $this->obRegra->excluirInfracao($this->inCodInfracao);
        if (is_object($obErro)) {
            $this->retornar(__FUNCTION__, $obErro, $stCaminho);
        }
    }

    private function baixar(array $arParametros)
    {
        $obTFISInfracaoBaixa = new TFISInfracaoBaixa;
        Sessao::getTransacao()->setMapeamento( $obTFISInfracaoBaixa );
        $obTFISInfracaoBaixa->setDado( "cod_infracao", $this->inCodInfracao );
        $obTFISInfracaoBaixa->setDado( "motivo", $arParametros["stMotivo"] );
        $obErro = $obTFISInfracaoBaixa->inclusao();
        if ( $arParametros["stChaveProcesso"] && !$obErro->ocorreu() ) {
            $arProcesso = explode( "/", $arParametros["stChaveProcesso"] );
            $obTFISInfracaoBaixa->recuperaMaxTimestampInfracao( $rsInfracao, $arParametros["inCodInfracao"] );
            $obTFISInfracaoBaixaProcesso = new TFISInfracaoBaixaProcesso;
            $obTFISInfracaoBaixaProcesso->setDado( "cod_infracao", $this->inCodInfracao );
            $obTFISInfracaoBaixaProcesso->setDado( "timestamp_inicio", $rsInfracao->getCampo( "timestamp_inicio" ) );
            $obTFISInfracaoBaixaProcesso->setDado( "cod_processo", $arProcesso[0] );
            $obTFISInfracaoBaixaProcesso->setDado( "ano_exercicio", $arProcesso[1] );
            $obErro = $obTFISInfracaoBaixaProcesso->inclusao();
        }

        SistemaLegado::alertaAviso( $this->pgList, "Baixar Infração ".$this->inCodInfracao, 'baixar', "aviso", Sessao::getId(), '../' );
    }

    private function reativar(array $arParametros)
    {
        $obTFISInfracaoBaixa = new TFISInfracaoBaixa;
        Sessao::getTransacao()->setMapeamento( $obTFISInfracaoBaixa );
        $obTFISInfracaoBaixa->setDado( "cod_infracao", $this->inCodInfracao );
        $obTFISInfracaoBaixa->setDado( "timestamp_inicio", $arParametros["stTimestampInicio"] );
        $obTFISInfracaoBaixa->setDado( "timestamp_termino", date( "Y-m-d H:i:s.u" ) );

        $obErro = $obTFISInfracaoBaixa->alteracao();

        SistemaLegado::alertaAviso( $this->pgList, "Reativar Infração ".$this->inCodInfracao, 'reativar', "aviso", Sessao::getId(), '../' );
    }
    /**
     * Altera infração existente
     * @arParametros array $param array com os parâmetros recebidos por $_REQUEST
     */
    private function alterar(array $arParametros)
    {
        $stNomInfracao         = $arParametros['stNomInfracao'];
        $boCominar             = $arParametros['boCominar'];
        $inCodTipoFiscalizacao = $arParametros['inTipoFiscalizacao'];
        $inCodNorma            = $arParametros['inCodNorma'];
        $arPenalidades         = $arParametros['arPenalidades'];

        $obErro = $this->obRegra->alterarInfracao($this->inCodInfracao, $stNomInfracao, $boCominar, $inCodTipoFiscalizacao, $inCodNorma, $arPenalidades);
        $this->retornar(__FUNCTION__, $obErro, $this->pgList);
    }
}

# Instanciação dos objetos usados
$obRegra = new RFISInfracao();
$obVisao = new PRManterInfracao($obRegra);
$obVisao->executarAcao($_REQUEST);

?>
