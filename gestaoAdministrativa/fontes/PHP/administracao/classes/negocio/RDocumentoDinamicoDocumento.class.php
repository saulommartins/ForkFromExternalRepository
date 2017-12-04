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
include_once ( CAM_GA_ADM_MAPEAMENTO."TDocumentoDinamicoDocumento.class.php"                          );
include_once ( CAM_GA_ADM_MAPEAMENTO."TDocumentoDinamicoTagBase.class.php"                            );
include_once ( CAM_GA_ADM_MAPEAMENTO."TDocumentoDinamicoDocumentoBlocoTexto.class.php"                );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoModulo.class.php"                                 );
include_once ( CAM_GA_ADM_NEGOCIO."RModulo.class.php"                                                 );
include_once ( CAM_GA_ADM_NEGOCIO."RDocumentoDinamicoBlocoTexto.class.php"                            );

class RDocumentoDinamicoDocumento
{
/**
    * @access Private
    * @var Integer
*/
var $inCodDocumento;
/**
    * @access Private
    * @var String
*/
var $stTitulo;
/**
    * @access Private
    * @var String
*/
var $stNom_documento;
/**
    * @access Private
    * @var Integer
*/
var $inMargem_esq;
/**
    * @access Private
    * @var Integer
*/
var $inMargem_dir;
/**
    * @access Private
    * @var Integer
*/
var $inMargem_sup;
/**
    * @access Private
    * @var String
*/
var $stFonte;
/**
    * @access Private
    * @var Integer
*/
var $inTamanhoFonte;
/**
    * @access Private
    * @var Object
*/
var $obRModulo;
/**
    * @access Private
    * @var Object
*/
var $obTAdministracaoModulo;
/**
    * @access Private
    * @var Object
*/
var $obTDocumentoDinamicoDocumento;
/**
    * @access Private
    * @var Object
*/
var $arRDocumentoBlocoTexto;
/**
    * @access Private
    * @var Object
*/
var $obTransacao;
/**
    * @access Private
    * @var Object
*/
var $obTDocumentoDinamicoTagBase;

/**
    * @access Public
    * @param Integer $Valor
*/
function setCodDocumento($valor) { $this->inCodDocumento      = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setTitulo($valor) { $this->stTitulo  = $valor ; }
/**
    * @access Public
    * @param String $Valor
*/
function setNom_documento($valor) { $this->stNom_documento  = $valor ; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setMargem_esq($valor) { $this->inMargem_esq     = $valor ; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setMargem_dir($valor) { $this->inMargem_dir     = $valor ; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setMargem_sup($valor) { $this->inMargem_sup     = $valor ; }
/**
    * @access Public
    * @param String $Valor
*/
function setFonte($valor) { $this->stFonte         = $valor  ; }
/**
     * @access Public
     * @param Integer $valor
*/
function setTamanhoFonte($valor) { $this->inTamanhoFonte  = $valor  ; }
/**
     * @access Public
     * @param Object $valor
*/
function setTAdministracaoModulo($valor) { $this->obTAdministracaoModulo      = $valor  ; }
/**
    * @access Public
    * @param Object $Valor
*/
function setRModulo($valor) { $this->obRModulo      = $valor  ; }
/**
    * @access Public
    * @param Object $Valor
*/

function setTDocumentoDinamicoDocumento($valor) { $this->obTDocumentoDinamicoDocumento    = $valor  ; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTDocumentoDinamicoTagBase($valor) { $this->obTDocumentoDinamicoTagBase    = $valor  ; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTDocumentoDinamicoDocumentoBlocoTexto($valor) { $this->obTDocumentoDinamicoDocumentoBlocoTexto    = $valor  ; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTDocumentoDinamicoBlocoTexto($valor) { $this->obTDocumentoDinamicoBlocoTexto    = $valor  ; }

/**
    * @access Public
    * @param Integer $Valor
*/
function getCodDocumento() { return $this->inCodDocumento ; }
/**
    * @access Public
    * @param String $Valor
*/
function getTitulo() { return $this->stTitulo ; }
/**
    * @access Public
    * @param String $Valor
*/
function getNom_documento() { return $this->stNom_documento ; }
/**
    * @access Public
    * @param Integer $Valor
*/
function getMargem_esq() { return $this->inMargem_esq; }
/**
    * @access Public
    * @param Integer $Valor
*/
function getMargem_dir() { return $this->inMargem_dir; }
/**
    * @access Public
    * @param Integer $Valor
*/
function getMargem_sup() { return $this->inMargem_sup; }
/**
    * @access Public
    * @param String $Valor
*/
function getFonte() { return $this->stFonte; }
/**
     * @access Public
     * @param Integer $valor
*/
function getTamanhoFonte() { return$this->inTamanhoFonte   ; }
/**
     * @access Public
     * @param Object $valor
*/
function getTAdministracaoModulo() { return $this->obTAdministracaoModulo     ; }
/**
    * @access Public
    * @param Object $Valor
*/
function getRModulo() { return $this->obRModulo  ; }
/**
    * @access Public
    * @return Object
*/
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
function getTDocumentoDinamicoDocumentoBlocoTexto() { return  $this->obTDocumentoDinamicoDocumentoBlocoTexto ; }
/**
    * @access Public
    * @return Object
*/
function getTDocumentoDinamicoBlocoTexto() { return  $this->obTDocumentoDinamicoBlocoTexto ; }

/**
    * @access Public
    * @param Object $Valor
*/

function RDocumentoDinamicoDocumento()
{
    $this->setTDocumentoDinamicoDocumento              ( new TDocumentoDinamicoDocumento    );
    $this->setTDocumentoDinamicoTagBase                ( new TDocumentoDinamicoTagBase      );
    $this->setTDocumentoDinamicoDocumentoBlocoTexto    ( new TDocumentoDinamicoDocumentoBlocoTexto      );
    $this->setTDocumentoDinamicoBlocoTexto             ( new TDocumentoDinamicoBlocoTexto      );
    $this->setTAdministracaoModulo                     ( new TModulo  );
    $this->setRModulo                                  ( new RModulo  );
    $this->obTransacao                                 = new Transacao;
    $this->arRDocumentoDinamicoBlocoTexto              = array();
}

/**
* Adiciona um array de referencia-objeto
* @access Public
*/
function addDocumentoBlocoTexto()
{
   $this->arRDocumentoBlocoTexto[] = new RDocumentoDinamicoBlocoTexto ( $this );
   $this->roUltimoBlocoTexto       = &$this->arRDocumentoBlocoTexto[ count($this->arRDocumentoBlocoTexto) - 1 ];
}

/**
    * Altera os dados do Documento
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarDocumento($boTransacao = "")
{
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTDocumentoDinamicoDocumento->setDado ( "cod_documento"         , $this->inCodDocumento  );
        $this->obTDocumentoDinamicoDocumento->setDado ( "margem_esq"            , $this->inMargem_esq    );
        $this->obTDocumentoDinamicoDocumento->setDado ( "cod_modulo"            , $this->obRModulo->getCodModulo());
        $this->obTDocumentoDinamicoDocumento->setDado ( "nom_documento"         , $this->stNom_documento );
        $this->obTDocumentoDinamicoDocumento->setDado ( "titulo"                , $this->stTitulo );
        $this->obTDocumentoDinamicoDocumento->setDado ( "margem_dir"            , $this->inMargem_dir    );
        $this->obTDocumentoDinamicoDocumento->setDado ( "margem_sup"            , $this->inMargem_sup    );
        $this->obTDocumentoDinamicoDocumento->setDado ( "fonte"                 , $this->stFonte         );
        $this->obTDocumentoDinamicoDocumento->setDado ( "tamanho_fonte"         , $this->inTamanhoFonte  );

        $obErro = $this->obTDocumentoDinamicoDocumento->alteracao ( $boTransacao );
        if (!$obErro->ocorreu()) {
          $obErro = $this->listarDocumentoBlocoTexto($arBlocoTexto,$stOrder,$boTransacao );
          if (!$obErro->ocorreu()) {
             foreach ($arBlocoTexto as $obRBlocoTexto) {
                       $obErro = $obRBlocoTexto->excluirBloco($boTransacao);
                       if ($obErro->ocorreu()) {
                          break;
                       }
             }
          }
        }
        if (!$obErro->ocorreu()) {
           foreach ($this->arRDocumentoBlocoTexto as $obRDocumentoBlocoTexto) {
               $obErro =  $obRDocumentoBlocoTexto->incluirBloco($boTransacao);
               if ($obErro->ocorreu()) {
                  break;
               }
           }

        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTDocumentoDinamicoDocumento);
    }

    return $obErro;
}
/**
    * Recupera todos Bloco Texto referente ao documento
    * @access Public
    * @param  Array  $arBlocoTexto Array de objetos Bloco_Texto
    * @param  String $stOrder
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/

function listarDocumentoBlocoTexto(&$arBlocoTexto, $stOrder = "", $boTransacao = "")
{
   if ($this->inCodDocumento) {
      $this->obTDocumentoDinamicoDocumentoBlocoTexto->setDado ( "cod_documento" , $this->inCodDocumento );
      $obErro = $this->obTDocumentoDinamicoDocumentoBlocoTexto->recuperaRelacionamento($rsRecordSet,$stFiltro,$stOrder,$boTransacao);
      if (!$obErro->ocorreu()) {
         $inCount = 0;
         $arBlocoTexto = array();
         while (!$rsRecordSet->eof()) {
               $inCount++;
               $arBlocoTexto[$inCount]  = new RDocumentoDinamicoBlocoTexto ($this);
               $arBlocoTexto[$inCount]->setCodBloco($rsRecordSet->getCampo('cod_bloco'));
               $arBlocoTexto[$inCount]->setTexto($rsRecordSet->getCampo('texto'));
               $arBlocoTexto[$inCount]->setAlinhamento($rsRecordSet->getCampo('alinhamento'));
               $rsRecordSet->proximo();
         }
      }
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
function listarDocumento(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    $stCampoCod =  $this->obTDocumentoDinamicoDocumento->getCampoCod();
    if ($this->obRModulo->getCodModulo()) {
       $this->obTDocumentoDinamicoDocumento->setCampoCod('cod_modulo');
       $this->obTDocumentoDinamicoDocumento->setDado('cod_modulo',$this->obRModulo->getCodModulo());
    }
    $obErro = $this->obTDocumentoDinamicoDocumento->recuperaPorChave( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    $this->obTDocumentoDinamicoDocumento->setCampoCod($stCampoCod);

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
function listarDocumentoTagBase(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    $stCampoCod =  $this->obTDocumentoDinamicoTagBase->getCampoCod();

    if ($this->getCodDocumento() && $this->obRModulo->getCodModulo()) {
       $this->obTDocumentoDinamicoTagBase->setCampoCod('cod_documento');
       $this->obTDocumentoDinamicoTagBase->setDado('cod_documento',$this->getCodDocumento());
       $this->obTDocumentoDinamicoTagBase->setComplementoChave('cod_modulo');
       $this->obTDocumentoDinamicoTagBase->setDado('cod_modulo',$this->obRModulo->getCodModulo());
    }
    $obErro = $this->obTDocumentoDinamicoTagBase->recuperaPorChave( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    $this->obTDocumentoDinamicoDocumento->setCampoCod($stCampoCod);

    return $obErro;
}
/**
    * Executa um recuperaPorChave na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarDocumento(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro ="";
    $stOrder = "";
    if ($this->getCodDocumento()) {
       $this->obTDocumentoDinamicoDocumento->setDado('cod_documento',$this->getCodDocumento());
    }
    $obErro = $this->obTDocumentoDinamicoDocumento->recuperaPorChave( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

}
