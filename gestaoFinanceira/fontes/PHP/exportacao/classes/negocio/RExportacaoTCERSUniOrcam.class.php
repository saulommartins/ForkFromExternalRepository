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

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.08.05
*/

/*
$Log$
Revision 1.8  2006/07/05 20:46:04  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GF_EXP_MAPEAMENTO."TExportacaoTCERSUniOrcam.class.php"     );
include_once ( CAM_GA_CGM_NEGOCIO."RCGMPessoaJuridica.class.php"  	);
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoUnidadeOrcamentaria.class.php"  	);

class RExportacaoTCERSUniOrcam extends ROrcamentoUnidadeOrcamentaria
{
var $obRCGMPessoaJuridica;
var $obTExportacaoTCERSUniOrcam;
var $inIdentificador;

//SETTERS
function setRCGMPessoaJuridica($valor) { $this->obRCGMPessoaJuridica = $valor;  }
function setTExportacaoTCERSUniOrcam($valor) { $this->obTExportacaoTCERSUniOrcam     = $valor;  }
function setIdentificador($valor) { $this->inIdentificador      = $valor;  }

//GETTERS
function getRCGMPessoaJuridica() { return $this->obRCGMPessoaJuridica; }
function getTExportacaoTCERSUniOrcam() { return $this->obTExportacaoTCERSUniOrcam;     }
function getIdentificador() { return $this->inIdentificador;      }

//METODO CONSTRUTOR
/**
     * Método construtor
     * @access Private
*/
function RExportacaoTCERSUniOrcam()
{
    parent::ROrcamentoUnidadeOrcamentaria();
    $this->setRCGMPessoaJuridica( new RCGMPessoaJuridica() );
    $this->setTExportacaoTCERSUniOrcam( new TExportacaoTCERSUniOrcam() );
}

function salvar($obTransacao = "")
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

function listar(&$rsUnidadeOrcamento, $stOrder = "", $boTransacao = "")
{
    $this->obTExportacaoTCERSUniOrcam->setDado( 'exercicio',$this->getExercicio() );
    $stOrder = "num_orgao,num_unidade";
    $obErro = $this->obTExportacaoTCERSUniOrcam->recuperaDadosUniOrcam( $rsUnidadeOrcamento, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

function listarDadosConversao(&$rsUnidadeOrcamento, $boTransacao = "")
{
    $stOrder = "exercicio,num_orgao,num_unidade";
    $this->obTExportacaoTCERSUniOrcam->setDado( 'exercicio',$this->getExercicio() );
    $obErro = $this->obTExportacaoTCERSUniOrcam->recuperaDadosUniOrcamConversao( $rsUnidadeOrcamento, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

}
?>
