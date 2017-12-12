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
    * Classe de Regra de Catálogo
    * Data de Criação   : 07/11/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Er Galvão Abbott

    * @package URBEM
    * @subpackage Regra

    $Revision: 16650 $
    $Name$
    $Autor: $
    $Date: 2006-10-11 07:08:19 -0300 (Qua, 11 Out 2006) $

    * Casos de uso: uc-03.03.04
*/

/*
$Log$
Revision 1.8  2006/10/11 10:08:19  larocca
Bug #5796#

Revision 1.7  2006/07/06 14:04:47  diego
Retirada tag de log com erro.

Revision 1.6  2006/07/06 12:09:31  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"                       );
include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogoNiveis.class.php");

/**
    * Classe de Regra de Catálogo
    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Er Galvão Abbott
*/
class RAlmoxarifadoCatalogoNivel
{
/**
    * @access Private
    * @var Object
*/
var $obTAlmoxarifadoCatalogoNiveis;
/**
    * @access Private
    * @var Integer
*/
var $inNivel;

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
    * @access Private
    * @var String
*/
var $stMascara;

/**
    * @access Public
    * @return Integer
*/

function setNivel($inNivel) { $this->inNivel = $inNivel; }

/**
    * @access Public
    * @return Integer
*/

function setDescricao($stDescricao) { $this->stDescricao = $stDescricao; }

/**
    * @access Public
    * @return String
*/

function setMascara($stMascara) { $this->stMascara = $stMascara; }

/**
    * @access Public
    * @return Integer
*/

function getNivel() { return $this->inNivel; }

/**
    * @access Public
    * @return String
*/

function getDescricao() { return $this->stDescricao; }

/**
    * @access Public
    * @return String
*/

function getMascara() { return $this->stMascara; }

/**
     * Método construtor
     * @access Public
     * @param Object Reference $roAlmoxarifadoCatalogo
*/

function RAlmoxarifadoCatalogoNivel(&$roAlmoxarifadoCatalogo)
{
    $this->roAlmoxarifadoCatalogo = &$roAlmoxarifadoCatalogo;

    $this->obTransacao  = new Transacao ;

    $this->obTAlmoxarifadoCatalogoNiveis = new TAlmoxarifadoCatalogoNiveis;
}

    /**
        * Executa um recuperaTodos na classe Persistente
        * @access Public
        * @param  Object $rsRecordSet Retorna o RecordSet preenchido
        * @param  String $stOrder Parâmetro de Ordenação
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */

