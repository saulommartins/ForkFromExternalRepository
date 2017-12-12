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
    * Arquivo de popup de busca de CGM
    * Data de Criação: 27/02/2003

    * @author Analista:
    * @author Desenvolvedor: Ricardo Lopes de Alencar

    * @package URBEM
    * @subpackage

    * Casos de uso: uc-03.03.06
                    uc-03.03.10
                    uc-03.03.11
                    uc-03.03.16
                    uc-03.03.17

    $Id: IPopUpItem.class.php 59612 2014-09-02 12:00:51Z gelson $

    */

include_once CLA_BUSCAINNER;

class IPopUpItem extends BuscaInner
{
    /**
        * @access Private
        * @var Object
    */

    public $obForm;

    /**
       * @access Private
       * @var Boolean
    */
    public $boRetornaUnidade;

    /**
       * @access Private
       * @var Boolean
    */
    public $boItemComposto;

    /**
       * @access Private
       * @var Boolean
    */
    public $boServico;

    /**
      * @access Private
      * @var String
    */
    public $stNomCampoUnidade;

    /**
       * @access Private
       * @var Boolean
    */
    public $boUnidadeNaoInformado;

    /**
       * @access Private
       * @var Boolean
    */
    public $boTipoNaoInformado;

    /**
       * @access Private
       * @var Boolean
    */
    public $boVerificaSaldo;

    /**
       * @access Private
       * @var Boolean
    */
    public $boPreencheUnidadeNaoInformada;

    /**
       * @access Private
       * @var Boolean
    */
    public $boPreencheTipoNaoInformado;
    /**
        * @access Private
        * @var Boolean
    */
    public $boExibeTipo;

    /**
        * @access Private
        * @var Boolean
    */
    public $boParametroDinamico;

    /**
        * @access Private
        * @var Int
    */
    public $inCodClassificacao;

    /**
        * @access Private
        * @var String
    */
    public $stCodEstruturalReduzido;

    /**
        * @access Private
        * @var Int
    */
    public $inCodCatalogo;

    /**
        * @access Private
        * @var String
    */
    public $stFiltroBusca;

    public $boVerificaMovimentacaoItem;

    public $obAlmoxarifadoOrigem;

    /**
        * @access Private
        * @var Boolean
    */
    public $boAtivo;

    public $stMsgComplementarSemSaldo;

    public function setAlmoxarifadoOrigem($obAlmoxarifadoOrigem)
    {
        $this->obAlmoxarifadoOrigem         = $obAlmoxarifadoOrigem;
    }

    public function getAlmoxarifadoOrigem()
    {
        return $this->obAlmoxarifadoOrigem;
    }

    public function setRetornaUnidade($valor) { $this->boRetornaUnidade              = $valor; }
    public function setNomCampoUnidade($valor) { $this->stNomCampoUnidade             = $valor; }
    public function setItemComposto($valor) { $this->boItemComposto                = $valor; }
    public function setServico($valor) { $this->boServico                     = $valor; }
    public function setUnidadeNaoInformado($valor) { $this->boUnidadeNaoInformado         = $valor; }
    public function setTipoNaoInformado($valor) { $this->boTipoNaoInformado            = $valor; }
    public function setComSaldo($valor) { $this->boVerificaSaldo               = $valor; }
    public function setPreencheUnidadeNaoInformada($valor) { $this->boPreencheUnidadeNaoInformada = $valor; }
    public function setExibeTipo($valor) { $this->boExibeTipo                   = $valor; }
    public function setPreencheTipoNaoInformado($valor) { $this->boPreencheTipoNaoInformado    = $valor; }
    public function setParametroDinamico($valor) { $this->boParametroDinamico           = $valor; }
    public function setCodClassificacao($valor) { $this->inCodClassificacao            = $valor; }
    public function setCodEstruturalReduzido($valor) { $this->stCodEstruturalReduzido       = $valor; }
    public function setVerificacaoMovimentacaoItem($verificacaoMovimentacaoItem) { $this->boVerificaMovimentacaoItem = $verificacaoMovimentacaoItem;}
    public function setCodCatalogo($valor) { $this->inCodCatalogo                 = $valor; }
    public function setTipoBusca($valor) { $this->stTipoBusca                   = $valor; }
    public function setAtivo($valor) { $this->boAtivo                       = $valor; }
    public function setFiltroBusca($valor) { $this->stFiltroBusca                 = $valor; }

