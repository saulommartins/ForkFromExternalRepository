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
 * Classe de negócio do UC-02.10.06
 * Data de Criação: 10/03/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Marcio Medeiros <marcio.medeiros>
 * @package GF
 * @subpackage LDO
 * @uc uc-02.10.06 - Manter Riscos Fiscais
 */
include_once CAM_GF_LDO_NEGOCIO    . 'RLDOPadrao.class.php';
include_once CAM_GF_LDO_MAPEAMENTO . 'TLDORiscoFiscal.class.php';
include_once CAM_GF_LDO_MAPEAMENTO . 'TLDOProvidenciaFiscal.class.php';

class RLDOManterRiscoFiscal extends RLDOPadrao implements IRLDOPadrao
{
    /**
     * Objeto mapeamento TLDORiscoFiscal
     *
     * @var stdClass TLDORiscoFiscal
     */
    private $obTLDORiscoFiscal;

    /**
     * Objeto mapeamento TLDOProvidenciaFiscal
     *
     * @var stdClass TLDOProvidenciaFiscal
     */
    private $obTLDOProvidenciaFiscal;

    /**
     * Retorna uma instância da classe
     *
     * @return RLDOManterRiscoFiscal
     */
    public static function recuperarInstancia()
    {
        return parent::recuperarInstancia(__CLASS__);
    }

    /**
     * Instancia os objetos de mapeamento
     */
    protected function inicializar()
    {
        $this->obTLDORiscoFiscal       = new TLDORiscoFiscal();
        $this->obTLDOProvidenciaFiscal = new TLDOProvidenciaFiscal();
    }

    /**
     * Inclui o Risco Fiscal
     *
     * @param  array $arArgs
     * @return int   Valor gerado pelo proximoCod
     */
    public function incluir(array $arArgs)
    {
        $obTransacao     = new Transacao();
        $boFlagTransacao = false;
        $boTransacao     = null;
        $obErroT = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        $this->obTLDORiscoFiscal->proximoCod($arArgs['inCodRiscoFiscal'], $boTransacao);
        $this->obTLDORiscoFiscal->setDado('cod_risco_fiscal', $arArgs['inCodRiscoFiscal']);
        $this->obTLDORiscoFiscal->setDado('ano', $arArgs['inAnoLDO']);
        $this->obTLDORiscoFiscal->setDado('descricao', $arArgs['stDescRiscoFiscal']);
        $this->obTLDORiscoFiscal->setDado('valor', $arArgs['flValorRiscoFiscal']);
        $obErro = $this->obTLDORiscoFiscal->inclusao($boTransacao);
        if ($obErro->ocorreu()) {
            throw new RLDOExcecao('Erro ao incluir o Risco Fiscal!');
        }
        $this->incluirProvidenciaFiscal($arArgs, $boTransacao);
        $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErroT, $this->obTLDOProvidenciaFiscal);