    public function listar(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
    {
        $stFiltro  = " cod_catalogo =  "  . $this->roAlmoxarifadoCatalogo->getCodigo();

        $stFiltro = "  WHERE " . $stFiltro;

        $stOrder = (strlen($stOrder) > 0) ? $stOrder : "nivel";

        $obErro = $this->obTAlmoxarifadoCatalogoNiveis->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

        return $obErro;
    }

    public function listarMae(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
    {
        $stFiltro  = " cod_catalogo =  "  . $this->roAlmoxarifadoCatalogo->getCodigo() . " AND nivel < " . $this->getNivel();

        $stFiltro = "  WHERE " . $stFiltro;

        $stOrder = (strlen($stOrder) > 0) ? $stOrder : "nivel";

        $obErro = $this->obTAlmoxarifadoCatalogoNiveis->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

        return $obErro;
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
    $this->obTAlmoxarifadoCatalogoNiveis->setDado( "nivel"       , $this->roAlmoxarifadoCatalogo->getCodigo());
    $this->obTAlmoxarifadoCatalogoNiveis->setDado( "cod_catalogo", $this->inCodigo );

    $obErro = $this->obTAlmoxarifadoCatalogoNiveis->recuperaPorChave( $rsRecordSet, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $this->stMascara   = $rsRecordSet->getCampo  ( "mascara"   );
        $this->stDescricao = $rsRecordSet->getCampo  ( "descricao" );
    }

    return $obErro;
}

/**
    * Incluir Almoxarifado
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/

function incluir($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    /*$obErro = $this->validaDescricaoCatalogoNivel( &$boValida,$stAcao='incluir',$boTransacao);
            if ($boValida == 'FALSE') {
               $obErro->setDescricao('Este Nível já está cadastrado.');
            } else {*/
                if ( !$obErro->ocorreu() ) {
                    $this->obTAlmoxarifadoCatalogoNiveis->setDado( "cod_catalogo"         , $this->roAlmoxarifadoCatalogo->getCodigo());
                    $obErro = $this->obTAlmoxarifadoCatalogoNiveis->proximoCod( $this->inNivel, $boTransacao );

                    if ( !$obErro->ocorreu() ) {
                        $this->obTAlmoxarifadoCatalogoNiveis->setDado( "nivel"                , $this->inNivel);
                        $this->obTAlmoxarifadoCatalogoNiveis->setDado( "mascara"              , $this->stMascara );
                        $this->obTAlmoxarifadoCatalogoNiveis->setDado( "descricao"            , $this->stDescricao );
                    }

                    $obErro = $this->obTAlmoxarifadoCatalogoNiveis->inclusao( $boTransacao );
                    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTAlmoxarifadoCatalogoNiveis );
                }

                return $obErro;
         //   }
}

/**
    * Alterar Almoxarifado
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterar($boTransacao = "")
{
    $boFlagTransacao = false;

    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $this->obTAlmoxarifadoCatalogoNiveis->setDado( "nivel"                , $this->inNivel);
        $this->obTAlmoxarifadoCatalogoNiveis->setDado( "cod_catalogo"         , $this->roAlmoxarifadoCatalogo->getCodigo());
        $this->obTAlmoxarifadoCatalogoNiveis->setDado( "mascara"              , $this->stMascara );
        $this->obTAlmoxarifadoCatalogoNiveis->setDado( "descricao"            , $this->stDescricao );

        $obErro = $this->obTAlmoxarifadoCatalogoNiveis->alteracao( $boTransacao );
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTAlmoxarifadoCatalogoNiveis );
    }

    return $obErro;
}

/**
    * Exclui Almoxarifado
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/

function excluir($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $this->obTAlmoxarifadoCatalogoNiveis->setDado( "nivel"                , $this->inNivel);
        $this->obTAlmoxarifadoCatalogoNiveis->setDado( "cod_catalogo"         , $this->roAlmoxarifadoCatalogo->getCodigo());
        $this->obTAlmoxarifadoCatalogoNiveis->setDado( "mascara"              , $this->stMascara );
        $this->obTAlmoxarifadoCatalogoNiveis->setDado( "descricao"            , $this->stDescricao );

        $obErro = $this->obTAlmoxarifadoCatalogoNiveis->exclusao( $boTransacao );
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTAlmoxarifadoCatalogoNiveis );
    }

    return $obErro;
}

function retornaMascaraCompleta(&$rsRecordSet, $boTransacao = "")
{
    $this->obTAlmoxarifadoCatalogoNiveis->setDado("codCatalogo", $this->roAlmoxarifadoCatalogo->getCodigo());

    $obErro = $this->obTAlmoxarifadoCatalogoNiveis->recuperaMascaraCompleta($rsRecordSet, '', $boTransacao);

    return $obErro;
}

/*function validaDescricaoCatalogoNivel(&$boValida ,$stAcao ,$boTransacao) {
    $stOrder ='';
    $boValida = 'TRUE';
    $obErro = $this->listar ( $rsLista,$stOrder,$boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( $rsLista->getNumLinhas() > 0 ) {
            if ($stAcao == 'incluir') {
                $boValida = 'FALSE';
            } else {
                while (!$rsLista->eof()) {
                    if ($rsLista->getCampo('cod_catalogo') != $this->getCodigo()  ) {
                        $boValida = 'FALSE';
                    }
                    $rsLista->proximo();
                }
            }
        }

        $rsLista->setPrimeiroElemento();
        $obErro = $this->listarNaoExcluiveis($rsLista,$stOrder,$boTransacao);
        if ( !$obErro->ocorreu() ) {
            if ( $rsLista->getNumLinhas() > 0 ) {
                if ($stAcao == 'incluir') {
                    $boValida = 'FALSE';
                } else {
                    while (!$rsLista->eof()) {
                        if ($rsLista->getCampo('cod_catalogo') != $this->getCodigo()  ) {
                            $boValida = 'FALSE';
                        }
                        $rsLista->proximo();
                    }
                }
            }
        }

    }

    return $obErro;
}*/

}
