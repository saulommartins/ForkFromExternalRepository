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
    * Página da regra de negócio da evolução da dívida
    * Data de Criação   : 03/07/2009

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz

    * @package URBEM
    * @subpackage

    $Id:$
 */

include_once CAM_GF_LDO_NEGOCIO.'RLDOLDO.class.php';
include_once CAM_GF_LDO_MAPEAMENTO.'TLDOTipoDivida.class.php';
include_once CAM_GF_LDO_MAPEAMENTO.'TLDOConfiguracaoDivida.class.php';

class RLDOEvolucaoDivida
{
    public $obTransacao,
           $obRLDOLDO,
           $obTLDOTipoDivida,
           $obTLDOConfiguracaoDivida,
           $inCodTipo,
           $inCodSelic,
           $stTipo,
           $stExercicio,
           $flValor;

    /**
     * Método contrutor, instancia as classes necessarias.
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     *
     * @return void
     */
    public function __construct()
    {
        $this->obTransacao              = new Transacao();
        $this->obRLDOLDO                = new RLDOLDO();
        $this->obTLDOTipoDivida         = new TLDOTipoDivida();
        $this->obTLDOConfiguracaoDivida = new TLDOConfiguracaoDivida();
    }

    /**
     * Método para incluir a dívida
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function incluir(&$boTransacao = '')
    {
        $obErro = $this->obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        $this->obTLDOConfiguracaoDivida->setDado('cod_ppa'  ,$this->obRLDOLDO->obRPPAManterPPA->inCodPPA);
        $this->obTLDOConfiguracaoDivida->setDado('ano'      ,$this->obRLDOLDO->inAno);
        $this->obTLDOConfiguracaoDivida->setDado('cod_tipo' ,$this->inCodTipo);
        $this->obTLDOConfiguracaoDivida->setDado('exercicio',$this->stExercicio);
        $this->obTLDOConfiguracaoDivida->setDado('valor'    ,$this->flValor);

        $obErro = $this->obTLDOConfiguracaoDivida->inclusao($boTransacao);

        $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTLDOConfiguracaoDivida);

        return $obErro;
    }

    /**
     * Método para alterar a dívida
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function alterar(&$boTransacao = '')
    {
        $obErro = $this->obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        $this->obTLDOConfiguracaoDivida->setDado('cod_ppa'  ,$this->obRLDOLDO->obRPPAManterPPA->inCodPPA);
        $this->obTLDOConfiguracaoDivida->setDado('ano'      ,$this->obRLDOLDO->inAno);
        $this->obTLDOConfiguracaoDivida->setDado('cod_tipo' ,$this->inCodTipo);
        $this->obTLDOConfiguracaoDivida->setDado('exercicio',$this->stExercicio);
        $this->obTLDOConfiguracaoDivida->setDado('valor'    ,$this->flValor);

        $obErro = $this->obTLDOConfiguracaoDivida->alteracao($boTransacao);

        $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTLDOConfiguracaoDivida);

        return $obErro;
    }

    /**
     * Método para buscar dividas do LDO
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function listDividasLDO(&$rsDividas)
    {

        $this->obTLDOTipoDivida->setDado('cod_ppa', $this->obRLDOLDO->obRPPAManterPPA->inCodPPA);
        $this->obTLDOTipoDivida->setDado('ano'    , $this->obRLDOLDO->inAno);

        $obErro = $this->obTLDOTipoDivida->listDividasLDO($rsDividas);

        return $obErro;
    }

    /**
     * Método para buscar servicos das dividas do LDO
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function listServicosLDO(&$rsDividas)
    {

        $this->obTLDOTipoDivida->setDado('cod_ppa'  , $this->obRLDOLDO->obRPPAManterPPA->inCodPPA);
        $this->obTLDOTipoDivida->setDado('ano'      , $this->obRLDOLDO->inAno);
        $this->obTLDOTipoDivida->setDado('cod_selic', $this->inCodSelic);

        $obErro = $this->obTLDOTipoDivida->listServicosLDO($rsDividas);

        return $obErro;
    }

    /**
     * Método para verificar se o valor existe na base
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function verificaDado(&$boExiste)
    {

        $this->obTLDOConfiguracaoDivida->setDado('cod_tipo' , $this->inCodTipo);
        $this->obTLDOConfiguracaoDivida->setDado('exercicio', $this->stExercicio);

        $obErro = $this->obTLDOConfiguracaoDivida->recuperaPorChave($rsDivida);

        if ($rsDivida->getNumLinhas() > 0) {
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
     * @author      Desenvolvedor   Eduardo Schitz      <eduardo.schitz@cnm.org.br>
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
