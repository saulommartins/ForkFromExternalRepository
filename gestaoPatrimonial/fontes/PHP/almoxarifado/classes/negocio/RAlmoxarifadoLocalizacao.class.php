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
    * Classe de Regra de ALMOXARIFADO.LOCALIZACAO
    * Data de Criação   : 30/01/2006

    * @author Analista      : Diego Barbosa Victoria
    * @author Desenvolvedor : Rodrigo

    * @package URBEM
    * @subpackage Regra

    * Casos de uso: uc-03.03.14
*/


include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/mascara/Mascara.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/ExpReg/ExpReg.class.php';
include_once(CAM_FW_BANCO_DADOS."Transacao.class.php"                                            );
include_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoAlmoxarifadoLocalizacao.class.php"              );
include_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoLocalizacaoFisica.class.php"                    );
include_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoLocalizacaoFisicaItem.class.php"                );
include_once(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoAlmoxarifado.class.php"                            );
include_once(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoAlmoxarife.class.php"                              );
include_once(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoItemMarca.class.php"                               );

class RAlmoxarifadoLocalizacao
{
/**
    * @access Private
    * @var Object
*/
var $obRAlmoxarifadoAlmoxarifado;
/**
    * @access Private
    * @var Integer
*/
var $inCodigo;
/**
    * @access Private
    * @var Integer
*/
var $stLocalizacao;
/**
    * @access Public
    * @param  String
*/
 function setLocalizacao($stLocalizacao) { $this->stLocalizacao = $stLocalizacao;}
/**
    * @access Public
    * @return String
*/
 function getLocalizacao() { return $this->stLocalizacao;}
/**
    * @access Public
    * @param  Integer
*/
 function setCodigo($inCodigo) { $this->inCodigo = $inCodigo;}
/**
    * @access Public
    * @param  Integer
*/
 function getCodigo() { return $this->inCodigo;}
/**
    * Método construtor
    * @access Public
*/
 function RAlmoxarifadoLocalizacao()
 {
  $this->obTransacao                          = new Transacao();
  $this->obRAlmoxarifadoAlmoxarifado          = new RAlmoxarifadoAlmoxarifado();
  $this->obRAlmoxarifadoAlmoxarife            = new RAlmoxarifadoAlmoxarife();
  $this->obRAlmoxarifadoItemMarca             = new RAlmoxarifadoItemMarca();
  $this->obTAlmoxarifadoLocalizacao           = new TAlmoxarifadoAlmoxarifadoLocalizacao();
  $this->obTAlmoxarifadoLocalizacaoFisica     = new TAlmoxarifadoLocalizacaoFisica();
  $this->obTAlmoxarifadoLocalizacaoFisicaItem = new TAlmoxarifadoLocalizacaoFisicaItem();
 }

    public function excluir($boTransacao="")
    {
        $this->obTAlmoxarifadoLocalizacaoFisicaItem->setDado("cod_almoxarifado",$this->obRAlmoxarifadoAlmoxarifado->getCodigo());
        $this->obTAlmoxarifadoLocalizacaoFisicaItem->setDado("cod_localizacao" ,$this->getCodigo());

        $obErro = $this->obTAlmoxarifadoLocalizacaoFisicaItem->exclusao($boTransacao);

        if (!$obErro->ocorreu()) {
            $this->obTAlmoxarifadoLocalizacaoFisica->setDado("cod_almoxarifado",$this->obRAlmoxarifadoAlmoxarifado->getCodigo());
            $this->obTAlmoxarifadoLocalizacaoFisica->setDado("cod_localizacao" ,$this->getCodigo());

            $obErro = $this->obTAlmoxarifadoLocalizacaoFisica->exclusao($boTransacao);
            if (!$obErro->ocorreu()) {
                $this->obTAlmoxarifadoLocalizacao->setDado("cod_almoxarifado" ,$this->obRAlmoxarifadoAlmoxarifado->getCodigo());
        //   $obErro = $this->obTAlmoxarifadoLocalizacao->exclusao($boTransacao);
            }
        }

        return $obErro;
    }

 function consultar($boTransacao = "")
 {
  $rsRecordSet = new Recordset;
  $this->obTAlmoxarifadoLocalizacaoFisicaItem->setDado("cod_almoxarifado", $this->obRAlmoxarifadoAlmoxarifado->getCodigo() );

  $obErro = $this->obTAlmoxarifadoLocalizacaoFisicaItem->recuperaPorChave($rsRecordSet, $boTransacao);

  if (!$obErro->ocorreu()) {

   $obErro = $this->obRAlmoxarifadoAlmoxarifado->consultar( $boTransacao );
    if (!$obErro->ocorreu()) {

      $stFiltro = " WHERE localizacao = '".$_REQUEST['HdnLocalizacao']."' AND cod_almoxarifado = ".$this->obRAlmoxarifadoAlmoxarifado->getCodigo();
      $this->obTAlmoxarifadoLocalizacaoFisicaItem->recuperaCodLocal($rsCodLocal, $stFiltro, $stOrdem, $boTransacao);

      $stFiltro = " WHERE cod_localizacao = ".$rsCodLocal->getCampo('cod_localizacao')." AND cod_almoxarifado = ".$this->obRAlmoxarifadoAlmoxarifado->getCodigo();
      $this->obTAlmoxarifadoLocalizacaoFisicaItem->recuperaTodos($rsRecordSet, $stFiltro, $stOrdem, $boTransacao);

      while (!$rsRecordSet->EOF()) {

       $this->addLocalizacaoItem();

       $this->roLocalizacaoItem->obRCatalogoItem->setCodigo($rsRecordSet->getCampo('cod_item'));
       $this->roLocalizacaoItem->obRMarca->setCodigo($rsRecordSet->getCampo('cod_marca'));
       $obErro = $this->roLocalizacaoItem->consultar($boTransacao);

        if (!$obErro->ocorreu()) {

          $this->obRAlmoxarifadoItemMarca->obRCatalogoItem->setCodigo($rsRecordSet->getCampo('cod_item'));
          $obErro = $this->obRAlmoxarifadoItemMarca->obRCatalogoItem->consultar($boTransacao);

          $this->obRAlmoxarifadoAlmoxarifado->setCodigo($rsRecordSet->getCampo('cod_almoxarifado'));
          if ($obErro->ocorreu()) {
            break;
          }
        }
       $rsRecordSet->proximo();
      }
   }
 }

  return $obErro;
 }

 function addLocalizacaoItem()
 {
  $this->arLocalizacaoItem[] = new RAlmoxarifadoItemMarca();
  $this->roLocalizacaoItem   = &$this->arLocalizacaoItem[ count( $this->arLocalizacaoItem ) -1 ];
 }

 function listarItens(&$rsRecordSet, $obTransacao = "")
 {
     $stFiltro = "";
     $obTCatalogoFisicaItem = new TAlmoxarifadoLocalizacaoFisicaItem;

     if ($this->roLocalizacaoItem->obRCatalogoItem->getCodigo()) {
       $stFiltro .= " AND localizacao_fisica_item.cod_item           = ".$this->roLocalizacaoItem->obRCatalogoItem->getCodigo()."  \n";
     }

     if ($this->roLocalizacaoItem->obRMarca->getCodigo()) {
       $stFiltro .= " AND localizacao_fisica_item.cod_marca          = ".$this->roLocalizacaoItem->obRMarca->getCodigo()."         \n";
     }

     if ($this->obRAlmoxarifadoAlmoxarifado->getCodigo()) {
         $stFiltro .= " AND localizacao_fisica_item.cod_almoxarifado = ".$this->obRAlmoxarifadoAlmoxarifado->getCodigo()."         \n";
     }

     $stOrdem = " ORDER BY localizacao_fisica.localizacao DESC                                                                \n";
     $obErro  = $obTCatalogoFisicaItem->recuperaFisicaItem( $rsRecordSet, $stFiltro, $stOrdem, $obTransacao );

     return $obErro;
 }

 function listar(&$rsRecordSet, $stOrdem="", $boTransacao = "")
 {
    $stFiltro .= " WHERE 1 = 1 ";

    if ($this->obRAlmoxarifadoAlmoxarifado->getCodigo()) {
        $stFiltro .= " AND cod_almoxarifado = ". $this->obRAlmoxarifadoAlmoxarifado->getCodigo();
    }

    if ($this->getLocalizacao()) {
        if (substr($this->getLocalizacao(),-1) == "%" | substr($this->getLocalizacao(),0,1) == "%") {
            $stFiltro .= " AND localizacao  iLike '".$this->getLocalizacao()."'";
        } else {
            $stFiltro .= " AND localizacao = '".$this->getLocalizacao()."'";
        }
    }

    $stOrdem = " ORDER BY localizacao_fisica.localizacao                                                          \n";
    $obErro = $this->obTAlmoxarifadoLocalizacaoFisica->recuperaTodos($rsRecordSet, $stFiltro, $stOrdem, $boTransacao);

    return $obErro;
 }

 function alterarItens($boTransacao = "")
 {
  $rsRecordSetItem       = new recordset();
  $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
   if (!($obErro->ocorreu())) {
     $this->obTAlmoxarifadoLocalizacaoFisicaItem->setDado( "cod_almoxarifado", $this->obRAlmoxarifadoAlmoxarifado->getCodigo());
     $this->obTAlmoxarifadoLocalizacaoFisicaItem->setDado( "cod_item"        , $this->obRAlmoxarifadoItemMarca->obRCatalogoItem->getCodigo());
     $this->obTAlmoxarifadoLocalizacaoFisicaItem->setDado( "cod_marca"       , $this->obRAlmoxarifadoItemMarca->obRMarca->getCodigo());

     $obErro = $this->obTAlmoxarifadoLocalizacaoFisicaItem->exclusao( $boTransacao );

     if (!($obErro->ocorreu())) {
       include_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogoItemMarca.class.php"                    );
       $obTAlmoxarifadoCatalogoItemMarca = new TAlmoxarifadoCatalogoItemMarca();
       $obTAlmoxarifadoCatalogoItemMarca->setDado("cod_item", $this->obRAlmoxarifadoItemMarca->obRCatalogoItem->getCodigo());
       $obTAlmoxarifadoCatalogoItemMarca->setDado("cod_marca", $this->obRAlmoxarifadoItemMarca->obRMarca->getCodigo());
       $obTAlmoxarifadoCatalogoItemMarca->recuperaPorChave($rsRecordSet, $boTransacao);
       if ($rsRecordSet->getNumLinhas() <= 0) {
          $obErro = $obTAlmoxarifadoCatalogoItemMarca->inclusao($boTransacao);
       }
       if (!$obErro->ocorreu()) {
          $this->obTAlmoxarifadoLocalizacaoFisicaItem->setDado( "cod_almoxarifado", $this->obRAlmoxarifadoAlmoxarifado->getCodigo());
          $this->obTAlmoxarifadoLocalizacaoFisicaItem->setDado( "cod_item"        , $this->obRAlmoxarifadoItemMarca->obRCatalogoItem->getCodigo());
          $this->obTAlmoxarifadoLocalizacaoFisicaItem->setDado( "cod_marca"       , $this->obRAlmoxarifadoItemMarca->obRMarca->getCodigo());
          $this->obTAlmoxarifadoLocalizacaoFisicaItem->setDado( "cod_localizacao" , $this->inCodigo);
          $obErro = $this->obTAlmoxarifadoLocalizacaoFisicaItem->inclusao( $boTransacao );
       }
     }

   }
  $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTAlmoxarifadoLocalizacaoFisicaItem );

  return $obErro;
 }

function alterar($boTransacao = "")
{
  $boFlagTransacao = false;
  $rsRecordSetItem = new recordset();

  $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
  if (!($obErro->ocorreu())) {
   $obErro = $this->checarArrayItem();
   if (!($obErro->ocorreu())) {

     for ($i=0;$i<count($this->arLocalizacaoItem);$i++) {
       $obRAlmoxarifadoItemMarca = $this->arLocalizacaoItem[$i];
       $obErro = $obRAlmoxarifadoItemMarca->listar($rsRecordSetItem,"",$boTransacao);
        if (!($obErro->ocorreu())) {
          if ($rsRecordSetItem->getNumLinhas()<1) {
            $obErro = $obRAlmoxarifadoItemMarca->incluir($boTransacao);
          }
          if ($obErro->ocorreu()) {
           break;
          }
        }
     }

     $this->obRAlmoxarifadoAlmoxarifado->setCodigo($this->obRAlmoxarifadoAlmoxarifado->getCodigo());
     $obErro = $this->obRAlmoxarifadoAlmoxarifado->consultar($boTransacao);

       if (!($obErro->ocorreu())) {

                     if (!($obErro->ocorreu())) {

                       $this->obTAlmoxarifadoLocalizacaoFisica->setDado("cod_localizacao" , $this->inCodigo);
                       $this->obTAlmoxarifadoLocalizacaoFisica->setDado("cod_almoxarifado", $this->obRAlmoxarifadoAlmoxarifado->getCodigo());
                       $this->obTAlmoxarifadoLocalizacaoFisica->setDado("localizacao"     , $this->stLocalizacao);

                       $obErro = $this->obTAlmoxarifadoLocalizacaoFisica->alteracao($boTransacao);

                       if ( !$obErro->ocorreu()) {

                               $this->obTAlmoxarifadoLocalizacaoFisicaItem->setDado( "cod_almoxarifado", $this->obRAlmoxarifadoAlmoxarifado->getCodigo());
                               $this->obTAlmoxarifadoLocalizacaoFisicaItem->setDado( "cod_localizacao" , $this->inCodigo);
                               $obErro = $this->obTAlmoxarifadoLocalizacaoFisicaItem->exclusao( $boTransacao );

                           if ( !$obErro->ocorreu()) {

                             for ($i=0;$i<count($this->arLocalizacaoItem);$i++) {
                               $this->obTAlmoxarifadoLocalizacaoFisicaItem->setDado( "cod_almoxarifado", $this->obRAlmoxarifadoAlmoxarifado->getCodigo());
                               $this->obTAlmoxarifadoLocalizacaoFisicaItem->setDado( "cod_item"        , $this->arLocalizacaoItem[$i]->obRCatalogoItem->getCodigo());
                               $this->obTAlmoxarifadoLocalizacaoFisicaItem->setDado( "cod_marca"       , $this->arLocalizacaoItem[$i]->obRMarca->getCodigo());
                               $this->obTAlmoxarifadoLocalizacaoFisicaItem->setDado( "cod_localizacao" , $this->inCodigo);
                               $obErro = $this->obTAlmoxarifadoLocalizacaoFisicaItem->inclusao( $boTransacao );

                             }

                            if ($obErro->ocorreu()) {
                              $obErro->setDescricao("Não pode haver mais de um item da mesma marca nesta localização. Almoxarifado : ".$this->obRAlmoxarifadoAlmoxarifado->getCodigo());
                            }
                           }
                         } else {
                            $obErro->setDescricao( "Esta Localização já está cadastrada. Localização : ".$this->stLocalizacao);
                         }
                       }

        
       }
   }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTAlmoxarifadoCatalogo );
  }

 return $obErro;
 }

 function incluir($boTransacao = "")
 {
  $boFlagTransacao = false;
  $rsRecordSetItem = new recordset();


  $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu()) {
        $obErro = $this->checarArrayItem();
        
        if (!$obErro->ocorreu()) {
            $obErro = $this->obTAlmoxarifadoLocalizacaoFisica->proximoCod( $this->inCodigo, $boTransacao );
            
            if (!$obErro->ocorreu()) {
             $this->obRAlmoxarifadoAlmoxarifado->setCodigo($this->obRAlmoxarifadoAlmoxarifado->getCodigo());
             $obErro = $this->obRAlmoxarifadoAlmoxarifado->consultar($boTransacao);

                if (!$obErro->ocorreu()) {
                    
                        for ($i=0;$i<count($this->arLocalizacaoItem);$i++) {
                           $obRAlmoxarifadoItemMarca = $this->arLocalizacaoItem[$i];
                           $ItemMarca = $obRAlmoxarifadoItemMarca->listar($rsRecordSetItem);

                            if ($rsRecordSetItem->getNumLinhas() < 1) {
                                $obErro = $obRAlmoxarifadoItemMarca->incluir($boTransacao);
                            }
                              if ($obErro->ocorreu()) {
                               break;
                              }
                         }

                     if (!($obErro->ocorreu())) {
                       $this->obTAlmoxarifadoLocalizacaoFisica->setDado("cod_localizacao" , $this->inCodigo);
                       $this->obTAlmoxarifadoLocalizacaoFisica->setDado("cod_almoxarifado", $this->obRAlmoxarifadoAlmoxarifado->getCodigo());
                       $this->obTAlmoxarifadoLocalizacaoFisica->setDado("localizacao"     , $this->stLocalizacao);

                       $obErro = $this->obTAlmoxarifadoLocalizacaoFisica->inclusao($boTransacao);

                       if (!$obErro->ocorreu()) {

                             for ($i=0;$i<count($this->arLocalizacaoItem);$i++) {
                               $this->obTAlmoxarifadoLocalizacaoFisicaItem->setDado( "cod_almoxarifado", $this->obRAlmoxarifadoAlmoxarifado->getCodigo());
                               $this->obTAlmoxarifadoLocalizacaoFisicaItem->setDado( "cod_item"        , $this->arLocalizacaoItem[$i]->obRCatalogoItem->getCodigo());
                               $this->obTAlmoxarifadoLocalizacaoFisicaItem->setDado( "cod_marca"       , $this->arLocalizacaoItem[$i]->obRMarca->getCodigo());
                               $this->obTAlmoxarifadoLocalizacaoFisicaItem->setDado( "cod_localizacao" , $this->inCodigo);
                               $obErro = $this->obTAlmoxarifadoLocalizacaoFisicaItem->inclusao( $boTransacao );

                               if ($obErro->ocorreu()) {
                                 break;
                               }

                             }

                           if ($obErro->ocorreu()) {
                               $obErro->setDescricao( "Não pode haver mais de um item da mesma marca nesta localização. Item :".$this->roLocalizacaoItem->obRCatalogoItem->getCodigo()." Marca : ".$this->roLocalizacaoItem->obRMarca->getCodigo() );
                           }
                      } else {
                          $obErro->setDescricao( "Essa localização já foi cadastrada. Localização : ".$this->stLocalizacao);
                      }
                    
                 
         }

       }
     }
   } else {
     return $obErro;
   }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTAlmoxarifadoCatalogo );
  }

 return $obErro;
 }

 function checarArrayItem()
 {
  $obErro = new Erro();
  if ($this->arLocalizacaoItem) {
   if (count($this->arLocalizacaoItem) < 1) {
//    $obErro->setDescricao("A localização deve possuir pelo menos um item.");
   }
  } else {
//   $obErro->setDescricao("A localização deve possuir pelo menos um item.");
  }

  return $obErro;
 }

}
