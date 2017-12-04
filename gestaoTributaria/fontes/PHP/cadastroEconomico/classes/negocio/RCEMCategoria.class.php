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
    * Classe de regra de negócio para Categoria
    * Data de Criação: 11/04/2005

    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @package URBEM
    * @subpackage Regra

    * $Id: RCEMCategoria.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.09
*/

/*
$Log$
Revision 1.7  2007/02/27 12:29:46  cassiano
Bug #8433#

Revision 1.6  2007/02/22 13:34:19  rodrigo
Bug #8415#

Revision 1.5  2006/09/15 12:13:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMCategoria.class.php"  );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMCadastroEconomicoEmpresaDireito.class.php"  );

class RCEMCategoria
{
/**
    * @access Private
    * @var Integer
*/
var $inCodigoCategoria;
/**
    * @access Private
    * @var String
*/
var $stNomeCategoria;

/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoCategoria($valor) { $this->inCodigoCategoria = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setNomeCategoria($valor) { $this->stNomeCategoria = $valor;   }

/**
    * @access Public
    * @return Integer
*/
function getCodigoCategoria() { return $this->inCodigoCategoria; }
/**
    * @access Public
    * @return String
*/
function getNomeCategoria() { return $this->stNomeCategoria;   }

/**
    * Método construtor
    * @access Private
*/
function RCEMCategoria()
{
    $this->obTCEMCategoria = new TCEMCategoria;
    $this->obTransacao     = new Transacao;
}

/**
    * Inclui uma nova categoria
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/

function incluirCategoria($boTransacao = "")
{
$boFlaTransacao = false;
$obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
  if ( !$obErro->ocorreu() ) {
    $this->obTCEMCategoria->setDado ("cod_categoria", $this->inCodigoCategoria );
    $obErro = $this->obTCEMCategoria->proximoCod ( $this->inCodigoCategoria, $boTransacao);
        if ( !$obErro->ocorreu() ) {
            $this->obTCEMCategoria->setDado( "cod_categoria", $this->inCodigoCategoria );
            $this->obTCEMCategoria->setDado( "nom_categoria" , $this->stNomeCategoria );
            $obErro = $this->validaNomeCategoria ($boTransacao);
            if ( !$obErro->ocorreu() ) {
                $obErro = $this->obTCEMCategoria->inclusao( $boTransacao );
            }
        }
  }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCategoria );

    return $obErro;
}

/**
    * Altera uma  categoria
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/

function alterarCategoria($boTransacao = "")
{
$boFlaTransacao = false;
$obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCEMCategoria->setDado( "cod_categoria", $this->inCodigoCategoria );
        $this->obTCEMCategoria->setDado( "nom_categoria" , $this->stNomeCategoria );
        $obErro = $this->validaNomeCategoria ($boTransacao);
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->obTCEMCategoria->alteracao( $boTransacao );
        }
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCategoria );

    return $obErro;

}
/**
    * Excluir uma Categoria
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/

function excluirCategoria($boTransacao = "")
{
$boFlaTransacao = false;
$obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
    $obTCEMCadastroEconomicoEmpresaDireito = new TCEMCadastroEconomicoEmpresaDireito;
    $obTCEMCadastroEconomicoEmpresaDireito->recuperaTodos($rsEmpresaDireito, " WHERE cod_categoria = ".$this->inCodigoCategoria);

    if (!$rsEmpresaDireito->eof()) {
        $obErro->setDescricao ("Categoria ".$this->inCodigoCategoria." - ".$this->stNomeCategoria." ainda está sendo referenciada no sistema!");

        return $obErro;
    }

    $this->obTCEMCategoria->setDado( "cod_categoria", $this->inCodigoCategoria );
    $this->obTCEMCategoria->setDado( "nom_categoria" , $this->stNomeCategoria );
    $obErro = $this->obTCEMCategoria->exclusao( $boTransacao );

        if ($obErro->ocorreu()) {
            $obErro->setDescricao ("Categoria ".$this->inCodigoCategoria." - ".$this->stNomeCategoria." ainda está sendo referenciada no sistema!");
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCategoria );

    return $obErro;
}

/**
    * Lista as Categorias
    * @access Public
    * @param  Object $rsRecordSet Objeto RecrdSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/

function listarCategoria(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->getCodigoCategoria() ) {
      $stFiltro .= " cod_categoria = ".$this->getCodigoCategoria()." AND ";
    }
    if ( $this->getNomeCategoria() ) {
            $stFiltro .= " UPPER( nom_categoria ) like UPPER( '%".$this->getNomeCategoria()."%' ) AND ";
        }

    if ($stFiltro) {
         $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }
    $stOrdem = " ORDER BY cod_categoria";
    $obErro = $this->obTCEMCategoria->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Consulta as Categorias
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/

function consultarCategoria($boTransacao = "")
{
    $obErro = new Erro;
        if ($this->inCodigoCategoria) {
            $obErro = $this->listarCategoria( $rsCategoria, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->stNomeCategoria   = $rsAtividade->getCampo( "nom_atividade" );
            }
        }

    return $obErro;

}
function validaNomeCategoria($boTransacao = "")
{
/*
    $stFiltro = " WHERE  nom_categoria = '".$this->stNomeCategoria."' ";
    if ($this->inCodigoCategoria AND $this->stNomeCategoria) {
        $stFiltro .= " AND cod_categoria <> ".$this->inCodigoCategoria;
    }
*/
    $stOrdem = "";
    $obErro = $this->obTCEMCategoria->recuperaTodos( $rsCategoria, $stFiltro, $stOrdem, $boTransacao );
    $stNomeCategoria = strtoupper($this->stNomeCategoria);
    $stNomeCategoria = str_replace(" ","",$stNomeCategoria);
    if( $obErro->ocorreu() )

        return $obErro;

    while (!$rsCategoria->eof()) {
        $stNomeCategoriaTmp = $rsCategoria->getCampo("nom_categoria");
        $stNomeCategoriaTmp = strtoupper($stNomeCategoriaTmp);
        $stNomeCategoriaTmp = str_replace(" ","",$stNomeCategoriaTmp);
        if ($stNomeCategoriaTmp === $stNomeCategoria) {
            $obErro->setDescricao( "Já existe outro nome de categoria  cadastrado com o nome ".$this->stNomeCategoria."!" );

            return $obErro;
        }

        $rsCategoria->proximo();
    }

    return $obErro;
}

}
