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
    * Classe de regra de negocio para MONETARIO.ESPECIE_CREDITO
    * Data de Criacao: 21/12/2004

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho

    * @package URBEM
    * @subpackage Regra

    * $Id: RMONEspecieCredito.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.05.09
*/

/*
$Log$
Revision 1.15  2007/05/25 14:11:08  cercato
Bug #9298#

Revision 1.14  2007/02/26 17:33:55  cassiano
Bug #8421#

Revision 1.13  2007/02/22 17:19:12  rodrigo
Bug #8419#

Revision 1.12  2006/09/15 14:46:22  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_MON_MAPEAMENTO."TMONEspecieCredito.class.php");

class RMONEspecieCredito
{
/**
    * @access Private
    * @var Integer
*/
var $inCodNatureza;

/**
    * @access Private
    * @var Integer
*/
var $inCodGenero;

/**
    * @access Private
    * @var Integer
*/
var $inCodEspecie;

/**
    * @access Private
    * @var String
*/
var $stDescricaoEspecie;

/**
    * @access Private
    * @var Object
*/
var $obTMONEspecieCredito;

/**
    * @access Private
    * @var Object
*/
var $obTransacao;

/**
    * @access Private
    * @var Object
*/
var $obErro;

//METODO CONSTRUTOR
/**
     * Metodo construtor
     * @access Private
*/
function RMONEspecieCredito()
{
  $this->obErro = new Erro;
  $this->obTMONEspecieCredito = new TMONEspecieCredito;
}

//SETTERS
function setCodNatureza($valor) { $this->inCodNatureza = $valor; }
function setCodGenero($valor) { $this->inCodGenero = $valor; }
function setCodEspecie($valor) { $this->inCodEspecie = $valor; }
function setDescricaoEspecie($valor) { $this->stDescricaoEspecie = $valor; }

//GETTERS
function getCodNatureza() { return $this->inCodNatureza; }
function getCodGenero() { return $this->inCodGenero; }
function getCodEspecie() { return $this->inCodEspecie; }
function getDescricaoEspecie() { return $this->stDescricaoEspecie; }

/*
    FUNCOES DE INCLUSAO, EXCLUSAO e ALTERACAO DO BANCO DE DADOS
*/

