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
 * Classe de Visão 02.10.14 - Manter Expansão das Despesas de Caráter Continuado
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

include_once CAM_GF_LDO_VISAO   . 'VLDOPadrao.class.php';
include_once CAM_GF_LDO_CLASSES . 'util/LDOAnotacoes.class.php';
include_once CAM_GF_LDO_CLASSES . 'excecao/LDOExcecao.class.php';

include_once CAM_GF_LDO_NEGOCIO . 'RLDOManterDespesaContinua.class.php';

class VLDOManterDespesaContinua extends VLDOPadrao implements IVLDOPadrao
{
    /**
     * Recupera a instância da classe
     * @return void
     */
    public static function recuperarInstancia()
    {
        return parent::recuperarInstancia(__CLASS__);
    }

    /**
     * Inicia as Regras da Classe
     * @return void
     */
    public function inicializar()
    {
        parent::inicializarRegra(__CLASS__);
    }

    public function recuperarLista($arParametros)
    {
        try {
            return $this->recuperarRegra()->recuperarLista($arParametros);
        } catch (RLDOExcecao $e) {
            SistemaLegado::exibeAviso($e->getMessage(), 'error', 'error', Sessao::getID(), '../');
        }
    }

    public function validarCampos(array $arParametros)
    {
        $arCampos = array('flAumentoPermanente'     => 'Aumento Permanente da Receita',
                          'flTransConstitucional'   => 'Transferências Constitucionais',
                          'flTransFUNDEB'           => 'Transferências ao FUNDEB',
                          'flReducaoPermanente'     => 'Redução Permanente de Despesa',
                          'flMargemBruta'           => 'Saldo Utilizado da Margem Bruta',
                          'flDOCC'                  => 'Novas DOCC',
                          'flDOCCPPP'               => 'Novas DOCC geradas por PPP');

        foreach ($arCampos as $stNome => $stDesc) {
            if ($arParametros[$stNome] == '0,00') {
                $stAviso = "Campo $stDesc inválido!()";

                SistemaLegado::exibeAviso($stAviso, 'form', 'erro');
                SistemaLegado::LiberaFrames(true,false);
                exit;
            }
        }
    }

    public function incluir(array $arParametros)
    {
        $this->validarCampos($arParametros);

        try {
            $inCodDespesa = $this->recuperarRegra()->incluir($arParametros);
            $stCaminho = 'FMManterDespesaContinua.php?stAcao=incluir';
            SistemaLegado::alertaAviso($stCaminho, $inCodDespesa, 'incluir', 'aviso', Sessao::getID(), '../');
        } catch (RLDOExcecao $e) {
            SistemaLegado::exibeAviso($e->getMessage(), 'n_incluir', 'erro');
            SistemaLegado::LiberaFrames(true,false);
        }
    }

    public function excluir(array $arParametros)
    {
        try {
            $inCodDespesa = $this->recuperarRegra()->excluir($arParametros);
            $stCaminho = 'LSManterDespesaContinua.php?stAcao=excluir';
            SistemaLegado::alertaAviso($stCaminho, $inCodDespesa, 'excluir', 'aviso', Sessao::getID(), '../');
        } catch (RLDOExcecao $e) {
            SistemaLegado::exibeAviso($e->getMessage(), 'n_excluir', 'erro');
            SistemaLegado::LiberaFrames(true,false);
        }
    }

    public function alterar(array $arParametros)
    {
        $this->validarCampos($arParametros);

        try {
            $inCodDespesa = $this->recuperarRegra()->alterar($arParametros);
            $stCaminho = 'LSManterDespesaContinua.php?stAcao=alterar';
            SistemaLegado::alertaAviso($stCaminho, $inCodDespesa, 'alterar', 'aviso', Sessao::getID(), '../');
        } catch (RLDOExcecao $e) {
            SistemaLegado::exibeAviso($e->getMessage(), 'n_alterar', 'erro');
            SistemaLegado::LiberaFrames(true,false);
        }
    }
}
