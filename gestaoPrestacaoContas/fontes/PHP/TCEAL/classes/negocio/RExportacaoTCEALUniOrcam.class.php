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
    * Pacote de configuração do TCEAL
    * Data de Criação   : 08/10/2013

    * @author Analista: Carlos Adriano
    * @author Desenvolvedor: Carlos Adriano
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GPC_TCEAL_MAPEAMENTO."TExportacaoTCEALUniOrcam.class.php"     );
include_once ( CAM_GA_CGM_NEGOCIO."RCGMPessoaJuridica.class.php"  	);
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoUnidadeOrcamentaria.class.php"  	);

class RExportacaoTCEALUniOrcam extends ROrcamentoUnidadeOrcamentaria
{
var $obRCGMPessoaJuridica;
var $obTExportacaoTCEALUniOrcam;
var $inIdentificador;

//SETTERS
function setRCGMPessoaJuridica($valor) { $this->obRCGMPessoaJuridica = $valor;  }
function setTExportacaoTCEALUniOrcam($valor) { $this->obTExportacaoTCEALUniOrcam     = $valor;  }
function setIdentificador($valor) { $this->inIdentificador      = $valor;  }

//GETTERS
function getRCGMPessoaJuridica() { return $this->obRCGMPessoaJuridica; }
function getTExportacaoTCEALUniOrcam() { return $this->obTExportacaoTCEALUniOrcam;     }
function getIdentificador() { return $this->inIdentificador;      }

//METODO CONSTRUTOR
/**
     * Método construtor
     * @access Private
*/
function RExportacaoTCEALUniOrcam()
{
    parent::ROrcamentoUnidadeOrcamentaria();
    $this->setRCGMPessoaJuridica( new RCGMPessoaJuridica() );
    $this->setTExportacaoTCEALUniOrcam( new TExportacaoTCEALUniOrcam() );
}

function salvar($obTransacao = "")
{
    $obErro = new Erro();
    if ( $this->getIdentificador() != "" AND  $this->obRCGMPessoaJuridica->getNumCGM() != "" ) {
        $this->obTExportacaoTCEALUniOrcam->setDado( "num_orgao", $this->obROrcamentoOrgaoOrcamentario->getNumeroOrgao() );
        $this->obTExportacaoTCEALUniOrcam->setDado( "num_unidade", $this->getNumeroUnidade() );
        $this->obTExportacaoTCEALUniOrcam->setDado( "identificador", $this->getIdentificador() );
        $this->obTExportacaoTCEALUniOrcam->setDado( "numcgm", $this->obRCGMPessoaJuridica->getNumCGM() );
        $this->obTExportacaoTCEALUniOrcam->setDado( "exercicio", $this->getExercicio() );
        $this->obTExportacaoTCEALUniOrcam->recuperaPorChave($rsUniOrcam, $boTransacao);
        if ( $rsUniOrcam->getNumLinhas() < 0 ) { 
            $obErro = $this->obTExportacaoTCEALUniOrcam->inclusao( $boTransacao );
        } else {
            $obErro = $this->obTExportacaoTCEALUniOrcam->alteracao( $boTransacao );
        }
    } elseif ( ($this->getIdentificador() != "" AND  $this->obRCGMPessoaJuridica->getNumCGM() == "") OR ($this->getIdentificador() == "" AND  $this->obRCGMPessoaJuridica->getNumCGM() != "") ) {
        $obErro->setDescricao("Para o orgão (".$this->obROrcamentoOrgaoOrcamentario->getNumeroOrgao().") e unidade (".$this->getNumeroUnidade()."), não foi informado o identificador ou cgm");
    }

    return $obErro;
}

function listar(&$rsUnidadeOrcamento, $stOrder = "", $boTransacao = "")
{
    $this->obTExportacaoTCEALUniOrcam->setDado( 'exercicio',$this->getExercicio() );
    $stOrder = "num_orgao,num_unidade";
    $obErro = $this->obTExportacaoTCEALUniOrcam->recuperaDadosUniOrcam( $rsUnidadeOrcamento, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

function listarDadosConversao(&$rsUnidadeOrcamento, $boTransacao = "")
{
    $stOrder = "exercicio,num_orgao,num_unidade";
    $this->obTExportacaoTCEALUniOrcam->setDado( 'exercicio',$this->getExercicio() );
    $obErro = $this->obTExportacaoTCEALUniOrcam->recuperaDadosUniOrcamConversao( $rsUnidadeOrcamento, $stFiltro, $stOrder, $boTransacao );
    return $obErro;
}

}
?>
