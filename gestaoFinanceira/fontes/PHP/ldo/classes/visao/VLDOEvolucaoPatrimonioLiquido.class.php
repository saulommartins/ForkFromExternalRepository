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
 * Classe Visão da Evolucao do patrimonio liquido
 *
 * @author Henrique Boaventura <henrique.boaventura@cnm.org.br>
 *
 */

include_once CAM_GF_PPA_MAPEAMENTO . 'TPPA.class.php';
include_once CAM_GF_LDO_MAPEAMENTO . 'TLDO.class.php';

class VLDOEvolucaoPatrimonioLiquido
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
    public function __construct(RLDOEvolucaoPatrimonioLiquido $obModel)
    {
        $this->obModel= $obModel;
    }

    /**
     * Metodo inclui a validacao da acao
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param object $arParam     array
     * @param object $boTransacao Transacao
     *
     * @return void
     */
    public function incluir(array $arParam, $boTransacao = '')
    {
        $obErro = new Erro();

        //recupera o exercicio inicial do ppa
        $obTPPA = new TPPA;
        $obTPPA->recuperaTodos($rsPPA,' WHERE cod_ppa = ' . $arParam['inCodPPA']);
        $inAno = $arParam['inAno'] - $rsPPA->getCampo('ano_inicio') + 1;

        $this->obModel->obRLDOLDO->obRPPAManterPPA->inCodPPA = $arParam['inCodPPA'];
        $this->obModel->obRLDOLDO->inAno                     = $inAno;

        //verifica se o ldo ja esta cadastrado
        $this->obModel->obRLDOLDO->listar($rsLDO);
        if ($rsLDO->getNumLinhas() <= 0) {
            $this->obModel->obRLDOLDO->incluir($boTransacao);
        }

        foreach ($arParam as $stKey => $stValue) {
            if (strpos($stKey,'flValorAno') !== false) {
                $arInfo = explode('_',$stKey);
                if ($arInfo[3] == '1' AND $arInfo[4] == 0) {
                    //flPorcAno2_[cod_tipo]_[rpps]_[nivel]_[orcamento_2]
                    $this->obModel->inCodTipo                            = $arInfo[1];
                    $this->obModel->stExercicio                          = $arParam['inAno'] - 1 + substr($arInfo[0],-1);
                    $this->obModel->boRPPS                               = ($arInfo[2] == 0) ? 'false' : 'true';
                    $this->obModel->flValor                              = $stValue;

                    $this->obModel->verificaDado($boExiste);
                    if ($boExiste) {
                        $obErro = $this->obModel->alterar($boTransacao);
                    } else {
                        $obErro = $this->obModel->incluir($boTransacao);
                    }
                }
            }
        }

        if (!$obErro->ocorreu()) {
            SistemaLegado::alertaAviso('FLEvolucaoPatrimonioLiquido.php' . '?' . Sessao::getId() . '&stAcao='.$arParam['stAcao'], 'Ano ' . $arParam['inAno'], 'incluir','aviso', Sessao::getId(), "../");
        } else {
            return sistemaLegado::exibeAviso($obErro->getDescricao(), 'n_incluir', 'erro');
        }
    }

    /**
     * Metodo que busca os valores para as entidades nao rpps
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam
     *
     * @return void
     */
    public function listEntidadeNaoRPPS(&$rsNaoRPPS, array $arParam)
    {
        $this->obModel->obRLDOLDO->obRPPAManterPPA->inCodPPA = $arParam['inCodPPA'];
        $this->obModel->obRLDOLDO->inAno                     = $arParam['slExercicioLDO'];
        $this->obModel->boRPPS                               = false;

        $obErro = $this->obModel->listValores($rsNaoRPPS);

        return $obErro;
    }

    /**
     * Metodo que busca os valores para as entidades rpps
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam
     *
     * @return void
     */
    public function listEntidadeRPPS(&$rsRPPS, array $arParam)
    {
        $this->obModel->obRLDOLDO->obRPPAManterPPA->inCodPPA = $arParam['inCodPPA'];
        $this->obModel->obRLDOLDO->inAno                     = $arParam['slExercicioLDO'];
        $this->obModel->boRPPS                               = true;

        $obErro = $this->obModel->listValores($rsRPPS);

        return $obErro;
    }

    /**
     * Metodo que preenche os dados do combo da LDO
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam
     *
     * @return void
     */
    public function preencheLDO(array $arParam)
    {
        $stJs  = "jq('#slExercicioLDO').removeOption(/./);";
        $stJs .= "var arOptions = {";
        if ($arParam['inCodPPA'] != '') {
            $obTLDO = new TLDO;
            $obTLDO->setDado('cod_ppa',$arParam['inCodPPA']);
            $obTLDO->recuperaExerciciosLDO($rsLDO, ' ORDER BY ano_ldo ');
            while (!$rsLDO->eof()) {
                $stJs .= "'" . $rsLDO->getCampo('ano_ldo') . "' : '" . $rsLDO->getCampo('ano_ldo') . "',";

                $rsLDO->proximo();
            }
        }
        $stJs .= "};";
        $stJs .= "jq('#slExercicioLDO').addOption(arOptions,false);";

        return $stJs;
    }
}

?>
