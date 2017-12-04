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
 * Classe de Negócio 02.10.14 - Manter Expansão das Despesas de Caráter Continuado
 * Data de Criação: 24/03/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Analista: Bruno Ferreira <bruno.ferreira>
 * @author Programador: Pedro Vaz de Mello de Medeiros <pedro.medeiros>
 * @package gestaoFinanceira
 * @subpackage LDO
 * @uc 02.10.14 - Manter Expansão das Despesas de Caráter Continuado
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';

include_once CAM_GF_LDO_NEGOCIO    . 'RLDOPadrao.class.php';
include_once CAM_GF_LDO_MAPEAMENTO . 'TLDODespesaContinua.class.php';

class RLDOManterDespesaContinua extends RLDOPadrao implements IRLDOPadrao
{
    private $obTLDODespesaContinua;

    public static function recuperarInstancia()
    {
        return parent::recuperarInstancia(__CLASS__);
    }

    protected function inicializar()
    {
        $this->obTLDODespesaContinua = new TLDODespesaContinua();
    }

    private function incluirDespesaContinua($arArgs, $boTransacao = null)
    {
        $obErro = new Erro();

        $this->obTLDODespesaContinua->setDado('cod_despesa',                  $arArgs['inCodDespesa']);
        $this->obTLDODespesaContinua->setDado('ano',                          $arArgs['inAnoLDO']);
        $this->obTLDODespesaContinua->setDado('aumento_permanente',           $arArgs['flAumentoPermanente']);
        $this->obTLDODespesaContinua->setDado('transferencia_constitucional', $arArgs['flTransConstitucional']);
        $this->obTLDODespesaContinua->setDado('transferencia_fundeb',         $arArgs['flTransFUNDEB']);
        $this->obTLDODespesaContinua->setDado('reducao_permanente',           $arArgs['flReducaoPermanente']);
        $this->obTLDODespesaContinua->setDado('saldo_utilizado_margem_bruta', $arArgs['flMargemBruta']);
        $this->obTLDODespesaContinua->setDado('docc',                         $arArgs['flDOCC']);
        $this->obTLDODespesaContinua->setDado('docc_ppp',                     $arArgs['flDOCCPPP']);

        $obErro = $this->obTLDODespesaContinua->inclusao($boTransacao);

        return $obErro;
    }

    private function alterarDespesaContinua($arArgs, $boTransacao = null)
    {
        $obErro = new Erro();

        $this->obTLDODespesaContinua->setDado('cod_despesa',                  $arArgs['inCodDespesa']);
        $this->obTLDODespesaContinua->setDado('ano',                          $arArgs['inAnoLDO']);
        $this->obTLDODespesaContinua->setDado('aumento_permanente',           $arArgs['flAumentoPermanente']);
        $this->obTLDODespesaContinua->setDado('transferencia_constitucional', $arArgs['flTransConstitucional']);
        $this->obTLDODespesaContinua->setDado('transferencia_fundeb',         $arArgs['flTransFUNDEB']);
        $this->obTLDODespesaContinua->setDado('reducao_permanente',           $arArgs['flReducaoPermanente']);
        $this->obTLDODespesaContinua->setDado('saldo_utilizado_margem_bruta', $arArgs['flMargemBruta']);
        $this->obTLDODespesaContinua->setDado('docc',                         $arArgs['flDOCC']);
        $this->obTLDODespesaContinua->setDado('docc_ppp',                     $arArgs['flDOCCPPP']);

        $obErro = $this->obTLDODespesaContinua->alteracao($boTransacao);

        return $obErro;
    }

    private function excluirDespesaContinua($inCodDespesa, $inAnoLDO, $boTransacao = null)
    {
        $obErro = new Erro();

        $this->obTLDODespesaContinua->setDado('cod_despesa', $inCodDespesa);
        $this->obTLDODespesaContinua->setDado('ano',         $inAnoLDO);

        $obErro = $this->obTLDODespesaContinua->exclusao($boTransacao);

        return $obErro;
    }

