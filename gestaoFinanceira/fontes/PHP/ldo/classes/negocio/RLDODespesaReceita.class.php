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
 * Classe de regra de despesa receita
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

include_once CAM_GF_LDO_NEGOCIO    . 'RLDOLDO.class.php';
include_once CAM_GF_LDO_MAPEAMENTO . 'TLDOTipoReceitaDespesa.class.php';
include_once CAM_GF_LDO_MAPEAMENTO . 'TLDOConfiguracaoReceitaDespesa.class.php';

class RLDODespesaReceita
{
    public $obTransacao,
           $obRLDOLDO,
           $obTLDOTipoReceitaDespesa,
           $inCodTipo,
           $stTipo,
           $stExercicio,
           $flArrecadadoLiquidado,
           $flPrevistoFixado,
           $flProjetado,
           $arDespesaReceita;

    /**
     * Método contrutor, instancia as classes necessarias.
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @return void
     */
    public function __construct()
    {
        $this->obTransacao                      = new Transacao();
        $this->obRLDOLDO                        = new RLDOLDO();
        $this->obTLDOTipoReceitaDespesa         = new TLDOTipoReceitaDespesa();
        $this->obTLDOConfiguracaoReceitaDespesa = new TLDOConfiguracaoReceitaDespesa();
    }

