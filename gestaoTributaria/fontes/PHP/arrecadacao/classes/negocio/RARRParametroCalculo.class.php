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
  * Classe de regra de negocio para ARRECADACAO.PARAMENTRO_CALCULO
  * Data de criação : 31/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Programador: Lucas Teixeira Stephanou

    * @package URBEM
    * @subpackage REGRA

    * $Id: RARRParametroCalculo.class.php 59612 2014-09-02 12:00:51Z gelson $

    Caso de uso: uc-05.03.05
**/

/*
$Log$
Revision 1.7  2006/09/15 11:50:14  fabio
corrigidas tags de caso de uso

Revision 1.6  2006/09/15 10:48:44  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRParametroCalculo.class.php" ) ;
include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php"                  ) ;
include_once ( CAM_GA_ADM_NEGOCIO."RFuncao.class.php"                    ) ;

/**
    * Classe de regra de negócio para ARRECADACAO.PARAMENTRO_CALCULO
    * Data de Criação: 31/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Regra
*/
class RARRParametroCalculo
{
/**
    * @access Private
    * @var  Integer
*/
var $inOcorrencia;
/**
    * @access Private
    * @var  String
*/
var $stValorCorrespondente;
/**
    * @access Private
    * @var  Object
*/
var $obTARRParametroCalculo;
// SETTERS
/**
     * @access Private
*/
function setOcorrencia($valor) { $this->inOcorrencia = $valor; }

function setValorCorrespondente($valor) { $this->stValorCorrespondente = $valor; }
// GETTERS
/**
     * @access Private
*/
function getOcorrencia() { return $this->inOcorrencia ; }

function getValorCorrespondente() { return $this->stValorCorrespondente ; }

function RARRParametroCalculo()
{
    // transacao
    $this->obTransacao              = new Transacao;
    // mapeamento
    $this->obTARRParametroCalculo   = new TARRParametroCalculo;
    // regra
    $this->obRARRGrupo              = new RARRGrupo;
    $this->obRFuncao                = new RFuncao;
    //
    $this->inOcorrencia             = 0;
}
/**
    * Definir Parametros
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function definirParametro($boTransacao = "")
{
   $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        // obter proxima ocorrencia cosultado ultima inserção
        $obErro = $this->consultarOcorrencia();
        if ( !$obErro->ocorreu() ) {
            if ( $this->inOcorrencia > 0 )
                $inProxOcorrencia = $this->inOcorrencia +1;
            elseif ( $this->inOcorrencia == 0 )
                $inProxOcorrencia = 1;
            $this->obTARRParametroCalculo->setDado  ( "cod_credito"         , $this->obRARRGrupo->obRMONCredito->getCodCredito()        );
            $this->obTARRParametroCalculo->setDado  ( "cod_especie"         , $this->obRARRGrupo->obRMONCredito->getCodEspecie()        );
            $this->obTARRParametroCalculo->setDado  ( "cod_genero"          , $this->obRARRGrupo->obRMONCredito->getCodGenero()         );
            $this->obTARRParametroCalculo->setDado  ( "cod_natureza"        , $this->obRARRGrupo->obRMONCredito->getCodNatureza()       );
            $this->obTARRParametroCalculo->setDado  ( "ocorrencia_credito"  , $inProxOcorrencia                                         );
            $this->obTARRParametroCalculo->setDado  ( "cod_funcao"          , $this->obRFuncao->getCodFuncao()                          );
            $this->obTARRParametroCalculo->setDado  ( "cod_biblioteca"      , $this->obRFuncao->obRBiblioteca->getCodigoBiblioteca()    );
            $this->obTARRParametroCalculo->setDado  ( "cod_modulo"          , $this->obRFuncao->obRBiblioteca->roRModulo->getCodModulo());
            $this->obTARRParametroCalculo->setDado  ( "valor_correspondente", $this->getValorCorrespondente()                           );
            $obErro = $this->obTARRParametroCalculo->inclusao ( $boTransacao );
        }
    }
   $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRParametroCalculo );

   return $obErro;
}

/**
* Recupera do banco de dados os dados da Parametro calculo
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function consultarParametro($boTransacao = "")
{
    $stFiltro = "";
    if ( $this->obRARRGrupo->obRMONCredito->getCodCredito() ) {
        $stFiltro .= " cod_credito = ".$this->obRARRGrupo->obRMONCredito->getCodCredito()." AND ";
    }
    if ( $this->obRARRGrupo->obRMONCredito->getCodEspecie() ) {
        $stFiltro .= " cod_especie = ".$this->obRARRGrupo->obRMONCredito->getCodEspecie()." AND ";
    }
    if ( $this->obRARRGrupo->obRMONCredito->getCodNatureza() ) {
        $stFiltro .= " cod_natureza = ".$this->obRARRGrupo->obRMONCredito->getCodNatureza()." AND ";
    }
    if ( $this->obRARRGrupo->obRMONCredito->getCodGenero() ) {
        $stFiltro .= " cod_genero = ".$this->obRARRGrupo->obRMONCredito->getCodGenero()." AND ";
    }
    if ( $this->getOcorrencia() ) {
        $stFiltro .= " ocorrencia_credito = ".$this->getOcorrencia()." AND ";
    }

    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrder = " ORDER BY cod_credito , ocorrencia_credito DESC LIMIT 1";

    $obErro = $this->obTARRParametroCalculo->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
//    $this->obTARRParametroCalculo->debug();
    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
        $this->obRARRGrupo->setCodGrupo                     ( $rsRecordSet->getCampo( "cod_grupo"           ) );
        $this->obRARRGrupo->obRMONCredito->setCodCredito    ( $rsRecordSet->getCampo( "cod_credito"         ) );
        $this->obRARRGrupo->obRMONCredito->setCodEspecie    ( $rsRecordSet->getCampo( "cod_especie"         ) );
        $this->obRARRGrupo->obRMONCredito->setCodGenero     ( $rsRecordSet->getCampo( "cod_genero"          ) );
        $this->obRARRGrupo->obRMONCredito->setCodNatureza   ( $rsRecordSet->getCampo( "cod_natureza"        ) );
        $this->obRFuncao->setCodFuncao                      ( $rsRecordSet->getCampo( "cod_funcao"          ) );
        $this->setOcorrencia                                ( $rsRecordSet->getCampo( "ocorrencia_credito"  ) );
    }

    return $obErro;
}

function consultarOcorrencia($boTransacao = "")
{
    $stFiltro = "";
    if ( $this->obRARRGrupo->obRMONCredito->getCodCredito() ) {
        $stFiltro .= " cod_credito = ".$this->obRARRGrupo->obRMONCredito->getCodCredito()." AND ";
    }
    if ( $this->obRARRGrupo->obRMONCredito->getCodEspecie() ) {
        $stFiltro .= " cod_especie = ".$this->obRARRGrupo->obRMONCredito->getCodEspecie()." AND ";
    }
    if ( $this->obRARRGrupo->obRMONCredito->getCodNatureza() ) {
        $stFiltro .= " cod_natureza = ".$this->obRARRGrupo->obRMONCredito->getCodNatureza()." AND ";
    }
    if ( $this->obRARRGrupo->obRMONCredito->getCodGenero() ) {
        $stFiltro .= " cod_genero = ".$this->obRARRGrupo->obRMONCredito->getCodGenero()." AND ";
    }
    if ( $this->getOcorrencia() ) {
        $stFiltro .= " ocorrencia_credito = ".$this->getOcorrencia()." AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrder = " ORDER BY cod_credito , ocorrencia_credito DESC LIMIT 1";

    $obErro = $this->obTARRParametroCalculo->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
        $this->setOcorrencia                                ( $rsRecordSet->getCampo( "ocorrencia_credito"  ) );
    }

    return $obErro;
}

} //fecha classe
