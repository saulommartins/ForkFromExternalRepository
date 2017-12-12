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
    * Classe de Regra para emissão do Plano de Contas
    * Data de Criação   : 25/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson Buzo
    * @author Desenvolvedor: Gelson W. Gonçalves

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2006-08-14 15:57:16 -0300 (Seg, 14 Ago 2006) $

    * Casos de uso: uc-02.02.19
*/

/*
$Log$
Revision 1.11  2006/08/14 18:57:16  fernando
Bug #6750#

Revision 1.10  2006/07/05 20:50:26  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE_RELATORIO        );
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoContaAnalitica.class.php" );

/**
    * Classe de Regra para emissão do Plano de Contas
    * @author Desenvolvedor: Eduardo Martins
    * @author Desenvolvedor: Anderson Buzo
*/
class RContabilidadeRelatorioPlanoConta extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obRContabilidadePlanoContaAnalitica;
/**
    * @var String
    * @access Private
*/
var $stFiltro;
/**
    * @var String
    * @access Private
*/
var $stCodEstruturalInicial;
/**
    * @var String
    * @access Private
*/
var $stCodEstruturalFinal;
/**
    * @var String
    * @access Private
*/
var $stEntidades;

/**
     * @access Public
     * @param Object $valor
*/
function setRContabilidadePlanoContaAnalitica($valor) { $this->obRContabilidadePlanoContaAnalitica = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setFiltro($valor) { $this->stFiltro = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setEntidades($valor) { $this->stEntidades = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setCodEstruturalInicial($valor) { $this->stCodEstruturalInicial = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setCodEstruturalFinal($valor) { $this->stCodEstruturalFinal = $valor; }

/**
     * @access Public
     * @return Object
*/
function getRContabilidadePlanoContaAnalitica() { return $this->obRContabilidadePlanoContaAnalitica; }
/**
     * @access Public
     * @return String
*/
function getFiltro() { return $this->stFiltro; }
/**
     * @access Public
     * @return String
*/
function getEntidades() { return $this->stEntidades; }
/**
     * @access Public
     * @return String
*/
function getCodEstruturalInicial() { return $this->stCodEstruturalInicial; }
/**
     * @access Public
     * @return String
*/
function getCodEstruturalFinal() { return $this->stCodEstruturalFinal; }

/**
    * Método Construtor
    * @access Private
*/
function RContabilidadeRelatorioPlanoConta()
{
    $this->obRContabilidadePlanoContaAnalitica = new RContabilidadePlanoContaAnalitica;
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet , $stOrder = "")
{
    $stFiltro = "";
    if ( $this->obRContabilidadePlanoContaAnalitica->getExercicio() ) {
        $stFiltro .= " pc.exercicio = '".$this->obRContabilidadePlanoContaAnalitica->getExercicio()."' AND ";
    }

    if ( !$this->getCodEstruturalInicial() ) {
        $this->obRContabilidadePlanoContaAnalitica->recuperaMascaraConta( $stMascara );
        $this->setCodEstruturalInicial(str_replace(9,'0',$stMascara));
    }

    if ( $this->getCodEstruturalFinal() ) {
        $arCodEstruturalFinal = explode( '.' ,$this->getCodEstruturalFinal() );
        $inSize = sizeof($arCodEstruturalFinal);
        for ($inSize -1; $inSize >= 0 ; $inSize--) {
            if ($arCodEstruturalFinal[$inSize-1] == 0) {
                $arCodEstruturalFinal[$inSize-1] = str_pad(9,strlen($arCodEstruturalFinal[$inSize-1]),'9',STR_PAD_LEFT);
            } else {
                break;
            }
        }
        $this->setCodEstruturalFinal(implode('.',$arCodEstruturalFinal));
    } else {

        $this->obRContabilidadePlanoContaAnalitica->recuperaMascaraConta( $stMascara );
        $this->setCodEstruturalFinal($stMascara);
    }

    $stFiltro .= " pc.cod_estrutural BETWEEN '".$this->getCodEstruturalInicial()."' ";
    $stFiltro .= " AND '".$this->getCodEstruturalFinal()."' AND ";

    if($this->obRContabilidadePlanoContaAnalitica->getCodPlanoInicial() )
        $stFiltro .= " PA.cod_plano >= " . $this->obRContabilidadePlanoContaAnalitica->getCodPlanoInicial() . "  AND ";
    if($this->obRContabilidadePlanoContaAnalitica->getCodPlanoFinal() )
        $stFiltro .= " PA.cod_plano <= " . $this->obRContabilidadePlanoContaAnalitica->getCodPlanoFinal() . "  AND ";

    $this->obRContabilidadePlanoContaAnalitica->obROrcamentoEntidade->setCodigoEntidade( $this->getEntidades() );

    if ($this->stFiltro) {
        $stFiltro .= $this->stFiltro;
    }

    $stFiltro = ( $stFiltro ) ? " WHERE " . substr( $stFiltro,0,strlen($stFiltro)-4) : '';

    $stOrder = "cod_estrutural";
    $obErro = $this->obRContabilidadePlanoContaAnalitica->listarRelatorioPlanoConta($rsRecordSet, $stFiltro, $stOrder );

    $arLista[] = array();
    $inContItens = 0;
    while ( !$rsRecordSet->eof() ) {
        $arItens[$inContItens]['cod_estrutural'] = SistemaLegado::doMask($rsRecordSet->getCampo('cod_estrutural'), $stMascara);
        $arItens[$inContItens]['cod_plano'] = $rsRecordSet->getCampo("cod_plano");
        $nom_sistema = strtoupper(substr($rsRecordSet->getCampo("nom_sistema"),0,1)) == 'N' ? '' : strtoupper(substr($rsRecordSet->getCampo("nom_sistema"),0,1));
        $arItens[$inContItens]['nom_sistema'] = $nom_sistema;
        $arItens[$inContItens]['nivel'] = $rsRecordSet->getCampo("nivel");
        if ( $rsRecordSet->getCampo( "nom_conta" ) ) {
             $stNomConta = str_replace( chr(10) , "", $rsRecordSet->getCampo( "nom_conta" ) );
             $stNomConta = wordwrap( $stNomConta , 60, chr(13) );
             $arNomConta = explode( chr(13), $stNomConta );
             foreach ($arNomConta as $stNomConta) {
                 $arItens[$inContItens]["nom_conta"]  = $stNomConta;
                 $arItens[$inContItens]["nivel"]      = $rsRecordSet->getCampo("nivel");
                 $inContItens++;
             }
        }
        $rsRecordSet->proximo();

    }
    $rsRecordSet = new RecordSet;
    $rsRecordSet->preenche( $arItens );

    return $obErro;
}

}
