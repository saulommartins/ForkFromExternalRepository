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
 * Classe de Regra de Penalidade
 * Data de Criação: 30/07/2008

 * @author Analista    : Heleno Menezes dos Santos
 * @author Programador : Pedro Vaz de Mello de Medeiros

 * @package URBEM
 * @subpackage Regra

 $Id: RFISPenalidade.class.php 65763 2016-06-16 17:31:43Z evandro $

 * Casos de uso:
 */

include_once CAM_GT_FIS_MAPEAMENTO . 'TFISPenalidade.class.php';
include_once CAM_GT_FIS_MAPEAMENTO . 'TFISPenalidadeMulta.class.php';
include_once CAM_GT_FIS_MAPEAMENTO . 'TFISPenalidadeDesconto.class.php';
include_once CAM_GT_FIS_MAPEAMENTO . 'TFISPenalidadeDocumento.class.php';
include_once CAM_GT_FIS_MAPEAMENTO . 'TFISInfracaoPenalidade.class.php';
include_once CAM_GT_FIS_MAPEAMENTO . 'TFISAutoInfracao.class.php';

class RFISPenalidade
{
    /**
     * Invoca classe de mapeamento com chamada e critério em uma só etapa.
     * @param  string    $stMapeamento o nome da classe de mapeamento
     * @param  string    $stMetodo     o método invocado para a classe de mapeamento
     * @param  string    $stCriterio   o critério que delimita a busca
     * @return RecordSet
     */
    protected function chamaMapeamento($stMapeamento, $stMetodo, $stCriterio = '', $stOrdem = '')
    {
        $obMapeamento = new $stMapeamento();
        $obMapeamento->$stMetodo($obRecordSet, $stCriterio, $stOrdem);

        return $obRecordSet;
    }

    /**
     * Retorna a descrição associada com a penalidade.
     * @param null|integer inChave
     */
    public function getDescricao($inCodTipoPenalidade, $inCodPenalidade = null)
    {
        $stCriterio = ' penalidade.cod_tipo_penalidade = ' . $inCodTipoPenalidade;

        if ($inCodPenalidade) {
            $stCriterio.= ' AND penalidade.cod_penalidade = ' . $inCodPenalidade;
        }

        return $this->chamaMapeamento('TFISPenalidade', 'recuperaListaPenalidades', $stCriterio);
    }

    /**
     * Retorna lista de penalidades por tipo e/ou nome.
     * @param  string    $stCriterio critério de busca
     * @return RecordSet
     */
    public function getListaPenalidades($inCodTipoPenalidade = '', $stNomPenalidade = '')
    {
        $stMapeamento = 'TFISPenalidade';
        $stMetodo     = 'recuperaListaPenalidades';
        $stCriterio   = '';
        $stOrdem      = 'penalidade.cod_penalidade ASC';
        $stSep        = '';

        if ($inCodTipoPenalidade) {
            $stCriterio = 'penalidade.cod_tipo_penalidade = ' . $inCodTipoPenalidade;
            $stSep = ' AND ';
        }

        if ($stNomPenalidade) {
            $stCriterio .= $stSep . 'penalidade.nom_penalidade ILIKE \'%' . addslashes($stNomPenalidade) . '%\'';
        }

        return $this->chamaMapeamento($stMapeamento, $stMetodo, $stCriterio, $stOrdem);
    }

    public function getListaPenalidadesBaixadas($inCodTipoPenalidade = '', $stNomPenalidade = '')
    {
        $stMapeamento = 'TFISPenalidade';
        $stMetodo     = 'recuperaListaPenalidadesBaixadas';
        $stCriterio   = '';
        $stOrdem      = 'penalidade.cod_penalidade ASC';
        $stSep        = '';

        if ($inCodTipoPenalidade) {
            $stCriterio = 'penalidade.cod_tipo_penalidade = ' . $inCodTipoPenalidade;
            $stSep = ' AND ';
        }

        if ($stNomPenalidade) {
            $stCriterio .= $stSep . 'penalidade.nom_penalidade ILIKE \'%' . addslashes($stNomPenalidade) . '%\'';
        }

        return $this->chamaMapeamento($stMapeamento, $stMetodo, $stCriterio, $stOrdem);
    }

