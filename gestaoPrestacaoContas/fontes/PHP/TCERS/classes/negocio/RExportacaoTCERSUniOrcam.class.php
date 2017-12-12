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
 * Data de Criação: 10/02/2005

 * @author Analista: Diego B. ictoria
 * @author Desenvolvedor: Diego Lemos de Souza
 * @package URBEM
 * @subpackage Regra

 * Casos de uso: uc-02.08.05

 $Id: RExportacaoTCERSUniOrcam.class.php 59612 2014-09-02 12:00:51Z gelson $
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GPC_TCERS_MAPEAMENTO."TExportacaoTCERSUniOrcam.class.php";
include_once CAM_GA_CGM_NEGOCIO."RCGMPessoaJuridica.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoUnidadeOrcamentaria.class.php";

class RExportacaoTCERSUniOrcam extends ROrcamentoUnidadeOrcamentaria
{
    public $obRCGMPessoaJuridica;
    public $obTExportacaoTCERSUniOrcam;
    public $inIdentificador;

    //SETTERS
    public function setRCGMPessoaJuridica($valor) { $this->obRCGMPessoaJuridica = $valor;  }
    public function setTExportacaoTCERSUniOrcam($valor) { $this->obTExportacaoTCERSUniOrcam     = $valor;  }
    public function setIdentificador($valor) { $this->inIdentificador      = $valor;  }

    //GETTERS
    public function getRCGMPessoaJuridica() { return $this->obRCGMPessoaJuridica; }
    public function getTExportacaoTCERSUniOrcam() { return $this->obTExportacaoTCERSUniOrcam;     }
    public function getIdentificador() { return $this->inIdentificador;      }

    //METODO CONSTRUTOR
    /**
         * Método construtor
         * @access Private
    */
    public function RExportacaoTCERSUniOrcam()
    {
        parent::ROrcamentoUnidadeOrcamentaria();
        $this->setRCGMPessoaJuridica( new RCGMPessoaJuridica() );
        $this->setTExportacaoTCERSUniOrcam( new TExportacaoTCERSUniOrcam() );
    }

    public function salvar($obTransacao = "")
    {
        $obErro = new Erro();
        if ( $this->getIdentificador() != "" AND  $this->obRCGMPessoaJuridica->getNumCGM() != "" ) {
            $this->obTExportacaoTCERSUniOrcam->setDado( "num_orgao", $this->obROrcamentoOrgaoOrcamentario->getNumeroOrgao() );
            $this->obTExportacaoTCERSUniOrcam->setDado( "num_unidade", $this->getNumeroUnidade() );
            $this->obTExportacaoTCERSUniOrcam->setDado( "identificador", $this->getIdentificador() );
            $this->obTExportacaoTCERSUniOrcam->setDado( "numcgm", $this->obRCGMPessoaJuridica->getNumCGM() );
            $this->obTExportacaoTCERSUniOrcam->setDado( "exercicio", $this->getExercicio() );
            $this->obTExportacaoTCERSUniOrcam->recuperaPorChave($rsUniOrcam, $boTransacao);
            if ( $rsUniOrcam->eof() ) {
                $obErro = $this->obTExportacaoTCERSUniOrcam->inclusao( $boTransacao );
            } else {
                $obErro = $this->obTExportacaoTCERSUniOrcam->alteracao( $boTransacao );
            }
        } elseif ( ($this->getIdentificador() != "" AND  $this->obRCGMPessoaJuridica->getNumCGM() == "") OR ($this->getIdentificador() == "" AND  $this->obRCGMPessoaJuridica->getNumCGM() != "") ) {
            $obErro->setDescricao("Para o orgão (".$this->obROrcamentoOrgaoOrcamentario->getNumeroOrgao().") e unidade (".$this->getNumeroUnidade()."), não foi informado o identificador ou cgm");
        }

        return $obErro;
    }

    public function listar(&$rsUnidadeOrcamento,  $stOrder = "", $boTransacao = "")
    {
        $this->obTExportacaoTCERSUniOrcam->setDado( 'exercicio',$this->getExercicio() );
        $stOrder = "num_orgao,num_unidade";
        $obErro = $this->obTExportacaoTCERSUniOrcam->recuperaDadosUniOrcam( $rsUnidadeOrcamento, $stFiltro, $stOrder, $boTransacao );

        return $obErro;
    }

    public function listarDadosConversao(&$rsUnidadeOrcamento, $boTransacao = "")
    {
        $stOrder = "exercicio,num_orgao,num_unidade";
        $this->obTExportacaoTCERSUniOrcam->setDado( 'exercicio',$this->getExercicio() );
        $obErro = $this->obTExportacaoTCERSUniOrcam->recuperaDadosUniOrcamConversao( $rsUnidadeOrcamento, $stFiltro, $stOrder, $boTransacao );

        return $obErro;
    }

}

?>
