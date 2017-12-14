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
    * Pacote de configuração do TCETO - Negócio Unidade Orçamentária
    * Data de Criação   : 05/11/2014

    * @author Analista: Silvia Martins Silva
    * @author Desenvolvedor: Michel Teixeira
    * $Id: RExportacaoTCETOUniOrcam.class.php 60654 2014-11-06 13:18:49Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GPC_TCETO_MAPEAMENTO.'TExportacaoTCETOUniOrcam.class.php';
include_once CAM_GA_CGM_NEGOCIO.'RCGMPessoaJuridica.class.php';
include_once CAM_GF_ORC_NEGOCIO.'ROrcamentoUnidadeOrcamentaria.class.php';

class RExportacaoTCETOUniOrcam extends ROrcamentoUnidadeOrcamentaria
{
    var $obRCGMPessoaJuridica;
    var $obTExportacaoTCETOUniOrcam;
    var $inIdentificador;
    
    //SETTERS
    function setRCGMPessoaJuridica($valor) { $this->obRCGMPessoaJuridica                = $valor;  }
    function setTExportacaoTCETOUniOrcam($valor) { $this->obTExportacaoTCETOUniOrcam    = $valor;  }
    function setIdentificador($valor) { $this->inIdentificador                          = $valor;  }
    
    //GETTERS
    function getRCGMPessoaJuridica() { return $this->obRCGMPessoaJuridica;              }
    function getTExportacaoTCETOUniOrcam() { return $this->obTExportacaoTCETOUniOrcam;  }
    function getIdentificador() { return $this->inIdentificador;                        }
    
    //METODO CONSTRUTOR
    /**
         * Método construtor
         * @access Private
    */
    function RExportacaoTCETOUniOrcam()
    {
        parent::ROrcamentoUnidadeOrcamentaria();
        $this->setRCGMPessoaJuridica( new RCGMPessoaJuridica() );
        $this->setTExportacaoTCETOUniOrcam( new TExportacaoTCETOUniOrcam() );
    }
    
    function salvar($obTransacao = "")
    {
        $obErro = new Erro();
        if ( $this->getIdentificador() != "" AND  $this->obRCGMPessoaJuridica->getNumCGM() != "" ) {
            $this->obTExportacaoTCETOUniOrcam->setDado( "num_orgao"     , $this->obROrcamentoOrgaoOrcamentario->getNumeroOrgao());
            $this->obTExportacaoTCETOUniOrcam->setDado( "num_unidade"   , $this->getNumeroUnidade()                             );
            $this->obTExportacaoTCETOUniOrcam->setDado( "identificador" , $this->getIdentificador()                             );
            $this->obTExportacaoTCETOUniOrcam->setDado( "numcgm"        , $this->obRCGMPessoaJuridica->getNumCGM()              );
            $this->obTExportacaoTCETOUniOrcam->setDado( "exercicio"     , $this->getExercicio()                                 );
            $this->obTExportacaoTCETOUniOrcam->recuperaPorChave($rsUniOrcam, $boTransacao);
            if ( $rsUniOrcam->getNumLinhas() < 0 )
                $obErro = $this->obTExportacaoTCETOUniOrcam->inclusao( $boTransacao );
            else
                $obErro = $this->obTExportacaoTCETOUniOrcam->alteracao( $boTransacao );
        } elseif ( ($this->getIdentificador() != "" AND  $this->obRCGMPessoaJuridica->getNumCGM() == "") OR ($this->getIdentificador() == "" AND  $this->obRCGMPessoaJuridica->getNumCGM() != "") ) {
            $obErro->setDescricao("Para o orgão (".$this->obROrcamentoOrgaoOrcamentario->getNumeroOrgao().") e unidade (".$this->getNumeroUnidade().") de ".$this->getExercicio().", não foi informado o identificador ou cgm");
        }
    
        return $obErro;
    }
    
    function listar(&$rsUnidadeOrcamento, $boTransacao = "")
    {
        $this->obTExportacaoTCETOUniOrcam->setDado( 'exercicio',$this->getExercicio() );
        $stOrder = "num_orgao,num_unidade";
        $obErro = $this->obTExportacaoTCETOUniOrcam->recuperaDadosUniOrcam( $rsUnidadeOrcamento, $stFiltro, $stOrder, $boTransacao );
    
        return $obErro;
    }
    
    function listarDadosConversao(&$rsUnidadeOrcamento, $boTransacao = "")
    {
        $stOrder = "exercicio,num_orgao,num_unidade";
        $this->obTExportacaoTCETOUniOrcam->setDado( 'exercicio',$this->getExercicio() );
        $obErro = $this->obTExportacaoTCETOUniOrcam->recuperaDadosUniOrcamConversao( $rsUnidadeOrcamento, $stFiltro, $stOrder, $boTransacao );
    
        return $obErro;
    }

}
?>
