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
 * Classe de Regra de Infração
 * Data de Criação: 04/08/2008

 * @author Analista    : Heleno Menezes dos Santos
 * @author Programador : Pedro Vaz de Mello de Medeiros

 * @package URBEM
 * @subpackage Regra

 $Id: RFISInfracao.class.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso:
 */

include_once CAM_GT_FIS_MAPEAMENTO . 'TFISInfracao.class.php';
include_once CAM_GT_FIS_MAPEAMENTO . 'TFISInfracaoPenalidade.class.php';

class RFISInfracao
{
    /**
     * Invoca classe de mapeamento com chamada e critério em uma só etapa.
     * @param  string    $stMapeamento o nome da classe de mapeamento
     * @param  string    $stMetodo     o método invocado para a classe de mapeamento
     * @param  string    $stCriterio   o critério que delimita a busca
     * @return RecordSet
     */
    protected function callMapeamento($stMapeamento, $stMetodo, $stCriterio = '', $stOrdem = '')
    {
        $obMapeamento = new $stMapeamento();
        $obMapeamento->$stMetodo($obRecordSet, $stCriterio, $stOrdem);

        return $obRecordSet;
    }

    public function getInfracao($inCodInfracao)
    {
        $stMapeamento = 'TFISInfracao';
        $stMetodo     = 'recuperaTodos';
        $stCriterio   = ' WHERE cod_infracao = ' . $inCodInfracao;

        return $this->callMapeamento($stMapeamento, $stMetodo, $stCriterio);
    }

    public function getListaInfracoesBaixa($inCodTipoInfracao = '', $stNomDescricao = '')
    {
        $stMapeamento = 'TFISInfracao';
        $stMetodo     = 'recuperaListaInfracoesBaixa';
        $stCriterio   = '';
        $stOrdem      = 'cod_infracao ASC';
        $stSeparador  = '';

        if ($inCodTipoInfracao) {
            $stCriterio .= 'cod_tipo_fiscalizacao = ' . $inCodTipoInfracao;
            $stSeparador = ' AND ';
        }

        if ($stNomDescricao) {
            $stCriterio .= $stSeparador . 'nom_infracao ILIKE \'%' . addslashes($stNomDescricao) . '%\'';
        }

        return $this->callMapeamento($stMapeamento, $stMetodo, $stCriterio, $stOrdem);
    }

    public function getListaInfracoes($inCodTipoInfracao = '', $stNomDescricao = '')
    {
        $stMapeamento = 'TFISInfracao';
        $stMetodo     = 'recuperaListaInfracoes';
        $stCriterio   = '';
        $stOrdem      = 'cod_infracao ASC';
        $stSeparador  = '';

        if ($inCodTipoInfracao) {
            $stCriterio .= 'cod_tipo_fiscalizacao = ' . $inCodTipoInfracao;
            $stSeparador = ' AND ';
        }

        if ($stNomDescricao) {
            $stCriterio .= $stSeparador . 'nom_infracao ILIKE \'%' . addslashes($stNomDescricao) . '%\'';
        }

        return $this->callMapeamento($stMapeamento, $stMetodo, $stCriterio, $stOrdem);
    }

    public function getListaPenalidadesPorInfracao($inCodInfracao)
    {
        $stMapeamento = 'TFISPenalidade';
        $stMetodo     = 'recuperaListaPenalidadesPorInfracao';
        $stCriterio   = 'infracao_penalidade.cod_infracao = ' . $inCodInfracao;
        $stOrdem      = 'infracao_penalidade.cod_penalidade ASC';

        return $this->callMapeamento($stMapeamento, $stMetodo, $stCriterio, $stOrdem);
    }

    /**
     * Retorna todos dados em infracao_penalidade
     * @param  integer   $inCodInfracao
     * @param  integer   $inCodPenalidade
     * @return RecordSet
     */
    public function getAutoInfracao($inCodInfracao = '', $inCodPenalidade = '',$inCodProcesso = '')
    {
        $stMapeamento = 'TFISPenalidade';
        $stMetodo     = 'recuperaListaInfracaoPenalidadeNotificacao';
        $stCriterio   = '';

        if (intval($inCodInfracao) > 0) {
            $arCriterio[] = " fip.cod_infracao = " . $inCodInfracao . "\n";
        }

        if (intval($inCodPenalidade) > 0) {
            $arCriterio[] = " fip.cod_penalidade = " . $inCodPenalidade . "\n";
        }

        if (intval($inCodProcesso) > 0) {
            $arCriterio[] = " fai.cod_processo = " . $inCodProcesso . "\n";
        } else {
           $arCriterio[] = " fai.cod_processo = 0 \n";
        }

        $inCount = count($arCriterio);
        for ($i = 0; $i < $inCount; $i++) {
            $stCriterio.= $arCriterio[$i];
            $j = $i + 1;
            if ($arCriterio[$j]) {
                $stCriterio.= " AND ";
            }
        }

        return $this->callMapeamento($stMapeamento, $stMetodo, $stCriterio);
    }

