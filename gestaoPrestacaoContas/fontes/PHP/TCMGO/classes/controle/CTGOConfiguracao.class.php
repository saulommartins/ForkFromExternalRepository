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
 * Classe de controle - configuracao tcmgo
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */
class CTGOConfiguracao
{
    public $obModel;

    /**
     * Metodo construtor, seta o atributo obModel com o que vier na assinatura da funcao
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param object $obModel Classe de Negocio
     *
     * @return void
     */
    public function __construct(&$obModel)
    {
        $this->obModel = $obModel;
    }

    /**
     * Metodo preencheCombustivelTCM, preenche o combo dos combustiveis do TCM
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Classe de Negocio
     *
     * @return void
     */
    public function preencheCombustivelTCM($arParam)
    {
        $stJs.= "jq('#inCodCombustivelTCM').removeOption(/./);";
        if ($arParam['inCodTipoCombustivelTCM'] != '') {
            $this->obModel->inCodTipo = $arParam['inCodTipoCombustivelTCM'];
            $this->obModel->listCombustivelTCM($rsCombustivel);

            if ($arParam['inCodTipoCombustivelTCM'] != '') {
                while (!$rsCombustivel->eof()) {
                    $stJs.= "jq('#inCodCombustivelTCM').addOption('" . $rsCombustivel->getCampo('cod_combustivel') . "','" . $rsCombustivel->getCampo('descricao') . "');";

                    $rsCombustivel->proximo();
                }
            }
        }

        $stJs.= "jq('#inCodCombustivelTCM').selectOptions('',true);";

        echo $stJs;
    }

    /**
     * Metodo preencheCombustivelSW, preenche o combo dos combustiveis do SW
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Classe de Negocio
     *
     * @return void
     */
    public function preencheCombustivel($arParam)
    {
        $stJs.= "jq('#arCodCombustivelSWDisponivel').removeOption(/./);";
        $stJs.= "jq('#arCodCombustivelSWSelecionado').removeOption(/./);";

        if ($arParam['inCodTipoCombustivelTCM'] != '' AND $arParam['inCodCombustivelTCM'] != '') {
            //Retorna os combustiveis nao vinculados
            $this->obModel->inCodTipo = $arParam['inCodTipoCombustivelTCM'];
            $this->obModel->inCodCombustivel = $arParam['inCodCombustivelTCM'];
            $this->obModel->listCombustivel($rsCombustivel);
            //Adiciona os combustiveis no select de disponiveis
            while (!$rsCombustivel->eof()) {
                $stJs.= "jq('#arCodCombustivelSWDisponivel').addOption('" . $rsCombustivel->getCampo('cod_item') . "','" . $rsCombustivel->getCampo('nom_combustivel') . "');";
                $rsCombustivel->proximo();
            }

            //Recupera os combustiveis vinculados
            $this->obModel->listCombustivelVinculado($rsCombustivel);
            //Adiciona os combustiveis no select de vinculados
            while (!$rsCombustivel->eof()) {
                $stJs.= "jq('#arCodCombustivelSWSelecionado').addOption('" . $rsCombustivel->getCampo('cod_item') . "','" . $rsCombustivel->getCampo('nom_combustivel') . "');";
                $rsCombustivel->proximo();
            }

        }

        echo $stJs;
    }

    /**
     * Metodo vincCombustivel, vincula os combustiveis do TCM com os do SW
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Classe de Negocio
     *
     * @return void
     */
    public function vincCombustivel($arParam)
    {
        $obErro = new Erro();

        $this->obModel->inCodTipo        = $arParam['inCodTipoCombustivelTCM'];
        $this->obModel->inCodCombustivel = $arParam['inCodCombustivelTCM'];
        //Adiciona os selecionados
        foreach ((array) $arParam['arCodCombustivelSWSelecionado'] as $stKey => $inCodItem) {
            $this->obModel->inCodItem    = $inCodItem;
            //Verifica se nao existe na base
            $this->obModel->listCombustivelVinculado($rsCombustivel);
            if ($rsCombustivel->getNumLinhas() <= 0) {
                $obErro = $this->obModel->vincularCombustivel(false,true);
            }
        }
        if (!$obErro->ocorreu()) {
            //Remove os nao selecionados
            foreach ((array) $arParam['arCodCombustivelSWDisponivel'] as $stKey => $inCodItem) {
                $this->obModel->inCodItem    = $inCodItem;
                $obErro = $this->obModel->desvincularCombustivel(false,true);
            }
        }

        if (!$obErro->ocorreu()) {
            SistemaLegado::alertaAviso('FMManterCombustivel.php' . '?' . Sessao::getId() . '&stAcao='.$arParam['stAcao'], 'Combustível vinculado com sucesso',$arParam['stAcao'],'aviso', Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso($obErro->getDescricao(), 'n_incluir', 'erro');
        }

    }
}