    /**
     * Método para incluir a receita/despesa
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function incluir(&$boTransacao = '')
    {
        $obErro = $this->obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        $this->obTLDOConfiguracaoReceitaDespesa->setDado('cod_ppa'                ,$this->obRLDOLDO->obRPPAManterPPA->inCodPPA);
        $this->obTLDOConfiguracaoReceitaDespesa->setDado('ano'                    ,$this->obRLDOLDO->inAno);
        $this->obTLDOConfiguracaoReceitaDespesa->setDado('cod_tipo'               ,$this->inCodTipo);
        $this->obTLDOConfiguracaoReceitaDespesa->setDado('tipo'                   ,$this->stTipo);
        $this->obTLDOConfiguracaoReceitaDespesa->setDado('exercicio'              ,$this->stExercicio);
        $this->obTLDOConfiguracaoReceitaDespesa->setDado('vl_arrecadado_liquidado',$this->flArrecadadoLiquidado);
        $this->obTLDOConfiguracaoReceitaDespesa->setDado('vl_previsto_fixado'     ,$this->flPrevistoFixado);
        $this->obTLDOConfiguracaoReceitaDespesa->setDado('vl_projetado'           ,$this->flProjetado);

        $obErro = $this->obTLDOConfiguracaoReceitaDespesa->inclusao($boTransacao);

        $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTLDOConfiguracaoReceitaDespesa);

        return $obErro;
    }

    /**
     * Método para alterar a receita/despesa
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function alterar(&$boTransacao = '')
    {
        $obErro = $this->obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        $this->obTLDOConfiguracaoReceitaDespesa->setDado('cod_ppa'                ,$this->obRLDOLDO->obRPPAManterPPA->inCodPPA);
        $this->obTLDOConfiguracaoReceitaDespesa->setDado('cod_tipo'               ,$this->inCodTipo);
        $this->obTLDOConfiguracaoReceitaDespesa->setDado('tipo'                   ,$this->stTipo);
        $this->obTLDOConfiguracaoReceitaDespesa->setDado('exercicio'              ,$this->stExercicio);
        $obErro = $this->obTLDOConfiguracaoReceitaDespesa->recuperaPorChave($rsReceitaDespesa);
        
        $this->obTLDOConfiguracaoReceitaDespesa->setDado('ano'                    ,$rsReceitaDespesa->getCampo('ano'));
        $this->obTLDOConfiguracaoReceitaDespesa->setDado('vl_arrecadado_liquidado',$this->flArrecadadoLiquidado);
        $this->obTLDOConfiguracaoReceitaDespesa->setDado('vl_previsto_fixado'     ,$this->flPrevistoFixado);
        $this->obTLDOConfiguracaoReceitaDespesa->setDado('vl_projetado'           ,$this->flProjetado);

        $obErro = $this->obTLDOConfiguracaoReceitaDespesa->alteracao($boTransacao);
        $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTLDOConfiguracaoReceitaDespesa);

        return $obErro;
    }

    /**
     * Método para buscar receitas do LDO
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function listReceitasLDO(&$rsReceitas)
    {
        $this->obTLDOTipoReceitaDespesa->setDado('funcao' ,'ldo.fn_receita_configuracao');
        $this->obTLDOTipoReceitaDespesa->setDado('cod_ppa',$this->obRLDOLDO->obRPPAManterPPA->inCodPPA);
        $this->obTLDOTipoReceitaDespesa->setDado('ano'    ,$this->obRLDOLDO->inAno);

        $obErro = $this->obTLDOTipoReceitaDespesa->listValoresTabela($rsReceitas);
        return $obErro;
    }

    /**
     * Método para buscar receitas previstas do LDO
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function listReceitasPrevistasLDO(&$rsReceitas)
    {
        $this->obTLDOTipoReceitaDespesa->setDado('funcao' ,'ldo.fn_receita_prevista_configuracao');
        $this->obTLDOTipoReceitaDespesa->setDado('cod_ppa',$this->obRLDOLDO->obRPPAManterPPA->inCodPPA);
        $this->obTLDOTipoReceitaDespesa->setDado('ano'    ,$this->obRLDOLDO->inAno);

        $obErro = $this->obTLDOTipoReceitaDespesa->listValoresTabela($rsReceitas);

        return $obErro;
    }

    /**
     * Método para buscar receitas projetadas do LDO
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function listReceitasProjetadasLDO(&$rsReceitas)
    {
        $this->obTLDOTipoReceitaDespesa->setDado('funcao' ,'ldo.fn_receita_projetada_configuracao');
        $this->obTLDOTipoReceitaDespesa->setDado('cod_ppa',$this->obRLDOLDO->obRPPAManterPPA->inCodPPA);
        $this->obTLDOTipoReceitaDespesa->setDado('ano'    ,$this->obRLDOLDO->inAno);

        $obErro = $this->obTLDOTipoReceitaDespesa->listValoresTabela($rsReceitas);

        return $obErro;
    }

    /**
     * Método para buscar despesas do LDO
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function listDespesasLDO(&$rsReceitas)
    {
        $this->obTLDOTipoReceitaDespesa->setDado('funcao' ,'ldo.fn_despesa_configuracao');
        $this->obTLDOTipoReceitaDespesa->setDado('cod_ppa',$this->obRLDOLDO->obRPPAManterPPA->inCodPPA);
        $this->obTLDOTipoReceitaDespesa->setDado('ano'    ,$this->obRLDOLDO->inAno);

        $obErro = $this->obTLDOTipoReceitaDespesa->listValoresTabela($rsReceitas);

        return $obErro;
    }

    /**
     * Método para buscar despesas fixada do LDO
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function listDespesasFixadasLDO(&$rsReceitas)
    {
        $this->obTLDOTipoReceitaDespesa->setDado('funcao' ,'ldo.fn_despesa_fixada_configuracao');
        $this->obTLDOTipoReceitaDespesa->setDado('cod_ppa',$this->obRLDOLDO->obRPPAManterPPA->inCodPPA);
        $this->obTLDOTipoReceitaDespesa->setDado('ano'    ,$this->obRLDOLDO->inAno);
        
        $obErro = $this->obTLDOTipoReceitaDespesa->listValoresTabela($rsReceitas);
        return $obErro;
    }

    /**
     * Método para buscar despesas projetadas do LDO
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function listDespesasProjetadasLDO(&$rsReceitas)
    {
        $this->obTLDOTipoReceitaDespesa->setDado('funcao' ,'ldo.fn_despesa_projetada_configuracao');
        $this->obTLDOTipoReceitaDespesa->setDado('cod_ppa',$this->obRLDOLDO->obRPPAManterPPA->inCodPPA);
        $this->obTLDOTipoReceitaDespesa->setDado('ano'    ,$this->obRLDOLDO->inAno);

        $obErro = $this->obTLDOTipoReceitaDespesa->listValoresTabela($rsReceitas);

        return $obErro;
    }

    /**
     * Método para verificar se o valor existe na base
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function verificaDado(&$boExiste)
    {

        $this->obTLDOConfiguracaoReceitaDespesa->setDado('cod_tipo' , $this->inCodTipo);
        $this->obTLDOConfiguracaoReceitaDespesa->setDado('tipo'     , $this->stTipo);
        $this->obTLDOConfiguracaoReceitaDespesa->setDado('exercicio', $this->stExercicio);
        $this->obTLDOConfiguracaoReceitaDespesa->setDado('cod_ppa'  , $this->obRLDOLDO->obRPPAManterPPA->inCodPPA);
        $this->obTLDOConfiguracaoReceitaDespesa->setDado('ano'      , null);

        $obErro = $this->obTLDOConfiguracaoReceitaDespesa->recuperaPorChave($rsReceitaDespesa);
        if ($rsReceitaDespesa->getNumLinhas() > 0) {
            $boExiste = true;
        } else {
            $boExiste = false;
        }

        return $obErro;
    }

    /**
     * Método para buscar acoes
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function getAcao(&$rsAcao, $stAcao)
    {
        $stFiltro .= ' AND acao.cod_acao = ' . $this->obRPPAManterAcao->inCodAcao . ' ';
        $this->obTLDOAcaoValidada->setDado('cod_ppa',$this->obRPPAManterAcao->obRPPAManterPrograma->obRPPAManterPPA->inCodPPA);
        $this->obTLDOAcaoValidada->setDado('ano',$this->obRPPAManterAcao->inAno);

        if ($stAcao == 'incluir') {
            $obErro = $this->obTLDOAcaoValidada->listAcaoNaoValidada($rsAcao,$stFiltro);
        } else {
            $obErro = $this->obTLDOAcaoValidada->listAcaoValidada($rsAcao,$stFiltro);
        }

        return $obErro;
    }
}

?>