    /**
     * Apaga todas as infrações_penalidades recebidas em um array.
     */
    private function apagarInfracoesPenalidades($inCodInfracao, array $arPenalidades, &$obErro, &$boTransacao = '')
    {
        $obTFISInfracaoPenalidade = new TFISInfracaoPenalidade();

        # Exclui todo infração_penalidade que bate.
        foreach ($arPenalidades as $inCodPenalidade) {
            if ($obErro->ocorreu())
                break;

            $obTFISInfracaoPenalidade->setDado('cod_infracao', $inCodInfracao);
            $obTFISInfracaoPenalidade->setDado('cod_penalidade', $inCodPenalidade);
            $obErro = $obTFISInfracaoPenalidade->exclusao($boTransacao);
        }
    }

    private function incluirInfracoesPenalidades($inCodInfracao, $arPenalidades, &$obErro, &$boTransacao = '')
    {
        $obTFISInfracaoPenalidade = new TFISInfracaoPenalidade();

        # Exclui todo infração_penalidade que bate.
        foreach ($arPenalidades as $inCodPenalidade) {
            if ($obErro->ocorreu())
                break;

            $obTFISInfracaoPenalidade->setDado('cod_infracao', $inCodInfracao);
            $obTFISInfracaoPenalidade->setDado('cod_penalidade', $inCodPenalidade);
            $obErro = $obTFISInfracaoPenalidade->inclusao($boTransacao);
        }
    }

    /**
     * Realiza a operação de inclusão ou alteração especificada.
     * @access private
     * @param  integer    $inCodPenalidade     o código da penalidade
     * @param  integer    $inCodTipoPenalidade o código do tipo da penalidade
     * @param  integer    $inCodNorma          o código da norma
     * @param  string     $stNomPenalidade     o nome da penalidade
     * @param  integer    $inCodIndicador      o código do indicador econômico
     * @param  boolean    $boValorFixo         se a penalidade tem valor fixo ou não
     * @param  float      $fiValor             o valor inicial (ou único) da multa
     * @param  null|float $fiValorFinal        o valor final da multa se aplicável.
     * @return Erro
     */
    private function acaoInfracao( $stOperacao, &$inCodInfracao, $stNomInfracao = '', $boCominar = '', $inCodTipoFiscalizacao = '', $inCodNorma = '', $arPenalidades = array())
    {
        $obTFISInfracao           = new TFISInfracao();
        $obTFISInfracaoPenalidade = new TFISInfracaoPenalidade();
        $obTransacao              = new Transacao();
        $boFlagTransacao          = false;
        $boTransacao              = '';
        $arCodPenalidadesIni      = array();
        $arCodPenalidades         = array();

        # Não permite inclusão de infração sem penalidades.
        if (count($arPenalidades) == 0) {
            $obErro = new Erro();
            $obErro->setDescricao('Necessário incluir pelo menos uma Penalidade');

            return $obErro;
        }

        # Gera array de código de penalidade a partir de infracao_penalidade.
        foreach ($arPenalidades as $arPenalidade) {
            $arCodPenalidades[] = $arPenalidade['cod_penalidade'];
        }

        # Inicia nova transação
        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        # Novo número de código de penalidade para inclusão
        if ((!$obErro->ocorreu()) && ($stOperacao == 'inclusao')) {
            $inCodInfracao = null;
            $obErro = $obTFISInfracao->proximoCod($inCodInfracao);
        }

        if (!$obErro->ocorreu()) {
            # Infração
            $obTFISInfracao->setDado('cod_infracao', $inCodInfracao);
            $obTFISInfracao->setDado('nom_infracao', $stNomInfracao);
            $obTFISInfracao->setDado('comminar', $boCominar);
            $obTFISInfracao->setDado('cod_tipo_fiscalizacao', $inCodTipoFiscalizacao);
            $obTFISInfracao->setDado('cod_norma', $inCodNorma);
            $obErro = $obTFISInfracao->$stOperacao($boTransacao);
        }

        # Inclui novas penalidades e exclui penalidades removidas.
        if (!$obErro->ocorreu()) {
            $this->incluirInfracoesPenalidades($inCodInfracao, $arCodPenalidades, $obErro, $boTransacao);
        }

        # Termina transação
        $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $obTFISInfracao);

