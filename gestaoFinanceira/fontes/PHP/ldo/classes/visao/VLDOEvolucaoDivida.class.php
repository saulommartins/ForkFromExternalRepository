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
    * Página da visão da evolução da dívida
    * Data de Criação   : 03/07/2009

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz

    * @package URBEM
    * @subpackage

    $Id:$
 */

include_once CAM_GF_PPA_MAPEAMENTO.'TPPA.class.php';
include_once CAM_GF_LDO_MAPEAMENTO.'TLDO.class.php';

class VLDOEvolucaoDivida
{
    public $obModel;

    /**
     * Metodo construtor, seta o atributo obModel com o que vier na assinatura da funcao
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     * @param object $obModel Classe de Negocio
     *
     * @return void
     */
    public function __construct(RLDOEvolucaoDivida $obModel)
    {
        $this->obModel= $obModel;
    }

    /**
     * Metodo inclui a validacao da acao
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Eduardo Schitz      <eduardo.schitz@cnm.org.br>
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

        $arExercicio[1] = $arParam['inAno'] - 3;
        $arExercicio[2] = $arParam['inAno'] - 2;
        $arExercicio[3] = $arParam['inAno'] - 1;
        $arExercicio[4] = $arParam['inAno'];
        $arExercicio[5] = $arParam['inAno'] + 1;
        $arExercicio[6] = $arParam['inAno'] + 2;
        foreach ($arParam as $stKey => $stValue) {
            if (strpos($stKey,'flValor') !== false) {
                $arInfo = explode('_',$stKey);
                if ($arInfo[2] == 0) {
                    $inExercicio = substr($arInfo[0],-1);
                    $this->obModel->inCodTipo   = $arInfo[1];
                    $this->obModel->stExercicio = $arExercicio[$inExercicio];
                    $this->obModel->flValor     = $stValue;

                    //verifica se o dado já existe na base
                    $obErro = $this->obModel->verificaDado($boExiste);
                    if ($boExiste) {
                        $this->obModel->alterar($boTransacao);
                    } else {
                        $this->obModel->incluir($boTransacao);
                    }
                }
            }
        }

        if (!$obErro->ocorreu()) {
            SistemaLegado::alertaAviso('FLEvolucaoDivida.php' . '?' . Sessao::getId() . '&stAcao='.$arParam['stAcao'], 'Ano ' . $arParam['inAno'], 'incluir','aviso', Sessao::getId(), "../");
        } else {
            return sistemaLegado::exibeAviso($obErro->getDescricao(), 'n_incluir', 'erro');
        }
    }

    /**
     * Metodo que monta a lista de dívidas do ldo
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     * @param object $rsAcao RecordSet
     *
     * @return object $obErro
     */
    public function listDividasLDO(&$rsDividas, $arParam)
    {

        $this->obModel->obRLDOLDO->obRPPAManterPPA->inCodPPA = $arParam['inCodPPA'];
        $this->obModel->obRLDOLDO->inAno                     = $arParam['slExercicioLDO'];

        $obErro = $this->obModel->listDividasLDO($rsDividas);

        return $obErro;
    }

    /**
     * Metodo que monta a lista servicos das dívidas do ldo
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     * @param object $rsAcao RecordSet
     *
     * @return object $obErro
     */
    public function listServicosLDO(&$rsDividas, $arParam)
    {

        $this->obModel->obRLDOLDO->obRPPAManterPPA->inCodPPA = $arParam['inCodPPA'];
        $this->obModel->obRLDOLDO->inAno                     = $arParam['slExercicioLDO'];
        $this->obModel->inCodSelic                           = $arParam['inCodSelic'];

        $obErro = $this->obModel->listServicosLDO($rsDividas);

        return $obErro;
    }

    /**
     * Metodo que preenche os dados do combo da LDO
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     * @param object $arParam array
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
