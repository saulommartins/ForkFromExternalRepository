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
 * Classe de Visao do 02.10.16 - Manter Compensação da Renúncia de Receita
 * Data de Criação: 23/03/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Marcio Medeiros <marcio.medeiros>
 * @package GF
 * @subpackage LDO
 * @uc 02.10.16 - Manter Compensação da Renúncia de Receita
 */
include_once CAM_GF_LDO_VISAO   . 'VLDOPadrao.class.php';
include_once CAM_GF_LDO_NEGOCIO . 'RLDOManterRenunciaReceita.class.php';
include_once CAM_GF_LDO_UTIL    . 'LDOString.class.php';

class VLDOManterRenunciaReceita extends VLDOPadrao implements IVLDOPadrao
{
    /**
     * Retorna uma instancia da classe
     *
     * @return VLDOManterRenunciaReceita
     */
    public static function recuperarInstancia($ob = NULL)
    {
        return parent::recuperarInstancia(__CLASS__);
    }

    /**
     * Instancia objeto Regra
     */
    public function inicializar()
    {
        parent::inicializarRegra(__CLASS__);
    }

    /**
     * Inclui Renúncia de Receita
     *
     * @param  array $arParametros
     * @return void
     */
    public function incluir(array $arParametros)
    {
        /**
        * Desabilidada a regra de validação para evitar LDO duplicada. Ainda não definido pelo usuário se
        * existirá e qual será a chave única além da
        * PK cod_compensacao já definida.

        $rsRenunciaReceita = $this->recuperarRegra()->recuperarRenunciaReceita($arParametros);
        if (count($rsRenunciaReceita->arElementos) > 0) {
            SistemaLegado::exibeAviso('LDO já cadastrado! ('.$arParametros['inAnoLDO'].')', 'n_incluir', 'aviso');

            return;
        }
        */

        $this->validarValores($arParametros);
        if ($arParametros['erroMsg']) {
            SistemaLegado::LiberaFrames(true,false);
            SistemaLegado::exibeAviso(urlencode($arParametros['erroMsg']), 'n_incluir', 'alerta');

            return;
        }

        try {
            $this->recuperarRegra()->incluir($arParametros);
            SistemaLegado::alertaAviso('FMManterRenunciaReceita.php?stAcao=incluir', $arParametros['inAnoLDO'], 'incluir', 'aviso', Sessao::getId(), '../');
        } catch (RLDOExcecao $e) {
            SistemaLegado::LiberaFrames(true,false);
            SistemaLegado::exibeAviso(urlencode($e->getMessage()), 'n_incluir', 'erro');
        }

    }

    /**
     * Altera uma Renúncia de Receita
     *
     * @param  array $arParametros
     * @return void
     */
    public function alterar(array $arParametros)
    {
        /**
        * Desabilidada a regra de validação para evitar LDO duplicada. Ainda não definido pelo usuário se
        * existirá e qual será a chave única além da
        * PK cod_compensacao já definida.

        $rsRenunciaReceita = $this->recuperarRegra()->recuperarRenunciaReceita($arParametros);
        if ($arParametros['inAnoLDO'] != $arParametros['inAnoLdoOriginal']) {
            if (count($rsRenunciaReceita->arElementos) > 0) {
                SistemaLegado::exibeAviso('LDO já cadastrado! ('.$arParametros['inAnoLDO'].')', 'n_alterar', 'aviso');

                return;
            }
        }
        */

        $this->validarValores($arParametros);
        if ($arParametros['erroMsg']) {
            SistemaLegado::LiberaFrames(true,false);
            SistemaLegado::exibeAviso(urlencode($arParametros['erroMsg']), 'n_alterar', 'alerta');

            return;
        }

        try {
            $this->recuperarRegra()->alterar($arParametros);
            SistemaLegado::alertaAviso('FLManterRenunciaReceita.php?stAcao=alterar', $arParametros['inCodCompensacao'], 'incluir', 'aviso', Sessao::getId(), '../');
        } catch (RLDOExcecao $e) {
            SistemaLegado::exibeAviso($e->getMessage(), 'n_incluir', 'erro');
        }
    }

    /**
     * Exclui uma Renúncia de Receita
     *
     * @param  array $arParametros
     * @return void
     */
    public function excluir(array $arParametros)
    {
        try {
            $this->recuperarRegra()->excluir($arParametros);
            SistemaLegado::alertaAviso('LSManterRenunciaReceita.php?stAcao=excluir', $arParametros['inCodCompensacao'], 'excluir', 'aviso', Sessao::getId(), '../');
        } catch (RLDOExcecao $e) {
            SistemaLegado::LiberaFrames(true,false);
            SistemaLegado::exibeAviso($e->getMessage(), 'n_excluir', 'erro', Sessao::getId(), '../');
        }
    }

    /**
     *
     * @param  int       $inAnoLDO
     * @return RecordSet
     */
    public function recuperarRenunciaReceita($inAnoLDO)
    {
        return $this->recuperarRegra()->recuperarRenunciaReceita($inAnoLDO);
    }

    /**
     * Valida os valores Renúncia de receita prevista
     *
     * @param  array $arParametros
     * @return void
     */
    private function validarValores(array &$arParametros)
    {
        $erroMsg = false;
        if (trim($arParametros['flValorAnoLDO']) == '0,00') {
            $erroMsg = 'ano atual';
        } elseif (trim($arParametros['flValorAnoLDO1']) == '0,00') {
            $erroMsg = 'ano + 1';
        } elseif (trim($arParametros['flValorAnoLDO2']) == '0,00') {
            $erroMsg = 'ano + 2';
        }
        if ($erroMsg) {
            $stMsg  = 'O valor da Renúncia de receita prevista para ' . $erroMsg;
            $stMsg .= ' deve ser maior que zero!';
            $arParametros['erroMsg'] = $stMsg;
        }

    }

    /**
     * Monta os dados do select de exercicio da LDO
     *
     * @param  array  $arParametros
     * @return string código javascript para montar os dados do select
     */
    public function montaExercicio(array $arParametros)
    {
        $stJs = 'jq("#inAnoLDO").removeOption(/./);';
        if ($arParametros['inCodPPATxt'] != '') {
            $rsLDO = $this->recuperarRegra()->recuperaExercicioPPA($arParametros['inCodPPATxt']);
            while (!$rsLDO->eof()) {
                $stJs .= 'jq("#inAnoLDO").addOption('.$rsLDO->getCampo('ano').', '.$rsLDO->getCampo('ano_ldo').', false);';
                $rsLDO->proximo();
            }
        }

        return $stJs;
    }

}
