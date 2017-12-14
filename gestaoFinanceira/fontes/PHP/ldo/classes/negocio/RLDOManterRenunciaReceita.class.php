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
 * Classe de negócio do UC-02.10.16
 * Data de Criação: 23/03/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Marcio Medeiros <marcio.medeiros>
 * @package GF
 * @subpackage LDO
 * @uc uc-02.10.16 - Manter Compensação da Renúncia de Receita
 */
include_once CAM_GF_LDO_NEGOCIO.'RLDOPadrao.class.php';
include_once CAM_GF_LDO_MAPEAMENTO.'TLDOCompensacaoRenuncia.class.php';
include_once CAM_GF_LDO_MAPEAMENTO.'TLDO.class.php';

class RLDOManterRenunciaReceita extends RLDOPadrao implements IRLDOPadrao
{
    /**
     * Objeto mapeamento TLDOCompensacaoRenuncia
     *
     * @var TLDOCompensacaoRenuncia
     */
    private $obTLDOCompensacaoRenuncia;

    /**
     * Objeto mapeamento TLDO
     *
     * @var TLDO
     */
    private $obTLDO;

    /**
     * Retorna uma instância da classe
     *
     * @return RLDOManterRenunciaReceita
     */
    public static function recuperarInstancia($ob = NULL)
    {
        return parent::recuperarInstancia(__CLASS__);
    }

    /**
     * Instancia os objetos de mapeamento
     */
    protected function inicializar()
    {
        $this->obTLDOCompensacaoRenuncia = new TLDOCompensacaoRenuncia();
        $this->obTLDO = new TLDO;
    }

