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
 * Classe de regra da Evolucao do patrimonio liquido
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

include_once CAM_GF_LDO_NEGOCIO    . 'RLDOLDO.class.php';
include_once CAM_GF_LDO_MAPEAMENTO . 'TLDOTipoEvolucaoPatrimonioLiquido.class.php';
include_once CAM_GF_LDO_MAPEAMENTO . 'TLDOConfiguracaoEvolucaoPatrimonioLiquido.class.php';

class RLDOEvolucaoPatrimonioLiquido
{
    public $obTransacao,
           $obRLDOLDO,
           $obTLDOTipoEvolucaoPatrimonioLiquido,
           $obTLDOConfiguracaoEvolucaoPatrimonioLiquido,
           $inCodTipo,
           $boRPPS,
           $stExercicio,
           $flValor,
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
        $this->obTransacao                                 = new Transacao();
        $this->obRLDOLDO                                   = new RLDOLDO();
        $this->obTLDOTipoEvolucaoPatrimonioLiquido         = new TLDOTipoEvolucaoPatrimonioLiquido();
        $this->obTLDOConfiguracaoEvolucaoPatrimonioLiquido = new TLDOConfiguracaoEvolucaoPatrimonioLiquido();
    }

    /**
     * Método para incluir a evolucao do patrimonio liquido
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

        $this->obTLDOConfiguracaoEvolucaoPatrimonioLiquido->setDado('cod_ppa'  ,$this->obRLDOLDO->obRPPAManterPPA->inCodPPA);
        $this->obTLDOConfiguracaoEvolucaoPatrimonioLiquido->setDado('ano'      ,$this->obRLDOLDO->inAno);
        $this->obTLDOConfiguracaoEvolucaoPatrimonioLiquido->setDado('cod_tipo' ,$this->inCodTipo);
        $this->obTLDOConfiguracaoEvolucaoPatrimonioLiquido->setDado('rpps'     ,$this->boRPPS);
        $this->obTLDOConfiguracaoEvolucaoPatrimonioLiquido->setDado('exercicio',$this->stExercicio);
        $this->obTLDOConfiguracaoEvolucaoPatrimonioLiquido->setDado('valor'    ,$this->flValor);

        $obErro = $this->obTLDOConfiguracaoEvolucaoPatrimonioLiquido->inclusao($boTransacao);

        $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTLDOConfiguracaoReceitaDespesa);

        return $obErro;
    }

    /**
     * Método para alterar a evolucao do patrimonio liquido
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

        $this->obTLDOConfiguracaoEvolucaoPatrimonioLiquido->setDado('cod_ppa'  ,$this->obRLDOLDO->obRPPAManterPPA->inCodPPA);
        $this->obTLDOConfiguracaoEvolucaoPatrimonioLiquido->setDado('ano'      ,$this->obRLDOLDO->inAno);
        $this->obTLDOConfiguracaoEvolucaoPatrimonioLiquido->setDado('cod_tipo' ,$this->inCodTipo);
        $this->obTLDOConfiguracaoEvolucaoPatrimonioLiquido->setDado('rpps'     ,$this->boRPPS);
        $this->obTLDOConfiguracaoEvolucaoPatrimonioLiquido->setDado('exercicio',$this->stExercicio);
        $this->obTLDOConfiguracaoEvolucaoPatrimonioLiquido->setDado('valor'    ,$this->flValor);

        $obErro = $this->obTLDOConfiguracaoEvolucaoPatrimonioLiquido->alteracao($boTransacao);

        $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTLDOConfiguracaoReceitaDespesa);

        return $obErro;
    }

    /**
     * Método para verificar se o valor existe na base
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param boolean $boExiste
     *
     * @return object $obErro
     */
    public function verificaDado(&$boExiste)
    {

        $this->obTLDOConfiguracaoEvolucaoPatrimonioLiquido->setDado('cod_tipo' , $this->inCodTipo);
        $this->obTLDOConfiguracaoEvolucaoPatrimonioLiquido->setDado('exercicio', $this->stExercicio);
        $this->obTLDOConfiguracaoEvolucaoPatrimonioLiquido->setDado('cod_ppa'  , $this->obRLDOLDO->obRPPAManterPPA->inCodPPA);
        $this->obTLDOConfiguracaoEvolucaoPatrimonioLiquido->setDado('rpps'     , $this->boRPPS);
        $this->obTLDOConfiguracaoEvolucaoPatrimonioLiquido->setDado('ano'      , $this->obRLDOLDO->inAno);

        $obErro = $this->obTLDOConfiguracaoEvolucaoPatrimonioLiquido->recuperaPorChave($rsEvolucaoPatrimonioLiquido);

        if ($rsEvolucaoPatrimonioLiquido->getNumLinhas() > 0) {
            $boExiste = true;
        } else {
            $boExiste = false;
        }

        return $obErro;
    }

    /**
     * Método para buscar os valores
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param objetc $rsNaoRPPS
     *
     * @return object $obErro
     */
    public function listValores(&$rsNaoRPPS)
    {

        $this->obTLDOTipoEvolucaoPatrimonioLiquido->setDado('cod_ppa',$this->obRLDOLDO->obRPPAManterPPA->inCodPPA);
        $this->obTLDOTipoEvolucaoPatrimonioLiquido->setDado('ano'    ,$this->obRLDOLDO->inAno);
        $this->obTLDOTipoEvolucaoPatrimonioLiquido->setDado('rpps'   ,(($this->boRPPS) ? 'true' : 'false'));

        $obErro = $this->obTLDOTipoEvolucaoPatrimonioLiquido->listValores($rsNaoRPPS);

        return $obErro;
    }

}

?>