        return $arArgs['inCodRiscoFiscal'];
    }

    /**
     * Altera um Risco Fiscale suas respectivas Prodiências Fiscais.
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

        $this->obTLDORiscoFiscal->setDado('cod_risco_fiscal', $arArgs['inCodRiscoFiscal']);
        $this->obTLDORiscoFiscal->setDado('ano', $arArgs['inAnoLDO']);
        $this->obTLDORiscoFiscal->setDado('descricao', $arArgs['stDescRiscoFiscal']);
        $this->obTLDORiscoFiscal->setDado('valor', $arArgs['flValorRiscoFiscal']);
        $obErro = $this->obTLDORiscoFiscal->alteracao($boTransacao);
        if ($obErro->ocorreu()) {
            throw new RLDOExcecao('Erro ao alterar Risco Fiscal!');
        }
        $stFiltro  = ' WHERE cod_risco_fiscal = ' . $arArgs['inCodRiscoFiscal'];
        $this->obTLDOProvidenciaFiscal->recuperaTodos($rsProvidenciaFiscal, $stFiltro, null, $boTransacao);
        $numRegistros = count($rsProvidenciaFiscal->arElementos);
        for ($i=0; $i < $numRegistros; $i++) {
            $this->obTLDOProvidenciaFiscal->setDado('cod_providencia_fiscal', $rsProvidenciaFiscal->arElementos[$i]['cod_providencia_fiscal']);
            $this->obTLDOProvidenciaFiscal->setDado('descricao', $arArgs['arDescProvidencia'][$i]);
            $this->obTLDOProvidenciaFiscal->setDado('valor', $arArgs['arValorProvidencia'][$i]);
            $this->obTLDOProvidenciaFiscal->setDado('cod_risco_fiscal', $arArgs['inCodRiscoFiscal']);
            $obErro = $this->obTLDOProvidenciaFiscal->exclusao($boTransacao);
            if ($obErro->ocorreu()) {
                throw new RLDOExcecao('Erro ao alterar Risco Fiscal!');
            }
        }
        $this->incluirProvidenciaFiscal($arArgs, $boTransacao);
        $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErroT, $this->obTLDOProvidenciaFiscal);

        return true;
    }

    /**
     * Exclui um Risco Fiscal e suas respectivas Providências Fiscais
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

        $stFiltro  = ' WHERE cod_risco_fiscal = ' . $arArgs['inCodRiscoFiscal'];
        $this->obTLDOProvidenciaFiscal->recuperaTodos($rsProvidenciaFiscal, $stFiltro, null, $boTransacao);
        $numRegistros = count($rsProvidenciaFiscal->arElementos);
        for ($i=0; $i < $numRegistros; $i++) {
            $this->obTLDOProvidenciaFiscal->setDado('cod_providencia_fiscal', $rsProvidenciaFiscal->arElementos[$i]['cod_providencia_fiscal']);
            $this->obTLDOProvidenciaFiscal->setDado('descricao', $arArgs['arDescProvidencia'][$i]);
            $this->obTLDOProvidenciaFiscal->setDado('valor', $arArgs['arValorProvidencia'][$i]);
            $this->obTLDOProvidenciaFiscal->setDado('cod_risco_fiscal', $arArgs['inCodRiscoFiscal']);
            $obErro = $this->obTLDOProvidenciaFiscal->exclusao($boTransacao);
            if ($obErro->ocorreu()) {
                throw new RLDOExcecao('Erro ao excluir as Providências Fiscais!');
            }
        }
        $this->obTLDORiscoFiscal->setDado('cod_risco_fiscal', $arArgs['inCodRiscoFiscal']);
        $this->obTLDORiscoFiscal->setDado('ano', $arArgs['inAnoLDO']);
        $this->obTLDORiscoFiscal->setDado('descricao', $arArgs['stDescRiscoFiscal']);
        $this->obTLDORiscoFiscal->setDado('valor', $arArgs['flValorRiscoFiscal']);
        $obErro = $this->obTLDORiscoFiscal->exclusao($boTransacao);
        if ($obErro->ocorreu()) {
            throw new RLDOExcecao('Erro ao excluir o Risco Fiscal!');
        }
        $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTLDOProvidenciaFiscal);

        return true;
    }

    /**
     * Inclui as Providências Fiscais
     *
     * @param  array $arArgs
     * @param  bool  $boTransacao
     * @return void
     */
    private function incluirProvidenciaFiscal(array $arArgs, $boTransacao)
    {
        $numProvidencias = count($arArgs['arDescProvidencia']);
        for ($i = 0; $i < $numProvidencias; $i++) {
            $this->obTLDOProvidenciaFiscal->proximoCod($arArgs['inCodProvidenciaFiscal'], $boTransacao);
            $this->obTLDOProvidenciaFiscal->setDado('cod_providencia_fiscal', $arArgs['inCodProvidenciaFiscal']);
            $this->obTLDOProvidenciaFiscal->setDado('descricao', $arArgs['arDescProvidencia'][$i]);
            $this->obTLDOProvidenciaFiscal->setDado('valor', $arArgs['arValorProvidencia'][$i]);
            $this->obTLDOProvidenciaFiscal->setDado('cod_risco_fiscal', $arArgs['inCodRiscoFiscal']);
            $obErro = $this->obTLDOProvidenciaFiscal->inclusao($boTransacao);
            if ($obErro->ocorreu()) {
                throw new RLDOExcecao('Erro ao incluir as Providências Fiscais!');
            }
        }
    }

    /**
     * Recupera os Riscos Fiscais de LDO em questão
     *
     * @param  int       $inAnoLDO
     * @return RecordSet
     */
    public function recuperarRiscoFiscal($inAnoLDO)
    {
        $obTransacao = new Transacao();
        $boFlagTransacao = false;
        $boTransacao = '';
        $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        $stFiltro = ' WHERE ano = ' . $inAnoLDO;
        $this->obTLDORiscoFiscal->recuperaTodos($rsRiscoFiscal, $stFiltro, null, $boTransacao);

        return $rsRiscoFiscal;
    }

    /**
     * Recupera as procidências de um risco fiscal.
     *
     * @param  int       $inCodRiscoFiscal
     * @return RecordSet
     */
    public function recuperarProvidenciaFiscal($inCodRiscoFiscal)
    {
        $obTransacao = new Transacao();
        $boFlagTransacao = false;
        $boTransacao = '';
        $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        $stFiltro = ' WHERE cod_risco_fiscal = ' . $inCodRiscoFiscal;
        $this->obTLDOProvidenciaFiscal->recuperaTodos($rsProvidenciaFiscal, $stFiltro, null, $boTransacao);

        return $rsProvidenciaFiscal;
    }

    /**
     * Verifica se já existe um Risco Fiscal cadastrado com o mesmo nome.
     *
     * @param  string $stDescOriginal
     * @param  string $stDescTratada
     * @return int    Numero de registros encontrados
     */
    public function checarCadastroRiscoFiscal($stDescOriginal, $stDescTratada)
    {
        $obTransacao = new Transacao();
        $boFlagTransacao = false;
        $boTransacao = '';
        $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        $stFiltro  = " WHERE UPPER(descricao) = UPPER('$stDescOriginal')   \n";
        $stFiltro .= "   OR UPPER(descricao) = UPPER('$stDescTratada')                                            \n";
        $this->obTLDORiscoFiscal->recuperaTodos($rsRiscoFiscal, $stFiltro, null, $boTransacao);

        return count($rsRiscoFiscal->arElementos);
    }

}
