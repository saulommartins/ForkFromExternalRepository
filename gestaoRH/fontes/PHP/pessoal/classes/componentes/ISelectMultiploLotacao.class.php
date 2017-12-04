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
* Gerar o componente o SelectMultiplo com a Lotação
* Data de Criação: 09/11/2005

* @author Analista: Vandre Miguel Ramos
* @author Desenvolvedor: Andre Almeida

* @package beneficios
* @subpackage componentes

Casos de uso: uc-00.00.00

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/GA.inc.php';
include_once ( CAM_GA_ORGAN_MAPEAMENTO."TOrganogramaOrgao.class.php" );
include_once ( CAM_GRH_PES_MAPEAMENTO."FPessoalOrganogramaVigentePorTimestamp.class.php" );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php" );

/**
    * Cria o componente SelectMultiplo com a Lotação
    * @author Desenvolvedor: Andre Almeida

    * @package beneficios
    * @subpackage componentes
*/
class ISelectMultiploLotacao extends SelectMultiplo
{
/**
    * @access Private
    * @var Object
*/
var $obTOrganogramaOrgao;

var $rsDisponiveis;
var $rsSelecionados;

function setDisponiveis($value) { $this->rsDisponiveis = $value; }
function getDisponiveis()       { return $this->rsDisponiveis; }

function setSelecionados($value) { $this->rsSelecionados = $value; }
function getSelecionados()       { return $this->rsSelecionados; }

/**
    * @access Public
    * @Param Object $valor
*/
function setTOrganogramaOrgao($valor) { $this->obTOrganogramaOrgao = $valor; }

/**
    * @access Public
    * @return Object
*/
function getTOrganogramaOrgao() { return $this->obTOrganogramaOrgao; }

/**
    * Método Construtor
    * @access Public
*/
function ISelectMultiploLotacao()
{
    parent::SelectMultiplo();

    $this->setTOrganogramaOrgao ( new TOrganogramaOrgao );

    $rsSelecionados = $rsDisponiveis = new Recordset;
    $this->setName       ( "inCodLotacao"                      );
    $this->setRotulo     ( "Lotação"                           );
    $this->setTitle      ( "Selecione a lotação para o filtro" );
    $this->setNomeLista1 ( "inCodLotacaoDisponiveis"           );
    $this->setRecord1    ( $rsDisponiveis                      );
    $this->setCampoId1   ( "[cod_orgao]"                       );
    $this->setCampoDesc1 ( "[cod_estrutural] - [descricao]"    );
    $this->setStyle1     ( "width:300px"                       );
    $this->setNomeLista2 ( "inCodLotacaoSelecionados"          );
    $this->setRecord2    ( $rsSelecionados                     );
    $this->setCampoId2   ( "[cod_orgao]"                       );
    $this->setCampoDesc2 ( "[cod_estrutural] - [descricao]"    );
    $this->setStyle2     ( "width:300px"                       );

}

function atualizarLotacao($stDataFinal, $inCodOrganograma)
{
    if (trim($stDataFinal)=="") {
        $stDataFinal = "1900-01-01";
    }

    $stFiltroOrganograma = " AND orgao_nivel.cod_organograma = ".$inCodOrganograma;
    $this->obTOrganogramaOrgao->setDado("vigencia", $stDataFinal);
    $this->obTOrganogramaOrgao->recuperaOrgaos($rsDisponiveis, $stFiltroOrganograma, " ORDER BY cod_estrutural");

    $stJs  = "jQuery('#".$this->getNomeLista1()." option').each(function () {jQuery(this).remove();});";
    $stJs .= "jQuery('#".$this->getNomeLista2()." option').each(function () {jQuery(this).remove();});";

    while (!$rsDisponiveis->eof()) {
        $stJs .= "	jQuery('#".$this->getNomeLista2()."').addOption('".$rsDisponiveis->getCampo("cod_orgao")."','".$rsDisponiveis->getCampo("cod_estrutural")." - ". $rsDisponiveis->getCampo( "descricao"). "');";
        $rsDisponiveis->proximo();
    }

    return $stJs;
}

    /**
     * Atualiza o select lotação selecionados, de acordo com um array de filtro.
     * Esta função mantém no select de selecionados apenas as lotações cujo codigo estão presentes em $arFiltroLotacao.
     * Assume que o select de disponiveis ja esta preenchido
     * @param  string $stDataFinal
     * @param  type   $inCodOrganograma
     * @param  type   $arFiltroLotacao
     * @return string
     */
    public function atualizarLotacaoFiltro($stDataFinal, $inCodOrganograma, $arFiltroLotacao)
    {
        if (trim($stDataFinal)=="") {
            $stDataFinal = "1900-01-01";
        }

        $stFiltroOrganograma = " AND orgao_nivel.cod_organograma = ".$inCodOrganograma;
        $this->obTOrganogramaOrgao->setDado("vigencia", $stDataFinal);
        $this->obTOrganogramaOrgao->recuperaOrgaos($rsDisponiveis, $stFiltroOrganograma, " ORDER BY cod_estrutural");

        $stJs .= "jQuery('#".$this->getNomeLista2()." option').each(function () {jQuery(this).remove();});";

        while (!$rsDisponiveis->eof()) {
            if (is_array($arFiltroLotacao) and in_array($rsDisponiveis->getCampo("cod_orgao"), $arFiltroLotacao)) {
                $stJs .= "	jQuery('#".$this->getNomeLista2()."').addOption('".$rsDisponiveis->getCampo("cod_orgao")."','".$rsDisponiveis->getCampo("cod_estrutural")." - ". $rsDisponiveis->getCampo( "descricao"). "', false);";
                $stJs .= "	jQuery('#".$this->getNomeLista1()."').removeOption('".$rsDisponiveis->getCampo("cod_orgao")."');";
            }
            $rsDisponiveis->proximo();
        }

        return $stJs;
    }

    public function montaHtml()
    {
        # Alterado o componente para retirar dos disponíveis as lotações já selecionadas.
        if ($this->getDisponiveis()) {
            $this->setRecord1($this->getDisponiveis());
        } else {
            $obFPessoalOrganogramaVigentePorTimestamp = new FPessoalOrganogramaVigentePorTimestamp();
            $obFPessoalOrganogramaVigentePorTimestamp->recuperaOrganogramaVigentePorTimestamp($rsOrganogramaVigente);

            $stFiltroOrganograma = " AND orgao_nivel.cod_organograma = ".$rsOrganogramaVigente->getCampo('cod_organograma');

            $this->obTOrganogramaOrgao->setDado( 'vigencia', $rsOrganogramaVigente->getCampo('dt_final') );
            $this->obTOrganogramaOrgao->recuperaOrgaos( $rsDisponiveis, $stFiltroOrganograma, " ORDER BY cod_estrutural " );
            $this->setRecord1( $rsDisponiveis );
        }

        # Alterado o componente para apresentar as lotações já selecionadas.
        if ($this->getSelecionados()) {
            $this->setRecord2($this->getSelecionados());
        }

        /***************************************************************************
        *  Carrega o array de refêrencia para o componente de competência atualizar
        *  as devidas lotações de acordo com o periodo de movimentação selecionado
        * **************************************************************************/
        $arSelectMultiploLotacao = Sessao::read("arSelectMultiploLotacao");
        if (!is_array($arSelectMultiploLotacao)) {
            $arSelectMultiploLotacao = array();
        }
        array_push($arSelectMultiploLotacao,$this);
        Sessao::write("arSelectMultiploLotacao", $arSelectMultiploLotacao);

        parent::montaHtml();
    }

}

?>
