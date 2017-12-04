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
    * Data de Criação: 22/07/2014

    * @author Analista: Eduardo Schitz
    * @author Desenvolvedor: Lisiane Morais

    * @package URBEM
    * @subpackage Regra

    * $Id: RExportacaoTCEPBUniOrcam.class.php 59612 2014-09-02 12:00:51Z gelson $
    * $Name: $
    * $Revision: $
    * $Author:$
    * $Date:$

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GF_EXP_MAPEAMENTO."TExportacaoTCEPBUniOrcam.class.php"     );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoUnidadeOrcamentaria.class.php"  	);

class RExportacaoTCEPBUniOrcam extends ROrcamentoUnidadeOrcamentaria
{
    public $obTExportacaoTCEPBUniOrcam;

    //SETTERS
    public function setTExportacaoTCEPBUniOrcam($valor)
    {
        $this->obTExportacaoTCEPBUniOrcam = $valor;
    }
    public function setOrdenador($valor)
    {
        $this->inOrdenador = $valor;
    }
    public function setNaturezaJuridica($valor)
    {
        $this->inNaturezaJuridica = $valor;
    }

    //GETTERS
    public function getTExportacaoTCEPBUniOrcam()
    {
        return $this->obTExportacaoTCEPBUniOrcam;
    }
    public function getOrdenador()
    {
        return $this->inOrdenador;
    }
    public function getNaturezaJuridica()
    {
        return $this->inNaturezaJuridica;
    }

    //METODO CONSTRUTOR
    /**
     * Método construtor
     * @access Private
     */
    public function RExportacaoTCEPBUniOrcam()
    {
        parent::ROrcamentoUnidadeOrcamentaria();
        $this->setTExportacaoTCEPBUniOrcam( new TExportacaoTCEPBUniOrcam() );
    }

    public function salvar($obTransacao = "")
    {
        $obErro = new Erro();

        //verifica se o cgm vem vazio e seta valor NULL para inclusao
        $inNumCGM = $this->getOrdenador() == "" ? "NULL" : $this->getOrdenador();
 
        $this->obTExportacaoTCEPBUniOrcam->setDado( "num_orgao", $this->obROrcamentoOrgaoOrcamentario->getNumeroOrgao() );
        $this->obTExportacaoTCEPBUniOrcam->setDado( "num_unidade", $this->getNumeroUnidade() );
        $this->obTExportacaoTCEPBUniOrcam->setDado( "exercicio", $this->getExercicio() );
        $this->obTExportacaoTCEPBUniOrcam->setDado( "cgm_ordenador", $inNumCGM );
        $this->obTExportacaoTCEPBUniOrcam->setDado( "natureza_juridica", $this->getNaturezaJuridica() );
        $this->obTExportacaoTCEPBUniOrcam->recuperaPorChave($rsUniOrcam, $boTransacao);
        
        if ( $rsUniOrcam->eof() ) {
            $obErro = $this->obTExportacaoTCEPBUniOrcam->inclusao( $boTransacao );
        } else {
            $obErro = $this->obTExportacaoTCEPBUniOrcam->alteracao( $boTransacao );
        }

        return $obErro;
    }

    public function listar(&$rsUnidadeOrcamento, $stOrder = "", $boTransacao = "")
    {
        $this->obTExportacaoTCEPBUniOrcam->setDado( 'exercicio',$this->getExercicio() );
        $stOrder = "num_orgao,num_unidade";
        $obErro = $this->obTExportacaoTCEPBUniOrcam->recuperaDadosUniOrcam( $rsUnidadeOrcamento, $stFiltro, $stOrder, $boTransacao );

        return $obErro;
    }

   
}
?>