    public function setMsgComplementarSemSaldo($valor) { $this->stMsgComplementarSemSaldo = $valor; }

    public function getVerificacaoMovimentacaoItem() { return $this->boVerificaMovimentacaoItem;}
    public function getRetornaUnidade() { return $this->boRetornaUnidade;              }
    public function getNomCampoUnidade() { return $this->stNomCampoUnidade;             }
    public function getServico() { return $this->boServico;                     }
    public function getUnidadeNaoInformado() { return $this->boUnidadeNaoInformado;         }
    public function getTipoNaoInformado() { return $this->boTipoNaoInformado;            }
    public function getComSaldo() { return $this->boVerificaSaldo;               }
    public function getPreencheUnidadeNaoInformada() { return $this->boPreencheUnidadeNaoInformada; }
    public function getExibeTipo() { return $this->boExibeTipo;                   }
    public function getPreencheTipoNaoInformado() { return $this->boPreencheTipoNaoInformado;    }
    public function getParametroDinamico() { return $this->boParametroDinamico;           }
    public function getCodClassificacao() { return $this->inCodClassificacao;            }
    public function getCodEstruturalReduzido() { return $this->stCodEstruturalReduzido;       }
    public function getCodCatalogo() { return $this->inCodCatalogo;                 }
    public function getAtivo() { return $this->boAtivo;                       }

    public function getMsgComplementarSemSaldo() { return $this->stMsgComplementarSemSaldo; }

    /**
        * Metodo Construtor
        * @access Public

    */
    public function IPopUpItem(&$obForm)
    {
        parent::BuscaInner();

        $this->obForm = $obForm;

        $this->setRotulo                 ( 'Item'             );
        $this->setTitle                  ( 'Informe o código do item.' );
        $this->setId                     ( 'stNomItem'        );
        $this->setNull                   ( false              );
        $this->setRetornaUnidade         ( false              );
        $this->setServico                ( true               );
        $this->setUnidadeNaoInformado    ( true               );
        $this->setTipoNaoInformado       ( false              );
        $this->setNomCampoUnidade        ( ""                 );
        $this->setComSaldo               ( false              );
        $this->setVerificacaoMovimentacaoItem(    false       );

        $this->obCampoCod->setName       ( "inCodItem"        );
        $this->obCampoCod->setSize       ( 10                 );
        $this->obCampoCod->setMaxLength  ( 9                  );
        $this->obCampoCod->setAlign      ( "left"             );

        $this->obImagem->setId           ( "imgBuscar"        );

        $this->setPreencheUnidadeNaoInformada( false );
        $this->setExibeTipo                  ( false );
        $this->setPreencheTipoNaoInformado   ( false );
        $this->setAtivo                      ( false );

        $this->setTipoBusca                 ('buscaPopup');
    }

