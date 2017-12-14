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
    * Classe de Regra de Negócio Itens
    * Data de Criação   : 29/10/2008

    * @author Analista: Heleno Santos
    * @author Desenvolvedor: Aldo Jean

    * @package URBEM
    * @subpackage Regra

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_GF_PPA_NEGOCIO."RPPAOrcamentoOrgaoOrcamentario.class.php");
include_once(CAM_GA_ADM_NEGOCIO."RUnidade.class.php");
include_once(CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php");
include_once(CAM_GF_ORC_MAPEAMENTO."TOrcamentoUnidade.class.php");

class ROrcamentoUnidadeOrcamentaria
{

    public $obROrcamentoOrgaoOrcamentario;
    public $obRUnidade;
    public $obTransacao;
    public $stExercicio;
    public $inNumeroUnidade;
    public $stMascara;
    public $obRConfiguracaoOrcamento;

    public function setRConfiguracaoOrcamento($valor) { $this->obRConfiguracaoOrcamento           = $valor; }
    public function setRUnidade($valor) {               $this->obRUnidade                         = $valor; }
    public function setROrcamentoOrgaoOrcamentario($valor) { $this->obROrcamentoOrgaoOrcamentario = $valor; }
    public function setExercicio($valor) {                   $this->stExercicio                   = $valor; }
    public function setNumeroUnidade($valor) {               $this->inNumeroUnidade               = $valor; }
    public function setMascara($valor) {                     $this->stMascara                     = $valor; }
    public function getConfiguracaoOrcamento() { return      $this->obRConfiguracaoOrcamento;               }
    public function getROrcamentoOrgaoOrcamentario() { return $this->obROrcamentoOrgaoOrcamentario;         }
    public function getRUnidade() { return                   $this->obRUnidade;                             }
    public function getExercicio() { return                  $this->stExercicio;                            }
    public function getNumeroUnidade() { return              $this->inNumeroUnidade;                        }
    public function getMascara() { return                    $this->stMascara;                              }

    public function ROrcamentoUnidadeOrcamentaria()
    {
        $this->setRConfiguracaoOrcamento(     new ROrcamentoConfiguracao     );
        $this->setROrcamentoOrgaoOrcamentario(new ROrcamentoOrgaoOrcamentario);
        $this->setRUnidade(                   new RUnidade                   );
        $this->setExercicio(                  Sessao::getExercicio()         );
    }

    public function listar(&$rsLista, $stOrder = "", $obTransacao = "")
    {
        $obTOrcamentoUnidade = new TOrcamentoUnidade;
        $this->pegarMascara($obTOrcamentoUnidade);
        $stFiltro = "";

        if ($this->getNumeroUnidade()) {
            $stFiltro .= " num_unidade = " . $this->getNumeroUnidade() . "\n AND ";
        }
        if ($this->getExercicio()) {
            $stFiltro .= " orgao.exercicio = '" . $this->getExercicio() . "' \n AND ";
        }
        if ($this->obROrcamentoOrgaoOrcamentario->obROrgao->getExercicio()) {
            $stFiltro .= " orgao.ano_exercicio = '" . $this->obROrcamentoOrgaoOrcamentario->obROrgao->getExercicio() . "' \n AND ";
        }
        if ($this->obRUnidade->getNomUnidade()) {
            $stFiltro .= " lower(nom_unidade)  like lower('%" . $this->obRUnidade->getNomUnidade() . "%')"." \n AND ";
        }
        if ( $this->obROrcamentoOrgaoOrcamentario->getNumeroOrgao()) {
            $stFiltro .= " orgao.num_orgao = " . $this->obROrcamentoOrgaoOrcamentario->getNumeroOrgao()." \n AND ";
        }
        if ($stFiltro) {
            $stFiltro = " \n AND " . substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
        }

        $obErro = $obTOrcamentoUnidade->recuperaMascarado( $rsLista, $stFiltro, $stOrder, $obTransacao );

        return $obErro;
    }

    public function pegarMascara(&$obTOrcamentoUnidadeParametro)
    {
        $obErro = $this->obRConfiguracaoOrcamento->consultarConfiguracao();
        $stMascara = $this->obRConfiguracaoOrcamento->getMascDespesa();
        $arMarcara = preg_split( "/[^a-zA-Z0-9]/", $stMascara);

        // Grupo U;
        $stMascara = $arMarcara[1];

        $this->setMascara( $stMascara );
        $obTOrcamentoUnidadeParametro->setDado( "stMascara"  , $this->getMascara() );

        $obTOrcamentoUnidadeParametro->setDado( "stMascaraOrgao" , $arMascara[0] );
        $obTOrcamentoUnidadeParametro->setDado( "stMascaraUnidade" , $arMascara[1] );

        return $obErro;
    }

}
?>