    /**
     * Inclui uma nova Despesa Contínua
     * @param $arArgs array contendo todos os argumentos
     * @return integer o código da despesa contínua incluída
     */
    public function incluir(array $arArgs)
    {
        $obTransacao = new Transacao();
        $boFlagTransacao = false;
        $boTransacao = '';

        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        if ($obErro->ocorreu()) {
            throw new RLDOExcecao('Ocorreu um erro ao tentar iniciar transação', $this->recuperarAnotacoes());
        }

        if ($this->validarDuplicidadeDespesaContinua($arArgs, $boTransacao)) {
            throw new RLDOExcecao('Despesa de caráter continuado já cadastrado para este LDO', $this->recuperarAnotacoes());
        }

        $this->obTLDODespesaContinua->proximoCod($arArgs['inCodDespesa'], $boTransacao);

        $obErro = $this->incluirDespesaContinua($arArgs, $boTransacao);

        if ($obErro->ocorreu()) {
            throw new RLDOExcecao('Ocorreu um erro ao incluir a despesa contínua');
        }

        $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTLDODespesaContinua);

        return $arArgs['inCodDespesa'];
    }

    /**
     * Altera uma Despesa Contínua
     * @param $arArgs array contendo todos os argumentos
     * @return integer o código da despesa contínua alterada
     */
    public function alterar(array $arArgs)
    {
        $obTransacao = new Transacao();
        $boFlagTransacao = false;
        $boTransacao = '';

        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        if ($obErro->ocorreu()) {
            throw new RLDOExcecao('Ocorreu um erro ao tentar iniciar transação.', $this->recuperarAnotacoes());
        }

        $obErro = $this->alterarDespesaContinua($arArgs, $boTransacao);

        if ($obErro->ocorreu()) {
            throw new RLDOExcecao('Ocorreu um erro ao alterar a despesa contínua');
        }

        $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTLDODespesaContinua);

        return $arArgs['inCodDespesa'];
    }

    /**
     * Exclui uma Despesa Contínua
     * @param $arArgs array contendo todos os argumentos
     * @return integer o código da despesa contínua excluida
     */
    public function excluir(array $arArgs)
    {
        $obTransacao = new Transacao();
        $boFlagTransacao = false;
        $boTransacao = '';

        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        if ($obErro->ocorreu()) {
            throw new RLDOExcecao('Ocorreu um erro ao tentar iniciar transação.', $this->recuperarAnotacoes());
        }

        $obErro = $this->excluirDespesaContinua($arArgs['inCodDespesa'], $arArgs['inAnoLDO'], $boTransacao);

        if ($obErro->ocorreu()) {
            throw new RLDOExcecao('Ocorreu um erro ao excluir a despesa contínua');
        }

        $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTLDODespesaContinua);

        return $arArgs['inCodDespesa'];
    }

    /**
     * Recupera todos os dados relativos a uma Despesa Contínua.
     *
     * @param $inCodDespesa código da Despesa Contínua
     * @param $boTransacao se houve transação
     * @return array atributos da Despesa Contínua
     */
    private function recuperarDespesaContinua($inCodDespesa, $boTransacao = null)
    {
        $stFiltro = ' WHERE cod_despesa = ' . $inCodDespesa;
        $stOrdem  = '';

        $obErro = $this->obTLDODespesaContinua->recuperaRelacionamento($rsDespesa, $stFiltro, $stOrdem, $boTransacao);

        if ($obErro->ocorreu()) {
            throw new RLDOExcecao('Ocorreu um erro ao tentar obter Despesa Contínua.', $this->recuperarAnotacoes());
        }

        return array_pop($rsDespesa->getElementos());
    }

    /**
     * Validar duplicidade de cadastro de Despesa Contínua
     *
     * @param $inCodDespesa código da Despesa Contínua
     * @param $boTransacao se houve transação
     * @return boolean
     */
    private function validarDuplicidadeDespesaContinua($arArgs, $boTransacao = null)
    {
        $stFiltro = " WHERE ano = '" . $arArgs['inAnoLDO'] . "'";

        $this->obTLDODespesaContinua->recuperaTodos($rsDespesa, $stFiltro, null, $boTransacao);

        return (int) !$rsDespesa->eof();
    }

    /**
     * Recupera lista de Despesas Contínuas
     *
     * @param  array     $arParametros
     * @return RecordSet
     */
    public function recuperarLista($arArgs, $boTransacao = null)
    {
        if ($arArgs['inAnoLDO']) {
            $stFiltro = ' WHERE ano = ' . $arArgs['inAnoLDO'];
        }

        $obErro = $this->obTLDODespesaContinua->recuperaTodos($rsLista, $stFiltro, $stOrdem, $boTransacao);

        if ($obErro->ocorreu()) {
            throw new RLDOExcecao('Ocorreu um erro ao ler lista de Despesas Contínuas', $this->recuperarAnotacoes());
        }

        return $rsLista;
    }
}