    public function montaHTML()
    {
        $lnkComSaldo = "";
        $lnkCodClassificacao = "";
        $lnkCodEstruturalReduzido = "";
        $lnkCodCatalogo = "";
        $lnkAlmoxarifadoOrigem = "";
        $stLink = "";

        if (!($this->boServico)) {
            $lnkServico = "&boServico=".$this->getServico();
        } else {
            $lnkServico = null;
        }

        if ($this->boUnidadeNaoInformado) {
            $lnkNaoInformado = "&boUnidadeNaoInformado=".$this->getUnidadeNaoInformado();
        } else {
            $lnkNaoInformado = "";
        }

        if ($this->boTipoNaoInformado) {
            $lnkNaoInformado .= "&boTipoNaoInformado=".$this->getTipoNaoInformado();
        } else {
            $lnkNaoInformado .= "";
        }

        if ($this->boVerificaSaldo) {
            $lnkComSaldo .= "&boVerificaSaldo=".$this->getComSaldo();
        } else {
            $lnkComSaldo .= "";
        }

        $lnkPreencheUnidadeNaoInformada = "";
        if ($this->boPreencheUnidadeNaoInformada) {
            $lnkPreencheUnidadeNaoInformada = "&boPreencheUnidadeNaoInformada=true";
        } else {
            $lnkPreencheUnidadeNaoInformada = "&boPreencheUnidadeNaoInformada=false";
        }

        if (!$this->boRetornaUnidade) {
            $this->setNomCampoUnidade( '' );
        }
        $stOnChange = $this->obCampoCod->obEvento->getOnChange();

        $lnkExibeTipo = '';
        if ($this->boExibeTipo) {
            $lnkExibeTipo = "&boExibeTipo=true";
        } else {
            $lnkExibeTipo = "&boExibeTipo=false";
        }

        $lnkPreencheTipoNaoInformado = "";
        if ($this->boPreencheTipoNaoInformado) {
            $lnkPreencheTipoNaoInformado = "&boPreencheTipoNaoInformado=true";
        } else {
            $lnkPreencheTipoNaoInformado = "&boPreencheTipoNaoInformado=false";
        }

        if ( $this->getParametroDinamico() ) {
            $lnkParametroDinamico = "&boParametroDinamico=true";
        } else {
            $lnkParametroDinamico = "&boParametroDinamico=false";
        }

        if ( $this->getCodClassificacao() ) {
            $lnkCodClassificacao = "&inCodClassificacao=".$this->getCodClassificacao();
        }
        if ( $this->getCodEstruturalReduzido() ) {
            $lnkCodEstruturalReduzido = "&stCodEstruturalReduzido=".$this->getCodEstruturalReduzido();
        }
        if ( $this->getCodCatalogo() ) {
            $lnkCodCatalogo = "&inCodCatalogo=".$this->getCodCatalogo();
        }

        if ( $this->getVerificacaoMovimentacaoItem() ) {
            $lnkParametroVerificacaoMovimentacaoItem = "&boVerificaMovimentacaoItem=true";
        } else {
            $lnkParametroVerificacaoMovimentacaoItem = "&boVerificaMovimentacaoItem=";
        }

        # Monta o link com a flag de ativo.
        $lnkAtivo = $this->getAtivo() ? "&boAtivo=true" : "";

        if ( $this->getAlmoxarifadoOrigem() ) {
           $lnkAlmoxarifadoOrigem = "&".$this->getAlmoxarifadoOrigem()->getName()."='+document.frm.".$this->getAlmoxarifadoOrigem()->getName().".value+'";
        }
        if ($this->stFiltroBusca) {
            $stLink .= "&stFiltroBusca=".$this->stFiltroBusca;
        }

        if ($this->getMsgComplementarSemSaldo()) {
            $stLink .= "&stMsgComplementarSemSaldo=".$this->getMsgComplementarSemSaldo();
        }

        if(!$stOnChange) $stOnChange.=";";

        $this->setFuncaoBusca("abrePopUp('" . CAM_GP_ALM_POPUPS . "catalogo/FLManterItem.php','".$this->obForm->getName()."&nomCampoUnidade=".$this->getNomCampoUnidade().$lnkServico.$lnkNaoInformado.$lnkComSaldo.$lnkPreencheUnidadeNaoInformada.$lnkExibeTipo.$lnkPreencheTipoNaoInformado.$lnkParametroDinamico.$lnkCodClassificacao.$lnkCodEstruturalReduzido.$lnkCodCatalogo.$lnkAtivo.$lnkParametroVerificacaoMovimentacaoItem.$lnkAlmoxarifadoOrigem.$stLink."', '". $this->obCampoCod->stName ."','". $this->stId . "','".$this->stTipoBusca."','" . Sessao::getId() . "','800','550');");

        if (!$this->boItemComposto)
            $this->obCampoCod->obEvento->setOnChange($stOnChange."ajaxJavaScript( '".CAM_GP_ALM_POPUPS.'catalogo/OCManterItem.php?'.Sessao::getId().$lnkServico.$lnkAtivo.$lnkNaoInformado.$lnkComSaldo.$lnkPreencheUnidadeNaoInformada.$lnkExibeTipo.$lnkPreencheTipoNaoInformado.$lnkParametroDinamico.$lnkCodClassificacao.$lnkCodEstruturalReduzido.$lnkCodCatalogo.$lnkParametroVerificacaoMovimentacaoItem.$stLink."&nomCampoUnidade=".$this->getNomCampoUnidade()."&stNomCampoCod=".$this->obCampoCod->stName."&stIdCampoDesc=".$this->stId."&stNomForm=".$this->obForm->getName()."&inCodigo='+this.value, '".$this->stTipoBusca."' );");

        parent::montaHTML();
    }
}
?>