    /**
     * Retorna todos os dados em penalidade por cod_penalidade.
     * @param  integer   $inCodPenalidade código da Penalidade
     * @return RecordSet
     */
    public function getPenalidade($inCodPenalidade)
    {
        $stMapeamento = 'TFISPenalidade';
        $stMetodo     = 'recuperaListaPenalidades';
        $stCriterio   = '';
        $stOrdem      = 'cod_penalidade ASC';

        if ($inCodPenalidade) {
            $stCriterio = 'cod_penalidade = ' . $inCodPenalidade;
        }

        return $this->chamaMapeamento($stMapeamento, $stMetodo, $stCriterio, $stOrdem);
    }

    /**
     * Retorna todos os dados em penalidade_multa por cod_penalidade.
     * @param  integer   $inCodPenalidade código da penalidade
     * @return RecordSet
     */
    public function getPenalidadeMulta($inCodPenalidade)
    {
        $stMapeamento = 'TFISPenalidadeMulta';
        $stMetodo     = 'recuperaPenalidadeMulta';
        $stCriterio   = '';

        if ($inCodPenalidade) {
            $stCriterio = ' fpm.cod_penalidade = ' . $inCodPenalidade;
        }

        return $this->chamaMapeamento($stMapeamento, $stMetodo, $stCriterio);
    }

    /**
     * Retorna todos dados em infracao_penalidade
     * @param  integer   $inCodInfracao
     * @param  integer   $inCodPenalidade
     * @return RecordSet
     */
    public function getPenalidadeInfracao($inCodInfracao = '', $inCodPenalidade = '')
    {
        $stMapeamento = 'TFISPenalidade';
        $stMetodo     = 'recuperaListaPenalidadesPorInfracao';
        $stCriterio   = '';

        if (intval($inCodInfracao) > 0) {
            $arCriterio[] = " infracao_penalidade.cod_infracao = " . $inCodInfracao . "\n";
        }

        if (intval($inCodPenalidade) > 0) {
            $arCriterio[] = " penalidade.cod_penalidade = " . $inCodPenalidade . "\n";
        }

        $inCount = count($arCriterio);
        for ($i = 0; $i < $inCount; $i++) {
            $stCriterio.= $arCriterio[$i];
            $j = $i + 1;
            if ($arCriterio[$j]) {
                $stCriterio.= " AND ";
            }
        }

        return $this->chamaMapeamento($stMapeamento, $stMetodo, $stCriterio);
    }

    /**
     * Retorna todos os dados em penalidade_desconto por cod_penalidade.
     * @param  integer   $inCodPenalidade código da penalidade
     * @return RecordSet
     */
    public function getPenalidadeDesconto($inCodPenalidade)
    {
        $stMapeamento = 'TFISPenalidadeDesconto';
        $stMetodo     = 'recuperaPenalidadeDesconto';
        $stCriterio   = '';
        $stOrdem     = ' ORDER BY prazo ';

        if ($inCodPenalidade) {
            $stCriterio  = ' cod_penalidade = ' . $inCodPenalidade;
        }

        return $this->chamaMapeamento($stMapeamento, $stMetodo, $stCriterio, $stOrdem);
    }

    /**
     * Retorna os dados em penalidade_documento por cod_penalidade.
     * @param  integer   $inCodPenalidade código da penalidade
     * @return RecordSet
     */
    public function getPenalidadeDocumento($inCodPenalidade)
    {
        $stMapeamento = 'TFISPenalidadeDocumento';
        $stMetodo     = 'recuperaTodos';
        $stCriterio   = '';
        $stOrdem      = ' ORDER BY cod_penalidade';

        if ($inCodPenalidade) {
            $stCriterio  = ' WHERE cod_penalidade = ' . $inCodPenalidade;
        }

        return $this->chamaMapeamento($stMapeamento, $stMetodo, $stCriterio, $stOrdem);
    }

    /**
     * Retorna todos dados em infracao_penalidade
     * @param  integer   $inCodInfracao
     * @param  integer   $inCodPenalidade
     * @return RecordSet
     */
    public function getAutoInfracao($inCodInfracao = '', $inCodPenalidade = '')
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

        $inCount = count($arCriterio);
        for ($i = 0; $i < $inCount; $i++) {
            $stCriterio.= $arCriterio[$i];
            $j = $i + 1;
            if ($arCriterio[$j]) {
                $stCriterio.= " AND ";
            }
        }

