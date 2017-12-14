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

include_once CAM_GF_PPA_NEGOCIO . 'RPPAManterPPA.class.php';
include_once CAM_GF_LDO_MAPEAMENTO . 'TLDO.class.php';

class RLDOLDO
{
    public $obTransacao,
           $obRPPAManterPPA,
           $obTLDO,
           $inAno,
           $stTimestamp;

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
        $this->obTransacao     = new Transacao();
        $this->obRPPAManterPPA = new RPPAManterPPA();
        $this->obTLDO          = new TLDO();
    }

    /**
     * Método para listar os ldos
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @return void
     */
    public function listar(&$rsLDO, $boTransacao = '')
    {
        if ($this->inAno != '') {
            $stFiltro .= " ldo.ano = '".$this->inAno."' AND ";
        }
        if ($this->obRPPAManterPPA->inCodPPA != '') {
            $stFiltro .= " ldo.cod_ppa = ".$this->obRPPAManterPPA->inCodPPA." AND ";
        }

        if ($stFiltro != '') {
            $stFiltro = ' WHERE ' . substr($stFiltro,0,-4);
        }

        $obErro = $this->obTLDO->recuperaTodos($rsLDO,$stFiltro, '', $boTransacao);

        return $obErro;
    }

    /**
     * Método para incluir LDO
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @return object
     */
    public function incluir(&$boTransacao = '')
    {
        $boFlagTransacao = false;

        $obErro = $this->obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        $this->obTLDO->setDado('cod_ppa'  , $this->obRPPAManterPPA->inCodPPA);
        $this->obTLDO->setDado('ano'      , $this->inAno);
        $this->obTLDO->setDado('timestamp', $this->stTimestamp);

        $obErro = $this->obTLDO->inclusao($boTransacao);

        $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTLDO);

        return $obErro;
    }

    /**
     * Metodo que atualiza o timestamp de um LDO
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @return object
     */
    public function updateTimestamp(&$boTransacao = '')
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        $this->getTimestamp($boTransaca);

        $this->obTLDO->setDado('cod_ppa'  , $this->obRPPAManterPPA->inCodPPA);
        $this->obTLDO->setDado('ano'      , $this->inAno);
        $this->obTLDO->setDado('timestamp', $this->stTimestamp);

        $obErro = $this->obTLDO->alteracao($boTransacao);

        $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obRPPAManterPPA);

        return $obErro;
    }

    /**
     * Metodo que busca o timestamp
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @return object
     */
    public function getTimestamp(&$boTransacao = '')
    {
        $boFlagTransacao = false;

        $obErro = $this->obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        $obErro = $this->obTLDO->getTimestamp($rsTimestamp,'','',$boTransacao);

        $this->stTimestamp = $rsTimestamp->getCampo('timestamp');

        $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTLDO);

        return $obErro;
    }
}

?>