    /**
     * Inclui o Compensação de Renúncia de Receita
     *
     * @param  array $arArgs
     * @return bool
     */
    public function incluir(array $arArgs)
    {
        $obTransacao     = new Transacao();
        $boFlagTransacao = false;
        $boTransacao     = null;
        $obErroT = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);
        $this->obTLDOCompensacaoRenuncia->proximoCod($inCodCompensacao, $boTransacao);
        $this->obTLDOCompensacaoRenuncia->setDado('cod_compensacao'  , $inCodCompensacao);
        $this->obTLDOCompensacaoRenuncia->setDado('ano'              , $arArgs['inAnoLDO']);
        $this->obTLDOCompensacaoRenuncia->setDado('cod_ppa'          , $arArgs['inCodPPA']);
        $this->obTLDOCompensacaoRenuncia->setDado('tributo'          , stripslashes($arArgs['stTributo']));
        $this->obTLDOCompensacaoRenuncia->setDado('modalidade'       , stripslashes($arArgs['stModalidade']));
        $this->obTLDOCompensacaoRenuncia->setDado('setores_programas', stripslashes($arArgs['stSetorProgramas']));
        $this->obTLDOCompensacaoRenuncia->setDado('valor_ano_ldo'    , $arArgs['flValorAnoLDO']);
        $this->obTLDOCompensacaoRenuncia->setDado('valor_ano_ldo_1'  , $arArgs['flValorAnoLDO1']);
        $this->obTLDOCompensacaoRenuncia->setDado('valor_ano_ldo_2'  , $arArgs['flValorAnoLDO2']);
        $this->obTLDOCompensacaoRenuncia->setDado('compensacao'      , stripslashes($arArgs['stCompensacao']));
        $obErro = $this->obTLDOCompensacaoRenuncia->inclusao($boTransacao);
        if ($obErro->ocorreu()) {
            throw new RLDOExcecao('Erro ao incluir a Compensação de Renúncia de Receita!');
        }
        $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErroT, $this->obTLDOCompensacaoRenuncia);

        return true;
    }

    /**
     * Altera um Compensação de Renúncia de Receita e suas respectivas Prodiências Fiscais.
     *
     * @param  array $arArgs
     * @return bool
     */
    public function alterar(array $arArgs)
    {
        $obTransacao     = new Transacao();
        $boFlagTransacao = false;
        $boTransacao     = null;
        $obErroT = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);
        $this->obTLDOCompensacaoRenuncia->setDado('cod_compensacao'  , $arArgs['inCodCompensacao']);
        $this->obTLDOCompensacaoRenuncia->setDado('ano'              , $arArgs['inAnoLDO']);
        $this->obTLDOCompensacaoRenuncia->setDado('cod_ppa'          , $arArgs['inCodPPA']);
        $this->obTLDOCompensacaoRenuncia->setDado('tributo'          , stripslashes($arArgs['stTributo']));
        $this->obTLDOCompensacaoRenuncia->setDado('modalidade'       , stripslashes($arArgs['stModalidade']));
        $this->obTLDOCompensacaoRenuncia->setDado('setores_programas', stripslashes($arArgs['stSetorProgramas']));
        $this->obTLDOCompensacaoRenuncia->setDado('valor_ano_ldo'    , $arArgs['flValorAnoLDO']);
        $this->obTLDOCompensacaoRenuncia->setDado('valor_ano_ldo_1'  , $arArgs['flValorAnoLDO1']);
        $this->obTLDOCompensacaoRenuncia->setDado('valor_ano_ldo_2'  , $arArgs['flValorAnoLDO2']);
        $this->obTLDOCompensacaoRenuncia->setDado('compensacao'      , stripslashes($arArgs['stCompensacao']));
        $obErro = $this->obTLDOCompensacaoRenuncia->alteracao($boTransacao);
        if ($obErro->ocorreu()) {
            throw new RLDOExcecao('Erro ao alterar a Compensação de Renúncia de Receita!');
        }
        $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErroT, $this->obTLDOCompensacaoRenuncia);

        return true;
    }

    /**
     * Exclui um Compensação de Renúncia de Receita e suas respectivas Providências Fiscais
     *
     * @param  array $arArgs
     * @return bool
     */
    public function excluir(array $arArgs)
    {
        $obTransacao     = new Transacao();
        $boFlagTransacao = false;
        $boTransacao     = null;
        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);
        $this->obTLDOCompensacaoRenuncia->setDado('cod_compensacao', $arArgs['inCodCompensacao']);
        $obErro = $this->obTLDOCompensacaoRenuncia->exclusao($boTransacao);
        if ($obErro->ocorreu()) {
            throw new RLDOExcecao('Erro ao excluir a Compensação de Renúncia de Receita!');
        }
        $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTLDOCompensacaoRenuncia);

        return true;
    }

    /**
     * Recupera Compensação de Renúncia de Receita
     *
     * @param  array     $arArgs
     * @return RecordSet
     */
    public function recuperarRenunciaReceita(array $arArgs)
    {
        $obTransacao = new Transacao();
        $boFlagTransacao = false;
        $boTransacao = '';
        $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        $stFiltro = ' WHERE ano = \''. $arArgs['inAnoLDO'].'\'';
        if ($arArgs['inCodCompensacao']) {
            $stFiltro .= ' AND cod_compensacao = '.$arArgs['inCodCompensacao'];
        }
        if ($arArgs['inCodPPA']) {
            $stFiltro .= ' AND cod_ppa = '.$arArgs['inCodPPA'];
        }

        $this->obTLDOCompensacaoRenuncia->recuperaTodos($rsRenunciaReceita, $stFiltro, null, $boTransacao);

        return $rsRenunciaReceita;
    }

    /**
     * Realiza o procedimento de recuperar os dados do exercicio da ppa selecionada
     *
     * @param  integer   $inCodPPA recebe o código da PPA para fazer a filtragem
     * @return RecordSet
     */
    public function recuperaExercicioPPA($inCodPPA)
    {
        $this->obTLDO->setDado('cod_ppa', $inCodPPA);
        $this->obTLDO->recuperaExerciciosLDO($rsLDO);

        return $rsLDO;
    }

}