        return $this->chamaMapeamento($stMapeamento, $stMetodo, $stCriterio);
    }

    private function acaoDocumento($stOperacao, $inCodPenalidade, $inCodTipoDocumento, $inCodDocumento)
    {
        $obTFISPenalidadeDocumento = new TFISPenalidadeDocumento();

        $obTFISPenalidadeDocumento->setDado('cod_penalidade', $inCodPenalidade);
        $obTFISPenalidadeDocumento->setDado('cod_tipo_documento', $inCodTipoDocumento);
        $obTFISPenalidadeDocumento->setDado('cod_documento', $inCodDocumento);

        return $obTFISPenalidadeDocumento->$stOperacao($boTransacao);
    }

    private function excluirDocumento($inCodPenalidade)
    {
        $obTFISPenalidadeDocumento = new TFISPenalidadeDocumento();
        $obTFISPenalidadeDocumento->setDado('cod_penalidade', $inCodPenalidade);

        return $obTFISPenalidadeDocumento->exclusao($boTransacao);
    }

    /**
     * Realiza a operação de inclusão ou alteração especificada.
     * @access private
     * @param  integer $inCodPenalidade     o código da penalidade
     * @param  integer $inCodTipoPenalidade o código do tipo da penalidade
     * @param  integer $inCodNorma          o código da norma
     * @param  string  $stNomPenalidade     o nome da penalidade
     * @param  integer $inCodIndicador      o código do indicador econômico
     * @param  string  $stCodFuncao         cod. função, cod. módulo, cod. biblioteca
     * @param  string  $stCodUnidade        cod. unidade, cod. grandeza
     * @param  string  $boConceder          S / N
     * @param  array   $inDia               contém os Prazos
     * @param  array   $inDesconto          contém os Valores dos Descontos
     * @param  array   $inDia               contém os códigos dos descontos
     * @return Erro
     */
    private function acaoPenalidade($stOperacao, &$inCodPenalidade, $inCodTipoPenalidade = '', $inCodNorma = '', $stNomPenalidade = '', $inCodIndicador = '', $stCodFuncao = '', $stCodUnidade = '', $boConceder = '', $inDia = '', $inDesconto = '', $stCodDesconto = '', $inCodTipoDocumento = '', $inCodDocumento = '')
    {
        $obTFISPenalidade         = new TFISPenalidade();
        $obTFISPenalidadeMulta    = new TFISPenalidadeMulta();
        $obTFISPenalidadeDesconto = new TFISPenalidadeDesconto();
        $obTransacao              = new Transacao();
        $boFlagTransacao          = false;
        $boTransacao              = '';
        $arDescontoIni            = array();
        $arDesconto               = array();

        # Inicia nova transação
        $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        # Novo número de código de penalidade para inclusão
        if ($stOperacao == 'inclusao') {
            $inCodPenalidade = null;
            $obTFISPenalidade->proximoCod($inCodPenalidade);
        }

        # Penalidade
        $obTFISPenalidade->setDado('cod_penalidade', $inCodPenalidade);
        $obTFISPenalidade->setDado('cod_tipo_penalidade', $inCodTipoPenalidade);
        $obTFISPenalidade->setDado('cod_norma', $inCodNorma);
        $obTFISPenalidade->setDado('nom_penalidade', $stNomPenalidade);
        $obErro = $obTFISPenalidade->$stOperacao($boTransacao);

        if (!$obErro->ocorreu()) {
            # Insere ou modifica penalidade_documento se for o caso.
            $rsPenalidadeDocumento = $this->getPenalidadeDocumento($inCodPenalidade);

            if ($inCodTipoDocumento && $inCodDocumento) {

                if ($rsPenalidadeDocumento->eof()) {
                    $obErro = $this->acaoDocumento('inclusao', $inCodPenalidade, $inCodTipoDocumento, $inCodDocumento, $boTransacao);
                } else {
                    $obErro = $this->acaoDocumento('alteracao', $inCodPenalidade, $inCodTipoDocumento, $inCodDocumento, $boTransacao);
                }
            } else {
                $obErro = $this->excluirDocumento($inCodPenalidade, $boTransacao);
            }
        }

        # Ocorreu erro?
        if (!$obErro->ocorreu()) {

            if ($inCodTipoPenalidade == 1) {
                # Cod_modulo, Cod_biblioteca, Cod_grandeza gerados por $stCodFuncao
                $arCodFuncao = explode('.', $stCodFuncao);
                $inCodModulo = $arCodFuncao[0];
                $inCodBiblioteca = $arCodFuncao[1];
                $inCodFuncao = $arCodFuncao[2];

                $arCodUnidade = explode('-', $stCodUnidade);
                $inCodUnidade = $arCodUnidade[0];
                $inCodGrandeza = $arCodUnidade[1];

                # PenalidadeMulta
                $obTFISPenalidadeMulta->setDado('cod_penalidade', $inCodPenalidade);
                $obTFISPenalidadeMulta->setDado('cod_indicador', $inCodIndicador);
                $obTFISPenalidadeMulta->setDado('cod_modulo', $inCodModulo);
                $obTFISPenalidadeMulta->setDado('cod_biblioteca', $inCodBiblioteca);
                $obTFISPenalidadeMulta->setDado('cod_funcao', $inCodFuncao);
                $obTFISPenalidadeMulta->setDado('cod_unidade', $inCodUnidade);
                $obTFISPenalidadeMulta->setDado('cod_grandeza', $inCodGrandeza);
                $obErro = $obTFISPenalidadeMulta->$stOperacao($boTransacao);

                 # Ocorreu erro?
                if (!$obErro->ocorreu()) {

                    if ($boConceder == 'S') {

                        if (is_array($inDia)) {

                            if ($stOperacao == 'inclusao') {
                                $inCodDesconto = null;
                                $inCount = count($inDia);

                                for ($i = 0; $i < $inCount; $i++) {
                                    $obTFISPenalidadeDesconto->proximoCod($inCodDesconto);
                                    $inPrazo = $inDia[$i];
                                    $fiDesconto = $inDesconto[$i];

                                    $obTFISPenalidadeDesconto->setDado('cod_penalidade', $inCodPenalidade);
                                    $obTFISPenalidadeDesconto->setDado('cod_desconto', $inCodDesconto);
                                    $obTFISPenalidadeDesconto->setDado('prazo', $inPrazo);
                                    $obTFISPenalidadeDesconto->setDado('desconto', $fiDesconto);
                                    $obErro = $obTFISPenalidadeDesconto->$stOperacao($boTransacao);
                                    $inCodDesconto += $i;
                                }
                            } else {
                                $contador = count($inDia);

                                # Gera array de código de penalidade a partir de infracao_penalidade.
                                for ($k = 0; $k < $contador ; $k++) {
                                    $arDesconto[$k]['cod_desconto'] = $stCodDesconto[$k];
                                    $arDesconto[$k]['prazo'] = $inDia[$k];
                                    $arDesconto[$k]['desconto'] = $inDesconto[$k];
                                    $arComparacao[] = $arDesconto[$k]['cod_desconto'].'--'.$arDesconto[$k]['prazo'].'--'.$arDesconto[$k]['desconto'];
                                }

                                # Retorna todas as infracoes_penalidades com a mesma cod_infracao.
                                $stCondicao = ' WHERE cod_penalidade = ' . $inCodPenalidade;
                                $obErro = $obTFISPenalidadeDesconto->recuperaTodos($rsPenalidadeDesconto, $stCondicao);
                                $arPenalidadeDesconto = $rsPenalidadeDesconto->arElementos;

                                $inCount = count($arPenalidadeDesconto);

                                # Gera array de descontos de penalidade_desconto
                                if ($inCount > 0) {

                                    for ($i = 0; $i < $inCount; $i++) {
                                        $arDescontoIni[$i]['cod_desconto'] = $arPenalidadeDesconto[$i]['cod_desconto'];
                                        $arDescontoIni[$i]['prazo'] = $arPenalidadeDesconto[$i]['prazo'];
                                        $arDescontoIni[$i]['desconto'] = $arPenalidadeDesconto[$i]['desconto'];
                                        $arComparacaoIni[] = $arDescontoIni[$i]['cod_desconto'].'--'.$arDescontoIni[$i]['prazo'].'--'.$arDescontoIni[$i]['desconto'];

                                        $j = $i + 1;
                                        if (!$arPenalidadeDesconto[$j]['cod_desconto']) {
                                            $inProximoCod = $arPenalidadeDesconto[$i]['cod_desconto'];
                                        }
                                    }
                                } else {
                                    $arComparacaoIni = array();
                                }

                                # Inclui e exclui Descontos.
                                if (!$obErro->ocorreu()) {
                                    if (is_array($arComparacaoIni) && is_array($arComparacao)) {

                                        $arExclusao = array_diff($arComparacaoIni, $arComparacao);
                                        if (count($arExclusao) > 0) {
                                            $this->apagarPenalidadeDesconto($inCodPenalidade, $arExclusao, $obErro, $boTransacao);
                                        }
                                    }

                                    if (!$obErro->ocorreu()) {

                                        if (is_array($arComparacaoIni) && is_array($arComparacao)) {
                                            $arInclusao = array_diff($arComparacao, $arComparacaoIni);

                                            if (count($arInclusao) > 0) {
                                                $this->incluirPenalidadeDesconto($inCodPenalidade, $inProximoCod, $arInclusao, $obErro, $boTransacao);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        $rsPenalidadeDesconto = $this->getPenalidadeDesconto($arParametros['inCodPenalidade']);
                        $inCountDesc = count($rsPenalidadeDesconto->arElementos);

                        if ($inCountDesc > 0) {
                            for ($m = 0; $m < $inCountDesc; $m++) {
                                $arExclusao[] = $rsPenalidadeDesconto->arElementos[$m]['cod_desconto'].'--'.$rsPenalidadeDesconto->arElementos[$m]['prazo'].'--'.$rsPenalidadeDesconto->arElementos[$m]['desconto'];
                            }

                            $this->apagarPenalidadeDesconto($inCodPenalidade, $arExclusao, $obErro, $boTransacao);
                        }
                    }
                }
            }
        }

        # Termina transação
        $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $obTFISPenalidade);

        return $obErro;
    }

    /**
     * Apaga os Descontos que não estão na lista de Descontos
     * @param integer $inCodPenalidade código da penalidade
     * @param array   $arDesconto      contendo o campo a ser deletado do banco
     * @param object  $obErro          que é retorno das Ações do Banco
     * @param boolean $boTransacao     Transação do Banco
     */
    private function apagarPenalidadeDesconto($inCodPenalidade, $arDesconto, &$obErro, &$boTransacao = '')
    {
        $obTFISPenalidadeDesconto = new TFISPenalidadeDesconto();
        $inCount = count($arDesconto);

        # Exclui todo penalidade_desconto que bate.
        foreach ($arDesconto as $stValues) {
            $arValues = explode('--', $stValues);
            $inCodDesconto = $arValues[0];

            if ($obErro->ocorreu()) {
                break;
            }

            $obTFISPenalidadeDesconto->setDado('cod_penalidade', $inCodPenalidade);
            $obTFISPenalidadeDesconto->setDado('cod_desconto', $inCodDesconto);
            $obErro = $obTFISPenalidadeDesconto->exclusao($boTransacao);
        }
    }

    /**
     * Inclui novos Descontos adicionados na Alteração de Penalidade
     * @param integer $inCodPenalidade código da penalidade
     * @param integer $inProximoCod    proximo código do desconto
     * @param array   $arDesconto      contendo o campo a ser deletado do banco
     * @param object  $obErro          que é retorno das Ações do Banco
     * @param boolean $boTransacao     Transação do Banco
     */
    private function incluirPenalidadeDesconto($inCodPenalidade, $inProximoCod, $arDesconto, &$obErro, &$boTransacao = '')
    {
        $obTFISPenalidadeDesconto = new TFISPenalidadeDesconto();
        $inCount = count($arDesconto);
        $inCodDesconto = $inProximoCod;

        # inclui novo desconto
        foreach ($arDesconto as $stValues) {
            $arValues = explode('--', $stValues);
            $inPrazo = $arValues[1];
            $inDesconto = $arValues[2];
            $inCodDesconto++;

            if ($obErro->ocorreu()) {
                break;
            }

            $obTFISPenalidadeDesconto->setDado('cod_penalidade', $inCodPenalidade);
            $obTFISPenalidadeDesconto->setDado('cod_desconto', $inCodDesconto);
            $obTFISPenalidadeDesconto->setDado('prazo', $inPrazo);
            $obTFISPenalidadeDesconto->setDado('desconto', $inDesconto);
            $obErro = $obTFISPenalidadeDesconto->inclusao($boTransacao);
        }
    }

    /**
     * Incluir penalidade
     * @param  integer $inCodTipoPenalidade o código do tipo da penalidade
     * @param  integer $inCodNorma          o código da norma
     * @param  string  $stNomPenalidade     o nome da penalidade
     * @param  integer $inCodIndicador      o código do indicador econômico
     * @param  string  $stCodFuncao         cod. função, cod. módulo, cod. biblioteca
     * @param  string  $stCodUnidade        cod. unidade, cod. grandeza
     * @param  string  $boConceder          S / N
     * @param  array   $inDia               contém os Prazos
     * @param  array   $inDesconto          contém os Valores dos Descontos
     * @param  array   $inDia               contém os códigos dos descontos
     * @return Erro
     */
    public function incluirPenalidade(&$inCodPenalidade, $inCodTipoPenalidade, $inCodNorma, $stNomPenalidade, $inCodIndicador, $stCodFuncao, $stCodUnidade, $boConceder, $inDia, $inDesconto, $stCodDesconto, $inCodTipoDocumento, $inCodDocumento)
    {
        return $this->acaoPenalidade('inclusao', $inCodPenalidade, $inCodTipoPenalidade, $inCodNorma, $stNomPenalidade, $inCodIndicador, $stCodFuncao, $stCodUnidade, $boConceder, $inDia, $inDesconto, $stCodDesconto, $inCodTipoDocumento, $inCodDocumento);
    }

    /**
     * Exclui penalidade
     * @param  integer $inCodPenalidade o código da penalidade
     * @return Erro
     */
    public function excluirPenalidade($inCodPenalidade)
    {
        $obTFISPenalidade          = new TFISPenalidade();
        $obTFISPenalidadeDesconto  = new TFISPenalidadeDesconto();
        $obTFISInfracaoPenalidade  = new TFISInfracaoPenalidade();
        $obTFISPenalidadeMulta     = new TFISPenalidadeMulta();
        $obTransacao               = new Transacao();
        $boFlagTransacao           = false;
        $boTransacao               = '';

        $rsAutoInfracao = $this->getAutoInfracao('',$inCodPenalidade);

        if ($rsAutoInfracao->inNumLinhas > 0) {
            $pgDestino = "LSManterPenalidade.php?" . Sessao::getID() . "&stAcao=excluir";

            return SistemaLegado::alertaAviso($pgDestino, 'Penalidade('.$inCodPenalidade.') já vinculada a um Documento de Notificação!', 'aviso', 'aviso', Sessao::getId(), '../');
        }

        # Inicia nova transação
        $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        # Excluir penalidade na infracao
        $obTFISInfracaoPenalidade->setDado('cod_penalidade', $inCodPenalidade);
        $obErro = $obTFISInfracaoPenalidade->exclusao($boTransacao);

        # Excluir penalidade desconto
        if (!$obErro->ocorreu()) {
            $obTFISPenalidadeDesconto->setDado('cod_penalidade', $inCodPenalidade);
            $obErro = $obTFISPenalidadeDesconto->exclusao($boTransacao);
        }

        # Exclui penalidade multa
        if (!$obErro->ocorreu()) {
            $obTFISPenalidadeMulta->setDado('cod_penalidade', $inCodPenalidade);
            $obErro = $obTFISPenalidadeMulta->exclusao($boTransacao);
        }

        # Exclui penalidade documento
        if (!$obErro->ocorreu()) {
            $obErro = $this->excluirDocumento($inCodPenalidade);
        }

        # Exclui penalidade
        if (!$obErro->ocorreu()) {
            $obTFISPenalidade->setDado('cod_penalidade', $inCodPenalidade);
            $obErro = $obTFISPenalidade->exclusao($boTransacao);
        }

        # Termina transação
        $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $obTFISPenalidade);

        return $obErro;
    }

    /**
     * Altera penalidade
     * @param  integer $inCodPenalidade     o código da penalidade
     * @param  integer $inCodTipoPenalidade o código do tipo da penalidade
     * @param  integer $inCodNorma          o código da norma
     * @param  string  $stNomPenalidade     o nome da penalidade
     * @param  integer $inCodIndicador      o código do indicador econômico
     * @param  string  $stCodFuncao         cod. função, cod. módulo, cod. biblioteca
     * @param  string  $stCodUnidade        cod. unidade, cod. grandeza
     * @param  string  $boConceder          S / N
     * @param  array   $inDia               contém os Prazos
     * @param  array   $inDesconto          contém os Valores dos Descontos
     * @param  array   $inDia               contém os códigos dos descontos
     * @return Erro
     */
    public function alterarPenalidade($inCodPenalidade, $inCodTipoPenalidade, $inCodNorma, $stNomPenalidade, $inCodIndicador, $stCodFuncao, $stCodUnidade, $boConceder, $inDia, $inDesconto, $stCodDesconto, $inCodTipoDocumento, $inCodDocumento)
    {
        # Altera penalidade

        return $this->acaoPenalidade('alteracao', $inCodPenalidade, $inCodTipoPenalidade, $inCodNorma, $stNomPenalidade, $inCodIndicador, $stCodFuncao, $stCodUnidade, $boConceder, $inDia, $inDesconto, $stCodDesconto, $inCodTipoDocumento, $inCodDocumento);
    }
}
