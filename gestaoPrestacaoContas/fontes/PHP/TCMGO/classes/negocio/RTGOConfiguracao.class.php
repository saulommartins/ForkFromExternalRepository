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
 * Classe de regra de configuracao do tcmgo
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

include_once CAM_GPC_TGO_MAPEAMENTO .'TTGOTipoCombustivel.class.php';
include_once CAM_GPC_TGO_MAPEAMENTO .'TTGOCombustivel.class.php';
include_once CAM_GPC_TGO_MAPEAMENTO .'TTGOCombustivelVinculo.class.php';
include_once CAM_GP_FRO_MAPEAMENTO .'TFrotaCombustivelItem.class.php';

class RTGOConfiguracao
{
    public $obTransacao,
        $obTTGOTipoCombustivel,
        $obTTGOCombustivel,
        $obTTGOCombustivelVinculo,
        $obTFrotaCombustivelItem,
        $inCodTipo,
        $inCodCombustivel,
        $inCodItem;

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
        $this->obTransacao                  = new Transacao();
        $this->obTTGOTipoCombustivel        = new TTGOTipoCombustivel();
        $this->obTTGOCombustivelVinculo     = new TTGOCombustivelVinculo();
        $this->obTTGOCombustivel            = new TTGOCombustivel();
        $this->obTFrotaCombustivelItem      = new TFrotaCombustivelItem();
    }

    /**
     * Método que vincula os combustiveis
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param boolean $boFlagTransacao
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function vincularCombustivel($boFlagTransacao = true, $boTransacao = '')
    {
        $obErro = $this->obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        if (!$obErro->ocorreu()) {

            $this->obTTGOCombustivelVinculo->setDado('cod_tipo'       , $this->inCodTipo);
            $this->obTTGOCombustivelVinculo->setDado('cod_combustivel', $this->inCodCombustivel);
            $this->obTTGOCombustivelVinculo->setDado('cod_item'       , $this->inCodItem);

            $obErro = $this->obTTGOCombustivelVinculo->inclusao($obTransacao);
        }
        //if ($obErro->ocorreu()) {
            //$obErro->setDescricao('Este cheque já está cadastrado para esta conta');
        //}

        $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTTesourariaCheque);

        return $obErro;
    }

    /**
     * Método que desvincula os combustiveis
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param boolean $boFlagTransacao
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function desvincularCombustivel($boFlagTransacao = true, $boTransacao = '')
    {
        $obErro = $this->obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        if (!$obErro->ocorreu()) {

            $this->obTTGOCombustivelVinculo->setDado('cod_tipo'       , $this->inCodTipo);
            $this->obTTGOCombustivelVinculo->setDado('cod_combustivel', $this->inCodCombustivel);
            $this->obTTGOCombustivelVinculo->setDado('cod_item'       , $this->inCodItem);

            $obErro = $this->obTTGOCombustivelVinculo->exclusao($obTransacao);
        }
        //if ($obErro->ocorreu()) {
            //$obErro->setDescricao('Este cheque já está cadastrado para esta conta');
        //}

        $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTTesourariaCheque);

        return $obErro;
    }

    /**
     * Método listTipoCombustivel, lista os tipos de combustiveis do tcmgo
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @return void
     */
    public function listTipoCombustivelTCM(&$rsTipoCombustivel)
    {
        $this->obTTGOTipoCombustivel->recuperaTodos($rsTipoCombustivel);
    }

    /**
     * Método listTipoCombustivel, lista os tipos de combustiveis do tcmgo
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @return void
     */
    public function listCombustivelTCM(&$rsCombustivel)
    {
        if ($this->inCodTipo != '') {
            $stFiltro = ' cod_tipo = ' . $this->inCodTipo . ' AND ';
        }
        if ($stFiltro != '') {
            $stFiltro = ' WHERE ' . substr($stFiltro,0,-4);
        }
        $this->obTTGOCombustivel->recuperaTodos($rsCombustivel, $stFiltro);
    }

    /**
     * Método listCombustivel, lista os tipos de combustiveis do sw
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @return void
     */
    public function listCombustivel(&$rsCombustivel)
    {
        //Adiciona um filtro para nao trazer os combustiveis ja vinculados
        $stFiltro = "
           WHERE NOT EXISTS( SELECT 1
                                FROM tcmgo.combustivel_vinculo
                               WHERE combustivel_item.cod_item = combustivel_vinculo.cod_item
                                 and combustivel_vinculo.cod_combustivel = " . $this->inCodCombustivel . "
                                 and combustivel_vinculo.cod_tipo        = " . $this->inCodTipo . "
                            )";
        $this->obTFrotaCombustivelItem->recuperaRelacionamento($rsCombustivel, $stFiltro);
    }

    /**
     * Método listCombustivelVinculado, lista os tipos de combustiveis do sw vinculados
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @return void
     */
    public function listCombustivelVinculado(&$rsCombustivel)
    {
        //Adiciona um filtro para trazer os combustiveis ja vinculados
        $stFiltro = "
            WHERE EXISTS( SELECT 1
                            FROM tcmgo.combustivel_vinculo
                           WHERE combustivel_item.cod_item = combustivel_vinculo.cod_item
                             AND combustivel_vinculo.cod_tipo        = " . $this->inCodTipo . "
                             AND combustivel_vinculo.cod_combustivel = " . $this->inCodCombustivel . "
                            )
        ";
        if ($this->inCodItem != '') {
            $stFiltro.= ' AND combustivel_item.cod_item = ' . $this->inCodItem;
        }

        $this->obTFrotaCombustivelItem->recuperaRelacionamento($rsCombustivel, $stFiltro);
    }
}

?>