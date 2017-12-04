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
    * Classe de regra de negócio UniOrcam
    * Data de Criação: 16/01/2014

    * @author Analista: Eduardo Schitz
    * @author Desenvolvedor: Franver Sarmento de Moraes

    * @package URBEM
    * @subpackage Regra

    * $Id: RExportacaoTCEMGUniOrcam.class.php 60484 2014-10-23 18:43:36Z lisiane $
    * $Name: $
    * $Revision: 60484 $
    * $Author: lisiane $
    * $Date: 2014-10-23 16:43:36 -0200 (Thu, 23 Oct 2014) $

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GF_EXP_MAPEAMENTO."TExportacaoTCEMGUniOrcam.class.php"     );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoUnidadeOrcamentaria.class.php"  	);

class RExportacaoTCEMGUniOrcam extends ROrcamentoUnidadeOrcamentaria
{
    public $obTExportacaoTCEMGUniOrcam;
    public $inIdentificador;
    public $inUnidadeAtual;
    public $inOrgaoAtual;
    public $stExercicioAtual;

    //SETTERS
    public function setTExportacaoTCEMGUniOrcam($valor)
    {
        $this->obTExportacaoTCEMGUniOrcam = $valor;
    }
    public function setIdentificador($valor)
    {
        $this->inIdentificador = $valor;
    }
    public function setOrdenador($valor)
    {
        $this->inOrdenador = $valor;
    }
    public function setUnidadeAtual($valor)
    {
        $this->inUnidadeAtual = $valor;
    }
    public function setOrgaoAtual($valor)
    {
        $this->inOrgaoAtual = $valor;
    }
    public function setExercicioAtual($valor)
    {
        $this->stExercicioAtual = $valor;
    }
    //GETTERS
    public function getTExportacaoTCEMGUniOrcam()
    {
        return $this->obTExportacaoTCEMGUniOrcam;
    }
    public function getIdentificador()
    {
        return $this->inIdentificador;
    }
    public function getOrdenador()
    {
        return $this->inOrdenador;
    }
    public function getUnidadeAtual()
    {
        return $this->inUnidadeAtual;
    }
    public function getOrgaoAtual()
    {
        return $this->inOrgaoAtual;
    }
    public function getExercicioAtual()
    {
        return $this->stExercicioAtual;
    }
    //METODO CONSTRUTOR
    /**
     * Método construtor
     * @access Private
     */
    public function RExportacaoTCEMGUniOrcam()
    {
        parent::ROrcamentoUnidadeOrcamentaria();
        $this->setTExportacaoTCEMGUniOrcam( new TExportacaoTCEMGUniOrcam() );
    }

    public function salvar($obTransacao = "")
    {
        $obErro = new Erro();

        //verifica se o cgm vem vazio e seta valor NULL para inclusao
        $inNumCGM = $this->getOrdenador() == "" ? "NULL" : $this->getOrdenador();
        $inCodOrgaoAtual = $this->getOrgaoAtual() == "" ? "NULL" : $this->getOrgaoAtual();
        $inCodUnidadeAtual = $this->getUnidadeAtual() == "" ? "NULL" : $this->getUnidadeAtual();
        $stExercicioAtual = $this->getExercicioAtual();
        
        $this->obTExportacaoTCEMGUniOrcam->setDado( "num_orgao", $this->obROrcamentoOrgaoOrcamentario->getNumeroOrgao() );
        $this->obTExportacaoTCEMGUniOrcam->setDado( "num_unidade", $this->getNumeroUnidade() );
        $this->obTExportacaoTCEMGUniOrcam->setDado( "identificador", $this->getIdentificador() );
        $this->obTExportacaoTCEMGUniOrcam->setDado( "exercicio", $this->getExercicio() );
        $this->obTExportacaoTCEMGUniOrcam->setDado( "cgm_ordenador", $inNumCGM );
        $this->obTExportacaoTCEMGUniOrcam->setDado( "num_orgao_atual", $inCodOrgaoAtual );
        $this->obTExportacaoTCEMGUniOrcam->setDado( "num_unidade_atual", $inCodUnidadeAtual );
        $this->obTExportacaoTCEMGUniOrcam->setDado( "exercicio_atual", $stExercicioAtual );
        
        $this->obTExportacaoTCEMGUniOrcam->recuperaPorChave($rsUniOrcam, $boTransacao);
        
        if ( $rsUniOrcam->eof() ) {
            $obErro = $this->obTExportacaoTCEMGUniOrcam->inclusao( $boTransacao );
        } else {
            $obErro = $this->obTExportacaoTCEMGUniOrcam->alteracao( $boTransacao );
        }

        return $obErro;
    }

    public function listar(&$rsUnidadeOrcamento, $stOrder = "", $boTransacao = "")
    {
        $this->obTExportacaoTCEMGUniOrcam->setDado( 'exercicio',$this->getExercicio() );
        $stOrder = "num_orgao,num_unidade";
        $obErro = $this->obTExportacaoTCEMGUniOrcam->recuperaDadosUniOrcam( $rsUnidadeOrcamento, $stFiltro, $stOrder, $boTransacao );

        return $obErro;
    }

    public function listarDadosConversao(&$rsUnidadeOrcamento, $boTransacao = "")
    {
        $stOrder = "exercicio,num_orgao,num_unidade";
        $this->obTExportacaoTCEMGUniOrcam->setDado( 'exercicio',$this->getExercicio() );
        $obErro = $this->obTExportacaoTCEMGUniOrcam->recuperaDadosUniOrcamConversao( $rsUnidadeOrcamento, $stFiltro, $stOrder, $boTransacao );

        return $obErro;
    }
}
?>