        return $obErro;
    }

    /**
     * Incluir infração
     * @param  integer $stNomInfracao         o nome da infração
     * @param  boolean $boCominar             cominar ou não
     * @param  integer $inCodTipoFiscalizacao o código do tipo de fiscalização
     * @param  integer $inCodNorma            o código da norma
     * @param  array   $arPenalidades         lista de penalidades associadas com esta infração
     * @return Erro
     */
    public function incluirInfracao(&$inCodInfracao, $stNomInfracao, $boCominar, $inCodTipoFiscalizacao, $inCodNorma, $arPenalidades)
    {
        return $this->acaoInfracao('inclusao', $inCodInfracao, $stNomInfracao, $boCominar, $inCodTipoFiscalizacao, $inCodNorma, $arPenalidades);
    }

    /**
     * Excluir infração
     * @param  integer $inCodPenalidade o código da penalidade
     * @return Erro
     */
    public function excluirInfracao($inCodInfracao)
    {
        $obTFISInfracao           = new TFISInfracao();
        $obTFISInfracaoPenalidade = new TFISInfracaoPenalidade();
        $obTransacao              = new Transacao();
        $boFlagTransacao          = false;
        $boTransacao              = '';
        $arInfracoesPenalidades   = array();

        $rsAutoInfracao = $this->getAutoInfracao($inCodInfracao,'');

        if ($rsAutoInfracao->inNumLinhas > 0) {
            $pgDestino = "LSManterInfracao.php?" . Sessao::getID() . "&stAcao=excluir";

            return SistemaLegado::alertaAviso($pgDestino, 'Infração('.$inCodInfracao.') já vinculada a um Documento de Notificação!', 'aviso', 'aviso', Sessao::getId(), '../');
        }

        # Inicia transação
        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        if (!$obErro->ocorreu()) {
            # Retorna todas as infracoes_penalidades com a mesma cod_infracao.
            $stCondicao = ' WHERE cod_infracao = ' . $inCodInfracao;
            $obErro = $obTFISInfracaoPenalidade->recuperaTodos($rsInfracoesPenalidades, $stCondicao);
        }

        if (!$obErro->ocorreu()) {
            # Exclui toda infração_penalidade que pertence a esta infração
            $arInfracoesPenalidades = $rsInfracoesPenalidades->getElementos();
            $arCodPenalidades = array();

            # Gera array de penalidade de infracao_penalidade.
            foreach ($arInfracoesPenalidades as $arInfracaoPenalidade) {
                $arCodPenalidades[] = $arInfracaoPenalidade['cod_penalidade'];
            }

            $this->apagarInfracoesPenalidades($inCodInfracao, $arCodPenalidades, $obErro, $boTransacao);
        }

        if (!$obErro->ocorreu()) {
            $obTFISInfracao->setDado('cod_infracao', $inCodInfracao);
            $obErro = $obTFISInfracao->exclusao($boTransacao);
        }

        $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $obTFISInfracao);

        return $obErro;
    }

    /**
     * Altera infracao
     * @param  integer $inCodInfracao         o código da infração
     * @param  integer $stNomInfracao         o nome da infração
     * @param  boolean $boCominar             cominar ou não
     * @param  integer $inCodTipoFiscalizacao o código do tipo de fiscalização
     * @param  integer $inCodNorma            o código da norma
     * @param  array   $arPenalidades         lista de penalidades associadas com esta infração
     * @return Erro
     */
    public function alterarInfracao($inCodInfracao, $stNomInfracao, $boCominar, $inCodTipoFiscalizacao, $inCodNorma, $arPenalidades)
    {
        return $this->acaoInfracao('alteracao', $inCodInfracao, $stNomInfracao, $boCominar, $inCodTipoFiscalizacao, $inCodNorma, $arPenalidades);
    }
}