/**
* Inclui os dados setados na tabela Monetaria.especie_credito
* @access Public
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function IncluirEspecie($boTransacao = '')
{
  $this->obTransacao = new Transacao;

  $boFlagTransacao = false;
  $obErro = $this->obTransacao->abreTransacao ( $boFlagTransacao, $boTransacao );
  if ( !$obErro->ocorreu () ) {

    $obErro = $this->verificaEspecie();
    if ( !$obErro->ocorreu() ) {

        $obErro = $this->obTMONEspecieCredito->proximoCod( $this->inCodEspecie, $boTransacao );
        if ( !$obErro->ocorreu() ) {

            $this->obTMONEspecieCredito->setDado ( 'cod_natureza',$this->getCodNatureza());
            $this->obTMONEspecieCredito->setDado ( 'cod_genero', $this->getCodGenero() );
            $this->obTMONEspecieCredito->setDado ( 'cod_especie', $this->getCodEspecie ());
            $this->obTMONEspecieCredito->setDado ( 'nom_especie',$this->getDescricaoEspecie());

            $obErro = $this->obTMONEspecieCredito->inclusao ( $boTransacao );
       }
    }
  }

  $this->obTransacao->fechaTransacao ( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONEspecieCredito );

  return $obErro;
}

/**
* Exclui os dados setados na tabela Monetaria.especie_credito
* @access Public
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function ExcluirEspecie($boTransacao = '')
{
    $this->obTransacao = new Transacao;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao ( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu () ) {
        include_once ( CAM_GT_MON_MAPEAMENTO."TMONCredito.class.php");
        $obTMONCredito = new TMONCredito();
        $stFiltro = " WHERE cod_natureza = ".$this->getCodNatureza()." AND cod_genero = ".$this->getCodGenero()." AND cod_especie = ".$this->getCodEspecie();
        $obErro = $obTMONCredito->recuperaTodos($rsRecordSet, $stFiltro, NULL, $boTransacao);
        if ( !$obErro->ocorreu() ) {
            if ( !$rsRecordSet->eof() ) {
                $stChave = $this->getCodNatureza().'-'.$this->getCodGenero().'-'.$this->getCodEspecie ();
                $obErro->setDescricao('Espécie '.$stChave.' - '.$this->getDescricaoEspecie().' ainda está sendo referenciada pelo sistema!');
            } else {
                $this->obTMONEspecieCredito->setDado ( 'cod_natureza',$this->getCodNatureza());
                $this->obTMONEspecieCredito->setDado ( 'cod_genero', $this->getCodGenero() );
                $this->obTMONEspecieCredito->setDado ( 'cod_especie', $this->getCodEspecie ());
                $this->obTMONEspecieCredito->setDado ( 'nom_especie',$this->getDescricaoEspecie());
                $obErro = $this->obTMONEspecieCredito->exclusao ( $boTransacao );
            }
        }
    }
    $this->obTransacao->fechaTransacao ( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONEspecieCredito );

    return $obErro;
}

/**
* Altera os dados setados na tabela Monetaria.especie_credito
* @access Public
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function AlterarEspecie($boTransacao = '')
{
  $this->obTransacao = new Transacao;

  $boFlagTransacao = false;
  $obErro = $this->obTransacao->abreTransacao ( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu () ) {

        $this->obTMONEspecieCredito->setDado ( 'cod_natureza',$this->getCodNatureza());
    $this->obTMONEspecieCredito->setDado ( 'cod_genero', $this->getCodGenero() );
    $this->obTMONEspecieCredito->setDado ( 'cod_especie', $this->getCodEspecie ());
    $this->obTMONEspecieCredito->setDado ('nom_especie',$this->getDescricaoEspecie());

    $obErro = $this->obTMONEspecieCredito->alteracao ( $boTransacao );
    }
  $this->obTransacao->fechaTransacao ( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONEspecieCredito );

  return $obErro;
}

/**
* Lista as Especies conforme o filtro setado
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function ListarEspecie(&$rsRecordSet, $boTransacao = '')
{
    $this->obTransacao = new Transacao;

    $stFiltro = "";
    if ( $this->getCodNatureza() ) {
        $stFiltro .= " e.cod_natureza = ".$this->getCodNatureza()." AND ";
    }
    if ( $this->getCodGenero() ) {
        $stFiltro .= " e.cod_genero = ".$this->getCodGenero()." AND ";
    }
    if ( $this->getCodEspecie() ) {
        $stFiltro .= " e.cod_especie = '".$this->getCodEspecie()."' AND ";
    }
    if ( $this->getDescricaoEspecie() ) {
        $stFiltro .= " upper ( e.nom_especie ) like upper('%".$this->getDescricaoEspecie ()."%') AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }

    $stOrder = "";

$obErro = $this->obTMONEspecieCredito->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

return $obErro;
}//fim lista especie

/**
* Lista os registros de Natureza de Especies
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function ListarNatureza(&$rsRecordSet, $boTransacao = '')
{
  include_once ( CAM_GT_MON_MAPEAMENTO."TMONNaturezaCredito.class.php");
  $this->obTMONNaturezaCredito = new TMONNaturezaCredito;

    $stFiltro = "";

    if ( $this->getCodNatureza() ) {
        $stFiltro .= " cod_natureza = '".$this->getCodNatureza()."' AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
  $stOrder = "";
  $obErro = $this->obTMONNaturezaCredito->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

  return $obErro;

}//fim lista natureza

/**
* Lista os registros de Genero de Especies
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function ListarGenero(&$rsRecordSet, $boTransacao = '')
{
  include_once (CAM_GT_MON_MAPEAMENTO."TMONGeneroCredito.class.php");
  $this->obTMONGeneroCredito = new TMONGeneroCredito;

    $stFiltro = "";
    if ( $this->getCodGenero() ) {
        $stFiltro .= " cod_genero = '".$this->getCodGenero ()."' AND ";
    }
    if ( $this->getCodNatureza() ) {
        $stFiltro .= " cod_natureza = '".$this->getCodNatureza ()."' AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrder = "";
    $obErro = $this->obTMONGeneroCredito->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;

}//fim lista genero

/**
* Recupera do BD os dados da Especie selecionada
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function ConsultarEspecie($boTransacao = "")
{
  $this->obTransacao = new Transacao;

    $stFiltro = "";
    if ($this->inCodEspecie) {
        $stFiltro .= " cod_especie = ". $this->inCodEspecie ." AND ";
    }
    if ($this->stDescricaoEspecie) {
        $stFiltro .= " nom_especie = ".$this->stDescricaoEspecie." AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrder = " ORDER BY cod_especie ";
    $obErro = $this->obTMONEspecieCredito->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
        $this->inCodEspecie  = $rsRecordSet->getCampo( "cod_especie" );
        $this->inCodGenero   = $rsRecordSet->getCampo( "cod_genero"  );
    $this->inCodNatureza = $rsRecordSet->getCampo( "cod_natureza");
    $this->stDescricaoEspecie = $rsRecordSet->getCampo( "nom_especie");
    }

  return $obErro;
}//------------------------------------------------------ FIM DA CONSULTA

/**
    * Verifica se a Especie a ser incluida ja existe
    * @access Public
    * @param  Object $rsBanco Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parametro Transacao
    * @return Object Objeto Erro
*/
function VerificaEspecie($boTransacao = "")
{
  $this->obTransacao = new Transacao;

    $obErro = $this->obTMONEspecieCredito->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    $cont =0;
    $achou = false;
    $valores = Array ();
    while ($cont < $rsRecordSet->getNumLinhas()) {

        $valores[$cont] = strtoupper ($rsRecordSet->getCampo("nom_especie"));

        if ( $valores[$cont] == strtoupper ($this->stDescricaoEspecie)  ) {
            $achou = true; break;
        }
        $cont++;
        $rsRecordSet->proximo();
    }

    if ( $rsRecordSet->getNumLinhas() > 0 && $achou ) {
      $obErro->setDescricao("Espécie já cadastrada no Sistema! ($this->stDescricaoEspecie)");
    }

    return $obErro;
}

}//fim class
?>
