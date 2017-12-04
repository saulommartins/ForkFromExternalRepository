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
    * Classe de Regra de Negócio Marca
    * Data de Criação   : 01/11/2005

    * @author Analista:
    * @author Desenvolvedor: Leandro André Zis

    * @package URBEM
    * @subpackage Regra

    * Casos de uso: uc-03.03.03
*/

/*
$Log$
Revision 1.9  2007/08/07 13:00:19  rodrigo_sr
Bug#9602#

Revision 1.8  2006/07/06 14:04:47  diego
Retirada tag de log com erro.

Revision 1.7  2006/07/06 12:09:32  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GP_ALM_MAPEAMENTO. "TAlmoxarifadoMarca.class.php");

/**
    * Classe de Regra de Marca
    * @author Analista:
    * @author Desenvolvedor: Leandro André Zis
*/
class RAlmoxarifadoMarca
{
/**
    * @access Private
    * @var Integer
*/
var $inCodigo;

/**
    * @access Private
    * @var String
*/
var $stDescricao;

/**
    * @access Public
    * @param Integer $Valor
*/
function setCodigo($valor) { $this->inCodigo = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setDescricao($valor) { $this->stDescricao = $valor; }

/**
    * @access Public
    * @return Integer
*/
function getCodigo() { return $this->inCodigo; }

/**
    * @access Public
    * @return String
*/
function getDescricao() { return $this->stDescricao; }

/**
    * Metodo Construtor
    * @access Public
*/
function RAlmoxarifadoMarca()
{
    $this->obTransacao  =  new Transacao;
}
/**
    * @access Public
    * @param RecordSet $obRecordSet
    * @param String $stOrdem
    * @param Boolean $boTransacao
    * @return Erro
*/
function listar(&$rsRecordSet, $stOrdem="", $boTransacao = "")
{
   $stFiltro = "";
   if ($this->getDescricao()) {
       $stFiltro .= " WHERE descricao ilike '". $this->getDescricao() ."'";
   }
   $obTAlmoxarifadoMarca = new TAlmoxarifadoMarca();
   $obErro = $obTAlmoxarifadoMarca->recuperaTodos($rsRecordSet, $stFiltro, $stOrdem, $boTransacao);

   return $obErro;
}

function consultar($boTransacao = "")
{
    $obTAlmoxarifadoMarca = new TAlmoxarifadoMarca();
    $obTAlmoxarifadoMarca->setDado( "cod_marca" , $this->getCodigo() );
    $obErro = $obTAlmoxarifadoMarca->recuperaPorChave( $rsRecordSet, $boTransacao );
    if ( !$obErro->ocorreu() ) {
       $this->setDescricao( $rsRecordSet->getCampo("descricao") );
    }

    return $obErro;
}

/**
    * @access Public
    * @param Boolean $boTransacao
    * @return Erro
*/
function incluir($boTransacao="")
{
     //$boFlagTransacao = false;
    //$obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    //if ( !$obErro->ocorreu() ) {
        $obTAlmoxarifadoMarca = new TAlmoxarifadoMarca();
        $obErro = $this->validaDescricaoMarca( $boValida,$stAcao='incluir',$boTransacao);
        if ($boValida == 'FALSE') {
            $obErro->setDescricao('Esta Marca já está cadastrada.');
        } else {
            $obErro =  $obTAlmoxarifadoMarca->proximoCod( $inCodigo , $boTransacao );
            $this->setCodigo($inCodigo);
            if ( !$obErro->ocorreu() ) {
                $obTAlmoxarifadoMarca->setDado("cod_marca"      , $this->getCodigo() );
                $obTAlmoxarifadoMarca->setDado("descricao"      , stripslashes(stripslashes($this->getDescricao())) );
                $obErro = $obTAlmoxarifadoMarca->inclusao( $boTransacao );
            }
        }
    //}
    //$this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalCausaRescisao);
    return $obErro;
}

/**
    * @access Public
    * @param Boolean $boTransacao
    * @return Erro
*/
function alterar($boTransacao="")
{
    $obTAlmoxarifadoMarca = new TAlmoxarifadoMarca();
    $obErro = $this->validaDescricaoMarca( $boValida,$stAcao='alterar',$boTransacao);
    if ($boValida == 'FALSE') {
        $obErro->setDescricao('Esta Marca já está cadastrada.');
    } else {
        $obTAlmoxarifadoMarca->setDado("cod_marca"               , $this->getCodigo() );
        $obTAlmoxarifadoMarca->setDado("descricao"               , stripslashes(stripslashes($this->getDescricao())) );

        $obErro = $obTAlmoxarifadoMarca->alteracao( $boTransacao );
    }

    return $obErro;
}

/**
    * @access Public
    * @param Boolean $boTransacao
    * @return Erro
*/
function excluir($boTransacao="")
{
    $obTAlmoxarifadoMarca = new TAlmoxarifadoMarca();
    $obTAlmoxarifadoMarca->setDado( "cod_marca"         , $this->getCodigo()           );

    $obErro = $obTAlmoxarifadoMarca->exclusao( $boTransacao );

    return $obErro;
}

function validaDescricaoMarca(&$boValida ,$stAcao ,$boTransacao)
{
    $stOrder ='';
        $boValida = 'TRUE';
        $obErro = $this->listar ( $rsLista,$stOrder,$boTransacao );
        if ( !$obErro->ocorreu() ) {
            if ( $rsLista->getNumLinhas() > 0 ) {
                if ($stAcao == 'incluir') {
                    $boValida = 'FALSE';
                } else {
                    while (!$rsLista->eof()) {
                        if ($rsLista->getCampo('cod_marca') != $this->getCodigo()  ) {
                            $boValida = 'FALSE';
                        }
                        $rsLista->proximo();
                }
            }
        }
    }

    return $obErro;
}

}
