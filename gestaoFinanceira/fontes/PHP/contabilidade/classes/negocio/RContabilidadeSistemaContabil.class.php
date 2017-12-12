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
    * Classe de Regra de Sistema Contabil
    * Data de Criação   : 03/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-03-07 09:56:31 -0300 (Qua, 07 Mar 2007) $

    * Casos de uso: uc-02.02.01,uc-02.02.02
*/

/*
$Log$
Revision 1.9  2007/03/07 12:56:31  rodrigo_sr
Bug #7993#

Revision 1.8  2006/07/05 20:50:26  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"                       );

/**
    * Classe de Regra de Sistema Contabil
    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo
*/
class RContabilidadeSistemaContabil
{
/**
    * @access Private
    * @var Integer
*/
var $inCodSistema;
/**
    * @access Private
    * @var String
*/
var $stNomSistema;
/**
    * @access Private
    * @var String
*/
var $stExercicio;

/**
    * @access Public
    * @param Integer $Valor
*/
function setCodSistema($valor) { $this->inCodSistema = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setNomSistema($valor) { $this->stNomSistema = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setExercicio($valor) { $this->stExercicio  = $valor; }

/**
    * @access Public
    * @return Integer
*/
function getCodSistema() { return $this->inCodSistema; }
/**
    * @access Public
    * @return String
*/
function getNomSistema() { return $this->stNomSistema; }
/**
    * @access Public
    * @return String
*/
function getExercicio() { return $this->stExercicio;   }

/**
     * Método construtor
     * @access Public
*/
function RContabilidadeSistemaContabil()
{
    $this->obTransacao = new Transacao ;
}

/**
    * Executa um recuperaPorChave na classe Persistente
    * @access Public
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultar($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeSistemaContabil.class.php");
    $obTContabilidadeSistemaContabil = new TContabilidadeSistemaContabil;

    $obTContabilidadeSistemaContabil->setDado( "cod_sistema", $this->inCodSistema );
    $obTContabilidadeSistemaContabil->setDado( "exercicio"  , $this->stExercicio  );
    $obErro = $obTContabilidadeSistemaContabil->recuperaPorChave( $rsRecordSet, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->stNomSistema = $rsRecordSet->getCampo( "nom_sistema" );
    }

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listar(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeSistemaContabil.class.php");
    $obTContabilidadeSistemaContabil = new TContabilidadeSistemaContabil;

    if($this->inCodSistema)
        $stFiltro  = " cod_sistema = "  . $this->inCodSistema . "  AND ";
    if($this->stExercicio)
        $stFiltro .= " exercicio = '"   . $this->stExercicio  . "' AND ";
    if($this->stNomSistema)
        $stFiltro .= " LOWER( nom_sistema ) LIKE LOWER('%". $this->stNomSistema ."%') AND";

    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $stOrder = ($stOrder) ? $stOrder : "cod_sistema";
    $obErro = $obTContabilidadeSistemaContabil->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaUltimoExercicio na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarUltimoExercicio(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    include_once CAM_GF_CONT_MAPEAMENTO.'TContabilidadeSistemaContabil.class.php';
    $obTContabilidadeSistemaContabil = new TContabilidadeSistemaContabil;

    $stOrder = ($stOrder) ? $stOrder : " ORDER BY cod_sistema";
    $obErro = $obTContabilidadeSistemaContabil->recuperaUltimoExercicio($rsRecordSet, "", $stOrder, $boTransacao);

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarSistemaContaAnalitica(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeSistemaContabil.class.php");
    $obTContabilidadeSistemaContabil = new TContabilidadeSistemaContabil;

    if($this->inCodSistema)
        $stFiltro  = " cod_sistema = "  . $this->inCodSistema . "  AND ";
    if($this->stExercicio)
        $stFiltro .= " exercicio = '"   . $this->stExercicio  . "' AND ";
    if($this->stNomSistema)
        $stFiltro .= " LOWER( nom_sistema ) LIKE LOWER('%". $this->stNomSistema ."%') AND";

    $stFiltro .= " cod_sistema != 5  AND ";

    if ( Sessao::getExercicio() > '2012' ) {
        $stFiltro .= " cod_sistema < 4  AND ";
    }

    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    if ( Sessao::getExercicio() > '2012' ) {

    }
    $stOrder = ($stOrder) ? $stOrder : "cod_sistema";
    $obErro = $obTContabilidadeSistemaContabil->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}
/**
    * Incluir Sistema Contabil
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluir($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeSistemaContabil.class.php");
    $obTContabilidadeSistemaContabil = new TContabilidadeSistemaContabil;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        $stFiltro  = " WHERE cod_sistema = ".$this->getCodSistema();
        $stFiltro .= "   AND exercicio = '".$this->getExercicio()."'";

            $obErro = $obTContabilidadeSistemaContabil->recuperaTodos($rsSistemaContabil, $stFiltro,'',$boTransacao);
            if ( !$obErro->ocorreu() ) {
                if ( $rsSistemaContabil->eof() ) {

        $obTContabilidadeSistemaContabil->setDado( "cod_sistema"  , $this->getCodSistema() );
        $obTContabilidadeSistemaContabil->setDado( "exercicio"    , $this->getExercicio()  );
        $obTContabilidadeSistemaContabil->setDado( "nom_sistema"  , $this->getNomSistema() );

        $obErro = $obTContabilidadeSistemaContabil->inclusao( $boTransacao );

                } else {
                    $obErro->setDescricao("Código ".$this->getCodSistema()." já cadastrado!");
                }

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTContabilidadeSistemaContabil );
               }
    }

    return $obErro;
}

/**
    * Alterar Sistema Contabil
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterar($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeSistemaContabil.class.php");
    $obTContabilidadeSistemaContabil = new TContabilidadeSistemaContabil;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        $obTContabilidadeSistemaContabil->setDado( "cod_sistema"  , $this->getCodSistema() );
        $obTContabilidadeSistemaContabil->setDado( "exercicio"    , $this->getExercicio()  );
        $obTContabilidadeSistemaContabil->setDado( "nom_sistema"  , $this->getNomSistema() );

        $obErro = $obTContabilidadeSistemaContabil->alteracao( $boTransacao );
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTContabilidadeSistemaContabil );
    }

    return $obErro;
}

/**
    * Exclui Sistema Contabil
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluir($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeSistemaContabil.class.php");
    $obTContabilidadeSistemaContabil = new TContabilidadeSistemaContabil;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $obTContabilidadeSistemaContabil->setDado("cod_sistema", $this->getCodSistema() );
        $obTContabilidadeSistemaContabil->setDado("exercicio"  , $this->getExercicio()  );
        $obErro = $obTContabilidadeSistemaContabil->exclusao( $boTransacao );
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTContabilidadeSistemaContabil );
    }

    return $obErro;
}

}
