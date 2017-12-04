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
    * Classe de regra de negócio PessoalPrevidencia
    * Data de Criação: 06/04/2005

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Vandré Miguel Ramos

    * @package URBEM
    * @subpackage Regra

Casos de uso: uc-01.01.00

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_ADM_MAPEAMENTO."TDocumentoDinamicoBlocoTexto.class.php"          );
include_once ( CAM_GA_ADM_MAPEAMENTO."TDocumentoDinamicoDocumentoBlocoTexto.class.php" );

class RDocumentoDinamicoBlocoTexto
{
/**
    * @access Private
    * @var Integer
*/
var $inCodBloco;
/**
    * @access Private
    * @var String
*/
var $stTexto;
/**
    * @access Private
    * @var String
*/
var $stAlinhamento;
/**
    * @access Private
    * @var Object
*/
var $obTDocumentoDinamicoBlocoTexto;
/**
    * @access Private
    * @var Object
*/
var $obTDocumentoDinamicoDocumentoBlocoTexto;

/**
    * @access Private
    * @var Object
*/
var $obTransacao;

/**
    * @access Public
    * @param Integer $Valor
*/
function setCodBloco($valor) { $this->inCodBloco     = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setTexto($valor) { $this->stTexto         = $valor ; }
/**
    * @access Public
    * @param String $Valor
*/
function setAlinhamento($valor) { $this->stAlinhamento   = $valor ; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTDocumentoDinamicoBlocoTexto($valor) { $this->obTDocumentoDinamicoBlocoTexto    = $valor  ; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTDocumentoDinamicoDocumentoBlocoTexto($valor) { $this->obTDocumentoDinamicoDocumentoBlocoTexto    = $valor  ; }

/**
    * @access Public
    * @param Integer $Valor
*/
function getCodBloco() { return $this->inCodBloco ; }
/**
    * @access Public
    * @param Integer $Valor
*/
function getTexto() { return $this->stTexto ; }
/**
    * @access Public
    * @param String $Valor
*/
function getAlinhamento() { return $this->stAlinhamento; }
function getTDocumentoDinamicoDocumento() { return  $this->obTDocumentoDinamicoDocumento ; }
/**
    * @access Public
    * @return Object
*/
function getTDocumentoDinamicoTagBase() { return  $this->obTDocumentoDinamicoTagBase ; }
/**
    * @access Public
    * @return Object
*/
function getTDocumentoDinamicoBlocoTexto() { return  $this->obTDocumentoDinamicoBlocoTexto ; }

/**
    * @access Public
    * @param Object $Valor
*/

function RDocumentoDinamicoBlocoTexto(&$roDocumentoDinamicoDocumento)
{
    $this->setTDocumentoDinamicoBlocoTexto             ( new TDocumentoDinamicoBlocoTexto    );
    $this->setTDocumentoDinamicoDocumentoBlocoTexto    ( new TDocumentoDinamicoDocumentoBlocoTexto);
    $this->roDocumentoDinamicoDocumento                = &$roDocumentoDinamicoDocumento;
    $this->obTransacao                                 = new Transacao;
}

/**
    * Inclui os dados do bloco texto
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirBloco($boTransacao = "")
{
    $obErro = $this->obTDocumentoDinamicoBlocoTexto->proximoCod ( $this->inCodBloco, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $this->obTDocumentoDinamicoBlocoTexto->setDado ( "cod_bloco"             , $this->inCodBloco  );
        $this->obTDocumentoDinamicoBlocoTexto->setDado ( "texto"                 , $this->stTexto    );
        $this->obTDocumentoDinamicoBlocoTexto->setDado ( "alinhamento"           , $this->stAlinhamento);
        $obErro = $this->obTDocumentoDinamicoBlocoTexto->inclusao ( $boTransacao );
//                  $this->obTDocumentoDinamicoBlocoTexto->debug();
        if ( !$obErro->ocorreu ()) {
            $this->obTDocumentoDinamicoDocumentoBlocoTexto->setDado ( "cod_documento" , $this->roDocumentoDinamicoDocumento->getCodDocumento());
            $this->obTDocumentoDinamicoDocumentoBlocoTexto->setDado ( "cod_bloco"     , $this->inCodBloco     );
            $obErro = $this->obTDocumentoDinamicoDocumentoBlocoTexto->inclusao($boTransacao);
  //                    $this->obTDocumentoDinamicoDocumentoBlocoTexto->debug();
        }
    }

    return $obErro;
}

/**
    * Excluir os dados do bloco texto
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirBloco($boTransacao = "")
{
  $this->obTDocumentoDinamicoBlocoTexto->setDado ( "cod_bloco"             , $this->inCodBloco  );
  $this->obTDocumentoDinamicoDocumentoBlocoTexto->setDado ( "cod_bloco" , $this->inCodBloco);
  $this->obTDocumentoDinamicoDocumentoBlocoTexto->setDado ( "cod_documento" , $this->roDocumentoDinamicoDocumento->getCodDOcumento());
  $obErro =  $this->obTDocumentoDinamicoDocumentoBlocoTexto->exclusao($boTransacao);
    //         $this->obTDocumentoDinamicoDocumentoBlocoTexto->debug();
  if (!$obErro->ocorreu()) {
      $obErro = $this->obTDocumentoDinamicoBlocoTexto->exclusao($boTransacao);
    //            $this->obTDocumentoDinamicoBlocoTexto->debug();
  }

  return $obErro;
}

}
